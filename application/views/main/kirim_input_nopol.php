<?php
/**
 * Process New Truck Data Insertion
 * 
 * This script receives POST data from the truck registration form, constructs
 * the license plate number, and inserts a new record into the tbm_truck table.
 * It uses prepared statements to prevent SQL injection.
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

$con73 = new mysqli($host, $user, $pass, $dbname);
if ($con73->connect_error) {
    $_SESSION['pesan'] = "❌ Koneksi database gagal: " . $con73->connect_error;
    $_SESSION['pesan_tipe'] = "danger";
    header("Location: input_nopol");
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
$tipe_truck      = htmlspecialchars($_POST['tipe_truck'] ?? '');
$tahun_pembuatan = htmlspecialchars($_POST['tahun_pembuatan'] ?? '');
$kir_date        = $_POST['kir_date'] ?? '';

$nopol_prefix = strtoupper(trim($_POST['nopol_prefix'] ?? ''));
$nopol_number = trim($_POST['nopol_number'] ?? '');
$nopol_suffix = strtoupper(trim($_POST['nopol_suffix'] ?? ''));

$plant_update_id   = htmlspecialchars($_POST['plant_update_id'] ?? '');
$plant_update_desc = htmlspecialchars($_POST['plant_update_desc'] ?? '');
$update_by         = htmlspecialchars($_POST['update_by'] ?? '');

// Combine license plate parts and remove all spaces
$nopol = $nopol_prefix . $nopol_number . $nopol_suffix;
$nopol = str_replace(' ', '', $nopol);

// ============================================================================
// DATABASE QUERIES - INSERT TRUCK RECORD
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Prepare and execute insert statement
// ----------------------------------------------------------------------------
$stmt = $con73->prepare("
    INSERT INTO tbm_truck 
        (nopol, tipe_truck, tahun_pembuatan, kir_date, 
         plant_update_id, plant_update_desc, update_by) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if ($stmt === false) {
    $_SESSION['pesan'] = "❌ Gagal menyiapkan query: " . $con73->error;
    $_SESSION['pesan_tipe'] = "danger";
    header("Location: input_nopol");
    exit();
}

$stmt->bind_param(
    "sssssss", 
    $nopol, $tipe_truck, $tahun_pembuatan, $kir_date,
    $plant_update_id, $plant_update_desc, $update_by
);

if ($stmt->execute()) {
    $_SESSION['pesan'] = "✅ Data truk berhasil disimpan.";
    $_SESSION['pesan_tipe'] = "success";
} else {
    $_SESSION['pesan'] = "❌ Gagal menyimpan data: " . $stmt->error;
    $_SESSION['pesan_tipe'] = "danger";
}

// ============================================================================
// CLEANUP & REDIRECT
// ============================================================================
$stmt->close();
$con73->close();

header("Location: start");
exit();
?>