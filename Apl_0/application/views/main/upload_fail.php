<?php
$username=$_SESSION[APP_NAME]["username"];

// $upload_dir = "application/views/main/capture/";
// echo $img = $_POST['hidden_data'];                 
// $img = str_replace('data:image/png;base64,', '', $img);
// $img = str_replace(' ', '+', $img);
// $data = base64_decode($img);
// $file = $upload_dir . date('ymdhis') . ".png";
// $nama = date('ymdhis') . ".png";
// $success = file_put_contents($file, $data);
// print $success ? $file : 'Unable to save the file.';


$upload_dir = "application/views/main/capture/";
$img = $_POST['hidden_data'];                 
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$file = $upload_dir . date('ymdhis') . ".png";
$nama = date('ymdhis') . ".png";
$success = file_put_contents($file, $data);


$idref         			= (isset($_POST['idref'])? $_POST['idref'] : '');
$ccp         			= (isset($_POST['ccp'])? $_POST['ccp'] : '');
$seq        			= (isset($_POST['seq'])? $_POST['seq'] : '');
$temuan       			= (isset($_POST['tem'])? $_POST['tem'] : '');
$nopol       			= (isset($_POST['nopol'])? $_POST['nopol'] : '');
$petugas       			= (isset($_POST['petugas'])? $_POST['petugas'] : '');
$lokasi       			= (isset($_POST['lokasi'])? $_POST['lokasi'] : '');
$item_utama       		= (isset($_POST['item_utama'])? $_POST['item_utama'] : '');




mysqli_query($con,"UPDATE tb_ceklist SET seq_foto=1 where idref='$idref'");
mysqli_query($con,"INSERT INTO tb_foto SET idref='$idref',username='$username',utama='$ccp',foto_name='$nama', description='$temuan', nopol='$nopol', petugas='$petugas', lokasi='$lokasi', item_utama='$item_utama' ");




?>