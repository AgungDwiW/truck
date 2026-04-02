<?php 

?>

<div class="navbar navbar-default navbar-fixed-top" id="custom-bootstrap-menu">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="logo">
                <img src="static/images/adop16.png"> <?php echo defined('APP_NAME') ? APP_NAME.'<sup>'.APP_VER.'</sup>' : 'TECH DB'; ?>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="navbar-text" style="color:yellow"><?php echo User::$plant; ?>
                <li><a href="../mos"> ADOP</a></li>
                
                <li><a href="main" class="main"> Main Menu</a></li>
            </ul> 
            <ul class="nav navbar-nav navbar-right">
                <li class="<?php if(isset($_GET['ac']) AND $_GET['ac']=='inbox') echo 'active';?>">
                    <a href="capex?ac=inbox">
                        <?php if(isset($inboxMsg['qty'])) echo '<span class="badgeadop">'.$inboxMsg['qty'].'</span>'; ?> 
                        <i class="fa fa-envelope-o"></i> Inbox
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-user"></i> <?php echo isset($_SESSION[APP_NAME]["name"]) ? $_SESSION[APP_NAME]["name"] : 'User'; ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:fg_popup_form('fgChgPass','frmInner','bgChgPass', 80);">Ganti Password</a></li>
                        <li class="divider"></li>
                        <li><a href="auth?ac=signout">Sign out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>