<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo APP_NAME; ?></title>
	<link rel="shortcut icon"  href="favicon.ico">
	<style type="text/css">
		@font-face {
			font-family: 'Stone Sans Regular';
			font-style: normal;
			font-weight: normal;
			src: local('Stone Sans Regular'), url('static/fonts/OpenSans.woff') format('woff');
		}
		body { font-family:'Stone Sans Regular'; }
		.content { margin-top: 80px; text-align: center; }
		hr { border: 0.5px solid #a4a4a5; margin-top: 20px; }
		input { padding: 7px; text-align: center; border: none; }
		input[type=text], input[type=password] { background-color: #eaebed; margin-right: 5px; }
		input[type=submit] { background-color: #3883e0;	color: white; }
		button:hover { opacity: 0.5 }
		sup { font-size: 14px; }
		div#log {color: red; font-style: italic;}
		.header-holder small { color: #2b6cef; font-size: 17px; font-weight: normal; }
		.logo { margin: 60px 0; }
		.logo img {	width: 229px; height: 131px; }
		.notice { margin: auto;	width: 910px; font-size: 11.5px; }
		h1.header-holder {display: inline-block;}
		#log {margin-bottom: 20px;color: red; font-style: italic; }
		/* Responsive */
		.responsive-column { display: none; }
		@media screen and (max-width:680px) {
		   	body { font-family:'Stone Sans Regular'; font-weight:normal; }
		  	.notice, .logo {display: none;}
			.footer-holder {text-align: center;}
		  	.footer-holder form {padding: 10px; display: block; margin-top: 50px}
		  	.footer-holder form input { display: block; width: 80%; margin: 10px; }
		  	.header-holder > h1 > small {display: block; width: 100%;}
		}
	</style>
	</head>

	<body>
		<div class="responsive-column">
		</div>
		<div class="content">
		
			<div class="logo">
				<img src="static/images/logo.png">
			</div>
			<div id="log"></div>
			<div class="footer-holder">
				<form action="lihat_foto.php" method="POST">
					<input type="text" name="idref" placeholder="ID Ref" required>
					<input type="submit" value="Submit">
				</form>
			    <div class="notice">
					<hr>
				    <label>Legal Notice:</label>
				    The information in this document and attachment is confidential and may also be legally privileged. It is intended only for the use of named recipient.
				    Internet communications are not secure and therefore DANONE does not accept legal responsibility for the contents of this message.
				    If you are not intended recipient, please notify us immediately and then delete this document. Do not disclose the contents of this document to any other person, nor take any copies.
				    Violation of this notice may be unlawful.
			    </div>
			</div>
		</div>
	</body>
</html>

