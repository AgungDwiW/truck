<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Hotel Taman Dayu | 2019-07-22 19:41
# Ket   : Master Template
*/

date_default_timezone_set("Asia/Jakarta");

// Initialize static helper
StaticHelper::init();
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
    <?= static_css('css/font-awesome.min.css') ?>
    
    <!-- Custome Style -->
    <?= static_css('css/damarteduh.css') ?>

    <!-- Bootstrap CSS -->
    <?= static_css('css/bootstrap.min.css') ?>

    <!-- table CSS -->
    <?= static_css('css/table.css') ?>

    <!-- Load JavaScript Libraries -->
    <?= static_js('js/jquery-1.12.0.min.js') ?>

    <!-- Load JavaScript table -->
    <?= static_js('js/fixed-header.js') ?>




    <title><?php echo APP_DESCRIPTION; ?></title>
  </head>
  <body>
    <?php 
        if(isset($navtop)) require($navtop);
        echo '<div class="container-fluid" style="margin-top:50px">';
        include($content);
        echo '</div>';
        if(isset($footer)) require($footer); 
    ?>

    <!-- Bootstrap Javascript -->
    <?= static_js('js/bootstrap.min.js') ?>
  </body>
</html>