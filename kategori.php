
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
		 
    if(!isset($gelen_veri->kategoriAdi) || empty($gelen_veri->kategoriAdi)) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; // Hatanın neden kaynaklı olduğu belirtilsin.
	}


   
	else if($db->query("SELECT * from categorys WHERE  ad='$gelen_veri->kategoriAdi'")->rowCount() !=0)
	 {
    	$_code = 400;
        $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Kategori Bulunuyor"; 
	}
	else
	 {
    

			$ex = $db->prepare("insert into categorys(ad,createdAt) values(:ad, 
			 :createdAt)");
			$ekle2 = $ex->execute(array(
			"ad" => $gelen_veri->kategoriAdi,
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
	 
	 else if($gelen_veri->action == "guncelle")
	 {
		 if(	isset($gelen_veri->id) && 
     		!empty($gelen_veri->id) && isset($gelen_veri->ad) && !empty($gelen_veri->ad)
     		
     	) {
     		
     		
				$q = $db->prepare("UPDATE categorys SET ad= :ad  WHERE id= :id ");
			 	$update = $q->execute(array(
			 			"ad" => trim($gelen_veri->ad),
			 			"id" => $gelen_veri->id,
			 				 	
			 	));
			 	// güncelleme başarılı ise bilgi veriyoruz. 
			 	if($update) {
			 		$_code = 200;
			 		//$jsonArray["mesaj"] = "Güncelleme Başarılı";
			 	}
			 	else {
			 		// güncelleme başarısız ise bilgi veriyoruz. 
			 		$_code = 500;
					//$jsonArray["hata"] = TRUE;
		 			$jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
				}
		}else {
			$_code = 400;
			$jsonArray["hata"] = TRUE;
	 		$jsonArray["hataMesaj"] = "Kategori Adi ve id Verilerini json olarak göndermediniz.";
		}
     }
     else if ($gelen_veri->action == "sil")
     {
        if(isset($gelen_veri->id) && !empty(trim($gelen_veri->id))) {
            $id = intval($gelen_veri->id);
            $userVarMi = $db->query("select * from categorys where id='$id'")->rowCount();
            if($userVarMi) {
                
                $sil = $db->query("delete from categorys where id='$id'");
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
                $jsonArray["hataMesaj"] = "Geçersiz id"; // Hatanın neden kaynaklı olduğu belirtilsin.
            }
        }else {
            $_code = 400;
            $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
            $jsonArray["hataMesaj"] = "Lütfen id değişkeni gönderin"; // Hatanın neden kaynaklı olduğu belirtilsin.
        }
     }
}
else if($_SERVER['REQUEST_METHOD'] == "GET") {
	
	//$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
        //$gelen_veri = $_SERVER['QUERY_STRING'];
	  //parse_str($gelen_veri,$output);
    // üye bilgisi listeleme burada olacak. GET işlemi 
   
	
		$query = $db->query("select * from categorys");
		
		if($query->rowCount()) {
		//$jsonArray["kategoriler"] = [];	
		$bilgiler = $query->fetchAll(PDO::FETCH_ASSOC);
		$jsonArray["kategoriler"]  = $bilgiler;
		$_code = 200;	

	}
  else {
    	$_code = 200;
 	$jsonArray["Mesaj"] = "Kategori Bulunamadı !";
	$jsonArray["kategoriler"] = array();


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
