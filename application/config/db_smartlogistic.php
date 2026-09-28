<?php
/*
# e_Truck Inspection — CONNECTION
# Name : smartlogistic (server 109)
# Var  : $con2, $conSL
*/
$db_host = '10.203.121.109';
$db_user = 'uap_smartlogistic';
$db_pswd = 'q$pF9QMAC!Dr';
$db_name = 'smartlogistic';

$con2 = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (smartlog) Lost ...</small></div></body>' . mysqli_error());

$conSL = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (SL) Lost,'.mysqli_connect_error().'</small></div></body>');
