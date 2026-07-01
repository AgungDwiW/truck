<?php
/**
 * Gate 1 Inspection Page (New Version)
 * This page displays the main checkpoints for Gate 1 inspection and allows
 * inspectors to record findings and upload photos.
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include "application/config/connection.php";
include "application/config/connectionSL.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref       = $_POST['idref'] ?? '';
$nopol       = $_POST['nopol'] ?? '';
$lokasi      = User::$plant_name;
$no_po       = $_POST['no_po'] ?? '';
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

$transporter_all    = explode(" - ",$transporter);
$transporter        = $transporter_all[0];
unset($transporter_all[0]);
$transporter_name   = implode(" - ", $transporter_all);

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
// Debuger::dump($_POST,1);
$supplier_all       = explode(" - ",$supplier);
$supplier           = $supplier_all[0];
unset($supplier_all[0]);
$supplier_name      = implode(" - ", $supplier_all);



$query_insert = "
    REPLACE INTO dbtruck.tbl_checklist 
    SET seq                = '$seq',
        idref              = '$idref',
        petugas_pemeriksa  = '$username',
        nopol              = '$nopol',
        nama_supplier      = '$supplier_name',
        kode_supplier      = '$supplier',
        nama_transporter   = '$transporter_name',
        kode_transporter   = '$transporter',
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

// Debuger::dump($query_insert,1); 
mysqli_query($con, $query_insert);
$id_checklist = mysqli_insert_id($con);


// ----------------------------------------------------------------------------
// 3.5. UPDATE OTM
// ----------------------------------------------------------------------------
    if ($_POST['muat'] == 'FG'){
        $otm = new Table("tbl_picking_shipment_otm_upload", "smartlogistic", $conSL);
        $otm->update(["service_provider_id" => $transporter, "transporter_name" => $transporter_name, 'truck_id' => $nopol, 'driver_name' => $driver])
            ->where("shipment_id = 'S{$_POST['id_barang']}'")->execute();
    }
// ----------------------------------------------------------------------------
// 4. Fetch all checkpoints (utama) for display
// ----------------------------------------------------------------------------
$result = mysqli_query($con, 
    "SELECT *, 
            CONCAT(name, id) AS namee,
            CONCAT(param_name, id, id) AS idgreen,
            CONCAT(param_name, id, id, id) AS idred
     FROM tbl_checklist_param
     where param_type = 'utama' 
     and id BETWEEN 0 AND 4"
);

$checkpoints = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $checkpoints[] = $row;
    }
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>
<script type="text/javascript">

// Track the current parameter being modified in the modal
var currentParamId = null;

// Handle Green Button click - immediate change (OK without modal)
function setParamValueGreen(id, btn) {
    // Set value to 1
    $('#param_' + id + '_value').val('1');
    
    // Clear previously stored photo and temuan data since it's now OK
    $('#param_' + id + '_photo').val('');
    $('#param_' + id + '_temuan').val('');
    
    // Update button borders (white indicates selection clearly on colored bg)
    var container = $(btn).closest('.button-container');
    container.find('.btn-check-green').css('border', '2px solid #ffffff');
    container.find('.btn-check-red').css('border', '2px solid transparent');
    
    // Change the row background color to Green
    $('#row_' + id).css('background-color', '#28a745');
    // Remove validation highlight if present
    $('#row_' + id).css('box-shadow', '0 2px 4px rgba(0,0,0,0.1)');
    
    checkOverallStatus();
}

// Handle Red Button click - opens modal but does NOT change row state yet
function openModal(btn, idparam) {
    var btnObj = $(btn);
    currentParamId = idparam;
    
    // Fill UI text in modal with checkpoint name
    $('#modalCeklistDisplay').text(btnObj.data('ceklist'));
    
    // Load previously stored temuan if exists (from any previous save)
    $('#temuan').val($('#param_' + idparam + '_temuan').val());
    
    // Reset file input and canvas
    $('#upload-Image').val(''); 
    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Attempt to load existing canvas data if user already saved an image for this row
    var existingPhoto = $('#param_' + idparam + '_photo').val();
    if(existingPhoto) {
        var img = new Image();
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
        }
        img.src = existingPhoto;
    }
    
    // Show Modal (no changes to row background or values yet)
    $('#uploadModal').modal('show');
}

// Real-time Overall Status checker
function checkOverallStatus() {
    var allOk = true;
    // Note: This only checks items that have been evaluated. 
    // The form submission handles the strict checking for missed items.
    $('input[id$="_value"]').each(function() {
        if($(this).val() == '0') {
            allOk = false;
        }
    });
    
    if(allOk) {
        $('#hasil').val('Lanjut Pemeriksaan Gate 2').removeClass('bg-danger').addClass('bg-success');
    } else {
        $('#hasil').val('Di Tolak di Pos 1').removeClass('bg-success').addClass('bg-danger');
    }
}

// Handle Image Preview
function loadImageFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.getElementById("myCanvas");
                var ctx = canvas.getContext("2d");
                
                // Scale down to 10% 
                canvas.width = img.width / 10;
                canvas.height = img.height / 10;
                ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    // Intercept Modal Form Submission
    $('#apiUploadForm').on('submit', function(e) {
        e.preventDefault(); 
        
        if (!currentParamId) return;

        var submitBtn = $('#btnSubmitApi');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        // 1. Get Temuan value
        var temuanValue = $('#temuan').val();
        
        // 2. Get Canvas Image as Base64 String
        var canvas = document.getElementById("myCanvas");
        var photoBase64 = canvas.toDataURL("image/png");

        // 3. Inject them into the hidden inputs of the specific row
        $('#param_' + currentParamId + '_temuan').val(temuanValue);
        $('#param_' + currentParamId + '_photo').val(photoBase64);
        $('#param_' + currentParamId + '_value').val('0'); // Set to 0 (REJECTED)
        
        // 4. Update the row UI: background color and button borders
        var container = $('#row_' + currentParamId).find('.button-container');
        container.find('.btn-check-green').css('border', '2px solid transparent');
        container.find('.btn-check-red').css('border', '2px solid #ffffff');
        $('#row_' + currentParamId).css('background-color', '#dc3545'); // Red background
        
        // Remove validation highlight if it was there
        $('#row_' + currentParamId).css('box-shadow', '0 2px 4px rgba(0,0,0,0.1)');
        
        // 5. Update the final inspection status
        checkOverallStatus();

        // 6. Clean up and close modal
        setTimeout(function() {
            submitBtn.prop('disabled', false).text('Simpan');
            $('#uploadModal').modal('hide');
        }, 300);
    });

    // Intercept Main Form Submission to validate all checkpoints
    $('#mainInspectionForm').on('submit', function(e) {
        var allChecked = true;
        var missingCount = 0;

        // Loop through all hidden value inputs
        $('input[id$="_value"]').each(function() {
            if ($(this).val() === "") {
                allChecked = false;
                missingCount++;
                // Visually highlight the row that was missed with a yellow outline/glow
                $(this).closest('.row').css('box-shadow', '0 0 10px 3px #ffc107'); 
            } else {
                // Remove highlight if it has a value
                $(this).closest('.row').css('box-shadow', '0 2px 4px rgba(0,0,0,0.1)');
            }
        });

        if (!allChecked) {
            e.preventDefault(); // Stop the form from submitting
            alert("Gagal Menyimpan! Terdapat " + missingCount + " kelengkapan yang belum diperiksa. Silakan pilih OK (Hijau) atau Reject (Merah) pada bagian yang disorot.");
            
            // Scroll to the first unchecked item
            $('html, body').animate({
                scrollTop: $('input[id$="_value"][value=""]').first().closest('.row').offset().top - 100
            }, 500);
        }
    });
});
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gate 1 Inspection</title>
<style type="text/css">
    body { background-color: transparent; }
    .header-box { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }
    .input-box { border-top-left-radius: 0; border-top-right-radius: 0; }
</style>

<div class="container-fluid mt-3 mb-5" style="max-width: 800px;">
    
    <div class="row text-center bg-danger text-white py-2 mb-3 rounded shadow-sm">
        <div class="col">
            <h3 class="m-0 fw-bold" style="font-size: 26px;">Kelengkapan Utama</h3>
        </div>
    </div>
    
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content text-center">
            <form id="apiUploadForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 style="color: green;"><strong>Temuan</strong></h4>
                </div>
                
                <div class="modal-body">
                    <h4 id="modalCeklistDisplay"><strong></strong></h4>
                    
                    <div class="form-group">
                        <textarea class="form-control" id="temuan" name="temuan" rows="3" required placeholder='temuan..'></textarea>
                    </div>
                    
                    <hr>
                    
                    <div class="icon_camera text-center">
                        <label for="upload-Image" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 10px;">
                            <div style="margin-bottom: 8px;">
                                <?= static_img('css/img/icon_camera.png', ['width' => '40', 'height' => '40']) ?>
                            </div>
                            <span style="font-size: 16px; font-weight: bold; color: #333;">Capture Image</span>
                        </label>
                        <input type="file" name="file" id="upload-Image" accept="image/*" capture="capture" style="display:none;" onchange="loadImageFile(this)">
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <canvas id="myCanvas" width="100" height="100" style="border:1px solid #ccc; background:#fff; display:block; margin:0 auto;"></canvas>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitApi">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <hr class="my-4">
    
    <form id="mainInspectionForm" method="post" action="<?=route('simpan_gate1')?>">
        <?php
        $no = 1;
        $hasil = 'Lanjut Pemeriksaan Gate 2';
        $hasil_color = 'bg-success';
        // Note: Make sure $border_green and $border_red are defined in your environment if they aren't here
        $border_green = '1px solid #ccc'; 
        $border_red = '1px solid #ccc';

        foreach ($checkpoints as $row):
            $noo = $no - 1;
            $row_bg  = "#bcbcbc"
        ?>
        <div id="row_<?=$row['id']?>" class="row" style="background-color: <?= $row_bg ?>; color: white; border-radius: 6px; padding: 10px 0; margin: 0 0 12px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: background-color 0.3s ease; transition: box-shadow 0.3s ease;">
            <div class="col-xs-8" style="font-size: 16px; font-weight: 500; line-height: 1.8; padding-left: 15px; white-space: normal; word-wrap: break-word;">
                <?= htmlspecialchars($row['param_name']) ?>
            </div>
            
            <div class="col-xs-4 text-right button-container" style="padding-right: 10px;">
                
                <button type="button" class="btn btn-default btn-sm btn-check-green" 
                        onclick="setParamValueGreen('<?=$row['id']?>', this)"
                        style="padding: 3px 8px; background-color: #f8f9fa; border: <?= $border_green ?>; border-radius: 4px; margin-right: 5px;">
                    <?= static_img('css/img/cekgreen.png', ['width' => '26', 'height' => '26']) ?>
                </button>
                
                <button type="button" class="btn btn-default btn-sm btn-check-red" 
                        onclick="openModal(this, '<?=$row['id']?>')"
                        data-ceklist="<?= htmlspecialchars($row['param_name']) ?>"
                        style="padding: 3px 8px; background-color: #f8f9fa; border: <?= $border_red ?>; border-radius: 4px;">
                    <?= static_img('css/img/red.png', ['width' => '26', 'height' => '26']) ?>
                </button>

                <!-- Value changed to empty so the JS validator knows it hasn't been checked -->
                <input type="hidden" name="param_<?=$row['id']?>_value"  id="param_<?=$row['id']?>_value"  value="">
                <input type="hidden" name="param_<?=$row['id']?>_photo"  id="param_<?=$row['id']?>_photo"  value="">
                <input type="hidden" name="param_<?=$row['id']?>_temuan" id="param_<?=$row['id']?>_temuan" value="">
                
            </div>
        </div>
        <?php
            $no++;
        endforeach;
        ?>
        
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Hasil Pemeriksaan :</label>
            </div>
            <input type="text" class="form-control input-box text-white text-center fw-bold fs-5 py-2 <?= $hasil_color ?> border-info" 
                   id="hasil" name="hasil" value="<?= $hasil ?>" readonly>
        </div>
        
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Komentar Kerusakan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" 
                   id="komentar" name="komentar" required placeholder="Tulis komentar disini...">
        </div>
        
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Tindakan Perbaikan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" 
                   id="tindakan" name="tindakan" required placeholder="Tulis tindakan perbaikan...">
        </div>
        
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="petugas" value="<?= htmlspecialchars($username) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="no_po" value="<?= htmlspecialchars($no_po) ?>">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        <input type='hidden' name='id' value='<?=$id_checklist?>'>
        
        <div class="d-grid gap-2 mt-4" style='width:100%'>
            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style='width:100%'>
                Simpan Data
            </button>
        </div>
    </form>
</div>