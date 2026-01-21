<div class="body-wrap-with-navbar">

<?php


$username=$_SESSION[APP_NAME]["username"];
$temp = mysqli_query($con,"SELECT idref, seq, nopol, petugas_pemeriksa, lokasi_pemeriksaan from tb_ceklist where petugas_pemeriksa='$username' order by tgbaca desc limit 1");
while($rowtemp = mysqli_fetch_assoc($temp)){
$idref=$rowtemp["idref"];
$seq=$rowtemp["seq"];
$nopol=$rowtemp["nopol"];
$petugas=$rowtemp["petugas_pemeriksa"];
$lokasi=$rowtemp["lokasi_pemeriksaan"];

}


// $seq=$_POST['seq'];
// $idref=$_POST['idref'];
$cek = mysqli_query($con,"SELECT seq from tb_ceklist where idref='$idref';");
while($row = mysqli_fetch_assoc($cek)){
$seq_cek=$row["seq"];
}



if ($seq_cek==1) {
$nopol=$_POST['nopol'];
$nama_sopir=$_POST['nama_sopir'];
$nama_transporter=$_POST['nama_transporter'];
$tipe_truck=$_POST['tipe_truck'];
$tujuan=$_POST['tujuan'];
$tahun=$_POST['tahun'];
$jam=$_POST['jam'];
$tgl=$_POST['tgl'];
$petugas=$_POST['petugas'];
$lokasi=$_POST['lokasi'];

$query="UPDATE tb_ceklist SET petugas_pemeriksa='$petugas',tujuan_kirim='$tujuan',nopol='$nopol',nama_transporter='$nama_transporter',nama_sopir='$nama_sopir',jenis_kendaraan='$tipe_truck',tahun_pembuatan='$tahun',tgl_pemeriksaan='$tgl',jam_pemeriksaan='$jam',lokasi_pemeriksaan='$lokasi', utama1='1', utama2='1', utama3='1', utama4='1', seq=0 where idref='$idref' ";

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
      
    <form method="post" action="main?action=foto_gate1">   
    <div class="row text-center" style="background-color: black">
        <div class="col" style="color: white; font-size:20px; margin-left: 10px; margin-top: 0px" <?php echo $hid; ?> >
          <?php echo "$row[ceklist_utama]"; ?>
          </div>
        <div class="col"<?php echo $hid; ?>>  
            <input type="text" id="ccp" name="ccp" value="<?php echo $no-1; ?>" hidden>
            <input type="text" id="utama<?php echo $no-1; ?>" name="utama" value="<?php echo $status; ?>" hidden> 
<?php
$cek1 = mysqli_query($con,"SELECT seq from tb_ceklist where idref='$idref';");
while($row1 = mysqli_fetch_assoc($cek1)){
$seq_cek1=$row1["seq"];
}
?>



            <input type="text" id="seq" name="seq" value="<?php echo $seq_cek1; ?>" hidden>
            <input type="text" id="idref" name="idref" value="<?php echo $idref; ?>" hidden>
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden>
            <input type="text" name="petugas" value="<?php echo $petugas; ?>" hidden>
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden>


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
    <input type="text" class="form-control" id="komentar" name="komentar" >
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
    <input type="text" class="form-control" id="tindakan" name="tindakan" >
    </div>

    </div>
    </div>
</div>

  <input type="text" name="idref" value="<?php echo $idref; ?>" hidden>
  <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden>
  <input type="text" name="petugas" value="<?php echo $petugas; ?>" hidden>
  <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden>



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
