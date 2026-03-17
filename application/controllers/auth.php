<?php
/*
# Prject: BWG Project 1.0
# Auth  : Dyah PP Wardhana
# Create: Hotel Taman Dayu | 2019-07-22 12:07 PM
# Ket   : Main controller
# Rev   : Updated for clean URL routing
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
        
    // Default case for any other view
    default:
        // Check if the view file exists
        $view_file = 'application/views/auth/' . $action . '.php';
        if (file_exists($view_file)) {
            // For auth views, we might want different handling
            // Currently, just include the view directly
            include($view_file);
        } else {
            // View doesn't exist
            require_once(APP_DIR . 'assets/error.php');
        }
        break;
}

?>