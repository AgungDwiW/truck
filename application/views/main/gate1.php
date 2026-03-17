<?php
/**
 * Gate 1 Inspection Page
 * 
 * This page handles the gate 1 inspection process, displaying checkpoints
 * and allowing inspectors to record findings.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
// Note: $con connection is assumed to be already available from parent context

// Initialize static helper
StaticHelper::init();

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref = $seq = $nopol = $petugas = $lokasi = '';
$seq_cek = 0;
$hasil = 'Lanjut Pemeriksaan Gate 2';
$checkpoint_data = array(); // Will store checkpoint rows from tb_ceklist_utama
$utama_values = array();    // Will store utama1..utama4 values for the current idref

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Retrieve latest inspection record for the current user
// ----------------------------------------------------------------------------
$username = User::$username;
$temp = mysqli_query($con, 
    "SELECT idref, seq, nopol, petugas_pemeriksa, lokasi_pemeriksaan 
     FROM tb_ceklist 
     WHERE petugas_pemeriksa = '$username' 
     ORDER BY tgbaca DESC 
     LIMIT 1"
);

if ($temp && mysqli_num_rows($temp) > 0) {
    $rowtemp = mysqli_fetch_assoc($temp);
    $idref   = $rowtemp["idref"];
    $seq     = $rowtemp["seq"];
    $nopol   = $rowtemp["nopol"];
    $petugas = $rowtemp["petugas_pemeriksa"];
    $lokasi  = $rowtemp["lokasi_pemeriksaan"];
}

// ----------------------------------------------------------------------------
// 2. Get current sequence number for the inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $cek = mysqli_query($con, "SELECT seq FROM tb_ceklist WHERE idref = '$idref'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $seq_cek = $row["seq"];
    }
}

// ----------------------------------------------------------------------------
// 3. If sequence is 1, process form data and update inspection record
// ----------------------------------------------------------------------------
if ($seq_cek == 1) {
    // Collect POST data (assuming they are set)
    $nopol            = $_POST['nopol'] ?? '';
    $nama_sopir       = $_POST['nama_sopir'] ?? '';
    $nama_transporter = $_POST['nama_transporter'] ?? '';
    $tipe_truck       = $_POST['tipe_truck'] ?? '';
    $tujuan           = $_POST['tujuan'] ?? '';
    $tahun            = $_POST['tahun'] ?? '';
    $jam              = $_POST['jam'] ?? '';
    $tgl              = $_POST['tgl'] ?? '';
    $petugas          = $_POST['petugas'] ?? $petugas;
    $lokasi           = $_POST['lokasi'] ?? $lokasi;
    
    $query = "
        UPDATE tb_ceklist 
        SET petugas_pemeriksa = '$petugas',
            tujuan_kirim      = '$tujuan',
            nopol             = '$nopol',
            nama_transporter  = '$nama_transporter',
            nama_sopir        = '$nama_sopir',
            jenis_kendaraan   = '$tipe_truck',
            tahun_pembuatan   = '$tahun',
            tgl_pemeriksaan   = '$tgl',
            jam_pemeriksaan   = '$jam',
            lokasi_pemeriksaan= '$lokasi',
            utama1            = '1',
            utama2            = '1',
            utama3            = '1',
            utama4            = '1',
            seq               = 0 
        WHERE idref = '$idref'
    ";
    
    mysqli_query($con, $query);
}

// ----------------------------------------------------------------------------
// 4. Update temporary status (global operation)
// ----------------------------------------------------------------------------
mysqli_query($con, "UPDATE tb_ceklist_utama SET status_temp = 1");

// ----------------------------------------------------------------------------
// 5. Fetch all checkpoints (utama) for display
// ----------------------------------------------------------------------------
$result = mysqli_query($con, 
    "SELECT *, 
            CONCAT(name, no) AS namee,
            CONCAT(ceklist_utama, no, no) AS idgreen,
            CONCAT(ceklist_utama, no, no, no) AS idred
     FROM tb_ceklist_utama 
     WHERE no BETWEEN 0 AND 4"
);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $checkpoint_data[] = $row;
    }
}

// ----------------------------------------------------------------------------
// 6. Pre‑fetch utama1..utama4 values for the current inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $utama_result = mysqli_query($con, 
        "SELECT utama1, utama2, utama3, utama4 
         FROM tb_ceklist 
         WHERE idref = '$idref'"
    );
    
    if ($utama_result && mysqli_num_rows($utama_result) > 0) {
        $utama_row = mysqli_fetch_assoc($utama_result);
        $utama_values = array(
            1 => $utama_row['utama1'],
            2 => $utama_row['utama2'],
            3 => $utama_row['utama3'],
            4 => $utama_row['utama4']
        );
    }
}

// ----------------------------------------------------------------------------
// 7. Determine overall inspection result based on utama values
// ----------------------------------------------------------------------------
foreach ($utama_values as $idx => $value) {
    if ($value == 0) {
        $hasil = 'Di Tolak di Pos 1';
        break;
    }
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
        
        /* Additional styles from original file */
        .contain {
            display: block;
            position: relative;
            padding-left: 100px;
            margin-bottom: 50px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        .contain input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 50px;
            width: 50px;
            background-color: red;
        }
        
        .contain input:checked ~ .checkmark {
            background-color: green;
        }
        
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        
        .contain input:checked ~ .checkmark:after {
            display: block;
        }
        
        .contain .checkmark:after {
            left: 20px;
            top: 10px;
            width: 10px;
            height: 20px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
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
        
        .icon_camera > input {
            display: none;
            left: 3px;
        }
        
        div.relative {
            position: relative;
            left: 4px;
            top: 23px;
        }
        
        div.cekmark {
            left: 0px;
        }
    </style>

<!-- Header: Kelengkapan Utama -->
<div class="row text-center" style="background-color: red;">
    <div class="col">
        <label style="font-size: 30px;">Kelengkapan Utama</label>
    </div>
</div>

<!-- Checkpoint List -->
<?php
$no = 1;
foreach ($checkpoint_data as $row):
    $noo = $no - 1;
    $hid = ($no > 1) ? '' : 'hidden';
    
    // Determine checkpoint status
    $cek_utama = $utama_values[$noo] ?? 1;
    $cek_img = ($cek_utama == 1) ? 'cekgreen.png' : 'red.png';
?>
    <form method="post" action="<?= route('foto_gate1') ?>">
        <div class="row text-center" style="background-color: black;">
            <div class="col" style="color: white; font-size:20px; margin-left: 10px; margin-top: 0px" <?= $hid ?>>
                <?= htmlspecialchars($row['ceklist_utama']) ?>
            </div>
            <div class="col" <?= $hid ?>>
                <input type="hidden" name="ccp" value="<?= $noo ?>">
                <input type="hidden" name="utama" value="<?= $cek_utama ?>">
                <input type="hidden" name="seq" value="<?= $seq_cek ?>">
                <input type="hidden" name="idref" value="<?= $idref ?>">
                <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
                <input type="hidden" name="petugas" value="<?= htmlspecialchars($petugas) ?>">
                <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
                
                <button type="submit" style="margin-left: 10px; margin-bottom: 0px;">
                    <?= static_img('css/img/' . $cek_img, ['width' => '30', 'height' => '30']) ?>
                </button>
            </div>
        </div>
    </form>
    <hr>
<?php
    $no++;
endforeach;
?>

<!-- Inspection Result Form -->
<form method="post" action="<?= route('simpan_gate1') ?>">
    
    <!-- Hasil Pemeriksaan -->
    <div class="row">
        <div class="col-md-12">
            <div class="text-center bg-info text-dark font-weight-bold">
                <label>Hasil Pemeriksaan :</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="text-center font-weight-bold">
                <input type="text" class="form-control bg-info text-white text-center font-weight-bold" 
                       id="hasil" name="hasil" value="<?= $hasil ?>" readonly>
            </div>
        </div>
    </div>
    <hr>
    
    <!-- Komentar Kerusakan -->
    <div class="row">
        <div class="col-md-12">
            <div class="text-center bg-info text-dark font-weight-bold">
                <label>Komentar Kerusakan :</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="text-center font-weight-bold">
                <input type="text" class="form-control" id="komentar" name="komentar">
            </div>
        </div>
    </div>
    <hr>
    
    <!-- Tindakan Perbaikan -->
    <div class="row">
        <div class="col-md-12">
            <div class="text-center bg-info text-dark font-weight-bold">
                <label>Tindakan Perbaikan :</label>
            </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="text-center font-weight-bold">
            <input type="text" class="form-control" id="tindakan" name="tindakan">
        </div>
    </div>
</div>

<!-- Hidden fields -->
<input type="hidden" name="idref" value="<?= $idref ?>">
<input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
<input type="hidden" name="petugas" value="<?= htmlspecialchars($petugas) ?>">
<input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">

<hr>
<div class="row">
    <button type="submit" class="btn btn-primary center-block">Simpan</button>
</div>
<hr>
</form>

