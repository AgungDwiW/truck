<div class="body-wrap-with-navbar">

<?php
$idref       = $_POST['idref'] ?? '';
$nopol       = $_POST['nopol'] ?? '';
$lokasi      = $_POST['lokasi'] ?? '';
$kode_kirim  = @$_POST['kode_kirim'];
$muat        = @$_POST['muat'];
$driver      = @$_POST['driver'];
$supplier    = @$_POST['supplier'];
$transporter = @$_POST['transporter'] ?? '';

$usia        = @$_POST['usia'];
$tipe_sim    = @$_POST['tipe_sim'];
$expired_sim = @$_POST['expired_sim'];
$expired_ddt = @$_POST['expired_ddt'];
$status_sim  = @$_POST['status_sim'];
$status_ddt  = @$_POST['status_ddt'];
$status_usia = @$_POST['status_usia'];
$id_barang   = @$_POST['id_barang'];
$tipe_truck  = '';

if ($usia == '')
  $usia = 0;

if($expired_sim == '')
  $expired_sim = '2999-12-30';

if($expired_ddt == '')
  $expired_ddt = '2999-12-30';

if ($muat=='FG') {

$seq = $_POST['seq'] ?? '';
$supplier = $_POST['supplier'] ?? '';
$driver = @$_POST['driver'];
$jam = $_POST['jam'] ?? '';
$date = $_POST['tgl'] ?? '';
$plant_id = $_POST['plant_id'] ?? '';

          $cari_tipe_truck = mysqli_query($con, "SELECT jenis_truck FROM tbm_tempat_muat where id_tempat_muat='$plant_id' and nama_supplier='$supplier' and nama_transporter='$transporter' group by nama_transporter limit 1   ");

          foreach ($cari_tipe_truck as $row){

            $tipe_truck=$row['jenis_truck'];

          }

$username=$_SESSION[APP_NAME]["username"];
$query="INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username' , nopol='$nopol', nama_supplier='$supplier', nama_transporter='$transporter' , jenis_kendaraan='$tipe_truck', plant_id='$plant_id', plant_name='$lokasi', nama_sopir='$driver', tgl_pemeriksaan='$date', jam_pemeriksaan='$jam', lokasi_pemeriksaan='$lokasi', muatan='$muat', usia='$usia', jenis_sim='$tipe_sim', expired_date_sim='$expired_sim', expired_date_ddt='$expired_ddt', status_sim='$status_sim', status_ddt='$status_ddt', status_usia='$status_usia', id_barang='$id_barang'  ";
mysqli_query($con, $query);

}

$petugas = $username ?? '';

/* ------------------------------------------------------------------
# TAMPILAN: mengikuti gaya branch main-coman (kartu putih, header berwarna,
# baris item dgn indikator OK / PERIKSA). Class utility-nya versi Bootstrap 4
# (font-weight-bold, h5, dst) karena app ini masih Bootstrap 4.1.3.
---------------------------------------------------------------------*/
?>

<style type="text/css">

body {
  background-color: #eef1f6;
}

.ng1-wrap {
  max-width: 900px;
  margin: 16px auto 40px;
}
.ng1-card {
  background: #fff;
  border: 1px solid #e2e6ee;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(20, 30, 60, .06);
  overflow: hidden;
  margin-bottom: 18px;
}
.ng1-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(90deg, #0f6ea8, #14a2b8);
  color: #fff;
  font-weight: 700;
  letter-spacing: .3px;
  padding: 14px 18px;
}
.ng1-hint {
  font-size: 12px;
  font-weight: 400;
  opacity: .85;
}
.ng1-body { padding: 0; }

