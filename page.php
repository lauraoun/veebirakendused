<?php
    $myname = "LAURA ÕUN";
    $currenttime = date("d.m.Y H:i:s");
    $timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</p>";
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-06-30");
    $semesterduration = $semesterbegin->diff($semesterend);
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurhtml = "\n <p>2021 kevadsemester kestus on " .$semesterdurationdays ." päeva.</p> \n";
    $today = new DateTime("now");
    $fromsemesterbegin = $semesterbegin->diff($today);
    $fromsemesterbegindays = $fromsemesterbegin->format("%r%a");

    if($fromsemesterbegindays <= $semesterdurationdays){$semesterprogress = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'">.</p>' ."\n";}
    else {
        $semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
    }

    //loeme piltide kataloogi sisu
    $picsdir = "../../pics/";
    $allfiles = array_slice(scandir($picsdir), 2);
    foreach($allfiles as $file) {
        $fileinfo = getimagesize($picsdir .$file);
        // var_dump($fileinfo); edastab kogu info
        if(isset($fileinfo[“mime”])) {
            if(in_array($fileinfo[“mime”], $allowedphototypes)) {
                array_push($picfiles, $file);
            }
        }
    }
    //echo $allfiles[0];


    $photocount = count($allfiles);
    $photonum = mt_rand(0, $photocount-1);
    $randomphoto = $allfiles[$photonum];
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
        echo $myname;
        echo $semesterdurhtml;
        echo $semesterprogress;
        ?>
	<h1>LAURA ÕUN</h1>
    <?=$currenttime ?>
	<p>See leht on valminud õppetöö raames!</p>
    <?=$timehtml ?>
    <img src="<?php echo $picsdir .$randomphoto; ?>" alt="vaade Haapsalus">
</body>
</html>