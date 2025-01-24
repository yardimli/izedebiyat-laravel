<?php

	set_time_limit(0);

	$system_prompt = '
no: Fra himmelen ser Barrøy ut som et fotspor i havet
tr: Barrøy parmakları batıya dönük bir ayak izini andırır gökyüzünden bakıldığında

no: Nå faller snøen tung over øya og gjør den hvit og rund
tr: Şimdi bütün gün süren yoğun kar adayı bembeyaz bir örtüyle kaplamış

no: – Frys du\'kkje?
tr: "Üşümüyor musun?"

no: – Kem e du?
tr: "Kimsin sen?"

no: Det er bare ingen som har sett Barrøy fra himmelen før
tr: Hiç kimse Barrøy\'ü gökyüzünden görmemiştir

no: Barbro vet hva hun gjør
tr: Barbro ne yaptığını biliyor

no: Hun åpner øynene og skjønner at hun ligger på rygg
tr: Barbro gözlerini açtığında sırtüstü yattığını anlıyor

no: Til sist går Barbro
tr: En arkalarında yürüyen Barbro

no: Det er Ingrid
tr: Bunu soran Ingrid\'ydi

no: Hun setter seg
tr: Barbro oturuyor

no: Det skal skjæres torv og det gamle huset skal males så det ikke trenger å skamme seg ved siden av det nye.
tr: Tezek kesilecek, eski ev boyanacak ki yenisinin yanında utanmasına gerek kalmasın.

no: Salthammer har kanon på baugen og hvit tønne med svart magebelte i masta.
tr: Salthammer\'in güvertesinde zıpkın fırlatıcısı, yelken direğinin oturduğu siyah kemerli beyaz fıçısı var.

no: Ingrid kan høre hammerslag og se Lars og Felix gjøre klar til den første fangsten.
tr: Ingrid çekiç seslerini duyabiliyor ve Lars\'la Felix\'in ilk ava hazırlandıklarını görebiliyor.

no: Ingrid vikler henne ut og lar henne kravle omkring i lyngen til hun blir lei.
tr: Ingrid onu yere indiriyor, yorulana kadar otların arasında emeklemeye bırakıyor.

no: Lars ler og løfter opp treåringen Oskar så også han kan kikke ned på Ingrid.
tr: Lars gülüyor, Ingrid\'yi görebilsin diye üç yaşındaki Oskar\'ı kucağına alıyor.

no: Barbro har spørsmål om hvor hun har tenkt seg, hvorfor, og for hvor lenge?
tr: Barbro nereye gitmeyi düşündüğünü, niye gideceğini, ne kadar süreceğini soruyor.

no: Ingrid pakker den vesle kofferten som er med henne hver gang hun forsøker å forlate øya.
tr: Ingrid ne zaman adadan ayrılmayı denese yanında taşıdığı küçük bavulu dolduruyor.

no: Dogger ska\' berre mal\', sier Ingrid.
tr: "Siz boyayın," diyor Ingrid.

no: Ja væl, sier Barbro. – Og nær kjæm du?
tr: "Peki," diyor Barbro. "Ne zaman dönüyorsun?"

no: Lars spør hva hun skal med han Adolf.
tr: Lars Adolf\'la ne işi olduğunu soruyor.

no: Hun drakk vann av hver bekk hun krysset, og de spiste to ganger før de kom ned til et nytt hav og sov i en rorbu som en venn av Adolf åpnet for dem, i to netter.
tr: Geçtiği her derecikten su içiyordu, yeni bir denize inene kadar iki kere yemek yediler, kıyıya ulaştıklarında Adolf\'un bir arkadaşının onlara açtığı bir kayıkhanede iki gece geçirdiler.

no: Rimala sa at det var ikke første gang han tok flyktninger ombord, han hadde vært med på å evakuere et helt folk fra Finnmark, hans eget folk.
tr: Rimala tekneye ilk kez mülteci almadığını, Finnmark\'taki bir yığın insanın, hem de kendi insanlarının tahliyesine de katıldığını söyledi.

no: Ved midnattstider hørte hun skipper og matros krangle høylytt ute på dekk, det regulære munnhuggeri.
tr: Gece yarısına yakın kaptanla yardımcısının güvertede bağıra çağıra kavga ettiklerini duydu.

no: Ja, det er planen, tenkte Ingrid, som var underveis og sto på dekk og så seg rundt.
tr: Evet, plan bu, diye düşünen Ingrid güverteye çıkıp durdu, çevresine baktı.

no: Men han var langt fra så skråsikker ved synet av Kajas øyne som Mathea og Adolf hadde vært.
tr: Ama Kaja\'nın gözlerini gördüğünde bile Mathea ve Adolf gibi emin olmaktan çok uzaktı.

