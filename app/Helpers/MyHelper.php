<?php

	namespace App\Helpers;

	use App\Models\Article;
	use App\Models\Keyword;
	use App\Models\SentencesTable;
	use App\Models\User;
	use Carbon\Carbon;
	use GuzzleHttp\Client;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Str;
	use Intervention\Image\ImageManagerStatic as Image;
	use Ahc\Json\Fixer;
	use League\HTMLToMarkdown\HtmlConverter;

	class MyHelper
	{
		private static $categoryImages = [];
		private static $unwantedArray = [
			'Š' => 'S',
			'š' => 's',
			'Ž' => 'Z',
			'ž' => 'z',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'A',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'B',
			'ß' => 'Ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'a',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'o',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ý' => 'y',
			'þ' => 'b',
			'ÿ' => 'y',
			'Ş' => 's',
			'ş' => 's',
			'İ' => 'i',
			'ı' => 'i',
			'(' => '',
			')' => '',
			'.' => '',
			',' => '',
			':' => '',
			';' => '',
			'&' => '_',
			'ğ' => 'g',
			'ü' => 'u'
		];


		public static function initializeCategoryImages()
		{
			$categories = DB::table('categories')->get();
			foreach ($categories as $category) {
				self::$categoryImages[$category->id] = $category->picture;
			}
		}

		public static function generateInitialsAvatar($pictureFile, $email, $extraCss = 'border-radius: 0px;', $extraClass = 'avatar')
		{
			// First, check if it's a Gmail account
			if (strpos(strtolower($email), '@gmail.com') !== false && empty($pictureFile)) {
				try {
					$client = new \GuzzleHttp\Client();
					$response = $client->request('GET', 'https://gmail-checker4.p.rapidapi.com/GmailCheck', [
						'query' => ['email' => $email],
						'headers' => [
							'x-rapidapi-host' => 'gmail-checker4.p.rapidapi.com',
							'x-rapidapi-key' => env('RAPID_API_KEY')
						]
					]);

					$result = json_decode($response->getBody(), true);

					if ($result['code'] === 200 && !empty($result['picture'])) {
						// Update user's picture in the database
						$user = User::where('email', $email)->first();
						if ($user) {
							// Save the image locally
							$imageContents = file_get_contents($result['picture']);
							$filename = 'avatars/' . md5($email) . '.jpg';
							Storage::disk('public')->put($filename, $imageContents);

							$user->avatar = $filename;
							$user->picture = $filename;
							$user->save();

							return "<img alt='yazar' style='{$extraCss}' src='/storage/{$filename}' class='{$extraClass}'>";
						}
					}
				} catch (\Exception $e) {
					// If there's any error, fall back to default avatar
					Log::error('Gmail avatar fetch error: ' . $e->getMessage());
				}
			}

			// Check for existing picture file
			if ($pictureFile !== null && $pictureFile !== "") {
				$pictureFile = str_replace('public/', '', $pictureFile);
				$pictureFile = str_replace('public\\', '', $pictureFile);
				if (Storage::disk('public')->exists($pictureFile)) {
					return "<img alt='yazar' style='{$extraCss}' src='/storage/{$pictureFile}' class='{$extraClass}'>";
				}
			}

			// Fallback to Gravatar
			return "<img alt='yazar' style='{$extraCss}' src='https://www.gravatar.com/avatar/" . md5($email) . "?s=200&d=mp' class='{$extraClass}'>";
		}

		public static function getImage($articleMainImage, $categoryId = '', $extraClass = '', $extraStyle = '', $resize_image_size = 'small_landscape')
		{
			$articleMainImage = str_ireplace('.png', '.jpg', $articleMainImage);
			$articleMainImage = str_replace('\\', '/', $articleMainImage);

			$aiResim = '';
			if (strpos($articleMainImage, '_00001_') !== false) {
				$aiResim = '<span class="ai-article-image" data-toggle="tooltip" data-placement="top" 
                        title="Yapay zekaya yazının içeriğini vererek üretildi">YZ</span>';
			}

			$storage_path = $articleMainImage;
			$storage_path = str_replace('storage/', '', $storage_path);

			if ($storage_path !== '' && Storage::disk('public')->exists($storage_path)) {

				if ($resize_image_size==='large_landscape') {
					$image_src = 'storage' . $storage_path;
				} else
				{
					$image_src = resize('storage' . $storage_path, $resize_image_size);
				}

				return "<div style='position:relative;'>
                    <img src='/{$image_src}' class='{$extraClass}' 
                    style='{$extraStyle}' alt='yazı resim'></div>";
			}

			if ($articleMainImage !== '' && Storage::disk('public')->exists("yazi_resimler/" . $articleMainImage)) {

				if ($resize_image_size==='large_landscape') {
					$image_src = 'storage/yazi_resimler/' . $articleMainImage;
				} else
				{
					$image_src = resize('storage/yazi_resimler/' . $articleMainImage, $resize_image_size);
				}

				return "<div style='position:relative;'>
                    <img src='/{$image_src}' class='{$extraClass}' 
                    style='{$extraStyle}' alt='yazı resim'>{$aiResim}</div>";
			}

			if (key_exists($categoryId, self::$categoryImages)) {
				if ($resize_image_size==='large_landscape') {
					$image_src = 'storage/catpicbox/' . self::$categoryImages[$categoryId];
				} else
				{
					$image_src = resize('storage/catpicbox/' . self::$categoryImages[$categoryId], $resize_image_size);
				}

				return "<img src='/{$image_src}' 
                class='{$extraClass}' style='{$extraStyle}' alt='yazı resim'>";
			} else {
				echo "Category image not found for category id: " . $categoryId;
			}
		}

		public static function timeElapsedString($datetime, $full = false)
		{
			$carbon = Carbon::parse($datetime);
			$now = Carbon::now();

			$yearsDiff = $now->diffInYears($carbon);

			if ($yearsDiff >= 1) {
				// Custom formatting for Turkish abbreviated months with period
				$months = [
					'01' => 'Oca',
					'02' => 'Şub',
					'03' => 'Mar',
					'04' => 'Nis',
					'05' => 'May',
					'06' => 'Haz',
					'07' => 'Tem',
					'08' => 'Ağu',
					'09' => 'Eyl',
					'10' => 'Eki',
					'11' => 'Kas',
					'12' => 'Ara'
				];

				return $carbon->format('d ') . $months[$carbon->format('m')] . $carbon->format(' Y');
			} else {
				return $carbon->locale('tr')->diffForHumans();
			}
		}

		public static function timeString($datetime, $full = false)
		{
			$carbon = Carbon::parse($datetime);
			$now = Carbon::now();

			$months = [
				'01' => 'Oca',
				'02' => 'Şub',
				'03' => 'Mar',
				'04' => 'Nis',
				'05' => 'May',
				'06' => 'Haz',
				'07' => 'Tem',
				'08' => 'Ağu',
				'09' => 'Eyl',
				'10' => 'Eki',
				'11' => 'Kas',
				'12' => 'Ara'
			];

			return $carbon->format('d ') . $months[$carbon->format('m')] . $carbon->format(' Y');
		}

		public static function estimatedReadingTime($content = '', $wpm = 250)
		{
			$cleanContent = strip_tags($content);
			$wordCount = str_word_count($cleanContent);
			$time = ceil($wordCount / $wpm);

			if ($time == 0) return "1 dk okuma";
			if ($time >= 60) {
				$hours = floor($time / 60);
				$minutes = ($time % 60);

				if ($minutes === 0) return "{$hours} saat okuma";
				return "{$hours} saat, {$minutes} dk okuma";
			}

			return "{$time} dk okuma";
		}

		public static function removeBadCharacters($input)
		{
			$badChars = array_map('chr', range(0, 31));
			$badChars[] = chr(127);
			return str_replace($badChars, '', $input);
		}

		public static function getWords($sentence, $count = 10, $keepBreaks = true)
		{
			$sentence = trim($sentence);
			// Remove [[...]] content
			$sentence = preg_replace('/\[\[.*?\]\]/', '', $sentence);

			// Normalize <br> tags
			$sentence = str_ireplace(['<br/>', '<br>'], ']]br[[', $sentence);

			// Remove all HTML tags except <br>
			$sentence = strip_tags($sentence);

			// replace <> with &lt; &gt;
			$sentence = str_replace(['<', '>'], ['&lt;', '&gt;'], $sentence);

			// Replace ]]br[[ with <br>
			$sentence = str_ireplace(']]br[[', '<br>', $sentence);

			$firstLines = "";
			$lines = explode('<br>', $sentence);

			$i = 1;
			foreach ($lines as $line) {
				if (trim($line) !== "") {
					$firstLines .= $keepBreaks ? $line . "<br>" : $line . " / ";
					$i++;
					if ($i >= 6) break;
				}
			}

			return implode(' ', array_slice(explode(' ', $firstLines), 0, $count));
		}

		public static function replaceAscii($input)
		{
			// First, handle line breaks and special characters
			$input = preg_replace("/\r\n|\r|\n/", '<br/>', $input);
			$input = str_replace("", "...", $input);
			$input = str_replace("", "-", $input);
			$input = str_replace("", " ", $input);
			$input = str_replace("", "'", $input);
			$input = str_replace("", " ", $input);
			$input = str_replace("  ", " ", $input);

			// Remove leading and trailing <br/> tags
			$input = preg_replace('/^(\s*<br\s*\/?>\s*)*/', '', $input);
			$input = preg_replace('/(\s*<br\s*\/?>\s*)*$/', '', $input);

			// Calculate uppercase percentage
			$totalChars = mb_strlen($input, 'UTF-8');
			if ($totalChars > 0) {
				$uppercaseChars = mb_strlen(preg_replace('/[^A-ZĞÜŞİÖÇ]/u', '', $input), 'UTF-8');
				$uppercasePercentage = ($uppercaseChars / $totalChars) * 100;

				// If more than 50% uppercase, convert to Title Case
				if ($uppercasePercentage > 50) {
					// Convert to lowercase first
					$input = mb_strtolower($input, 'UTF-8');

					// Split into words
					$words = preg_split('/\b/u', $input);
					$result = '';

					foreach ($words as $word) {
						if (mb_strlen($word, 'UTF-8') > 0) {
							// Convert first letter to uppercase
							$firstChar = mb_substr($word, 0, 1, 'UTF-8');
							$restOfWord = mb_substr($word, 1, null, 'UTF-8');

							// Special handling for Turkish i/İ
							if ($firstChar === 'i') {
								$result .= 'İ' . $restOfWord;
							} else {
								$result .= mb_strtoupper($firstChar, 'UTF-8') . $restOfWord;
							}
						} else {
							$result .= $word;
						}
					}

					$input = $result;
				}
			}

			return $input;
		}

		//------------------------------------------------------------

		public static function checkLLMsJson()
		{
			$llmsJsonPath = Storage::disk('public')->path('llms.json');

			if (!File::exists($llmsJsonPath) || Carbon::now()->diffInDays(Carbon::createFromTimestamp(File::lastModified($llmsJsonPath))) > 1) {
				$client = new Client();
				$response = $client->get('https://openrouter.ai/api/v1/models');
				$data = json_decode($response->getBody(), true);

				if (isset($data['data'])) {
					File::put($llmsJsonPath, json_encode($data['data']));
				} else {
					return [];
				}
			}

			$openrouter_admin_or_key = false;
//			if ((Auth::user() && Auth::user()->isAdmin()) ||
//				(Auth::user() && !empty(Auth::user()->openrouter_key))) {
//				$openrouter_admin_or_key = true;
//			}

			$llms = json_decode(File::get($llmsJsonPath), true);
			$filtered_llms = array_filter($llms, function ($llm) use ($openrouter_admin_or_key) {
				if (isset($llm['id']) && (stripos($llm['id'], 'openrouter/auto') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], 'vision') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-3-') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-7b-') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-7b ') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-8b-') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-8b ') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-3b-') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], '-1b-') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], 'online') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], 'gpt-3.5') !== false)) {
					return false;
				}

				if (isset($llm['name']) && (
						(stripos($llm['name'], 'openai') !== false) ||
						(stripos($llm['name'], 'anthropic') !== false) ||
						(stripos($llm['name'], 'gemini flash') !== false) ||
						(stripos($llm['name'], 'deepseek v3') !== false) ||
						(stripos($llm['name'], 'command r') !== false) ||
						(stripos($llm['name'], 'midnight rose') !== false)
					)) {
					//... do nothing
				} else {
					return false;
				}

				if (isset($llm['pricing']['completion'])) {
					$price_per_million = floatval($llm['pricing']['completion']) * 1000000;
					if ($openrouter_admin_or_key) {
						return $price_per_million <= 20;
					} else {
						return $price_per_million <= 4;
					}
				}

				if (!isset($llm['pricing']['completion'])) {
					return false;
				}

				return true;
			});


			usort($filtered_llms, function ($a, $b) {
				return strcmp($a['name'], $b['name']);
			});

			//for each llm with score 0 sort them alphabetically
			return array_values($filtered_llms);
		}

		public static function moderation($message)
		{

			//make sure $message can be json encoded
			if (!self::isValidUtf8($message)) {
				$message = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $message);
			}


			$response = Http::withHeaders([
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . env('OPEN_AI_API_KEY'),
			])->post(env('OPEN_AI_API_BASE_MODERATION'), [
				'input' => $message,
			]);

			return $response->json();
		}

		public static function validateJson($str)
		{
			Log::debug('Starting JSON validation.');

			$error = json_last_error();
			json_decode($str);
			$error = json_last_error();

			switch ($error) {
				case JSON_ERROR_NONE:
					return "Valid JSON";
				case JSON_ERROR_DEPTH:
					return "Maximum stack depth exceeded";
				case JSON_ERROR_STATE_MISMATCH:
					return "Underflow or the modes mismatch";
				case JSON_ERROR_CTRL_CHAR:
					return "Unexpected control character found";
				case JSON_ERROR_SYNTAX:
					return "Syntax error, malformed JSON";
				case JSON_ERROR_UTF8:
					return "Malformed UTF-8 characters, possibly incorrectly encoded";
				default:
					return "Unknown error";
			}
		}

		public static function repaceNewLineWithBRInsideQuotes($input)
		{
			$output = '';
			$inQuotes = false;
			$length = strlen($input);
			$i = 0;

			while ($i < $length) {
				$char = $input[$i];

				if ($char === '"') {
					$inQuotes = !$inQuotes;
					$output .= $char;
				} elseif ($inQuotes) {
					if ($char === "\n" || $char === "\r") {
						$output .= '<BR>';
						if ($char === "\r" && $i + 1 < $length && $input[$i + 1] === "\n") {
							$i++; // Skip the next character if it's a Windows-style line ending (\r\n)
						}
					} elseif ($char === '\\') {
						if ($i + 1 < $length) {
							$nextChar = $input[$i + 1];
							if ($nextChar === 'n') {
								$output .= '<BR>';
								$i++;
							} elseif ($nextChar === 'r') {
								$output .= '<BR>';
								$i++;
								if ($i + 1 < $length && $input[$i + 1] === '\\' && $i + 2 < $length && $input[$i + 2] === 'n') {
									$i += 2; // Skip the next two characters if it's \\r\\n
								}
							} elseif ($nextChar === '\\') {
								if ($i + 2 < $length) {
									$nextNextChar = $input[$i + 2];
									if ($nextNextChar === 'n' || $nextNextChar === 'r') {
										$output .= '<BR>';
										$i += 2;
										if ($nextNextChar === 'r' && $i + 2 < $length && $input[$i + 1] === '\\' && $input[$i + 2] === 'n') {
											$i += 2; // Skip the next two characters if it's \\r\\n
										}
									} else {
										$output .= $char;
									}
								} else {
									$output .= $char;
								}
							} else {
								$output .= $char;
							}
						} else {
							$output .= $char;
						}
					} else {
						$output .= $char;
					}
				} else {
					$output .= $char;
				}
				$i++;
			}

			return $output;
		}

		public static function getContentsInBackticksOrOriginal($input)
		{
			// Define a regular expression pattern to match content within backticks
			$pattern = '/`([^`]+)`/';

			// Initialize an array to hold matches
			$matches = array();

			// Perform a global regular expression match
			preg_match_all($pattern, $input, $matches);

			// Check if any matches were found
			if (empty($matches[1])) {
				return $input; // Return the original input if no matches found
			} else {
				return implode(' ', $matches[1]);
			}
		}

		public static function extractJsonString($input)
		{
			// Find the first position of '{' or '['
			$startPos = strpos($input, '{');
			if ($startPos === false) {
				$startPos = strpos($input, '[');
			}

			// Find the last position of '}' or ']'
			$endPos = strrpos($input, '}');
			if ($endPos === false) {
				$endPos = strrpos($input, ']');
			}

			// If start or end positions are not found, return an empty string
			if ($startPos === false || $endPos === false) {
				return '';
			}

			// Extract the JSON substring
			$jsonString = substr($input, $startPos, $endPos - $startPos + 1);

			return $jsonString;
		}

		public static function mergeStringsWithoutRepetition($string1, $string2, $maxRepetitionLength = 100)
		{
			$len1 = strlen($string1);
			$len2 = strlen($string2);

			// Determine the maximum possible repetition length
			$maxPossibleRepetition = min($maxRepetitionLength, $len1, $len2);

			// Find the length of the actual repetition
			$repetitionLength = 0;
			for ($i = 1; $i <= $maxPossibleRepetition; $i++) {
				if (substr($string1, -$i) === substr($string2, 0, $i)) {
					$repetitionLength = $i;
				} else {
					break;
				}
			}

			// Remove the repetition from the beginning of the second string
			$string2 = substr($string2, $repetitionLength);

			// Merge the strings
			return $string1 . $string2;
		}

		public static function isValidUtf8($string)
		{
			return mb_check_encoding($string, 'UTF-8');
		}

		//------------------------------------------------------------

		public static function returnIsHarmful()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 50) {
				// Get stories using Laravel's query builder
				$articles = DB::table('articles')
					->where('bad_critical', -1)
					->where('deleted', 0)
					->where('approved', 1)
					->orderBy('id', 'DESC')
					->limit(10)
					->get();

				$continue = $articles->count() > 0;

				foreach ($articles as $article) {
					$counter++;

					$llm_result = self::llm_no_tool_call(
						'openai/gpt-4o-mini',
						'',
						[[
							'role' => 'user',
							'content' => "take the following Turkish, and analyze it for being harmful to a subset of people, such as ethnic, religious, sexual orientation, or other minority groups. Rate it from 0 to 5, where 0 is not harmful at all, and 5 is extremely harmful. If harmful provide a brief explanation of why you rated it as such. Return reason in English. If not harmful, leave the explanation empty. Write the explanation in Turkish. The text is as follows: Title: " . $article->title . " Subtitle: " . $article->subtitle . " Category: " . $article->parent_category_name . " Subcategory: " . $article->category_name . " text: " . $article->main_text . " output in Turkish, output JSON as: ``` { \"harmful\": \"0-5\" \"explanation in Turkish\": \"\" } ```"
						]],
						true
					);


					if ($llm_result['error']) {
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'bad_critical' => 999,
								'critical_reason' => $llm_result['content'],
								'has_changed' => 1
							]);
						echo $counter . " - Error: <br>\n";
						echo $article->id . " " . $article->title . ", kategori " . $article->parent_category_name . "/" . $article->category_name . "<br>\n";
						var_dump($llm_result);
						flush();
					} else {

						$harmful = $llm_result['harmful'] ?? '0';
						$explanation = $llm_result['explanation'] ?? '';

						// Update using Laravel's query builder
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'bad_critical' => $harmful,
								'critical_reason' => $explanation,
								'has_changed' => 1
							]);

						echo $counter . " - Moderation: - \n";
						if ($harmful > 0 || $explanation !== '') {
							echo $article->id . " " . $article->title . ", kategori " . $article->parent_category_name . "/" . $article->category_name . "<br>\n";
							echo "Harmful: " . $harmful . "<br>\n";
							echo "Explanation: " . $explanation . "<br>\n";
						} else {
							echo "id: " . $article->id . " - Not harmful.<br>\n";
						}
						flush();
					}

				}
			}
		}

		public static function returnReligiousReason()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 50) {
				// Get stories using Laravel's query builder
				$articles = DB::table('articles')
					->where('has_religious_moderation', 0)
					->where('deleted', 0)
					->where('approved', 1)
					->where('id', '>', 110000)
//					->where('bad_critical', '<', 4)
					->orderBy('id', 'DESC')
					->limit(10)
					->get();

				$continue = $articles->count() > 0;

				foreach ($articles as $article) {
					$continue = true;
					$counter++;

					// Get first 1000 words
					$article_text = $article->main_text;
					$article_words = explode(' ', $article_text);
					$article_text = implode(' ', array_slice($article_words, 0, 1000));

					$llm_result = self::llm_no_tool_call(
						'openai/gpt-4o-mini', //google/gemini-flash-1.5-8b
						'',
						[[
							'role' => 'user',
							'content' => "take the following Turkish text and analyze it if it is religious and also respectful towards other religions and beliefs. The text is as follows: Title: " . $article->title . " Subtitle: " . $article->subtitle . " Category: " . $article->parent_category_name . " Subcategory: " . $article->category_name . " text: " . $article_text . " output explantion in Turkish and in JSON as: ``` { \"religious_reason\": { \"religious\": 0-5, \"respect\": 0-5, \"reason\": \"write in Turkish reasoning for the moderation\" } } ```"
						]],
						true
					);

					if ($llm_result['error']) {
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'has_religious_moderation' => 1,
								'religious_reason' => '{"religious":99,"respect":0,"reason":"' . $llm_result['content'] . '"}',
								'has_changed' => 1
							]);
						echo $counter . " - Error: <br>\n";
						var_dump($llm_result);
						flush();
					} else {
						$moderation = $llm_result['religious_reason'] ?? '';
						$moderation = json_encode($moderation);

						// Update using Laravel's query builder
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'religious_reason' => $moderation,
								'has_religious_moderation' => 1,
								'has_changed' => 1
							]);

						echo $counter . "- Religious Moderation:<br>\n";
						echo $article->id . " " . $article->title . " -- " . $article->parent_category_name . '/' . $article->category_name . "<br>\n";
						echo $moderation . "<br>\n";
						flush();
					}
				}
			}
		}

		public static function returnModeration()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 50) {
				// Get stories using Laravel's query builder
				$articles = DB::table('articles')
					->where('has_moderation', -1)
					->where('approved', 1)
					->where('deleted', 0)
					->orderBy('id', 'DESC')
					->limit(10)
					->get();

				$continue = $articles->count() > 0;

				foreach ($articles as $article) {
					$continue = true;
					$counter++;

					$moderation = self::moderation($article->title . ' ' . $article->main_text);
					$moderation = json_encode($moderation['results']);
					echo $counter . "- Moderation:<br>\n";
					echo $moderation . "<br>\n";
					flush();


					DB::table('articles')
						->where('id', $article->id)
						->update([
							'moderation' => $moderation,
							'has_moderation' => 1,
							'has_changed' => 1
						]);
				}
			}
		}


		public static function fix_encoding($text)
		{
			$text = preg_replace("/\r\n|\r|\n/", ' <br> ', $text);
			$text = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $text);

			$text = str_replace('[[K]]', '<span class="bold-font">', $text);
			$text = str_replace('[[/K]]', '</span>', $text);
			$text = str_replace('[[K]', '<span class="bold-font">', $text);
			$text = str_replace('[[/K]', '</span>', $text);
			$text = str_replace('[K]]', '<span class="bold-font">', $text);
			$text = str_replace('[/K]]', '</span>', $text);
			$text = str_replace('[K]', '<span class="bold-font">', $text);
			$text = str_replace('[/K]', '</span>', $text);

			$text = str_replace('[[I]]', '<span class="italic-font">', $text);
			$text = str_replace('[[İ]]', '', $text);
			$text = str_replace('[[/I]]', '</span>', $text);
			$text = str_replace('[I]]', '<span class="italic-font">', $text);
			$text = str_replace('[/I]]', '</span>', $text);
			$text = str_replace('[[I]', '<span class="italic-font">', $text);
			$text = str_replace('[[/I]', '</span>', $text);
			$text = str_replace('[I]', '<span class="italic-font">', $text);
			$text = str_replace('[/I]', '</span>', $text);

			$text = str_replace('[[O]]', '', $text);
			$text = str_replace('[[/O]]', '', $text);
			$text = str_replace('[O]]', '', $text);
			$text = str_replace('[/O]]', '', $text);
			$text = str_replace('[[O]', '', $text);
			$text = str_replace('[[/O]', '', $text);
			$text = str_replace('[O]', '', $text);
			$text = str_replace('[/O]', '', $text);

			$text = str_replace('[[[SA]]', '', $text);
			$text = str_replace('[[[/SA]]', '', $text);
			$text = str_replace('[[SA]]', '', $text);
			$text = str_replace('[[/SA]]', '', $text);
			$text = str_replace('[[[SA]', '', $text);
			$text = str_replace('[[[/SA]', '', $text);
			$text = str_replace('[[SA]', '', $text);
			$text = str_replace('[[/SA]', '', $text);

			$text = str_replace('[[[SO]]', '', $text);
			$text = str_replace('[[[/SO]]', '', $text);
			$text = str_replace('[[SO]]', '', $text);
			$text = str_replace('[[/SO]]', '', $text);
			$text = str_replace('[[[SO]', '', $text);
			$text = str_replace('[[[/SO]', '', $text);
			$text = str_replace('[[SO]', '', $text);
			$text = str_replace('[[/SO]', '', $text);

			$text = str_replace('[[[A]]', '', $text);
			$text = str_replace('[[[/A]]', '', $text);
			$text = str_replace('[[A]]', '', $text);
			$text = str_replace('[[/A]]', '', $text);
			$text = str_replace('[[[A]', '', $text);
			$text = str_replace('[[[/A]', '', $text);
			$text = str_replace('[[A]', '', $text);
			$text = str_replace('[[/A]', '', $text);

			$text = str_replace('[[BP]]', '<br>', $text);
			$text = str_replace('[[PB]]', '<br>', $text);
			$text = str_replace('[[YS]]', '<br>', $text);
			$text = str_replace('[[BP]', '<br>', $text);
			$text = str_replace('[[PB]', '<br>', $text);
			$text = str_replace('[[YS]', '<br>', $text);
			$text = str_replace('[[P]]', '<br>', $text);

			$text = str_replace('search q=', 'search?q=', $text);

			$re = '/(\[\[|\[)( *)YB( *)\=([^\]]*)(\]\]|\])/im';
			$subst = '';
			$text = preg_replace($re, $subst, $text);

			$text = str_replace('[[/YB]', '', $text);

			$re = '/(\[\[|\[)( *)YR( *)\=([^\]]*)(\]\]|\])/mi';
			$subst = '';
			$text = preg_replace($re, $subst, $text);

			$re = '/\[\[( *)(RESİMSAĞ|RESİMSAG|RESİM SAĞ|RESİM SAG|RESIMSAG|RESIM SAG|RESÝMSAG|RESÝMSAÐ)( *)\=( *)([^]]+)( *)\]\]/mi';
			$subst = '<img src="$4" class="picture-left">';
			$text = preg_replace($re, $subst, $text);

			$re = '/\[\[( *)(RESİMSAĞ|RESİMSAG|RESİM SAĞ|RESİM SAG|RESIMSAG|RESIM SAG|RESÝMSAG)( *)\=( *)([^]]+)( *)\]/mi';
			$subst = '<img src="$4" class="picture-left">';
			$text = preg_replace($re, $subst, $text);

			$re = '/\[\[( *)(RESİMSOL|RESÝMSOL|RESİM SOL|RESÝM SOL|resimsol|RESEMSOL)( *)\=( *)([^]]+)( *)\]\]/mi';
			$subst = '<img src="$4" class="picture-right">';
			$text = preg_replace($re, $subst, $text);

			$re = '/\[\[( *)(RESİMSOL|RESÝMSOL|RESİM SOLA|RESİM SOL|RESÝM SOL)( *)\=( *)([^]]+)( *)\]/mi';
			$subst = '<img src="$4" class="picture-right">';
			$text = preg_replace($re, $subst, $text);


			$text = str_replace('YR=kavunici]]', '', $text);
			$text = str_replace('[[K/]]', '', $text);
			$text = str_replace('[[B]]', '', $text);
			$text = str_replace(' [[SA ]]', '', $text);
			$text = str_replace('[[/YR]', '', $text);
			$text = str_replace('[[/YB', '', $text);
			$text = str_replace('[[YB]]', '', $text);
			$text = str_replace('[[Y', '', $text);
			$text = str_replace('[[/Y', '', $text);
			$text = str_replace('[[IK]]', '', $text);
			$text = str_replace('[[/]]', '', $text);
			$text = str_replace('[[/', '', $text);
			$text = str_replace('/I]]', '', $text);
			$text = str_replace('I]]', '', $text);
			$text = str_replace('K]]', '', $text);
			$text = str_replace('[[/YR]]', '', $text);
			$text = str_replace('[/YR]]', '', $text);
			$text = str_replace('[/YB]]', '', $text);
			$text = str_replace('SO]]', '', $text);
			$text = str_replace('YB]]', '', $text);
			$text = str_replace('YR]]', '', $text);
			$text = str_replace('B]]', '', $text);
			$text = str_replace('[[o]]', '', $text);
			$text = str_replace('[[ı]]', '', $text);
			$text = str_replace('YB=2]]', '', $text);
			$text = str_replace('YB=3]]', '', $text);
			$text = str_replace('YB=2]', '', $text);
			$text = str_replace('YB=3]', '', $text);
			$text = str_replace('=2]]', '', $text);
			$text = str_replace('=2]', '', $text);
			$text = str_replace('=1]]', '', $text);
			$text = str_replace('=1]', '', $text);
			$text = str_replace('B=1]]', '', $text);
			$text = str_replace('RYR=siyah]]', '', $text);
			$text = str_replace('[[SO', '', $text);
			$text = str_replace('[[K', '', $text);
			$text = str_replace('][[', '', $text);
			$text = str_replace('O]]', '', $text);
			$text = str_replace('R]]', '', $text);
			$text = str_replace(']] ]', '', $text);
			$text = str_replace('SA ]]', '', $text);

			$text = str_replace('þ', 'ş', $text);
			$text = str_replace('Þ', 'Ş', $text);

			$text = str_replace('ý', 'ı', $text);
			$text = str_replace('ð', 'ğ', $text);

			$text = str_replace('<br>  <br>  <br>  <br>', '<br>  <br>', $text);
			$text = str_replace('<br>  <br>  <br>', '<br>  <br>', $text);

