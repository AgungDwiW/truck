<?php
/**
 * Gate 1 Inspection Page (New Version)
 * 
 * This version uses the new Checklist and ChecklistParam models
 * instead of direct database queries.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
// Models are autoloaded via Table.php inclusion
// We need to include our new models
include_once APP_DIR . 'models/Checklist.php';
include_once APP_DIR . 'models/ChecklistParam.php';

// Initialize static helper
StaticHelper::init();

// ============================================================================
// INITIALIZE MODELS
// ============================================================================
$checklistModel = new Checklist();
$paramModel = new ChecklistParam();

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref = $seq = $nopol = $petugas = $lokasi = '';
$seq_cek = 0;
$hasil = 'Lanjut Pemeriksaan Gate 2';
$checkpoint_data = array();
$utama_values = array();

// ============================================================================
// DATA RETRIEVAL USING MODELS
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Retrieve latest inspection record for the current user
// ----------------------------------------------------------------------------
$username = User::$username;
if ($username) {
    $latestChecklist = $checklistModel->get()
        ->where(['petugas_pemeriksa' => $username])
        ->order('tgbaca DESC')
        ->limit(0, 1)
        ->fetchOne();
    
    if ($latestChecklist) {
        $idref   = $latestChecklist["idref"];
        $seq     = $latestChecklist["seq"];
        $nopol   = $latestChecklist["nopol"];
        $petugas = $latestChecklist["petugas_pemeriksa"];
        $lokasi  = $latestChecklist["lokasi_pemeriksaan"];
    }
}

// ----------------------------------------------------------------------------
// 2. Get current sequence number for the inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $checklist = $checklistModel->get()
        ->where(['idref' => $idref])
        ->fetchOne();
    
    if ($checklist) {
        $seq_cek = $checklist["seq"];
    }
}

// ----------------------------------------------------------------------------
// 3. If sequence is 1, process form data and update inspection record
// ----------------------------------------------------------------------------
if ($seq_cek == 1) {
    // Collect POST data
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
    
    // Update checklist data
    $updateData = [
        'petugas_pemeriksa' => $petugas,
        'tujuan_kirim'      => $tujuan,
        'nopol'             => $nopol,
        'nama_transporter'  => $nama_transporter,
        'nama_sopir'        => $nama_sopir,
        'jenis_kendaraan'   => $tipe_truck,
        'tahun_pembuatan'   => $tahun,
        'tgl_pemeriksaan'   => $tgl,
        'jam_pemeriksaan'   => $jam,
        'lokasi_pemeriksaan'=> $lokasi,
        'seq'               => 0
    ];
    
    // For this example, we'll set the first 4 utama parameters to 1 (good)
    // In a real implementation, you'd get these from the form
    $paramUpdates = [
        1 => 1, // utama1
        2 => 1, // utama2  
        3 => 1, // utama3
        4 => 1  // utama4
    ];
    
    $checklistModel->updateWithParams($checklist['no'], $updateData, $paramUpdates);
}

// ----------------------------------------------------------------------------
// 4. Update temporary status in parameters (if needed)
// ----------------------------------------------------------------------------
// Note: status_temp functionality might need to be reimplemented
// For now, we'll skip this as it's application-specific

// ----------------------------------------------------------------------------
// 5. Fetch checkpoints (utama parameters) for display
// ----------------------------------------------------------------------------
$utamaParams = $paramModel->getByType('utama');
// Limit to first 5 for this example (matching original query: no BETWEEN 0 AND 4)
$checkpoint_data = array_slice($utamaParams, 0, 5);

// ----------------------------------------------------------------------------
// 6. Get parameter values for the current inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $checklistWithParams = $checklistModel->getWithParams($checklist['no'] ?? 0);
    
    if ($checklistWithParams && !empty($checklistWithParams['parameters'])) {
        // Extract values for the first 4 parameters
        foreach ($checklistWithParams['parameters'] as $param) {
            if ($param['id'] >= 1 && $param['id'] <= 4) {
                $utama_values[$param['id']] = $param['value'] ?? 1;
            }
        }
    }
}

// ----------------------------------------------------------------------------
// 7. Determine overall inspection result
// ----------------------------------------------------------------------------
foreach ($utama_values as $idx => $value) {
    if ($value == 0) {
        $hasil = 'Di Tolak di Pos 1';
        break;
    }
}

// ============================================================================
// HTML OUTPUT
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate 1 Inspection (New Model)</title>
    <style type="text/css">
        body {
            background-color: transparent;
        }
        
        /* Reuse existing styles from original */
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
    </style>

<div class="alert alert-info">
    <strong>Note:</strong> This is the new version using Checklist models. 
    <a href="<?= route('gate1') ?>">Switch to old version</a>
</div>

<!-- Header: Kelengkapan Utama -->
<div class="row text-center" style="background-color: red;">
    <div class="col">
        <label style="font-size: 30px;">Kelengkapan Utama (New Model)</label>
    </div>
</div>

<!-- Checkpoint List -->
<?php
$no = 1;
foreach ($checkpoint_data as $row):
    $noo = $no - 1;
    $hid = ($no > 1) ? '' : 'hidden';
    
    // Determine checkpoint status
    $cek_utama = $utama_values[$no] ?? 1; // Note: $no instead of $noo to match param_id
    $cek_img = ($cek_utama == 1) ? 'cekgreen.png' : 'red.png';
?>
    <form method="post" action="<?= route('foto_gate1') ?>">
        <div class="row text-center" style="background-color: black;">
            <div class="col" style="color: white; font-size:20px; margin-left: 10px; margin-top: 0px" <?= $hid ?>>
                <?= htmlspecialchars($row['param_name']) ?>
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
        <button type="submit" class="btn btn-primary center-block">Simpan (New Model)</button>
    </div>
    <hr>
</form>

<!-- Model Usage Example -->
<div class="alert alert-success">
    <h4>Model Usage Example:</h4>
    <pre>
// Initialize models
$checklist = new Checklist();
$params = new ChecklistParam();

// Get checklist with parameters
$data = $checklist->getWithParams(123);

// Create new checklist
$checklistData = [
    'idref' => 'TRUCK-001',
    'nopol' => 'B1234XYZ',
    // ... other fields
];

$paramValues = [
    1 => 1,  // utama1 = good
    2 => 0,  // utama2 = bad
    // ... other parameters
];

$id = $checklist->createWithParams($checklistData, $paramValues);
    </pre>
</div>

