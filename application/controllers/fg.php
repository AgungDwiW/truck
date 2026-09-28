<?php
/*
# e_Truck Inspection
# Ket  : Controller FG (Finished Goods) truck flow
#        View: application/views/fg/
#        URL  : fg?action=...   (URL lama main?action=... tetap jalan via controllers/main.php)
*/

# Halaman ber-template master
$fg_template = array(
    'FG_cek_nopol',
    'FG_cek_truck',
    'FG_cek_truck_rev',
    'FG_cek_nopol_new',
    'input_nopol',
    'edit_nopol',
);

# Endpoint terminal: keluarkan JSON / redirect, tidak memakai template master
switch ($action) {
    case 'cari_truck':
        include APP_DIR . 'views/fg/cari_truck.php';
        exit;

    case 'kirim_input_nopol':
        include APP_DIR . 'views/fg/kirim_input_nopol.php';
        exit;

    case 'kirim_edit_nopol':
        include APP_DIR . 'views/fg/kirim_edit_nopol.php';
        exit;
}

if (in_array($action, $fg_template, true) AND file_exists(APP_DIR . 'views/fg/' . $action . '.php')) {
    $navtop  = 'navtop.php';
    $content = APP_DIR . 'views/fg/' . $action . '.php';
    require(ROOT_DIR . 'master.php');
    exit;
}

require_once(APP_DIR . 'assets/error.php');
