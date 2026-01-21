<?php
/*
# Prject: BWG Project 1.0
# Auth  : Dyah PP Wardhana
# Create: Hotel Taman Dayu | 2019-07-22 12:07 PM
# Ket   : Main controller
# Rev   : 
*/

// include "application/assets/sql_function.php";

# Akses previlage
// $crud = getAkses();

switch($action){
	case 'index':
		header('location:application/models/');
		break;

	case 'signout':
		session_destroy();
		setcookie("user", "", time()-3600);
		header('location:login.php');
		break;
}

?>