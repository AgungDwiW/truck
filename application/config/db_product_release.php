<?php
/*
# e_Truck Inspection — CONNECTION
# Name : db_product_release (server 73) — dipakai form KIR / e-Truck (alur FG)
# Var  : $con73
*/
$host   = '10.203.121.73';
$user   = 'uapp_productcode';
$pass   = 'ocr.productcode';
$dbname = 'db_product_release';

$con73 = new mysqli($host, $user, $pass, $dbname);
if ($con73->connect_error) {
    die("Koneksi gagal: " . $con73->connect_error);
}
