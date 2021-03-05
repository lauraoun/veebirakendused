<?php
    $myname = "Laura Õun";
    $currenttime = date("d.m.Y H:i:s");
    $timehtml = "\n <p>Lehe avamise hetkel oli: " .$currenttime .".</p>";
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-06-30");
    $semesterduration = $semesterbegin->diff($semesterend);
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurhtml = "\n <p>2021 kevadsemester kestus on " .$semesterdurationdays ." päeva.<\p> \n";
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
        ?>
	<h1>Laura Õun</h1>
    <?=$currenttime ?>
	<p>See leht on valminud õppetöö raames!</p>
    <?=$timehtml ?>
</body>
</html>