<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Category;
	use App\Models\ChatMessage;
	use App\Models\ChatSession;
	use Carbon\Carbon;
	use GuzzleHttp\Client;
	use Illuminate\Http\Request;
	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Validator;


	class ChatController extends Controller
	{

		public function checkLLMsJson()
		{
			$checkLLMs = MyHelper::checkLLMsJson();
			return response()->json($checkLLMs);
		}

		public function createSession(Request $request)
		{
			$chatSession = ChatSession::create([
				'session_id' => (string)Str::uuid(), // Generate UUID for session_id
				'user_id' => Auth::id(),
				'created_at' => now(),
				'updated_at' => now(),
			]);

			return response()->json(['session_id' => $chatSession->session_id]); // Return session_id instead of id
		}

		public function getChatSessions()
		{
			$sessions = ChatSession::where('user_id', Auth::id())
				->whereHas('messages', function ($query) {
					$query->where('id', '>', 1);  // Or any other condition you might want
				})
				->with(['messages' => function ($query) {
					$query->orderBy('created_at', 'asc');
				}])
				->orderBy('updated_at', 'desc')
				->get();

			return response()->json($sessions);
		}

		public function getChatMessages($sessionId)
		{

			$chatSession = ChatSession::where('session_id', $sessionId)
				->where('user_id', Auth::id())
				->first();

			$session_id = $chatSession->id;

			$messages = ChatMessage::where('session_id', $session_id)
				->orderBy('created_at', 'asc')
				->get();

			return response()->json($messages);
		}


		public function sendLlmPrompt(Request $request)
		{
			$userPrompt = $request->input('user_prompt');
			$sessionId = $request->input('session_id');
			$llm = $request->input('llm');

			$chatSession = ChatSession::where('session_id', $sessionId)
				->where('user_id', Auth::id())
				->first();

			if (!$chatSession) {
				return response()->json(['success' => false, 'message' => 'Invalid session']);
			}

			$session_id = $chatSession->id;

			try {
				// Fetch previous messages from the session
				$chatHistory = ChatMessage::where('session_id', $session_id)
					->orderBy('created_at', 'desc')
					->limit(20)
					->get();
				$chat_history = [];
				foreach ($chatHistory as $msg) {
					$chat_history[] = [
						'role' => $msg->role,
						'content' => $msg->message,
					];
				}
				//add user prompt to the chat history
				$chat_history[] = [
					'role' => 'user',
					'content' => $userPrompt,
				];

				$resultData = MyHelper::llm_no_tool_call($llm, '', $chat_history, false);

				if ($resultData['error']) {
					Log::error('Error in chat LLM call: ' . $resultData['content']);
					return response()->json(['success' => false, 'message' => $resultData->content], 500);
				}

				// Save the user's prompt and assistant's response to the database
				ChatMessage::create([
					'session_id' => $session_id,
					'role' => 'user',
					'message' => $userPrompt,
					'llm' => $llm,
					'prompt_tokens' => 0,
					'completion_tokens' => 0
				]);

				ChatMessage::create([
					'session_id' => $session_id,
					'role' => 'assistant',
					'message' => $resultData['content'],
					'llm' => $llm,
					'prompt_tokens' => $resultData['prompt_tokens'] ?? 0,
					'completion_tokens' => $resultData['completion_tokens'] ?? 0
				]);


				return response()->json(['success' => true, 'result' => $resultData]);
			} catch (\Exception $e) {
				return response()->json(['success' => false, 'message' => $e->getMessage()]);
			}
		}

		public function destroy($sessionId)
		{
			$chatSession = ChatSession::where('session_id', $sessionId)
				->where('user_id', Auth::id())
				->first();

			if (!$chatSession) {
				return response()->json(['success' => false, 'message' => 'Session not found']);
			}

			// Delete associated messages first
			ChatMessage::where('session_id', $chatSession->id)->delete();

			// Delete the session
			$chatSession->delete();

			return response()->json(['success' => true]);
		}


		public function index(Request $request, $session_id = null)
		{
			if (!Auth::check()) {
				return redirect()->route('login');
			}

			return view('backend.chat', ['current_session_id' => $session_id]);
		}

		//-------------------------------------------------------------------------
		// AI AUTO GENERATION METHODS

		public function generateCategory(Request $request)
		{
			$mainText = $request->input('main_text');

			// Limit text length for the prompt
			if (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000) . '...';
			}

			// Get all main categories with their subcategories
			$categories = Category::where('parent_category_id', 0)
				->with('subCategories')
				->get();

			// Build the prompt with hierarchical category structure
			$prompt = "Analyze this text and suggest the most appropriate category. Respond in this format:\n";
			$prompt .= "Main Category: [main category name]\n";
			$prompt .= "Sub Category: [sub category name]\n\n";
			$prompt .= "Available categories:\n\n";

			foreach ($categories as $mainCategory) {
				$prompt .= "* {$mainCategory->category_name}\n";
				foreach ($mainCategory->subCategories as $subCategory) {
					$prompt .= "  - {$subCategory->category_name}\n";
				}
			}

			$prompt .= "\nText to analyze:\n{$mainText}\n\n";
			$prompt .= "Remember to respond only with the category names in the specified format.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call(
					'anthropic/claude-3.7-sonnet:beta',
					'',
					$chat_history,
					false
				);

				if ($result['error']) {
					Log::error('Error in generate Category LLM call: ' . $result['content']);
					return response()->json(['error' => $result['content']], 500);
				}

				// Parse the response to extract main and sub category
				$response = $result['content'];
				preg_match('/Main Category: (.*?)\nSub Category: (.*?)(?:\n|$)/s', $response, $matches);

				if (count($matches) >= 3) {
					$mainCategoryName = trim($matches[1]);
					$subCategoryName = trim($matches[2]);

					// Find the matching categories in the database
					$mainCategory = Category::where('category_name', 'LIKE', "%{$mainCategoryName}%")
						->where('parent_category_id', 0)
						->first();

					if ($mainCategory) {
						$subCategory = Category::where('category_name', 'LIKE', "%{$subCategoryName}%")
							->where('parent_category_id', $mainCategory->id)
							->first();

						if ($subCategory) {
							return response()->json([
								'category_id' => $subCategory->id,
								'main_category_id' => $mainCategory->id,
								'main_category_name' => $mainCategory->category_name,
								'sub_category_name' => $subCategory->category_name
							]);
						}
					}
				}

				return response()->json([
					'error' => 'Could not determine appropriate category'
				], 422);

			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}

		public function generateDescription(Request $request)
		{
			$mainText = $request->input('main_text');
			if (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000) . '...';
			}

			$prompt = "Create a brief, engaging description (maximum 500 characters) for this text:\n\n{$mainText}\n\nRespond with only the description. Respond in Turkish.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call('anthropic/claude-3.7-sonnet:beta', '', $chat_history, false);

				if ($result['error']) {
					Log::error('Error in Description call: ' . $result['content']);
					return response()->json(['success' => false, 'description' => $result->content]);
				}

				return response()->json([
					'description' => trim($result['content'])
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}

		public function generateKeywords(Request $request)
		{
			$mainText = $request->input('main_text');
			$mainText = $request->input('main_text');
			if (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000) . '...';
			}

			$prompt = "Generate 5-10 relevant keywords (maximum 16 characters each) for this text. Separate keywords with commas:\n\n{$mainText}\n\nRespond with only the comma-separated keywords. Respond in Turkish.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call('anthropic/claude-3.7-sonnet:beta', '', $chat_history, false);

				if ($result['error']) {
					Log::error('Error in Keywords call: ' . $result['content']);
					return response()->json(['error' => $result['content']], 500);
				}

				$keywords = array_map('trim', explode(',', $result['content']));
				$keywords = array_map(function ($keyword) {
					return ['value' => $keyword];
				}, $keywords);

				return response()->json([
					'keywords' => $keywords
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}

		public function generateBookCategory(Request $request)
		{
			$mainText = $request->input('main_text');
			$prompt = "Analyze the following book review text and suggest up to 3 relevant categories for it. The text is in Turkish. Categories should be concise and common for book genres (e.g., Roman, Bilim Kurgu, Polisiye, Felsefe, Tarih).
        Text: \"{$mainText}\"
        Output JSON as: ``` { \"categories\": [\"category1\", \"category2\"] } ```";

			$llm_result = MyHelper::llm_no_tool_call(
				'openai/gpt-4.1-mini',
				'',
				[['role' => 'user', 'content' => $prompt]],
				true
			);

			if ($llm_result['error']) {
				return response()->json(['error' => $llm_result['content']], 500);
			}

			return response()->json(['categories' => $llm_result['categories'] ?? []]);
		}

		public function generateBookKeywords(Request $request)
		{
			$mainText = $request->input('main_text');
			$prompt = "Analyze the following book review text and suggest 5-7 relevant keywords (tags). The text is in Turkish. Keywords should be single words or short phrases that capture the main themes, concepts, or style of the book.
        Text: \"{$mainText}\"
        Output JSON as: ``` { \"keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"] } ```";

			$llm_result = MyHelper::llm_no_tool_call(
				'openai/gpt-4.1-mini',
				'',
				[['role' => 'user', 'content' => $prompt]],
				true
			);

			if ($llm_result['error']) {
				return response()->json(['error' => $llm_result['content']], 500);
			}

			return response()->json(['keywords' => $llm_result['keywords'] ?? []]);
		}

	}
