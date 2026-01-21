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
$datamuat = mysqli_fetch_array($query_mysql);

$muat=$datamuat['muatan'];




$username=$_SESSION[APP_NAME]["username"];
$sql_username = mysqli_query($con,"  SELECT * from tbm_user where nama='$username' ");


          while($rowuser = mysqli_fetch_assoc($sql_username)){
          $plant_name=$rowuser["plant_name"];
          $plant_id=$rowuser["plant_id"];

          }



if ($muat=='FG') {

$query_mysql = mysqli_query($con,"SELECT * FROM tb_ceklist WHERE no='$kode'")or die(mysql_error());
while($row = mysqli_fetch_assoc($query_mysql)){
          $nopol=$row["nopol"];
          }

$date=date("Y-m-d");

$sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where REPLACE(no_pol,' ','')='$nopol' ORDER BY tanggal_datang DESC LIMIT 1");
//$sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where REPLACE(no_pol,' ','')='$nopol' AND DATE(tanggal_datang)='$date' LIMIT 1");


//$sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where no_pol='$nopol' order by tanggal_datang desc limit 1");


$count_nopol=mysqli_num_rows($sql_nop); 
if ($count_nopol==0) {echo "<script>window.alert('No Pol belum di input di e_Visitor...!!!');

window.location='main?action=index';

</script>";}

foreach ($sql_nop as $row_nop){
$nama_sopir=$row_nop['nama_visitor'];
$seq_visitor=$row_nop['seq_visitor'];
$id_barang=$row_nop['id_barang'];

}


$sql_seq_visitor=mysqli_query($con_3,"SELECT * from tbm_visitor where seq='$seq_visitor' ");
foreach ($sql_seq_visitor as $row_visitor){
$tgl_lahir=$row_visitor['tanggal_lahir'];
$valid=$row_visitor['valid_id_date'];
$tipe_sim=$row_visitor['tipe_id'];
$valid_ddt=$row_visitor['valid_ddt_date'];

}





$lahir= new DateTime($tgl_lahir);
$val= new DateTime($valid);
$val_ddt= new DateTime($valid_ddt);
$today= new DateTime();

$diff=$today -> diff($lahir);
$umur= $diff -> y;



$year_today= date("Y");
$year_expired= date_format($val, 'Y');
$year_diff=$year_expired-$year_today;

$year_expired_ddt= date_format($val_ddt, 'Y');
$year_diff_ddt=$year_expired_ddt-$year_today;





$month_today= date("m");
$month_expired= date_format($val, 'm');
$month_diff=$month_expired-$month_today;

$month_expired_ddt= date_format($val_ddt, 'm');
$month_diff_ddt=$month_expired_ddt-$month_today;



$day_today= date("d");
$day_expired= date_format($val, 'd');
$day_diff=$day_expired-$day_today;

$day_expired_ddt= date_format($val_ddt, 'd');
$day_diff_ddt=$day_expired_ddt-$day_today;






$valid_date='SIM Masih Berlaku'; $color_expired='green';  $color_text_sim='white';
$valid_date_ddt='ID DDT Masih Berlaku'; $color_expired_ddt='green';  $color_text_ddt='white';

if ($year_diff<0) {$valid_date='SIM Sudah Kadaluwarsa'; $color_expired='red';  $color_text_sim='white';}


if ($year_diff==0 and $month_diff<0) {$valid_date='SIM Sudah Kadaluwarsa'; $color_expired='red';  $color_text_sim='white';}
if ($year_diff==0 and $month_diff==0 and $day_diff<0 ) {$valid_date='SIM Sudah Kadaluwarsa'; $color_expired='red';  $color_text_sim='white';}


if ($year_diff_ddt<0) {$valid_date_ddt='ID DDT Sudah Kadaluwarsa'; $color_expired_ddt='red';  $color_text_ddt='white';}
if ($year_diff_ddt==0 and $month_diff_ddt<0) {$valid_date_ddt='ID DDT Sudah Kadaluwarsa'; $color_expired_ddt='red';  $color_text_ddt='white';}
if ($year_diff_ddt==0 and $month_diff_ddt==0 and $day_diff_ddt<0 ) {$valid_date_ddt='ID DDT Sudah Kadaluwarsa'; $color_expired_ddt='red';  $color_text_ddt='white';}





//$valid_date=$valid_date.' '.'('.$valid.')';

$status_sim=$valid_date;
$status_ddt=$valid_date_ddt;

$valid_date=$valid_date.' '.'||'.' '.'Expired Date :'.' '.$valid;
$valid_date_ddt=$valid_date_ddt.' '.'||'.' '.'Expired Date :'.' '.$valid_ddt;







if ($umur<=55) {$color_usia='green'; $color_text='white'; $status_usia='Low Risk';}
if ($umur>55 and $umur <=60) {$color_usia='yellow';$color_text='black'; $status_usia='Medium Risk';}
if ($umur>60) {$color_usia='red'; $color_text='black'; $status_usia='High Risk';}





$query="UPDATE tb_ceklist SET nama_sopir='$nama_sopir', usia='$umur', jenis_sim='$tipe_sim', expired_date_sim='$valid', expired_date_ddt='$valid_ddt', status_sim='$status_sim', status_ddt='$status_ddt', status_usia='$status_usia'  WHERE no='$kode'  ";

mysqli_query($con, $query);

}




$query_mysql = mysqli_query($con,"SELECT * FROM tb_ceklist WHERE no='$kode'")or die(mysql_error());
$data = mysqli_fetch_array($query_mysql);



?>





<?php

if ($muat=='FG') {

?>



<div class="container">
    <form method="post" action="main?action=lanjut_gate2">
<div class="row">

  <div class="col">
        <label>ID Shipment</label>
  </div>
  <div class="col">
      <input type="text" class="form-control text-uppercase" id="kode_kirim" name="kode_kirim" 
      value="<?php echo $data['id_barang'] ?>" aria-describedby="emailHelp" readonly>
  </div>


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
      value="<?php echo $nama_sopir ?>" aria-describedby="emailHelp" readonly>
  </div>


  <div class="col">  
        <label>Usia</label>
       <strong><input type="text" class="form-control" style="background-color: <?php echo $color_usia; ?>; color: <?php echo $color_text; ?>    "  value="<?php echo $umur.' '. 'Tahun'; ?>" readonly></strong>

       <input type="text"  id="usia" name="usia" value="<?php echo $umur; ?>" hidden>
       <input type="text"  id="status_usia" name="status_usia" value="<?php echo $status_usia; ?>" hidden>


  </div>

  <div class="col">  
        <label>Jenis SIM</label>
        <input type="text" class="form-control" id="tipe_sim" name="tipe_sim" value="<?php echo $tipe_sim; ?>" readonly>

  </div>

  <div class="col">  
        <label>Masa Berlaku SIM</label>
        <input type="text" class="form-control" style="background-color: <?php echo $color_expired; ?>; color: <?php echo $color_text_sim; ?>" value="<?php echo $valid_date; ?>"  readonly>
        <input type="text"  id="expired_sim" name="expired_sim" value="<?php echo $valid; ?>" hidden>
        <input type="text"  id="status_sim" name="status_sim" value="<?php echo $status_sim; ?>" hidden>
  </div>

  <div class="col">  
        <label>Masa Berlaku ID DDT</label>
        <input type="text" class="form-control" style="background-color: <?php echo $color_expired_ddt; ?>; color: <?php echo $color_text_ddt; ?>" value="<?php echo $valid_date_ddt; ?>"  readonly>
        <input type="text"  id="expired_ddt" name="expired_ddt" value="<?php echo $valid_ddt; ?>" hidden>
        <input type="text"  id="status_ddt" name="status_ddt" value="<?php echo $status_ddt; ?>" hidden>
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



<?php
}
?>
 

<?php

if ($muat=='Material') {

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



<?php
}
?>









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
    <input type="text" class="form-control" id="komentar" name="komentar" aria-describedby="emailHelp" required >
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
    <input type="text" class="form-control" id="tindakan" name="tindakan" aria-describedby="emailHelp" required >
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
