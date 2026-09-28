<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbtruck LOCAL / DEV (127.0.0.1) — dipakai report.php & lihat_foto.php
# Var  : $con
*/
$db_host = '127.0.0.1';
$db_user = 'afandiach';
$db_pswd = '4d0pd4n60';
$db_name = 'dbtruck';

$con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die("<div style='padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> Server Connection Lost...</div>" . mysql_error());
