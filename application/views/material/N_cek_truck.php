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
include  "application/config/connection.php";

// API call (kept for compatibility, not used directly in UI)
$plant_id =User::$plantid;
$url =  API_SERVER ."PurchaseOrders?IncludeUnitConversion=true&PoNumbers={$_POST['no_po']}";
$data = ApiCall("GET", $url,"");

// Debuger::show();
// Debuger::dump($data);
$data = json_decode($data,1);

// Debuger::dump($data);
// exit();
if (isset($data['status']) && $data['status'] == 404){
        echo "<script>
            window.alert('PO Tidak Ditemukan...!!!');
            window.location = 'cek_nopol';
          </script>";
    exit;
}
else{
    $data = $data[0];
    if ($data["siteId"] != User::$plantid){
        $plant = User::$plantid;
         echo "<script>
            window.alert('PO Tidak Sesuai dengan user (PO : {$data["siteId"]}) vs (User : {$plant})');
            window.location = 'cek_nopol';
          </script>";
        exit;
    }
}


$muat       = $_POST['muat'] ?? '';


$date = date("Y-m-d");
$jam  = date("H:i:s");
$idref = time();
$seq   = 1;
$username = User::$username;

$no_po       = $_POST['no_po'];
$supplier     = $data["purchaseOrderVendor"]["vendorName"];
$supplier_id  = $data["purchaseOrderVendor"]['sapVendorId'];
$plant_name   = $data["siteId"];
$plant_id     = $data["siteId"];
$nopol        = $_POST["nopol"];

// ----------------------------------------------------------------------------
// 6. Insert a new inspection record if delivery exists
// ----------------------------------------------------------------------------
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
        tgl_pemeriksaan    = '$date',
        jam_pemeriksaan    = '$jam',
        lokasi_pemeriksaan = '$plant_name',
        muatan             = '$muat',
        no_po         = '{$_POST['no_po']}'
";

mysqli_query($con, $query);
$id_checklist = mysqli_insert_id($con);
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
                <label>No PO</label>
                <input type="text" class="form-control text-uppercase" id="no_po" name="no_po" 
                       value="<?= htmlspecialchars($_POST['no_po']) ?>" readonly>
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
        <input type="hidden" name="no_po" value="<?= htmlspecialchars($no_po) ?>">
        
        <hr>
        
        <!-- Submit button -->
        <div class="row">
            <button type="submit" class="btn btn-success center-block">Go Ceklist</button>
        </div>
        <hr>
        
    </form>
</div>