no: Markus ble revet ut av en dramatik og fyrverkerilignende søvn og kjente et solstreif mot netthinnene
tr: Markus dramatik, havai fişekleri andıran bir uykudan uyandırıldığında retinasına çarpan güneş ışığını hissetti

no: det var sommer over landet, en mild og kjærtegnende damp, fredens nåde
tr: yaz mevsimiydi, yumuşak, okşayıcı bir nem ve barışın bağışlayıcılığı yayılmıştı toprağın üzerine

no: han hadde en forvirret, ubesluttsom, upålitelig og velmenende kraft på sin høyre skulder
tr: onun sağ omzunda kafası karışık, kararsız, güvenilmez, iyi niyetli bir güç vardı

no: en due med falkeblikk, en valmue med jernvinger, en medalje av snø
tr: kartal bakışlı bir güvercin, demir kanatlı bir haşhaş, karlardan bir madalya

no: I det fjerne lød granatnedslag fra en russisk streifer
tr: Uzaklardan patlayan bir el bombalarının sesi geliyordu

no: – Hva er klokka? sa Markus og spratt opp
tr: Hızla doğrulan Markus "Saat kaç?" diye sordu

no: – Er det skjedd noe?
tr: "Bir şey mi oldu?"

no: som spinner og spinner gjennom tre årstider
tr: üç mevsim boyunca durmaksızın döndürerek

no: naturligvis, en russisk streifer som dropper lasten
tr: elbette Volga ovasındaki tabyaların arkasına dönmeden önce yükünü kendince uygun bulduğu bir yere boşaltan

no: – Åtte, sa Beber forundret, våknet han også
tr: "Sekiz," dedi Beber şaşkın şaşkın, o da uyanmıştı

