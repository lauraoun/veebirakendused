<?php
	function read_all_semi_public_photo_thumbs(){
		$privacy = 2;
		$thumbs_dir = "../upload_photos_thumbnail/";
		$finalHTML = "";
		$html = "";
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		
		$stmt = $conn->prepare("SELECT vr21_photos.vr21_photos_id, vr21_photos.vr21_photos_filename, vr21_photos.vr21_photos_alttext, vr21_users.vr21_users_firstname, vr21_users.vr21_users_lastname FROM vr21_photos JOIN vr21_users ON vr21_photos.vr21_photos_userid = vr21_users.vr21_users_id WHERE vr21_photos.vr21_photos_privacy <= ? AND vr21_photos.vr21_photos_deleted IS NULL GROUP BY vr21_photos.vr21_photos_id");
		
		echo $conn->error;
		$stmt->bind_param("i", $privacy);
		$stmt->bind_result($id_from_db, $filename_from_db, $alt_from_db, $firstname_from_db, $lastname_from_db);
		$stmt->execute();
		while($stmt->fetch()){
			$html .= '<div class="thumbgallery">' ."\n";
			$html .= '<img src="' .$thumbs_dir .$filename_from_db .'" alt="'.$alt_from_db .'" class="thumbs" data-fn="' .$filename_from_db .'" data-id="' .$id_from_db .'">' ."\n \t \t";
			$html .= "<p>" .$firstname_from_db ." " .$lastname_from_db ."</p> \n \t \t";
			$html .= "</div> \n \t \t";
		}
		if($html != ""){
			$finalHTML = $html;
		} else {
			$finalHTML = "<p>Kahjuks pilte pole!</p>";
		}
		$stmt->close();
		$conn->close();
		return $finalHTML;
	}
