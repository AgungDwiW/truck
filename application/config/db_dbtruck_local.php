<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbtruck (dipakai report.php & lihat_foto.php)  |  Var : $con
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $db_host = '127.0.0.1'; $db_user = 'afandiach'; $db_pswd = '4d0pd4n60';
# --- LOKAL / DEV ---
$con = db_open('127.0.0.1', 'root', 'root', 'dbtruck', 'dbtruck (report)');
