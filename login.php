<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Bandara Sby | 2019-09-20 12:50 PM
*/

include "config.php";
# Query user fr (10.203.121.73.dbopboard.tb_usr)
//if(isset($_POST["password"])){

if(isset($_POST["username"]) AND isset($_POST["password"])){


	$json='﻿{"username":"afandiac","name":"Achmad Afandi","email":"Achmad.Afandi@danone.com","region":"REG-3","plant":"9016","line":null,"admin":"N"}';
	$row =json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$json),true);

					$user_login=@$_POST["username"];
					$pass=@$_POST["password"];
					$nik='';
					$cari_user = mysqli_query($con,"  SELECT * from tbm_user where nik='$pass' and username='$user_login'  ");
					//$cari_user = mysqli_query($con,"  SELECT * from tbm_user where nik='$pass' ");
					while($row1 = mysqli_fetch_assoc($cari_user)){
					$nama=$row1["nama"];
					$nik=$row1["nik"];
					$plant_id=$row1["plant_id"];
					$plant_name=$row1["plant_name"];

					}
			
	if($nik<>'') {
		session_start();
	
		$_SESSION[APP_NAME]["username"]		=$nama;
		$_SESSION[APP_NAME]["nik"]   		=$nik;
		$_SESSION[APP_NAME]["plant_id"]		=$plant_id;
		header("location:main");
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("ALERT! Password and username is invalid...");';
		echo '</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="favicon.ico" />
    <link href="static/css/damar-login.css" rel="stylesheet">
    <link href="static/css/font-awesome.min.css" rel="stylesheet">
    <title><?php echo APP_DESCRIPTION; ?></title>
  </head>
  <body>
	<div class='login'>
	  <div class='login_title'>
	  	<img src='static/images/adop32.png'>
	    <span><?php echo APP_NAME; ?></span>
	  </div>
	  <form action="#" method="POST">
		  <div class='login_fields'>

		  	<div class='login_fields__user'>
		      <div class='icon'><i class="fa fa-user"></i></div>
		      <input name="username" placeholder='Username' type='text' required>
		    </div>
		 
		    <div class='login_fields__password'>
		      <div class='icon'><i class="fa fa-key"></i></div>
		      <input name="password" placeholder='Password' type='password' required>
		    </div>
		    <div class='login_fields__submit'>
		      <input type='submit' value='Log In'>
		    </div>
		    <div class='footer'>
		    	<p>ADOP Inovation</p>
		    </div>
		  </div>
	  </form>
	</div>
</body>