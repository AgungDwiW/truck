<div class="body-wrap-with-navbar">

<?php
/* ============================================================================
# N_gate1 — Checklist Gate 1
# Struktur file:
#   1. INPUT  : semua $_POST dibaca di sini, di-cast mengikuti TIPE KOLOM db
#   2. QUERY  : SEMUA akses database (abaikan bagian ini kalau cuma mau lihat UI)
#   3. DATA   : nyusun data siap-render
#   4. VIEW   : HTML/CSS + JS (tidak ada query lagi)
#
# Simpan hasil per item TIDAK pindah halaman lagi: tombol OK dan tombol
# kamera (temuan + foto) mengirim AJAX ke common?action=gate1_ajax lalu
# mengubah baris di halaman ini. Tombol aslinya tetap form biasa, jadi kalau
# JS mati alurnya masih jalan seperti dulu (POST ke N_foto_gate1).
# ============================================================================ */

/* ----------------------------------------------------------------------------
# 1. INPUT — tipe cast mengikuti kolom tb_ceklist (information_schema)
# ----------------------------------------------------------------------------
# kolom                tipe            input
# seq                  int(10)         (int)
# idref                varchar(50)     (string)
# nopol                varchar(50)     (string)
# nama_supplier        varchar(50)     (string)
# nama_transporter     varchar(512)    (string)
# nama_sopir           varchar(50)     (string)   <- dari POST 'driver'
# jenis_kendaraan      varchar(50)     (string)   <- hasil lookup
# plant_id             varchar(10)     (string)
# plant_name           varchar(50)     (string)
# tgl_pemeriksaan      varchar(50)     (string)
# jam_pemeriksaan      varchar(50)     (string)
# lokasi_pemeriksaan   varchar(50)     (string)
# muatan               varchar(10)     (string)
# usia                 int(11)         (int)
# jenis_sim            varchar(50)     (string)
# expired_date_sim     date            (string Y-m-d)
# expired_date_ddt     date            (string Y-m-d)
# status_sim/ddt/usia  varchar(50)     (string)
# id_barang            varchar(50)     (string)
# kode_kirim           int(11)         (string; dipakai sbg kode, bukan hitungan)
# ---------------------------------------------------------------------------- */
$idref       = (string) ($_POST['idref'] ?? '');
$nopol       = (string) ($_POST['nopol'] ?? '');
$lokasi      = (string) ($_POST['lokasi'] ?? '');
$kode_kirim  = (string) (@$_POST['kode_kirim'] ?? '');
$muat        = (string) (@$_POST['muat'] ?? '');
$driver      = (string) (@$_POST['driver'] ?? '');
$supplier    = (string) (@$_POST['supplier'] ?? '');
$transporter = (string) (@$_POST['transporter'] ?? '');

$usia        = (int)    (@$_POST['usia'] ?? 0);
$tipe_sim    = (string) (@$_POST['tipe_sim'] ?? '');
$expired_sim = (string) (@$_POST['expired_sim'] ?? '');
$expired_ddt = (string) (@$_POST['expired_ddt'] ?? '');
$status_sim  = (string) (@$_POST['status_sim'] ?? '');
$status_ddt  = (string) (@$_POST['status_ddt'] ?? '');
$status_usia = (string) (@$_POST['status_usia'] ?? '');
$id_barang   = (string) (@$_POST['id_barang'] ?? '');

$seq         = (int)    ($_POST['seq'] ?? 0);
$jam         = (string) ($_POST['jam'] ?? '');
$date        = (string) ($_POST['tgl'] ?? '');
$plant_id    = (string) ($_POST['plant_id'] ?? '');

$username    = $_SESSION[APP_NAME]["username"];
$petugas     = $username;

if ($expired_sim === '') { $expired_sim = '2999-12-30'; }   /* date: default "jauh" */
if ($expired_ddt === '') { $expired_ddt = '2999-12-30'; }

/* ============================================================================
# 2. QUERY
# ============================================================================ */

/* 2.1 tanda sementara: daftar checklist sedang dipakai */
mysqli_query($con, "UPDATE tb_ceklist_utama SET status_temp=1");

/* 2.2 daftar item kelengkapan utama (no 0..4; no=0 baris kosong utk penomoran) */
$sql_item = "SELECT no, ceklist_utama FROM tb_ceklist_utama WHERE no BETWEEN 0 AND 4 ORDER BY no";
$res_item = mysqli_query($con, $sql_item);
$db_item  = array();
while ($r_item = mysqli_fetch_assoc($res_item)) { $db_item[] = $r_item; }

