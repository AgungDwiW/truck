<?php
/**
 * Process Truck Data Update
 * 
 * This script receives POST data from the edit form and updates the truck record
 * in the database. It uses prepared statements to prevent SQL injection.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// SESSION & CONFIGURATION
// ============================================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection parameters
$host = "10.203.121.73";
$user = "uapp_productcode";
$pass = "ocr.productcode";
$dbname = "db_product_release";

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
    $_SESSION['pesan'] = "❌ Koneksi database gagal: " . $con->connect_error;
    $_SESSION['pesan_tipe'] = "danger";
    header("Location: start");
    exit();
}

// ============================================================================
// REQUEST VALIDATION
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['pesan'] = "❌ Metode request tidak diizinkan.";
    $_SESSION['pesan_tipe'] = "danger";
    header("Location: start");
    exit();
}

// ============================================================================
// COLLECT FORM DATA
// ============================================================================
$nopol           = $_POST['nopol'] ?? '';
$tipe_truck      = htmlspecialchars($_POST['tipe_truck'] ?? '');
$tahun_pembuatan = htmlspecialchars($_POST['tahun_pembuatan'] ?? '');
$kir_date        = $_POST['kir_date'] ?? '';
$update_by       = htmlspecialchars($_POST['update_by'] ?? '');

// ============================================================================
// DATABASE QUERIES - UPDATE TRUCK RECORD
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Prepare and execute update statement
// ----------------------------------------------------------------------------
$stmt = $con->prepare("
    UPDATE tbm_truck 
    SET tipe_truck = ?, 
        tahun_pembuatan = ?, 
        kir_date = ?, 
        update_by = ?
    WHERE nopol = ?
");

if ($stmt === false) {
    $_SESSION['pesan'] = "❌ Gagal menyiapkan query: " . $con->error;
    $_SESSION['pesan_tipe'] = "danger";
    header("Location: edit_nopol?nopol=" . urlencode($nopol));
    exit();
}

$stmt->bind_param("sssss", $tipe_truck, $tahun_pembuatan, $kir_date, $update_by, $nopol);

if ($stmt->execute()) {
    $_SESSION['pesan'] = "✅ Data truk berhasil diperbarui.";
    $_SESSION['pesan_tipe'] = "success";
} else {
    $_SESSION['pesan'] = "❌ Gagal memperbarui data: " . $stmt->error;
    $_SESSION['pesan_tipe'] = "danger";
}

// ============================================================================
// CLEANUP & REDIRECT
// ============================================================================
$stmt->close();
$con->close();

header("Location: start");
exit();
?>