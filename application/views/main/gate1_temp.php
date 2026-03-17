<?php
/**
 * Temporary Gate 1 Photo Sequence Update
 * 
 * This script updates the photo sequence and checkpoint status after a photo
 * is uploaded, then redirects back to the Gate 1 inspection page.
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
$seq    = $_POST['seq'] ?? '';
$idref  = $_POST['idref'] ?? '';
$ccp    = $_POST['ccp'] ?? '';
$nopol  = $_POST['nopol'] ?? '';
$petugas = $_POST['petugas'] ?? '';
$lokasi = $_POST['lokasi'] ?? '';

$seq_foto = 0;

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL & UPDATES
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch current photo sequence for this inspection
// ----------------------------------------------------------------------------
if (!empty($idref)) {
    $query_foto = mysqli_query($con, 
        "SELECT seq_foto FROM tb_ceklist WHERE idref = '$idref'"
    );
    
    if ($query_foto && mysqli_num_rows($query_foto) > 0) {
        $row = mysqli_fetch_assoc($query_foto);
        $seq_foto = $row["seq_foto"];
    }
}

// ----------------------------------------------------------------------------
// 2. If photo sequence is 1, reset it to 0 and mark the checkpoint as FAIL
// ----------------------------------------------------------------------------
if ($seq_foto == 1) {
    mysqli_query($con, 
        "UPDATE tb_ceklist 
         SET seq_foto = 0, 
             utama{$ccp} = 0 
         WHERE idref = '$idref'"
    );
}

// ============================================================================
// REDIRECT
// ============================================================================
header("location: N_gate1");
exit();
?>