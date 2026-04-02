<?php
/**
 * Gate 1 Inspection Form (Initial Data Entry)
 * 
 * This page creates a new inspection record and displays a form for entering
 * truck, driver, and shipment details before proceeding to the checkpoint inspection.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include_once "application/config/connection.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref = mktime();
$seq   = 1;
$username = User::$username;

$tujuan_rows = array(); // will store tujuan_pengiriman options

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL & INSERTS
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Create a new inspection record
// ----------------------------------------------------------------------------
$query = "INSERT INTO tb_ceklist 
          SET seq = '$seq', 
              idref = '$idref', 
              petugas_pemeriksa = '$username'";

mysqli_query($con, $query);

// ----------------------------------------------------------------------------
// 2. Fetch all tujuan_pengiriman options for dropdown
// ----------------------------------------------------------------------------
$tujuan_result = mysqli_query($con, 
    "SELECT * FROM tujuan_pengiriman ORDER BY tujuan ASC"
);

if ($tujuan_result) {
    while ($row = mysqli_fetch_assoc($tujuan_result)) {
        $tujuan_rows[] = $row;
    }
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================
function get_date() {
    echo date("d-m-Y");
}

function get_jam() {
    echo date("H:i:s");
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate 1 Data Entry</title>
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
    </style>

<div class='container'>
    <form method="post" action="<?=route('gate1_new')?>">
        
        <!-- Row 1: No Polisi, Nama Sopir, Nama Transporter -->
        <div class="row justify-content-md-center">
            <div class="col">
                <label>No Polisi</label>
                <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" required>
            </div>
            
            <div class="col">
                <label>Nama Sopir</label>
                <input type="text" class="form-control text-uppercase" id="nama_sopir" name="nama_sopir" required>
            </div>
            
            <div class="col">
                <label>Nama Transporter</label>
                <input type="text" class="form-control text-uppercase" id="nama_transporter" name="nama_transporter" required>
            </div>
        </div>
        
        <!-- Row 2: Jenis Kendaraan, Tujuan Pengiriman, Tahun Pembuatan -->
        <div class="row justify-content-md-center">
            <div class="col">
                <label>Jenis Kendaraan</label>
                <input type="text" class="form-control" id="tipe_truck" name="tipe_truck" required>
            </div>
            
            <div class="col">
                <label>Tujuan Pengiriman</label>
                <select class="form-control" id="tujuan" name="tujuan">
                    <?php foreach ($tujuan_rows as $row): ?>
                    <option value="<?= htmlspecialchars($row['tujuan']) ?>">
                        <?= htmlspecialchars($row['tujuan']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col">
                <label>Tahun Pembuatan</label>
                <input type="text" class="form-control" id="tahun" name="tahun">
            </div>
        </div>
        
        <!-- Row 3: Jam Pemeriksaan, Tanggal Pemeriksaan, Petugas, Lokasi -->
        <div class="row justify-content-md-center">
            <div class="col">
                <label>Jam Pemeriksaan</label>
                <input type="text" class="form-control" id="jam" name="jam" 
                       value="<?php get_jam(); ?>" readonly>
            </div>
            
            <div class="col">
                <label>Tanggal Pemeriksaan</label>
                <input type="text" class="form-control text-uppercase" id="tgl" name="tgl" 
                       value="<?php get_date(); ?>" readonly>
            </div>
            
            <div class="col">
                <label>Petugas Pemeriksa</label>
                <input type="text" class="form-control" id="petugas" name="petugas" 
                       value="<?= htmlspecialchars(User::$username) ?>" readonly>
            </div>
            
            <div class="col">
                <label>Lokasi Plant</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" value="Pandaan" readonly>
            </div>
        </div>
        
        <!-- Hidden fields -->
        <input type="hidden" id="seq" name="seq" value="<?= $seq ?>">
        <input type="hidden" id="idref" name="idref" value="<?= $idref ?>">
        
        <hr>
        
        <!-- Submit button -->
        <div class="row">
            <button type="submit" class="btn btn-success center-block">Go Ceklist</button>
        </div>
        <hr>
        
    </form>
</div>

