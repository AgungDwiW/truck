<div class="body-wrap-with-navbar">
<?php
$username=$_SESSION[APP_NAME]["username"];
$query = mysqli_query($con,"SELECT user from tb_user where username='$username'")or die(mysql_error());
        
$user_safety = mysqli_fetch_assoc($query);


if ($user_safety["user"]<>"safety") {
  				echo "<script>alert('Anda bukan User Safety..!!!');history.go(-1);</script>";
  			}
else {header("location:main?action=pilih_gate");}
  			
?>