<div class="body-wrap-with-navbar">

<?php
$kode_kirim=$_POST['kode_kirim'];
$muat = $_POST['muat'];




$date=date("Y-m-d");

$sql_nopol = mysqli_query($con2,"  SELECT * from tbl_pengiriman where kode_pengiriman='$kode_kirim' and tgl_kedatangan='$date'  ");

$count_nopol=mysqli_num_rows($sql_nopol); 
if ($count_nopol==0) {echo "<script>window.alert('Schedule Truck Tidak Ditemukan...!!!');

window.location='main?action=cek_nopol';

</script>";}




          while($rownopol = mysqli_fetch_assoc($sql_nopol)){
          $driver=$rownopol["driver_name"];
          $supplier=$rownopol["supplier_name"];
          $supplier_id=$rownopol["supplier_id"];
          $plant_name=$rownopol["plant_name"];
          $plant_id=$rownopol["plant_id"];
          $kode_kirim=$rownopol["kode_pengiriman"];
          $nopol=$rownopol["no_pol"];
          }

         


function get_date(){
                echo date("Y-m-d");
            }

function get_jam(){
                echo date("H:i:s");
            }

$jam=date("H:i:s");           


$idref=mktime();
$seq=1;
$username=$_SESSION[APP_NAME]["username"];


if ($count_nopol<>0) {
$query="INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username' , nopol='$nopol', nama_transporter='$supplier' , kode_transporter='$supplier_id', plant_id='$plant_id', plant_name='$plant_name', nama_sopir='$driver', tgl_pemeriksaan='$date', jam_pemeriksaan='$jam', lokasi_pemeriksaan='$plant_name', muatan='$muat', kode_kirim='$kode_kirim' ";

mysqli_query($con, $query);

}

//print_r($_SESSION);
//exit;
?>

<div class='container'>
<form method="post" action="main?action=N_gate1">

<div class="row justify-content-md-center">
  <div class="col">
        <label>No Polisi</label>
        <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" value="<?php echo $nopol; ?>" readonly >
  </div>
  </br>
  <div class="col">
        <label>Nama Sopir</label>
        <input type="text" class="form-control text-uppercase" id="driver" name="driver" value="<?php echo $driver; ?>" readonly>
  </div>
  </br>
  <div class="col">
      <label>Nama Supplier</label>
      <input type="text" class="form-control text-uppercase" id="supplier" name="supplier" value="<?php echo $supplier; ?>" readonly> 
  </div>
  </br>
  <!-- <div class="col">
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
  </br>-->
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
      <input type="text" class="form-control" id="petugas" name="petugas" value="<?php echo $_SESSION[APP_NAME]["username"]; ?>" readonly>
  </div>
  </br>
  <div class="col">
      <label>Lokasi Plant</label>
      <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo $plant_name; ?>" readonly>
  </div>
  </br>

  <input type="text" id="seq" name="seq" value="<?php echo $seq ?>" hidden>
  <input type="text" id="idref" name="idref" value="<?php echo $idref ?>" hidden>
  <input type="text" name="nopol" value="<?php echo $nopol ?>" hidden>
  <input type="text" name="kode_kirim" value="<?php echo $kode_kirim ?>" hidden>
  


</div>
<hr>
<div class="row">
<button type="submit" class="btn btn-success center-block">Go Ceklist</button>
  
</div>
<hr>


</form>
</div>


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