<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbtruck @ server 73 (API BWG gate)  |  Var : $con73
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $s = '10.203.121.73'; $u = 'webuser'; $p = 'RTYF34567CVBN$%GYJ*Fghjk';
# --- LOKAL / DEV ---
$con73 = db_open('127.0.0.1', 'root', 'root', 'dbtruck', 'gate73');
