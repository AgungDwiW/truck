
<!-- <div class="body-wrap-with-navbar"> -->
<link rel="stylesheet" href="static/css/kotak.css">




<!-- <div class="kotak_sq">
      <a class="btn btn-primary tombol_safety" href="main?action=pilih_gate">FG Truck</a>
      </br>
      </br>
      <a class="btn btn-primary tombol_quality" href="main?action=pilih_gate">Material Truck</a>
      </br>
      <!-- <center>
        <a class="link" href="index.php">BACK</a>
      </center>
</div> -->
  


<div class="kotak_sq">    

<form method="post" action="main?action=pilih_gate">
 		<input type="text" name="muat" value="FG" hidden></input>
		<button type="submit" class="btn btn-primary tombol_safety">FG Truck</button>
</form>	

</br>
</br>

<form method="post" action="main?action=pilih_gate">
 		<input type="text" name="muat" value="Material" hidden></input>
		<button type="submit" class="btn btn-primary tombol_quality">Material Truck</button>
</form>	


</div>


</body>
</html>