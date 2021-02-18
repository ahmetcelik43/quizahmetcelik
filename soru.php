
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
 if($_SERVER['REQUEST_METHOD'] == "POST") {
	 
	$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
    
	 //$kullaniciAdi = ($_POST["kullaniciAdi"]);
    //$adSoyad =($_POST["adSoyad"]);
    //$sifre = ($_POST["sifre"]);
    //$posta = ($_POST["posta"]);
    //$telefon = addslashes($_POST["telefon"]);
    
    // Kontrollerimizi yapalım.
    // gelen kullanıcı adı veya e-posta veri tabanında kayıtlı mı kontrol edelim. 
     //echo($gelen_veri->kategoriAdi);die();
	 if($gelen_veri->action == "ekle")
	 {
		 
    if(!isset($gelen_veri->soru) || empty($gelen_veri->soru) || !isset($gelen_veri->kategoriID) || empty($gelen_veri->kategoriID)) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; // Hatanın neden kaynaklı olduğu belirtilsin.
	}


   

	else
	 {
            if($db->query("SELECT * from categorys WHERE  id = '$gelen_veri->kategoriID'")->rowCount() ==0)
	    {
		    $_code = 400; 
        $jsonArray["hataMesaj"] = "Kategori Bulunamadı !"; 
	    }
	    else {
		    

			$ex = $db->prepare("insert into sorular(soru,kategoriID,createdAt) values(:soru,:kategoriID,:createdAt)");
			$ekle2 = $ex->execute(array(
            "soru" => $gelen_veri->soru,
            "kategoriID"=>$gelen_veri->kategoriID,
			"createdAt" => date("Y-m-d H:i:s"),
			
		));
		if($ekle2) {
			$_code = 201;
			$jsonArray["mesaj"] = "Eklendi";
		}else {
			$_code = 400;
			 $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
       		 $jsonArray["hataMesaj"] = "Sistem Hatası.";
		}
	    }
    }
}
	 
	 else if($gelen_veri->action == "guncelle")
	 {
		 if(	isset($gelen_veri->id) && 
     		!empty($gelen_veri->id) && isset($gelen_veri->soru) && !empty($gelen_veri->soru && isset($gelen_veri->kategoriID) && !empty($gelen_veri->kategoriID)
     		
     	)) {
     		
			   if($db->query("SELECT * from categorys WHERE  id='$gelen_veri->kategoriID'")->rowCount() == 0)
	    {
		    $_code = 400; 
                    $jsonArray["hataMesaj"] = "Kategori Bulunamadı !"; 
	                      }
			else
			{
				$q = $db->prepare("UPDATE sorular SET soru= :soru , kategoriID = :kategoriID  WHERE id= :id ");
			 	$update = $q->execute(array(
			 			"soru" => trim($gelen_veri->soru),
                         "kategoriID" => $gelen_veri->kategoriID,
                         "id"=>$gelen_veri->id
			 				 	
			 	));
			 	// güncelleme başarılı ise bilgi veriyoruz. 
			 	if($update) {
			 		$_code = 200;
			 		//$jsonArray["mesaj"] = "Güncelleme Başarılı";
			 	}
			 	else {
			 		$_code = 500;
		 			$jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
				}
			}
		
		
		}else {
			$_code = 400;
			$jsonArray["hata"] = TRUE;
	 		$jsonArray["hataMesaj"] = "soru , kategoriId ve id verilerini göndermediniz.";
		}
     }
     else if ($gelen_veri->action == "sil")
     {
        if(isset($gelen_veri->id) && !empty(trim($gelen_veri->id))) {
            $id = intval($gelen_veri->id);
           
                $sil = $db->query("delete from sorular where id='$id'");
                if( $sil ) {
                    $_code = 200;
                    //$jsonArray["mesaj"] = "Silindi.";
                }else {
                    $_code = 400;
                    $jsonArray["hata"] = TRUE;
                     $jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
                }
          
        }else {
            $_code = 400;
            $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
            $jsonArray["hataMesaj"] = "Lütfen id değişkeni gönderin"; // Hatanın neden kaynaklı olduğu belirtilsin.
        }
     }
}
else if($_SERVER['REQUEST_METHOD'] == "GET") {
	
    
	
		$query = $db->query("select s.soru , s.createdAt , c.ad as kategori , s.id as soruID , c.id as kategoriID from sorular s inner join categorys c on s.kategoriID = c.id");
		
		if($query->rowCount()) {
		$bilgiler = $query->fetchAll(PDO::FETCH_ASSOC);
		$jsonArray = $bilgiler;
		$_code = 200;	

	}
  else {
    	$_code = 200;
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
