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


if ($id_shipment<>'trial') {
                  

$sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where no_pol='$nopol' order by tanggal_datang desc limit 1");

$count_nopol=mysqli_num_rows($sql_nop); 
if ($count_nopol==0) {echo "<script>window.alert('No Pol belum di input di e_Visitor...!!!');

window.location='main?action=FG_cek_nopol';

</script>";}

foreach ($sql_nop as $row_nop){
$nama_sopir=$row_nop['nama_visitor'];
$seq_visitor=$row_nop['seq_visitor'];
$id_barang=$row_nop['id_barang'];

}
}

if ($id_shipment=='trial') {
$nama_sopir='Trial Driver';
$seq_visitor='seq_trial';
$id_barang='Muat Trial';

}




      // if ($plant_id=='90A8') {


      // $sql_shipment=mysqli_query($con_140,"SELECT * from tbl_otm_upload where shipment_id='$id_shipment' ");

      // $count_shipment=mysqli_num_rows($sql_shipment); 
      // if ($count_shipment==0) {echo "<script>window.alert('ID SHIPMENT Tidak Ditemukan...!!!');

      // window.location='main?action=FG_cek_nopol';

      // </script>";}

      // }




if ($seq_visitor<>'seq_trial') {


$sql_seq_visitor=mysqli_query($con_3,"SELECT * from tbm_visitor where seq='$seq_visitor' ");
foreach ($sql_seq_visitor as $row_visitor){
$tgl_lahir=$row_visitor['tanggal_lahir'];
$valid=$row_visitor['valid_id_date'];
$tipe_sim=$row_visitor['tipe_id'];
$valid_ddt=$row_visitor['valid_ddt_date'];

}

}

if ($seq_visitor=='seq_trial') {
$tgl_lahir='1987-03-17';
$valid='2022-03-17';
$tipe_sim='SIM B2';
$valid_ddt='2020-12-31';


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


// if ($umur<17) {echo "<script>window.alert('Tanggal Lahir Sopir di e_Visitor Salah...!!!');

// window.location='main?action=FG_cek_nopol';

// </script>";}


         


function get_date(){
                echo date("Y-m-d");
            }

function get_jam(){
                echo date("H:i:s");
            }

$jam=date("H:i:s");           


$idref=mktime();
$seq=1;

//$query="INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username' , nopol='$nopol', nama_transporter='$supplier' , kode_transporter='$supplier_id', plant_id='$plant_id', plant_name='$plant_name', nama_sopir='$driver', tgl_pemeriksaan='$date', jam_pemeriksaan='$jam', lokasi_pemeriksaan='$plant_name', muatan='$muat' ";

//mysqli_query($con, $query);

//print_r($_SESSION);
//exit;
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



          
          $name_transporter = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat where id_tempat_muat='$plant_id' Group by nama_transporter   ");
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
        <label>Nama Sopir</label>
        <input type="text" class="form-control text-uppercase" id="driver" name="driver" value="<?php echo $nama_sopir; ?>"  >
  </div>
  </br>
 

      
  <div class="col">  
        <label>Usia</label>
       <strong><input type="text" class="form-control" style="background-color: <?php echo $color_usia; ?>; color: <?php echo $color_text; ?>    "  value="<?php echo $umur.' '. 'Tahun'; ?>" readonly></strong>

       <input type="text"  id="usia" name="usia" value="<?php echo $umur; ?>" hidden>
       <input type="text"  id="status_usia" name="status_usia" value="<?php echo $status_usia; ?>" hidden>


  </div>
  </br>

  <div class="col">  
        <label>Jenis SIM</label>
        <input type="text" class="form-control" id="tipe_sim" name="tipe_sim" value="<?php echo $tipe_sim; ?>" readonly>



  </div>
  </br>

  <div class="col">  
        <label>Masa Berlaku SIM</label>
        <input type="text" class="form-control" style="background-color: <?php echo $color_expired; ?>; color: <?php echo $color_text_sim; ?>" value="<?php echo $valid_date; ?>"  readonly>
        <input type="text"  id="expired_sim" name="expired_sim" value="<?php echo $valid; ?>" hidden>
        <input type="text"  id="status_sim" name="status_sim" value="<?php echo $status_sim; ?>" hidden>
  </div>
  </br>

  <div class="col">  
        <label>Masa Berlaku ID DDT</label>
        <input type="text" class="form-control" style="background-color: <?php echo $color_expired_ddt; ?>; color: <?php echo $color_text_ddt; ?>" value="<?php echo $valid_date_ddt; ?>"  readonly>
        <input type="text"  id="expired_ddt" name="expired_ddt" value="<?php echo $valid_ddt; ?>" hidden>
        <input type="text"  id="status_ddt" name="status_ddt" value="<?php echo $status_ddt; ?>" hidden>
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
  <input type="text" name="kode_kirim" value="<?php echo $kode_kirim ?>" hidden>
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