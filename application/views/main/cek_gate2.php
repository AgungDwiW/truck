<?php
/**
 * Gate 2 Inspection Page
 * 
 * This page displays the Gate 2 inspection form, including driver and vehicle
 * information, and allows inspectors to check completeness of main and additional
 * checkpoints.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include_once "application/config/connection.php";
include "application/assets/function.php";
include_once "application/config/connectionEvisitor.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$kode = mysqli_real_escape_string($con, $_POST['kode'] ?? '');

$muat = $nopol = $nama_sopir = $plant_id = $date = '';
$data = array();

$umul = $status_usia = $color_usia = $tipe_sim = $valid = $valid_ddt = '';
$status_sim = $status_ddt = $valid_date = $valid_date_ddt = '';
$is_sim_valid = false;
$is_ddt_valid = false;

$utama_rows = array();
$tambahan_rows = array();

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch base checklist data
// ----------------------------------------------------------------------------
$query_mysql = mysqli_query($con, 
    "SELECT * FROM tb_ceklist WHERE no = '$kode'"
) or die(mysqli_error($con));

if (mysqli_num_rows($query_mysql) == 0) {
    die("Data tidak ditemukan.");
}

$data = mysqli_fetch_array($query_mysql);

$muat      = $data['muatan'];
$nopol     = $data['nopol'];
$nama_sopir = $data['nama_sopir'] ?? '';
$plant_id  = User::$plantid;
$date      = date("Y-m-d");

// ----------------------------------------------------------------------------
// 2. If muatan is FG, fetch e‑Visitor data and perform validations
// ----------------------------------------------------------------------------
if ($muat == 'FG') {
    // Clean nopol for matching
    $clean_nopol = str_replace(' ', '', $nopol);
    $sql_nop = mysqli_query($con_3, 
        "SELECT * FROM tbl_visit 
         WHERE REPLACE(no_pol, ' ', '') = '$clean_nopol' 
         ORDER BY tanggal_datang DESC LIMIT 1"
    );
    
    if (mysqli_num_rows($sql_nop) == 0) {
        echo "<script>
                alert('No Pol belum di input di e_Visitor...!!!');
                window.location = 'index';
              </script>";
        exit;
    }
    
    $row_nop = mysqli_fetch_assoc($sql_nop);
    $nama_sopir  = $row_nop['nama_visitor'];
    $seq_visitor = $row_nop['seq_visitor'];
    
    // Get visitor details
    $sql_visitor = mysqli_query($con_3, 
        "SELECT * FROM tbm_visitor WHERE seq = '$seq_visitor' LIMIT 1"
    );
    
    if ($sql_visitor && mysqli_num_rows($sql_visitor) > 0) {
        $row_visitor = mysqli_fetch_assoc($sql_visitor);
        $tgl_lahir   = $row_visitor['tanggal_lahir'];
        $valid       = $row_visitor['valid_id_date'];
        $tipe_sim    = $row_visitor['tipe_id'];
        $valid_ddt   = $row_visitor['valid_ddt_date'];
        
        // Date calculations
        $lahir   = new DateTime($tgl_lahir);
        $val     = new DateTime($valid);
        $val_ddt = new DateTime($valid_ddt);
        $today   = new DateTime();
        $today->setTime(0, 0, 0);
        
        $umul = $today->diff($lahir)->y;
        
        // Risk Assessment based on age
        if ($umul <= 55) {
            $color_usia = 'green';
            $status_usia = 'Low Risk';
        } elseif ($umul > 55 && $umul <= 60) {
            $color_usia = 'yellow';
            $status_usia = 'Medium Risk';
        } else {
            $color_usia = 'red';
            $status_usia = 'High Risk';
        }
        
        // SIM & DDT Expiry Logic
        $is_sim_valid = ($val >= $today);
        $is_ddt_valid = ($val_ddt >= $today);
        
        $status_sim = $is_sim_valid ? 'SIM Masih Berlaku' : 'SIM Sudah Kadaluwarsa';
        $status_ddt = $is_ddt_valid ? 'ID DDT Masih Berlaku' : 'ID DDT Sudah Kadaluwarsa';
        
        $valid_date     = "$status_sim || Expired Date : $valid";
        $valid_date_ddt = "$status_ddt || Expired Date : $valid_ddt";
        
        // Update DB with fresh visitor data
        $update_query = "
            UPDATE tb_ceklist 
            SET nama_sopir        = '$nama_sopir',
                usia              = '$umul',
                jenis_sim         = '$tipe_sim',
                expired_date_sim  = '$valid',
                expired_date_ddt  = '$valid_ddt',
                status_sim        = '$status_sim',
                status_ddt        = '$status_ddt',
                status_usia       = '$status_usia' 
            WHERE no = '$kode'
        ";
        
        mysqli_query($con, $update_query);
    }
}

// ----------------------------------------------------------------------------
// 3. Fetch main checkpoints (utama) for display
// ----------------------------------------------------------------------------
$result_utama = mysqli_query($con, 
    "SELECT *, 
            CONCAT(name, no) AS namee,
            CONCAT(ceklist_utama, no, no) AS idgreen 
     FROM tb_ceklist_utama 
     WHERE no > 4"
);

if ($result_utama) {
    while ($row = mysqli_fetch_assoc($result_utama)) {
        $utama_rows[] = $row;
    }
}

// ----------------------------------------------------------------------------
// 4. Fetch additional checkpoints (tambahan) for display
// ----------------------------------------------------------------------------
$result_tambahan = mysqli_query($con, 
    "SELECT *, 
            CONCAT(name, no) AS namee,
            CONCAT(ceklist_tambahan, no, no) AS idgreen 
     FROM tb_ceklist_tambahan"
);

if ($result_tambahan) {
    while ($row = mysqli_fetch_assoc($result_tambahan)) {
        $tambahan_rows[] = $row;
    }
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate 2 Inspection</title>
    <style type="text/css">
        body { background-color: #f8f9fa; }
        .contain {
            display: block; position: relative; cursor: pointer; 
            font-size: 22px; user-select: none; padding-bottom: 20px;
        }
        .contain input { position: absolute; opacity: 0; cursor: pointer; }
        .checkmark {
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            height: 40px; width: 40px; background-color: #dc3545; 
            border-radius: 5px; transition: 0.2s;
        }
        .contain input:checked ~ .checkmark { background-color: #28a745; }
        .checkmark:after {
            content: ""; position: absolute; display: none;
            left: 15px; top: 5px; width: 10px; height: 20px;
            border: solid white; border-width: 0 3px 3px 0; transform: rotate(45deg);
        }
        .contain input:checked ~ .checkmark:after { display: block; }
    </style>
</head>
<body>

<div class="container bg-white p-4 shadow-sm rounded mt-3 mb-5">
    <form method="post" action="lanjut_gate2">
        <input type="hidden" id="code" name="code" value="<?= htmlspecialchars($kode) ?>">

        <h4 class="mb-4 text-primary border-bottom pb-2">Informasi Kendaraan & Pengemudi</h4>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">No Polisi</label>
                <input type="text" class="form-control text-uppercase bg-light" 
                       name="nopol" value="<?= htmlspecialchars($nopol) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Tanggal & Jam Pemeriksaan</label>
                <input type="text" class="form-control bg-light" 
                       value="<?= htmlspecialchars($data['tgl_pemeriksaan'] . ' ' . $data['jam_pemeriksaan']) ?>" readonly>
                <input type="hidden" name="tgl" value="<?= htmlspecialchars($data['tgl_pemeriksaan']) ?>">
                <input type="hidden" name="jam" value="<?= htmlspecialchars($data['jam_pemeriksaan']) ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Nama Sopir</label>
                <input type="text" class="form-control text-uppercase bg-light" 
                       name="nama_sopir" value="<?= htmlspecialchars($nama_sopir) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Nama Transporter</label>
                <input type="text" class="form-control text-uppercase bg-light" 
                       name="nama_transporter" value="<?= htmlspecialchars($data['nama_transporter']) ?>" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Jenis Kendaraan</label>
                <input type="text" class="form-control bg-light" 
                       name="tipe_truck" value="<?= htmlspecialchars($data['jenis_kendaraan']) ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Lokasi & Petugas</label>
                <input type="text" class="form-control bg-light" 
                       name="lokasi" value="<?= htmlspecialchars($data['lokasi_pemeriksaan'] . ' - ' . User::$username) ?>" readonly>
                <input type="hidden" name="petugas" value="<?= htmlspecialchars(User::$username) ?>">
            </div>
        </div>

        <?php if ($muat == 'FG'): ?>
        <hr class="my-4">
        <h5 class="mb-3 text-success">Kelengkapan FG (Finished Goods)</h5>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="fw-bold">ID Shipment</label>
                <input type="text" class="form-control text-uppercase bg-light" 
                       name="kode_kirim" value="<?= htmlspecialchars($data['id_barang']) ?>" readonly>
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Usia Sopir</label>
                <input type="text" class="form-control fw-bold" 
                       style="background-color: <?= $color_usia ?>; color: <?= ($color_usia == 'yellow' ? 'black' : 'white') ?>;" 
                       value="<?= "$umul Tahun ($status_usia)" ?>" readonly>
                <input type="hidden" name="usia" value="<?= $umul ?>">
                <input type="hidden" name="status_usia" value="<?= $status_usia ?>">
            </div>
            <div class="col-md-4">
                <label class="fw-bold">Jenis SIM</label>
                <input type="text" class="form-control bg-light" 
                       name="tipe_sim" value="<?= htmlspecialchars($tipe_sim) ?>" readonly>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label class="fw-bold">Status SIM</label>
                <input type="text" class="form-control text-white fw-bold" 
                       style="background-color: <?= $is_sim_valid ? '#28a745' : '#dc3545' ?>;" 
                       value="<?= htmlspecialchars($valid_date) ?>" readonly>
                <input type="hidden" name="expired_sim" value="<?= htmlspecialchars($valid) ?>">
                <input type="hidden" name="status_sim" value="<?= htmlspecialchars($status_sim) ?>">
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Status ID DDT</label>
                <input type="text" class="form-control text-white fw-bold" 
                       style="background-color: <?= $is_ddt_valid ? '#28a745' : '#dc3545' ?>;" 
                       value="<?= htmlspecialchars($valid_date_ddt) ?>" readonly>
                <input type="hidden" name="expired_ddt" value="<?= htmlspecialchars($valid_ddt) ?>">
                <input type="hidden" name="status_ddt" value="<?= htmlspecialchars($status_ddt) ?>">
            </div>
        </div>
        <?php endif; ?>

        <h4 class="mt-5 bg-danger text-white text-center py-2 rounded">Kelengkapan Utama</h4>
        <div class="row mt-3">
            <?php foreach ($utama_rows as $row): ?>
            <div class="col-md-6 mb-4 text-center">
                <div class="card shadow-sm border-danger h-100">
                    <div class="card-body">
                        <p class="fw-bold fs-5 mb-4"><?= htmlspecialchars($row['ceklist_utama']) ?></p>
                        <label class="contain w-100">
                            <input type="checkbox" id="<?= htmlspecialchars($row['idgreen']) ?>" 
                                   name="<?= htmlspecialchars($row['namee']) ?>" value="1" onchange="tambahan()">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <h4 class="mt-5 bg-warning text-dark text-center py-2 rounded">Kelengkapan Tambahan</h4>
        <p class="text-center text-muted small">
            Jika ada point Kelengkapan Tambahan tidak terpenuhi maka segera dilakukan tindakan perbaikan 
            sesuai batas waktu yang telah ditentukan
        </p>
        <div class="row mt-3">
            <?php foreach ($tambahan_rows as $row): ?>
            <div class="col-md-4 mb-4 text-center">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-body">
                        <p class="fw-bold mb-4"><?= htmlspecialchars($row['ceklist_tambahan']) ?></p>
                        <label class="contain w-100">
                            <input type="checkbox" id="<?= htmlspecialchars($row['idgreen']) ?>" 
                                   name="<?= htmlspecialchars($row['namee']) ?>" value="1" onchange="tambahan()">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <hr class="my-5">
        <h4 class="text-primary border-bottom pb-2">Kesimpulan Pemeriksaan</h4>
        
        <div class="mb-3">
            <label class="fw-bold">Hasil Pemeriksaan:</label>
            <input type="text" class="form-control bg-info text-white fw-bold text-center fs-5" 
                   id="hasil" name="hasil" readonly>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Komentar Kerusakan:</label>
            <input type="text" class="form-control" id="komentar" name="komentar" 
                   required placeholder="Tuliskan komentar...">
        </div>

        <div class="mb-4">
            <label class="fw-bold">Tindakan Perbaikan:</label>
            <input type="text" class="form-control" id="tindakan" name="tindakan" 
                   required placeholder="Tuliskan tindakan perbaikan...">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                Simpan Data Gate 2
            </button>
        </div>

    </form>
</div>
</body>
</html>