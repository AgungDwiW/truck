<?php
/**
 * Truck Check & Synchronization Page
 * 
 * This page verifies delivery codes, synchronizes data between cloud and local
 * databases, and prepares the Gate 1 inspection form.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
// Note: Database connections $concloud, $conSL, $con2, $con are assumed to be already available.

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
include  "application/config/connectioncloud.php";
include  "application/config/connection.php";
$kode_kirim = $_POST['kode_kirim'] ?? '';
$muat       = $_POST['muat'] ?? '';

$debug = (User::$username == 'Latihan Wonosobo 1') ? 1 : 0;

$count_cloud = $count_local = 0;
$pengiriman_cloud = $pengiriman_local = array();
$item_cloud = $item_local = array();
$pengiriman_synced = 0;
$item_synced = array();

$date = date("Y-m-d");
$jam  = date("H:i:s");
$idref = time();
$seq   = 1;
$username = User::$username;

$driver = $supplier = $supplier_id = $plant_name = $plant_id = $nopol = '';
$count_nopol = 0;

$tujuan_options = array(); // will store tujuan_pengiriman rows

// ============================================================================
// DATABASE QUERIES - SYNC LOGIC
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Check credibility of on‑premise data (cloud vs local)
// ----------------------------------------------------------------------------

// 1.1 Cloud pengiriman data
$result = mysqli_query($concloud, 
    "SELECT * FROM tbl_pengiriman_combined WHERE kode_pengiriman = '{$kode_kirim}'"
);
$count_cloud = mysqli_num_rows($result);
$pengiriman_cloud = mysqli_fetch_assoc($result);

// 1.2 Local pengiriman data
$result = mysqli_query($conSL, 
    "SELECT * FROM tbl_pengiriman WHERE kode_pengiriman = '{$kode_kirim}'"
);
$count_local = mysqli_num_rows($result);
$pengiriman_local = mysqli_fetch_assoc($result);

// 1.3 Cloud item pengiriman data
$result = mysqli_query($concloud, 
    "SELECT * FROM tbl_item_pengiriman WHERE pengiriman_id = '{$kode_kirim}'"
);
while ($row = mysqli_fetch_assoc($result)) {
    $item_cloud[$row['kode_item_kirim']] = $row;
}

// 1.4 Local item pengiriman data
$result = mysqli_query($conSL, 
    "SELECT * FROM tbl_item_pengiriman WHERE pengiriman_id = '{$kode_kirim}'"
);
while ($row = mysqli_fetch_assoc($result)) {
    $item_local[$row['kode_item_kirim']] = $row;
}

// Debug output (only for specific user)
if ($debug) {
    printpre(['n' => 'cloud', 'pengiriman' => $pengiriman_cloud, 'item' => array_keys($item_cloud)]);
    printpre(['n' => 'local', 'pengiriman' => $pengiriman_local, 'item' => array_keys($item_local)]);
    printpre("aaaaaaaaaaaaaa");
}

// ----------------------------------------------------------------------------
// 2. Sync pengiriman table if counts differ
// ----------------------------------------------------------------------------
if ($count_cloud != $count_local) {
    $pengiriman_synced = 1;
    $col = array_keys($pengiriman_cloud);
    $values = array_values($pengiriman_cloud);
    $col = "`" . implode("`, `", $col) . "`";
    $val = "";
    
    foreach ($values as $v) {
        if ($v == '') {
            $val .= "NULL, ";
        } else {
            $val .= "'$v', ";
        }
    }
    
    $val = rtrim($val, ", ");
    $SQL = "INSERT INTO tbl_pengiriman ({$col}) VALUES ({$val})";
    
    if ($debug) printpre($SQL);
    
    mysqli_query($conSL, $SQL);
    if ($debug) print_r(mysqli_error($conSL));
}

// ----------------------------------------------------------------------------
// 3. Sync item_pengiriman table for missing items
// ----------------------------------------------------------------------------
foreach ($item_cloud as $key => $row_cloud) {
    if (!isset($item_local[$key])) {
        $item_synced[] = $key;
        $col = array_keys($row_cloud);
        $val = array_values($row_cloud);
        $col = "`" . implode("`, `", $col) . "`";
        $val_str = '';
        
        foreach ($val as $item) {
            if ($item == '') {
                $val_str .= 'NULL,';
            } else {
                $val_str .= "'{$item}',";
            }
        }
        
        $val_str = rtrim($val_str, ",");
        $SQL = "REPLACE INTO tbl_item_pengiriman ({$col}) VALUES ({$val_str})";
        
        if ($debug) printpre($SQL);
        
        mysqli_query($conSL, $SQL);
        if ($debug) printpre(mysqli_error($conSL));
    }
}

// Stop execution for debug user
if ($debug) {
    exit();
}

// ============================================================================
// DATABASE QUERIES - DELIVERY DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 4. Check if the delivery exists for today
// ----------------------------------------------------------------------------
$sql = "SELECT * FROM tbl_pengiriman_combined
     WHERE kode_pengiriman = '$kode_kirim' AND tgl_kedatangan = '$date'";
$sql_nopol = mysqli_query($concloud, $sql);
$count_nopol = mysqli_num_rows($sql_nopol);

if ($count_nopol == 0) {
    echo "<script>
            window.alert('Schedule Truck Tidak Ditemukan...!!!');
            window.location = 'cek_nopol';
          </script>";
    exit;
}

// ----------------------------------------------------------------------------
// 5. Extract delivery details
// ----------------------------------------------------------------------------
while ($rownopol = mysqli_fetch_assoc($sql_nopol)) {
    $driver       = $rownopol["driver_name"];
    $supplier     = $rownopol["supplier_name"];
    $supplier_id  = $rownopol["supplier_id"];
    $plant_name   = $rownopol["plant_name"];
    $plant_id     = $rownopol["plant_id"];
    $kode_kirim   = $rownopol["kode_pengiriman"];
    $nopol        = $rownopol["no_pol"];
}

// ----------------------------------------------------------------------------
// 6. Insert a new inspection record if delivery exists
// ----------------------------------------------------------------------------
if ($count_nopol != 0) {
    $query = "
        INSERT INTO tbl_checklist 
        SET seq                = '$seq',
            idref              = '$idref',
            petugas_pemeriksa  = '$username',
            nopol              = '$nopol',
            nama_transporter   = '$supplier',
            kode_transporter   = '$supplier_id',
            plant_id           = '$plant_id',
            plant_name         = '$plant_name',
            nama_sopir         = '$driver',
            tgl_pemeriksaan    = '$date',
            jam_pemeriksaan    = '$jam',
            lokasi_pemeriksaan = '$plant_name',
            muatan             = '$muat',
            kode_kirim         = '$kode_kirim'
    ";
    
    mysqli_query($con, $query);
    $id_checklist = mysqli_insert_id($con);
}

// ----------------------------------------------------------------------------
// 7. Fetch tujuan_pengiriman options for dropdown (if needed in future)
// ----------------------------------------------------------------------------
$tujuan_result = mysqli_query($con, 
    "SELECT * FROM tujuan_pengiriman ORDER BY tujuan ASC"
);
while ($row = mysqli_fetch_assoc($tujuan_result)) {
    $tujuan_options[] = $row;
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truck Check</title>
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
    <form method="post" action="<?=route("N_gate1")?>">
        <input type='hidden' name='id_checklist' value='<?=$id_checklist?>'>
        <!-- Row 1: No Polisi, Nama Sopir, Nama Supplier -->
        <div class="row justify-content-md-center">
            <div class="col">
                <label>No Polisi</label>
                <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" 
                       value="<?= htmlspecialchars($nopol) ?>" readonly>
            </div>
            
            <div class="col">
                <label>Nama Sopir</label>
                <input type="text" class="form-control text-uppercase" id="driver" name="driver" 
                       value="<?= htmlspecialchars($driver) ?>" readonly>
            </div>
            
            <div class="col">
                <label>Nama Supplier</label>
                <input type="text" class="form-control text-uppercase" id="supplier" name="supplier" 
                       value="<?= htmlspecialchars($supplier) ?>" readonly>
            </div>
        </div>
        
        <!-- Row 2: Jam Pemeriksaan, Tanggal Pemeriksaan, Petugas, Lokasi -->
        <div class="row justify-content-md-center">
            <div class="col">
                <label>Jam Pemeriksaan</label>
                <input type="text" class="form-control" id="jam" name="jam" 
                       value="<?= date('H:i:s') ?>" readonly>
            </div>
            
            <div class="col">
                <label>Tanggal Pemeriksaan</label>
                <input type="text" class="form-control text-uppercase" id="tgl" name="tgl" 
                       value="<?= date('Y-m-d') ?>" readonly>
            </div>
            
            <div class="col">
                <label>Petugas Pemeriksa</label>
                <input type="text" class="form-control" id="petugas" name="petugas" 
                       value="<?= htmlspecialchars(User::$username) ?>" readonly>
            </div>
            
            <div class="col">
                <label>Lokasi Plant</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" 
                       value="<?= htmlspecialchars($plant_name) ?>" readonly>
            </div>
        </div>
        
        <!-- Hidden fields -->
        <input type="hidden" id="seq" name="seq" value="<?= $seq ?>">
        <input type="hidden" id="idref" name="idref" value="<?= $idref ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        
        <hr>
        
        <!-- Submit button -->
        <div class="row">
            <button type="submit" class="btn btn-success center-block">Go Ceklist</button>
        </div>
        <hr>
        
    </form>
</div>

