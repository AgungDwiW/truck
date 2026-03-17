<?php
$db_host = '103.153.61.243';$db_user = 'usersmartlog';$db_pswd = 'nEdu5a';$db_name='dbasnho';


$conASN = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (ASN) Lost ...</small></div></body>' . mysqli_error(mysqli_connect($db_host, $db_user, $db_pswd, $db_name)));

$db_host = '103.153.61.243';$db_user = 'usersmartlog';$db_pswd = 'nEdu5a';$db_name='dbasnho';
$db_host = '127.0.0.1';$db_user = 'root';$db_pswd = 'root';$db_name='dbasnho';
$conASNPDO = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pswd);

?>