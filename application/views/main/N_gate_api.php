<?php
/**
 * N_gate_api.php
 * * Asynchronous API handler for checkpoint failures and photo uploads.
 */

header('Content-Type: application/json');
include "application/config/connection.php";

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred.'
];

try {
    // 1. Capture POST Data
    $idref      = $_POST['idref'] ?? '';
    $utama      = $_POST['utama'] ?? ''; // Index 1, 2, 3, or 4
    $temuan     = $_POST['temuan'] ?? '';
    $nopol      = $_POST['nopol'] ?? '';
    $kode_kirim = $_POST['kode_kirim'] ?? '';

    // Validation
    if (empty($idref) || empty($utama)) {
        throw new Exception("Missing required parameters (idref or utama).");
    }

    // 2. Handle File Upload
    $uploadPath = "uploads/gate_photos/"; // Ensure this directory exists and is writable
    $fileName   = "";

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $originalName = $_FILES['file']['name'];
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Generate a unique filename: idref_utama_timestamp.ext
        $fileName = $idref . "_utama" . $utama . "_" . time() . "." . $fileExtension;
        $destPath = $uploadPath . $fileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            throw new Exception("Failed to move uploaded file to destination.");
        }
    } else {
        throw new Exception("No valid image file uploaded.");
    }

    // 3. Database Update
    // Update the specific 'utamaX' column to 0 (Fail) and save the photo/note
    // Note: Adjust column names (fotoX, temuanX) based on your actual tb_ceklist schema
    $columnToUpdate = "utama" . mysqli_real_escape_string($con, $utama);
    $photoColumn    = "foto" . mysqli_real_escape_string($con, $utama);
    $noteColumn     = "note" . mysqli_real_escape_string($con, $utama);
    
    $cleanTemuan   = mysqli_real_escape_string($con, $temuan);
    $cleanIdref    = mysqli_real_escape_string($con, $idref);

    $sql = "UPDATE tb_ceklist SET 
            $columnToUpdate = 0, 
            $photoColumn = '$fileName', 
            $noteColumn = '$cleanTemuan' 
            WHERE idref = '$cleanIdref'";

    if (mysqli_query($con, $sql)) {
        $response['status'] = 'success';
        $response['message'] = 'Data and photo uploaded successfully.';
        $response['file'] = $fileName;
    } else {
        throw new Exception("Database update failed: " . mysqli_error($con));
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;