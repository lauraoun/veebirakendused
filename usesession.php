<?php
  require("classes/SessionManager.class.php");
  SessionManager::sessionStart("vr", 0, "/~andrus.rinde/", "tigu.hk.tlu.ee");
  
  //kas on sisse logitud?
  if(!isset($_SESSION["user_id"])){
	//jõuga suunatakse sisselogimise lehele
	header("Location: page.php");
	exit();
  }
  
  //väljalogimine
  if(isset($_GET["logout"])){
	//sessiooni lõpetamine
	session_destroy();
	//jõuga suunatakse sisselogimise lehele
	header("Location: page.php");
	exit();
  }