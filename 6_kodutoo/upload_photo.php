<?php
	require_once "usesession.php";
	require_once "../../../../conf.php";
	require_once "fnc_general.php";
	require_once "fnc_upload_photo.php";
	require_once "classes/Upload_photo.class.php";
	 
  $photo_upload_error = null;
	$image_file_type = null;
	$image_file_name = null;
	$file_name_prefix = "vr_";
	$file_size_limit = 1 * 1024 * 1024;
	$image_max_w = 600;
	$image_max_h = 400;
	$image_thumbnail_size = 100;
	$notice = null;
	$watermark = "../images/vr_watermark.png";
	
	if(isset($_POST["photo_submit"])){
		//kas üldse on pilt
		$check = getimagesize($_FILES["file_input"]["tmp_name"]);
		if($check !== false){
			//kontrollime, kas aktsepteeritud failivorming ja fikseerime laiendi
			if($check["mime"] == "image/jpeg"){
				$image_file_type = "jpg";
			} elseif ($check["mime"] == "image/png"){
				$image_file_type = "png";
			} else {
				$photo_upload_error = "Pole sobiv formaat! Ainult jpg ja png on lubatud!";
			}
		} else {
			$photo_upload_error = "Tegemist pole pildifailiga!";
		}
		
		if(empty($photo_upload_error)){
			//ega pole liiga suur fail
			if($_FILES["file_input"]["size"] > $file_size_limit){
				$photo_upload_error = "Valitud fail on liiga suur! Lubatud kuni 1MiB!";
			}
			
			if(empty($photo_upload_error)){
				
				//Võtame kasutusele Upload_photo klassi
				$photo_upload = new Upload_photo($_FILES["file_input"], $image_file_type);
				
				
				//loome oma failinime
				$timestamp = microtime(1) * 10000;
				$image_file_name = $file_name_prefix .$timestamp ."." .$image_file_type;
				
				$photo_upload->resize_photo($image_max_w, $image_max_h);
				
				//lisan vesimärgi
				$photo_upload->add_watermark($watermark);
				
				//salvestame pikslikgumi faili
				$target_file = "../upload_photos_normal/" .$image_file_name;
				$result = $photo_upload->save_image_to_file($target_file);
				if($result == 1) {
					$notice = "Vähendatud pilt laeti üles! ";
				} else {
					$photo_upload_error = "Vähendatud pildi salvestamisel tekkis viga!";
				}
				
				//teen pisipildi
				$photo_upload->resize_photo($image_thumbnail_size, $image_thumbnail_size, false);
				
				//salvestame pisipildi faili
				$target_file = "../upload_photos_thumbnail/" .$image_file_name;
				$result = $photo_upload->save_image_to_file($target_file);
				if($result == 1) {
					$notice .= " Pisipilt laeti üles! ";
				} else {
					$photo_upload_error .= " Pisipildi salvestamisel tekkis viga!";
				}
				
				unset($photo_upload);
				
				$target_file = "../upload_photos_orig/" .$image_file_name;
				//if(file_exists($target_file))
				if(move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)){
					$notice .= " Originaalfoto üleslaadimine õnnestus!";
				} else {
					$photo_upload_error .= " Originaalfoto üleslaadimine ebaõnnestus!";
				}
				
			}
			
			//kui kõik hästi, salvestame info andmebaasi!!!
			if($photo_upload_error == null){
				$result = store_photo_data($image_file_name, $_POST["alt_input"], $_POST["privacy_input"], $_FILES["file_input"]["name"]);
				if($result == 1){
					$notice .= " Pildi andmed lisati andmebaasi!";
				} else {
					$photo_upload_error = "Pildi andmete lisamisel andmebaasi tekkis tehniline tõrge: " .$result;
				}
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
	<h1>Fotode üleslaadimine</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><a href="?logout=1">Logi välja</a></p>
	<p><a href="home.php">Avalehele</a></p>
	<hr>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<label for="file_input">Vali foto fail! </label>
		<input id="file_input" name="file_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
		<input id="alt_input" name="alt_input" type="text" placeholder="Pildil on ...">
		<br>
		<label>Privaatsustase: </label>
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
		<label for="privacy_input_1">Privaatne</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2">
		<label for="privacy_input_2">Registreeritud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="1">
		<label for="privacy_input_3">Avalik</label>
		<br>
		<input type="submit" name="photo_submit" value="Lae pilt üles!">
	</form>
	<p><?php echo $photo_upload_error; echo $notice; ?></p>
</body>
</html>