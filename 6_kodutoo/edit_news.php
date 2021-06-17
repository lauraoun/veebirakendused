<?php
	
	require_once "../../../../conf.php";
	require_once "usesession.php";
    require_once "fnc_general.php";
    require_once "classes/Upload_photo.class.php";
	
    $news_input_error = null;
    $news_title = null;
    $news_content = null;
    $news_author = null;
    $file_size_limit = 1 * 1024 * 1024;
    $photo_upload_error = null;
    $notice = null;
    $result = null;
    $news_id = (int)$_REQUEST["news_id"];
    $news_parameters_from_db = read_news($news_id);
    $title_old = $news_parameters_from_db[0];
    $content_old = $news_parameters_from_db[1];
    $author_old = $news_parameters_from_db[2];
    $existing_pic = $news_parameters_from_db[3];
    $picture_id_from_db = $news_parameters_from_db[4];
    $picture_alttext_from_db = $news_parameters_from_db[5];


	function news_update($news_title, $news_content, $news_author, $news_picture_id, $news_id){
        //echo $news_title .$news_content .$news_author;
        //echo $GLOBALS["server_host"];
         // loome andmebaasi serveri ja baasiga ühenduse
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"],);
        //määrame suhtluseks kodeeringu
        $conn -> set_charset("utf8");
        // valmistan ette SQL käsu
        $stmt = $conn -> prepare(" UPDATE vr21_new (vr21_news_news_title = ?, vr21_news_news_content = ?, vr21_news_news_author = ? , picture_id = ? WHERE news_id = ?");
        echo $conn -> error;
        // ?-ga andmete sidumine i-integer, s-string d-decimal, peavad ühtima väljadega
        $stmt -> bind_param("sssii", $news_title, $news_content, $news_author, $news_picture_id, $news_id);
        $stmt -> execute();
        $news_id = $conn -> insert_id;
        echo $news_id;
        $stmt -> close();
        $conn -> close();
        return $news_id;
    }
       
    function news_update_photo ($photo_filename, $photo_alttext, $uploader_id, $picture_id_from_db) {
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $conn -> set_charset("utf8");
        $stmt = $conn -> prepare("UPDATE vr21_news_photo SET vr21_news_photo_news_id = ?, vr21_news_photo_filename = ?, vr21_news_photo_alt_text = ?, vr21_news_photo_owner_id = ?) VALUES (?,?,?,?)");
        echo $conn -> error;
        $stmt -> bind_param("ssii", $photo_filename, $photo_alttext, $uploader_id, $picture_id_from_db);
        echo $photo_filename, $photo_filename, $photo_alttext, $uploader_id;
        $stmt2 = $conn -> prepare("UPDATE vr21_new SET picture_id = ? WHERE news_id = ?");
        $stmt2 -> bind_param("ii", $pic_id, $news_id);
        $stmt2 -> execute();
        $stmt2 -> close();
        $stmt -> close();
        $conn -> close();
    
    }
    function read_news(){
		//loome andmebaasis serveriga ja baasiga ühenduse
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määrame suhtluseks kodeeringu
		$conn -> set_charset("utf8");
		//valmistan ette SQL käsu
		$stmt = $conn -> prepare("SELECT vr21_news_news_title, vr21_news_news_content, vr21_news_news_author, vr21_news_added, vr21_news_photo_filename, vr21_news_photo_alt_text FROM vr21_new LEFT JOIN vr21_news_photo ON vr21_news_photo_news_id = vr21_news_id ORDER BY vr21_news_id DESC LIMIT 3");
		echo $conn -> error;
		$stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_date_from_db, $picture_file_from_db, $picture_alttext_from_db );
		$stmt -> execute();
		$raw_news_html = null;
		while ($stmt -> fetch()){
			$raw_news_html .= "\n <h2>" .$news_title_from_db ."</h2>";
			$date_of_news = new DateTime($news_date_from_db);
			$raw_news_html .= "\n <p>Uudis on lisatud: " .$date_of_news->format('d-m-Y') ."</p>";
			$raw_news_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
	
			$raw_news_html .= "\n <p>Edastas:  ";
			if(!empty($news_author_from_db)){
				$raw_news_html .= $news_author_from_db;
			} else {
				$raw_news_html .= "Tundmatu reporter";
			}
			$raw_news_html .= '<br><img class="pilt" src="../upload_photos_news/' .$picture_file_from_db .'" alt="' .$picture_alttext_from_db .'">';
			$raw_news_html .= "</p>";
		}
		$stmt -> close();
		$conn -> close();
		return $raw_news_html;
        $stmt -> close();
        $conn -> close();
        return $news_parameters_from_db;
    }
        if (isset($_POST["news_submit"])) {
            $_SESSION["success"] = 1;
            header('location: show_news.php');
            if(file_exists($_FILES["file_input"]["tmp_name"]) || is_uploaded_file($_FILES["file_input"]['tmp_name'])) {
                $photo_upload = new Upload_photo($_FILES["file_input"], $file_size_limit);
                $photo_upload_error .= $photo_upload->photo_upload_error;
                if (empty($photo_upload_error)) {
                    $image_file_name = $photo_upload->image_filename();
                    $target_file = "../upload_photos_news/" .$image_file_name;
                    $result = $photo_upload->save_image_to_file($target_file, true);
                    if($result == 1) {
                        $notice = " Pilt on salvestatud!";
                    } else {
                        $photo_upload_error = " Pilti ei salvestatud!";
                    }
                    unset($photo_upload);
                    if (empty($photo_upload_error)) {
                        update_news_photo($image_file_name, $_POST["alt_text"], $_SESSION["user_id"], $picture_id_from_db);
                    }
                }
            }

            $news_id = $_POST["news_id_input"];
            $photo_id = $_POST["photo_id_input"];
            if (empty($_POST["news_title_input"])) {
                $news_input_error = "Uudise pealkiri on puudu! ";
            } else {
                $news_title = test_input($_POST["news_title_input"]);
            }
            if (empty($_POST["news_content_input"])) {
                $news_input_error .= "Uudise tekst on puudu!";
            } else {
                $news_content = test_input($_POST["news_content_input"]);
            }
            if (!empty($_POST["news_author_input"])){
                $news_author = test_input($_POST["news_author_input"]);
            }
            if (empty($news_input_error)) {
                $news_input_error .= "Uudis edukalt lisatud!";
                //salvestame andmebaasi
                update_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $picture_id_from_db, $news_id);
            }
        }
        
	
?>
<!DOCTYPE html>
<link rel="stylesheet" href="stylee.css">
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
	<h1>Uudiste muutmine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr><form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    <label for="news_title_input">Uudise pealkiri</label>
    <br>
    <input hidden type="text" id="photo_id_input" name="photo_id_input" value='<?php echo $picture_id_from_db; ?>'>
    <input hidden type="text" id="news_id_input" name="news_id_input" value='<?php echo $news_id; ?>'>
    <input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $title_old; ?>">
    <br>
    <label for="news_content_input">Uudise tekst</label>
    <br>
    <textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $content_old; ?></textarea>
    <br>
    <label for="news_author_input">Uudise lisaja nimi</label>
    <input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $author_old; ?>">
    <br>
    <?php echo $existing_pic; ?>
    <br>
    <label for="file_input">Lisa pilt!</label>
    <input id="file_input" name="file_input" type="file">
    <br>
    <br>
    <label for="alt_input">Pildi selgitus</label>
    <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on" value="<?php echo $picture_alttext_from_db; ?>">
    <br>
    <br>
    <input type="submit" name="news_submit" value="Salvesta uuendatud uudis">
</form>
</body>
</html>