Translate the following text to Turkish. Use the translation from Norwegian to Turkish examples given above as guidelines.';


	function echo_log($log, $log_to_file = true)
	{
		if ($log_to_file) {
			$log_file = fopen("log.txt", "a");
			if (is_array($log)) {
				fwrite($log_file, print_r($log, true) . "\n");
			} else {
				fwrite($log_file, $log . "\n");
			}
			fclose($log_file);
		} else {
			//cehck if log is an array
			if (is_array($log)) {
				echo "<pre>";
				print_r($log);
				echo "</pre>";
				return;
			} else {
				echo $log . "<br>\n";
			}
			flush();
			ob_flush();
		}
	}

	function validateJson($str)
	{
		echo_log('Starting JSON validation.');

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

	function repaceNewLineWithBRInsideQuotes($input)
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

	function getContentsInBackticksOrOriginal($input)
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

	function extractJsonString($input)
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

	function mergeStringsWithoutRepetition($string1, $string2, $maxRepetitionLength = 100)
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

	function llm_no_tool_call($llm, $system_prompt, $chat_messages, $return_json = true)
	{
		set_time_limit(300);
		session_write_close();

		$llm_base_url = 'https://openrouter.ai/api/v1/chat/completions';
		$llm_api_key = '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';
		$llm_model = $llm;

		$chat_messages = [[
			'role' => 'system',
			'content' => $system_prompt],
			...$chat_messages
		];

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


		$post_json = json_encode($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $llm_base_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);

		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = "Authorization: Bearer " . $llm_api_key;
		$headers[] = "HTTP-Referer: https://writebookswithai.com";
		$headers[] = "X-Title: WriteBooksWithAI";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$complete = curl_exec($ch);
		if (curl_errno($ch)) {
			echo_log('CURL Error:');
			echo_log(curl_getinfo($ch));
		}
		curl_close($ch);

		$complete = trim($complete, " \n\r\t\v\0");

		$complete_rst = json_decode($complete, true);

		echo_log("GPT NO STREAM RESPONSE:");
		echo_log($complete_rst);
		$prompt_tokens = 0;
		$completion_tokens = 0;

		if (isset($complete_rst['error'])) {
			echo_log('================== ERROR =====================');
			echo_log($complete_rst);
			echo_log($complete_rst['error']['message']);
			return json_decode($complete_rst['error']['message'] ?? '{}');
		}
		if (isset($complete_rst['choices'][0]['message']['content'])) {
			$content = $complete_rst['choices'][0]['message']['content'];
			$prompt_tokens = $complete_rst['usage']['prompt_tokens'] ?? 0;
			$completion_tokens = $complete_rst['usage']['completion_tokens'] ?? 0;
		} else {
			$content = '';
		}

		if (!$return_json) {
			echo_log('Return is NOT JSON. Will return content presuming it is text.');
			return array('content' => $content, 'prompt_tokens' => $prompt_tokens, 'completion_tokens' => $completion_tokens);
		}

		//			$content = str_replace("\\\"", "\"", $content);
		$content = $content ?? '';
		$content = getContentsInBackticksOrOriginal($content);

		//remove all backticks
		$content = str_replace("`", "", $content);

		//check if content is JSON
		$content_json_string = extractJsonString($content);
		$content_json_string = repaceNewLineWithBRInsideQuotes($content_json_string);

		$validate_result = validateJson($content_json_string);

		if ($validate_result !== "Valid JSON") {
			echo_log('================== VALIDATE JSON ON FIRST PASS FAILED =====================');
			echo_log('String that failed:: ---- Error:' . $validate_result);
			echo_log("$content_json_string");

			//$content_json_string = (new Fixer)->silent(true)->missingValue('"truncated"')->fix($content_json_string);
			$validate_result = validateJson($content_json_string);
		}

		if (strlen($content ?? '') < 20) {
			echo_log('================== CONTENT IS EMPTY =====================');
			echo_log($complete);
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
			echo_log('======== SECOND CALL TO FINISH JSON =========');
			echo_log($data);
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
				echo_log('CURL Error:');
				echo_log(curl_getinfo($ch));
			}
			curl_close($ch);

			$complete2 = trim($complete2, " \n\r\t\v\0");

			echo_log("GPT NO STREAM RESPONSE FOR EXTENDED VERSION JSON CHECK:");
			echo_log($complete2);

			$complete2_rst = json_decode($complete2, true);
			$content2 = $complete2_rst['choices'][0]['message']['content'];

			//$content2 = str_replace("\\\"", "\"", $content2);
			$content2 = getContentsInBackticksOrOriginal($content2);

			if (!str_contains($content2, 'DONE')) {
				$content = mergeStringsWithoutRepetition($content, $content2, 255);
			}

			//------------------------------------------------------------

			$content_json_string = extractJsonString($content);
			$content_json_string = repaceNewLineWithBRInsideQuotes($content_json_string);

			$validate_result = validateJson($content_json_string);

//			if ($validate_result !== "Valid JSON") {
//				$content_json_string = (new Fixer)->silent(true)->missingValue('"truncated"')->fix($content_json_string);
//				$validate_result = validateJson($content_json_string);
//			}

		} else {
			echo_log("GPT NO STREAM RESPONSE:");
			echo_log($complete_rst);
		}

		if ($validate_result == "Valid JSON") {
			echo_log('================== VALID JSON =====================');
			$content_rst = json_decode($content_json_string, true);
			echo_log($content_rst);
			return $content_rst;
		} else {
			echo_log('================== INVALID JSON =====================');
			echo_log('JSON error : ' . $validate_result . ' -- ');
			echo_log($content);
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
			echo_log('======== SECOND CALL TO FINISH JSON =========');
			echo_log($data);
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
				echo_log('CURL Error:');
				echo_log(curl_getinfo($ch));
			}
			curl_close($ch);

			$complete2 = trim($complete2, " \n\r\t\v\0");

			echo_log("GPT NO STREAM RESPONSE FOR EXTENDED VERSION JSON CHECK:");
			echo_log($complete2);

			$complete2_rst = json_decode($complete2, true);
			$content2 = $complete2_rst['choices'][0]['message']['content'];

			//$content2 = str_replace("\\\"", "\"", $content2);
			$content2 = getContentsInBackticksOrOriginal($content2);

			if (!str_contains($content2, 'DONE')) {
				$content = mergeStringsWithoutRepetition($content, $content2, 255);
			}

			//------------------------------------------------------------

			$content_json_string = extractJsonString($content);
			$content_json_string = repaceNewLineWithBRInsideQuotes($content_json_string);

			$validate_result = validateJson($content_json_string);

//			if ($validate_result !== "Valid JSON") {
//				$content_json_string = (new Fixer)->silent(true)->missingValue('"truncated"')->fix($content_json_string);
//				$validate_result = validateJson($content_json_string);
//			}

		} else {
			echo_log("GPT NO STREAM RESPONSE:");
			echo_log($complete_rst);
		}

		if ($validate_result == "Valid JSON") {
			echo_log('================== VALID JSON =====================');
			$content_rst = json_decode($content_json_string, true);
			echo_log($content_rst);
			return $content_rst;
		} else {
			echo_log('================== INVALID JSON =====================');
			echo_log('JSON error : ' . $validate_result . ' -- ');
			echo_log($content);
		}
	}

	//----------------------------------------------

	define('izedebiyat_db_username', 'root');

	define('izedebiyat_db_password', 'A123456b!');
	define('izedebiyat_db_hostname', 'localhost');

	define('izedebiyat_db_database', 'izedebiyat');


	$dbconn = new mysqli(izedebiyat_db_hostname, izedebiyat_db_username, izedebiyat_db_password, izedebiyat_db_database);
	if ($dbconn->connect_errno) {
		die('Connect Error: ' . $dbconn->connect_errno);
	}

	$result = $dbconn->query("SET NAMES utf8");


	//----------------------------------------------


	$output_translted_book = false;
	if ($output_translted_book) {
		//loop through the database and output the translation in order
		$textQ = "SELECT * FROM ceviri WHERE book_id=2 ORDER BY section_number";
		$statement = $dbconn->prepare($textQ);
		$statement->execute();
		$text_result = $statement->get_result();

		echo '<table border="1">';
		while ($text_row = $text_result->fetch_assoc()) {
			$original = str_replace("\n", "<br>", $text_row['original']) . "\n<br>";
			$translation = str_replace("\n", "<br>", $text_row['translation']) . "\n<br>";
			echo '<tr>';
			echo '<td>' . $translation . '</td>';
			echo '<td>' . $original . '</td>';
			echo '</tr>';
		}
		echo '</table>';

		$statement->close();

		exit();
	}

	// Read the text file
	$text = file_get_contents('grenser-no.txt');

	// Split the text into lines
	$lines = explode("\n", $text);

	// First pass: handle hyphenation
	$processed_lines = [];
	$current_line = '';

	foreach ($lines as $line) {
		$line = trim($line);

		// Check if current line is just a number or has empty lines around it
		$is_isolated_line = false;
		$current_index = array_search($line, $lines);
		if ($current_index > 0 && $current_index < count($lines) - 1) {
			$prev_line = trim($lines[$current_index - 1]);
			$next_line = trim($lines[$current_index + 1]);
			if (empty($prev_line) && empty($next_line)) {
				$is_isolated_line = true;
			}
		}

		// Check if line is just a number
		$is_number = preg_match('/^\d+$/', $line);

		if ($is_number || $is_isolated_line) {
			if ($current_line !== '') {
				$processed_lines[] = $current_line;
				$current_line = '';
			}
			if ($is_isolated_line) {
				$processed_lines[] = '';
			}
			$processed_lines[] = $line;
			if ($is_isolated_line) {
				$processed_lines[] = '';
			}

			continue;
		}

		if (empty($line)) {
			if ($current_line !== '') {
				$processed_lines[] = $current_line;
				$current_line = '';
			}
			continue;
		}

		// Check if previous line ended with a hyphen
		if ($current_line !== '' && preg_match('/([a-zæøåA-ZÆØÅ])-$/', $current_line, $matches)) {
			// Remove the hyphen and concatenate with next line
			$current_line = substr($current_line, 0, -1) . $line;
		} else {
			if ($current_line !== '') {
				$processed_lines[] = $current_line;
			}
			$current_line = $line;
		}
	}
	if ($current_line !== '') {
		$processed_lines[] = $current_line;
	}

	//	echo '<pre>';
	//	var_dump($processed_lines);
	//	exit();

	// Combine lines into paragraphs based on periods
	$paragraphs = [];
	$current_paragraph = '';

	foreach ($processed_lines as $line) {
		// If line is just a number or was isolated, treat as separate paragraph
		if (preg_match('/^\d+$/', $line) ||
			(array_search($line, $processed_lines) > 0 &&
				array_search($line, $processed_lines) < count($processed_lines) - 1 &&
				empty(trim($processed_lines[array_search($line, $processed_lines) - 1])) &&
				empty(trim($processed_lines[array_search($line, $processed_lines) + 1])))) {

			// Save any existing paragraph first
			if ($current_paragraph !== '') {
				$paragraphs[] = trim($current_paragraph);
				$current_paragraph = '';
			}
			// Add the isolated line as its own paragraph
			$paragraphs[] = $line;
			continue;
		}

		// For regular lines, check if we should start a new paragraph
		if (empty($current_paragraph)) {
			$current_paragraph = $line;
		} else {
			// Only add space if the current paragraph doesn't end with hyphen
			if (!preg_match('/-$/', $current_paragraph)) {
				$current_paragraph .= ' ';
			}
			$current_paragraph .= $line;
		}

		// If line ends with a period, question mark, etc., save as paragraph
		if (preg_match('/[\.|\:\?\»\…]\s*$/', $line)) {
			$paragraphs[] = trim($current_paragraph);
			$current_paragraph = '';
		}
	}

	// Add any remaining content as last paragraph
	if (!empty($current_paragraph)) {
		$paragraphs[] = trim($current_paragraph);
	}

	$sections = [];
	$current_section = '';
	$current_word_count = 0;

	foreach ($paragraphs as $paragraph) {
		// Count words in the current paragraph
		$paragraph_word_count = str_word_count($paragraph);

		// If single paragraph is longer than 500 words
		if ($paragraph_word_count > 500) {
			// If there's content in current section, save it first
			if ($current_section !== '') {
				$sections[] = trim($current_section);
				$current_section = '';
				$current_word_count = 0;
			}
			// Add long paragraph as its own section
			$sections[] = trim($paragraph);
			continue;
		}

		// Check if adding this paragraph would exceed 500 words
		if ($current_word_count + $paragraph_word_count > 500) {
			// Save current section and start new one
			if ($current_section !== '') {
				$sections[] = trim($current_section);
			}
			$current_section = $paragraph;
			$current_word_count = $paragraph_word_count;
		} else {
			// Add paragraph to current section
			if ($current_section !== '') {
				$current_section .= "\n\n";
			}
			$current_section .= $paragraph;
			$current_word_count += $paragraph_word_count;
		}
	}

	// Add any remaining content as the last section
	if ($current_section !== '') {
		$sections[] = trim($current_section);
	}

	//	foreach ($sections as $index => $section) {
	//		echo "Section " . ($index + 1) . ":\n<br>";
	//		echo "----------------\n<br>";
	//		echo str_replace("\n", "<br>", $section) . "\n<br>";
	//		echo "Word count: " . str_word_count($section) . "\n\n<br><br>";
	//	}
	//	exit();


	//----------------------------------------------

	$run_translation_from_sql_table = true;
	if ($run_translation_from_sql_table) {
		$checkQ = "SELECT * FROM ceviri WHERE book_id=2 ORDER BY section_number ASC";
		$statement = $dbconn->prepare($checkQ);
		$statement->execute();
		$sections_result = $statement->get_result();
		while ($section_row = $sections_result->fetch_assoc()) {
			$section = $section_row['original'];
			$section_number = $section_row['section_number'];
			$completion_tokens = (int)$section_row['completion_tokens'];
			if ($completion_tokens < 20) {
				echo "Section " . $section_number . ":\n<br>";
				echo "----------------\n<br>";
				echo str_replace("\n", "<br>", $section) . "\n<br>";
				echo "Word count: " . str_word_count($section) . "\n\n<br><br>";

				// Prepare messages array with previous section context if available
				$messages = [];
				if ($section_number > 1) {
					$prevQ = "SELECT original, translation FROM ceviri WHERE book_id=2 AND section_number = ?";
					$statement = $dbconn->prepare($prevQ);
					$prev_section = $section_number - 1;
					$statement->bind_param("i", $prev_section);
					$statement->execute();
					$prev_result = $statement->get_result();
					$statement->close();

					if ($prev_row = $prev_result->fetch_assoc()) {
						$messages[] = ['role' => 'user', 'content' => $prev_row['original']];
						$messages[] = ['role' => 'assistant', 'content' => $prev_row['translation']];
					}
				}
				$messages[] = ['role' => 'user', 'content' => $section];

				// Send to LLM and retry if completion tokens are too low
				do {
					$llm_result = llm_no_tool_call('anthropic/claude-3.5-sonnet:beta', $system_prompt, $messages, false);

					if ($llm_result['completion_tokens'] < 10) {
						echo "Completion tokens too low ({$llm_result['completion_tokens']}), retrying...<br>";
						continue;
					}
					break;
				} while (true);

				echo str_replace("\n", "<br>", $llm_result['content']) . "<br><br>";

				$updateQ = "UPDATE ceviri SET book_id = 2, translation = ?, word_count = ?, prompt_tokens = ?, completion_tokens = ? WHERE section_number = ?";
				$statement = $dbconn->prepare($updateQ);
				$word_count = str_word_count($section);
				$statement->bind_param("siiii", $llm_result['content'], $word_count, $llm_result['prompt_tokens'], $llm_result['completion_tokens'], $section_number);
				$statement->execute();
				$statement->close();

			}
		}

		exit();
	}


	//----------------------------------------------

	$total_prompt_tokens = 0;
	$total_completion_tokens = 0;

	foreach ($sections as $index => $section) {
		$section_number = $index + 1;
		echo "Section " . $section_number . ":\n<br>";
		echo "----------------\n<br>";
		echo str_replace("\n", "<br>", $section) . "\n<br>";
		echo "Word count: " . str_word_count($section) . "\n\n<br><br>";

		//check if section is in table
		$checkQ = "SELECT * FROM ceviri WHERE book_id=2 AND section_number = ?";
		$statement = $dbconn->prepare($checkQ);
		$statement->bind_param("i", $section_number);
		$statement->execute();
		$check_result = $statement->get_result();
		$statement->close();

		$redo_section = false;
		if ($check_result->num_rows > 0) {
			$check_row = $check_result->fetch_assoc();
			if ($check_row['completion_tokens'] < 20) {
				$redo_section = true;
			} else {
				echo "Section " . $section_number . " already exists in the database.\n\n<br><br>";
				continue;
			}
		}

		// Prepare messages array with previous section context if available
		$messages = [];
		if ($section_number > 1) {
			$prevQ = "SELECT original, translation FROM ceviri WHERE book_id=2 AND section_number = ?";
			$statement = $dbconn->prepare($prevQ);
			$prev_section = $section_number - 1;
			$statement->bind_param("i", $prev_section);
			$statement->execute();
			$prev_result = $statement->get_result();
			$statement->close();

			if ($prev_row = $prev_result->fetch_assoc()) {
				$messages[] = ['role' => 'user', 'content' => $prev_row['original']];
				$messages[] = ['role' => 'assistant', 'content' => $prev_row['translation']];
			}
		}
		$messages[] = ['role' => 'user', 'content' => $section];

		// Send to LLM and retry if completion tokens are too low
		do {
			$llm_result = llm_no_tool_call('anthropic/claude-3.5-sonnet:beta', $system_prompt, $messages, false);

			if ($llm_result['completion_tokens'] < 10) {
				echo "Completion tokens too low ({$llm_result['completion_tokens']}), retrying...<br>";
				continue;
			}
			break;
		} while (true);

		echo str_replace("\n", "<br>", $llm_result['content']) . "<br><br>";

		$total_prompt_tokens += $llm_result['prompt_tokens'];
		$total_completion_tokens += $llm_result['completion_tokens'];

		if ($redo_section) {
			$updateQ = "UPDATE ceviri SET book_id = 2, translation = ?, word_count = ?, prompt_tokens = ?, completion_tokens = ? WHERE section_number = ?";
			$statement = $dbconn->prepare($updateQ);
			$word_count = str_word_count($section);
			$statement->bind_param("siiii", $llm_result['content'], $word_count, $llm_result['prompt_tokens'], $llm_result['completion_tokens'], $section_number);
			$statement->execute();
			$statement->close();
		} else {
			$insertQ = "INSERT INTO ceviri (book_id, section_number, original, translation, word_count, prompt_tokens, completion_tokens) VALUES (2, ?, ?, ?, ?, ?, ?)";
			$statement = $dbconn->prepare($insertQ);
			$word_count = str_word_count($section);
			$statement->bind_param("issiii", $section_number, $section, $llm_result['content'], $word_count, $llm_result['prompt_tokens'], $llm_result['completion_tokens']);
			$statement->execute();
			$statement->close();
		}
	}


	$system_prompt = '
		Norwegian:
		Markus ble revet ut av en dramatisk og fyrverkerilignende søvn
