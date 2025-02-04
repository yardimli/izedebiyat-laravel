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

		public function createSession(Request $request) {
			$chatSession = ChatSession::create([
				'session_id' => (string) Str::uuid(), // Generate UUID for session_id
				'user_id' => Auth::id(),
				'created_at' => now(),
				'updated_at' => now(),
			]);

			return response()->json(['session_id' => $chatSession->session_id]); // Return session_id instead of id
		}

		public function getChatSessions()
		{
			$sessions = ChatSession::where('user_id', Auth::id())
				->whereHas('messages', function($query) {
					$query->where('id', '>', 1);  // Or any other condition you might want
				})
				->with(['messages' => function($query) {
					$query->orderBy('created_at', 'asc');
				}])
				->orderBy('updated_at', 'desc')
				->get();

			return response()->json($sessions);
		}

		public function getChatMessages($sessionId) {

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
					->orderBy('created_at')
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

				if (isset($resultData->error)) {
					return response()->json(['success' => false, 'message' => $resultData->error]);
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
			ıf (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000).'...';
			}

			$prompt = "Analyze this text and suggest the most appropriate category from the following options:\n\n";
			$categories = Category::where('parent_category_id', '!=', 0)->get();
			foreach ($categories as $category) {
				$prompt .= "- {$category->category_name}\n";
			}
			$prompt .= "\nText to analyze:\n{$mainText}\n\nRespond with only the category name.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call('anthropic/claude-3.5-sonnet:beta', '', $chat_history, false);

				$suggestedCategory = trim($result['content']);
				$category = Category::where('category_name', 'LIKE', "%{$suggestedCategory}%")->first();

				return response()->json([
					'category_id' => $category ? $category->id : null
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}

		public function generateDescription(Request $request)
		{
			$mainText = $request->input('main_text');
			ıf (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000).'...';
			}

			$prompt = "Create a brief, engaging description (maximum 500 characters) for this text:\n\n{$mainText}\n\nRespond with only the description. Respond in Turkish.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call('anthropic/claude-3.5-sonnet:beta', '', $chat_history, false);

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
			ıf (strlen($mainText) > 1000) {
				$mainText = substr($mainText, 0, 1000).'...';
			}

			$prompt = "Generate 5-10 relevant keywords (maximum 16 characters each) for this text. Separate keywords with commas:\n\n{$mainText}\n\nRespond with only the comma-separated keywords. Respond in Turkish.";

			try {
				$chat_history = [
					['role' => 'user', 'content' => $prompt]
				];

				$result = MyHelper::llm_no_tool_call('anthropic/claude-3.5-sonnet:beta', '', $chat_history, false);

				$keywords = array_map('trim', explode(',', $result['content']));
				$keywords = array_map(function($keyword) {
					return ['value' => $keyword];
				}, $keywords);

				return response()->json([
					'keywords' => $keywords
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		}

	}
