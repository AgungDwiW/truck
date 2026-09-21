<?php 

include_once "application/config/config.php";
include_once "application/library/autoload.php";
User::unsetSession();
session_destroy();  
header("location:login.php");
?>