og kjente et solstreif mot netthinnene, Gud hadde åpnet sitt øye,
det var ihvertfall nøyaktig slik han selv en gang hadde planer
om å få igjen synet, nå fikk han det igjen enda en gang, det var
sommer over landet, en mild og kjærtegnende damp, fredens
nåde, og Markus tenkte – ganske klart egentlig – at dersom
han hadde holdt seg med en tilregnelig og rettferdig Gud, så
ville han ha kunnet foreholde Manstein at hans første militære
mesterverk i denne krigen – «Sigdesnittet» som i 1940 gjorde
det av med hele Frankrike i løpet av en enkelt måned – også
hadde rammet skogene hans med full tyngde, Markus’ egne
skoger, Ardennerskogene, og derfor ville forfølge generalen til
dommens dag, som Europas dårligste samvittighet, og at det var
denne hevnende, men likevel rettferdige kraft som nå nærmet
seg på russiske larveføtter fra alle himmelretninger. Men Mar-
kus hadde ingen rettferdig Gud – tenkte han også forunderlig
klart –, han hadde en forvirret, ubesluttsom, upålitelig og vel-
menende kraft på sin høyre skulder, en due med falkeblikk, en
valmue med jernvinger, en medalje av snø, lik de to bekkene
som bak huset hjemme i Clervaux løper sammen i én og for-
dobler både sin styrke og den gleden hans barn har av å sette
opp et vannhjul, som spinner og spinner gjennom tre årstider
og er låst fast av isen i den fjerde – hva? I det fjerne lød gra-
natnedslag fra en russisk streifer, naturligvis, en russisk strei-
fer som dropper lasten der han synes det passer før han vender
tilbake bak Volgas flate skanser, eller det er Tjukke Beber som
sitter på brisken midt imot ham og famler med en vrien bønn.
– Hva er klokka? sa Markus og spratt opp.
– Åtte, sa Beber forundret, våknet han også, – vår tid.
– Er det skjedd noe?

