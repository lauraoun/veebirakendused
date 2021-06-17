<?php
	
	require_once "../../../../conf.php";
	require_once "fnc_general.php";
	//echo $server_host;
	$news_input_error = null;
	$news_title = null;
	$news_content = null;
	$news_author = null;
	//var_dump($_POST); // on olemas ka $_GET
	if(isset($_POST["news_submit"])){
		if(empty($_POST["news_title_input"])){
			$news_input_error = "Uudise pealkiri on puudu! ";
		} else {
			$news_title = test_input($_POST["news_title_input"]);
		}
		if(empty($_POST["news_content_input"])){
			$news_input_error .= "Uudise tekst on puudu!";
		} else {
			$news_content = test_input($_POST["news_content_input"]);
		}
		if(!empty($_POST["news_author_input"])){
			$news_author = test_input($_POST["news_author_input"]);
		}
		
		if(empty($news_input_error)){
			//salvestame andmebaasi
			store_news($news_title, $news_content, $news_author);
		}
	}
	
	function store_news($news_title, $news_content, $news_author){
		//echo $news_title .$news_content .$news_author;
		//echo $GLOBALS["server_host"];
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("INSERT INTO vr21_new (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author) VALUES (?,?,?)");
		echo $conn -> error;
		//i - integer   s - string   d - decimal
		$stmt -> bind_param("sss", $news_title, $news_content, $news_author);
		$stmt -> execute();
		$stmt -> close();
		$conn -> close();
		$GLOBALS["news_input_error"] = null;
		$GLOBALS["news_title"] = null;
		$GLOBALS["news_content"] = null;
		$GLOBALS["news_author"] = null;
	}
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste lisamine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<form method="POST"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $news_title; ?>">
		<br>
		<label for="news_content_input">Uudise tekst</label>
		<br>
		<textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $news_content; ?></textarea>
		<br>
		<label for="news_author_input">Uudise lisaja nimi</label>
		<br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $news_author; ?>">
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<p><?php echo $news_input_error; ?></p>
</body>
</html>