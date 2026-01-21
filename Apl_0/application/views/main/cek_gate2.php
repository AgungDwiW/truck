<div class="body-wrap-with-navbar">
<style type="text/css">
  
body {
  background-color:white;
}
</style>

<?php

include "application/assets/function.php";
	
$kode = $_POST['kode'];

$query_mysql = mysqli_query($con,"SELECT * FROM tb_ceklist WHERE no='$kode'")or die(mysql_error());
$data = mysqli_fetch_array($query_mysql)
?>



    <div class="container">
    <form method="post" action="main?action=lanjut_gate2">
<div class="row">
  <div class="col">
        <label>No Polisi</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" 
      value="<?php echo $data['nopol'] ?>" aria-describedby="emailHelp" readonly>
  </div>
  <div class="col">
        <label>Tanggal Pemeriksaan</label>
  </div>

  <div class="col">
      <input type="text" class="form-control" id="tgl" name="tgl" aria-describedby="emailHelp"
      value="<?php echo $data['tgl_pemeriksaan'] ?>" readonly>
  </div>
</div>




<div class="row">
  <div class="col">
        <label>Nama Sopir</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="nama_sopir" name="nama_sopir"
      value="<?php echo $data['nama_sopir'] ?>" aria-describedby="emailHelp" readonly>
  </div>
  <div class="col">
        <label>Jam Pemeriksaan</label>
  </div>

  <div class="col">
      <input type="text" class="form-control" id="jam" name="jam" aria-describedby="emailHelp"   
      value="<?php echo $data['jam_pemeriksaan'] ?>" readonly>
  </div>
</div>




<div class="row">
  <div class="col">
      <label>Nama Transporter</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="nama_transporter" name="nama_transporter"
      value="<?php echo $data['nama_transporter'] ?>" aria-describedby="emailHelp" readonly>
  </div>
  <div class="col">
      <label>Jenis Kendaraan</label>
  </div>

  <div class="col">
      <input type="text" class="form-control" id="tipe_truck" name="tipe_truck"
      value="<?php echo $data['jenis_kendaraan'] ?>" aria-describedby="emailHelp"   readonly>
  </div>
</div>




<div class="row">
  <div class="col">
      <label>Petugas Pemeriksa</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="petugas" name="petugas" aria-describedby="emailHelp"   value="<?php echo $_SESSION[APP_NAME]["username"]; ?>" readonly>
  </div>
  <div class="col">
      <label>Lokasi Plant</label>
  </div>

  <div class="col">
      <input type="text" class="form-control text-uppercase" id="lokasi" name="lokasi" aria-describedby="emailHelp"   value="<?php echo $data['lokasi_pemeriksaan'] ?>" readonly>
  </div>
</div>


      

<div class="row">
  <div class="col">
      <label>Tujuan Pengiriman</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="tujuan" name="tujuan" aria-describedby="emailHelp"   value="<?php echo $data['tujuan_kirim'] ?>" readonly>
  </div>
  <div class="col">
      <label>Tahun Pembuatan</label>
  </div>

  <div class="col">
      <input type="text" class="form-control" id="tahun" name="tahun" aria-describedby="emailHelp"
      value="<?php echo $data['tahun_pembuatan'] ?>" readonly>
  </div>
</div>

<div class="row">
  <div class="col-md-1">
    <input type="hidden" class="form-control" id="code" name="code" aria-describedby="emailHelp"   value="<?php echo $kode; ?>" readonly>
  </div>
  <div class="col">
    
  </div>

</div>







<hr>
      <div class="row text-center text-white font-weight-bold" style="background-color: red">
        <div class="col">
        <h2><label style="color: white">Kelengkapan Utama</label></h2>
        </div>
      </div>
<hr>

      
     