164

– Næ … egentlig ikke …
Først nå oppdaget Markus at den tunge mannen ikke satt
der i egne tanker, men med et åpnet brev i hånden, fra Tysk-
land tydeligvis. Beber var noen år yngre enn ham, men virket
ti år eldre. I en mer vellykket verden ville han ha vært en me-
lankolsk gartner, en lettlurt landsbylærer eller en omfangsrik
slakter. Nå satt han fast i sin egen kropp og gjorde som han
pleide i slike stunder, trakk den ene hånden fram og tilbake
over det brede, deigfargede fjeset som for å viske ut sine egne
trekk, eller kanskje bare i et lønnlig håp om å slippe å se på
den han snakket med.
– Brev hjemmefra? konverserte Markus og begynte å vaske
seg.
– Ja.
Beber ble taus igjen.
– Som du vil vise meg, du pleier da å ta dem til Kuntnagel?
Markus kjente til denne ordningen. Beber hadde en kone
hjemme i landsbyen i Pommern, som skrev til ham i ett sett
og gjerne var svært utførlig om hvor bra det sto til med de to
sønnene som var stasjonert i Frankrike og Holland. Men Beber
hadde i løpet av høsten begynt å fantasere om at hun førte ham
bak lyset, for å skåne ham, det var nemlig ikke problemer med
noe som helst lenger, mens hun i all den tid han hadde kjent
henne hadde vært en sann sytepave; Kuntnagel måtte forsikre
ham om at selvfølgelig var det sant alt som sto der, både om
sønnene og henne og alt det likegyldige og paradisiske livet de
hadde anledning til å bedrive der hjemme.
– Jeg stoler ikke på ham lenger, sa den store mannen alvor-
lig. – Jeg stoler mer på deg.
Markus lo humørløst og begynte å kle seg.
– Hvorfor det?
– Du vet mer. Du vet hva som kommer til å skje.
– Få se.
Han leste en barnslig, rundhåndet beretning om sju – åtte
høner og en gris, om at hun hadde lagt ned «tonnevis» av kir-
sebær og pærer for vinteren, en gammel nabo skulle slakte for
henne, og «Hans har det godt i Scheveningen, det er jo så fre-
delig der ute ved havet», fra Johann hadde hun ikke hørt noe

