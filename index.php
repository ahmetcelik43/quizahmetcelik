<?php
include "db.php";
include "function.php";
$jsonArray = array(); // array değişkenimiz bunu en alta json objesine çevireceğiz. 
$jsonArray["hata"] = FALSE; // Başlangıçta hata yok olarak kabul edelim. 
$_code = 200; // HTTP Ok olarak durumu kabul edelim. 
//https://fast-temple-97418.herokuapp.com
    // üye ekleme kısmı burada olacak. CREATE İşlemi 
 if($_SERVER['REQUEST_METHOD'] == "POST") {
	 echo(124);
	$gelen_veri = json_decode(file_get_contents("php://input")); // veriyi alıp diziye atadık.
    
	 //$kullaniciAdi = ($_POST["kullaniciAdi"]);
    //$adSoyad =($_POST["adSoyad"]);
    //$sifre = ($_POST["sifre"]);
    //$posta = ($_POST["posta"]);
    //$telefon = addslashes($_POST["telefon"]);
    
    // Kontrollerimizi yapalım.
    // gelen kullanıcı adı veya e-posta veri tabanında kayıtlı mı kontrol edelim. 
   
    if(!isset($gelen_veri->posta) || empty($gelen_veri->posta) || !isset($gelen_veri->sifre) ||  empty($gelen_veri->sifre) ) {
    	$_code = 400; 
		$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Boş Alan Bırakmayınız."; // Hatanın neden kaynaklı olduğu belirtilsin.
	}
	else if(!filter_var($gelen_veri->posta,FILTER_VALIDATE_EMAIL)) {
    	$_code = 400;
        $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Geçersiz E-Posta Adresi"; // Hatanın neden kaynaklı olduğu belirtilsin. 
	}

   
	else if($db->query("SELECT * from uyeler WHERE  posta='$gelen_veri->posta'")->rowCount() !=0)
	 {
    	$_code = 400;
        $jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
        $jsonArray["hataMesaj"] = "Kullanıcı Adı Veya E-Posta Alınmış."; 
	}
	else
	 {
    
	$rolid = $db->query("SELECT id from roller WHERE rol='kullanici'")->fetchColumn();
	if(empty($rolid))
	{
		$exrol = $db->prepare("insert into roller(rol) values('kullanici')");
		   //$jsonArray["hata"]=$rol->fetchAll()->rolid;
		$ekleRol = $exrol->execute();
		
		$stmt = $db->query("SELECT LAST_INSERT_ID()");
		$rolid = $stmt->fetchColumn();
	}
			$ex = $db->prepare("insert into uyeler(kullaniciAdi,sifre,posta,rolID) values(:kadi, 
			 :pass, 
			 :mail,
		     :rolID)");
			$ekle2 = $ex->execute(array(
			"kadi" => "user".uniqid(),
			"pass" => $gelen_veri->sifre,
			"mail" => $gelen_veri->posta,
		    "rolID"=> $rolid
		));
		if($ekle2) {
			$_code = 201;
			$jsonArray["mesaj"] = "Başarılıyle eklendi";
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
} else if($_SERVER['REQUEST_METHOD'] == "GET") {

	
    // üye bilgisi listeleme burada olacak. GET işlemi 
    if(isset($_GET["posta"]) && !empty(trim($_GET["posta"])) && isset($_GET["sifre"]) && !empty(trim($_GET["sifre"]))) {
		//$user_id = intval($_GET["user_id"]);
		$posta = $_GET["posta"];
		$sifre = $_GET["sifre"];
		$query = $db->query("select kullaniciAdi from uyeler where posta='$posta' and  sifre='$sifre'");
		
		if($query->rowCount()) {
			
			//$bilgiler = $db->query("select * from  uyeler where id='$user_id'")->fetch(PDO::FETCH_ASSOC);
			$bilgiler = $query->fetch(PDO::FETCH_ASSOC);
			$jsonArray["uye-bilgileri"] = $bilgiler;
			$jsonArray["durum"] = "Giriş başarılı";

			$_code = 200;
			
		}else {
			$_code = 400;
			$jsonArray["hata"] = TRUE; // bir hata olduğu bildirilsin.
    		$jsonArray["hataMesaj"] = "Üye bulunamadı"; // Hatanın neden kaynaklı olduğu belirtilsin.
		}
	}
	else
	{
		$_code = 400;
		$jsonArray["hata"] = TRUE;
		 $jsonArray["hataMesaj"] = "Kullanıcı bilgileri girilmedi !";
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
