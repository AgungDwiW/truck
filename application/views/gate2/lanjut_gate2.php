<?php
/**
 * Gate 2 Form Processor
 * 
 * Handles submission from cek_gate2.php.
 * Updates tbl_checklist and tbl_checklist_detail records.
 */

// ============================================================================
// INCLUDES & INITIALIZATION
// ============================================================================
include "application/config/connection.php";
// include "application/assets/function.php"; // uncomment if needed

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); // or appropriate fallback
    exit;
}
// Debuger::show();
// Debuger::dump($_POST,1);
// ============================================================================
// SANITIZE & RETRIEVE INPUTS
// ============================================================================
$code = isset($_POST['code']) ? mysqli_real_escape_string($con, $_POST['code']) : '';
if (empty($code)) {
    die("Error: Missing inspection record code.");
}

// Basic vehicle/driver info
$nopol              = mysqli_real_escape_string($con, $_POST['nopol'] ?? '');
$tgl_pemeriksaan    = mysqli_real_escape_string($con, $_POST['tgl'] ?? '');
$jam_pemeriksaan    = mysqli_real_escape_string($con, $_POST['jam'] ?? '');
$nama_sopir         = mysqli_real_escape_string($con, $_POST['nama_sopir'] ?? '');
$nama_transporter   = mysqli_real_escape_string($con, $_POST['nama_transporter'] ?? '');
$jenis_kendaraan    = mysqli_real_escape_string($con, $_POST['tipe_truck'] ?? '');
$lokasi_full        = mysqli_real_escape_string($con, $_POST['lokasi'] ?? '');
// Extract location part (before " - ") if present
$lokasi_pemeriksaan = explode(' - ', $lokasi_full)[0] ?? $lokasi_full;
$petugas_gate2      = mysqli_real_escape_string($con, $_POST['petugas'] ?? '');

// FG specific fields (may be empty if muatan != FG)
$usia               = isset($_POST['usia']) ? (int)$_POST['usia'] : null;
$status_usia        = mysqli_real_escape_string($con, $_POST['status_usia'] ?? '');
$tipe_sim           = mysqli_real_escape_string($con, $_POST['tipe_sim'] ?? '');
$status_sim         = mysqli_real_escape_string($con, $_POST['status_sim'] ?? '');
$status_ddt         = mysqli_real_escape_string($con, $_POST['status_ddt'] ?? '');

// Hidden fields from FG section (Aktif/Kadaluarsa) – we ignore for date columns
// $expired_sim_text = $_POST['expired_sim'] ?? '';
// $expired_ddt_text = $_POST['expired_ddt'] ?? '';

// Results & comments
$hasil_pemeriksaan  = mysqli_real_escape_string($con, $_POST['hasil'] ?? '');
$komentar           = mysqli_real_escape_string($con, $_POST['komentar'] ?? '');
$tindakan           = mysqli_real_escape_string($con, $_POST['tindakan'] ?? '');

// ============================================================================
// VALIDATION
// ============================================================================
if (empty($hasil_pemeriksaan) || empty($komentar) || empty($tindakan)) {
    die("Hasil pemeriksaan, komentar, dan tindakan perbaikan wajib diisi.");
}

// Ensure the checklist record exists
$check = mysqli_query($con, "SELECT no, muatan FROM tbl_checklist WHERE no = '$code'");
if (mysqli_num_rows($check) == 0) {
    die("Data checklist dengan no '$code' tidak ditemukan.");
}
$existing = mysqli_fetch_assoc($check);
$muatan = $existing['muatan']; // to know if FG or not

// ============================================================================
// UPDATE MAIN CHECKLIST (tbl_checklist)
// ============================================================================
$updateFields = [
    "nopol = '$nopol'",
    "tgl_pemeriksaan = '$tgl_pemeriksaan'",
    "jam_pemeriksaan = '$jam_pemeriksaan'",
    "nama_sopir = '$nama_sopir'",
    "nama_transporter = '$nama_transporter'",
    "jenis_kendaraan = '$jenis_kendaraan'",
    "lokasi_pemeriksaan = '$lokasi_pemeriksaan'",
    "hasil_pemeriksaan = '$hasil_pemeriksaan'",
    "komentar_kerusakan = '$komentar'",
    "tindakan_perbaikan = '$tindakan'",
    "petugas_gate2 = '$petugas_gate2'"
];

// Add FG fields only if this is an FG shipment
if ($muatan == 'FG') {
    // Status usia, jenis SIM, status SIM & DDT
    $updateFields[] = "usia = " . ($usia !== null ? $usia : "NULL");
    $updateFields[] = "status_usia = '$status_usia'";
    $updateFields[] = "jenis_sim = '$tipe_sim'";
    $updateFields[] = "status_sim = '$status_sim'";
    $updateFields[] = "status_ddt = '$status_ddt'";
    
    // Expiry date columns: set to NULL (we rely on status fields)
    $updateFields[] = "expired_date_sim = NULL";
    $updateFields[] = "expired_date_ddt = NULL";
}

$sqlUpdate = "UPDATE tbl_checklist SET " . implode(", ", $updateFields) . " WHERE no = '$code'";
if (!mysqli_query($con, $sqlUpdate)) {
    die("Gagal memperbarui data utama: " . mysqli_error($con));
}

// ============================================================================
// UPDATE CHECKLIST DETAILS (tbl_checklist_detail)
// ============================================================================
// Get all relevant parameters (utama + tambahan)
$paramQuery = mysqli_query($con, "
    SELECT id FROM tbl_checklist_param 
    WHERE param_type IN ('utama', 'tambahan') OR id > 4
");
if (!$paramQuery) {
    die("Gagal mengambil daftar parameter: " . mysqli_error($con));
}

$allParams = [];
while ($p = mysqli_fetch_assoc($paramQuery)) {
    $allParams[] = $p['id'];
}

// Prepare insert/update statement with ON DUPLICATE KEY UPDATE
$stmt = mysqli_prepare($con, "
    INSERT INTO tbl_checklist_detail (checklist_id, param_id, value)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE value = VALUES(value)
");

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($con));
}

mysqli_begin_transaction($con);
try {
    foreach ($allParams as $paramId) {
        // Check if checkbox was sent (value = 1)
        $checkboxName = "param_" . $paramId;
        $isChecked = isset($_POST[$checkboxName]) && $_POST[$checkboxName] == '1';
        $value = $isChecked ? 'Y' : 'N';
        
        mysqli_stmt_bind_param($stmt, "iis", $code, $paramId, $value);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal menyimpan detail untuk param_id $paramId: " . mysqli_stmt_error($stmt));
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_commit($con);
    
    // Success: redirect back to a summary or success page
    // You can change the redirect target as needed
    redirect_page(route("index"));
    // header("Location: gate2_success.php?code=" . urlencode($code));
    exit;
    
} catch (Exception $e) {
    mysqli_rollback($con);
    die("Terjadi kesalahan saat menyimpan data detail: " . $e->getMessage());
}

?>