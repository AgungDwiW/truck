<div class="body-wrap-with-navbar">

<?php
// $nopol=$_POST['nopol'];
$muat = $_POST['muat'];
$nopol = str_replace(' ', '', $_POST['nopol'])  ;
$username=$_SESSION[APP_NAME]["username"];
$id_shipment=$_POST['id_shipment'];


$date=date("Y-m-d");

$sql_username = mysqli_query($con,"  SELECT * from tbm_user where nama='$username' ");


          while($rowuser = mysqli_fetch_assoc($sql_username)){
          $plant_name=$rowuser["plant_name"];
          $plant_id=$rowuser["plant_id"];

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


?>

<div class='container'>
<form method="post" action="main?action=N_gate1">

<div class="row justify-content-md-center">


<div class="col">
      <label>Nama Supplier</label>
      <!-- <input type="text" class="form-control text-uppercase" id="supplier" name="supplier" required>  -->

      <select class="form-control text-uppercase" id="supplier" name="supplier" required >
      <option value=""></option>
          
<?php



          
          $name_transporter = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat where id_tempat_muat='$plant_id' Group by nama_supplier   ");
          $no=1;
          foreach ($name_transporter as $row){
          ?>
           
           <option value="<?php echo $row['nama_supplier'];?>"><?php echo $row['nama_supplier'];?></option>
          <?php
          $no++;
          }



?>

       </select>

  </div>
  </br>



  
  <div class="col">
      <label>Nama Transporter</label>
      <!-- <input type="text" class="form-control text-uppercase" id="supplier" name="supplier" required>  -->

      <select class="form-control text-uppercase" id="transporter" name="transporter" required >
      <option value=""></option>
          
<?php
if ($plant_id=='90A8') {


          
          $name_transporter = mysqli_query($con_140, "SELECT planned_transporter_name FROM tbl_otm_upload GROUP BY planned_transporter_name asc");
          $no=1;
          foreach ($name_transporter as $row){
          ?>
           <option value="<?php echo $row['planned_transporter_name'];?>"><?php echo $row['planned_transporter_name'];?></option>
          <?php
          $no++;
          }
          

}


if ($plant_id<>'90A8') {


          
          $name_transporter = mysqli_query($con, "SELECT nama_transporter FROM tbm_tempat_muat where id_tempat_muat='$plant_id' Group by nama_transporter   ");
          $no=1;

          foreach ($name_transporter as $row){
          ?>
           
           <option value="<?php echo $row['nama_transporter'];?>"><?php echo $row['nama_transporter'];?></option>
          <?php
          $no++;
          }

}

?>

       </select>

  </div>
  </br>




  <div class="col">
        <label>No Polisi</label>
        <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" value="<?php echo $nopol; ?>" readonly >
  </div>
  </br>
  <div class="col">
        <label>ID Shipment</label>
        <input type="text" class="form-control text-uppercase" value="<?php echo $id_shipment; ?>" readonly  >
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
  <input type="text" name="kode_kirim" value="" hidden>
  <input type="text" name="muat" value="<?php echo $muat ?>" hidden>
  <input type="text" name="plant_id" value="<?php echo $plant_id ?>" hidden>
  <input type="text" name="id_barang" value="<?php echo $id_shipment ?>" hidden>
  


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