<?php
          $result = mysqli_query($con,"SELECT *,CONCAT(name,no) as namee,CONCAT(ceklist_utama, no,no) as idgreen,CONCAT(ceklist_utama, no,no,no) as idred
            FROM tb_ceklist_utama WHERE no>4");
?>
          <?php
              $color='bg-danger';
              $no=1;
            while($row = mysqli_fetch_assoc($result))
             {
          ?>    
      
      
  <div class="row border text-center" style="background-color: red ">
        <div class="col" style="color: black; font-size:20px">
        
          <strong><?php echo "$row[ceklist_utama]"; ?></strong>
        </div>  
  </div> 
  <br>       
  <div class="row border text-center" style="background-color: "> 
        <div class="col" style="color: black; font-size:20px">    
          <label class="contain">
          <input type="checkbox" id="<?php echo "$row[idgreen]"; ?>" name="<?php echo "$row[namee]"; ?>" value=1 onchange="tambahan()">
          <span class="checkmark"></span>
          </label>
        </div>
  </div> 
  <hr> 
      <?php
       $no++;
        } 
      ?>   






<hr>


      <div class="row text-center text-white font-weight-bold" style="background-color: orange">
        <div class="col">
        <h2><label style="color: white">Kelengkapan Tambahan</label></h2>
        </div>
      </div>
<hr>

<?php
          $result = mysqli_query($con,"SELECT *,CONCAT(name,no) as namee,CONCAT(ceklist_tambahan, no,no) as idgreen,CONCAT(ceklist_tambahan, no,no,no) as idred
            FROM tb_ceklist_tambahan");
?>
          <?php
            while($row = mysqli_fetch_assoc($result))
             {
          ?>    
      

<div class="row border text-center" style="background-color: orange ">
        <div class="col" style="color: black; font-size:20px">        
          <strong><?php echo "$row[ceklist_tambahan]"; ?></strong>
        </div>
</div>
<br>       
<div class="row border text-center" style="background-color: "> 

        <div class="col" >
          <label class="contain">
          <input type="checkbox" id="<?php echo "$row[idgreen]"; ?>" name="<?php echo "$row[namee]"; ?>" value=1 onchange="tambahan()">
          <span class="checkmark"></span>
          </label>
        </div>
</div>  

      <?php
        } 
    ?>    





	<div class="row text-center bg-warning text-dark font-weight-bold" >
      	<label style="background-color: orange">Jika ada point Kelengkapan Tambahan tidak terpenuhi maka segera dilakukan tindakan perbaikan sesuai batas waktu yang telah ditentukan</label>
     </div>

<hr>

<div class="row">
<div class="col-md-12">
<div class="text-center bg-info text-dark font-weight-bold" >
<label>Hasil Pemeriksaan :</label>
</div>
</div>
</div>

<div class="row" >
    <div class="col-md-12">
    <div class="text-center font-weight-bold" >
    <div>
    <?php
    $color="bg-info";
    ?>
    <input type="text" class="form-control <?php echo "$color"; ?> text-white text-center font-weight-bold" id="hasil" name="hasil" readonly>
    </div>
    </div>
    </div>
</div>



<hr>

<div class="row">
<div class="col-md-12">
<div class="text-center bg-info text-dark font-weight-bold" >
<label>Komentar Kerusakan :</label>
</div>
</div>
</div>

<div class="row" >
		<div class="col-md-12">
		<div class="text-center font-weight-bold" >

		<div class="bg-white text-dark" >
		<!--<textarea class="form-control" rows="5" cols="100" id="komentar" name="komentar"></textarea>-->
    <input type="text" class="form-control" id="komentar" name="komentar" aria-describedby="emailHelp" >
		</div>

		</div>
		</div>
</div>

<hr>

<div class="row">
<div class="col-md-12">
<div class="text-center bg-info text-dark font-weight-bold" >
<label>Tindakan Perbaikan :</label>
</div>
</div>
</div>

<div class="row" >
		<div class="col-md-12">
		<div class="text-center font-weight-bold" >

		<div class="bg-white text-dark" >
		<!--<textarea rows="5" cols="100" id="tindakan"></textarea>-->
    <input type="text" class="form-control" id="tindakan" name="tindakan" aria-describedby="emailHelp" >
		</div>

		</div>
		</div>
</div>
<hr>
	   <div class="row">
      <input type="text" id="kode" name="kode" value="<?php echo $kode ?>" hidden>
      <input type="submit" value="Simpan" class="btn btn-primary center-block">
     </div>
</form>

<hr>
    </div>	

<br/>
<br/>

<style type="text/css">

    .contain {
        display: block;
        position: relative;
        padding-left: 0px;
        margin-bottom: 50px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .contain input {
        position: center;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark {
        position: absolute;
        top:0;
        left:45%;
        height: 50px;
        width: 50px;
        background-color: red;
    }

    /*.contain:hover input ~ .checkmark {
        background-color: #ccc;
    }*/

    .contain input:checked ~ .checkmark {
        background-color: green;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .contain input:checked ~ .checkmark:after {
        display: block;
    }

    .contain .checkmark:after {
        left:20px;
        top: 10px;
        width: 10px;
        height: 20px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
 
</style>

	
</body>
</html>
