<?php
@$no = $_GET['no'];
// Atur zona waktu ke Waktu Indonesia Barat (GMT+7)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi database
$s = '10.203.121.73';
$u = 'webuser';
$p = 'RTYF34567CVBN$%GYJ*Fghjk';
$db = 'dbtruck';  

$con73=mysqli_connect($s,$u,$p,$db) or die("Could not connect #73 Server");

// $sql = "SELECT *
//         FROM tb_ceklist t
//         WHERE t.plant_id = '90A8' AND t.tgl_pemeriksaan = '$tgl' AND t.hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2'
//         ORDER BY `no` DESC
//         ";

$sql = "SELECT *
        FROM tb_ceklist t
        WHERE t.plant_id = '90A8' AND t.no = '$no'
        ";        
      
$d=mysqli_query($con73,$sql) or die($sql.mysqli_error($con73));

while ($row=mysqli_fetch_assoc($d)):
  $response = $row;
endwhile;

if (count(@$response) === 0) {
     $response = NULL;
}
// Menyediakan respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
// echo $sql;