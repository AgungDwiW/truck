<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbasnho / ASN  |  Var : $concloud, $conASN
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $db_host = '103.153.61.243'; $db_user = 'usersmartlog'; $db_pswd = 'nEdu5a';
# --- LOKAL / DEV ---
$concloud = db_open('127.0.0.1', 'root', 'root', 'dbasnho', 'ASN');
$conASN   = $concloud;
