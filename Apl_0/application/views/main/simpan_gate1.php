<?php
include 'application/assets/class.phpmailer.php';

function sendEmail($mailto,$subject,$msgcontent){
	$mail = new PHPMailer; 
	$mail->IsSMTP();
	$mail->SMTPAuth = false;
	$mail->Host = "mail.nead.danet";
	$mail->SMTPDebug = 2;
	$mail->Port = 25;
	$mail->SetFrom("no-reply@adop.admin","ADOP No-Reply");
	$mail->Subject = $subject;
	$mail->MsgHTML("$msgcontent <br> Please follow on bellow link.<br>
		<a href='http://10.203.121.73:83/truck/temuan.php'>http://10.203.121.73:83/truck</a><br><br> Sekian<br>Trimakasih   ");
	//foreach ($mailto as $key => $value) {
	foreach ($mailto as $key) {	
		$mail->AddAddress($key);
	}
	$mail->Send();
}

# Iki nek lebih dari satu email to
//$to=array("dyah.wardhana@danone.com"=>"wardhady","achmad.afandi@danone.com"=>"afandiac","ach.afandi26@gmail.com"=>"achmadaf");
//$to=array("dyah.wardhana@danone.com","achmad.afandi@danone.com");
$to=array("achmad.afandi@danone.com","prihadi.eko@danone.com");
# Iki nek cuma satu email
//$to["achmad.afandi@danone.com"]="afandiac_atau_terserah";

$hasil         		= (isset($_POST['hasil'])? $_POST['hasil'] : '');
$komentar        	= (isset($_POST['komentar'])? $_POST['komentar'] : '');
$tindakan      		= (isset($_POST['tindakan'])? $_POST['tindakan'] : '');
$idref      		= (isset($_POST['idref'])? $_POST['idref'] : '');

$message="Truck Anda Ditolak dengan ID Ref";
$message.=' '.$idref;



if ($hasil=='Di Tolak di Pos 1') { sendEmail($to,"Truck Anda Di Tolak",$message); }

mysqli_query($con,"UPDATE tb_ceklist SET hasil_pemeriksaan='$hasil', komentar_kerusakan='$komentar', tindakan_perbaikan='$tindakan' where idref='$idref'");
header("location:main?action=pilih_gate");
?>