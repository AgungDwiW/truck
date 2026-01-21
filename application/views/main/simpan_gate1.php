<?php
include 'application/assets/class.phpmailer.php';
include 'application/assets/Table.php';
include 'application/assets/TableASN.php';

$idref      		= (isset($_POST['idref'])? $_POST['idref'] : '');
$nopol      		= (isset($_POST['nopol'])? $_POST['nopol'] : '');
$driver				= (isset($_POST['driver'])? $_POST['driver'] : '');
$supplier			= (isset($_POST['supplier'])? $_POST['supplier'] : '');
$lokasi				= (isset($_POST['lokasi'])? $_POST['lokasi'] : '');
$petugas_pemeriksa	= $_SESSION[APP_NAME]["username"];



function sendEmail($mailto,$subject,$msgcontent){
	$mail = new PHPMailer; 
	$mail->IsSMTP();
	$mail->SMTPAuth = false;
	$mail->Host = "mail.nead.danet";
	$mail->SMTPDebug = 2;
	$mail->Port = 25;
	//$mail->SetFrom("no-reply@adop.admin","ADOP No-Reply");
	$mail->SetFrom("achmad.afandi@danone.com","ADOP No-Reply");
	$mail->Subject = $subject;
	$mail->MsgHTML("$msgcontent <br><br> Please follow on bellow link.<br>
		<a href='http://10.203.121.73:83/truck/temuan.php'>http://10.203.121.73:83/truck</a><br><br> Sekian<br>Trimakasih  ");
	
	foreach ($mailto as $key) {	
		$mail->AddAddress($key);
	}
	$mail->Send();
}



























# Iki nek lebih dari satu email to
//$to=array("dyah.wardhana@danone.com"=>"wardhady","achmad.afandi@danone.com"=>"afandiac","ach.afandi26@gmail.com"=>"achmadaf");
//$to=array("pp.wardhana@gmail.com","achmad.afandi@danone.com","ach.afandi26@gmail.com");
//$tooo=array("achmad.afandi@danone.com","prihadi.eko@danone.com");



//$to = "";

$plant_id= $_SESSION[APP_NAME]["plant_id"];

$sql=mysqli_query($con, "SELECT region from tbm_plant where plant_id='$plant_id' " );
while ($row_region=mysqli_fetch_assoc($sql)) {
  $region=$row_region["region"];

} 


//$str = "SELECT email FROM `dbtruck`.tbm_email_to WHERE plant_id='$plant_id' or plant_id='9000'    ";

//$str=" SELECT email FROM (SELECT email, active FROM `dbtruck`.tbm_email_to WHERE plant_id='$plant_id' or plant_id='9000') AS a where active=1 ";

$str=" SELECT email FROM (SELECT * FROM (SELECT email, active, region FROM `dbtruck`.tbm_email_to WHERE plant_id=$plant_id or plant_id='9000') AS a WHERE region='$region' OR region=0) AS b WHERE active=1 ";


        $result = mysqli_query($con, $str);
        while ($row = mysqli_fetch_assoc($result)) {
        	$to[]= $row["email"];
        }
		//$to = rtrim($to, ", ");




//echo "<pre>";print_r($to);echo "</pre>";
//echo "<pre>";print_r($tooo);echo "</pre>";

//exit;


# Iki nek cuma satu email
//$to["ach.afandi26@gmail.com"]="afandiac_atau_terserah";

$hasil         		= (isset($_POST['hasil'])? $_POST['hasil'] : '');
$komentar        	= (isset($_POST['komentar'])? $_POST['komentar'] : '');
$tindakan      		= (isset($_POST['tindakan'])? $_POST['tindakan'] : '');
$idref      		= (isset($_POST['idref'])? $_POST['idref'] : '');
$kode_kirim      	= (isset($_POST['kode_kirim'])? $_POST['kode_kirim'] : '');

$message="Truck Anda Ditolak dengan ID Ref"; 
$message.=' '.$idref;
$message.="<br> Nopol 				:".' '.strtoupper($nopol);
$message.="<br> Petugas Pemeriksa 	:".' '.strtoupper($petugas_pemeriksa);
$message.="<br> Ekspedisi 			:".' '.strtoupper($supplier);
$message.="<br> Ditolak dari Plant 	:".' '.strtoupper($lokasi);
$message.="<br> Kerusakan        	:".' '.strtoupper($komentar);



if ($hasil=='Di Tolak di Pos 1') { sendEmail($to,"Truck Anda Di Tolak",$message); }

$kode_str = ", kode_kirim='{$kode_kirim}'";
if ($kode_kirim == ''){
	$kode_str = "";
}
$str = "UPDATE tb_ceklist SET hasil_pemeriksaan='$hasil', komentar_kerusakan='$komentar', tindakan_perbaikan='$tindakan' {$kode_str} where idref='$idref'";
// echo "<pre>";
// print_r($str);
// echo "</pre>";

mysqli_query($con,$str);
if (mysqli_error($con)){
	echo "<pre>";
	print_r(mysqli_error($con));
	echo "</pre>";
	exit();
}
// exit();
if ($kode_kirim != ''){
	// $debug = 1;
	$condition = "pengiriman_id='$kode_kirim'";

	$table = new Table("tbl_item_pengiriman", $supplier);
	$item = $table->get()->where($condition)->fetchOne();
	
	// if ($item['keterangan'] == 'CREATE'){
	// 	echo "<h4 style = \"color : red !important\">Nomor pengiriman {$kode_kirim} belum tersinkron, mohon tunggu beberapa menit lalu coba lagi</h4>";
	// 	exit();
	// }
	
	$condition 	= "kode_pengiriman='$kode_kirim'";
	$table 		= new Table("tbl_pengiriman");
	$supplier 	=  $table->get("supplier_id")->where($condition)->fetchOne();
	$supplier 	= $supplier['supplier_id'];

	global $controller;
	$controller = 'truck';
	// $debug = 1;

	if ($hasil=="Layak di Operasikan (Stiker Hijau)" or $hasil=="Lanjut Pemeriksaan Gate 2") {
		$status  = 'PASS GATEIN';
	}

	else{
		$status  = 'NOT PASS GATEIN';
	}



	$updateData = array(
		"status" => $status,
		"keterangan" => $komentar,
		"security_gate_in" => $petugas_pemeriksa,
		"tgl_gate_in" => date("Y-m-d H:i:s")
	);

	$condition = "kode_pengiriman='$kode_kirim'";

	$table = new TableASN("tbl_pengiriman", $supplier);
	$table->update($updateData)->where($condition)->execute();

	$updateData = array(
		"keterangan" => $status
	);
	$condition = "pengiriman_id='$kode_kirim'";

	$table = new TableASN("tbl_item_pengiriman", $supplier);
	$table->update($updateData)->where($condition)->execute();
}

header("location:main?action=index");
// exit();


// if ($hasil=="Layak di Operasikan (Stiker Hijau)" or $hasil=="Lanjut Pemeriksaan Gate 2") {
// 	$status  = 'PASS GATEIN'
// 	$query2="UPDATE tbl_pengiriman SET status='PASS GATEIN', keterangan='$komentar', security_gate_in='$petugas_pemeriksa', tgl_gate_in=NOW() where kode_pengiriman='$kode_kirim';";


// 	$query2.="UPDATE tbl_item_pengiriman SET `keterangan`='PASS GATEIN' WHERE pengiriman_id='$kode_kirim';";

// 	mysqli_multi_query($con2,$query2);
// }


// if ($hasil=="Tidak Layak di Operasikan (Stiker Merah)") {
// 	$query2="UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', keterangan='$komentar' , security_gate_in='$petugas_pemeriksa', tgl_gate_in='$datetime' where kode_pengiriman='$kode_kirim' ";
// 	$query2.="UPDATE tbl_item_pengiriman SET `keterangan`='NOT PASS GATEIN' WHERE pengiriman_id='$kode_kirim';";
// 	mysqli_multi_query($con2,$query2);
// }

// if ($hasil=="Perlu Perbaikan (Stiker Kuning)") {
// 	$query2="UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', keterangan='$komentar', security_gate_in='$petugas_pemeriksa', tgl_gate_in=NOW()  where kode_pengiriman='$kode_kirim' ";
// 	$query2.="UPDATE tbl_item_pengiriman SET `keterangan`='NOT PASS GATEIN' WHERE pengiriman_id='$kode_kirim';";
// 	mysqli_multi_query($con2,$query2);
// }

// if ($hasil=="Di Tolak di Pos 1") {
// 	$query2="UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', keterangan='$komentar', security_gate_in='$petugas_pemeriksa', tgl_gate_in=NOW() where kode_pengiriman='$kode_kirim' ";
// 	$query2.="UPDATE tbl_item_pengiriman SET `keterangan`='NOT PASS GATEIN' WHERE pengiriman_id='$kode_kirim';";
// 	mysqli_multi_query($con2,$query2);
// }


?>