<?php
/**
 * simpan_gate1 - Process Gate 1 inspection form submission
 * 
 * Expected POST fields:
 * - id (checklist ID from hidden input)
 * - idref, nopol, petugas, lokasi, kode_kirim, driver, supplier
 * - hasil (Lanjut Pemeriksaan Gate 2 / Di Tolak di Pos 1)
 * - komentar, tindakan
 * - For each checkpoint (id = param ID):
 *   - param_{id}_value (1 = OK, 0 = Not OK)
 *   - param_{id}_photo (base64 image data)
 *   - param_{id}_temuan (text description)
 */

// Include database connection
include "application/config/connection.php";

// Helper function to save base64 image to file
function saveBase64Image($base64Data, $checklistId, $paramId) {
    if (empty($base64Data)) {
        return null;
    }
    
    // Remove data URL prefix if present
    if (strpos($base64Data, 'base64,') !== false) {
        $base64Data = substr($base64Data, strpos($base64Data, 'base64,') + 7);
    }
    
    $imageData = base64_decode($base64Data);
    if ($imageData === false) {
        return null;
    }
    
    // Create folder: static/file/YYYY-MM-DD/
    $dateFolder = date('Y-m-d');
    $uploadDir = "static/file/{$dateFolder}/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate filename: idChecklist_paramId.png
    $filename = "{$checklistId}_{$paramId}.png";
    $filepath = $uploadDir . $filename;
    
    if (file_put_contents($filepath, $imageData)) {
        return $filepath; // Return relative path to store in DB
    }
    return null;
}

// ----------------------------------------------------------------------------
// 1. Validate request method
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

// ----------------------------------------------------------------------------
// 2. Get and sanitize header fields
// ----------------------------------------------------------------------------
$checklistId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($checklistId <= 0) {
    die('Checklist ID is missing or invalid.');
}

$idref       = mysqli_real_escape_string($con, $_POST['idref'] ?? '');
$nopol       = mysqli_real_escape_string($con, $_POST['nopol'] ?? '');
$petugas     = mysqli_real_escape_string($con, $_POST['petugas'] ?? '');
$lokasi      = mysqli_real_escape_string($con, $_POST['lokasi'] ?? '');
$kode_kirim  = mysqli_real_escape_string($con, $_POST['kode_kirim'] ?? '');
$driver      = mysqli_real_escape_string($con, $_POST['driver'] ?? '');
$supplier    = mysqli_real_escape_string($con, $_POST['supplier'] ?? '');
$hasil       = mysqli_real_escape_string($con, $_POST['hasil'] ?? '');
$komentar    = mysqli_real_escape_string($con, $_POST['komentar'] ?? '');
$tindakan    = mysqli_real_escape_string($con, $_POST['tindakan'] ?? '');

// ----------------------------------------------------------------------------
// 3. Update header in tbl_checklist
// ----------------------------------------------------------------------------
$updateHeader = "
    UPDATE tbl_checklist 
    SET hasil_pemeriksaan = '$hasil',
        komentar_kerusakan = '$komentar',
        tindakan_perbaikan = '$tindakan',
        status_gate1 = 1
";
if (!empty($idref))   $updateHeader .= ", idref = '$idref'";
if (!empty($nopol))   $updateHeader .= ", nopol = '$nopol'";
if (!empty($petugas)) $updateHeader .= ", petugas_pemeriksa = '$petugas'";
if (!empty($lokasi))  $updateHeader .= ", lokasi_pemeriksaan = '$lokasi'";
if (!empty($kode_kirim)) $updateHeader .= ", kode_kirim = '$kode_kirim'";
if (!empty($driver))  $updateHeader .= ", nama_sopir = '$driver'";
if (!empty($supplier))$updateHeader .= ", nama_supplier = '$supplier'";

$updateHeader .= " WHERE no = $checklistId";

if (!mysqli_query($con, $updateHeader)) {
    die("Failed to update header: " . mysqli_error($con));
}

// ----------------------------------------------------------------------------
// 4. Process each checkpoint detail
// ----------------------------------------------------------------------------
// Loop through all POST keys to find param_*_value fields
foreach ($_POST as $key => $value) {
    if (preg_match('/^param_(\d+)_value$/', $key, $matches)) {
        $paramId = (int)$matches[1];
        $paramValue = ($value == '1') ? 'Y' : 'N';  // 1=OK -> Y, 0=Not OK -> N
        
        // Get temuan and photo for this param
        $temuanKey = "param_{$paramId}_temuan";
        $photoKey  = "param_{$paramId}_photo";
        
        $temuan = isset($_POST[$temuanKey]) ? mysqli_real_escape_string($con, $_POST[$temuanKey]) : '';
        $photoBase64 = $_POST[$photoKey] ?? '';
        
        // Save photo file if provided
        $photoPath = null;
        if (!empty($photoBase64)) {
            $photoPath = saveBase64Image($photoBase64, $checklistId, $paramId);
        }
        
        // Insert or update detail record
        $checkDetail = "SELECT id FROM tbl_checklist_detail 
                        WHERE checklist_id = $checklistId AND param_id = $paramId";
        $result = mysqli_query($con, $checkDetail);
        
        if (mysqli_num_rows($result) > 0) {
            // Update existing detail
            $updateDetail = "
                UPDATE tbl_checklist_detail 
                SET `value` = '$paramValue',
                    temuan = '$temuan',
                    photo = " . ($photoPath ? "'$photoPath'" : "photo") . "
                WHERE checklist_id = $checklistId AND param_id = $paramId
            ";
            mysqli_query($con, $updateDetail);
        } else {
            // Insert new detail
            $insertDetail = "
                INSERT INTO tbl_checklist_detail (checklist_id, param_id, `value`, temuan, photo)
                VALUES ($checklistId, $paramId, '$paramValue', '$temuan', " . ($photoPath ? "'$photoPath'" : "NULL") . ")
            ";
            mysqli_query($con, $insertDetail);
        }
    }
}

// ----------------------------------------------------------------------------
// 5. Redirect back or show success message
// ----------------------------------------------------------------------------
// Assuming there is a route named 'gate1_success' or similar
// You can change the redirect as needed
header("Location: " . route('index') . "?msg=success");
exit;