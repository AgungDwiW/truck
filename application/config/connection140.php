<?php


$db_host1 = '10.203.121.140';
$db_user1 = 'user_safety';
$db_pswd1 = 'user.safety';
$db_name1 = 'aquan_central';    

// $db_host1 = '10.204.148.28';$db_user1 = 'wicaksau';$db_pswd1 = 'rmPqE$f7;YVx';$db_name1 = 'evisitor';
$db_host = '127.0.0.1';$db_user = 'root';$db_pswd = 'root';
$db_host = '127.0.0.1';$db_user = 'root';$db_pswd = 'root';$db_name='aquan_central';
$con_140 = mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (140) Lost ...</small></div></body>' . mysqli_error());    


?>