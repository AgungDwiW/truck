<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Pandaan Plant | 2019-09-25 18:05
# Ket   : Main controller
*/

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('APP_DIR', ROOT_DIR .'application/');
define('APP_NAME', 'e_Truck Inspection');
define('APP_DESCRIPTION', 'Truck Inspection');
define('APP_VER', '1.0');

//$db_host = '127.0.0.1';$db_user = 'root';$db_pswd = '12qwaszx';$db_name='dbtruck1';
//$db_host = '127.0.0.1';$db_user = 'afandiach';$db_pswd = '4d0pd4n60';$db_name='dbtruck';
$db_host = '127.0.0.1';$db_user = 'afandiach';$db_pswd = '4d0pd4n60';$db_name='dbtruck';$db_name2='smartlogistic';
$db_host = '127.0.0.1';$db_user = 'afandiach';$db_pswd = '4d0pd4n60';$db_name='dbtruck';$db_name2='smartlogistic';

$db_host = '10.203.121.109';
$db_user = 'uap_smartlogistic';
$db_pswd = 'q$pF9QMAC!Dr';
$db_name = 'dbtruck';

// $db_host = '10.203.121.109';$db_user = 'truck';$db_pswd = 'S3bEBer7';$db_name = 'dbtruck';$db_name2='smartlogistic';

$con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection  (73) Lost ...</small></div></body>' . mysqli_error());

// $db_host = '10.203.121.109';$db_user = 'truck';$db_pswd = 'S3bEBer7';$db_name2='smartlogistic';
// $db_host = '10.204.148.28';$db_user = 'wicaksau';$db_pswd = 'rmPqE$f7;YVx';$db_name = 'smartlogistic';
$con2 = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name2) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (smartlog) Lost ...</small></div></body>' . mysqli_error());



$db_host1 = '10.203.121.140';
$db_user1 = 'user_safety';
$db_pswd1 = 'user.safety';
$db_name1 = 'aquan_central';    

// $db_host1 = '10.204.148.28';$db_user1 = 'wicaksau';$db_pswd1 = 'rmPqE$f7;YVx';$db_name1 = 'evisitor';


$con_140 = @mysqli_connect($db_host1, $db_user1, $db_pswd1, $db_name1) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (140) Lost ...</small></div></body>' . mysqli_error());    


$db_host3 = '10.203.121.73';
$db_user3 = 'webuser';
$db_pswd3 = 'RTYF34567CVBN$%GYJ*Fghjk';
$db_name3 = 'evisitor';
  

// $db_host3 = '10.204.148.28';$db_user3 = 'wicaksau';$db_pswd3 = 'rmPqE$f7;YVx';$db_name3 = 'evisitor';

$con_3 = @mysqli_connect($db_host3, $db_user3, $db_pswd3, $db_name3) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (73) Lost ...</small></div></body>' . mysqli_error());        
    
$strict= "SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
mysqli_query($con, $strict);

?>