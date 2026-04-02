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
<script type="text/javascript">
// 1. Populate Modal on Click
function openModal(btn){
    var btn = $(btn);
    
    // Fill hidden inputs
    $('#modal_utama').val(btn.data('utama'));
    $('#modal_idref').val(btn.data('idref'));
    $('#modal_nopol').val(btn.data('nopol'));
    $('#modal_lokasi').val(btn.data('lokasi'));
    $('#modal_ceklist').val(btn.data('ceklist'));
    $('#modal_kode_kirim').val(btn.data('kode_kirim'));
    $('#modal_driver').val(btn.data('driver'));
    $('#modal_supplier').val(btn.data('supplier'));
    
    // Fill UI text
    $('#modalCeklistDisplay').text(btn.data('ceklist'));
    $('#temuan').val('');
    $('#upload-Image').val(''); // Reset file input
    
    // Clear the canvas
    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Show Modal
    $('#uploadModal').modal('show');
}

// 2. Handle Image Preview (Refactored from your original code)
function loadImageFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.getElementById("myCanvas");
                var ctx = canvas.getContext("2d");
                
                // Scale down to 10% just like your original code
                canvas.width = img.width / 10;
                canvas.height = img.height / 10;
                ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, canvas.width, canvas.height);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// 3. Submit Form Data to API
// 3. Submit Form Data to API
$('#apiUploadForm').on('submit', function(e) {
    e.preventDefault();
    
    var submitBtn = $('#btnSubmitApi');
    // Disable the button and insert a Font Awesome spinner with the text
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
    // FormData automatically packages all inputs, including the file payload
    var formData = new FormData(this);
    $.ajax({
        url: '<?= route("N_gate_api") ?>', // Point this to your API endpoint
        type: 'POST',
        data: formData,
        contentType: false, // Required for file uploads via AJAX
        processData: false, // Required for file uploads via AJAX
        success: function(response) {
            // Handle your API success logic here
            alert('Upload Berhasil!');
            $('#uploadModal').modal('hide');
            
            // Optional: Reload the page to reflect changes
            // location.reload();
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan saat upload data.');
            console.error(error);
        },
        complete: function() {
            // Re-enable the button and remove the spinner
            submitBtn.prop('disabled', false).text('Upload via API');
        }
    });
});


</script>
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
<div class="row" style="background-color: #212529; color: white; border-radius: 6px; padding: 10px 0; margin: 0 0 12px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div class="col-xs-10" style="font-size: 16px; font-weight: 500; line-height: 1.8; padding-left: 15px; white-space: normal; word-wrap: break-word;">
        <?= htmlspecialchars($row['ceklist_utama']) ?>
    </div>
    <div class="col-xs-2 text-right " style="padding-right: 10px;">
        <button type="button" class="btn btn-default btn-sm " onclick="openModal(this)"
            style="padding: 3px 8px; background-color: #f8f9fa; border: none; border-radius: 4px;"
            data-utama="<?= $noo ?>"
            data-idref="<?= htmlspecialchars($idref) ?>"
            data-nopol="<?= htmlspecialchars($nopol) ?>"
            data-lokasi="<?= htmlspecialchars($lokasi) ?>"
            data-ceklist="<?= htmlspecialchars($row['ceklist_utama']) ?>"
            data-kode_kirim="<?= htmlspecialchars($kode_kirim) ?>"
            data-driver="<?= htmlspecialchars($driver) ?>"
            data-supplier="<?= htmlspecialchars($supplier) ?>">
            
            <?= static_img('css/img/' . $cek_img, ['width' => '26', 'height' => '26']) ?>
        </button>
    </div>
</div>
<?php
    $no++;
endforeach;
?>

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
                    <textarea class="form-control" style="" id="temuan" name="temuan" rows="3" required placeholder = 'temuan..'></textarea>
                </div>
                
                <hr>
                
                <input type="hidden" name="utama" id="modal_utama">
                <input type="hidden" name="idref" id="modal_idref">
                <input type="hidden" name="nopol" id="modal_nopol">
                <input type="hidden" name="lokasi" id="modal_lokasi">
                <input type="hidden" name="ceklist" id="modal_ceklist">
                <input type="hidden" name="kode_kirim" id="modal_kode_kirim">
                <input type="hidden" name="driver" id="modal_driver">
                <input type="hidden" name="supplier" id="modal_supplier">
                
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
