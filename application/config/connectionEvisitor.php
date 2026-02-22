<?php

$db_host3 = '10.203.121.73';
$db_user3 = 'webuser';
$db_pswd3 = 'RTYF34567CVBN$%GYJ*Fghjk';
$db_name3 = 'evisitor';
$db_host3 = '127.0.0.1';$db_user3 = 'root';$db_pswd3 = 'root';

// $db_host3 = '10.204.148.28';$db_user3 = 'wicaksau';$db_pswd3 = 'rmPqE$f7;YVx';$db_name3 = 'evisitor';

$con_3 = @mysqli_connect($db_host3, $db_user3, $db_pswd3, $db_name3) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (73) Lost ...</small></div></body>' . mysqli_error());        
    

?>