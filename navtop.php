<?php
/*
# Prject: VMI Primary 1.0
# Auth  : DamarTeduh©2019
# Create: Cyber 2 | 2019-07-31 11:30 AM
# Ket   : Main Top Navigation
# Rev   : 
*/
?>

<style type="text/css">
.navbar-brand>img {
   max-height: 100%;
   margin: 0 auto;
   -o-object-fit: contain;
   object-fit: contain; 
   display: inline;
}
</style>

<div class="navbar navbar-default navbar-fixed-top"  id="custom-bootstrap-menu">
	<div class="container-fluid">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand"><img src="static/images/adop32.png"> <?php echo APP_NAME.'<sup>'.APP_VER.'</sup>'; ?> </a>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li class="nav-item">
                    <a class="nav-link" href="main?action=index">Home<span class="sr-only">(current)</span></a>
            </li>
			<li class="nav-item">
                    <a class="nav-link" href="main?action=cek_kpi">Cek KPI<span class="sr-only">(current)</span></a>
            </li>     
		</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user"></i> <?php echo User::$username; ?> <span class="caret"></span></a>
				  	<ul class="dropdown-menu">
						<li><a href="auth?action=signout">Sign out</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>