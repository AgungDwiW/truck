<?php
/*
# e_Truck Inspection — loader kompatibilitas
# Var : $conASN, $conASNPDO (dipakai application/assets/TableASN.php)
*/
require_once(dirname(__FILE__) . '/db_cloud_asn.php');      # $conASN
require_once(dirname(__FILE__) . '/db_smartlogistic.php');  # $conSL / $con2

# PDO (dulu dari db_smartlogistic_local.php) — sekarang lokal
$conASNPDO = new PDO('mysql:host=127.0.0.1;dbname=smartlogistic', 'root', 'root');
