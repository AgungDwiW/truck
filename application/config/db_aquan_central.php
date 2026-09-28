<?php
/*
# e_Truck Inspection — CONNECTION
# Name : aquan_central  |  Var : $con_140
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $db_host1 = '10.203.121.140'; $db_user1 = 'user_safety'; $db_pswd1 = 'user.safety';
# --- LOKAL / DEV ---
$con_140 = db_open('127.0.0.1', 'root', 'root', 'aquan_central', '140');
