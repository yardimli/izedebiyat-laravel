<?php
	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Image;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;

	class ImageController extends Controller
	{
		public function index(Request $request)
		{
			$perPage = 9;

			// Get uploaded images with union
			$userImages = Image::where('user_id', Auth::id())
				->orderBy('created_at', 'desc')
				->paginate($perPage);


			return response()->json([
				'images' => $userImages,
				'pagination' => [
					'current_page' => $userImages->currentPage(),
					'last_page' => $userImages->lastPage(),
					'per_page' => $userImages->perPage(),
					'total' => $userImages->total()
				]
			]);
		}

		private function resizeImage($sourcePath, $destinationPath, $maxWidth)
		{
			list($originalWidth, $originalHeight, $type) = getimagesize($sourcePath);

			// Calculate new dimensions
			$ratio = $originalWidth / $originalHeight;
			$newWidth = min($maxWidth, $originalWidth);
			$newHeight = $newWidth / $ratio;

			// Create new image
			$newImage = imagecreatetruecolor($newWidth, $newHeight);

			// Handle transparency for PNG images
			if ($type == IMAGETYPE_PNG) {
				imagealphablending($newImage, false);
				imagesavealpha($newImage, true);
				$transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
				imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
			}

			// Load source image
			switch ($type) {
				case IMAGETYPE_JPEG:
					$source = imagecreatefromjpeg($sourcePath);
					break;
				case IMAGETYPE_PNG:
					$source = imagecreatefrompng($sourcePath);
					break;
				case IMAGETYPE_GIF:
					$source = imagecreatefromgif($sourcePath);
					break;
				default:
					return false;
			}

			// Resize
			imagecopyresampled(
				$newImage,
				$source,
				0, 0, 0, 0,
				$newWidth,
				$newHeight,
				$originalWidth,
				$originalHeight
			);

			// Save resized image
			switch ($type) {
				case IMAGETYPE_JPEG:
					imagejpeg($newImage, $destinationPath, 90);
					break;
				case IMAGETYPE_PNG:
					imagepng($newImage, $destinationPath, 9);
					break;
				case IMAGETYPE_GIF:
					imagegif($newImage, $destinationPath);
					break;
			}

			// Free up memory
			imagedestroy($newImage);
			imagedestroy($source);

			return true;
		}

		public function store(Request $request)
		{
			$request->validate([
				'image' => 'required|image|max:5120', // 5MB max
				'alt' => 'nullable|string|max:255'
			]);

			// Create directories if they don't exist
			$directories = ['original', 'large', 'medium', 'small'];
			foreach ($directories as $dir) {
				if (!Storage::disk('public')->exists("upload-images/$dir")) {
					Storage::disk('public')->makeDirectory("upload-images/$dir");
				}
			}

			$image = $request->file('image');
			$guid = Str::uuid();
			$extension = $image->getClientOriginalExtension();

			// Generate filenames
			$originalFilename = $guid . '.' . $extension;
			$largeFilename = $guid . '_large.' . $extension;
			$mediumFilename = $guid . '_medium.' . $extension;
			$smallFilename = $guid . '_small.' . $extension;

			// Save original file
			$originalPath = storage_path('app/public/upload-images/original/' . $originalFilename);
			move_uploaded_file($image->getPathname(), $originalPath);

			// Create resized versions
			$this->resizeImage(
				$originalPath,
				storage_path('app/public/upload-images/large/' . $largeFilename),
				1200
			);
			$this->resizeImage(
				$originalPath,
				storage_path('app/public/upload-images/medium/' . $mediumFilename),
				600
			);
			$this->resizeImage(
				$originalPath,
				storage_path('app/public/upload-images/small/' . $smallFilename),
				300
			);

			// Save to database
			$imageModel = Image::create([
				'user_id' => Auth::id(),
				'image_type' => 'upload',
				'image_guid' => $guid,
				'image_alt' => $request->alt ?? $image->getClientOriginalName(),
				'image_original_filename' => $originalFilename,
				'image_large_filename' => $largeFilename,
				'image_medium_filename' => $mediumFilename,
				'image_small_filename' => $smallFilename
			]);

			return response()->json($imageModel);
		}

		public function update(Request $request, $id)
		{
			$request->validate([
				'alt' => 'required|string|max:255'
			]);

			$image = Image::where('user_id', Auth::id())
				->findOrFail($id);

			$image->update([
				'image_alt' => $request->alt
			]);

			return response()->json($image);
		}

		public function destroy($id)
		{
			$image = Image::where('user_id', Auth::id())
				->findOrFail($id);

			// Delete physical files
			$files = [
				'upload-images/original/' . $image->image_original_filename,
				'upload-images/large/' . $image->image_large_filename,
				'upload-images/medium/' . $image->image_medium_filename,
				'upload-images/small/' . $image->image_small_filename
			];

			foreach ($files as $file) {
				if (Storage::disk('public')->exists($file)) {
					Storage::disk('public')->delete($file);
				}
			}

			$image->delete();
			return response()->json(['success' => true]);
		}

		public function makeImage(Request $request)
		{
			if (!Auth::check()) {
				return [];
			}

			$model = 'https://queue.fal.run/fal-ai/flux/schnell';
			$size = 'landscape_4_3';

			//square_hd,square,portrait_4_3,portrait_16_9,landscape_4_3,landscape_16_9

			//$prompt_enhancer = $request->input('prompt_enhancer', '##UserPrompt##');
			$prompt_enhancer = '##UserPrompt##
Write a prompt to create an image using the above text.:
Write in English even if the above text is written in another language.
With the above information, compose a image. Write it as a single paragraph. The instructions should focus on the text elements of the image. If the prompt above mentions texts then add them with the instructions of placement. The texts should not repeat. If no texts are mentioned don\'t add anything to the prompt.';

			if ($prompt_enhancer === null || $prompt_enhancer === '') {
				$prompt_enhancer = '##UserPrompt##';
			}
			$user_prompt = $request->input('user_prompt', 'A fantasy picture of a cat');
			if ($user_prompt === null || $user_prompt === '') {
				$user_prompt = 'A fantasy picture of a cat';
			}
			$gpt_prompt = str_replace('##UserPrompt##', $user_prompt, $prompt_enhancer);
			//$llm = $request->input('llm');
			$llm = 'anthropic/claude-3.5-sonnet:beta';

			$chat_history[] = [
				'role' => 'user',
				'content' => $gpt_prompt,
			];


			$image_prompt = MyHelper::llm_no_tool_call($llm, '', $chat_history, false);
			Log::info('Enhanced Cover Image Prompt');
			Log::info($image_prompt);

			$falApiKey = $_ENV['FAL_API_KEY'];
			if (empty($falApiKey)) {
				echo json_encode(['error' => 'FAL_API_KEY environment variable is not set']);
			}

			$client = new \GuzzleHttp\Client();

			$response = $client->post($model, [
				'headers' => [
					'Authorization' => 'Key ' . $falApiKey,
					'Content-Type' => 'application/json',
				],
				'json' => [
					'prompt' => $image_prompt['content'],
					'image_size' => $size,
					'safety_tolerance' => '5',
				]
			]);
			Log::info('FLUX image response');
			Log::info($response->getBody());

			$body = $response->getBody();
			$data = json_decode($body, true);

			if ($response->getStatusCode() == 200) {

				$status_url = $data['status_url'];
				$check_count = 0;
				$check_limit = 10;
				$response_url = '';
				while ($check_count < $check_limit) {
					$response = $client->get($status_url, [
						'headers' => [
							'Authorization' => 'Key ' . $falApiKey,
							'Content-Type' => 'application/json',
						]
					]);
					Log::info('FLUX image status response');
					Log::info($response->getBody());

					$body = $response->getBody();
					$data = json_decode($body, true);
					if ($data['status'] == 'COMPLETED') {
						$response_url = $data['response_url'];
						break;
					}
					sleep(1);
					$check_count++;
				}

				if ($response_url !== '') {
					$response = $client->get($response_url, [
						'headers' => [
							'Authorization' => 'Key ' . $falApiKey,
							'Content-Type' => 'application/json',
						]
					]);
					Log::info('FLUX image status response');
					Log::info($response->getBody());
					$body = $response->getBody();
					$data = json_decode($body, true);
				}

				if (isset($data['images'][0]['url'])) {
					$image_url = $data['images'][0]['url'];
					$image = file_get_contents($image_url);


					if (!Storage::disk('public')->exists('ai-images')) {
						Storage::disk('public')->makeDirectory('ai-images');
					}

					// Create directories if they don't exist
					$directories = ['original', 'large', 'medium', 'small'];
					foreach ($directories as $dir) {
						if (!Storage::disk('public')->exists("ai-images/$dir")) {
							Storage::disk('public')->makeDirectory("ai-images/$dir");
						}
					}

					$guid = Str::uuid();

					$extension = 'jpg';

					// Generate filenames
					$originalFilename = $guid . '.' . $extension;
					$largeFilename = $guid . '_large.' . $extension;
					$mediumFilename = $guid . '_medium.' . $extension;
					$smallFilename = $guid . '_small.' . $extension;

					$outputFile = Storage::disk('public')->path('ai-images/' . $originalFilename);
					file_put_contents($outputFile, $image);

					// Create resized versions
					$this->resizeImage(
						$outputFile,
						storage_path('app/public/ai-images/large/' . $largeFilename),
						1024,
					);
					$this->resizeImage(
						$outputFile,
						storage_path('app/public/ai-images/medium/' . $mediumFilename),
						600
					);
					$this->resizeImage(
						$outputFile,
						storage_path('app/public/ai-images/small/' . $smallFilename),
						300
					);

					// Save to database
					$imageModel = Image::create([
						'user_id' => Auth::id(),
						'image_type' => 'generated',
						'image_guid' => $guid,
						'image_alt' => '',
						'user_prompt' => $user_prompt,
						'llm_prompt' => $prompt_enhancer,
						'llm' => $llm,
						'prompt_tokens' => $image_prompt['prompt_tokens'] ?? 0,
						'completion_tokens' => $image_prompt['completion_tokens'] ?? 0,
						'image_original_filename' => $originalFilename,
						'image_large_filename' => $largeFilename,
						'image_medium_filename' => $mediumFilename,
						'image_small_filename' => $smallFilename
					]);

					return json_encode([
						'success' => true,
						'message' => __('Image generated successfully'),
						'image_large_filename' => $largeFilename,
						'image_medium_filename' => $mediumFilename,
						'image_small_filename' => $smallFilename,
						'data' => json_encode($data),
						'seed' => $data['seed'],
						'status_code' => $response->getStatusCode(),
						'user_prompt' => $user_prompt,
						'llm_prompt' => $prompt_enhancer,
						'image_prompt' => $image_prompt['content'],
						'prompt_tokens' => $image_prompt['prompt_tokens'] ?? 0,
						'completion_tokens' => $image_prompt['completion_tokens'] ?? 0
					]);
				} else {
					return json_encode(['success' => false, 'message' => __('Error (2) generating image'), 'status_code' => $response->getStatusCode()]);
				}
			} else {
				return json_encode(['success' => false, 'message' => __('Error (1) generating image'), 'status_code' => $response->getStatusCode()]);
			}
		}

		public function destroyGenImage($id)
		{
			if (!Auth::check()) {
				return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
			}

			$image = Image::where('id', $id)
				->where('user_id', Auth::id())
				->first();

			if (!$image) {
				return response()->json(['success' => false, 'message' => 'Record not found'], 404);
			}

			// Delete the image file
			if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
				Storage::disk('public')->delete($image->image_path);
			}

			// Delete the database record
			$image->delete();

			return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
		}
	}
