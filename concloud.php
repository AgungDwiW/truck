<?php
$db_host = '103.153.61.243';$db_user = 'usersmartlog';$db_pswd = 'nEdu5a';$db_name='dbasnho';


$concloud = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection Lost ...</small></div></body>' . mysqli_error(mysqli_connect($db_host, $db_user, $db_pswd, $db_name)));

// $db_host = '10.203.121.73';$db_user = 'admin';$db_pswd = '4dm1n@sql';$db_name = 'smartlogistic';
// $db_host = '10.203.121.109';$db_user = 'truck';$db_pswd = 'S3bEBer7';$db_name = 'smartlogistic';
$db_host = '10.204.148.28';$db_user = 'wicaksau';$db_pswd = 'rmPqE$f7;YVx';$db_name = 'smartlogistic';
$db_host = '10.203.121.109';
$db_user = 'uap_smartlogistic';
$db_pswd = 'q$pF9QMAC!Dr';
$db_name = 'smartlogistic';
// $db_host = '10.203.121.81';$db_user = 'userdev';$db_pswd = 'password';$db_name = 'smartlogistic';
$conSL = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
        die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (SL) Lost,'.mysqli_connect_error().'</small></div></body>');
    
?>