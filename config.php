<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Pandaan Plant | 2019-09-25 18:05
# Ket   : Main config — konstanta aplikasi + pemuatan koneksi database.
# Rev   : 2026-09-28 — semua koneksi dipindah ke application/config/db_*.php
#         (satu file untuk tiap koneksi).
*/

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('APP_DIR', ROOT_DIR .'application/');
define('APP_NAME', 'e_Truck Inspection');
define('APP_DESCRIPTION', 'Truck Inspection');
define('APP_VER', '1.0');

# --- Koneksi database (1 file per koneksi, lihat application/config/) ---
require_once(APP_DIR . 'config/db_dbtruck.php');        # $con     -> 10.203.121.109 / dbtruck
require_once(APP_DIR . 'config/db_smartlogistic.php');  # $con2    -> 10.203.121.109 / smartlogistic (+$conSL)
require_once(APP_DIR . 'config/db_aquan_central.php');  # $con_140 -> 10.203.121.140 / aquan_central
require_once(APP_DIR . 'config/db_evisitor.php');       # $con_3   -> 10.203.121.73  / evisitor

$strict= "SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'";
mysqli_query($con, $strict);

?>
