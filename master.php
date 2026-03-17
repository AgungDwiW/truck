<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Hotel Taman Dayu | 2019-07-22 19:41
# Ket   : Master Template
*/

date_default_timezone_set("Asia/Jakarta");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="favicon.ico" />

    <!-- Fontawesome -->
    <link href="<?=BASE_URL?>static/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Custome Style -->
    <link rel="stylesheet" href="<?=BASE_URL?>static/css/damarteduh.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?=BASE_URL?>static/css/bootstrap.min.css">

    <!-- table CSS -->
    <link rel="stylesheet" href="<?=BASE_URL?>static/css/table.css">

    <!-- Load JavaScript Libraries -->
    <script type="text/javascript" src="<?=BASE_URL?>static/js/jquery-1.12.0.min.js"></script>

    <!-- Load JavaScript table -->
    <script type="text/javascript" src="<?=BASE_URL?>static/js/fixed-header.js"></script>

    <title><?php echo APP_DESCRIPTION; ?></title>
  </head>
  <body>
    <?php 
        if(isset($navtop)) require($navtop);
        echo '<div class="container-fluid" style="margin-top:100px">';
        include($content);
        echo '</div>';
        if(isset($footer)) require($footer); 
    ?>

    <!-- Bootstrap Javascript -->
    <script type="text/javascript" src="<?=BASE_URL?>static/js/bootstrap.min.js"></script>
  </body>
</html>