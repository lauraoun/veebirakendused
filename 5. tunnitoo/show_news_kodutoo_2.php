<?php
	
	require_once "../../../../conf.php";
	require_once "fnc_general.php";
	
	function read_news($news_limit){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_added FROM vr21_new ORDER BY vr21_news_id DESC LIMIT ?");
		echo $conn -> error;
		$stmt->bind_param("i", $news_limit);
		$stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_added_from_db);
		$stmt -> execute();
		$raw_news_html = null;
		while ($stmt -> fetch()){
			$raw_news_html .= "\n <h2>" .$news_title_from_db ."</h2>";
			$date_of_news = new DateTime($news_added_from_db);
			$raw_news_html .= "<p>Lisatud: " .$date_of_news->format("m.d.Y") ." kell " .$date_of_news->format("H:i");
			$raw_news_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
			$raw_news_html .= "\n <p>Edastas: ";
			if(!empty($news_author_from_db)){
				$raw_news_html .= $news_author_from_db;
			} else {
				$raw_news_html .= "Tundmatu reporter";
			}
			$raw_news_html .= "</p>";
		}
		$stmt -> close();
		$conn -> close();
		return $raw_news_html;
	}
	
	$news_limit = 3;
	
	if(isset($_POST["news_limit_submit"])){
		if(!empty($_POST["news_limit_input"])){
			$news_limit = filter_var($_POST["news_limit_input"], FILTER_VALIDATE_INT);
		}
	}
	
	$news_html = read_news($news_limit);
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lugemine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<form method="POST"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label for="news_limit_input">Mitu uudist korraga näidata: </label>
		<input id="news_limit_input" name="news_limit_input" type="number" min="1" max="10" value="<?php echo $news_limit; ?>">
		<input id="news_limit_submit" name="news_limit_submit" type="submit" value="Rakenda">
	</form>
	<?php echo $news_html; ?>
</body>
</html>