<?php
include_once "application/config/connection.php";
function get_date(){
                echo date("d-m-Y");
            }

function get_jam(){
                echo date("H:i:s");
            }


$idref=mktime();
$seq=1;
$username=User::$username;
$query="INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username'";

mysqli_query($con, $query);

//print_r($_SESSION);
//exit;
?>



<style type="text/css">
  
body {
  background-color:white;
}
.fileUpload {
    position: relative;
    overflow: hidden;
    margin: 10px;
}
.fileUpload input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
}
</style>

<div class='container'>
<form method="post" action="main?action=gate1">

<div class="row justify-content-md-center">
  <div class="col">
        <label>No Polisi</label>
        <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" required >
  </div>
  </br>
  <div class="col">
        <label>Nama Sopir</label>
        <input type="text" class="form-control text-uppercase" id="nama_sopir" name="nama_sopir" required>
  </div>
  </br>
  <div class="col">
      <label>Nama Transporter</label>
      <input type="text" class="form-control text-uppercase" id="nama_transporter" name="nama_transporter" required>
  </div>
  </br>
  <div class="col">
      <label>Jenis Kendaraan</label>
      <input type="text" class="form-control" id="tipe_truck" name="tipe_truck" required>
  </div>
  </br>
  <div class="col">
      <label>Tujuan Pengiriman</label>
      <select class="form-control" id="tujuan" name="tujuan">
          <?php
          $tujuan = mysqli_query($con, "SELECT * from tujuan_pengiriman order by tujuan asc");
          $no=1;
          foreach ($tujuan as $row){
          ?>
           <option value="<?php echo $row['tujuan'];?>"><?php echo $row['tujuan'];?></option>
          <?php
          $no++;
          }
          ?>
        </select>
  </div>
  </br>
  <div class="col">
      <label>Tahun Pembuatan</label>
      <input type="text" class="form-control" id="tahun" name="tahun">
  </div>
  </br>
  <div class="col">
        <label>Jam Pemeriksaan</label>
        <input type="text" class="form-control" id="jam" name="jam" aria-describedby="emailHelp"   value="<?php get_jam(); ?>" readonly>
  </div>
  </br>
  <div class="col">
        <label>Tanggal Pemeriksaan</label>
        <input type="text" class="form-control text-uppercase" id="tgl" name="tgl" value="<?php get_date(); ?>" readonly>
  </div>
  </br>
  <div class="col">
      <label>Petugas Pemeriksa</label>
      <input type="text" class="form-control" id="petugas" name="petugas" value="<?php echo User::$username; ?>" readonly>
  </div>
  </br>
  <div class="col">
      <label>Lokasi Plant</label>
      <input type="text" class="form-control" id="lokasi" name="lokasi" value="Pandaan" readonly>
  </div>
  </br>

  <input type="text" id="seq" name="seq" value="<?php echo $seq ?>" hidden>
  <input type="text" id="idref" name="idref" value="<?php echo $idref ?>" hidden>
  


</div>
<hr>
<div class="row">
<button type="submit" class="btn btn-success center-block">Go Ceklist</button>
  
</div>
<hr>


</form>
</div>
