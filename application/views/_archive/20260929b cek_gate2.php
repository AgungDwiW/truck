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

window.location='common?action=index';

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
/* ============================================================================
# 4. VIEW — pemeriksaan Gate 2 (ringkasan + checklist kelengkapan)
#    Gaya disamakan dengan N_gate1: latar abu terang, kartu putih, header
#    gradien, badge, kotak hasil hijau/merah, tombol simpan modern.
#    Nama/id field TIDAK diubah supaya POST ke common?action=lanjut_gate2
#    tetap sama seperti sebelumnya (termasuk checkbox untuk tambahan()).
============================================================================ */
$cg2_fg    = ($muat === 'FG');
$cg2_nopol = (string) $data['nopol'];
$cg2_pill  = array('green' => 'cg2-badge-green', 'yellow' => 'cg2-badge-yellow', 'red' => 'cg2-badge-red');
$cg2_kelas_usia = isset($cg2_pill[$color_usia])        ? $cg2_pill[$color_usia]        : 'cg2-badge-green';
$cg2_kelas_sim  = isset($cg2_pill[$color_expired])     ? $cg2_pill[$color_expired]     : 'cg2-badge-green';
$cg2_kelas_ddt  = isset($cg2_pill[$color_expired_ddt]) ? $cg2_pill[$color_expired_ddt] : 'cg2-badge-green';
$cg2_sopir = $cg2_fg ? (string) $nama_sopir : (string) $data['nama_sopir'];

/* teks pil dipendekkan (buang awalan "SIM "/"ID DDT ") supaya tidak
   terpotong di kolom grid yang sempit. Nilai aslinya tetap dikirim lewat
   input hidden status_sim / status_ddt. */
$cg2_sim_txt = trim(str_replace(array('SIM ', 'ID DDT '), '', (string) $status_sim));
$cg2_ddt_txt = trim(str_replace(array('SIM ', 'ID DDT '), '', (string) $status_ddt));
if ($cg2_sim_txt === '') { $cg2_sim_txt = (string) $status_sim; }
if ($cg2_ddt_txt === '') { $cg2_ddt_txt = (string) $status_ddt; }
?>

<style type="text/css">
/* ---- cek_gate2 (cg2-*) : samakan dengan gaya N_gate1 ---- */
/* Paksa latar terang halaman ini saja; aturan body di blok <style> paling atas
   file ini menang karena ditulis lebih dulu. */
