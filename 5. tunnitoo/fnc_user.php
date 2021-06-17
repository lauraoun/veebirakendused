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