<?php
$host = "remotemysql.com";
$user = "zjx0BZjenx";
$pass = "4MyRmrY0lR";
$db = "zjx0BZjenx";

try {
//	  $dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";


	$db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
	//$db = new PDO($dsn);
}catch(PDOException $e) {
	echo $e->getMessage();
}




?>
