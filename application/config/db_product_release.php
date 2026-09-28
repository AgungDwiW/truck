<?php
/*
# e_Truck Inspection — CONNECTION
# Name : db_product_release (form KIR / e-Truck, alur FG)  |  Var : $con73
*/
require_once(dirname(__FILE__) . '/db_connect.php');

# --- PRODUCTION (aktifkan kembali kalau perlu) ---
// $host = 'adop.ops.nead.danet'; $user = 'uapp_productcode'; $pass = 'ocr.productcode';
# --- LOKAL / DEV ---
$con73 = db_open('127.0.0.1', 'root', 'root', 'db_product_release', 'product-release');
