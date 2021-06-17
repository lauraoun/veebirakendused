<?php
	
	require_once "../../../../conf.php";
	require_once "usesession.php";
	
	function read_news(){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_news_id, vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_added, vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_new LEFT JOIN vr21_news_photo ON vr21_news_photo_news_id = vr21_news_id ORDER BY vr21_news_id DESC LIMIT 3");
		echo $conn -> error;
		$stmt -> bind_result($vr21_news_id, $news_title_from_db, $news_content_from_db, $news_author_from_db, $news_date_from_db, $picture_file_from_db, $picture_alttext_from_db );
		$stmt -> execute();
		$raw_news_html = null;
		while ($stmt -> fetch()){
			$raw_news_html .= "\n <h2>" .$news_title_from_db ."</h2>";
			$date_of_news = new DateTime($news_date_from_db);
			$raw_news_html .= "\n <p>Uudis on lisatud: " .$date_of_news->format('d-m-Y') ."</p>";
			$raw_news_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
            $news_id= 
			$raw_news_html .= "\n <p>Edastas:  ";
			if(!empty($news_author_from_db)){
				$raw_news_html .= $news_author_from_db;
			} else {
				$raw_news_html .= "Tundmatu reporter";
			}
			$raw_news_html .= '<br><img class="pilt" src="../upload_photos_news/' .$picture_file_from_db .'" alt="' .$picture_alttext_from_db .'">';
			$raw_news_html .= '<br><a href="edit_news.php?news_id='.$news_title_from_db.'">Muuda uudist</a>';
			$raw_news_html .= "</p>";
		}
		$stmt -> close();
		$conn -> close();
		return $raw_news_html;
	}
	
	$news_html = read_news();
	
?>
<!DOCTYPE html>
<link rel="stylesheet" href="stylee.css">
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lugemine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<?php echo $news_html; ?>
</body>
</html>
