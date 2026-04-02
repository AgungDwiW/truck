<?php
/**
 * Search Truck by License Plate (API endpoint)
 * 
 * This script provides an API endpoint to search for truck data by license plate.
 * It returns JSON data if found, otherwise an error message.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include_once "application/config/connection73.php";

// Check connection
if ($con73->connect_error) {
    die("Koneksi gagal: " . $con73->connect_error);
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Search truck by license plate.
 * 
 * @param mysqli $con73 Database connection
 * @param string $nopol License plate number
 * @return array|null Truck data as associative array or null if not found.
 */
function cari_nopol($con73, $nopol) {
    $stmt = $con73->prepare("SELECT * FROM tbm_truck WHERE nopol = ?");
    if (!$stmt) {
        die("Query gagal disiapkan: " . $con73->error);
    }
    
    $stmt->bind_param("s", $nopol);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// ============================================================================
// REQUEST HANDLING
// ============================================================================

// Check required parameters
if (isset($_GET['action']) && $_GET['action'] == 'cari_truck' && isset($_GET['nopol'])) {
    $nopol = $_GET['nopol'];
    $data = cari_nopol($con73, $nopol);
    
    if ($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo "Data tidak ditemukan untuk nopol: " . htmlspecialchars($nopol);
    }
} else {
    echo "Parameter tidak lengkap.";
}
?>