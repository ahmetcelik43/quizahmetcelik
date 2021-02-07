<?php
$host = "5.180.185.125";
$user = "xlivacom_ahmet";
$pass = "r=np?~OxV7-F";
$db = "xlivacom_quiz";

try {
	$db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
}catch(PDOException $e) {
	echo $e->getMessage();
}




?>
