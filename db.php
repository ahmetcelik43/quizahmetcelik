<?php
$host = "ec2-52-50-171-4.eu-west-1.compute.amazonaws.com";
$user = "jcqfmvskaxnsff";
$pass = "5f916f074b1b63a7b4a91e469aa25ef14aaeaa028ec52e101e83baf02813de02";
$db = "d9cq1i52lr8iaf";

try {
	  $dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";


	//$db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
	$db = new PDO($dsn);
}catch(PDOException $e) {
	echo $e->getMessage();
}




?>
