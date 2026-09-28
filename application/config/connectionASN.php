<?php
/*
# e_Truck Inspection — loader kompatibilitas
# Var  : $conASN, $conASNPDO
# Def  : application/config/db_cloud_asn.php, application/config/db_smartlogistic_local.php
# Rev  : 2026-09-28
*/

require_once(dirname(__FILE__) . '/db_cloud_asn.php');          # $conASN
require_once(dirname(__FILE__) . '/db_smartlogistic_local.php'); # $conASNPDO (dan $conSL)

