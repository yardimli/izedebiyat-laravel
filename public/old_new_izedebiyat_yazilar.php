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
	$db1 = new mysqli($host, $username, $password, 'izedebiyat');
	if ($db1->connect_error) {
		die("Connection to izedebiyat failed: " . $db1->connect_error);
	}
	$db1->set_charset("utf8mb4");

// Connect to izedebiyat_asp database
	$db2 = new mysqli($host, $username, $password, 'izedebiyat_asp');
	if ($db2->connect_error) {
		die("Connection to izedebiyat_asp failed: " . $db2->connect_error);
	}
	$db2->set_charset("utf8mb4");

// Get the maximum ID from izedebiyat database
	$maxIdQuery = "SELECT MAX(id) as max_id FROM yazilar";
	$result = $db1->query($maxIdQuery);
	$row = $result->fetch_assoc();
	$lastId = $row['max_id'];

// Get new records from izedebiyat_asp
	$query = "SELECT * FROM yazilar WHERE yaziID > $lastId ORDER BY yaziID ASC";
	$result = $db2->query($query);

	if ($result->num_rows > 0) {
		echo "<h2>New Records Found: " . $result->num_rows . "</h2>";

		while ($row = $result->fetch_assoc()) {
			// Create INSERT query
			$insertQuery = "INSERT INTO izedebiyat.yazilar SET 
            id = {$row['yaziID']},
            katilma_tarihi = " . ($row['ktarih'] ? "'{$row['ktarih']}'" : "NULL") . ",
            onay = {$row['onay']},
            baslik = " . ($row['baslik'] ? "'" . $db1->real_escape_string($row['baslik']) . "'" : "NULL") . ",
            slug = " . ($row['baslik'] ? "'" . slugify($db1->real_escape_string($row['baslik'])) . "'" : "NULL") . ",
            alt_baslik = " . ($row['altbaslik'] ? "'" . $db1->real_escape_string($row['altbaslik']) . "'" : "NULL") . ",
            yayinlama_tarih = " . ($row['ytarih'] ? "'" . $db1->real_escape_string($row['ytarih']) . "'" : "NULL") . ",
            tanitim = " . ($row['tanitim'] ? "'" . $db1->real_escape_string(replace_ascii($row['tanitim'])) . "'" : "NULL") . ",
            yazi = " . ($row['yazi'] ? "'" . $db1->real_escape_string(replace_ascii($row['yazi'])) . "'" : "NULL") . ",
            yazar_ad = " . ($row['yazarad'] ? "'" . $db1->real_escape_string($row['yazarad']) . "'" : "''") . ",
            yazar_slug = " . ($row['yazarad'] ? "'" . slugify($db1->real_escape_string($row['yazarad'])) . "'" : "''") . ",
            kategori_ad = " . ($row['katead'] ? "'" . $db1->real_escape_string($row['katead']) . "'" : "''") . ",
            kategori_slug = " . ($row['katead'] ? "'" . slugify($db1->real_escape_string($row['katead'])) . "'" : "''") . ",
            ust_kategori_ad = " . ($row['ustkatead'] ? "'" . $db1->real_escape_string($row['ustkatead']) . "'" : "''") . ",
            ust_kategori_slug = " . ($row['ustkatead'] ? "'" . slugify($db1->real_escape_string($row['ustkatead'])) . "'" : "''") . ",
            org_yazar = " . ($row['orgyazar'] ? "'" . $db1->real_escape_string($row['orgyazar']) . "'" : "NULL") . ",
            yazi_resim = " . ($row['yaziresim'] ? "'" . $db1->real_escape_string($row['yaziresim']) . "'" : "NULL") . ",
            silindi = {$row['silindi']},
            yazar_id = {$row['yazarno']},
            ust_kategori_id = {$row['ustkateno']},
            kategori_id = {$row['kateno']},
            formul_ekim = {$row['FormulEkim']},
            sayac = {$row['sayac']},
            gunluk_sayac = {$row['gunluksayac']},
            yaslilikgun = {$row['yaslilikgun']},
            oncekikonum = {$row['oncekikonum']},
            yorumsay = {$row['yorumsay']},
            kutuphanesay = {$row['kutuphanesay']},
            oncekikonum_altkate = {$row['oncekikonum_altkate']},
            oncekikonum_ustkate = {$row['oncekikonum_ustkate']},
            
            yazi_sira = {$row['yaziSira']}";

			echo "<div style='margin-bottom: 20px;'>";
			echo "<h3>New Record ID: {$row['yaziID']}</h3>";
			echo "<pre>" . htmlspecialchars($insertQuery) . ";</pre>";
			echo "</div>";

			// Uncomment the following line to actually execute the INSERT query
			 $db1->query($insertQuery);
		}
	} else {
		echo "<h2>No new records found</h2>";
	}











		// Get the maximum ID from izedebiyat database
		$maxIdQuery = "SELECT MAX(id) as max_id FROM yazar";
		$result = $db1->query($maxIdQuery);
		$row = $result->fetch_assoc();
		$lastId = $row['max_id'];

		// Get new records from izedebiyat_asp
		$query = "SELECT * FROM yazar WHERE yazarID > $lastId ORDER BY yazarID ASC";
		$result = $db2->query($query);

		if ($result->num_rows > 0) {
			echo "<h2>New Records Found: " . $result->num_rows . "</h2>";

			while ($row = $result->fetch_assoc()) {
				// Create INSERT query
				$insertQuery = "INSERT INTO izedebiyat.yazar SET 
            id = {$row['yazarID']},
            yazar_ad = " . ($row['yazarad'] ? "'" . $db1->real_escape_string($row['yazarad']) . "'" : "NULL") . ",
            slug = " . ($row['yazarad'] ? "'" . slugify($db1->real_escape_string($row['yazarad'])) . "'" : "NULL") . ",
            nick = " . ($row['nick'] ? "'" . $db1->real_escape_string($row['nick']) . "'" : "NULL") . ",
            sifre = " . ($row['sifre'] ? "'" . $db1->real_escape_string($row['sifre']) . "'" : "NULL") . ",
            eposta = " . ($row['eposta'] ? "'" . $db1->real_escape_string($row['eposta']) . "'" : "NULL") . ",
            sayfa_baslik = " . ($row['sayfabaslik'] ? "'" . $db1->real_escape_string($row['sayfabaslik']) . "'" : "NULL") . ",
            site_adres = " . ($row['siteadres'] ? "'" . $db1->real_escape_string($row['siteadres']) . "'" : "NULL") . ",
            yazar_tanitim = " . ($row['ytanitim'] ? "'" . $db1->real_escape_string(replace_ascii($row['ytanitim'])) . "'" : "NULL") . ",
            yazar_ozellik = " . ($row['yozellik'] ? "'" . $db1->real_escape_string(replace_ascii($row['yozellik'])) . "'" : "NULL") . ",
            yazar_etkiler = " . ($row['yetkiler'] ? "'" . $db1->real_escape_string($row['yetkiler']) . "'" : "NULL") . ",
            yazar_benzerler = " . ($row['ybenzerler'] ? "'" . $db1->real_escape_string($row['ybenzerler']) . "'" : "NULL") . ",
            yazar_gecmis = " . ($row['ygecmis'] ? "'" . $db1->real_escape_string(replace_ascii($row['ygecmis'])) . "'" : "NULL") . ",
            yazar_resim_yazi = " . ($row['yresimyazi'] ? "'" . $db1->real_escape_string(replace_ascii($row['yresimyazi'])) . "'" : "NULL") . ",
            yazar_konum = " . ($row['ykonum'] ? "'" . $db1->real_escape_string($row['ykonum']) . "'" : "NULL") . ",
            yazar_adres = " . ($row['yadres'] ? "'" . $db1->real_escape_string($row['yadres']) . "'" : "NULL") . ",
            sehir = " . ($row['sehir'] ? $row['sehir'] : "NULL") . ",
            ulke = " . ($row['ulke'] ? $row['ulke'] : "NULL") . ",
            link1 = " . ($row['link1'] ? "'" . $db1->real_escape_string($row['link1']) . "'" : "NULL") . ",
            link1_aciklama = " . ($row['link1aciklama'] ? "'" . $db1->real_escape_string($row['link1aciklama']) . "'" : "NULL") . ",
            link2 = " . ($row['link2'] ? "'" . $db1->real_escape_string($row['link2']) . "'" : "NULL") . ",
            link2_aciklama = " . ($row['link2aciklama'] ? "'" . $db1->real_escape_string($row['link2aciklama']) . "'" : "NULL") . ",
            link3 = " . ($row['link3'] ? "'" . $db1->real_escape_string($row['link3']) . "'" : "NULL") . ",
            link3_aciklama = " . ($row['link3aciklama'] ? "'" . $db1->real_escape_string($row['link3aciklama']) . "'" : "NULL") . ",
            link4 = " . ($row['link4'] ? "'" . $db1->real_escape_string($row['link4']) . "'" : "NULL") . ",
            link4_aciklama = " . ($row['link4aciklama'] ? "'" . $db1->real_escape_string($row['link4aciklama']) . "'" : "NULL") . ",
            link5 = " . ($row['link5'] ? "'" . $db1->real_escape_string($row['link5']) . "'" : "NULL") . ",
            link5_aciklama = " . ($row['link5aciklama'] ? "'" . $db1->real_escape_string($row['link5aciklama']) . "'" : "NULL") . ",
            bgsecim = " . ($row['bgsecim'] ? $row['bgsecim'] : "NULL") . ",
            yazar_resim = " . ($row['yresim'] ? "'" . $db1->real_escape_string($row['yresim']) . "'" : "NULL") . ",
            yazar_portre = " . ($row['yportre'] ? "'" . $db1->real_escape_string($row['yportre']) . "'" : "NULL") . ",
            yazar_imge_say = " . ($row['yimgesay'] ? $row['yimgesay'] : "NULL") . ",
            katilma_tarih = " . ($row['ktarih'] ? "'{$row['ktarih']}'" : "NULL") . ",
            onay = {$row['onay']},
            adak = " . ($row['adak'] ? "'" . $db1->real_escape_string($row['adak']) . "'" : "NULL") . ",
            resim1_aciklama = " . ($row['resim1aciklama'] ? "'" . $db1->real_escape_string($row['resim1aciklama']) . "'" : "NULL") . ",
            resim2_aciklama = " . ($row['resim2aciklama'] ? "'" . $db1->real_escape_string($row['resim2aciklama']) . "'" : "NULL") . ",
            resim3_aciklama = " . ($row['resim3aciklama'] ? "'" . $db1->real_escape_string($row['resim3aciklama']) . "'" : "NULL") . ",
            resim4_aciklama = " . ($row['resim4aciklama'] ? "'" . $db1->real_escape_string($row['resim4aciklama']) . "'" : "NULL") . ",
            resim5_aciklama = " . ($row['resim5aciklama'] ? "'" . $db1->real_escape_string($row['resim5aciklama']) . "'" : "NULL") . ",
            resim6_aciklama = " . ($row['resim6aciklama'] ? "'" . $db1->real_escape_string($row['resim6aciklama']) . "'" : "NULL") . ",
            aylikbulten = {$row['aylikbulten']},
            otomatikonay = {$row['otomatikonay']},
            okur_uye_id = {$row['okurUyeID']},
            yazi_var = {$row['yaziVar']},
            ip_log = " . ($row['IPLog'] ? "'" . $db1->real_escape_string($row['IPLog']) . "'" : "NULL");

				echo "<div style='margin-bottom: 20px;'>";
				echo "<h3>New Record ID: {$row['yazarID']}</h3>";
				echo "<pre>" . htmlspecialchars($insertQuery) . ";</pre>";
				echo "</div>";

				// Uncomment the following line to actually execute the INSERT query
				 $db1->query($insertQuery);
			}
		} else {
			echo "<h2>No new records found</h2>";
		}


		// Close database connections
	$db1->close();
	$db2->close();
?>
