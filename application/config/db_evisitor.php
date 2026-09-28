<?php
/*
# e_Truck Inspection — CONNECTION
# Name : evisitor (server 73)
# Var  : $con_3
*/
$db_host3 = '10.203.121.73';
$db_user3 = 'webuser';
$db_pswd3 = 'RTYF34567CVBN$%GYJ*Fghjk';
$db_name3 = 'evisitor';

$con_3 = @mysqli_connect($db_host3, $db_user3, $db_pswd3, $db_name3) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (73) Lost ...</small></div></body>' . mysqli_error());
