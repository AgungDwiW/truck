<?php
/*
# e_Truck Inspection — CONNECTION
# Name : smartlogistic LOCAL / DEV (localhost root)
# Var  : $conSL, $conASNPDO
# Note : override lokal yang aktif di connectionSL.php & connectionASN.php.
#        Ganti ke server 109 (db_smartlogistic.php) bila tidak dipakai lagi.
*/
$db_host = 'localhost';
$db_user = 'root';
$db_pswd = 'root';
$db_name = 'smartlogistic';

$conSL = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (SL) Lost,'.mysqli_connect_error().'</small></div></body>');

$conASNPDO = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pswd);
