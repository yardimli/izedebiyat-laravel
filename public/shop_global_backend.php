<?php
	error_reporting(error_reporting() & ~E_NOTICE);
	ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

	define('izedebiyat_db_username', 'root');

	define('izedebiyat_db_password', 'A123456b!');
	define('izedebiyat_db_hostname', 'localhost');

	define('izedebiyat_db_database', 'izedebiyat');


	$dbconn = new mysqli(izedebiyat_db_hostname, izedebiyat_db_username, izedebiyat_db_password, izedebiyat_db_database);
	if ($dbconn->connect_errno) {
		die('Connect Error: ' . $dbconn->connect_errno);
	}

	$result = $dbconn->query("SET NAMES utf8");

	$category_images = [];
	$category_command = "SELECT * FROM kategoriler";
	$category_result = $dbconn->query($category_command);
	while ($category_row = $category_result->fetch_assoc()) {
		$category_images[$category_row['id']] = $category_row['picture'];
	}

	function generateInitialsAvatar($picture_file, $name, $extra_css = 'border-radius: 0px;', $extra_class = 'avatar')
	{
		$has_picture = false;
		if ($picture_file !== null && $picture_file !== "") {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../storage/app/public/" . $picture_file)) {
				$has_picture = true;
				?>
				<img alt="yazar" style="<?php echo $extra_css; ?>" src="<?php echo "/storage/" . $picture_file; ?>"
				     class="<?php echo $extra_class; ?>">
				<?php if (strpos($picture_file, 'ai_yazar_resimler') !== false): ?>
					<span class="yz-yazar-resim" data-toggle="tooltip" data-placement="top"
					      title="Yapay zekaya yazarın bilgilerini vererek üretildi">YZ</span>
				<?php endif;
			}
		}

		if (!$has_picture) {

			// Clean and get initials (max 2)
			$name = trim($name);
			$words = explode(' ', $name);
			$initials = '';

			for ($i = 0; $i < min(2, count($words)); $i++) {
				if (!empty($words[$i])) {
					$initials .= mb_strtoupper(mb_substr($words[$i], 0, 1));
				}
			}

			// Generate random background color
			$hue = rand(0, 360);
			$saturation = rand(35, 80);
			$lightness = rand(35, 65);
			$bgColor = "hsl($hue, $saturation%, $lightness%)";

			// Determine text color based on background brightness
			// Using relative luminance formula
			$l = $lightness / 100;
			$textColor = ($l > 0.5) ? '#000000' : '#FFFFFF';

			// Generate unique ID for this avatar
			$uniqueId = 'avatar_' . uniqid();

			// CSS styles
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

			echo $css . $html;
		}
	}

	function get_image($yazi_ana_resim, $category_images, $category_id = '', $extra_class = '', $extra_style = '')
	{
		$yazi_ana_resim = str_ireplace('.png', '.jpg', $yazi_ana_resim);
		$yazi_ana_resim = str_replace('\\', '/', $yazi_ana_resim);

		$ai_resim = '';
		if (strpos($yazi_ana_resim, '_00001_') !== false) {
			$ai_resim = '<span class="yz-yazi-resim" data-toggle="tooltip" data-placement="top" title="Yapay zekaya yazının içeriğini vererek üretildi">YZ</span>';
		}

		if ($yazi_ana_resim !== '' && file_exists($_SERVER['DOCUMENT_ROOT'] . "/../storage/app/public/" . "/yazi_resimler/" . $yazi_ana_resim)) {
			return "<div style='position:relative;'><img src='/storage/yazi_resimler/" . $yazi_ana_resim . "' class='" . $extra_class . "' style='" . $extra_style . "' alt='yazı resim'>" . $ai_resim . "</div>";
		} else {
			return "<img src='/storage/catpicbox/" . $category_images[$category_id] . "' class='" . $extra_class . "' style='" . $extra_style . "' alt='yazı resim'>";
		}
	}

	function time_elapsed_string($datetime, $full = false)
	{
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'yil',
			'm' => 'ay',
			'w' => 'hafta',
			'd' => 'gun',
			'h' => 'saat',
			'i' => 'dakika',
			's' => 'saniye',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) {
			$string = array_slice($string, 0, 1);
		}

		return $string ? implode(', ', $string) . ' once' : 'just now';
	}

	function site_estimated_reading_time($content = '', $wpm = 250)
	{
		$clean_content = strip_tags($content);
		$word_count = str_word_count($clean_content);
		$time = ceil($word_count / $wpm);
		if ($time == 0) {
			$time = 1;
		}
		if ($time >= 60) {

			$hours = floor($time / 60);
			$minutes = ($time % 60);

			if ($minutes === 0) {
				return $hours . " saat okuma";
			}

			return $hours . " saat, " . $minutes . " dk okuma";
		}

		return $time . " dk okuma";
	}

	function remove_bad_characters($input)
	{
		$badchar = array(
			// control characters
			chr(0),
			chr(1),
			chr(2),
			chr(3),
			chr(4),
			chr(5),
			chr(6),
			chr(7),
			chr(8),
			chr(9),
			chr(10),
			chr(11),
			chr(12),
			chr(13),
			chr(14),
			chr(15),
			chr(16),
			chr(17),
			chr(18),
			chr(19),
			chr(20),
			chr(21),
			chr(22),
			chr(23),
			chr(24),
			chr(25),
			chr(26),
			chr(27),
			chr(28),
			chr(29),
			chr(30),
			chr(31),
			// non-printing characters
			chr(127)
		);

		return str_replace($badchar, '', $input);
	}

	function get_words($sentence, $count = 10, $keep_breaks = true)
	{

		//$sentence = remove_bad_characters($sentence);
		$sentence = trim($sentence);

		$sentence = preg_replace('/\[\[.*?\]\]/', '', $sentence);


		$first_lines = "";

		$sentence = str_ireplace(['<br/>', '<br>'], '<br>', $sentence);
		$lines = explode('<br>', $sentence);

		$i = 1;
		foreach ($lines as $line) {
			if (trim($line) !== "") {
				if ($keep_breaks) {
					$first_lines .= $line . "<br>";
				} else {
					$first_lines .= $line . " / ";
				}
				$i++;
				if ($i >= 6) {
					break;
				}
			}
		}

		return implode(' ', array_slice(explode(' ', $first_lines), 0, $count));;
	}

	$unwanted_array = array(
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
	);

	function slugify($str)
	{
		global $unwanted_array;
		$divider = '_';

		$text = strtr($str, $unwanted_array);
		$text = str_replace(" ", $divider, $text);
		$text = strtolower($text);

		// replace non letter or digits by divider
		$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, $divider);

		// remove duplicate divider
		$text = preg_replace('~-+~', $divider, $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	function replace_ascii($input)
	{

		$input = preg_replace("/\r\n|\r|\n/", '<br/>', $input);

		$input = str_replace("", "...", $input);
		$input = str_replace("", "-", $input);
		$input = str_replace("", " ", $input);
		$input = str_replace("", "'", $input);
		$input = str_replace("", " ", $input);

		$input = str_replace("  ", " ", $input);
		$input = str_replace("  ", " ", $input);
		$input = str_replace("  ", " ", $input);

		//trim <br> from top and bottom
		$input = preg_replace('/^(\s*<br\s*\/?>\s*)*/', '', $input);  // top
		$input = preg_replace('/(\s*<br\s*\/?>\s*)*$/', '', $input);  // bottom
		return $input;

	}
