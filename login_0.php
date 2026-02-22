<?php
/*
# Prject: BWG Project 1.0
# Auth  : DamarTeduh©2019
# Create: Bandara Sby | 2019-09-20 12:50 PM

# catatan :
# $json='﻿{"username":"afandiac","name":"Achmad Afandi","email":"Achmad.Afandi@danone.com","region":"REG-3","plant":"9011","line":null,"admin":"N"}';


*/

include "config.php";
# Query user fr (10.203.121.73.dbopboard.tb_usr)
if(isset($_POST["username"]) AND isset($_POST["password"])){
	# Call ADOP API
	 $url ='http://10.203.121.73:83/adopapi/user/login.php?u='.stripslashes($_POST['username']).'&p='.stripslashes($_POST['password']);
	 $json=file_get_contents($url);
	$row =json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$json),true);
	if($json) {
		session_start();
		$_SESSION[APP_NAME]["name"]	   =$row["name"];
		User::$username=$row["username"];
		$_SESSION[APP_NAME]["plant"]   =$row["plant"];
		$_SESSION[APP_NAME]["region"]  =$row["region"];
		$_SESSION[APP_NAME]["line"]    =$row["line"];
		$_SESSION[APP_NAME]["admin"]   =$row["admin"];
		$_SESSION[APP_NAME]["email"]   =$row["email"];
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