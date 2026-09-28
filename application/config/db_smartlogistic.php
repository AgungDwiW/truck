<?php
/*
# e_Truck Inspection — CONNECTION
# Name : smartlogistic  |  Var : $con2, $conSL
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $db_host = 'smartlog.nead.danet'; $db_user = 'uap_smartlogistic'; $db_pswd = 'q$pF9QMAC!Dr';
# --- LOKAL / DEV ---
$con2  = db_open('127.0.0.1', 'root', 'root', 'smartlogistic', 'smartlog');
$conSL = db_open('127.0.0.1', 'root', 'root', 'smartlogistic', 'SL');
