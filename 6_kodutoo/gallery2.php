<?php

require_once "../../../../conf.php";
require_once "fnc_upload_photo.php";

$pictures_to_html = gallery_pics();
?>

<!DOCTYPE html>
<html lang="et">
<head>
 <!-- vajalikud meta andmed -->
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Limelight&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet" >  
    <link rel="stylesheet" href="style.css">
    <title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
<div class="container">
	<h1>Galerii</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
    <div class="gallery">
    <?php echo $pictures_to_html; ?>
	</div>
    <div class="btn">
        <a href="page.php">Avalehele</a>
    </div>
    </div>
</body>
</html>