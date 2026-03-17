<?php
/**
 * Upload Failure Photo Processing Script
 * 
 * This script receives a base64‑encoded image from the client, saves it to disk,
 * and records the failure photo metadata in the database.
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
$username = User::$username;

$idref      = $_POST['idref'] ?? '';
$ccp        = $_POST['ccp'] ?? '';
$seq        = $_POST['seq'] ?? '';
$temuan     = $_POST['tem'] ?? '';
$nopol      = $_POST['nopol'] ?? '';
$petugas    = $_POST['petugas'] ?? '';
$lokasi     = $_POST['lokasi'] ?? '';
$item_utama = $_POST['item_utama'] ?? '';

$nama = ''; // will hold the generated filename

// ============================================================================
// FILE UPLOAD PROCESSING
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Decode base64 image and save to disk
// ----------------------------------------------------------------------------
$upload_dir = "application/views/capture/";
$img = $_POST['hidden_data'] ?? '';

if (!empty($img)) {
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    
    $nama = date('ymdhis') . ".png";
    $file = $upload_dir . $nama;
    
    $success = file_put_contents($file, $data);
    // Note: $success could be checked, but original script does not validate.
}

// ============================================================================
// DATABASE QUERIES - DATA STORAGE
// ============================================================================

// ----------------------------------------------------------------------------
// 2. Update photo sequence flag in the inspection record
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    mysqli_query($con, 
        "UPDATE tb_ceklist SET seq_foto = 1 WHERE idref = '$idref'"
    );
}

// ----------------------------------------------------------------------------
// 3. Insert failure photo metadata
// ----------------------------------------------------------------------------
if (!empty($idref) && !empty($nama)) {
    mysqli_query($con, 
        "INSERT INTO tb_foto 
         SET idref       = '$idref',
             username    = '$username',
             utama       = '$ccp',
             foto_name   = '$nama',
             description = '$temuan',
             nopol       = '$nopol',
             petugas     = '$petugas',
             lokasi      = '$lokasi',
             item_utama  = '$item_utama'"
    );
}

// ============================================================================
// NOTE: This script does not output any HTML; it is called via AJAX.
// ============================================================================