165

på en stund, men i det forrige brevet sto det at alt sto bra til,
og de hilser så mye.
– Ja, hva er det med dette? sa Markus irritert. – Selv sitter
du trygt i en generalstab, sønnene dine er like trygge, og din
kone har mat nok, kan det bli bedre?
Beber nikket ettertenksomt, men fikk omsider fram at han
likte ikke setningen «det er jo så fredelig der ute ved havet»
– den var helt overflødig og kledde dessuten ikke det sure og
upoetiske lynnet hennes, som om hun ikke hadde skrevet den
selv, og dessuten hadde han ikke tidligere hørt noe om at yngs-
tesønnen var ved Scheveningen.
– Har vi noe der? spurte han. – Han er politisoldat?
– Jeg vet ikke. Men hold brevet opp mot lyset, les det om
igjen, hvert ord, og lær det utenat.
Beber så skeptisk på ham og ristet på hodet, men gjorde som
han sa.
– Ja? spurte han etter et par minutter.
– Hva sto det?
– Sannelig om jeg vet – det sto vel det samme som du leste?
– Ikke noe mer?
– Nei … jeg tror ikke det.
– Tror?
– Nei, det står ikke noe mer.
– Har du da noen mulighet til å finne ut noe mer?
– Herfra – nei.
Beber var stadig i villrede. Han så lenge på brevet, holdt det
nok en gang mot det bleke vinterlyset og leste mens leppene
beveget seg.

