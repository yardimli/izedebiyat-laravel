<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
<body>
<?php
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

	// Database connection parameters
	$host = 'localhost';
	$username = 'root';
	$password = 'A123456b!';

	// Connect to izedebiyat database
	$new_db = new mysqli($host, $username, $password, 'izedebiyat',3306);
	if ($new_db->connect_error) {
		die("Connection to izedebiyat failed: " . $new_db->connect_error);
	}
	$new_db->set_charset("utf8mb4");

	// Connect to izedebiyat_asp database
	$old_db = new mysqli($host, $username, $password, 'izedebiyat_asp',3306);
	if ($old_db->connect_error) {
		die("Connection to izedebiyat_asp failed: " . $old_db->connect_error);
	}
	$old_db->set_charset("utf8mb4");

// Get the maximum ID from izedebiyat database
	$maxIdQuery = "SELECT MAX(id) as max_id FROM articles";
	$result = $new_db->query($maxIdQuery);
	$row = $result->fetch_assoc();
	$lastId = $row['max_id'];

// Get new records from izedebiyat_asp
	$query = "SELECT * FROM yazilar WHERE yaziID > $lastId ORDER BY yaziID ASC";
	$result = $old_db->query($query);

	if ($result->num_rows > 0) {
		echo "<h2>New Records Found: " . $result->num_rows . "</h2>";

		while ($row = $result->fetch_assoc()) {
			// Create INSERT query
			$insertQuery = "INSERT INTO izedebiyat.articles SET 
            id = {$row['yaziID']},
            created_at = " . ($row['ktarih'] ? "'{$row['ktarih']}'" : "NULL") . ",
            approved = {$row['onay']},
            title = " . ($row['baslik'] ? "'" . $new_db->real_escape_string($row['baslik']) . "'" : "NULL") . ",
            slug = " . ($row['baslik'] ? "'" . slugify($new_db->real_escape_string($row['baslik'])) . "'" : "NULL") . ",
            subtitle = " . ($row['altbaslik'] ? "'" . $new_db->real_escape_string($row['altbaslik']) . "'" : "NULL") . ",
            publishing_date = " . ($row['ytarih'] ? "'" . $new_db->real_escape_string($row['ytarih']) . "'" : "NULL") . ",
            subheading = " . ($row['tanitim'] ? "'" . $new_db->real_escape_string(replace_ascii($row['tanitim'])) . "'" : "NULL") . ",
            main_text = " . ($row['yazi'] ? "'" . $new_db->real_escape_string(replace_ascii($row['yazi'])) . "'" : "NULL") . ",
            name = " . ($row['yazarad'] ? "'" . $new_db->real_escape_string($row['yazarad']) . "'" : "''") . ",
            name_slug = " . ($row['yazarad'] ? "'" . slugify($new_db->real_escape_string($row['yazarad'])) . "'" : "''") . ",
            category_name = " . ($row['katead'] ? "'" . $new_db->real_escape_string($row['katead']) . "'" : "''") . ",
            category_slug = " . ($row['katead'] ? "'" . slugify($new_db->real_escape_string($row['katead'])) . "'" : "''") . ",
            parent_category_name = " . ($row['ustkatead'] ? "'" . $new_db->real_escape_string($row['ustkatead']) . "'" : "''") . ",
            parent_category_slug = " . ($row['ustkatead'] ? "'" . slugify($new_db->real_escape_string($row['ustkatead'])) . "'" : "''") . ",
            deleted = {$row['silindi']},
            user_id = {$row['yazarno']},
            parent_category_id = {$row['ustkateno']},
            category_id = {$row['kateno']},
            formul_ekim = {$row['FormulEkim']},
            read_count = {$row['sayac']},
            article_order = {$row['yaziSira']}";

			echo "<div style='margin-bottom: 20px;'>";
			echo "<h3>New Record ID: {$row['yaziID']}</h3>";
			echo "<pre>" . htmlspecialchars($insertQuery) . ";</pre>";
			echo "</div>";

			// Uncomment the following line to actually execute the INSERT query
			 $new_db->query($insertQuery);
		}
	} else {
		echo "<h2>No new records found</h2>";
	}













		// Close database connections
	$new_db->close();
	$old_db->close();
?>
