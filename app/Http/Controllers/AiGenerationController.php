<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiGenerationController extends Controller
{
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
					'openai/gpt-5.6-luna',
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
					'error' => __('Could not determine appropriate category')
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

				$result = MyHelper::llm_no_tool_call('openai/gpt-5.6-luna', '', $chat_history, false);

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

				$result = MyHelper::llm_no_tool_call('openai/gpt-5.6-luna', '', $chat_history, false);

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
				'openai/gpt-5.6-luna',
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
				'openai/gpt-5.6-luna',
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