.ng1-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 18px;
  border-bottom: 1px solid #eef1f6;
  border-left: 6px solid #ccd4e2;
  transition: background .15s ease;
}
.ng1-item:last-child { border-bottom: 0; }
.ng1-item:hover { background: #f7f9fc; }
.ng1-ok  { border-left-color: #28a745; background: #f5fbf7; }
.ng1-bad { border-left-color: #dc3545; background: #fdf6f7; }

.ng1-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
.ng1-num {
  flex: 0 0 auto;
  width: 26px;
  height: 26px;
  line-height: 26px;
  text-align: center;
  border-radius: 50%;
  background: #eef1f6;
  color: #495057;
  font-size: 13px;
  font-weight: 700;
}
.ng1-name { font-size: 16px; font-weight: 600; color: #212529; }
.ng1-right { display: flex; align-items: center; gap: 10px; }

.ng1-btn {
  width: 54px;
  height: 54px;
  border: 2px solid #cfd6e4;
  border-radius: 50%;
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 1px 4px rgba(20, 30, 60, .08);
  transition: transform .12s ease, border-color .12s ease;
}
.ng1-btn:hover { transform: translateY(-1px); border-color: #0f6ea8; }
.ng1-ok  .ng1-btn { border-color: #28a745; }
.ng1-bad .ng1-btn { border-color: #dc3545; }

.ng1-badge {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .4px;
  padding: 4px 10px;
  border-radius: 999px;
}
.ng1-ok  .ng1-badge { background: #e6f6ea; color: #1e7e34; }
.ng1-bad .ng1-badge { background: #fdeaec; color: #b02a37; }

.ng1-result label { font-weight: 600; color: #343a40; margin-bottom: 6px; }
.ng1-result .form-control { height: 48px; font-size: 16px; border-radius: 10px; }
.ng1-hasil {
  font-weight: 700;
  text-align: center;
  color: #fff;
  border: 0;
}
.ng1-hijau { background: #28a745; }
.ng1-merah { background: #dc3545; }
.ng1-save {
  border-radius: 10px;
  padding: 12px 40px;
  font-weight: 700;
}

</style>

<div class="container ng1-wrap">

  <div class="ng1-card">
    <div class="ng1-head">
      <span><i class="fa fa-truck"></i>&nbsp; Kelengkapan Utama</span>
      <span class="ng1-hint">klik tombol untuk memeriksa / foto item</span>
    </div>

    <div class="ng1-body">
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

$state = ($cek_utama==1) ? 'ng1-ok' : 'ng1-bad';
$label = ($cek_utama==1) ? 'OK' : 'PERIKSA';
?>
      <form method="post" action="common?action=N_foto_gate1" <?php echo $hid; ?>>
        <div class="ng1-item <?php echo $state; ?>">
          <div class="ng1-left">
            <span class="ng1-num"><?php echo $no-1; ?></span>
            <span class="ng1-name"><?php echo htmlspecialchars((string)$row['ceklist_utama'], ENT_QUOTES); ?></span>
          </div>
          <div class="ng1-right">
            <input type="text" name="utama" value="<?php echo $no-1; ?>" hidden >
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden >
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden >
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden >
            <input type="text" name="ceklist" value="<?php echo htmlspecialchars((string)$row['ceklist_utama'], ENT_QUOTES); ?>" hidden >
            <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden >
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden >

            <span class="ng1-badge"><?php echo $label; ?></span>
            <button type="submit" class="ng1-btn" title="Periksa: <?php echo htmlspecialchars((string)$row['ceklist_utama'], ENT_QUOTES); ?>">
              <img src="static/css/img/<?php echo $cek; ?>" width="34" height="34" alt="">
            </button>
          </div>
        </div>
      </form>
<?php
$no++;
}
?>
    </div>
  </div>

  <form method="post" action="common?action=simpan_gate1" id="formSimpan" class="ng1-result">
    <div class="ng1-card">
      <div class="ng1-head">
        <span><i class="fa fa-clipboard"></i>&nbsp; Hasil Pemeriksaan</span>
      </div>
      <div class="body" style="padding: 18px;">

        <div class="form-group">
          <label for="hasil">Hasil Pemeriksaan</label>
          <input type="text" class="form-control ng1-hasil <?php echo ($hasil=='Di Tolak di Pos 1') ? 'ng1-merah' : 'ng1-hijau'; ?>"
                 id="hasil" name="hasil" value="<?php echo htmlspecialchars((string)$hasil, ENT_QUOTES); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="komentar">Komentar Kerusakan</label>
          <input type="text" class="form-control" id="komentar" name="komentar" required >
        </div>

        <div class="form-group mb-0">
          <label for="tindakan">Tindakan Perbaikan</label>
          <input type="text" class="form-control" id="tindakan" name="tindakan" required >
        </div>

        <input type="text" name="idref" value="<?php echo $idref; ?>" hidden>
        <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden>
        <input type="text" name="petugas" value="<?php echo $petugas; ?>" hidden>
        <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden>
        <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden>
        <input type="text" name="driver" value="<?php echo $driver; ?>" hidden >
        <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden >

        <div class="text-center mt-4">
          <button type="submit" id="btnSimpan" class="btn btn-primary ng1-save">
            <i class="fa fa-save"></i> Simpan
          </button>
        </div>

      </div>
    </div>
  </form>

</div>

<script type="text/javascript">
/* tombol simpan kasih indikator proses (seperti branch main-coman) */
$(document).ready(function () {
  $('#formSimpan').on('submit', function () {
    $('#btnSimpan').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
  });
});
</script>
