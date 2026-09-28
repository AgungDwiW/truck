
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<div class="body-wrap-with-navbar">
<br>
<br>
<br>
<?php
$muat = @$_POST['muat'];

$username=$_SESSION[APP_NAME]["username"];

$sql_username = mysqli_query($con,"  SELECT * from tbm_user where nama='$username' ");


          while($rowuser = mysqli_fetch_assoc($sql_username)){
          $plant_name=$rowuser["plant_name"];
          $plant_id=$rowuser["plant_id"];

          }



 if ($plant_id=='90A8') {$link='FG_cek_truck';}
 if ($plant_id<>'90A8') {$link='FG_cek_truck_rev';}



?>





  	<div class="kotak_sq">

  	<form method="post" action="main?action=<?php echo $link;  ?>">
 			 
 		
    <h2><strong><label style="color: yellow; text-align: center;" class="center-block">INPUT NOPOL</label></strong></h2> 
 		<input type="text" class="center-block text-uppercase" style="width: 300px; height: 80px; font-size: 50px; background: white; color: black; text-align: center;" name="nopol" required></input>
<br>
   
    <h2><strong><label style="color: yellow; text-align: center;" class="center-block">INPUT ID SHIPMENT</label></strong></h2> 
    <input type="text" class="center-block text-uppercase" style="width: 480px; height: 80px; font-size: 50px; background: white; color: black; text-align: center;" name="id_shipment" required></input>



    <input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>

 		<br>
 		<br>
		<button type="submit" class="btn btn-success tombol_ic center-block">Submit</button>
	  </form>	
		
	  </div>
  	


<style type="text/css">
.tombol_ic{
  color: white;
  font-size: 20pt;
  width: 200px;
  height: 80px;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.label{
  color: white;
  font-size: 30pt;
  width: 300px;
  height: 100px;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
  background: black;
}


</style>