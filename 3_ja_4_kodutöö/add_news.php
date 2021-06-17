<?php
require_once "usesession.php";
require_once "../../../../conf.php";
echo $server_host;
// var_dump($_POST);                                          // On olemas ka $_GET - avalik infi saamine  ja  $_REQUEST - sisaldab kõiki meetodeid ja cookisid
$news_input_error = null;
$clean_news_title = "";
$clean_news_content = "";
$clean_news_author = "";

if (isset($_POST["news_submit"])) {
    if (empty($_POST["news_title_input"])){
        $news_input_error = "Uudise pealkiri on puudu! ";
    } else {
        $clean_news_title = Input::str($_POST['news_title_input']);
    }
    if (empty($_POST["news_content_input"])){
        $news_input_error .= "Uudise tekst on puudu! ";
    }
    else {
        $clean_news_content = Input::str($_POST['news_content_input']);
        $clean_news_author = Input::str($_POST['news_author_input']);
    }
    if (empty($news_input_error)){
        //salvestame andmebaasi
        store_news($clean_news_title,$clean_news_content,$clean_news_author);
        $clean_news_title = "";
        $clean_news_content = "";
        $clean_news_author = "";
    }

}
function store_news($news_title,$news_content,$news_author){
    //$GLOBALS["server_host"]
    //loome ühenduse andmebaasiga
    $conn =  new mysqli ($GLOBALS["server_host"],$GLOBALS["server_user_name"],$GLOBALS["server_password"],$GLOBALS["database"]);
    //valmistan ette SQL käsu
    $conn -> set_charset("utf-8");
    $stmt = $conn ->prepare("INSERT INTO vr21_news (vr21_news_title, vr21_news_content, vr21_news_author) VALUES (?,?,?)");
    echo $conn -> error;
    // i - integer   s - string   d - decimal
    $stmt -> bind_param("sss",$news_title,$news_content,$news_author);
    $stmt -> execute();
    $stmt -> close();
    $conn -> close();
}



class  Input {
	static $errors = true;

	static function check($arr, $on = false) {
		if ($on === false) {
			$on = $_REQUEST;
		}
		foreach ($arr as $value) {	
			if (empty($on[$value])) {
				self::throwError('Data is missing', 900);
			}
		}
	}

	static function int($val) {
		$val = filter_var($val, FILTER_VALIDATE_INT);
		if ($val === false) {
			self::throwError('Invalid Integer', 901);
		}
		return $val;
	}

	static function str($val) {
		if (!is_string($val)) {
			self::throwError('Invalid String', 902);
		}
		$val = trim(htmlspecialchars($val));
		return $val;
	}

	static function bool($val) {
		$val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
		return $val;
	}

	static function email($val) {
		$val = filter_var($val, FILTER_VALIDATE_EMAIL);
		if ($val === false) {
			self::throwError('Invalid Email', 903);
		}
		return $val;
	}

	static function url($val) {
		$val = filter_var($val, FILTER_VALIDATE_URL);
		if ($val === false) {
			self::throwError('Invalid URL', 904);
		}
		return $val;
	}

	static function throwError($error = 'Error In Processing', $errorCode = 0) {
		if (self::$errors === true) {
			throw new Exception($error, $errorCode);
		}
	}
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
	<form method="POST">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri">
		<br>
		<label for="news_content_input">Uudise tekst</label>
		<br>
		<textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"></textarea>
		<br>
		<label for="news_author_input">Uudise lisaja nimi</label>
		<br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi">
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<p><?php echo $news_input_error; ?></p>
</body>
</html>