<?php
	
	require("classes/SessionManager.class.php");
	SessionManager::sessionStart("vr", 0, "/~laura.oun/", "tigu.hk.tlu.ee");
	require_once "../../../../conf.php";
	require_once "fnc_user.php";


	//klassi näide
	require_once "classes/Test.class.php";
	$test_object = new Test(5);
	echo " Avalik number on ".$test_object->non_secret .". ";
	$test_object->reveal();
	unset($test_object);
	//echo " Avalik number on ".$test_object->non_secret .". ";


    $myname = "Laura Õun";
    $currenttime = date("d.m.Y H:i:s"); // paneme hetke kuupäeva ja kellaaja muutujasse currenttime
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
    
        //sissselogimine

	
   //NÄDALAPÄEVA LISAMINE EESTI KEELES//
   
   setlocale(LC_TIME, 'et_EE.utf8');//utf-8 ka PHP koodi lisades näitab ka täpitähti
   $date =  strftime('%A.');// A annab hetke nädalapäeva nimetuse
   echo "\n <h1> Täna on " .$date ."</h1>";
  

    //loeme piltide kataloogi sisu
    $picsdir = "../../pics/";
    $allfiles = array_slice(scandir($picsdir), 2);
	$allowedphototypes = ["image/jpeg", "image/png"];
	$picfiles = [];
	
	// Kontrollime, et leht näitaks ainult jpeg ja png faile. Selleks kontrollib iga faili sisu 
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

//sissselogimine

$notice = null;
$email = null;
$email_error = null;
$password_error = null;
if(isset($_POST['login_submit'])){
    //kontrollime kas email ja parool on olemas
    if (verify_user($_POST["email_input"]) == 1){

		$notice = sign_in($_POST["email_input"], $_POST["password_input"]);

	} else {
		$notice = "Sellist kasutajanime pole";
	}
	}
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
</head>
<body> <h2>Logi sisse</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail (kasutajatunnus):</label><br>
		<input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
		<label>Salasõna:</label><br>
		<input name="password_input" type="password"><span><?php echo $password_error; ?></span><br>
		<input name="login_submit" type="submit" value="Logi sisse!"><span><?php echo $notice; ?></span>
	</form>
	<p>Kui kasutajakontot veel ei ole, siis loo see endale <a href="add_user.php">SIIN!</a></p>
    
    <?php
       

        echo $semesterdurhtml;
        echo $semesterprogress;
        ?>
        
	<h1>LAURA ÕUN</h1>
    <?=$currenttime ?>
    <?=$timehtml ?>

    <?php $random_photo = array_rand($picfiles,3);?><!-- // see funktsioon võtab massiivist juhulikul teel 3 elementi ja paneb nende võtmeväärtused randomfoto massiivi-->
    <img width= "250px" src ="<?php echo $picsdir .$picfiles[$random_photo[0]]; ?>" alt="Vaade Haapsalus">
    <img width= "250px" src ="<?php echo $picsdir .$picfiles[$random_photo[1]];  ?>" alt="Vaade Haapsalus2">
    <img width= "250px" src ="<?php echo $picsdir .$picfiles[$random_photo[2]]; ; ?>" alt="Vaade Haapsalus3">
   
    <p>See leht on valminud õppetöö raames!</p>

</body>
</html>