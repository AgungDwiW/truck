<?php
/* ============================================================================
# N_cek_truck (Material) — input data truck sebelum checklist Gate 1
#
# Struktur file:
#   1. INPUT  : nilai dari POST + helper debug
#   2. QUERY  : SEMUA akses database (sinkron cloud->lokal, jadwal truck,
#               simpan header pemeriksaan) — tidak ada query di bagian VIEW
#   3. DATA   : nyusun nilai siap-render
#   4. VIEW   : HTML/CSS
#
# Alur: material?action=cek_nopol (input kode kirim) -> halaman ini -> N_gate1
# ============================================================================ */

/* --- 1. INPUT ------------------------------------------------------------- */

$kode_kirim = $_POST['kode_kirim'] ?? '';
$muat       = $_POST['muat'] ?? 'Material';
$username   = $_SESSION[APP_NAME]["username"];
$debug      = 0;

if ($username == 'Latihan Wonosobo 1') {
    $debug = 1;
}

/* cetak debug: aktif kalau $debug = 1 (atau argumen kedua = 1) */
function printpre($str, $fl = -1)
{
    global $debug;

    if ($fl == -1) {
        $fl = $debug;
    }
    if ($fl) {
        echo "<pre>";
        print_r($str);
        echo "</pre>";
    }
    return 0;
}

/* --- 2. QUERY ------------------------------------------------------------- */

/* 2.1 Sinkronisasi data cloud (dbasnho) -> lokal (smartlogistic).
   Hanya dikerjakan kalau jumlah baris cloud berbeda dengan lokal. */
$kode_sql = mysqli_real_escape_string($concloud, (string) $kode_kirim);

$result           = mysqli_query($concloud, "SELECT * from tbl_pengiriman_combined where kode_pengiriman='{$kode_sql}'");
$count_cloud      = mysqli_num_rows($result);
$pengiriman_cloud = mysqli_fetch_assoc($result);

$result           = mysqli_query($conSL, "SELECT * from tbl_pengiriman where kode_pengiriman='{$kode_sql}'");
$count_local      = mysqli_num_rows($result);
$pengiriman_local = mysqli_fetch_assoc($result);

$result = mysqli_query($concloud, "SELECT * from tbl_item_pengiriman where pengiriman_id='{$kode_sql}'");
$item_cloud = [];
while ($row = mysqli_fetch_assoc($result)) {
    $item_cloud[$row['kode_item_kirim']] = $row;
}

$result = mysqli_query($conSL, "SELECT * from tbl_item_pengiriman where pengiriman_id='{$kode_sql}'");
$item_local = [];
while ($row = mysqli_fetch_assoc($result)) {
    $item_local[$row['kode_item_kirim']] = $row;
}

printpre(['n' => 'cloud', 'pengiriman' => $pengiriman_cloud, 'item' => array_keys($item_cloud)]);
printpre(['n' => 'local', 'pengiriman' => $pengiriman_local, 'item' => array_keys($item_local)]);

/* 2.2 Salin header kiriman cloud -> lokal.
   Kalau cloud kosong, JANGAN INSERT kolom kosong (dulu: SQL error -> 500). */
if ($count_cloud != $count_local && !empty($pengiriman_cloud)) {
    $pengiriman_synced = 1;

    $col = implode(', ', array_keys($pengiriman_cloud));

    $vals = [];
    foreach (array_values($pengiriman_cloud) as $v) {
        $vals[] = ($v === '' || $v === null)
            ? 'NULL'
            : "'" . mysqli_real_escape_string($conSL, (string) $v) . "'";
    }

    $SQL = "INSERT into tbl_pengiriman ({$col}) VALUES(" . implode(', ', $vals) . ")";
    printpre($SQL);

    try {
        mysqli_query($conSL, $SQL);
    } catch (mysqli_sql_exception $e) {
        /* sinkron gagal tidak boleh bikin halaman blank */
        printpre($e->getMessage());
    }
}

printpre($item_cloud);

/* 2.3 Salin item kiriman yang belum ada di lokal */
foreach ($item_cloud as $key => $row_cloud) {
    if (isset($item_local[$key])) {
        continue;
    }

    $item_synced[] = $key;

    $col = implode(', ', array_keys($row_cloud));

    $vals = [];
    foreach (array_values($row_cloud) as $v) {
        $vals[] = ($v === '' || $v === null)
            ? 'NULL'
            : "'" . mysqli_real_escape_string($conSL, (string) $v) . "'";
    }

    $SQL = "REPLACE into tbl_item_pengiriman ({$col}) VALUES(" . implode(', ', $vals) . ")";
    printpre($SQL);

    try {
        mysqli_query($conSL, $SQL);
    } catch (mysqli_sql_exception $e) {
        /* sinkron item gagal (mis. kolom beda antar DB) tidak boleh bikin halaman blank */
        printpre($e->getMessage());
    }
}

if ($username == 'Latihan Wonosobo 1') {
    exit();
}

/* 2.4 Jadwal truck: kode kirim + tanggal kedatangan HARI INI (koneksi lokal) */
$date = date("Y-m-d");

