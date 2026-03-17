<?php
/**
 * Gate 1 Photo Upload Page
 * 
 * This page allows inspectors to upload photos for specific checkpoints and
 * records findings.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
// Note: $con database connection is assumed to be already available.

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$seq     = $_POST['seq'] ?? '';
$idref   = $_POST['idref'] ?? '';
$ccp     = $_POST['ccp'] ?? '';
$nopol   = $_POST['nopol'] ?? '';
$petugas = $_POST['petugas'] ?? '';
$lokasi  = $_POST['lokasi'] ?? '';

$username = User::$username;
$utama = 0;
$seq_foto = 0;
$item_utama = '';

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL & UPDATES
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch current checkpoint status and photo sequence
// ----------------------------------------------------------------------------
if (!empty($idref) && !empty($ccp)) {
    $data_utama = mysqli_query($con, 
        "SELECT utama{$ccp}, seq_foto 
         FROM tb_ceklist 
         WHERE idref = '$idref'"
    );
    
    if ($data_utama && mysqli_num_rows($data_utama) > 0) {
        $row = mysqli_fetch_assoc($data_utama);
        $utama = $row["utama{$ccp}"];
        $seq_foto = $row["seq_foto"];
    }
    
    // If checkpoint is FAIL and no photo yet, revert to PASS and delete old photos
    if ($utama == 0 && $seq_foto == 0) {
        mysqli_query($con, 
            "UPDATE tb_ceklist SET utama{$ccp} = 1 WHERE idref = '$idref'"
        );
        mysqli_query($con, 
            "DELETE FROM tb_foto 
             WHERE idref = '$idref' 
               AND utama = '$ccp' 
               AND username = '$username'"
        );
        
        header("location: gate1");
        exit;
    }
}

// ----------------------------------------------------------------------------
// 2. Fetch checkpoint description for display
// ----------------------------------------------------------------------------
if (!empty($ccp)) {
    $query_item = mysqli_query($con, 
        "SELECT ceklist_utama 
         FROM tb_ceklist_utama 
         WHERE no = '$ccp'"
    );
    
    if ($query_item && mysqli_num_rows($query_item) > 0) {
        $row_item = mysqli_fetch_assoc($query_item);
        $item_utama = $row_item["ceklist_utama"];
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
    <title>Foto Gate 1</title>
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
</head>
<body>

<div class="container-fluid text-center" style="margin-top: 0px">
    <div style="padding-top: 60px; margin-left: 10px">
        
        <!-- BACK button -->
        <form method="post" action="gate1_temp">
            <input type="hidden" id="temp" name="temp" value="1">
            <input type="hidden" id="seq" name="seq" value="<?= htmlspecialchars($seq) ?>">
            <input type="hidden" id="idref" name="idref" value="<?= htmlspecialchars($idref) ?>">
            <input type="hidden" id="ccp" name="ccp" value="<?= htmlspecialchars($ccp) ?>">
            <input type="hidden" id="nopol" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
            <input type="hidden" id="petugas" name="petugas" value="<?= htmlspecialchars($petugas) ?>">
            <input type="hidden" id="lokasi" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
            
            <button class="btn btn-primary">Back<span class="sr-only">(current)</span></button>
        </form>
        
    </div>
    
    <!-- Header -->
    <div class="row">
        <div class="col">
            <br>
            <h2><label><strong>Control Point Gate 1</strong></label></h2>
            <h3><label style="color: blue"><strong><?= htmlspecialchars($item_utama) ?></strong></label></h3>
        </div>
    </div>
    
    <hr>
    
    <!-- Deskripsi Temuan -->
    <div class="form-group col-md-12 text-center">
        <label><strong>Deskripsi Temuan</strong></label>
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
    
    <!-- Camera input -->
    <div class="row">
        <div class="col">
            <div class="icon_camera">
                <label for="upload-Image">
                    <img src="static/css/img/icon_camera.png" width="70" height="70">
                </label>
                <input type="file" name="file" id="upload-Image" capture="capture" onchange="loadImageFile()"/>
                <div hidden>Original Img - <img id="original-Img"/></div>
                <div hidden>Compress Img - <img id="upload-Preview"/></div>
            </div>
        </div>
    </div>
    
    <br><br>
    
    <!-- Upload form -->
    <div class="row">
        <div class="col">
            <form method="post" accept-charset="utf-8" name="form1">
                <input type="hidden" id="idref" name="idref" value="<?= htmlspecialchars($idref) ?>">
                <input type="hidden" id="ccp" name="ccp" value="<?= htmlspecialchars($ccp) ?>">
                <input type="hidden" id="seq" name="seq" value="<?= htmlspecialchars($seq) ?>">
                <input type="hidden" id="tem" name="tem">
                <input type="hidden" id="nopol" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
                <input type="hidden" id="petugas" name="petugas" value="<?= htmlspecialchars($petugas) ?>">
                <input type="hidden" id="lokasi" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
                <input type="hidden" id="item_utama" name="item_utama" value="<?= htmlspecialchars($item_utama) ?>">
                
                <button class="btn btn-success" name="upload" id="upload" onclick="uploadEx();">Upload<span class="sr-only">(current)</span></button>
                <input name="hidden_data" id='hidden_data' type="hidden"/>
            </form>
        </div>
    </div>
    
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
        canvas.width = image.width / 12;
        canvas.height = image.height / 12;
        context.drawImage(image, 0, 0, image.width, image.height, 0, 0, canvas.width, canvas.height);
        
        document.getElementById("upload-Preview").src = canvas.toDataURL();
        
        var destinationCanvas = document.getElementById("myCanvas");
        destinationCanvas.width = image.width / 12;
        destinationCanvas.height = image.height / 12;
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
</script>

<script>
function uploadEx() {
    var canvas = document.getElementById('myCanvas');
    console.log(canvas);
    var dataURL = canvas.toDataURL("image/png");
    document.getElementById('hidden_data').value = dataURL;
    var fd = new FormData(document.forms["form1"]);
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload_fail', true);
    
    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var percentComplete = (e.loaded / e.total) * 100;
            console.log(percentComplete + '% uploaded');
            alert('Succesfully uploaded');
        }
    };
    
    xhr.onload = function() {
        // Handle response if needed
    };
    
    xhr.send(fd);
};

function ambil() {
    var temuan = document.getElementById("temuan").value;
    document.getElementById("tem").value = temuan;
}
</script>

</body>
</html>