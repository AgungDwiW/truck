<div class="body-wrap-with-navbar">
<?php
/* ============================================================================
# db_waiting — daftar truck yang menunggu pemeriksaan Gate 2
#
# CATATAN DB (penting untuk prod):
#   Di prod, tabel truck dan tabel evisitor ada di DATABASE BERBEDA, jadi
#   JANGAN memakai nama ber-titik ("dbtruck.tb_ceklist" / "evisitor.tbl_visit")
#   dalam satu query. Setiap koneksi dibiarkan memakai skema default-nya
#   sendiri, dan tiap tabel diambil dari koneksi yang sesuai:
#     $conTruck   -> application/config/db_dbtruck.php   (tb_ceklist)
#     $conVisitor -> application/config/db_evisitor.php  (tbl_visit)
#   Jadi ganti server/DB cukup di file config, tidak perlu ubah view ini.
# ============================================================================ */

$muat   = @$_POST['muat'] ?? @$_GET['muat'] ?? '';
$PLANT  = $_SESSION[APP_NAME]['plant_id'];
$MUATAN = ($muat == 'FG') ? 'FG' : 'Material';

$TGLTRUCK   = date('Y-m-d', strtotime(date('Y-m-d') . ' -2 day'));
$TGLEVISITOR = date('Y-m-d', strtotime(date('Y-m-d') . ' -21 day'));
$now        = date('Y-m-d');

/* koneksi per database (lihat catatan di atas) */
$conTruck   = isset($con)   ? $con   : null;
$conVisitor = isset($con_3) ? $con_3 : null;

/* alamat form eVisitor - bisa dioverride dari config */
$evisitor_url = defined('EVISITOR_URL')
    ? EVISITOR_URL
    : 'https://adop.danet/evisitor/tamu?ac=regtamu2';

$PLANT_SQL   = mysqli_real_escape_string($conTruck, (string) $PLANT);
$MUATAN_SQL  = mysqli_real_escape_string($conTruck, (string) $MUATAN);

/* --- header pemeriksaan (tabel truck: koneksi truck) --- */
$str = "SELECT ck.`no`, ck.petugas_pemeriksa, ck.plant_name, ck.tgbaca,
               ck.nopol, ck.muatan, DATE(ck.tgbaca) AS TGLPERIKSA
        FROM tb_ceklist ck
        WHERE DATE(ck.tgbaca) BETWEEN '{$TGLTRUCK}' AND '{$now}'
          AND ck.hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2'
          AND ck.plant_id = '{$PLANT_SQL}'
          AND ck.muatan = '{$MUATAN_SQL}'
        ORDER BY ck.tgbaca DESC";

$rows = array();
$err  = '';
try {
    $result = mysqli_query($conTruck, $str);
    while ($d = mysqli_fetch_assoc($result)) { $rows[] = $d; }
} catch (mysqli_sql_exception $e) {
    $err = $e->getMessage();   /* PHP 8: error mysqli = exception, bukan warning */
}

/* --- sudah terdaftar di eVisitor? (tabel visitor: koneksi evisitor) --- */
foreach ($rows as $i => $d) {
    $rows[$i]['ada_visit'] = 0;
    if (!$conVisitor) { continue; }
    $nopol_sql = mysqli_real_escape_string($conVisitor, (string) $d['nopol']);
    $sql_vis = "SELECT COUNT(*) c FROM tbl_visit
                WHERE no_pol = '{$nopol_sql}'
                  AND DATE(tanggal_datang) BETWEEN '{$TGLEVISITOR}' AND '{$now}'";
    try {
        $r2 = mysqli_query($conVisitor, $sql_vis);
        $c2 = mysqli_fetch_assoc($r2);
        $rows[$i]['ada_visit'] = isset($c2['c']) ? (int) $c2['c'] : 0;
    } catch (mysqli_sql_exception $e) {
        $rows[$i]['ada_visit'] = 0;
    }
}
$jml = count($rows);
?>