$sql_nopol = mysqli_query($con2, "SELECT * from tbl_pengiriman
                                  where kode_pengiriman='{$kode_sql}'
                                    and tgl_kedatangan='{$date}'");
$count_nopol = mysqli_num_rows($sql_nopol);

$driver      = '';
$supplier    = '';
$supplier_id = '';
$plant_name  = '';
$plant_id    = '';
$nopol       = '';

while ($rownopol = mysqli_fetch_assoc($sql_nopol)) {
    $driver      = $rownopol["driver_name"];
    $supplier    = $rownopol["supplier_name"];
    $supplier_id = $rownopol["supplier_id"];
    $plant_name  = $rownopol["plant_name"];
    $plant_id    = $rownopol["plant_id"];
    $kode_kirim  = $rownopol["kode_pengiriman"];
    $nopol       = $rownopol["no_pol"];
}

/* 2.5 Simpan header pemeriksaan (sekali per kiriman).
   int tanpa kutip, string di-escape -> sesuai tipe kolom. */
$jam   = date("H:i:s");
$idref = time();
$seq   = 1;

if ($count_nopol != 0) {
    $query = "INSERT INTO tb_ceklist SET
                seq = $seq,
                idref = '{$idref}',
                petugas_pemeriksa = '" . mysqli_real_escape_string($con, $username) . "',
                nopol = '" . mysqli_real_escape_string($con, $nopol) . "',
                nama_transporter = '" . mysqli_real_escape_string($con, $supplier) . "',
                kode_transporter = '" . mysqli_real_escape_string($con, $supplier_id) . "',
                plant_id = '" . mysqli_real_escape_string($con, $plant_id) . "',
                plant_name = '" . mysqli_real_escape_string($con, $plant_name) . "',
                nama_sopir = '" . mysqli_real_escape_string($con, $driver) . "',
                tgl_pemeriksaan = '{$date}',
                jam_pemeriksaan = '{$jam}',
                lokasi_pemeriksaan = '" . mysqli_real_escape_string($con, $plant_name) . "',
                muatan = '" . mysqli_real_escape_string($con, $muat) . "',
                kode_kirim = '" . mysqli_real_escape_string($con, $kode_kirim) . "'";

    /* PHP 8: dua kali buka halaman dalam detik yang sama -> idref (time())
       sama -> "Duplicate entry" -> blank/500. */
    try {
        mysqli_query($con, $query);
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate entry') === false) {
            throw $e;
        }
    }
}

/* --- 3. DATA -------------------------------------------------------------- */

$ada_truck = ($count_nopol != 0);

/* sederhana: dipakai di VIEW, tidak ada query lagi di bawah ini */
function get_date()
{
    echo date("Y-m-d");
}

function get_jam()
{
    echo date("H:i:s");
}
?>
<div class="body-wrap-with-navbar">

<?php if (!$ada_truck) { ?>
    <script>
        window.alert('Schedule Truck Tidak Ditemukan...!!!');
        window.location = 'material?action=cek_nopol';
    </script>
<?php } ?>

<div class='container'>
    <form method="post" action="common?action=N_gate1" class="kir-form">

        <h4 class="kir-title">Pemeriksaan Gate 1 &mdash; Material</h4>

        <h6 class="kir-sub">Data Truck</h6>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nopol">No Polisi</label>
                <input type="text" class="form-control text-uppercase" id="nopol" name="nopol"
                       value="<?php echo htmlspecialchars((string) $nopol, ENT_QUOTES); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="driver">Nama Sopir</label>
                <input type="text" class="form-control text-uppercase" id="driver" name="driver"
                       value="<?php echo htmlspecialchars((string) $driver, ENT_QUOTES); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="supplier">Nama Supplier</label>
                <input type="text" class="form-control text-uppercase" id="supplier" name="supplier"
                       value="<?php echo htmlspecialchars((string) $supplier, ENT_QUOTES); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="kode_kirim">Kode Kirim</label>
                <input type="text" class="form-control text-uppercase" id="kode_kirim" name="kode_kirim"
                       value="<?php echo htmlspecialchars((string) $kode_kirim, ENT_QUOTES); ?>" readonly>
            </div>
        </div>

        <h6 class="kir-sub">Data Pemeriksaan</h6>

        <div class="row">
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
                <input type="text" class="form-control" id="petugas" name="petugas"
                       value="<?php echo htmlspecialchars((string) $username, ENT_QUOTES); ?>" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="lokasi">Lokasi Plant</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi"
                       value="<?php echo htmlspecialchars((string) $plant_name, ENT_QUOTES); ?>" readonly>
            </div>
        </div>

        <input type="text" id="seq" name="seq" value="<?php echo $seq; ?>" hidden>
        <input type="text" id="idref" name="idref" value="<?php echo $idref; ?>" hidden>
        <input type="text" name="muat" value="<?php echo htmlspecialchars((string) $muat, ENT_QUOTES); ?>" hidden>

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
   Sama seperti FG_cek_truck_rev: tiap field col-md-6 + mb-3 supaya tidak
   melebar sepanjang layar dan label tidak terpotong. */
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
</div>
