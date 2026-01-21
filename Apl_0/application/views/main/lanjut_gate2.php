<?php
include 'koneksi.php';

$kode	      				= (isset($_POST['kode'])? $_POST['kode'] : '');
$petugas         			= (isset($_POST['petugas'])? $_POST['petugas'] : '');
$tujuan         			= (isset($_POST['tujuan'])? $_POST['tujuan'] : '');
$nopol        				= (isset($_POST['nopol'])? $_POST['nopol'] : '');
$nama_transporter       	= (isset($_POST['nama_transporter'])? $_POST['nama_transporter'] : '');
$nama_sopir        			= (isset($_POST['nama_sopir'])? $_POST['nama_sopir'] : '');
$tipe_truck       			= (isset($_POST['tipe_truck'])? $_POST['tipe_truck'] : '');
$tahun       				= (isset($_POST['tahun'])? $_POST['tahun'] : '');
$tgl       					= (isset($_POST['tgl'])? $_POST['tgl'] : '');
$jam      					= (isset($_POST['jam'])? $_POST['jam'] : '');
$lokasi      				= (isset($_POST['lokasi'])? $_POST['lokasi'] : '');
$komentar     				= (isset($_POST['komentar'])? $_POST['komentar'] : '');
$tindakan      				= (isset($_POST['tindakan'])? $_POST['tindakan'] : '');
$petugas_gate2      		= (isset($_POST['petugas'])? $_POST['petugas'] : '');


				for ($i=1; $i <= 20; $i++) { 
                $utama[$i]=(isset($_POST['utama'.$i.''])? $_POST['utama'.$i.''] : '');
                $tamb[$i]=(isset($_POST['tamb'.$i.''])? $_POST['tamb'.$i.''] : '');
            	}

$hasil_periksa				= (isset($_POST['hasil'])? $_POST['hasil'] : '');

if ($hasil_periksa=="Layak di Operasikan (Stiker Hijau)") {$warna_hasil="green";}
if ($hasil_periksa=="Tidak Layak di Operasikan (Stiker Merah)") {$warna_hasil="red";}
if ($hasil_periksa=="Perlu Perbaikan (Stiker Kuning)") {$warna_hasil="yellow";}
if ($hasil_periksa=="Di Tolak di Pos 1") {$warna_hasil="black";}

if ($hasil_periksa=="") {
echo "<script>alert('Anda belum Ceklist');history.go(-1)</script>";	

}





$query="UPDATE tb_ceklist SET utama6='$utama[6]',utama7='$utama[7]',utama8='$utama[8]',utama9='$utama[9]',utama10='$utama[10]',utama11='$utama[11]',utama12='$utama[12]',utama13='$utama[13]',utama14='$utama[14]',utama15='$utama[15]',utama16='$utama[16]',utama17='$utama[17]',utama18='$utama[18]',utama19='$utama[19]',utama20='$utama[20]',


	tambahan1='$tamb[1]',tambahan2='$tamb[2]',tambahan3='$tamb[3]',tambahan4='$tamb[4]',tambahan5='$tamb[5]',tambahan6='$tamb[6]',tambahan7='$tamb[7]',tambahan8='$tamb[8]',tambahan9='$tamb[9]',tambahan10='$tamb[10]',tambahan11='$tamb[11]',tambahan12='$tamb[12]',tambahan13='$tamb[13]',tambahan14='$tamb[14]',tambahan15='$tamb[15]',tambahan16='$tamb[16]',tambahan17='$tamb[17]',tambahan18='$tamb[18]',tambahan19='$tamb[19]',tambahan20='$tamb[20]',hasil_pemeriksaan='$hasil_periksa',warna='$warna_hasil',tindakan_perbaikan='$tindakan',komentar_kerusakan='$komentar',petugas_gate2='$petugas_gate2',status_gate1='0',list_gate1='0',tujuan_kirim='$tujuan',nopol='$nopol',nama_transporter='$nama_transporter',nama_sopir='$nama_sopir'
		where no='$kode'"; 



mysqli_query($con,$query);
echo "<script>alert('Berhasil di Simpan');history.go(-2);</script>";
mysqli_close($con); 
//header("location:db_waiting.php");

?>