<?php
/*
# e_Truck Inspection — CONNECTION
# Name : aquan_central (server 140)
# Var  : $con_140
*/
$db_host1 = '10.203.121.140';
$db_user1 = 'user_safety';
$db_pswd1 = 'user.safety';
$db_name1 = 'aquan_central';

$con_140 = @mysqli_connect($db_host1, $db_user1, $db_pswd1, $db_name1) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (140) Lost ...</small></div></body>' . mysqli_error());
