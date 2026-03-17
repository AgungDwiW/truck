<?php
/**
 * Gate 1 Inspection Page (New Version)
 * 
 * This page displays the main checkpoints for Gate 1 inspection and allows
 * inspectors to record findings and upload photos.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include  "application/config/connection.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref       = $_POST['idref'] ?? '';
$nopol       = $_POST['nopol'] ?? '';
$lokasi      = User::$plant_name;
$kode_kirim  = $_POST['kode_kirim'] ?? '';
$muat        = $_POST['muat'] ?? '';
$driver      = $_POST['driver'] ?? '';
$supplier    = $_POST['supplier'] ?? '';
$transporter = $_POST['transporter'] ?? '';
$usia        = $_POST['usia'] ?? 0;
$tipe_sim    = $_POST['tipe_sim'] ?? '';
$expired_sim = $_POST['expired_sim'] ?? '2999-12-30';
$expired_ddt = $_POST['expired_ddt'] ?? '2999-12-30';
$status_sim  = $_POST['status_sim'] ?? '';
$status_ddt  = $_POST['status_ddt'] ?? '';
$status_usia = $_POST['status_usia'] ?? '';
$id_barang   = $_POST['id_barang'] ?? '';
$tipe_truck  = '';

$seq = $_POST['seq'] ?? '';
$jam = $_POST['jam'] ?? '';
$date = $_POST['tgl'] ?? '';
$plant_id = $_POST['plant_id'] ?? '';
$username = User::$username;

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL & UPDATES
// ============================================================================

// ----------------------------------------------------------------------------
// 1. If muat is FG, determine truck type and insert inspection record
// ----------------------------------------------------------------------------
if ($muat == 'FG') {
    $query_truck = "
        SELECT jenis_truck 
        FROM tbm_tempat_muat 
        WHERE id_tempat_muat = '$plant_id' 
          AND nama_supplier = '$supplier' 
          AND nama_transporter = '$transporter' 
        GROUP BY nama_transporter 
        LIMIT 1
    ";
    
    $cari_tipe_truck = mysqli_query($con, $query_truck);
    
    if ($row = mysqli_fetch_assoc($cari_tipe_truck)) {
        $tipe_truck = $row['jenis_truck'];
    }
    
    $query_insert = "
        INSERT INTO tb_ceklist 
        SET seq                = '$seq',
            idref              = '$idref',
            petugas_pemeriksa  = '$username',
            nopol              = '$nopol',
            nama_supplier      = '$supplier',
            nama_transporter   = '$transporter',
            jenis_kendaraan    = '$tipe_truck',
            plant_id           = '$plant_id',
            plant_name         = '$lokasi',
            nama_sopir         = '$driver',
            tgl_pemeriksaan    = '$date',
            jam_pemeriksaan    = '$jam',
            lokasi_pemeriksaan = '$lokasi',
            muatan             = '$muat',
            usia               = '$usia',
            jenis_sim          = '$tipe_sim',
            expired_date_sim   = '$expired_sim',
            expired_date_ddt   = '$expired_ddt',
            status_sim         = '$status_sim',
            status_ddt         = '$status_ddt',
            status_usia        = '$status_usia',
            id_barang          = '$id_barang'
    ";
    
    mysqli_query($con, $query_insert);
}

// ----------------------------------------------------------------------------
// 2. Update temporary status (global operation)
// ----------------------------------------------------------------------------
mysqli_query($con, "UPDATE tb_ceklist_utama SET status_temp = 1");

// ----------------------------------------------------------------------------
// 3. Fetch current inspection record (if exists)
// ----------------------------------------------------------------------------
$row_utama = array();
if (!empty($idref)) {
    $ceklist_query = mysqli_query($con, 
        "SELECT * FROM tb_ceklist WHERE idref = '$idref' LIMIT 1"
    );
    
    if ($ceklist_query && mysqli_num_rows($ceklist_query) > 0) {
        $row_utama = mysqli_fetch_assoc($ceklist_query);
    }
}

// ----------------------------------------------------------------------------
// 4. Fetch all checkpoints (utama) for display
// ----------------------------------------------------------------------------
$result = mysqli_query($con, 
    "SELECT *, 
            CONCAT(name, no) AS namee,
            CONCAT(ceklist_utama, no, no) AS idgreen,
            CONCAT(ceklist_utama, no, no, no) AS idred
     FROM tb_ceklist_utama 
     WHERE no BETWEEN 0 AND 4"
);

$checkpoints = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $checkpoints[] = $row;
    }
}

// ----------------------------------------------------------------------------
// 5. Determine overall inspection result based on utama values
// ----------------------------------------------------------------------------
$hasil = 'Lanjut Pemeriksaan Gate 2';
$hasil_color = 'bg-success';

foreach ($checkpoints as $index => $row) {
    $noo = $index; // actually we need to compute the 'utama' number
    // The original logic uses $no-1 where $no starts at 1
    // We'll compute later in loop.
}

// We'll compute during the loop below, but we need to precompute status.
// Let's create an array of utama values from $row_utama.
$utama_values = array();
for ($i = 0; $i <= 4; $i++) {
    $utama_values[$i] = $row_utama["utama".$i] ?? 1;
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate 1 Inspection</title>
    <style type="text/css">
        body {
            background-color: transparent;
        }
        
        .header-box { 
            border-bottom-left-radius: 0; 
            border-bottom-right-radius: 0; 
        }
        
        .input-box { 
            border-top-left-radius: 0; 
            border-top-right-radius: 0; 
        }
    </style>

<div class="container-fluid mt-3 mb-5" style="max-width: 800px;">
    
    <!-- Header: Kelengkapan Utama -->
    <div class="row text-center bg-danger text-white py-2 mb-3 rounded shadow-sm">
        <div class="col">
            <h3 class="m-0 fw-bold" style="font-size: 26px;">Kelengkapan Utama</h3>
        </div>
    </div>
    
<!-- Checkpoint List -->
<?php
$no = 1;
$hasil = 'Lanjut Pemeriksaan Gate 2';
$hasil_color = 'bg-success';

foreach ($checkpoints as $row):
    $noo = $no - 1;
    // All checkpoints are now visible (removed hidden logic)
    $cek_utama = $utama_values[$noo] ?? 1;
    
    if ($cek_utama == 1) {
        $cek_img = 'cekgreen.png';
    } else {
        $cek_img = 'red.png';
        $hasil = 'Di Tolak di Pos 1';
        $hasil_color = 'bg-danger';
    }
?>
<form method="post" action="<?=route('N_foto_gate1')?>" style="margin-bottom: 12px;">
    <div class="row" style="background-color: #212529; color: white; border-radius: 6px; padding: 10px 0; margin: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="col-xs-10" style="font-size: 16px; font-weight: 500; line-height: 1.8; padding-left: 15px; white-space: normal; word-wrap: break-word;">
            <?= htmlspecialchars($row['ceklist_utama']) ?>
        </div>
        <div class="col-xs-2 text-right" style="padding-right: 10px;">
            <input type="hidden" name="utama" value="<?= $noo ?>">
            <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
            <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
            <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
            <input type="hidden" name="ceklist" value="<?= htmlspecialchars($row['ceklist_utama']) ?>">
            <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
            <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
            <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
            
            <button type="submit" class="btn btn-default btn-sm" style="padding: 3px 8px; background-color: #f8f9fa; border: none; border-radius: 4px;">
                <?= static_img('css/img/' . $cek_img, ['width' => '26', 'height' => '26']) ?>
            </button>
        </div>
    </div>
</form>
<?php
    $no++;
endforeach;
?>
    <hr class="my-4">
    
    <!-- Inspection Result Form -->
    <form method="post" action="<?=route('simpan_gate1')?>">
        
        <!-- Hasil Pemeriksaan -->
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Hasil Pemeriksaan :</label>
            </div>
            <input type="text" class="form-control input-box text-white text-center fw-bold fs-5 py-2 <?= $hasil_color ?> border-info" 
                   id="hasil" name="hasil" value="<?= $hasil ?>" readonly>
        </div>
        
        <!-- Komentar Kerusakan -->
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Komentar Kerusakan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" 
                   id="komentar" name="komentar" required placeholder="Tulis komentar disini...">
        </div>
        
        <!-- Tindakan Perbaikan -->
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Tindakan Perbaikan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" 
                   id="tindakan" name="tindakan" required placeholder="Tulis tindakan perbaikan...">
        </div>
        
        <!-- Hidden fields -->
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="petugas" value="<?= htmlspecialchars($username) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        
        <!-- Submit button -->
        <div class="d-grid gap-2 mt-4" style='width:100%'>
            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style='width:100%'>
                Simpan Data
            </button>
        </div>
    </form>
</div>