/* 2.3 jenis kendaraan dari master tempat muat (hanya muatan FG) */
$tipe_truck = '';
if ($muat === 'FG') {
    $sql_truck = "SELECT jenis_truck FROM tbm_tempat_muat
                  WHERE id_tempat_muat='" . mysqli_real_escape_string($con, $plant_id) . "'
                    AND nama_supplier='" . mysqli_real_escape_string($con, $supplier) . "'
                    AND nama_transporter='" . mysqli_real_escape_string($con, $transporter) . "'
                  GROUP BY nama_transporter LIMIT 1";
    $res_truck = mysqli_query($con, $sql_truck);
    if ($row_truck = mysqli_fetch_assoc($res_truck)) { $tipe_truck = (string) $row_truck['jenis_truck']; }
}

/* 2.4 simpan header pemeriksaan (sekali per kiriman, hanya muatan FG)
#      int ditulis tanpa kutip, string di-escape -> formatnya sesuai tipe kolom */
if ($muat === 'FG') {
    $sql_insert = "INSERT INTO tb_ceklist SET
        seq = $seq,
        idref = '" . mysqli_real_escape_string($con, $idref) . "',
        petugas_pemeriksa = '" . mysqli_real_escape_string($con, $username) . "',
        nopol = '" . mysqli_real_escape_string($con, $nopol) . "',
        nama_supplier = '" . mysqli_real_escape_string($con, $supplier) . "',
        nama_transporter = '" . mysqli_real_escape_string($con, $transporter) . "',
        jenis_kendaraan = '" . mysqli_real_escape_string($con, $tipe_truck) . "',
        plant_id = '" . mysqli_real_escape_string($con, $plant_id) . "',
        plant_name = '" . mysqli_real_escape_string($con, $lokasi) . "',
        nama_sopir = '" . mysqli_real_escape_string($con, $driver) . "',
        tgl_pemeriksaan = '" . mysqli_real_escape_string($con, $date) . "',
        jam_pemeriksaan = '" . mysqli_real_escape_string($con, $jam) . "',
        lokasi_pemeriksaan = '" . mysqli_real_escape_string($con, $lokasi) . "',
        muatan = '" . mysqli_real_escape_string($con, $muat) . "',
        usia = $usia,
        jenis_sim = '" . mysqli_real_escape_string($con, $tipe_sim) . "',
        expired_date_sim = '" . mysqli_real_escape_string($con, $expired_sim) . "',
        expired_date_ddt = '" . mysqli_real_escape_string($con, $expired_ddt) . "',
        status_sim = '" . mysqli_real_escape_string($con, $status_sim) . "',
        status_ddt = '" . mysqli_real_escape_string($con, $status_ddt) . "',
        status_usia = '" . mysqli_real_escape_string($con, $status_usia) . "',
        id_barang = '" . mysqli_real_escape_string($con, $id_barang) . "'";
    mysqli_query($con, $sql_insert);
}

/* 2.5 status tiap item (utama1..utama4) untuk idref ini */
$db_utama = array();
if ($idref !== '') {
    $sql_utama = "SELECT utama1, utama2, utama3, utama4 FROM tb_ceklist
                  WHERE idref='" . mysqli_real_escape_string($con, $idref) . "' LIMIT 1";
    $res_utama = mysqli_query($con, $sql_utama);
    if ($row_utama = mysqli_fetch_assoc($res_utama)) { $db_utama = $row_utama; }
}

/* ============================================================================
# 3. DATA — siapkan untuk render
# ============================================================================ */
$hasil = 'Lanjut Pemeriksaan Gate 2';
$rows  = array();

foreach ($db_item as $item) {
    $idx       = (int) $item['no'];                       /* 0..4 */
    $cek_utama = 1;
    if ($idx > 0 && array_key_exists('utama' . $idx, $db_utama)) {
        $cek_utama = (int) $db_utama['utama' . $idx];      /* int(10) */
    }
    if ($cek_utama === 0) { $hasil = 'Di Tolak di Pos 1'; }

    $rows[] = array(
        'no'     => $idx,
        'nama'   => (string) $item['ceklist_utama'],        /* varchar(50) */
        'status' => ($cek_utama === 1) ? 'OK' : 'PERIKSA',
        'tampil' => ($idx > 0),                             /* no=0 disembunyikan */
    );
}

