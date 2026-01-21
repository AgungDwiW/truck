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
$db_host = '127.0.0.1';$db_user = 'afandiach';$db_pswd = '4d0pd4n60';$db_name='dbtruck';
$con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection Lost ...</small></div></body>' . mysqli_error());
?>