<?php

	namespace App\Helpers;

	use App\Models\SentencesTable;
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
	use Intervention\Image\ImageManagerStatic as Image;
	use Ahc\Json\Fixer;

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
			$categories = DB::table('kategoriler')->get();
			foreach ($categories as $category) {
				self::$categoryImages[$category->id] = $category->picture;
			}
		}

		public static function generateInitialsAvatar($pictureFile, $name, $extraCss = 'border-radius: 0px;', $extraClass = 'avatar', $aiClass = 'yz-yazar-resim')
		{
			$hasPicture = false;
			if ($pictureFile !== null && $pictureFile !== "") {
				if (Storage::disk('public')->exists($pictureFile)) {
					$hasPicture = true;
					$html = "<img alt='yazar' style='{$extraCss}' src='/storage/{$pictureFile}' class='{$extraClass}'>";

					if (strpos($pictureFile, 'ai_yazar_resimler') !== false) {
						$html .= "<span class='" . $aiClass . "' data-toggle='tooltip' data-placement='top' 
                             title='Yapay zekaya yazarın bilgilerini vererek üretildi'>YZ</span>";
					}
					return $html;
				}
			}

			// If no picture, generate initials avatar
			$name = trim($name);
			$words = explode(' ', $name);
			$initials = '';

			for ($i = 0; $i < min(2, count($words)); $i++) {
				if (!empty($words[$i])) {
					$initials .= mb_strtoupper(mb_substr($words[$i], 0, 1));
				}
			}

			$hue = rand(0, 360);
			$saturation = rand(35, 80);
			$lightness = rand(35, 65);
			$bgColor = "hsl($hue, $saturation%, $lightness%)";

			$l = $lightness / 100;
			$textColor = ($l > 0.5) ? '#000000' : '#FFFFFF';
			$uniqueId = 'avatar_' . uniqid();

			$css = "
        <style>
            #$uniqueId {
                background-color: $bgColor;
                color: $textColor;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: Arial, sans-serif;
                font-weight: bold;
                font-size: 36px;
                user-select: none;
            }
        </style>
    ";

			// HTML
			$html = "<div id='$uniqueId'>$initials</div>";

			return $css . $html;
		}

		public static function getImage($yaziAnaResim, $categoryId = '', $extraClass = '', $extraStyle = '')
		{
			$yaziAnaResim = str_ireplace('.png', '.jpg', $yaziAnaResim);
			$yaziAnaResim = str_replace('\\', '/', $yaziAnaResim);

			$aiResim = '';
			if (strpos($yaziAnaResim, '_00001_') !== false) {
				$aiResim = '<span class="yz-yazi-resim" data-toggle="tooltip" data-placement="top" 
                        title="Yapay zekaya yazının içeriğini vererek üretildi">YZ</span>';
			}

			if ($yaziAnaResim !== '' && Storage::disk('public')->exists("yazi_resimler/" . $yaziAnaResim)) {
				return "<div style='position:relative;'>
                    <img src='/storage/yazi_resimler/{$yaziAnaResim}' class='{$extraClass}' 
                    style='{$extraStyle}' alt='yazı resim'>{$aiResim}</div>";
			}

			if (key_exists($categoryId, self::$categoryImages)) {
				return "<img src='/storage/catpicbox/" . self::$categoryImages[$categoryId] . "' 
                class='{$extraClass}' style='{$extraStyle}' alt='yazı resim'>";
			} else
			{
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

		public static function slugify($str)
		{
			$text = strtr($str, self::$unwantedArray);
			$text = str_replace(" ", "_", $text);
			$text = strtolower($text);
			$text = preg_replace('~[^\pL\d]+~u', '_', $text);
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
			$text = preg_replace('~[^-\w]+~', '', $text);
			$text = trim($text, '_');
			$text = preg_replace('~-+~', '_', $text);
			$text = strtolower($text);

			return empty($text) ? 'n-a' : $text;
		}

		public static function replaceAscii($input)
		{
			$input = preg_replace("/\r\n|\r|\n/", '<br/>', $input);
			$input = str_replace("", "...", $input);
			$input = str_replace("", "-", $input);
			$input = str_replace("", " ", $input);
			$input = str_replace("", "'", $input);
			$input = str_replace("", " ", $input);
			$input = str_replace("  ", " ", $input);

			$input = preg_replace('/^(\s*<br\s*\/?>\s*)*/', '', $input);
			$input = preg_replace('/(\s*<br\s*\/?>\s*)*$/', '', $input);

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
			if ((Auth::user() && Auth::user()->isAdmin()) ||
				(Auth::user() && !empty(Auth::user()->openrouter_key))) {
				$openrouter_admin_or_key = true;
			}

			$llms_with_rank_path = resource_path('data/llms_with_rank.json');
			$llms_with_rank = json_decode(File::get($llms_with_rank_path), true);

			$llms = json_decode(File::get($llmsJsonPath), true);
			$filtered_llms = array_filter($llms, function ($llm) use ($openrouter_admin_or_key) {
				if (isset($llm['id']) && (stripos($llm['id'], 'openrouter/auto') !== false)) {
					return false;
				}

				if (isset($llm['id']) && (stripos($llm['id'], 'vision') !== false)) {
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

				if (isset($llm['pricing']['completion'])) {
					$price_per_million = floatval($llm['pricing']['completion']) * 1000000;
					if ($openrouter_admin_or_key) {
						return $price_per_million <= 20;
					} else {
						return $price_per_million <= 1.5;
					}
				}

				if (!isset($llm['pricing']['completion'])) {
					return false;
				}

				return true;
			});

			foreach ($filtered_llms as &$filtered_llm) {
				$found_rank = false;
				foreach ($llms_with_rank as $llm_with_rank) {
					if ($filtered_llm['id'] === $llm_with_rank['id']) {
						$filtered_llm['score'] = $llm_with_rank['score'] ?? 0;
						$filtered_llm['ugi'] = $llm_with_rank['ugi'] ?? 0;
						$found_rank = true;
					}
				}
				if (!$found_rank) {
					$filtered_llm['score'] = 0;
					$filtered_llm['ugi'] = 0;
				}
			}

			// Sort $filtered_llms by score, then alphabetically for score 0
			usort($filtered_llms, function ($a, $b) {
				// First, compare by score in descending order
				$scoreComparison = $b['score'] <=> $a['score'];

				// If scores are different, return this comparison
				if ($scoreComparison !== 0) {
					return $scoreComparison;
				}

				// If scores are the same (particularly both 0), sort alphabetically by name
				return strcmp($a['name'], $b['name']);
			});

			//for each llm with score 0 sort them alphabetically
			return array_values($filtered_llms);
		}

		public static function moderation($message)
		{

			$openai_api_key = self::getOpenAIKey();
			//make sure $message can be json encoded
			if (!self::isValidUtf8($message)) {
				$message = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $message);
			}


			$response = Http::withHeaders([
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $openai_api_key,
			])->post(env('OPEN_AI_API_BASE_MODERATION'), [
				'input' => $message,
			]);

			return $response->json();
		}

		public static function validateJson($str)
		{
			Log::info('Starting JSON validation.');

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

		public static function getAnthropicKey()
		{
			$user = Auth::user();
			return !empty($user->anthropic_key) ? $user->anthropic_key : $_ENV['ANTHROPIC_KEY'];
		}

		public static function getOpenAIKey()
		{
			$user = Auth::user();
			return !empty($user->openai_api_key) ? $user->openai_api_key : $_ENV['OPEN_AI_API_KEY'];
		}

		public static function getOpenRouterKey()
		{
			$user = Auth::user();
			return !empty($user->openrouter_key) ? $user->openrouter_key : $_ENV['OPEN_ROUTER_KEY'];
		}

		//------------------------------------------------------------

		public static function returnIsHarmful()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 100) {
				// Get stories using Laravel's query builder
				$stories = DB::table('yazilar')
					->where('bad_critical', -1)
					->where('silindi', 0)
					->where('onay', 1)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $stories->count() > 0;

				foreach ($stories as $story) {
					$counter++;

					$llm_result = self::llm_no_tool_call(
						'openai/gpt-4o-mini',
						'',
						[[
							'role' => 'user',
							'content' => "take the following Turkish, and analyze it for being harmful to a subset of people, such as ethnic, religious, sexual orientation, or other minority groups. Rate it from 0 to 5, where 0 is not harmful at all, and 5 is extremely harmful. If harmful provide a brief explanation of why you rated it as such. Return reason in English. If not harmful, leave the explanation empty. The text is as follows: Title: " . $story->baslik . " Subtitle: " . $story->alt_baslik . " Category: " . $story->ust_kategori_ad . " Subcategory: " . $story->kategori_ad . " text: " . $story->yazi . " output in Turkish, output JSON as: ``` { \"harmful\": \"0-5\" \"explanation\": \"\" } ```"
						]],
						true
					);

					if (is_array($llm_result)) {
						$harmful = $llm_result['harmful'] ?? '0';
						$explanation = $llm_result['explanation'] ?? '';

						// Update using Laravel's query builder
						DB::table('yazilar')
							->where('id', $story->id)
							->update([
								'bad_critical' => $harmful,
								'critical_reason' => $explanation
							]);

						echo $counter . " - Moderation: - \n";
						if ($harmful > 0 || $explanation !== '') {
							echo $story->id . " " . $story->baslik . ", kategori " . $story->ust_kategori_ad . "/" . $story->kategori_ad . "<br>\n";
							echo "Harmful: " . $harmful . "<br>\n";
							echo "Explanation: " . $explanation . "<br>\n";
						} else {
							echo "id: " . $story->id . " - Not harmful.<br>\n";
						}
						flush();
						ob_flush();
					} else {
						echo $counter . " - Error: <br>\n";
						echo $story->id . " " . $story->baslik . ", kategori " . $story->ust_kategori_ad . "/" . $story->kategori_ad . "<br>\n";
						echo var_dump($llm_result);
						flush();
						ob_flush();
					}
				}
			}
		}

		public static function returnReligiousReason()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 100) {
				// Get stories using Laravel's query builder
				$stories = DB::table('yazilar')
					->where('has_religious_moderation', 0)
					->where('silindi', 0)
					->where('onay', 1)
					->where('bad_critical', '<', 4)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $stories->count() > 0;

				foreach ($stories as $story) {
					$continue = true;
					$counter++;

					// Get first 1000 words
					$yazi = $story->yazi;
					$yazi_words = explode(' ', $yazi);
					$yazi = implode(' ', array_slice($yazi_words, 0, 1000));

					$llm_result = self::llm_no_tool_call(
						'google/gemini-flash-1.5-8b',
						'',
						[[
							'role' => 'user',
							'content' => "take the following Turkish text and analyze it if it is religious and also respectful towards other religions and beliefs. The text is as follows: Title: " . $story->baslik . " Subtitle: " . $story->alt_baslik . " Category: " . $story->ust_kategori_ad . " Subcategory: " . $story->kategori_ad . " text: " . $yazi . " output JSON as: ``` { \"religious_reason\": { \"religious\": 0..5, \"respect\": 0..5, \"reason\": \"reasoning for the moderation\" } } ```"
						]],
						true
					);

					if (is_array($llm_result)) {
						$moderation = $llm_result['religious_reason'] ?? '';
						$moderation = json_encode($moderation);

						// Update using Laravel's query builder
						DB::table('yazilar')
							->where('id', $story->id)
							->update([
								'religious_reason' => $moderation,
								'has_religious_moderation' => 1
							]);

						echo $counter . "- Religious Moderation:<br>\n";
						echo $story->id . " " . $story->baslik . " -- " . $story->ust_kategori_ad . '/' . $story->kategori_ad . "<br>\n";
						echo $moderation . "<br>\n";
						flush();
						ob_flush();
					} else {
						echo $counter . " - Error: <br>\n";
						echo var_dump($llm_result);
						flush();
						ob_flush();
					}
				}
			}
		}

		public static function returnModeration()
		{
			$offset = request()->get('offset', 0);
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 100) {
				echo "<hr>Offset: " . $offset . "<br>\n";

				// Get stories using Laravel's query builder
				$stories = DB::table('yazilar')
					->where('has_moderation', -1)
					->where('onay', 1)
					->where('silindi', 0)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();

				$continue = $stories->count() > 0;

				foreach ($stories as $story) {
					$continue = true;
					$counter++;

					$moderation = self::moderation($story->baslik . ' ' . $story->yazi);
					$moderation = json_encode($moderation['results']);
					echo $counter . "- Moderation:<br>\n";
					echo $moderation . "<br>\n";
					flush();
					ob_flush();

					DB::table('yazilar')
						->where('id', $story->id)
						->update([
							'moderation' => $moderation,
							'has_moderation' => 1
						]);
				}
			}
		}

		public static function returnKeywords()
		{
			$counter = 0;
			$continue = true;

			while ($continue && $counter < 100) {

				// Get stories using Laravel's query builder
				$stories = DB::table('yazilar')
					->whereNull('keywords')
					->where('silindi', 0)
					->where('onay', 1)
					->where('bad_critical', '<', 4)
					->orderBy('id', 'DESC')
					->limit(10)
					->get();

				$continue = $stories->count() > 0;

				foreach ($stories as $story) {
					$continue = true;
					$counter++;

					$yazi = $story->baslik . ' ' . $story->yazi;
					$yazi_words = explode(' ', $yazi);
					$yazi = implode(' ', array_slice($yazi_words, 0, 500));

					if (!empty($story->tanitim)) {
						$yazi = $story->baslik . ' ' . $story->alt_baslik . ' ' . $story->tanitim . ' ' . $yazi;
					}

					$llm_result = self::llm_no_tool_call(
						'google/gemini-flash-1.5-8b',
						'',
						[['role' => 'user', 'content' => "
	take the following Turkish text and output the most meaningful 5 keywords that describe the text then do sentiment analysis on the text. the sentiment analysis should choose one of the following:
Olumlu, Olumsuz, Nötr, Belirsiz, Karışık.

The text is as follows:
" .
							$yazi . "
				
output in Turkish, output JSON as:

```
{
	\"keywords\": [\"keyword1\", \"keyword2\", \"keyword3\"],
	\"sentiment\": \"olumlu\"
}"]], true);

					if (is_array($llm_result)) {
						$sentiment = $llm_result['sentiment'] ?? '';
						$keywords = implode(', ', $llm_result['keywords'] ?? []);

						// Update using Laravel's query builder
						DB::table('yazilar')
							->where('id', $story->id)
							->update([
								'keywords' => $keywords,
								'sentiment' => $sentiment
							]);

						echo $counter."- Keywords:<br>\n";
						echo $story->id . " " . $story->baslik . " -- " . $keywords . " - Sentiment: " . $sentiment . "<br>\n";
						flush();
						ob_flush();
					} else {
						echo $counter . " - Error: <br>\n";
						echo var_dump($llm_result);
						echo 'ERROR ON: id:'.$story->id . " - " . $story->baslik . "<br>\n";
						flush();
						ob_flush();
					}
				}
			}
		}

		public static function updateYaziTable() {
			$batchSize = 1000;

			do {
				$records = DB::table('yazilar as y')
					->leftJoin('kategoriler as k', 'k.id', '=', 'y.kategori_id')
					->leftJoin('kategoriler as uk', 'uk.id', '=', 'y.ust_kategori_id')
					->leftJoin('yazar as yz', 'yz.id', '=', 'y.yazar_id')
					->select([
						'y.id',
						'k.slug as kategori_slug',
						'uk.slug as ust_kategori_slug',
						'k.kategori_ad',
						'uk.kategori_ad as ust_kategori_ad',
						'yz.slug as yazar_slug',
						'yz.yazar_ad',
						'y.moderation',
						'y.religious_reason'
					])
					->where('y.has_changed','=', '1')
					->take($batchSize)
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
							}
						}

						try {
							DB::table('yazilar')
								->where('id', $record->id)
								->update([
									'kategori_slug' => $record->kategori_slug,
									'kategori_ad' => $record->kategori_ad,
									'ust_kategori_slug' => $record->ust_kategori_slug,
									'ust_kategori_ad' => $record->ust_kategori_ad,
									'yazar_slug' => $record->yazar_slug,
									'yazar_ad' => $record->yazar_ad,
									'moderation_flagged' => $moderationFlagged,
									'religious_moderation_value' => $religiousValue,
									'respect_moderation_value' => $respectValue,
									'has_changed' => 0,
									'updated_at' => Carbon::now()
								]);

							$counter++;
							if ($counter % 100 == 0) {
								echo "Processed $counter records...";
								flush();
								ob_flush();							}
						} catch (\Exception $e) {
							echo "Error updating record ID {$record->id}: " . $e->getMessage() . '<br>';
							flush();
							ob_flush();
						}
					}

					echo "Update completed. Total records updated: $counter<br>";
					flush();
					ob_flush();
				} else {
					echo "No records found to update<br>";
					flush();
					ob_flush();
				}

				echo "Processed batch<br>";
				flush();
				ob_flush();

			} while ($recordsInBatch > 0);
		}

		//------------------------------------------------------------
		public static function function_call($llm, $example_question, $example_answer, $prompt, $schema, $language = 'english')
		{
			set_time_limit(300);
			session_write_close();

			if ($llm === 'anthropic-haiku') {
				$llm_base_url = $_ENV['ANTHROPIC_HAIKU_BASE'];
				$llm_api_key = getAnthropicKey();
				$llm_model = $_ENV['ANTHROPIC_HAIKU_MODEL'];

			} else if ($llm === 'anthropic-sonet') {
				$llm_base_url = $_ENV['ANTHROPIC_SONET_BASE'];
				$llm_api_key = getAnthropicKey();
				$llm_model = $_ENV['ANTHROPIC_SONET_MODEL'];

			} else if ($llm === 'open-ai-gpt-4o') {
				$llm_base_url = $_ENV['OPEN_AI_GPT4_BASE'];
				$llm_api_key = self::getOpenAIKey();
				$llm_model = $_ENV['OPEN_AI_GPT4_MODEL'];

			} else if ($llm === 'open-ai-gpt-4o-mini') {
				$llm_base_url = $_ENV['OPEN_AI_GPT4_MINI_BASE'];
				$llm_api_key = self::getOpenAIKey();
				$llm_model = $_ENV['OPEN_AI_GPT4_MINI_MODEL'];
			} else {
				$llm_base_url = $_ENV['OPEN_ROUTER_BASE'];
				$llm_api_key = self::getOpenRouterKey();
				$llm_model = $llm;
			}


			$chat_messages = [];
			if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {

				$chat_messages[] = [
					'role' => 'user',
					'content' => $prompt
				];
			} else {
//				$chat_messages[] = [
//					'role' => 'system',
//					'content' => 'You are an expert author advisor.'
//				];
				$chat_messages[] = [
					'role' => 'user',
					'content' => $prompt
				];
			}


			$temperature = rand(80, 100) / 100;
			$max_tokens = 4000;

			$tool_name = 'auto';
//			if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
//				$tool_name = $schema['function']['name'];
//			}

			$data = array(
				'model' => $llm_model,
				'messages' => $chat_messages,
				'tools' => [$schema],
				'tool_choice' => $tool_name,
				'temperature' => $temperature,
				'max_tokens' => $max_tokens,
				'top_p' => 1,
				'frequency_penalty' => 0,
				'presence_penalty' => 0,
				'n' => 1,
				'stream' => false
			);

			if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
				//remove tool_choice
				unset($data['tool_choice']);
				unset($data['frequency_penalty']);
				unset($data['presence_penalty']);
				unset($data['n']);
			}

			Log::info('================== FUNCTION CALL DATA =====================');
			Log::info($data);

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
			}

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$complete = curl_exec($ch);
			if (curl_errno($ch)) {
				Log::info('CURL Error:');
				Log::info(curl_getinfo($ch));
			}
			curl_close($ch);
//			session_start();

			Log::info('==================Log complete 1 =====================');
			$complete = trim($complete, " \n\r\t\v\0");
			Log::info($complete);

			$validateJson = self::validateJson($complete);
			if ($validateJson == "Valid JSON") {
				Log::info('==================Log JSON complete=====================');
				$complete_rst = json_decode($complete, true);
				Log::info($complete_rst);
				$arguments_rst = [];

				if ($llm === 'anthropic-haiku' || $llm === 'anthropic-sonet') {
					$contents = $complete_rst['content'];
					foreach ($contents as $content) {
						if ($content['type'] === 'tool_use') {
							$arguments_rst = $content['input'];
						}
					}
				} else {
					$content = $complete_rst['choices'][0]['message']['tool_calls'][0]['function'];
					$arguments = $content['arguments'];
					$validateJson = self::validateJson($arguments);
					if ($validateJson == "Valid JSON") {
						Log::info('==================Log JSON arguments=====================');
						$arguments_rst = json_decode($arguments, true);
						Log::info($arguments_rst);
					}
				}


				return $arguments_rst;
			} else {
				Log::info('==================Log JSON error=====================');
				Log::info($validateJson);
			}
		}

		public static function llm_no_tool_call($llm, $system_prompt, $chat_messages, $return_json = true)
		{
			set_time_limit(300);
			session_write_close();

			if ($llm === 'anthropic-haiku') {
				$llm_base_url = $_ENV['ANTHROPIC_HAIKU_BASE'];
				$llm_api_key = elf::getAnthropicKey();;
				$llm_model = $_ENV['ANTHROPIC_HAIKU_MODEL'];

			} else if ($llm === 'anthropic-sonet') {
				$llm_base_url = $_ENV['ANTHROPIC_SONET_BASE'];
				$llm_api_key = elf::getAnthropicKey();;
				$llm_model = $_ENV['ANTHROPIC_SONET_MODEL'];

			} else if ($llm === 'open-ai-gpt-4o') {
				$llm_base_url = $_ENV['OPEN_AI_GPT4_BASE'];
				$llm_api_key = self::getOpenAIKey();
				$llm_model = $_ENV['OPEN_AI_GPT4_MODEL'];

			} else if ($llm === 'open-ai-gpt-4o-mini') {
				$llm_base_url = $_ENV['OPEN_AI_GPT4_MINI_BASE'];
				$llm_api_key = self::getOpenAIKey();
				$llm_model = $_ENV['OPEN_AI_GPT4_MINI_MODEL'];
			} else {
				$llm_base_url = $_ENV['OPEN_ROUTER_BASE'];
				$llm_api_key = self::getOpenRouterKey();
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

			Log::info('GPT NO TOOL USE: ' . $llm_base_url . ' (' . $llm . ')');
			Log::info($data);

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
					return json_decode($complete_rst['error']['message'] ?? '{}');
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
				Log::info('Return is NOT JSON. Will return content presuming it is text.');
				return array('content' => $content, 'prompt_tokens' => $prompt_tokens, 'completion_tokens' => $completion_tokens);
			}

//			$content = str_replace("\\\"", "\"", $content);
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
				return '';
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
				Log::info('======== SECOND CALL TO FINISH JSON =========');
				Log::info($data);
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
				Log::info('================== VALID JSON =====================');
				$content_rst = json_decode($content_json_string, true);
				Log::info($content_rst);
				return $content_rst;
			} else {
				Log::info('================== INVALID JSON =====================');
				Log::info('JSON error : ' . $validate_result . ' -- ');
				Log::info($content);
			}
		}

		//-------------------------------------------------------------------------

	}
