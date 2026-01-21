<div class="body-wrap-with-navbar">

<?php
$idref=$_POST['idref'];
$nopol=$_POST['nopol'];
$lokasi=$_POST['lokasi'];
$kode_kirim=@$_POST['kode_kirim'];
$muat = @$_POST['muat'];
$driver = @$_POST['driver'];
$supplier = @$_POST['supplier'];
$transporter = @$_POST['transporter'];

$usia = @$_POST['usia'];
$tipe_sim = @$_POST['tipe_sim'];
$expired_sim = @$_POST['expired_sim'];
$expired_ddt = @$_POST['expired_ddt'];
$status_sim = @$_POST['status_sim'];
$status_ddt = @$_POST['status_ddt'];
$status_usia = @$_POST['status_usia'];
$id_barang = @$_POST['id_barang'];
$tipe_truck='';

if ($usia == '')
  $usia = 0;

if($expired_sim == '')
  $expired_sim = '2999-12-30';

if($expired_ddt == '')
  $expired_ddt = '2999-12-30';

if ($muat=='FG') {

$seq = $_POST['seq'];
$supplier = $_POST['supplier'];
$driver = @$_POST['driver'];
$jam = $_POST['jam'];
$date = $_POST['tgl'];
$plant_id = $_POST['plant_id'];  

          $cari_tipe_truck = mysqli_query($con, "SELECT jenis_truck FROM tbm_tempat_muat where id_tempat_muat='$plant_id' and nama_supplier='$supplier' and nama_transporter='$transporter' group by nama_transporter limit 1   ");
          
          foreach ($cari_tipe_truck as $row){
          
            $tipe_truck=$row['jenis_truck'];
          
          }




$username=$_SESSION[APP_NAME]["username"];
$query="INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username' , nopol='$nopol', nama_supplier='$supplier', nama_transporter='$transporter' , jenis_kendaraan='$tipe_truck', plant_id='$plant_id', plant_name='$lokasi', nama_sopir='$driver', tgl_pemeriksaan='$date', jam_pemeriksaan='$jam', lokasi_pemeriksaan='$lokasi', muatan='$muat', usia='$usia', jenis_sim='$tipe_sim', expired_date_sim='$expired_sim', expired_date_ddt='$expired_ddt', status_sim='$status_sim', status_ddt='$status_ddt', status_usia='$status_usia', id_barang='$id_barang'  ";
// echo "<pre>";
// print_r($query);
// echo "</pre>";
mysqli_query($con, $query);

}





?>

<style type="text/css">
  
body {
  background-color:transparent;
}
</style>

<div class="row text-center" style="background-color: red" >
        <div class="col">
        <label style="font-size: 30px;">Kelengkapan Utama</label>
        </div>
      </div>

      
     



<?php
mysqli_query($con,"UPDATE tb_ceklist_utama SET  status_temp=1");
            $result = mysqli_query($con,"SELECT *,CONCAT(name,no) as namee,CONCAT(ceklist_utama, no,no) as idgreen,CONCAT(ceklist_utama, no,no,no) as idred
            FROM tb_ceklist_utama WHERE no BETWEEN 0 AND 4;");

              $color='bg-dark';
              $no=1;
              $noo=2;
              $hid='hidden';
              $hasil='Lanjut Pemeriksaan Gate 2';
              $cek_utama=1;
            while($row = mysqli_fetch_assoc($result))
              
             {
              $noo=$no-1; 
              if ($no>1) {$hid='';}
              if ($no>1) {
              $utama = mysqli_query($con,"SELECT utama".$noo." from tb_ceklist where idref='$idref';");
        while($row_utama = mysqli_fetch_assoc($utama)){
        $cek_utama=$row_utama["utama".$noo.""];
        }
              }


              if ($cek_utama==1) {$cek='cekgreen.png';}
              if ($cek_utama==0) {$cek='red.png'; $hasil='Di Tolak di Pos 1';}  
              



?>    
      
    <form method="post" action="main?action=N_foto_gate1">   
    <div class="row text-center" style="background-color: black">
        <div class="col" style="color: white; font-size:20px; margin-left: 10px; margin-top: 0px" <?php echo $hid; ?> >
          <?php echo "$row[ceklist_utama]"; ?>
          </div>
        <div class="col"<?php echo $hid; ?>>  
            <input type="text" name="utama" value="<?php echo $no-1; ?>" hidden > 
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden >
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden >
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden >
            <input type="text" name="ceklist" value="<?php echo "$row[ceklist_utama]"; ?>" hidden >
            <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden >
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden >

            <button type="submit" style="margin-left: 10px; margin-bottom: 0px" ><img src="static/css/img/<?php echo $cek; ?>" width="30" height="30"></button>

        </div>

    </div>  
    </form>
<hr>

      <?php
       $no++;
        } 
      ?>    
  

<form method="post" action="main?action=simpan_gate1">
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
    <input type="text" class="form-control <?php echo "$color"; ?> text-white text-center font-weight-bold" id="hasil" name="hasil" value="<?php echo "$hasil"; ?>" readonly>
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
    <input type="text" class="form-control" id="komentar" name="komentar" required >
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
    <input type="text" class="form-control" id="tindakan" name="tindakan" required >
    </div>

    </div>
    </div>
</div>

  <input type="text" name="idref" value="<?php echo $idref; ?>" hidden>
  <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden>
  <input type="text" name="petugas" value="<?php echo $petugas; ?>" hidden>
  <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden>
  <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden>
  <input type="text" name="driver" value="<?php echo $driver; ?>" hidden >
  <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden >



<hr>
     <div class="row">
      <button type="submit" class="btn btn-primary center-block">Simpan</button>
     </div>
<hr>
    </div>  

  
</form>









  

  <br/>
  <br/>

<style type="text/css">

    .contain {
        display: block;
        position: relative;
        padding-left: 100px;
        margin-bottom: 50px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .contain input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkmark {
        position: absolute;
        top:0;
        left:0;
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


<style type="text/css">
  




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


.icon_camera > input
{
  display:none;
  left: 3px

}

div.relative {
    position: relative;
    left: 4px;
    top: 23px;
  }

div.cekmark {
    left: 0px;
  }




</style>    




  
</body>
</html>
