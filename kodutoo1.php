<?php
    $myname = "LAURA ÕUN";
    $currenttime = date("d.m.Y H:i:s"); // hetke kuupäev ja kellaaeg muutujasse currenttime
    $timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</p>";    // teeme html osa jaoks vormindatud muutuja kellaaja ja kuupäevaga
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-06-30");     // semestri aluse ja lõpuaeg muutujasse
    $semesterduration = $semesterbegin->diff($semesterend); // semestri kestvus kasutades diff funktsiooni alguse ja lõpuaja võrdlemiseks
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurhtml = "\n <p>2021 kevadsemester kestus on " .$semesterdurationdays ." päeva.</p> \n";
    $today = new DateTime("now");
    $fromsemesterbegin = $semesterbegin->diff($today);
    $fromsemesterbegindays = $fromsemesterbegin->format("%r%a");
 //SEMESTRI KULGEMISE KONTROLL//
    if ($fromsemesterbegindays < 0) { // Kõigepealt kontrollime kas semester on alanud - kui on, siis  järgmine samm elseif
		$semesterprogress = "\n <p>Semester pole veel alanud.</p> \n";//kui ei, siis väljastame vastava teate
	}
	elseif ($fromsemesterbegindays <= $semesterdurationdays) {// võrdleme kas ajavahemik on vahemikus
		$semesterprogress = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'"></meter>.</p>' ."\n";	// ajavahemik on lubatud piires, seega semester kestab ja vormindame HTML muutuja mis näitab semetri kulgu
	}
	else {
		$semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
        }
    
   //NÄDALAPÄEVA LISAMINE EESTI KEELES//
   
   setlocale(LC_TIME, 'et_EE.utf8');//utf-8 ka PHP koodi lisades näitab ka täpitähti
   $date =  strftime('%A.');// A annab hetke nädalapäeva nimetuse
   echo "\n <h1> Täna on " .$date ."</h1>";
    //TEINE VARIANT//
    //$weekday_nr=date('w'); // PHP funktsioon või nädalapäevade numbriline definitsioon. date('w') on leitav PHP manuaalis
//$day_names=['pühapäev','esmaspäev','teisipäev','kolmapäev','neljapäev','reede','laupäev'];
//$todaysweekdayhtml="<p> Täna on ". $day_names[$weekday_nr].". Andmed massiivist.</p>";


    //loeme piltide kataloogi sisu
    $picsdir = "../pics/";
    $allfiles = array_slice(scandir($picsdir), 2);
	//echo $allfiles[5];
	//var_dump($allfiles);
	$allowedphototypes = ["image/jpeg", "image/png"];
	$picfiles = [];
	
	//for($x = 0; $x <10;$++){
		//tegevus
	// Kontrollime, et leht näitaks ainult jpeg ja png faile. Selleks kontrollid IGA faili sisu (foreach), mille command on getimagesize(). 
	foreach ($allfiles as $file) {
		$fileinfo = getimagesize($picsdir .$file); //kasuta getimagesize, et kontrollida, et kas tegu on pildiga. Faililaiendi kontrollimine ei tööta sest lambi fail võib lõppeda .jpg nimega.
		// var_dump($fileinfo);
		if(isset($fileinfo["mime"])) { //kui "mime" sisaldub faili infos, mis on piltide sees olemas. Mp3s nt pole.
			if(in_array($fileinfo["mime"], $allowedphototypes)) {//kontrolli, kas "mime" info sialdub lubatud pilditüüpides
				array_push($picfiles, $file); //kui jah, siis lisa meie piltide listi
			}
		}
	}

	$photo_count = count($picfiles);
	$photo_num = mt_rand(0, $photo_count-1);
	$random_photo = $picfiles[$photo_num]; //mt_rand peaks olema kiirem kui rand
    
   
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
    <h1>
    <?php
    
        echo $semesterdurhtml;
        echo $semesterprogress;
        ?>
        

	<h1>LAURA ÕUN</h1>
    <?=$currenttime ?>
    <?=$timehtml ?>

   <!--SEE SIIN ON 3 RANDOM PILTI-->
    <?php $random_photo = array_rand($picfiles,3);?><!-- // see funktsioon võtab mssivist juhulikul teel 3 elementi ja paneb nende võtmeväärtused randomfoto massiivi-->
    <img src="<?php echo $picsdir .$picfiles[$random_photo[0]]; ?>" alt="Vaade Haapsalus">
    <img  src="<?php echo $picsdir .$picfiles[$random_photo[1]];  ?>" alt="Vaade Haapsalus2">
    <img  src="<?php echo $picsdir .$picfiles[$random_photo[2]]; ; ?>" alt="Vaade Haapsalus3">
   
    <p>See leht on valminud õppetöö raames!</p>
</body>
</html>