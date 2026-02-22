<?php

$user 					=User::$username;
$sukses=0;


			
			$ekstensi_diperbolehkan	= array('png','jpg','jpeg');
			//$nama = $_FILES['file']['name'];
			$nama = date('ymdhis') ."_".$user. ".png";
			$x = explode('.', $nama);
			$ekstensi = strtolower(end($x));
			$ukuran	= $_FILES['file']['size'];
			//var_dump($ukuran);
			//exit;
			$file_tmp = $_FILES['file']['tmp_name'];	

			if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
				if($ukuran < 1200000 and $ukuran <> 0){			
					move_uploaded_file($file_tmp, 'application/views/main/capture/'.$nama);
					$query = mysqli_query($con,"INSERT INTO upload_vaksin SET nama_file='$nama'  ");
					if($query){ $sukses=1; ?>

						<script type="text/javascript">alert('FOTO BERHASIL DI UPLOAD')</script>

					<?php	

					}else{ ?>

						<script type="text/javascript">alert('GAGAL MENGUPLOAD FOTO')</script>

					<?php	
					}
				}else{ ?>


							<script type="text/javascript">alert('Tidak Ada Foto yang di Upload...!!! (Cek File or Max size 1 MB)')</script>

				<?php			

				}
			}else{?>


				<script type="text/javascript">alert('Tidak Ada Foto yang di Upload...!!!')</script>

			<?php		

			}









 


$nama_driver          	= (isset($_POST['nama_driver'])? $_POST['nama_driver'] : '');
$nik          			= (isset($_POST['nik'])? $_POST['nik'] : '');
$nama_trans          	= (isset($_POST['nama_trans'])? $_POST['nama_trans'] : '');
$orang          		= (isset($_POST['orang'])? $_POST['orang'] : '');
$dosis          		= (isset($_POST['dosis'])? $_POST['dosis'] : '');

$plant_name        		= (isset($_POST['plant_name'])? $_POST['plant_name'] : '');
$plant_id          		= (isset($_POST['plant_id'])? $_POST['plant_id'] : '');







if ($sukses==1) {




mysqli_query($con,"INSERT INTO tb_vaksin SET pengendara='$orang', nama='$nama_driver', nik_8_digit_awal='$nik', nama_transporter='$nama_trans', status_vaksin='$dosis',plant_name='$plant_name', foto_name='$nama' ");



}






?>

</br>
</br>
</br>
</br>
</br>


<?php

if ($orang=='driver') { ?>



<div class="kotak_sq">

	<form method="post" action="main?action=pilih_helper">	
		
			 <input type="text" id="plant_name" name="plant_name" value="<?php echo $plant_name ;  ?>" hidden >
             <input type="text" id="plant_id" name="plant_id" value="<?php echo $plant_id ;  ?>" hidden >
            

			<button type="submit" class="btn btn-success tombol_pass">Next Helper</button>
			</br>
			</br>
	</form>

		
</div>


<?php } ?>


<?php

if ($orang=='helper') { ?>



<div class="kotak_sq">

	<form method="post" action="main?action=start">	
		
			<button type="submit" class="btn btn-success tombol_pass">Lanjut</button>
			</br>
			</br>
	</form>

		
</div>


<?php } ?>






<style type="text/css">

.kotak_sq{
	width: 250px;
	background: blue;
	/*meletakkan form ke tengah*/
	margin: 50px auto;
	padding: 50px 20px;
	box-shadow: 0px 0px 100px 4px #d6d6d6;
}

.tombol_pass{
	background: green;
	color: white;
	font-size: 20pt;
	width: 100%;
	height: 100%;
	border: none;
	border-radius: 3px;
	padding: 20px 20px;
}

.tombol_fail{
	background: red;
	color: white;
	font-size: 20pt;
	width: 100%;
	height: 100%;
	border: none;
	border-radius: 3px;
	padding: 20px 20px;	

}	

.tombol_na{
	background: black;
	color: white;
	font-size: 20pt;
	width: 100%;
	height: 100%;
	border: none;
	border-radius: 3px;
	padding: 20px 20px;	

}	


</style>