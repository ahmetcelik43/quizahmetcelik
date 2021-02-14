
<?php
include "db.php";
include "function.php";
$jsonArray = array(); 
$_code = 200; 


    // Access-Control headers are received during OPTIONS requests
  
date_default_timezone_set('Europe/Istanbul'); 
 if($_SERVER['REQUEST_METHOD'] == "GET") {
	
    
	    $gelen_veri = $_SERVER['QUERY_STRING'];
        parse_str($gelen_veri,$output);
        if(isset($output["kategoriID"]) && !empty(trim($output["kategoriID"])))
        {
            $kategoriID = $output['kategoriID'];
            $query = $db->query("select s.id as soruID, s.soru , s.createdAt , c.id as kategoriID , c.ad as kategoriAdi  from sorular s inner join categorys c on c.id=s.kategoriID  where kategoriID='$kategoriID'");
		
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
        else{
            $_code = 400;
            $jsonArray["Mesaj"] = "Kategori id  Bulunamadı !";
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
