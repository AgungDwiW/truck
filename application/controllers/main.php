<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Plant Pandaan | 2019-07-22 20:57
# Ket   : Production Module
*/

if (isset($_GET['action']) && $_GET['action'] === 'cari_truck') {
    include 'application/views/main/cari_truck.php'; // hanya include file yang mengembalikan JSON
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'kirim_input_nopol') {
    include 'application/views/main/kirim_input_nopol.php'; 
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'kirim_edit_nopol') {
    include 'application/views/main/kirim_edit_nopol.php'; 
    exit;
}


switch($action){

	case 'cek_user_safety':
	case 'pilih_gate':		
	case 'index':		# 20181227
	case 'cek_gate1':	
	case 'gate1':
	case 'foto_gate1':
	case 'upload_fail':	
	case 'gate1_temp':
	case 'db_waiting':
	case 'cek_gate2':
	case 'lanjut_gate2':
	case 'cek_kpi':
	case 'cek_nopol':
	case 'N_cek_truck':
	case 'N_gate1':
	case 'N_foto_gate1':
	case 'N_upload_fail':
	case 'FG_cek_truck':
	case 'FG_cek_nopol':
	case 'reg_user':
	case 'edit_user':
	case 'FG_cek_truck_rev':


	case 'start':
	case 'pilih_driver':
	case 'pilih_helper':
	case 'status_vaksin':
	case 'status_vaksin_helper':
	case 'foto_vaksin':
	case 'upload_vaksin':
	
	//update e-truck
	case 'FG_cek_nopol_new':
	case 'input_nopol':
	case 'edit_nopol':




		$navtop = 'navtop.php';
		// $footer = 'footer.php';
		require('master.php');
		break;

	case 'simpan_gate1':
	case 'vmipost':
	case 'simupost':
		require('master.php');
		break;

		
	


}
?>