<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('main', function() {
    // Home page (already handled by base route)
    // Router::addGet('', 'main@index', 'main_index');
    
    // ============================================
    // Flat action routes (for compatibility with existing links)
    // ============================================
    Router::addGet('',  'index');
    Router::addGet('cek_user_safety',  'cek_user_safety');
    Router::addGet('pilih_gate',  'pilih_gate');
    Router::addGet('index',  'main_index');
    Router::addGet('cek_gate1',  'cek_gate1');
    Router::addGet('gate1',  'gate1');
    Router::addGet('gate1_new',  'gate1_new');
    Router::addGet('foto_gate1',  'foto_gate1');
    Router::addGet('upload_fail',  'upload_fail');
    Router::addGet('gate1_temp',  'gate1_temp');
    Router::addGet('db_waiting',  'db_waiting');
    Router::addGet('cek_gate2',  'cek_gate2');
    Router::addGet('lanjut_gate2',  'lanjut_gate2');
    Router::addGet('cek_kpi',  'cek_kpi');
    Router::addGet('cek_nopol',  'cek_nopol');
    Router::addGet('N_cek_truck',  'N_cek_truck');
    Router::addGet('N_gate1',  'N_gate1');
    Router::addGet('N_gate_api',  'N_gate_api');
    Router::addGet('N_foto_gate1',  'N_foto_gate1');
    Router::addGet('N_upload_fail',  'N_upload_fail');
    Router::addGet('reg_user',  'reg_user');
    Router::addGet('edit_user',  'edit_user');
    Router::addGet('start',  'start');
    Router::addGet('pilih_driver',  'pilih_driver');
    Router::addGet('pilih_helper',  'pilih_helper');
    Router::addGet('status_vaksin',  'status_vaksin');
    Router::addGet('status_vaksin_helper',  'status_vaksin_helper');
    Router::addGet('foto_vaksin',  'foto_vaksin');
    Router::addGet('upload_vaksin',  'upload_vaksin');
    
    Router::addGet('input_nopol',  'input_nopol');
    Router::addGet('edit_nopol',  'edit_nopol');
    Router::addGet('cari_truck',  'cari_truck');
    Router::addGet('kirim_input_nopol',  'kirim_input_nopol');
    Router::addGet('kirim_edit_nopol',  'kirim_edit_nopol');
    
    // ============================================
    // AJAX endpoints (POST)
    // ============================================
    Router::addPost('simpan_gate1', 'simpan_gate1');
    Router::addPost('status_utama', 'status_utama');
    Router::addPost('status_tambahan', 'status_tambahan');
    Router::addPost('status_tambahan_qa', 'status_tambahan_qa');
    Router::addPost('status_all', 'status_all');
    Router::addPost('cari', 'cari');
    Router::addPost('cari_sopir', 'cari_sopir');
    Router::addPost('simpan_db', 'simpan_db');
    Router::addPost('simpan_db_gate2', 'simpan_db_gate2');
    // Additional GET routes for .php extensions (backward compatibility)
    Router::addGet('status_utama.php', 'status_utama.php');
    Router::addGet('status_tambahan.php', 'status_tambahan.php');
    Router::addGet('status_tambahan_qa.php', 'status_tambahan_qa.php');
    Router::addGet('status_all.php', 'status_all.php');
    Router::addGet('cari.php', 'cari.php');
    Router::addGet('cari_sopir.php', 'cari_sopir.php');
    Router::addGet('simpan_db.php', 'simpan_db.php');
    Router::addGet('simpan_db_gate2.php', 'simpan_db_gate2.php');

});