body { background-color: #eef1f6; }

.cg2-wrap { max-width: 1000px; margin: 16px auto 40px; }
.cg2-card {
  background: #fff;
  border: 1px solid #e2e6ee;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(20, 30, 60, .06);
  overflow: hidden;
  margin-bottom: 18px;
}
.cg2-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: linear-gradient(90deg, #0f6ea8, #14a2b8);
  color: #fff;
  font-weight: 700;
  letter-spacing: .3px;
  padding: 14px 18px;
}
.cg2-head-utama   { background: linear-gradient(90deg, #0f6ea8, #14a2b8); }
.cg2-head-tambahan{ background: linear-gradient(90deg, #b06a00, #e8a33d); }
.cg2-head-hasil   { background: linear-gradient(90deg, #155724, #28a745); }
.cg2-hint { font-size: 12px; font-weight: 400; opacity: .9; }
.cg2-chip {
  background: rgba(255, 255, 255, .22);
  border-radius: 999px;
  padding: 3px 12px;
  font-size: 12px;
  font-weight: 700;
}
.cg2-nopol { font-weight: 700; letter-spacing: .06em; margin-right: 8px; }
.cg2-body { padding: 16px 18px; }

.cg2-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
  gap: 12px 16px;
}
.cg2-field { margin-bottom: 12px; }
.cg2-field label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  color: #6b7280;
  margin-bottom: 5px;
}
.cg2-body .form-control { border-radius: 10px; height: 44px; }
.cg2-ro { background: #f8fafd; font-weight: 600; color: #1f2937; }
.cg2-badge-wrap { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; min-height: 44px; }
.cg2-mini { font-size: 12px; color: #6b7280; }

.cg2-badge {
  display: inline-block;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 700;
}
.cg2-badge-green  { background: #e6f6ea; color: #1e7e34; }
.cg2-badge-yellow { background: #fff4e0; color: #9a5b00; }
.cg2-badge-red    { background: #fdeaec; color: #b02a37; }

.cg2-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 2px;
  border-bottom: 1px solid #eef1f6;
}
.cg2-item:last-child { border-bottom: 0; }
.cg2-item-name { font-size: 15px; font-weight: 600; color: #212529; }
.cg2-num {
  display: inline-block;
  width: 26px;
  height: 26px;
  line-height: 26px;
  text-align: center;
  border-radius: 50%;
  background: #eef1f6;
  color: #495057;
  font-size: 12px;
  font-weight: 700;
  margin-right: 9px;
}

/* satu kontrol checklist per item. Tanpa JS tetap terkirim sebagai checkbox
   biasa (nama sama seperti sebelumnya), jadi alur lama tidak berubah. */
.cg2-chk { position: relative; display: inline-block; margin: 0; cursor: pointer; }
.cg2-chk input { position: absolute; opacity: 0; width: 0; height: 0; }
.cg2-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 52px;
  border: 2px solid #dc3545;
  border-radius: 14px;
  background: #fff;
  color: #dc3545;
  font-size: 22px;
  box-shadow: 0 1px 4px rgba(20, 30, 60, .08);
  transition: transform .12s ease, background .12s ease, border-color .12s ease, color .12s ease;
}
.cg2-chk:hover .cg2-box { transform: translateY(-1px); }
.cg2-chk .cg2-i-ok { display: none; }
.cg2-chk input:checked ~ .cg2-box { background: #28a745; border-color: #28a745; color: #fff; }
.cg2-chk input:checked ~ .cg2-box .cg2-i-no { display: none; }
.cg2-chk input:checked ~ .cg2-box .cg2-i-ok { display: inline; }

.cg2-hasil {
  font-weight: 700;
  text-align: center;
  color: #fff;
  border: 0;
  font-size: 16px;
}
/* Bootstrap menang atas satu class (.form-control[readonly] 0,2,0) -> pakai
   selektor dua class supaya warnanya benar-benar terpakai. */
.cg2-body .cg2-hasil.cg2-hijau  { background-color: #28a745; color: #fff; }
.cg2-body .cg2-hasil.cg2-kuning { background-color: #e8a33d; color: #fff; }
.cg2-body .cg2-hasil.cg2-merah  { background-color: #dc3545; color: #fff; }
.cg2-body .cg2-hasil.cg2-biru   { background-color: #0f6ea8; color: #fff; }
.cg2-note { font-size: 12px; color: #6b7280; margin: -2px 0 14px; }
.cg2-actions { padding: 6px 0 2px; }
.cg2-save {
  background: #0f6ea8;
  color: #fff;
  border: 0;
  border-radius: 10px;
  padding: 12px 40px;
  font-weight: 700;
}
.cg2-save:hover, .cg2-save:focus { background: #0b557f; color: #fff; }
</style>

<div class="container cg2-wrap">

  <form method="post" action="common?action=lanjut_gate2" id="formGate2">

    <div class="cg2-card">
      <div class="cg2-head">
        <span><i class="fa fa-clipboard"></i>&nbsp; Pemeriksaan Gate 2</span>
        <span>
          <span class="cg2-nopol"><?php echo htmlspecialchars($cg2_nopol, ENT_QUOTES); ?></span>
          <span class="cg2-chip"><?php echo htmlspecialchars((string) $muat, ENT_QUOTES); ?></span>
        </span>
      </div>
      <div class="cg2-body">
        <div class="cg2-grid">

<?php if ($cg2_fg) { ?>
          <div class="cg2-field">
            <label for="kode_kirim">ID Shipment</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="kode_kirim" name="kode_kirim" value="<?php echo htmlspecialchars((string) $data['id_barang'], ENT_QUOTES); ?>" readonly>
          </div>
<?php } ?>

          <div class="cg2-field">
            <label for="nopol">No Polisi</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="nopol" name="nopol" value="<?php echo htmlspecialchars((string) $data['nopol'], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="tgl">Tanggal Pemeriksaan</label>
            <input type="text" class="form-control cg2-ro" id="tgl" name="tgl" value="<?php echo htmlspecialchars((string) $data['tgl_pemeriksaan'], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="jam">Jam Pemeriksaan</label>
            <input type="text" class="form-control cg2-ro" id="jam" name="jam" value="<?php echo htmlspecialchars((string) $data['jam_pemeriksaan'], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="nama_sopir">Nama Sopir</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="nama_sopir" name="nama_sopir" value="<?php echo htmlspecialchars($cg2_sopir, ENT_QUOTES); ?>" readonly>
          </div>

<?php if ($cg2_fg) { ?>
          <div class="cg2-field">
            <label>Usia</label>
            <div class="cg2-badge-wrap">
              <span class="cg2-badge <?php echo $cg2_kelas_usia; ?>"><?php echo htmlspecialchars($umur . ' Tahun', ENT_QUOTES); ?></span>
              <span class="cg2-mini"><?php echo htmlspecialchars((string) $status_usia, ENT_QUOTES); ?></span>
            </div>
            <input type="text" id="usia" name="usia" value="<?php echo htmlspecialchars((string) $umur, ENT_QUOTES); ?>" hidden>
            <input type="text" id="status_usia" name="status_usia" value="<?php echo htmlspecialchars((string) $status_usia, ENT_QUOTES); ?>" hidden>
          </div>

          <div class="cg2-field">
            <label for="tipe_sim">Jenis SIM</label>
            <input type="text" class="form-control cg2-ro" id="tipe_sim" name="tipe_sim" value="<?php echo htmlspecialchars((string) $tipe_sim, ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label>Masa Berlaku SIM</label>
            <div class="cg2-badge-wrap">
              <span class="cg2-badge <?php echo $cg2_kelas_sim; ?>"><?php echo htmlspecialchars($cg2_sim_txt, ENT_QUOTES); ?></span>
              <span class="cg2-mini">exp: <?php echo htmlspecialchars((string) $valid, ENT_QUOTES); ?></span>
            </div>
            <input type="text" id="expired_sim" name="expired_sim" value="<?php echo htmlspecialchars((string) $valid, ENT_QUOTES); ?>" hidden>
            <input type="text" id="status_sim" name="status_sim" value="<?php echo htmlspecialchars((string) $status_sim, ENT_QUOTES); ?>" hidden>
          </div>

          <div class="cg2-field">
            <label>Masa Berlaku ID DDT</label>
            <div class="cg2-badge-wrap">
              <span class="cg2-badge <?php echo $cg2_kelas_ddt; ?>"><?php echo htmlspecialchars($cg2_ddt_txt, ENT_QUOTES); ?></span>
              <span class="cg2-mini">exp: <?php echo htmlspecialchars((string) $valid_ddt, ENT_QUOTES); ?></span>
            </div>
            <input type="text" id="expired_ddt" name="expired_ddt" value="<?php echo htmlspecialchars((string) $valid_ddt, ENT_QUOTES); ?>" hidden>
            <input type="text" id="status_ddt" name="status_ddt" value="<?php echo htmlspecialchars((string) $status_ddt, ENT_QUOTES); ?>" hidden>
          </div>
<?php } ?>

          <div class="cg2-field">
            <label for="nama_transporter">Nama Transporter</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="nama_transporter" name="nama_transporter" value="<?php echo htmlspecialchars((string) $data['nama_transporter'], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="tipe_truck">Jenis Kendaraan</label>
            <input type="text" class="form-control cg2-ro" id="tipe_truck" name="tipe_truck" value="<?php echo htmlspecialchars((string) $data['jenis_kendaraan'], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="petugas">Petugas Pemeriksa</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="petugas" name="petugas" value="<?php echo htmlspecialchars((string) $_SESSION[APP_NAME]["username"], ENT_QUOTES); ?>" readonly>
          </div>

          <div class="cg2-field">
            <label for="lokasi">Lokasi Plant</label>
            <input type="text" class="form-control cg2-ro text-uppercase" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars((string) $data['lokasi_pemeriksaan'], ENT_QUOTES); ?>" readonly>
          </div>

        </div>

        <input type="hidden" id="code" name="code" value="<?php echo htmlspecialchars((string) $kode, ENT_QUOTES); ?>">
      </div>
    </div>

    <div class="cg2-card">
      <div class="cg2-head cg2-head-utama">
        <span><i class="fa fa-list-ul"></i>&nbsp; Kelengkapan Utama</span>
        <span class="cg2-hint">centang kalau sesuai</span>
      </div>
      <div class="cg2-body">
<?php
$cg2_res = mysqli_query($con, "SELECT *, CONCAT(name,no) as namee, CONCAT(ceklist_utama, no,no) as idgreen, CONCAT(ceklist_utama, no,no,no) as idred
          FROM tb_ceklist_utama WHERE no>4");
$cg2_i = 1;
while ($row = mysqli_fetch_assoc($cg2_res)) {
?>
        <div class="cg2-item">
          <div class="cg2-item-name">
            <span class="cg2-num"><?php echo $cg2_i; ?></span><?php echo htmlspecialchars((string) $row['ceklist_utama'], ENT_QUOTES); ?>
          </div>
          <label class="cg2-chk" title="Centang kalau kelengkapan ini sesuai">
            <input type="checkbox" id="<?php echo $row['idgreen']; ?>" name="<?php echo $row['namee']; ?>" value=1 onchange="tambahan()">
            <span class="cg2-box">
              <i class="fa fa-times cg2-i-no"></i><i class="fa fa-check cg2-i-ok"></i>
            </span>
          </label>
        </div>
<?php $cg2_i++; } ?>
      </div>
    </div>

    <div class="cg2-card">
      <div class="cg2-head cg2-head-tambahan">
        <span><i class="fa fa-plus-square-o"></i>&nbsp; Kelengkapan Tambahan</span>
        <span class="cg2-hint">pilih jika ada temuan</span>
      </div>
      <div class="cg2-body">
<?php
$cg2_res2 = mysqli_query($con, "SELECT *, CONCAT(name,no) as namee, CONCAT(ceklist_tambahan, no,no) as idgreen, CONCAT(ceklist_tambahan, no,no,no) as idred
          FROM tb_ceklist_tambahan");
$cg2_j = 1;
while ($row2 = mysqli_fetch_assoc($cg2_res2)) {
?>
        <div class="cg2-item">
          <div class="cg2-item-name">
            <span class="cg2-num"><?php echo $cg2_j; ?></span><?php echo htmlspecialchars((string) $row2['ceklist_tambahan'], ENT_QUOTES); ?>
          </div>
          <label class="cg2-chk" title="Centang kalau kelengkapan ini sesuai">
            <input type="checkbox" id="<?php echo $row2['idgreen']; ?>" name="<?php echo $row2['namee']; ?>" value=1 onchange="tambahan()">
            <span class="cg2-box">
              <i class="fa fa-times cg2-i-no"></i><i class="fa fa-check cg2-i-ok"></i>
            </span>
          </label>
        </div>
<?php $cg2_j++; } ?>

        <p class="cg2-note" style="margin-top:14px">
          <i class="fa fa-info-circle"></i>
          Jika ada point Kelengkapan Tambahan tidak terpenuhi maka segera dilakukan tindakan perbaikan
          sesuai batas waktu yang telah ditentukan.
        </p>
      </div>
    </div>

    <div class="cg2-card">
      <div class="cg2-head cg2-head-hasil">
        <span><i class="fa fa-check-square-o"></i>&nbsp; Hasil Pemeriksaan</span>
      </div>
      <div class="cg2-body">

        <div class="cg2-field">
          <label for="hasil">Hasil Pemeriksaan</label>
          <input type="text" class="form-control cg2-hasil cg2-biru" id="hasil" name="hasil" readonly>
        </div>
        <p class="cg2-note">Terisi otomatis setiap kali centang kelengkapan diubah.</p>

        <div class="cg2-field">
          <label for="komentar">Komentar Kerusakan</label>
          <input type="text" class="form-control" id="komentar" name="komentar" required>
        </div>

        <div class="cg2-field">
          <label for="tindakan">Tindakan Perbaikan</label>
          <input type="text" class="form-control" id="tindakan" name="tindakan" required>
        </div>

        <input type="hidden" id="kode" name="kode" value="<?php echo htmlspecialchars((string) $kode, ENT_QUOTES); ?>">

        <div class="text-center cg2-actions">
          <button type="submit" class="btn cg2-save"><i class="fa fa-save"></i> Simpan</button>
        </div>

      </div>
    </div>

  </form>
</div>

<script type="text/javascript">
/* Warnai kotak Hasil Pemeriksaan sesuai teks yang dikirim status_tambahan.php
   ("Stiker Hijau" / "Stiker Kuning" / "Stiker Merah" / "Di Tolak di Pos 1").
   Urutan cek penting: "Tidak Layak di Operasikan (Stiker Merah)" juga memuat
   kata "layak di operasikan", jadi merah/kuning diperiksa lebih dulu. */
function cg2WarnaiHasil() {
  var t = ($('#hasil').val() || '').toLowerCase();
  var c = 'cg2-biru';
  if (t.indexOf('merah') >= 0 || t.indexOf('tolak') >= 0 || t.indexOf('tidak layak') >= 0) {
    c = 'cg2-merah';
  } else if (t.indexOf('kuning') >= 0 || t.indexOf('perlu perbaikan') >= 0) {
    c = 'cg2-kuning';
  } else if (t.indexOf('hijau') >= 0 || t.indexOf('layak di operasikan') >= 0) {
    c = 'cg2-hijau';
  }
  $('#hasil').removeClass('cg2-hijau cg2-kuning cg2-merah cg2-biru').addClass(c);
}

$(document).ready(function () {
  cg2WarnaiHasil();
  /* status_tambahan.php membalas lewat AJAX (lihat tambahan() di
     application/assets/function.php) dan mengisi #hasil tanpa memicu event. */
  $(document).ajaxSuccess(function (e, xhr, settings) {
    if (String(settings.url).indexOf('status_tambahan') >= 0) { cg2WarnaiHasil(); }
  });
  /* umpan balik saat menyimpan */
  $('#formGate2').on('submit', function () {
    $(this).find('button[type=submit]').prop('disabled', true)
      .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
  });
});
</script>
