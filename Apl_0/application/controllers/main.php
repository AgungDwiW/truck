<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Plant Pandaan | 2019-07-22 20:57
# Ket   : Production Module
*/

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