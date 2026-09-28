<div class="body-wrap-with-navbar">
<br>
<br>
<br>
<?php
$muat = @$_POST['muat'];
?>


  	<div class="kotak_sq">

  	<form method="post" action="main?action=N_cek_truck">
 			 
 		<h2><strong><label style="color: yellow; text-align: center;" class="center-block">INPUT KODE KIRIM</label></strong></h2> 
 		<input type="text" class="center-block text-uppercase" style="width: 300px; height: 80px; font-size: 50px; text-align: center;" name="kode_kirim" required></input>
    <input type="text" name="muat" value="Material" hidden></input>

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
}


</style>