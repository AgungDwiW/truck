<?php


$db_host1 = '10.203.121.140';
$db_user1 = 'user_safety';
$db_pswd1 = 'user.safety';
$db_name = 'aquan_central';    
$db_host = '127.0.0.1:6604';
$db_user = 'wicaksau-adm';
$db_pswd = '1';

$con_140 = mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (140) Lost ...</small></div></body>' . mysqli_error());    


?>