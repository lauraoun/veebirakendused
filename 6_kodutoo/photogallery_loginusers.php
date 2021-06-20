<?php
	require_once "usesession.php";
	require_once "../../../../conf.php";
	require_once "fnc_gallery.php";
	
	//$gallery = readAllSemiPublicPictureThumbsPage($page, $limit);
	$gallery = read_all_semi_public_photo_thumbs();
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="stylesheet" type="text/css" href="style/gallery.css">
	<link rel="stylesheet" type="text/css" href="style/modal.css">
	<script src="javascript/modal.js" defer></script>
</head>
<body>
  <!--Modaalaken fotogalerii jaoks-->
  <div id="modalarea" class="modalarea">
	<!--sulgemisnupp-->
	<span id="modalclose" class="modalclose">&times;</span>
	<!--pildikoht-->
	<div class="modalhorizontal">
		<div class="modalvertical">
			<p id="modalcaption"></p>
			<img id="modalimg" src="empty.png" alt="galeriipilt">

			<br>
			<div id="rating" class="modalRating">
				<label><input id="rate1" name="rating" type="radio" value="1">1</label>
				<label><input id="rate2" name="rating" type="radio" value="2">2</label>
				<label><input id="rate3" name="rating" type="radio" value="3">3</label>
				<label><input id="rate4" name="rating" type="radio" value="4">4</label>
				<label><input id="rate5" name="rating" type="radio" value="5">5</label>
				<button id="storeRating">Salvesta hinnang!</button>
				<br>
				<p id="avgRating"></p>
			</div>
		</div>
	</div>
  </div>
	<h1>Fotogalerii</h1>
	<p>See leht on valminud õppetöö raames!</p>
	<hr>
	<p><a href="?logout=1">Logi välja</a></p>
	<p><a href="home.php">Avalehele</a></p>
    <a href="show_news.php">Uudised</a></p>
    <a href="upload_photo.php">Lisa pilt</a></p>
	<a href="add_news.php">Lisa uudis</a></p>
	<a href="photogallery_loginusers.php">Galerii</a></li>

	<div class="gallery" id="gallery">
		<?php echo $gallery; ?>
	</div>
</body>
</html>