//	$text = str_replace( '[[/I', '', $text );
//	$text = str_replace( '[[/', '', $text );
//	$text = str_replace( '[[', '', $text );


			$text = trim($text);
			$text = preg_replace('/^(?:<br\s*\/?>\s*)+/i', '', $text);

			$text = preg_replace('/(<br>)+$/i', '', $text);

			$text = preg_replace('~<(\w+)[^>]*>[\p{Z}\p{C}]*</\1>~ui', '', $text);

			$text = str_replace('  ', ' ', $text);
			$text = str_replace('  ', ' ', $text);
			$text = str_replace('  ', ' ', $text);
			$text = str_replace('<3', '❤️', $text);
			$text = str_replace('< Silah ile Söz >', '&lt; Silah ile Söz &gt;', $text);
			$text = str_replace('< Bir Zamana Dönüşebilir Değerler... >', '&lt; Bir Zamana Dönüşebilir Değerler... &gt;', $text);
			$text = str_replace('< Gerisi Unutulur... >', '&lt; Gerisi Unutulur... &gt;', $text);

			return $text;
		}

		public static function returnMarkdown()
		{

			$converter = new HtmlConverter();
			$converter->getConfig()->setOption('strip_tags', true);

			$counter = 0;
			$continue = true;
			while ($continue && $counter < 10000) {
				// Get stories using Laravel's query builder
				$articles = DB::table('articles')
					->where('markdown', 0)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $articles->count() > 0;

				foreach ($articles as $article) {
					$counter++;
					echo "Counter: " . $counter . "\n";

					$main_text = $article->main_text;
					$main_text = html_entity_decode($main_text, ENT_QUOTES);
					$main_text = self::fix_encoding($main_text);
					$main_text = $converter->convert($main_text);

					$subheading = $article->subheading;
					$subheading = html_entity_decode($subheading, ENT_QUOTES);
					$subheading = self::fix_encoding($subheading);
					$subheading = $converter->convert($subheading);

					$title = $article->title;
					$title = html_entity_decode($title, ENT_QUOTES);
					$title = self::fix_encoding($title);
					$title = $converter->convert($title);

					$subtitle = $article->subtitle;
					$subtitle = html_entity_decode($subtitle, ENT_QUOTES);
					$subtitle = self::fix_encoding($subtitle);
					$subtitle = $converter->convert($subtitle);

					$updateQuery = DB::table('articles')
						->where('id', $article->id)
						->update([
							'main_text' => $main_text,
							'subheading' => $subheading,
							'title' => $title,
							'subtitle' => $subtitle,
							'markdown' => 1,
							'updated_at' => Carbon::now(),
							'has_changed' => 1
						]);
				}
			}

			$counter = 0;
			$continue = true;
			while ($continue && $counter < 10000) {

				// Get stories using Laravel's query builder
				$users = DB::table('users')
					->where('markdown', 0)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $users->count() > 0;

				foreach ($users as $user) {
					$counter++;
					echo "Counter: " . $counter . "\n";

					$about_me = $user->about_me;
					$about_me = html_entity_decode($about_me, ENT_QUOTES);
					$about_me = self::fix_encoding($about_me);
					$about_me = $converter->convert($about_me);

					$page_title = $user->page_title;
					$page_title = html_entity_decode($page_title, ENT_QUOTES);
					$page_title = self::fix_encoding($page_title);
					$page_title = $converter->convert($page_title);

					$updateQuery = DB::table('users')
						->where('id', $user->id)
						->update([
							'about_me' => $about_me,
							'page_title' => $page_title,
							'markdown' => 1,
							'updated_at' => Carbon::now()
						]);
				}
			}
		}

		public static function returnKeywords()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 5) {

				// Get stories using Laravel's query builder
				$articles = DB::table('articles')
					->where(function ($query) {
						$query->whereNull('keywords_string')
							->orWhere('keywords_string', '');
					})
					->where('deleted', 0)
					->where('approved', 1)
					//->where('bad_critical', '<', 5)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $articles->count() > 0;

				foreach ($articles as $article) {
					$continue = true;
					$counter++;

					$article_text = $article->title . ' ' . $article->main_text;
					$article_words = explode(' ', $article_text);
					$article_text = implode(' ', array_slice($article_words, 0, 500));

					if (!empty($article->subheading)) {
						$article_text = $article->title . ' ' . $article->subtitle . ' ' . $article->subheading . ' ' . $article_text;
					}

					$llm_result = self::llm_no_tool_call(
						'openai/gpt-4o-mini', //google/gemini-flash-1.5-8b
						'',
						[['role' => 'user', 'content' => "
	take the following Turkish text and output the most meaningful 5 keywords that describe the text then do sentiment analysis on the text. the sentiment analysis should choose one of the following:
Olumlu, Olumsuz, Nötr, Belirsiz, Karışık.

The text is as follows:
" .
							$article_text . "
				
output in Turkish, output JSON as:

```
{
	\"keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
	\"sentiment\": \"olumlu\"
}"]], true);

					if ($llm_result['error']) {
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'bad_critical' => 999,
								'critical_reason' => $llm_result['content'],
								'keywords_string' => 'ERROR ' . $llm_result['content'],
								'has_changed' => 1
							]);
						echo $counter . " - Error: <br>\n";
						var_dump($llm_result);
						echo 'ERROR ON: id:' . $article->id . " - " . $article->title . "<br>\n";
						flush();
					} else {
						$sentiment = $llm_result['sentiment'] ?? '';
						$keywords = implode(', ', $llm_result['keywords'] ?? []);

						// Update using Laravel's query builder
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'keywords_string' => $keywords,
								'sentiment' => $sentiment,
								'has_changed' => 1
							]);

						echo $counter . "- Keywords:<br>\n";
						echo $article->id . " " . $article->title . " -- " . $keywords . " - Sentiment: " . $sentiment . "<br>\n";
						flush();
					}

				}
			}
		}


		public static function updateArticleTable()
		{
			set_time_limit(0);
			do {
				// First, find articles with duplicate slugs
				$records = DB::table('articles')
					->select('articles.*')
					->joinSub(
						DB::table('articles')
							->select('slug')
							->groupBy('slug')
							->havingRaw('COUNT(*) > 1'),
						'dupes',
						'articles.slug',
						'=',
						'dupes.slug'
					)
					->where('has_changed', '=', 1)
					->distinct()
					->limit(100)
					->get();

				$recordsInBatch = $records->count();

				if ($recordsInBatch > 0) {
					foreach ($records as $record) {
						// Generate new slug based on the title
						$baseArticleSlug = Str::slug($record->title);
						$articleSlugCounter = 0;
						$tempArticleSlug = $baseArticleSlug;

						// Find a unique slug
						while (DB::table('articles')
							->where('slug', $tempArticleSlug)
							->where('id', '!=', $record->id)
							->exists()) {
							$articleSlugCounter++;
							$tempArticleSlug = $baseArticleSlug . '_' . $articleSlugCounter;
						}

						// Update the record with the new unique slug
						DB::table('articles')
							->where('id', $record->id)
							->update([
								'slug' => $tempArticleSlug,
								'has_changed' => 0,
								'updated_at' => Carbon::now()
							]);
					}
				}
			} while ($recordsInBatch > 0);

			do {
				$records = DB::table('articles as y')
					->leftJoin('categories as k', 'k.id', '=', 'y.category_id')
					->leftJoin('categories as uk', 'uk.id', '=', 'y.parent_category_id')
					->leftJoin('users as usr', 'usr.id', '=', 'y.user_id')
					->select([
						'y.id',
						'y.slug as article_slug',
						'y.title as article_title',
						'y.keywords_string as keywords_string',
						'k.slug as category_slug',
						'uk.slug as parent_category_slug',
						'k.category_name',
						'uk.category_name as parent_category_name',
						'usr.slug as name_slug',
						'usr.name',
						'y.moderation',
						'y.religious_reason'
					])
					->where('y.has_changed', '=', '1')
					->limit(100)
					->get();

				$recordsInBatch = $records->count();

				if ($recordsInBatch > 0) {
					$counter = 0;

					foreach ($records as $record) {
						// Parse moderation JSON
						$moderationFlagged = 0;
						if (!empty($record->moderation)) {
							$moderationData = json_decode($record->moderation, true);
							if (is_array($moderationData) && !empty($moderationData)) {
								$moderationFlagged = !empty($moderationData[0]['flagged']) ? 1 : 0;
							}
						}

						// Parse religious_reason JSON
						$religiousValue = 0;
						$respectValue = 0;
						if (!empty($record->religious_reason)) {
							$religiousData = json_decode($record->religious_reason, true);
							if (is_array($religiousData)) {
								$religiousValue = isset($religiousData['religious']) ?
									intval($religiousData['religious']) : 0;
								$respectValue = isset($religiousData['respect']) ?
									intval($religiousData['respect']) : 0;
							} else {
								$religiousValue = 666;
								$respectValue = 1;
							}
						}

						if ($record->article_slug === 'n-a') {
							$record->article_slug = Str::slug($record->article_title);
						}

						try {
							DB::table('articles')
								->where('id', $record->id)
								->update([
									'category_slug' => $record->category_slug,
									'category_name' => $record->category_name,
									'parent_category_slug' => $record->parent_category_slug,
									'parent_category_name' => $record->parent_category_name,
									'name_slug' => $record->name_slug,
									'name' => $record->name,
									'slug' => $record->article_slug,
									'moderation_flagged' => $moderationFlagged,
									'religious_moderation_value' => $religiousValue,
									'respect_moderation_value' => $respectValue,
									'has_changed' => 0,
									'updated_at' => Carbon::now()
								]);


							$keywordString = $record->keywords_string;
							$keywordArray = array_map('trim', preg_split('/[,\s]+/', $keywordString));
							$keywordArray = array_filter($keywordArray); // Remove empty values

							$keywordIds = [];
							foreach ($keywordArray as $keywordText) {
								$keywordText = substr($keywordText, 0, 16); // Limit to 16 characters
								if (empty($keywordText)) continue;

								// Find or create keyword
								$keyword = Keyword::firstOrCreate(
									['keyword' => $keywordText],
									['keyword_slug' => Str::slug($keywordText)]
								);

								$keywordIds[] = $keyword->id;
							}

							// Sync keywords with article
							$article = Article::findOrFail($record->id);
							$article->keywords()->sync($keywordIds);

							$counter++;
							if ($counter % 100 == 0) {
								echo "Processed $counter records...";
								flush();
							}
						} catch (\Exception $e) {
							echo "Error updating record ID {$record->id}: " . $e->getMessage() . '<br>';
							flush();
						}
					}

					echo "Update completed. Total records updated: $counter<br>";
					flush();
				} else {
					echo "No records found to update<br>";
					flush();
				}

				echo "Processed batch<br>";
				flush();

			} while ($recordsInBatch > 0);
		}

		//------------------------------------------------------------
		public static function llm_no_tool_call($llm, $system_prompt, $chat_messages, $return_json = true)
		{
			set_time_limit(300);
			session_write_close();

			if ($llm === 'anthropic-haiku') {
				$llm_base_url = env('ANTHROPIC_HAIKU_BASE');
				$llm_api_key = env('ANTHROPIC_KEY');
				$llm_model = env('ANTHROPIC_HAIKU_MODEL');

			} else if ($llm === 'anthropic-sonet') {
				$llm_base_url = env('ANTHROPIC_SONET_BASE');
				$llm_api_key = env('ANTHROPIC_KEY');
				$llm_model = env('ANTHROPIC_SONET_MODEL');

			} else if ($llm === 'open-ai-gpt-4o') {
				$llm_base_url = env('OPEN_AI_GPT4_BASE');
				$llm_api_key = env('OPEN_AI_API_KEY');
				$llm_model = env('OPEN_AI_GPT4_MODEL');

			} else if ($llm === 'open-ai-gpt-4o-mini') {
				$llm_base_url = env('OPEN_AI_GPT4_MINI_BASE');
				$llm_api_key = env('OPEN_AI_API_KEY');
				$llm_model = env('OPEN_AI_GPT4_MINI_MODEL');
			} else {
				$llm_base_url = env('OPEN_ROUTER_BASE');
				$llm_api_key = env('OPEN_ROUTER_KEY');
				$llm_model = $llm;
			}


			if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
			} else {
				$chat_messages = [[
					'role' => 'system',
					'content' => $system_prompt],
					...$chat_messages
				];
			}

			$temperature = 0.8;
			$max_tokens = 8096;

			$data = array(
				'model' => $llm_model,
				'messages' => $chat_messages,
				'temperature' => $temperature,
				'max_tokens' => $max_tokens,
				'top_p' => 1,
				'frequency_penalty' => 0,
				'presence_penalty' => 0,
				'n' => 1,
				'stream' => false
			);

			if ($llm === 'open-ai-gpt-4o' || $llm === 'open-ai-gpt-4o-mini') {
				$data['max_tokens'] = 4096;
				$data['temperature'] = 1;
			} else if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
				$data['max_tokens'] = 8096;
				unset($data['frequency_penalty']);
				unset($data['presence_penalty']);
				unset($data['n']);
				$data['system'] = $system_prompt;
			} else {
				$data['max_tokens'] = 8096;
				if (stripos($llm_model, 'anthropic') !== false) {
					unset($data['frequency_penalty']);
					unset($data['presence_penalty']);
					unset($data['n']);
				} else if (stripos($llm_model, 'openai') !== false) {
					$data['temperature'] = 1;
				} else if (stripos($llm_model, 'google') !== false) {
					$data['stop'] = [];
				} else {
					unset($data['frequency_penalty']);
					unset($data['presence_penalty']);
					unset($data['n']);
				}
			}

			Log::debug('GPT NO TOOL USE: ' . $llm_base_url . ' (' . $llm . ')');
			Log::debug($data);

			$post_json = json_encode($data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $llm_base_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);

			$headers = array();
			if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
				$headers[] = "x-api-key: " . $llm_api_key;
				$headers[] = 'anthropic-version: 2023-06-01';
				$headers[] = 'content-type: application/json';
			} else {
				$headers[] = 'Content-Type: application/json';
				$headers[] = "Authorization: Bearer " . $llm_api_key;
				$headers[] = "HTTP-Referer: https://fictionfusion.io";
				$headers[] = "X-Title: FictionFusion";
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$complete = curl_exec($ch);
			if (curl_errno($ch)) {
				Log::info('CURL Error:');
				Log::info(curl_getinfo($ch));
			}
			curl_close($ch);

//			Log::info('==================Log complete 2 =====================');
			$complete = trim($complete, " \n\r\t\v\0");
//			Log::info($complete);

			$complete_rst = json_decode($complete, true);

			Log::info("GPT NO STREAM RESPONSE:");
			Log::info($complete_rst);
			$prompt_tokens = 0;
			$completion_tokens = 0;

			if ($llm === 'open-ai-gpt-4o' || $llm === 'open-ai-gpt-4o-mini') {
				$content = $complete_rst['choices'][0]['message']['content'];
				$prompt_tokens = $complete_rst['usage']['prompt_tokens'] ?? 0;
				$completion_tokens = $complete_rst['usage']['completion_tokens'] ?? 0;
			} else if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
				$content = $complete_rst['content'][0]['text'];
				$prompt_tokens = $complete_rst['usage']['prompt_tokens'] ?? 0;
				$completion_tokens = $complete_rst['usage']['completion_tokens'] ?? 0;
			} else {
				if (isset($complete_rst['error'])) {
					Log::info('================== ERROR =====================');
					Log::info($complete_rst);
					Log::info($complete_rst['error']['message']);
					return array('error' => true, 'content' => $complete_rst['error']['message'], 'prompt_tokens' => 0, 'completion_tokens' => 0);
				}

				if (isset($complete_rst['choices'][0]['message']['content'])) {
					$content = $complete_rst['choices'][0]['message']['content'];
					$prompt_tokens = $complete_rst['usage']['prompt_tokens'] ?? 0;
					$completion_tokens = $complete_rst['usage']['completion_tokens'] ?? 0;
				} else {
					$content = '';
				}
			}

			if (!$return_json) {
				Log::info('Return is set to NOT JSON. Will return content presuming it is text.');
				return array('error' => false, 'content' => $content, 'prompt_tokens' => $prompt_tokens, 'completion_tokens' => $completion_tokens);
			}

			$content = $content ?? '';
			$content = self::getContentsInBackticksOrOriginal($content);

			//remove all backticks
			$content = str_replace("`", "", $content);

			//check if content is JSON
			$content_json_string = self::extractJsonString($content);
			$content_json_string = self::repaceNewLineWithBRInsideQuotes($content_json_string);

			$validate_result = self::validateJson($content_json_string);

			if ($validate_result !== "Valid JSON") {
				Log::info('================== VALIDATE JSON ON FIRST PASS FAILED =====================');
				Log::info('String that failed:: ---- Error:' . $validate_result);
				Log::info("$content_json_string");

				$content_json_string = (new Fixer)->silent(true)->missingValue('"truncated"')->fix($content_json_string);
				$validate_result = self::validateJson($content_json_string);
			}

			if (strlen($content ?? '') < 20) {
				Log::info('================== CONTENT IS EMPTY =====================');
				Log::info($complete);
				return array('error' => true, 'content' => $content, 'prompt_tokens' => $prompt_tokens, 'completion_tokens' => $completion_tokens);
			}

			//if JSON failed make a second call to get the rest of the JSON
			if ($validate_result !== "Valid JSON") {

				//------ Check if JSON is complete or not with a prompt to continue ------------
				//-----------------------------------------------------------------------------
				$verify_completed_prompt = 'If the JSON is complete output DONE otherwise continue writing the JSON response. Only write the missing part of the JSON response, don\'t repeat the already written story JSON. Continue from exactly where the JSON response left off. Make sure the combined JSON response will be valid JSON.';

				$chat_messages[] = [
					'role' => 'assistant',
					'content' => $content
				];
				$chat_messages[] = [
					'role' => 'user',
					'content' => $verify_completed_prompt
				];

				$data['messages'] = $chat_messages;
				Log::debug('======== SECOND CALL TO FINISH JSON =========');
				Log::debug($data);
				$post_json = json_encode($data);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $llm_base_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$complete2 = curl_exec($ch);
				if (curl_errno($ch)) {
					Log::info('CURL Error:');
					Log::info(curl_getinfo($ch));
				}
				curl_close($ch);

				$complete2 = trim($complete2, " \n\r\t\v\0");

				Log::info("GPT NO STREAM RESPONSE FOR EXTENDED VERSION JSON CHECK:");
				Log::info($complete2);

				$complete2_rst = json_decode($complete2, true);
				$content2 = $complete2_rst['choices'][0]['message']['content'];

				//$content2 = str_replace("\\\"", "\"", $content2);
				$content2 = self::getContentsInBackticksOrOriginal($content2);

				if (!str_contains($content2, 'DONE')) {
					$content = self::mergeStringsWithoutRepetition($content, $content2, 255);
				}

				//------------------------------------------------------------

				$content_json_string = self::extractJsonString($content);
				$content_json_string = self::repaceNewLineWithBRInsideQuotes($content_json_string);

				$validate_result = self::validateJson($content_json_string);

				if ($validate_result !== "Valid JSON") {
					$content_json_string = (new Fixer)->silent(true)->missingValue('"truncated"')->fix($content_json_string);
					$validate_result = self::validateJson($content_json_string);
				}

			} else {
				Log::info("GPT NO STREAM RESPONSE:");
				Log::info($complete_rst);
			}

			if ($validate_result == "Valid JSON") {
				Log::debug('================== VALID JSON =====================');
				$content_rst = json_decode($content_json_string, true);
				Log::debug($content_rst);
				$content_rst['error'] = false;
				return $content_rst;
			} else {
				Log::info('================== INVALID JSON =====================');
				Log::info('JSON error : ' . $validate_result . ' -- ');
				Log::info($content);
				return array('error' => true, 'content' => $content, 'prompt_tokens' => $prompt_tokens, 'completion_tokens' => $completion_tokens);
			}
		}
	}
