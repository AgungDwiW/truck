<div class="body-wrap-with-navbar">

<?php
$muat        = $_POST['muat'] ?? '';
$nopol       = str_replace(' ', '', $_POST['nopol'] ?? '');
$id_shipment = $_POST['id_shipment'] ?? '';
$username    = $_SESSION[APP_NAME]["username"];

$date = date("Y-m-d");

$sql_username = mysqli_query($con, "  SELECT * from tbm_user where nama='$username' ");

while ($rowuser = mysqli_fetch_assoc($sql_username)) {
    $plant_name = $rowuser["plant_name"];
    $plant_id   = $rowuser["plant_id"];
}

function get_date() { echo date("Y-m-d"); }
function get_jam()  { echo date("H:i:s"); }

$jam   = date("H:i:s");
$idref = time();   // mktime() tanpa argumen = fatal di PHP 8 (ArgumentCountError)
$seq   = 1;
?>

<div class='container'>
<form method="post" action="main?action=N_gate1" class="kir-form">

  <h4 class="kir-title">Pemeriksaan Gate 1 &mdash; Finished Goods</h4>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label for="supplier">Nama Supplier <span class="kir-req">*</span></label>
      <select class="form-control text-uppercase" id="supplier" name="supplier" required>
        <option value=""></option>
<?php
$name_transporter = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat where id_tempat_muat='$plant_id' Group by nama_supplier   ");
$no = 1;
foreach ($name_transporter as $row) {
?>
        <option value="<?php echo $row['nama_supplier']; ?>"><?php echo $row['nama_supplier']; ?></option>
<?php
    $no++;
}
?>
      </select>
    </div>

    <div class="col-md-6 mb-3">
      <label for="transporter">Nama Transporter <span class="kir-req">*</span></label>
      <select class="form-control text-uppercase" id="transporter" name="transporter" required>
        <option value=""></option>
<?php
if ($plant_id == '90A8') {
    $name_transporter = mysqli_query($con_140, "SELECT planned_transporter_name FROM tbl_otm_upload GROUP BY planned_transporter_name asc");
    $no = 1;
    foreach ($name_transporter as $row) {
?>
        <option value="<?php echo $row['planned_transporter_name']; ?>"><?php echo $row['planned_transporter_name']; ?></option>
<?php
        $no++;
    }
}

if ($plant_id <> '90A8') {
    $name_transporter = mysqli_query($con, "SELECT nama_transporter FROM tbm_tempat_muat where id_tempat_muat='$plant_id' Group by nama_transporter   ");
    $no = 1;
    foreach ($name_transporter as $row) {
?>
        <option value="<?php echo $row['nama_transporter']; ?>"><?php echo $row['nama_transporter']; ?></option>
<?php
        $no++;
    }
}
?>
      </select>
    </div>
  </div>

  <h6 class="kir-sub">Data Pemeriksaan</h6>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label for="nopol">No Polisi</label>
      <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" value="<?php echo htmlspecialchars((string)$nopol, ENT_QUOTES); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label for="id_shipment_view">ID Shipment</label>
      <input type="text" class="form-control text-uppercase" id="id_shipment_view" value="<?php echo htmlspecialchars((string)$id_shipment, ENT_QUOTES); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label for="jam">Jam Pemeriksaan</label>
      <input type="text" class="form-control" id="jam" name="jam" value="<?php get_jam(); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label for="tgl">Tanggal Pemeriksaan</label>
      <input type="text" class="form-control text-uppercase" id="tgl" name="tgl" value="<?php get_date(); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label for="petugas">Petugas Pemeriksa</label>
      <input type="text" class="form-control" id="petugas" name="petugas" value="<?php echo htmlspecialchars((string)$_SESSION[APP_NAME]["username"], ENT_QUOTES); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label for="lokasi">Lokasi Plant</label>
      <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars((string)$plant_name, ENT_QUOTES); ?>" readonly>
    </div>
  </div>

  <input type="text" id="seq" name="seq" value="<?php echo $seq ?>" hidden>
  <input type="text" id="idref" name="idref" value="<?php echo $idref ?>" hidden>
  <input type="text" name="kode_kirim" value="" hidden>
  <input type="text" name="muat" value="<?php echo $muat ?>" hidden>
  <input type="text" name="plant_id" value="<?php echo $plant_id ?>" hidden>
  <input type="text" name="id_barang" value="<?php echo $id_shipment ?>" hidden>

  <div class="kir-actions">
    <button type="submit" class="btn btn-success btn-lg">Go Ceklist</button>
  </div>

</form>
</div>

<style type="text/css">

body {
  background-color: white;
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

/* --- layout form: 2 kolom di layar lebar, 1 kolom di layar kecil ---
   Sebelumnya semua field ada dalam SATU .row sehingga melebar sepanjang layar
   (8 kolom sempit, label terpotong). Sekarang tiap field col-md-6 + mb-3. */
.kir-form {
  max-width: 980px;
  margin: 0 auto 32px;
}
.kir-title {
  text-align: center;
  font-weight: 600;
  color: #222;
  margin: 18px 0 22px;
}
.kir-sub {
  text-transform: uppercase;
  letter-spacing: .6px;
  font-size: 12px;
  font-weight: 700;
  color: #888;
  border-bottom: 1px solid #e3e3e3;
  padding-bottom: 6px;
  margin: 4px 0 16px;
}
.kir-form label {
  font-weight: 600;
  color: #333;
  margin-bottom: 4px;
}
.kir-form .form-control {
  height: 46px;
  font-size: 16px;
}
.kir-form .form-control[readonly] {
  background: #f3f4f6;
  color: #212529;
}
.kir-req {
  color: #d9534f;
}
.kir-actions {
  text-align: center;
  margin: 12px 0 24px;
}
.kir-actions .btn {
  min-width: 260px;
  font-size: 18px;
  padding: 12px 28px;
}

</style>
