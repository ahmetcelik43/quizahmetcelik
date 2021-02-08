<?php
$host = "sql7.freemysqlhosting.net";
$user = "sql7391574";
$pass = "TmJxjaEdAd";
$db = "sql7391574";

try {
//	  $dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";


	$db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
	//$db = new PDO($dsn);
}catch(PDOException $e) {
	echo $e->getMessage();
}




?>
