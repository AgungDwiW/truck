<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Hotel Taman Dayu | 2019-07-22 19:41
# Ket   : Main controller
# Rev   : 
*/

//Start the Session
session_start(); 

/* Fitlering POST and GET*/
foreach ($_GET as $key => $value) $_GET[$key] = preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$value);
// foreach ($_POST as $key => $value) $_POST[$key] = preg_replace('/[^a-zA-Z0-9_ -\/]/s',' ',$value);

# Load config
include_once "application/config/config.php";
include_once "application/library/autoloader.php";
include_once "application/models/auth/User.php";

if(!User::checkLogin()){
    // printpre("login failed",1);
    echo"<script type='text/javascript'>alert('Session sudah habis, perubahan data pada sistem yang dilakukan sebelumnnya belum tersimpan. Mohon log in kembali dan lakukan perubahan kembali.');window.location.href='login.php'</script>";

    exit;
    }

// Set our defaults
$controller = 'main';
$action = 'index';
$url = '';
    

// Get request url and script url
    $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
    $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
        
// Get our url path and trim the / of the left and the right
    if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

// Split the url into segments
    $segments = explode('?', $url);
    $org_segments = explode('/', $request_url);

// Do our default checks
    if(isset($segments[0]) && $segments[0] != '') $controller = $segments[0];
    if(isset($_GET['action']) && $_GET['action'] != '') $action = $_GET['action'];

// Get our controller file
    $path = APP_DIR . 'controllers/' . $controller . '.php';
    $content = APP_DIR . 'views/' . $controller . '/' . $action . '.php';
    // printpre([$path, $content],1);
    if(file_exists($path) AND file_exists($content) AND !isset($org_segments[3])){
        include($path);
    } else {
        require_once(APP_DIR . 'assets/error.php');
    }
?>