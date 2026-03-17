<?php
include "application/models/auth/User.php";
include_once "application/library/autoload.php";

// Initialize static helper
StaticHelper::init();

// $debug = 1;
# Event Login 
if(isset($_POST["username"]) AND isset($_POST["password"])) {
	$USER = new User();
  	if($USER->login( $_POST['username'], $_POST['password'])){
		if (!$debug)
			header("location:main/index");
	}
	echo "<script type='text/javascript'>document.getElementById('log').innerHTML='ALERT! Login failed...'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo APP_NAME; ?></title>
		<link rel="shortcut icon"  href="favicon.ico">
		<?= static_css('css/font-awesome.min.css') ?>
		<?= static_css('css/damar-login.css') ?>
	</head>
	<body>
		<div class="responsive-column">
		</div>
		<div class="content">
			<div class="header-holder">
				<h1><sup><?= static_img('images/adop16.png') ?></sup> <?php echo APP_NAME; ?> <sup><?php echo APP_VER; ?></sup> Buy & Sell
				<small><?php echo APP_DESCRIPTION; ?></small></h1>
			</div>
			<!-- <div><a href="loginsso.php">Login SSO</a></div> -->
			<div class="logo"><?= static_img('images/aqua217.png') ?></div>
			<div id="log"></div>
			<div class="footer-holder">
				<form action="#" method="POST">
					<div class="form-control">
						<input type="text" name="username" autocomplete="off" placeholder="&#xf007; USERNAME" style="font-family:Arial, FontAwesome" maxlength="20" required>
						<input type="password" name="password" autocomplete="off" placeholder="&#xf084; PASSWORD"  style="font-family:Arial, FontAwesome" maxlength="20" required>
						<input type="submit" value="SIGN IN">
					</div>

				</form>
			    <div class="notice">
					<hr>
				    <label>Legal Notice:</label>
				    The information in this document and attachment is confidential and may also be legally privileged. It is intended only for the use of named recipient.
				    Internet communications are not secure and therefore DANONE does not accept legal responsibility for the contents of this message.
				    If you are not intended recipient, please notify us immediately and then delete this document. Do not disclose the contents of this document to any other person, nor take any copies.
				    Violation of this notice may be unlawful. ADOP Inovation&copy2020, ver.<?php echo APP_VER; ?>
			    </div>
			</div>
		</div>
	</body>
</html>