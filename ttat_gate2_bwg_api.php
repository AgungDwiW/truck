<?php
@$no = $_GET['no'];
// Atur zona waktu ke Waktu Indonesia Barat (GMT+7)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi database (application/config/db_dbtruck.php)
require __DIR__ . '/application/config/db_dbtruck.php';

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