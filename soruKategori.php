
<?php
include "db.php";
include "function.php";
$jsonArray = array(); // array değişkenimiz bunu en alta json objesine çevireceğiz. 
//$jsonArray["hata"] = FALSE; // Başlangıçta hata yok olarak kabul edelim. 
$_code = 200; // HTTP Ok olarak durumu kabul edelim. 
//https://fast-temple-97418.herokuapp.com
    // üye ekleme kısmı burada olacak. CREATE İşlemi 
//register
  

    // Access-Control headers are received during OPTIONS requests
  
date_default_timezone_set('Europe/Istanbul'); 
 if($_SERVER['REQUEST_METHOD'] == "GET") {
	
    
	
		$query = $db->query("select * from sorular ");
		
		if($query->rowCount()) {
		$bilgiler = $query->fetchAll(PDO::FETCH_ASSOC);
		$jsonArray = $bilgiler;
		$_code = 200;	

	}
  else {
    	$_code = 400;
 	$jsonArray["Mesaj"] = "Sorular Bulunamadı !";


}
}
else {
	$_code = 406;
	$jsonArray["hata"] = TRUE;
 	$jsonArray["hataMesaj"] = "Geçersiz method!";
}


SetHeader($_code);
//$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
?>
