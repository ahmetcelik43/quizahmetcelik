
<?php
include "db.php";
include "function.php";
$jsonArray = array(); // array değişkenimiz bunu en alta json objesine çevireceğiz. 
$jsonArray["hata"] = FALSE; // Başlangıçta hata yok olarak kabul edelim. 
$_code = 200; // HTTP Ok olarak durumu kabul edelim. 
//https://fast-temple-97418.herokuapp.com
    // üye ekleme kısmı burada olacak. CREATE İşlemi 
//register
 if($_SERVER['REQUEST_METHOD'] == "POST") {
	 
	$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
    
	 //$kullaniciAdi = ($_POST["kullaniciAdi"]);
    //$adSoyad =($_POST["adSoyad"]);
    //$sifre = ($_POST["sifre"]);
    //$posta = ($_POST["posta"]);
    //$telefon = addslashes($_POST["telefon"]);
    
    // Kontrollerimizi yapalım.
    // gelen kullanıcı adı veya e-posta veri tabanında kayıtlı mı kontrol edelim. 
   
    if(!isset($gelen_veri->kategoriAdi) || empty($gelen_veri->kategoriAdi)) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; // Hatanın neden kaynaklı olduğu belirtilsin.
	}


   
	else if($db->query("SELECT * from uyeler WHERE  posta='$gelen_veri->kategoriAdi'")->rowCount() !=0)
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
			"createdAt" => date("d-m-Y"),
			
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
else if($_SERVER['REQUEST_METHOD'] == "PUT") {
     $gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
    	
    	// basitçe bi kontrol yaptık veriler varmı yokmu diye 
     if(	isset($gelen_veri->kullanici_adi) && 
     		isset($gelen_veri->ad_soyad) && 
     		isset($gelen_veri->posta) && 
     		isset($gelen_veri->user_id) && 
     		isset($gelen_veri->telefon)
     	) {
     		
     		// veriler var ise güncelleme yapıyoruz.
				$q = $db->prepare("UPDATE uyeler SET kullaniciAdi= :kadi, adSoyad= :ad_soyad, posta= :posta, telefon= :telefon WHERE id= :user_id ");
			 	$update = $q->execute(array(
			 			"kadi" => $gelen_veri->kullanici_adi,
			 			"ad_soyad" => $gelen_veri->ad_soyad,
			 			"posta" => $gelen_veri->posta,
			 			"telefon" => $gelen_veri->telefon,
			 			"user_id" => $gelen_veri->user_id	 	
			 	));
			 	// güncelleme başarılı ise bilgi veriyoruz. 
			 	if($update) {
			 		$_code = 200;
			 		$jsonArray["mesaj"] = "Güncelleme Başarılı";
			 	}
			 	else {
			 		// güncelleme başarısız ise bilgi veriyoruz. 
			 		$_code = 400;
					$jsonArray["hata"] = TRUE;
		 			$jsonArray["hataMesaj"] = "Sistemsel Bir Hata Oluştu";
				}
		}else {
			// gerekli veriler eksik gelirse apiyi kulanacaklara hangi bilgileri istediğimizi bildirdik. 
			$_code = 400;
			$jsonArray["hata"] = TRUE;
	 		$jsonArray["hataMesaj"] = "kullanici_adi,ad_soyad,posta,telefon,user_id Verilerini json olarak göndermediniz.";
		}
} else if($_SERVER['REQUEST_METHOD'] == "DELETE") {

    // üye silme işlemi burada olacak. DELETE işlemi 
    if(isset($_GET["user_id"]) && !empty(trim($_GET["user_id"]))) {
		$user_id = intval($_GET["user_id"]);
		$userVarMi = $db->query("select * from uyeler where id='$user_id'")->rowCount();
		if($userVarMi) {
			
			$sil = $db->query("delete from uyeler where id='$user_id'");
			if( $sil ) {
				$_code = 200;
				$jsonArray["mesaj"] = "Üyelik Silindi.";
			}else {
				// silme başarısız ise bilgi veriyoruz. 
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
    	$jsonArray["hataMesaj"] = "Lütfen user_id değişkeni gönderin"; // Hatanın neden kaynaklı olduğu belirtilsin.
	}
//login
} else if($_SERVER['REQUEST_METHOD'] == "GET") {
	
	//$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
        //$gelen_veri = $_SERVER['QUERY_STRING'];
	  //parse_str($gelen_veri,$output);
    // üye bilgisi listeleme burada olacak. GET işlemi 
   
	
		$query = $db->query("select * from categorys");
		
		if($query->rowCount()) {
			
		$bilgiler = $query->fetch(PDO::FETCH_ASSOC);
		$jsonArray["kategoriler"] = $bilgiler;
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
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
?>
