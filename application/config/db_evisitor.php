<?php
/*
# e_Truck Inspection — CONNECTION
# Name : evisitor  |  Var : $con_3
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $db_host3 = 'adop.ops.nead.danet'; $db_user3 = 'webuser'; $db_pswd3 = 'RTYF34567CVBN$%GYJ*Fghjk';
# --- LOKAL / DEV ---
$con_3 = db_open('127.0.0.1', 'root', 'root', 'evisitor', 'evisitor');
