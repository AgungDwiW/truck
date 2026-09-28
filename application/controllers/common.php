<?php
/*
# e_Truck Inspection
# Ket  : Controller Common (view yang dipakai bersama / lainnya)
#        View: application/views/common/
#        URL  : common?action=...   (URL lama main?action=... tetap jalan via controllers/main.php)
*/

# Halaman ber-template master
$common_template = array(
    'index',
    'start',
    'pilih_gate',
    'cek_user_safety',
    'pilih_driver',
    'pilih_helper',
    'status_vaksin',
    'status_vaksin_helper',
    'foto_vaksin',
    'upload_vaksin',
    'N_gate1',
    'N_foto_gate1',
    'N_upload_fail',
    'gate1_temp',
    'simpan_gate1',
    'db_waiting',
    'cek_gate2',
    'lanjut_gate2',
    'cek_kpi',
    'reg_user',
    'edit_user',
    'cek_gate1',
    'gate1',
    'foto_gate1',
    'upload_fail',
);

# Endpoint terminal: keluarkan JSON, tidak memakai template master
if ($action === 'status_tambahan') {
    include APP_DIR . 'views/common/status_tambahan.php';
    exit;
}

if (in_array($action, $common_template, true) AND file_exists(APP_DIR . 'views/common/' . $action . '.php')) {
    $navtop  = 'navtop.php';
    $content = APP_DIR . 'views/common/' . $action . '.php';
    require(ROOT_DIR . 'master.php');
    exit;
}

require_once(APP_DIR . 'assets/error.php');
