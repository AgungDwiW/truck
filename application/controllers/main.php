<?php
/*
# e_Truck Inspection
# Ket  : Front controller "main" — kompatibilitas URL lama (main?action=...).
#        Memetakan action -> controller grup (fg | material | common) lewat
#        application/config/routes.php. Controller grup di-INCLUDE (bukan redirect)
#        supaya data POST tetap terjaga.
# Rev  : 2026-09-28 — dipecah dari switch raksasa ke controller per grup.
*/

$routes = require(APP_DIR . 'config/routes.php');

$group = (isset($routes[$action]) ? $routes[$action] : '');
$group_controller = APP_DIR . 'controllers/' . $group . '.php';

if ($group != '' AND file_exists($group_controller)) {
    require($group_controller);
} else {
    require_once(APP_DIR . 'assets/error.php');
}
