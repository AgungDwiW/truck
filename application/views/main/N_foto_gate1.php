<?php
/**
 * Gate 1 Photo Upload & Checkpoint Reversion Page
 * 
 * This page handles photo uploads for inspection failures and allows inspectors
 * to revert a checkpoint status back to PASS.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include  "application/config/connection.php";
include  "application/config/connection.php";
// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$utama       = $_POST['utama'] ?? '';
$idref       = $_POST['idref'] ?? '';
$ceklist     = $_POST['ceklist'] ?? '';
$ccp         = $_POST['ceklist'] ?? ''; // same as $ceklist
$nopol       = $_POST['nopol'] ?? '';
$lokasi      = $_POST['lokasi'] ?? '';
$tambah_foto = $_POST['tambah_foto'] ?? '';
$kode_kirim  = $_POST['kode_kirim'] ?? '';
$driver      = $_POST['driver'] ?? '';
$supplier    = $_POST['supplier'] ?? '';
$temuan      = '';

$utama_values = array(1 => 1, 2 => 1, 3 => 1, 4 => 1); // default to 1 (PASS)

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Retrieve current utama1..utama4 values for this inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $cek_utama = mysqli_query($con, 
        "SELECT utama1, utama2, utama3, utama4 
         FROM tb_ceklist 
         WHERE idref = '$idref'"
    );
    
    if ($cek_utama && mysqli_num_rows($cek_utama) > 0) {
        $row1 = mysqli_fetch_assoc($cek_utama);
        $utama_values[1] = $row1['utama1'];
        $utama_values[2] = $row1['utama2'];
        $utama_values[3] = $row1['utama3'];
        $utama_values[4] = $row1['utama4'];
    }
}

// ----------------------------------------------------------------------------
// 2. If not adding a new photo, check if we need to revert the checkpoint to PASS
// ----------------------------------------------------------------------------
if ($tambah_foto != 1) {
    $updated = false;
    
    for ($i = 1; $i <= 4; $i++) {
        if ($utama == $i && $utama_values[$i] == 0) {
            // Revert this checkpoint to PASS
            mysqli_query($con, 
                "UPDATE tb_ceklist SET utama{$i} = 1 WHERE idref = '$idref'"
            );
            $updated = true;
            break;
        }
    }
    
    // If we reverted any checkpoint, show the "Ceklist akan dikembalikan ke PASS" message
    if ($updated) {
        // The HTML for this case is identical for all $utama values, so we can output it once.
        // We'll break out of PHP and output the HTML, then exit.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Checkpoint Reverted</title>
            <style type="text/css">
                .kotak_sq {
                    width: 250px;
                    background: blue;
                    margin: 50px auto;
                    padding: 50px 20px;
                    box-shadow: 0px 0px 100px 4px #d6d6d6;
                }
                
                .tombol_ic {
                    color: white;
                    font-size: 20pt;
                    width: 200px;
                    height: 80px;
                    border: none;
                    border-radius: 3px;
                    padding: 20px 20px;
                    margin-top: 100px;
                }
                
                .label {
                    color: red;
                    font-size: 24px;
                    text-align: center;
                    display: block;
                }
            </style>
        </head>
        <body>
            <div class="kotak_sq">
                <form method="post" action="<?=route("N_gate1")?>">
                    <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
                    <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
                    <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
                    <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
                    <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
                    <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
                    
                    <h2><strong><label class="label center-block">Ceklist akan dikembalikan ke PASS...!!!</label></strong></h2>
                    
                    <button type="submit" class="btn btn-success tombol_ic center-block">OK</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit; // Stop further execution
    }
}

// If we reach here, either $tambah_foto == 1 or the checkpoint was already PASS.
// We'll display the photo upload form for the specific $utama (1‑4).
// Ensure $utama is between 1 and 4 and the corresponding value is not 0 (i.e., already PASS).
if ($utama < 1 || $utama > 4 || $utama_values[$utama] == 0) {
    // Should not happen; maybe redirect back.
    echo "<script>window.history.back();</script>";
    exit;
}

// ============================================================================
// HTML OUTPUT STARTS HERE (Photo Upload Form)
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto Temuan</title>
    <style type="text/css">
        body {
            background-color: yellow;
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

<script type="text/javascript">window.refresh();</script>

<div class="container-fluid text-center">
    
    <!-- BACK button -->
    <form method="post" action="<?=route("N_gate1")?>">
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        
        <button type="submit" class="btn btn-danger">BACK</button>
    </form>
    
    <!-- Header -->
    <div class="row">
        <div class="col">
            <br>
            <h2><label style="color: blue"><strong>Control Check Point</strong></label></h2>
        </div>
    </div>
    
    <hr>
    
    <!-- Temuan description -->
    <div class="form-group col-md-12 text-center">
        <h2><label style="color: green"><strong>Temuan</strong></label></h2>
        <h2><label><strong><?= htmlspecialchars($ceklist) ?></strong></label></h2>
        <textarea class="form-control input-sm" style="background-color: red; color: white" 
                  type="text" id="temuan" name="temuan" onchange="ambil()"></textarea>
    </div>
    
    <hr>
    
    <!-- Canvas for image preview -->
    <div>
        <div class="col">
            <canvas id="myCanvas" name="myCanvas" width="100" height="100">xx</canvas>
        </div>
    </div>
    
    <hr>
    
    <!-- Photo upload form -->
    <form action="N_upload_fail" method="post" enctype="multipart/form-data">
        
        <!-- Camera icon -->
        <div class="row">
            <div class="col">
                <div class="icon_camera">
                    <label for="upload-Image">
                        <?= static_img('css/img/icon_camera.png', ['width' => '70', 'height' => '70']) ?>
                    </label>
                    <input type="file" name="file" id="upload-Image" capture="capture" onchange="loadImageFile()"/>
                    <div hidden>Original Img - <img id="original-Img"/></div>
                    <div hidden>Compress Img - <img id="upload-Preview"/></div>
                </div>
            </div>
        </div>
        
        <!-- Hidden fields -->
        <input type="hidden" id="tem" name="tem" value="<?= htmlspecialchars($temuan) ?>">
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="ccp" value="<?= htmlspecialchars($ccp) ?>">
        <input type="hidden" name="utama" value="<?= htmlspecialchars($utama) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        
        <!-- Upload button -->
        <div class="row">
            <div class="col">
                <div class="fileUpload btn btn-success">
                    <span>Upload</span>
                    <input class="upload" type="submit" name="upload" id="upload">
                    <input name="hidden_data" id='hidden_data' type="hidden"/>
                </div>
            </div>
        </div>
        
    </form>
    
</div>

<!-- JavaScript for image preview and upload -->
<script type="text/javascript">
var fileReader = new FileReader();
var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

fileReader.onload = function (event) {
    var image = new Image();
    
    image.onload = function() {
        document.getElementById("original-Img").src = image.src;
        var canvas = document.createElement("canvas");
        var context = canvas.getContext("2d");
        canvas.width = image.width / 10;
        canvas.height = image.height / 10;
        context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
        
        document.getElementById("upload-Preview").src = canvas.toDataURL();
        
        var destinationCanvas = document.getElementById("myCanvas");
        destinationCanvas.width = image.width / 10;
        destinationCanvas.height = image.height / 10;
        var destCtx = destinationCanvas.getContext('2d');
        destCtx.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
    };
    
    image.src = event.target.result;
};

var loadImageFile = function () {
    var uploadImage = document.getElementById("upload-Image");
    
    if (uploadImage.files.length === 0) { 
        return; 
    }
    
    var uploadFile = uploadImage.files[0];
    if (!filterType.test(uploadFile.type)) {
        alert("Please select a valid image."); 
        return;
    }
    
    fileReader.readAsDataURL(uploadFile);
};

function ambil() {
    var temuan = document.getElementById("temuan").value;
    document.getElementById("tem").value = temuan;
}

function klik() {
    var xxx = document.getElementById("upload");
    xxx.disabled = true;
}

function klikk() {
    var xxx = document.getElementById("back");
    xxx.disabled = true;
}
</script>

