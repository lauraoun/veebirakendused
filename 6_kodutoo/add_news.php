<?php
require_once "usesession.php";
require_once "../../../../conf.php";
require_once "fnc_user.php";
require_once "classes/Upload_photo.class.php";	
echo $server_host;
// var_dump($_POST);                                          // On olemas ka $_GET - avalik infi saamine  ja  $_REQUEST - sisaldab kõiki meetodeid ja cookisid
$news_input_error = null;
$clean_news_title = "";
$clean_news_content = "";
$clean_news_author = "";
$photo_upload_error = null;
$image_file_type = null;
$image_file_name = null;
$file_name_prefix = "vr_";
$file_size_limit = 1 * 1024 * 1024;
$image_max_w = 600;
$image_max_h = 400;
$image_thumbnail_size = 100;
$notice = null;

function store_news($news_title, $news_content, $news_author){
    //echo $news_title .$news_content .$news_author;
    //echo $GLOBALS["server_host"];

    // loome andmebaasi serveri ja baasiga ühenduse
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"],);
    //määrame suhtluseks kodeeringu
    $conn -> set_charset("utf8");
    // valmistan ette SQL käsu
    $stmt = $conn -> prepare("INSERT INTO vr21_new (vr21_news_news_title, vr21_news_news_content, vr21_news_news_author) VALUES (?,?,?)");
    echo $conn -> error;
    // ?-ga andmete sidumine i-integer, s-string d-decimal, peavad ühtima väljadega
    $stmt -> bind_param("sss", $news_title, $news_content, $news_author);
    $stmt -> execute();
    $news_id = $conn -> insert_id;
    echo $news_id;
    $stmt -> close();
    $conn -> close();
    $GLOBALS["news_input_error"] = null;
    $GLOBALS["news_title"] = null;
    $GLOBALS["news_content"] = null;
    $GLOBALS["news_author"] = null;
    return $news_id;
}



function store_news_photo ($picture_filename, $picture_alttext, $uploader_id, $news_id) {
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn -> set_charset("utf8");
    $stmt = $conn -> prepare("INSERT INTO vr21_news_photo (vr21_news_photo_news_id, vr21_news_photo_filename, vr21_news_photo_alt_text, vr21_news_photo_owner_id) VALUES (?,?,?,?)");
    echo $conn -> error;
    $stmt -> bind_param("issi",$news_id, $picture_filename, $picture_alttext, $uploader_id);
    echo $news_id, $picture_filename, $picture_alttext, $uploader_id;
    $stmt -> execute();
    $stmt -> close();
    $conn -> close();
}

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
        $news_id = store_news($news_title, $news_content, $news_author);
    }
    
    if(!empty($_FILES["file_input"]["name"])) {
        var_dump($_FILES["file_input"]);
        $image_file_name=$_FILES["file_input"]["name"];
        store_news_photo($image_file_name, $_POST["alt_text"], $_SESSION["user_id"], $news_id);
                
            }
            $target_file = "../upload_photos_news/" .$image_file_name;
            //if(file_exists($target_file))
            if(move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)){
                $notice .= " Originaalfoto üleslaadimine õnnestus!";
            } else {
                $photo_upload_error .= " Originaalfoto üleslaadimine ebaõnnestus!";
            }

}

function test_input($input) { // sisendandmete valideerimise funktsioon
    $data = trim($input);
    $data = stripslashes($input);
    $data = htmlspecialchars($input);
    return $input;
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
	<form method="POST"action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="news_title_input">Uudise pealkiri</label>
		<br>
		<input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri">
		<br>
		<label for="news_content_input">Uudise tekst</label>
		<br>
		<textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"></textarea>
		<br>
		<label for="file_input">Lisa pilt</label>

		<input id="file_input" name="file_input" type="file">
        <br>
        <label for="alt_input">Pildi selgitus</label>
        <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on:">
        <br>
		<label for="news_author_input">Uudise lisaja nimi</label>
		<br>
		<input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi">
		<br>
		<input type="submit" name="news_submit" value="Salvesta uudis!">
	</form>
	<p>
    <?php echo $news_input_error; ?></p>
</body>
</html>