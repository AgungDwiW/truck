<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbasnho / ASN cloud (103.153.61.243)
# Var  : $concloud, $conASN
# Note : $concloud dan $conASN menunjuk server/db yang sama (nama variabel lama).
*/
$db_host = '103.153.61.243';
$db_user = 'usersmartlog';
$db_pswd = 'nEdu5a';
$db_name = 'dbasnho';

$concloud = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection Lost ...</small></div></body>' . mysqli_error(mysqli_connect($db_host, $db_user, $db_pswd, $db_name)));

$conASN = $concloud;
