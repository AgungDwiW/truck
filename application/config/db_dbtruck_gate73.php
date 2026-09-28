<?php
/*
# e_Truck Inspection — CONNECTION
# Name : dbtruck @ server 73 (webuser) — dipakai API BWG gate
# Var  : $con73
*/
$s  = '10.203.121.73';
$u  = 'webuser';
$p  = 'RTYF34567CVBN$%GYJ*Fghjk';
$db = 'dbtruck';

$con73 = mysqli_connect($s, $u, $p, $db) or die("Could not connect #73 Server");
