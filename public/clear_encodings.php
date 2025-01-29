<?php
set_time_limit( 3600 );
ob_start();

error_reporting( error_reporting() & ~E_NOTICE );
ini_set( 'error_reporting', E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING );

define( 'izedebiyat_db_username', 'root' );

define('izedebiyat_db_password', 'A123456b!');
define('izedebiyat_db_hostname', 'localhost');

//define( 'izedebiyat_db_password', 'A123456b' );
//define( 'izedebiyat_db_hostname', 'izedebiyat-ssl-mysql' );

define( 'izedebiyat_db_database', 'izedebiyat' );


$dbconn = new mysqli( izedebiyat_db_hostname, izedebiyat_db_username, izedebiyat_db_password, izedebiyat_db_database );
if ( $dbconn->connect_errno ) {
	die( 'Connect Error: ' . $dbconn->connect_errno );
}

$result = $dbconn->query( "SET NAMES utf8" );


function fix_encoding( $text ) {
	$text = preg_replace( "/\r\n|\r|\n/", ' <br> ', $text );
	$text = preg_replace( '/[\x00-\x1F\x7F-\x9F]/u', '', $text );

	$text = str_replace( '[[K]]', '<span class="bold-font">', $text );
	$text = str_replace( '[[/K]]', '</span>', $text );
	$text = str_replace( '[[K]', '<span class="bold-font">', $text );
	$text = str_replace( '[[/K]', '</span>', $text );
	$text = str_replace( '[K]]', '<span class="bold-font">', $text );
	$text = str_replace( '[/K]]', '</span>', $text );
	$text = str_replace( '[K]', '<span class="bold-font">', $text );
	$text = str_replace( '[/K]', '</span>', $text );

	$text = str_replace( '[[I]]', '<span class="italic-font">', $text );
	$text = str_replace( '[[İ]]', '', $text );
	$text = str_replace( '[[/I]]', '</span>', $text );
	$text = str_replace( '[I]]', '<span class="italic-font">', $text );
	$text = str_replace( '[/I]]', '</span>', $text );
	$text = str_replace( '[[I]', '<span class="italic-font">', $text );
	$text = str_replace( '[[/I]', '</span>', $text );
	$text = str_replace( '[I]', '<span class="italic-font">', $text );
	$text = str_replace( '[/I]', '</span>', $text );

	$text = str_replace( '[[O]]', '', $text );
	$text = str_replace( '[[/O]]', '', $text );
	$text = str_replace( '[O]]', '', $text );
	$text = str_replace( '[/O]]', '', $text );
	$text = str_replace( '[[O]', '', $text );
	$text = str_replace( '[[/O]', '', $text );
	$text = str_replace( '[O]', '', $text );
	$text = str_replace( '[/O]', '', $text );

	$text = str_replace( '[[[SA]]', '', $text );
	$text = str_replace( '[[[/SA]]', '', $text );
	$text = str_replace( '[[SA]]', '', $text );
	$text = str_replace( '[[/SA]]', '', $text );
	$text = str_replace( '[[[SA]', '', $text );
	$text = str_replace( '[[[/SA]', '', $text );
	$text = str_replace( '[[SA]', '', $text );
	$text = str_replace( '[[/SA]', '', $text );

	$text = str_replace( '[[[SO]]', '', $text );
	$text = str_replace( '[[[/SO]]', '', $text );
	$text = str_replace( '[[SO]]', '', $text );
	$text = str_replace( '[[/SO]]', '', $text );
	$text = str_replace( '[[[SO]', '', $text );
	$text = str_replace( '[[[/SO]', '', $text );
	$text = str_replace( '[[SO]', '', $text );
	$text = str_replace( '[[/SO]', '', $text );

	$text = str_replace( '[[[A]]', '', $text );
	$text = str_replace( '[[[/A]]', '', $text );
	$text = str_replace( '[[A]]', '', $text );
	$text = str_replace( '[[/A]]', '', $text );
	$text = str_replace( '[[[A]', '', $text );
	$text = str_replace( '[[[/A]', '', $text );
	$text = str_replace( '[[A]', '', $text );
	$text = str_replace( '[[/A]', '', $text );

	$text = str_replace( '[[BP]]', '<br>', $text );
	$text = str_replace( '[[PB]]', '<br>', $text );
	$text = str_replace( '[[YS]]', '<br>', $text );
	$text = str_replace( '[[BP]', '<br>', $text );
	$text = str_replace( '[[PB]', '<br>', $text );
	$text = str_replace( '[[YS]', '<br>', $text );
	$text = str_replace( '[[P]]', '<br>', $text );

	$text = str_replace( 'search q=', 'search?q=', $text );

	$re    = '/(\[\[|\[)( *)YB( *)\=([^\]]*)(\]\]|\])/im';
	$subst = '';
	$text  = preg_replace( $re, $subst, $text );

	$text = str_replace( '[[/YB]', '', $text );

	$re    = '/(\[\[|\[)( *)YR( *)\=([^\]]*)(\]\]|\])/mi';
	$subst = '';
	$text  = preg_replace( $re, $subst, $text );

	$re    = '/\[\[( *)(RESİMSAĞ|RESİMSAG|RESİM SAĞ|RESİM SAG|RESIMSAG|RESIM SAG|RESÝMSAG|RESÝMSAÐ)( *)\=( *)([^]]+)( *)\]\]/mi';
	$subst = '<img src="$4" class="picture-left">';
	$text  = preg_replace( $re, $subst, $text );

	$re    = '/\[\[( *)(RESİMSAĞ|RESİMSAG|RESİM SAĞ|RESİM SAG|RESIMSAG|RESIM SAG|RESÝMSAG)( *)\=( *)([^]]+)( *)\]/mi';
	$subst = '<img src="$4" class="picture-left">';
	$text  = preg_replace( $re, $subst, $text );

	$re    = '/\[\[( *)(RESİMSOL|RESÝMSOL|RESİM SOL|RESÝM SOL|resimsol|RESEMSOL)( *)\=( *)([^]]+)( *)\]\]/mi';
	$subst = '<img src="$4" class="picture-right">';
	$text  = preg_replace( $re, $subst, $text );

	$re    = '/\[\[( *)(RESİMSOL|RESÝMSOL|RESİM SOLA|RESİM SOL|RESÝM SOL)( *)\=( *)([^]]+)( *)\]/mi';
	$subst = '<img src="$4" class="picture-right">';
	$text  = preg_replace( $re, $subst, $text );


	$text = str_replace( 'YR=kavunici]]', '', $text );
	$text = str_replace( '[[K/]]', '', $text );
	$text = str_replace( '[[B]]', '', $text );
	$text = str_replace( ' [[SA ]]', '', $text );
	$text = str_replace( '[[/YR]', '', $text );
	$text = str_replace( '[[/YB', '', $text );
	$text = str_replace( '[[YB]]', '', $text );
	$text = str_replace( '[[Y', '', $text );
	$text = str_replace( '[[/Y', '', $text );
	$text = str_replace( '[[IK]]', '', $text );
	$text = str_replace( '[[/]]', '', $text );
	$text = str_replace( '[[/', '', $text );
	$text = str_replace( '/I]]', '', $text );
	$text = str_replace( 'I]]', '', $text );
	$text = str_replace( 'K]]', '', $text );
	$text = str_replace( '[[/YR]]', '', $text );
	$text = str_replace( '[/YR]]', '', $text );
	$text = str_replace( '[/YB]]', '', $text );
	$text = str_replace( 'SO]]', '', $text );
	$text = str_replace( 'YB]]', '', $text );
	$text = str_replace( 'YR]]', '', $text );
	$text = str_replace( 'B]]', '', $text );
	$text = str_replace( '[[o]]', '', $text );
	$text = str_replace( '[[ı]]', '', $text );
	$text = str_replace( 'YB=2]]', '', $text );
	$text = str_replace( 'YB=3]]', '', $text );
	$text = str_replace( 'YB=2]', '', $text );
	$text = str_replace( 'YB=3]', '', $text );
	$text = str_replace( '=2]]', '', $text );
	$text = str_replace( '=2]', '', $text );
	$text = str_replace( '=1]]', '', $text );
	$text = str_replace( '=1]', '', $text );
	$text = str_replace( 'B=1]]', '', $text );
	$text = str_replace( 'RYR=siyah]]', '', $text );
	$text = str_replace( '[[SO', '', $text );
	$text = str_replace( '[[K', '', $text );
	$text = str_replace( '][[', '', $text );
	$text = str_replace( 'O]]', '', $text );
	$text = str_replace( 'R]]', '', $text );
	$text = str_replace( ']] ]', '', $text );
	$text = str_replace( 'SA ]]', '', $text );

	$text = str_replace( 'þ', 'ş', $text );
	$text = str_replace( 'Þ', 'Ş', $text );

	$text = str_replace( 'ý', 'ı', $text );
	$text = str_replace( 'ð', 'ğ', $text );

	$text = str_replace( '<br>  <br>  <br>  <br>', '<br>  <br>', $text );
	$text = str_replace( '<br>  <br>  <br>', '<br>  <br>', $text );

//	$text = str_replace( '[[/I', '', $text );
//	$text = str_replace( '[[/', '', $text );
//	$text = str_replace( '[[', '', $text );


	$text = trim($text);
	$text = preg_replace( '/^(?:<br\s*\/?>\s*)+/i', '', $text );

	$text = preg_replace('/(<br>)+$/i', '', $text);

	$text = preg_replace('~<(\w+)[^>]*>[\p{Z}\p{C}]*</\1>~ui', '', $text);

	$text = str_replace( '  ', ' ', $text );
	$text = str_replace( '  ', ' ', $text );
	$text = str_replace( '  ', ' ', $text );

	return $text;
}