$hasil_merah = ($hasil === 'Di Tolak di Pos 1');
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
  width: 50px;
  height: 50px;
  border: 2px solid #cfd6e4;
  border-radius: 50%;
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #6b7280;
  font-size: 18px;
  box-shadow: 0 1px 4px rgba(20, 30, 60, .08);
  transition: transform .12s ease, border-color .12s ease, color .12s ease;
}
.ng1-btn:hover { transform: translateY(-1px); }
.ng1-btn-ok:hover  { border-color: #28a745; color: #28a745; }
.ng1-btn-bad:hover { border-color: #dc3545; color: #dc3545; }
.ng1-ok  .ng1-btn-ok  { border-color: #28a745; color: #28a745; background: #eaf8ee; }
.ng1-bad .ng1-btn-bad { border-color: #dc3545; color: #dc3545; background: #fdeaec; }

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
.ng1-preview {
  margin-top: 10px;
  max-width: 100%;
  max-height: 220px;
  border-radius: 10px;
  border: 1px solid #e2e6ee;
  display: none;
}

/* --- overlay temuan: dibuat sendiri, TIDAK bergantung JS/CSS Bootstrap ---
   (JS bootstrap di app ini masih v3 sedangkan CSS-nya v4, modal .modal
   jadi tidak pernah kelihatan walau display-nya sudah block.) */
.ng1-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 2000;
  background: rgba(15, 23, 42, .55);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.ng1-overlay[hidden] { display: none; }
.ng1-modal {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  max-width: 520px;
  max-height: 92vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
}
.ng1-modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 14px 18px;
  background: linear-gradient(90deg, #0f6ea8, #14a2b8);
  color: #fff;
  font-weight: 700;
}
.ng1-modal-body { padding: 18px; overflow-y: auto; }
.ng1-modal-body label { font-weight: 600; color: #343a40; margin-bottom: 6px; }
.ng1-modal-foot {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 18px;
  border-top: 1px solid #eef1f6;
}
.ng1-x {
  background: none;
  border: 0;
  color: #fff;
  font-size: 26px;
  line-height: 1;
  cursor: pointer;
  padding: 0 4px;
}

</style>

<!-- ==========================================================================
# 4. VIEW
=========================================================================== -->
<div class="container ng1-wrap">

  <div class="ng1-card">
    <div class="ng1-head">
      <span><i class="fa fa-truck"></i>&nbsp; Kelengkapan Utama</span>
      <span class="ng1-hint">centang OK, atau klik kamera kalau ada temuan</span>
    </div>

    <div class="ng1-body">
<?php foreach ($rows as $r) { ?>
      <?php if (!$r['tampil']) { continue; } $u = $r['no']; ?>
      <div class="ng1-item <?php echo ($r['status'] === 'OK') ? 'ng1-ok' : 'ng1-bad'; ?>" id="row-<?php echo $u; ?>"
           data-utama="<?php echo $u; ?>"
           data-ceklist="<?php echo htmlspecialchars($r['nama'], ENT_QUOTES); ?>"
           title="Klik untuk ambil foto / isi temuan">
        <div class="ng1-left">
          <span class="ng1-num"><?php echo $u; ?></span>
          <span class="ng1-name"><?php echo htmlspecialchars($r['nama'], ENT_QUOTES); ?></span>
        </div>
        <div class="ng1-right">

          <!-- OK: form asli (fallback non-JS) -> common?action=N_foto_gate1 -->
          <form method="post" action="common?action=N_foto_gate1" class="ng1-okform" data-utama="<?php echo $u; ?>">
            <input type="text" name="utama" value="<?php echo $u; ?>" hidden >
            <input type="text" name="idref" value="<?php echo htmlspecialchars($idref, ENT_QUOTES); ?>" hidden >
            <input type="text" name="nopol" value="<?php echo htmlspecialchars($nopol, ENT_QUOTES); ?>" hidden >
            <input type="text" name="lokasi" value="<?php echo htmlspecialchars($lokasi, ENT_QUOTES); ?>" hidden >
            <input type="text" name="ceklist" value="<?php echo htmlspecialchars($r['nama'], ENT_QUOTES); ?>" hidden >
            <input type="text" name="kode_kirim" value="<?php echo htmlspecialchars($kode_kirim, ENT_QUOTES); ?>" hidden >
            <input type="text" name="driver" value="<?php echo htmlspecialchars($driver, ENT_QUOTES); ?>" hidden >
            <input type="text" name="supplier" value="<?php echo htmlspecialchars($supplier, ENT_QUOTES); ?>" hidden >
            <button type="submit" class="ng1-btn ng1-btn-ok" title="Tandai OK: <?php echo htmlspecialchars($r['nama'], ENT_QUOTES); ?>">
              <i class="fa fa-check"></i>
            </button>
          </form>

          <!-- PERIKSA: buka modal di halaman ini (tanpa pindah halaman) -->
          <button type="button" class="ng1-btn ng1-btn-bad"
                  title="Ambil foto / isi temuan: <?php echo htmlspecialchars($r['nama'], ENT_QUOTES); ?>">
            <i class="fa fa-camera"></i>
          </button>

          <span class="ng1-badge" id="badge-<?php echo $u; ?>"><?php echo $r['status']; ?></span>
        </div>
      </div>
<?php } ?>
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
          <input type="text" class="form-control ng1-hasil <?php echo $hasil_merah ? 'ng1-merah' : 'ng1-hijau'; ?>"
                 id="hasil" name="hasil" value="<?php echo htmlspecialchars($hasil, ENT_QUOTES); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="komentar">Komentar Kerusakan</label>
          <input type="text" class="form-control" id="komentar" name="komentar" required >
        </div>

        <div class="form-group mb-0">
          <label for="tindakan">Tindakan Perbaikan</label>
          <input type="text" class="form-control" id="tindakan" name="tindakan" required >
        </div>

        <input type="text" name="idref" value="<?php echo htmlspecialchars($idref, ENT_QUOTES); ?>" hidden>
        <input type="text" name="nopol" value="<?php echo htmlspecialchars($nopol, ENT_QUOTES); ?>" hidden>
        <input type="text" name="petugas" value="<?php echo htmlspecialchars($petugas, ENT_QUOTES); ?>" hidden>
        <input type="text" name="lokasi" value="<?php echo htmlspecialchars($lokasi, ENT_QUOTES); ?>" hidden>
        <input type="text" name="kode_kirim" value="<?php echo htmlspecialchars($kode_kirim, ENT_QUOTES); ?>" hidden>
        <input type="text" name="driver" value="<?php echo htmlspecialchars($driver, ENT_QUOTES); ?>" hidden >
        <input type="text" name="supplier" value="<?php echo htmlspecialchars($supplier, ENT_QUOTES); ?>" hidden >

        <div class="text-center mt-4">
          <button type="submit" id="btnSimpan" class="btn btn-primary ng1-save">
            <i class="fa fa-save"></i> Simpan
          </button>
        </div>

      </div>
    </div>
  </form>

</div>

<!-- overlay temuan: dipakai semua item, isinya diisi dari baris yg diklik -->
<div class="ng1-overlay" id="ng1Overlay" hidden>
  <div class="ng1-modal">
    <div class="ng1-modal-head">
      <span>Temuan: <b id="mCeklist"></b></span>
      <button type="button" class="ng1-x" id="btnTutupModal" title="Tutup">&times;</button>
    </div>

      <!-- action tetap ke halaman lama sebagai fallback kalau JS mati -->
      <form id="formTemuan" method="post" action="common?action=N_upload_fail" enctype="multipart/form-data">
        <div class="ng1-modal-body">
          <input type="text" name="utama" id="mUtama" hidden>
          <input type="text" name="ccp" id="mCcp" hidden>
          <input type="text" name="tem" id="mTem" hidden>
          <input type="text" name="idref" value="<?php echo htmlspecialchars($idref, ENT_QUOTES); ?>" hidden>
          <input type="text" name="nopol" value="<?php echo htmlspecialchars($nopol, ENT_QUOTES); ?>" hidden>
          <input type="text" name="lokasi" value="<?php echo htmlspecialchars($lokasi, ENT_QUOTES); ?>" hidden>
          <input type="text" name="kode_kirim" value="<?php echo htmlspecialchars($kode_kirim, ENT_QUOTES); ?>" hidden>
          <input type="text" name="driver" value="<?php echo htmlspecialchars($driver, ENT_QUOTES); ?>" hidden>
          <input type="text" name="supplier" value="<?php echo htmlspecialchars($supplier, ENT_QUOTES); ?>" hidden>

          <div class="form-group">
            <label for="mTemuanText">Komentar / Temuan</label>
            <textarea class="form-control" id="mTemuanText" rows="3" required></textarea>
          </div>

          <div class="form-group mb-0">
            <label for="mFile">Foto (kamera / file, maks 1 MB)</label>
            <input type="file" class="form-control-file" name="file" id="mFile" accept="image/*" capture="capture" required>
            <img id="mPreview" class="ng1-preview" alt="pratinjau foto">
          </div>
        </div>
        <div class="ng1-modal-foot">
          <button type="button" class="btn btn-secondary" id="btnBatal">Batal</button>
          <button type="submit" class="btn btn-danger" id="mSimpan">
            <i class="fa fa-camera"></i> Simpan Temuan
          </button>
        </div>
      </form>
  </div>
</div>

<script type="text/javascript">
/* ============================================================================
# Simpan hasil per item tanpa pindah halaman (AJAX ke common?action=gate1_ajax)
============================================================================ */
var N_GATE1_IDREF = <?php echo json_encode($idref); ?>;

$(document).ready(function () {

  function refreshHasil() {
    var merah = $('.ng1-item.ng1-bad').length > 0;
    $('#hasil')
      .val(merah ? 'Di Tolak di Pos 1' : 'Lanjut Pemeriksaan Gate 2')
      .toggleClass('ng1-merah', merah)
      .toggleClass('ng1-hijau', !merah);
  }

  function setRow(utama, status) {
    var $row = $('#row-' + utama);
    $row.toggleClass('ng1-ok', status === 'OK').toggleClass('ng1-bad', status !== 'OK');
    $('#badge-' + utama).text(status);
    refreshHasil();
  }

  /* --- tombol OK --- */
  $('.ng1-okform').on('submit', function (e) {
    e.preventDefault();
    var utama = $(this).data('utama');
    $.ajax({
      url: 'common?action=gate1_ajax',
      type: 'POST',
      data: { op: 'ok', idref: N_GATE1_IDREF, utama: utama },
      dataType: 'json'
    }).done(function (res) {
      if (res && res.ok) { setRow(utama, 'OK'); }
      else { alert((res && res.msg) ? res.msg : 'Gagal menyimpan.'); }
    }).fail(function () { alert('Gagal kirim ke server.'); });
  });

  /* --- overlay temuan (tidak pakai modal Bootstrap) --- */
  function bukaTemuan($row) {
    $('#mUtama').val($row.data('utama'));
    $('#mCcp').val($row.data('ceklist'));
    $('#mCeklist').text($row.data('ceklist'));
    $('#mTemuanText').val('');
    $('#mTem').val('');
    $('#mFile').val('');
    $('#mPreview').hide().attr('src', '');
    $('#ng1Overlay').prop('hidden', false);
    setTimeout(function () { $('#mTemuanText').focus(); }, 60);
  }
  function tutupTemuan() { $('#ng1Overlay').prop('hidden', true); }

  /* klik di mana saja pada baris item = ambil foto / isi temuan
     (kecuali tombol OK yang punya aksi sendiri) */
  $(document).on('click', '.ng1-item', function (e) {
    if ($(e.target).closest('.ng1-okform').length) { return; }
    bukaTemuan($(this));
  });
  $('#btnTutupModal, #btnBatal').on('click', tutupTemuan);
  $(document).on('keydown', function (e) { if (e.key === 'Escape') { tutupTemuan(); } });

  $('#mFile').on('change', function () {
    var f = this.files && this.files[0];
    if (f) { $('#mPreview').attr('src', URL.createObjectURL(f)).show(); }
    else { $('#mPreview').hide().attr('src', ''); }
  });

  /* --- kirim temuan + foto tanpa reload --- */
  $('#formTemuan').on('submit', function (e) {
    e.preventDefault();
    var tem = $.trim($('#mTemuanText').val());
    if (tem === '') { return; }
    $('#mTem').val(tem);

    var utama = $('#mUtama').val();
    var fd = new FormData(this);
    fd.append('op', 'fail');

    $('#mSimpan').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    $.ajax({
      url: 'common?action=gate1_ajax',
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json'
    }).done(function (res) {
      if (res && res.ok) {
        setRow(utama, 'PERIKSA');
        tutupTemuan();
      } else {
        alert((res && res.msg) ? res.msg : 'Gagal menyimpan temuan.');
      }
    }).fail(function () {
      alert('Gagal kirim ke server.');
    }).always(function () {
      $('#mSimpan').prop('disabled', false).html('<i class="fa fa-camera"></i> Simpan Temuan');
    });
  });

  /* --- simpan akhir --- */
  $('#formSimpan').on('submit', function () {
    $('#btnSimpan').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
  });

});
</script>