<style type="text/css">
/* ---- db_waiting (dw-*) ---- */
.dw-wrap { max-width: 1080px; margin: 0 auto; }
.dw-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 4px 18px rgba(20, 30, 60, .08);
  overflow: hidden;
  border: 1px solid #e6eaf2;
}
.dw-head {
  background: linear-gradient(135deg, #0f6ea8, #1f8fd0);
  color: #fff;
  padding: 14px 20px;
  font-weight: 700;
  font-size: 17px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.dw-head .dw-count {
  background: rgba(255, 255, 255, .2);
  border-radius: 999px;
  padding: 3px 12px;
  font-size: 13px;
  font-weight: 600;
}
.dw-tools {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  padding: 12px 16px;
  background: #f8fafd;
  border-bottom: 1px solid #e6eaf2;
}
.dw-tools .dw-label { font-size: 13px; font-weight: 600; color: #4b5563; }
.dw-tab {
  display: inline-block;
  padding: 6px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  color: #0f6ea8;
  background: #e8f2fa;
  border: 1px solid #cfe3f3;
}
.dw-tab:hover { background: #d9eaf8; text-decoration: none; color: #0b557f; }
.dw-tab.aktif { background: #0f6ea8; border-color: #0f6ea8; color: #fff; }
.dw-note { margin-left: auto; font-size: 12px; color: #6b7280; }

.dw-body { max-height: 62vh; overflow: auto; }
.dw-table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; }
.dw-table thead th {
  position: sticky;
  top: 0;
  z-index: 2;
  background: #f1f5fb;
  color: #374151;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 700;
  text-align: left;
  padding: 11px 14px;
  border-bottom: 1px solid #e2e8f2;
  white-space: nowrap;
}
.dw-table tbody td {
  padding: 12px 14px;
  font-size: 14px;
  color: #1f2937;
  border-bottom: 1px solid #eef1f6;
  vertical-align: middle;
}
.dw-table tbody tr:hover td { background: #f7fbff; }
.dw-table tbody tr:last-child td { border-bottom: 0; }
.dw-num { color: #9aa4b2; font-variant-numeric: tabular-nums; }
.dw-nopol { font-weight: 700; letter-spacing: .03em; text-transform: uppercase; }
.dw-badge {
  display: inline-block;
  padding: 4px 11px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}
.dw-badge-fg  { background: #e6f0fb; color: #0f5c8f; }
.dw-badge-mat { background: #fff4e0; color: #9a5b00; }
.dw-kosong {
  text-align: center;
  color: #6b7280;
  padding: 34px 14px !important;
  font-size: 14px;
}
.dw-kosong i { font-size: 22px; display: block; margin-bottom: 8px; color: #c3cbd8; }
.dw-btn {
  display: inline-block;
  border: 0;
  border-radius: 9px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  text-decoration: none;
}
.dw-btn:hover { text-decoration: none; color: #fff; opacity: .92; }
.dw-btn-go { background: #28a745; }
.dw-btn-ev { background: #e8a33d; }
.dw-err {
  margin: 14px 16px;
  padding: 12px 14px;
  border-radius: 10px;
  background: #fdeaec;
  border: 1px solid #f5c2c7;
  color: #842029;
  font-size: 13px;
  font-family: monospace;
  white-space: pre-wrap;
}
</style>

<div class="dw-wrap">
  <div class="dw-card">

    <div class="dw-head">
      <span><i class="fa fa-hourglass-half"></i>&nbsp; Menunggu Pemeriksaan Gate 2</span>
      <span class="dw-count"><?php echo $jml; ?> truck</span>
    </div>

    <div class="dw-tools">
      <span class="dw-label">Muatan</span>
      <a class="dw-tab <?php echo $MUATAN === 'FG' ? 'aktif' : ''; ?>"
         href="common?action=db_waiting&amp;muat=FG">FG</a>
      <a class="dw-tab <?php echo $MUATAN === 'Material' ? 'aktif' : ''; ?>"
         href="common?action=db_waiting&amp;muat=Material">Material</a>
      <span class="dw-note">Pemeriksaan <?php echo $TGLTRUCK; ?> s/d <?php echo $now; ?>
        &middot; Plant <?php echo htmlspecialchars((string) $PLANT, ENT_QUOTES); ?></span>
    </div>

    <?php if ($err !== '') { ?>
      <div class="dw-err"><b>Query gagal:</b>
<?php echo htmlspecialchars($err, ENT_QUOTES); ?></div>
    <?php } ?>

    <div class="dw-body">
      <table class="dw-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Pemeriksa</th>
            <th>Plant</th>
            <th>Tanggal</th>
            <th>Nopol</th>
            <th>Muatan</th>
            <th>Cek Gate</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($jml === 0) { ?>
          <tr>
            <td class="dw-kosong" colspan="7">
              <i class="fa fa-check-circle-o"></i>
              Tidak ada truck yang menunggu Gate 2<?php echo $err !== '' ? '.' : ' untuk muatan ' . htmlspecialchars($MUATAN, ENT_QUOTES) . ' saat ini.'; ?>
            </td>
          </tr>
        <?php } else { ?>
          <?php foreach ($rows as $data) { ?>
            <tr>
              <td class="dw-num"><?php echo htmlspecialchars((string) $data['no'], ENT_QUOTES); ?></td>
              <td><?php echo htmlspecialchars((string) $data['petugas_pemeriksa'], ENT_QUOTES); ?></td>
              <td><?php echo htmlspecialchars((string) $data['plant_name'], ENT_QUOTES); ?></td>
              <td><?php echo htmlspecialchars((string) $data['tgbaca'], ENT_QUOTES); ?></td>
              <td class="dw-nopol"><?php echo htmlspecialchars((string) $data['nopol'], ENT_QUOTES); ?></td>
              <td>
                <span class="dw-badge <?php echo strtoupper((string) $data['muatan']) === 'FG' ? 'dw-badge-fg' : 'dw-badge-mat'; ?>">
                  <?php echo htmlspecialchars((string) $data['muatan'], ENT_QUOTES); ?>
                </span>
              </td>
              <td>
                <form method="post" action="common?action=cek_gate2" class="dw-form">
                  <?php if ($data['ada_visit'] > 0) { ?>
                    <button type="submit" class="dw-btn dw-btn-go" name="kode"
                            value="<?php echo htmlspecialchars((string) $data['no'], ENT_QUOTES); ?>">
                      <i class="fa fa-arrow-right"></i> Lanjut Gate 2
                    </button>
                  <?php } else { ?>
                    <a class="dw-btn dw-btn-ev" target="_blank"
                       href="<?php echo htmlspecialchars($evisitor_url, ENT_QUOTES); ?>">
                      <i class="fa fa-external-link"></i> Input eVisitor
                    </a>
                  <?php } ?>
                </form>
              </td>
            </tr>
          <?php } ?>
        <?php } ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script type="text/javascript">
/* tombol Lanjut Gate 2: kasih umpan balik biar tidak terasa "diam" */
$(document).on('submit', '.dw-form', function () {
  $(this).find('button[type=submit]')
    .prop('disabled', true)
    .html('<i class="fa fa-spinner fa-spin"></i> Membuka...');
});
</script>
</div>
