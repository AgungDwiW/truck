<?php

$user 					=User::$username;
$sukses=0;
$temuan          		= (isset($_POST['tem'])? $_POST['tem'] : '');

if ($temuan<>'') {

			
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
					$query = mysqli_query($con,"INSERT INTO upload SET nama_file='$nama'  ");
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

} else{ ?><script type="text/javascript">alert('No Data... Pastikan Temuan di isi...!!!')</script>

				<?php			

				} 







 


$idref          		= (isset($_POST['idref'])? $_POST['idref'] : '');
$ccp          			= (isset($_POST['ccp'])? $_POST['ccp'] : '');
$temuan          		= (isset($_POST['tem'])? $_POST['tem'] : '');
$utama          		= (isset($_POST['utama'])? $_POST['utama'] : '');
$nopol          		= (isset($_POST['nopol'])? $_POST['nopol'] : '');
$lokasi          		= (isset($_POST['lokasi'])? $_POST['lokasi'] : '');
$kode_kirim          	= (isset($_POST['kode_kirim'])? $_POST['kode_kirim'] : '');
$driver       			= (isset($_POST['driver'])? $_POST['driver'] : '');
$supplier     			= (isset($_POST['supplier'])? $_POST['supplier'] : '');







if ($sukses==1 and $temuan<>'') {




mysqli_query($con,"INSERT INTO tb_foto SET idref='$idref', item_utama='$ccp', description='$temuan', petugas='$user', foto_name='$nama',username='$user', utama='$utama', nopol='$nopol', lokasi='$lokasi' ");


if($utama==1){ mysqli_query($con,"UPDATE tb_ceklist SET utama1=0  where idref='$idref' ");  }
if($utama==2){ mysqli_query($con,"UPDATE tb_ceklist SET utama2=0  where idref='$idref' ");  }
if($utama==3){ mysqli_query($con,"UPDATE tb_ceklist SET utama3=0  where idref='$idref' ");  }
if($utama==4){ mysqli_query($con,"UPDATE tb_ceklist SET utama4=0  where idref='$idref' ");  }


}






?>

</br>
</br>
</br>
</br>
</br>

<div class="kotak_sq">

	<form method="post" action="main?action=N_gate1">	
		   <input type="text" name="idref" value="<?php echo $idref;?>" hidden></input>
		   <input type="text" name="nopol" value="<?php echo $nopol;?>" hidden></input>
		   <input type="text" name="lokasi" value="<?php echo $lokasi;?>" hidden></input>
		   <input type="text" name="kode_kirim" value="<?php echo $kode_kirim;?>" hidden></input>
		   <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
   		   <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 


			<button type="submit" class="btn btn-success tombol_pass">BACK</button>
			</br>
			</br>
	</form>


	</br>

	<form method="post" action="main?action=N_foto_gate1">	


	<input type="text" name="utama" value="<?php echo $utama; ?>" hidden></input> 
    <input type="text" name="idref" value="<?php echo $idref;?>" hidden></input>
    <input type="text" name="ceklist" value="<?php echo $ccp;?>" hidden></input>
    <input type="text" name="nopol" value="<?php echo $nopol;?>" hidden></input>
    <input type="text" name="lokasi" value="<?php echo $lokasi;?>" hidden></input>
    <input type="text" name="kode_kirim" value="<?php echo $kode_kirim;?>" hidden></input>
    <input type="text" name="tambah_foto" value="1" hidden></input>
    <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
    <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 


	<button type="submit" class="btn btn-warning tombol_na">Tambah Foto</button>
	</br>
	</br>
	</form>
		
</div>









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