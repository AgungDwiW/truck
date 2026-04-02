<?php

$db_host = '10.203.121.109';
$db_user = 'uap_smartlogistic';
$db_pswd = 'q$pF9QMAC!Dr';
$db_name = 'dbtruck';

$db_host = '127.0.0.1:6604';
$db_user = 'wicaksau-adm';
$db_pswd = '1';

$con = mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection  (73) Lost ...</small></div></body>' . mysqli_error($con));

// $con2 = mysqli_connect($db_host, $db_user, $db_pswd, $db_name2) or
//     die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (smartlog) Lost ...</small></div></body>' . mysqli_error($con_2));

$strict= "SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
mysqli_query($con, $strict);


?>