$more_records = true;
while ($more_records) {

	$stmt = $dbconn->prepare( "SELECT id,title,subheading,main_text FROM articles 
WHERE approved=1 AND deleted=0 
ORDER by id ASC LIMIT 100" );

	$stmt->execute();
	$story_result = $stmt->get_result();
	$more_records = false;
	while ( $story_row = $story_result->fetch_assoc() ) {
		$more_records = true;
		$title  = $story_row["title"];
		$subheading = $story_row["subheading"];
		$article    = $story_row["main_text"];

		$article    = html_entity_decode( $article, ENT_QUOTES );
		$subheading = html_entity_decode( $subheading, ENT_QUOTES );
		$title  = html_entity_decode( $title, ENT_QUOTES );

		$article    = fix_encoding( $article );
		$subheading = fix_encoding( $subheading );
		$title  = fix_encoding( $title );

		$stmt_update = $dbconn->prepare( "UPDATE articles SET title=?, subheading=?, main_text=? WHERE id=" . $story_row["id"] );
		$stmt_update->bind_param( "sss", $title, $subheading, $article );
		$stmt_update->execute();

		echo ". " . $story_row["id"] . " ";


//	if ( stripos( $yeni_article, "[[" ) || stripos( $yeni_article, "]]" ) ) {
//		echo $story_row["id"] . " ---<br>" . $yeni_article;
//		echo "<hr>";
//	} else {
////		echo ". ";
//	}


	}

	ob_flush();
	flush();
}

ob_end_flush();
