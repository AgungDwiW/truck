<div class="body-wrap-with-navbar">

<?php
$kode_kirim=$_POST['kode_kirim'];
$muat = $_POST['muat'];

function printpre($str, $fl = -1){
	//function to print string for debug purpose
	//change $debug variable to enable
	//use second argument to override global debug variable
	global $debug;
	if ($fl == -1)
		$fl = $debug;

	if ($fl){
		// echo "";
		echo "<pre>";
		print_r ($str);
		echo "</pre>";	
	}
	
	return 0;
}

// printpre($_SESSION,1);
// exit();
// include_once "/concloud.php";
if ($_SESSION[APP_NAME]["username"] == 'Latihan Wonosobo 1'){
    $debug = 1;
}
// ===============================================================================================================
// ============================================================ SYNC ============================================
// ===============================================================================================================
// =========================== checking the credibility of on premise data ===================================
$result = mysqli_query($concloud, "SELECT * from tbl_pengiriman_combined where kode_pengiriman='{$kode_kirim}'");
$count_cloud        = mysqli_num_rows($result);
$pengiriman_cloud = mysqli_fetch_assoc($result);


$result = mysqli_query($conSL, "SELECT * from tbl_pengiriman where kode_pengiriman='{$kode_kirim}'");
$count_local        = mysqli_num_rows($result);
$pengiriman_local = mysqli_fetch_assoc($result);



// ------------checking pengiriman data end-------------------

// ------------checking item pengiriman data-------------------
$result = mysqli_query($concloud, "SELECT * from tbl_item_pengiriman where pengiriman_id='{$kode_kirim}'");
$item_cloud = [];

while ($row = mysqli_fetch_assoc($result)){
    $item_cloud [$row['kode_item_kirim']] = $row;

}
$result = mysqli_query($conSL, "SELECT * from tbl_item_pengiriman where pengiriman_id='{$kode_kirim}'");
$item_local = [];
while ($row = mysqli_fetch_assoc($result)){
    $item_local [$row['kode_item_kirim']] = $row;
}

printpre(['n'=>'cloud',  'pengiriman' => $pengiriman_cloud, 'item' =>array_keys($item_cloud)]);
printpre(['n'=>'local',  'pengiriman' => $pengiriman_local, 'item' =>array_keys($item_local)]);

printpre("aaaaaaaaaaaaaa");
//================================== syncing pengiriman ========================
if ($count_cloud!=$count_local){
    $pengiriman_synced = 1;    
    $col = array_keys($pengiriman_cloud);
    $values = array_values($pengiriman_cloud);
    $col = "`" . implode("`, `",$col) . "`"; 
    $val = "";
    foreach($values as $v){
        if ($v == '')
            $val .= "NULL, ";
        else
            $val .= "'$v', ";

    }
    $val = rtrim($val, ", ");
    $SQL = "INSERT into tbl_pengiriman ({$col}) VALUES({$val})";
    printpre($SQL);
    mysqli_query($conSL,$SQL);
    print_r(mysqli_error($conSL));
    
}
printpre($item_cloud);

foreach($item_cloud as $key => $row_cloud){
    
    if(!isset($item_local[$key])){
        $item_synced[] = $key;
        $col = array_keys($row_cloud);
        $val = array_values($row_cloud);
        $col = "`" . implode("`, `",$col) . "`"; 
        // $val = "'" . implode("', '",$val) . "'"; 
        $val_str= '';
        foreach($val as $item){
            if ($item == '')
                $val_str.='NULL,';
            else
                $val_str.="'{$item}',";
            
        }
        $val_str = rtrim($val_str, ",");
        
        // $val = rtrim($val, ",");
        $SQL = "REPLACE into tbl_item_pengiriman ({$col}) VALUES({$val_str})";
        printpre($SQL);
        mysqli_query($conSL,$SQL);
        printpre(mysqli_error($conSL));
        
    }
}
if ($_SESSION[APP_NAME]["username"] == 'Latihan Wonosobo 1'){
    exit();
}
// ===============================================================================================================
// ============================================================ SYNC ============================================
// ===============================================================================================================


$date=date("Y-m-d");

$sql_nopol = mysqli_query($con2,"  SELECT * from tbl_pengiriman where kode_pengiriman='$kode_kirim' and tgl_kedatangan='$date'  ");

$count_nopol=mysqli_num_rows($sql_nopol); 
if ($count_nopol==0) {
    echo "<script>window.alert('Schedule Truck Tidak Ditemukan...!!!');

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


$idref=time();
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