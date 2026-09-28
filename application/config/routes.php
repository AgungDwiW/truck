<?php
/*
# e_Truck Inspection
# Ket : Route map (action -> grup view: fg | material | common)
#       Sumber kebenaran tunggal pemetaan action ke folder view.
#       Dipakai oleh index.php (resolusi view) dan controllers/main.php (dispatch).
#       Refactor pemisahan view: fg / material / common.  2026-09-28
*/

return array(

    /* ---------------- FG (Finished Goods) ---------------- */
    'FG_cek_nopol'      => 'fg',
    'FG_cek_truck'      => 'fg',
    'FG_cek_truck_rev'  => 'fg',
    'FG_cek_nopol_new'  => 'fg',
    'input_nopol'       => 'fg',
    'edit_nopol'        => 'fg',
    'kirim_input_nopol' => 'fg',
    'kirim_edit_nopol'  => 'fg',
    'cari_truck'        => 'fg',

    /* ---------------- Material ---------------- */
    'cek_nopol'         => 'material',
    'N_cek_truck'       => 'material',

    /* ---------------- Common (shared / others) ---------------- */
    'index'                => 'common',
    'start'                => 'common',
    'pilih_gate'           => 'common',
    'cek_user_safety'      => 'common',
    'pilih_driver'         => 'common',
    'pilih_helper'         => 'common',
    'status_vaksin'        => 'common',
    'status_vaksin_helper' => 'common',
    'foto_vaksin'          => 'common',
    'upload_vaksin'        => 'common',
    'N_gate1'              => 'common',
    'N_foto_gate1'         => 'common',
    'N_upload_fail'        => 'common',
    'gate1_temp'           => 'common',
    'simpan_gate1'         => 'common',
    'db_waiting'           => 'common',
    'cek_gate2'            => 'common',
    'lanjut_gate2'         => 'common',
    'cek_kpi'              => 'common',
    'reg_user'             => 'common',
    'edit_user'            => 'common',
    'status_tambahan'      => 'common',
    'gate1_ajax'           => 'common',
    'cek_gate1'            => 'common',
    'gate1'                => 'common',
    'foto_gate1'           => 'common',
    'upload_fail'          => 'common',
);
