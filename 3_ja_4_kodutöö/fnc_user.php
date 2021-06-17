<?php
	
	function sign_up($name, $surname, $gender, $birth_date, $email, $password){
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn->prepare("INSERT INTO vr21_users (vr21_users_firstname, vr21_users_lastname, vr21_users_birthdate, vr21_users_gender, vr21_users_email, vr21_users_password) VALUES (?,?,?,?,?,?)");
		echo $conn->error;
		//krüpteerime parooli
		//$options = ["cost" => 12, "salt" => substr(sha1(rand()), 0, 22)];
		$options = ["cost" => 12];
		$pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);
		
		$stmt -> bind_param("sssiss", $name, $surname, $birth_date, $gender, $email, $pwd_hash);
		
		if($stmt -> execute()){
			$notice = 1;
		}
		$stmt -> close();
		$conn -> close();
		return $notice;
	}
	
	function sign_in($email, $password){
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn -> prepare("SELECT vr21_users_id, vr21_users_firstname, vr21_users_lastname, vr21_users_password FROM vr21_users WHERE vr21_users_email = ?");
		echo $conn -> error;
		$stmt -> bind_param("s", $email);
		$stmt -> bind_result($id_from_db, $first_name_from_db, $last_name_from_db, $password_from_db);
		$stmt -> execute();
		//kui leiti
		if($stmt -> fetch()){
			//kas parool klapib
			if(password_verify($password, $password_from_db)){
				//olemegi sisse loginud
				$notice = 1;
				$_SESSION["user_id"] = $id_from_db;
				$stmt -> close();
				$conn -> close();
				header("Location: home.php");
				exit();
			}
		}
		
		$stmt -> close();
		$conn -> close();
		return $notice;
	}

	function verify_user($email){
		echo $email;
		$notice = 0;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$stmt = $conn -> prepare("SELECT vr21_users_id FROM vr21_users  WHERE vr21_users_email = ?");
		
		echo $conn -> error;
		$stmt -> bind_param("s", $email);
		$stmt -> execute();
		if($stmt -> fetch()){
		$notice = 1;
		}
		$stmt -> close();
		$conn -> close();
		return $notice;
	}

	//pildi ruutu funktsioon
	function insert_pic_db($pic_name,$pic_orig_name,$alt_text,$pic_privacy){
		//	echo $pic_name,$pic_orig_name,$alt_text,$pic_privacy;
		//	$sql_cmd="INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?)";
		//	echo $sql_cmd;
			$notice = 0;
			$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
			$stmt = $conn->prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?)");
			echo $conn->error;
	
			$stmt -> bind_param("isssi", $_SESSION["user_id"], $pic_name, $pic_orig_name, $alt_text, $pic_privacy);
			
			if($stmt -> execute()){
				$notice = 1;
			}
			$stmt -> close();
			$conn -> close();
			return $notice;
		}
	
		//pildi suuruse muutmine ruutu funktsioon
		function resize_image($temp_image, $image_max_width, $image_max_height, $keep_ratio) {
							
			$image_width = imagesx($temp_image);
			$image_height = imagesy($temp_image);
	
			//kui keep_ratio vastab tõele siis täidab selle osa
			
			if ($keep_ratio){ 
				if($image_width / $image_max_width > $image_height / $image_max_height){
					$image_size_ratio = $image_width / $image_max_width;
				} else {
					$image_size_ratio = $image_height / $image_max_height;
				}
	
				$image_new_width = round($image_width / $image_size_ratio);
				$image_new_height = round($image_height / $image_size_ratio);
	
				$new_temp_image = imagecreatetruecolor($image_new_width, $image_new_height);
				imagecopyresampled($new_temp_image, $temp_image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_width, $image_height);
	//kui keep_ratio ei vasta tõele siis teeb selle osa
			} else {
				if($image_height<$image_width){
		
					$src_x = ($image_width - $image_height) /2;
					$src_width = $image_height;
					$src_y = 0;
					$src_height = $image_height;
				
				} else {
					
					$src_x = 0;
					$src_width = $image_height;
					$src_y = ($image_height - $image_width) /2;
					$src_height = $image_width;
				}
	
				$image_new_width = $image_max_width;
				$image_new_height = $image_max_height;
			
				$new_temp_image = imagecreatetruecolor($image_new_width, $image_new_height);
				imagecopyresampled($new_temp_image, $temp_image, 0, 0, $src_x, $src_y, $image_new_width, $image_new_height, $src_width, $src_height);
			}
	
			return $new_temp_image;
	
		}