Turkish Translation:
				Markus dramatik, havai fişekleri andıran bir uykudan uyandırıldığında retinasına çarpan güneş ışığını hissetti, Tanrı gözünü açmıştı, en azından bir zamanlar yeniden görmeye başlamak için yaptığı planlar tam böyleydi, şimdi bir kez daha görmeye başlamıştı, yaz mevsimiydi, yumuşak, okşayıcı bir nem ve barışın bağışlayıcılığı yayılmıştı toprağın üzerine ve Markus aklı başında ve adil bir Tanrı’ya bağlı olsaydı Manstein’a bu savaştaki ilk askeri ustalığını – 1940’da yalnızca bir ay içinde bütün Fransa’yı kapsayan ‘yatay kesit’i – gösterebilmeyi isteyeceğini – hem de çok açık bir kafayla – düşündü. O savaş da ormanlara, Markus’ün kendi ormanlarına, Arden ormanlarına tüm ağırlığıyla çökmüştü, bu yüzden Avrupa’nın vicdanında kapkara bir leke olarak yargı gününe dek generali izleyecekti ve şimdi gökyüzünün dört yanından Rus tanklarıyla yaklaşan intikamcı ama yine de adil bir güçtü. Ama Markus’ün adil bir Tanrı’sı yoktu – bunu da şaşılacak bir açıklıkla düşündü – onun sağ omzunda kafası karışık, kararsız, güvenilmez, iyi niyetli bir güç, kartal bakışlı bir güvercin, demir kanatlı bir haşhaş, karlardan bir madalya vardı, tıpkı Clervaux’da, evinin arkasından birlikte akan, güçlerini iki katına çıkaran ve oraya kurduğu çarkları üç mevsim boyunca durmaksızın döndürerek çocukluğunda onu sevindirdikten sonra dördüncü mevsimde buz tutan iki ırmak gibi – ne? Uzaklardan patlayan bir el bombalarının sesi geliyordu, Rus bir gezgin olmalıydı, elbette Volga ovasındaki tabyaların arkasına dönmeden önce yükünü kendince uygun bulduğu bir yere boşaltan bir Rus gezgindi ya da tahta bankın tam ortasında oturmuş çapraşık bir dua mırıldanan Şişko Beber.
