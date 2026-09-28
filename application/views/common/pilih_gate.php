<link rel="stylesheet" href="static/css/kotak.css">


  	<!-- <div class="kotak_sq">
 
			<a class="btn btn-primary tombol_gate1" href="main?action=cek_nopol">GATE 1</a>
			</br>
			</br>
			<a class="btn btn-primary tombol_gate2" href="main?action=db_waiting">GATE 2</a>
			</br>
			</br>
			<center>
				<a class="link" href="main?action=index">BACK</a>
			</center>
		
	</div> -->
<?php
$muat = $_POST['muat'];	

if ($muat=='FG') {$link='FG_cek_nopol';}
else {$link='cek_nopol';}	

?>


<div class="kotak_sq">

	<form method="post" action="main?action=<?php echo $link; ?>">
 		<input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>
		<button type="submit" class="btn btn-primary tombol_gate1">GATE 1</button>
	</form>	
	</br>
	</br>
  	<form method="post" action="main?action=db_waiting">
  		<input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>
		<button type="submit" class="btn btn-primary tombol_gate2">GATE 2</button>
	</form>	

</div>




</body>
</html>