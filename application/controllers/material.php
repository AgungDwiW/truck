<?php
/*
# e_Truck Inspection
# Ket  : Controller Material truck flow
#        View: application/views/material/
#        URL  : material?action=...   (URL lama main?action=... tetap jalan via controllers/main.php)
*/

$material_template = array(
    'cek_nopol',
    'N_cek_truck',
);

if (in_array($action, $material_template, true) AND file_exists(APP_DIR . 'views/material/' . $action . '.php')) {
    $navtop  = 'navtop.php';
    $content = APP_DIR . 'views/material/' . $action . '.php';
    require(ROOT_DIR . 'master.php');
    exit;
}

require_once(APP_DIR . 'assets/error.php');
