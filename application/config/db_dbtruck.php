<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbtruck (production, server 109)
# Var  : $con
*/
$db_host = '10.203.121.109';
$db_user = 'uap_smartlogistic';
$db_pswd = 'q$pF9QMAC!Dr';
$db_name = 'dbtruck';

$con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection  (73) Lost ...</small></div></body>' . mysqli_error());