Hızla doğrulan Markus “Saat kaç?” diye sordu.
“Sekiz,” dedi Beber şaşkın şaşkın, o da uyanmıştı, “bizim saatimiz.”
“Bir şey mi oldu?”
“Şey… aslında hayır…”
Ancak o zaman Markus şişman adamın orada kendi düşüncelerine dalmış oturmadığının, elinde belli ki Almanya’dan gelen açılmış bir mektup olduğunun farkına vardı. Beber ondan birkaç yaş gençti ama on yıl daha yaşlıymış gibi görünüyordu. Daha şanslı bir dünya olsaydı orada hüzünlü bir bahçıvan, saf bir köy öğretmeni ya da iri yarı bir kasap olurdu. Şimdi kendi bedenine sıkışmış, böyle zamanlarda hep yaptığı gibi kendi çizgilerini silmek için ya da belki konuştuğu insana bakmaktan kurtulma umuduyla bir elini geniş, hamur rengi yüzünde gezdiriyordu.
“Mektup evden mi?” diyen Markus elini yüzünü yıkamaya başladı.
“Evet.”
Beber yine sustu.
“Bana göstermek istiyorsun ama bunları hep Kuntnagel’e göstermez miydin?”
Markus durumu biliyordu. Beber’in Pommern’deki karısı ona durmadan mektuplar gönderiyor, Fransa’ya ve Hollanda’ya gönderilen iki oğullarının ne kadar iyi durumda olduğunu anlatıp duruyordu. Ama sonbaharda Beber karısının üzülmesin diye onu kandırdığından kuşkulanmaya başlamıştı çünkü şimdi hiçbir sorunlarının kalmadığını söyleyen karısı onu tanıdı tanıyalı her şeyden yakınır dururdu; Kuntnagel’in ona güvence vermesi, hem oğulları hem karısının durumu hem de evdekilerin savaştan uzak, cennet gibi yaşamları konusunda mektupta yazanların elbette doğru olduğunu söylemesi gerekiyordu.
“Artık ona güvenmiyorum,” dedi iri yarı adam ciddi bir yüzle. “Sana daha çok güveniyorum.”
Markus gülmeye çalıştı, giyinmeye başladı.
“Niye?”
“Sen daha çok şey biliyorsun. Neler olacağını biliyorsun.”
“Ver bakayım.”
Çocukça ve yuvarlak bir yazıyla yazılmış mektupta yedi sekiz tavuk ve bir domuz hakkında yazılanları, kadının kış için ‘tonlarca’ vişne ve armut topladığını, yaşlı bir komşunun kesim işini üstlendiğini, Hans’ın Scheveningen’de durumunun çok iyi olduğunu, ne de olsa açık denizlerin pek sakin olduğunu, Johann’dan bir süredir haber gelmediğini ama geçen mektubunda her şeyin iyi olduğunu yazıp ona da selam söylediğini okudu.
“Evet, ne var ki bunda?” dedi Markus kızarak. “Sen kendin generalin karargahında güvendesin, çocukların da güvendeler ve karının yeteri kadar yemeği var, bundan daha iyi ne olabilir?”
Beber başıyla onaylarken düşünceliydi, sonunda “ne de olsa açık denizler pek sakindir” cümlesinden hoşlanmadığını söyleyebildi. “Bunu söylemesine hiç gerek yoktu ki,” dedi, ayrıca karısının ekşi, şairanelikten uzak huyuna da hiç uymuyordu, sanki mektubu o yazmamış gibiydi, üstelik küçük oğlunun Scheveningen’de olduğunu zaten biliyordu Beber.
“Bizim orada bir işimiz mi var?” diye sordu. “Oğlum askeri polis mi?”
“Bilmiyorum. Ama mektubu ışığa tut, bir daha oku, her bir sözcüğü ve ezberle.”
Beber kuşkuyla ona bakıp başını salladı ama dediğini yaptı.
“Evet?” dedi bir iki dakika sonra.
“Ne yazıyordu?”
“Ne bileyim, herhalde senin okuduklarının aynısı.”
“Daha fazlası yok mu?”
“Hayır… sanmıyorum.”
“Sanmıyorsun öyle mi?”
“Hayır, daha fazlası yok.”
“Daha fazla birşey öğrenme olanağın var mı?”
“Buradan mı? Hayır.”
Beber’in şaşkınlığı geçmemişti. Uzun süre mektuba baktı, bir kez daha solgun kış ışığına tuttu, dudaklarını kıpırdatarak okudu.';
?>
