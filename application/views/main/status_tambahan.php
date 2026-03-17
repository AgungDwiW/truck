<?php
/**
 * Additional Checklist Status API Endpoint
 * 
 * This script calculates the overall inspection status based on the submitted
 * "utama" and "tambahan" checklist values (received via POST).
 * The result is returned as a JSON response.
 * 
 * Note: No database queries are performed; all logic is based on the submitted
 * checkbox values.
 */
header("Content-Type: application/json");

// ============================================================================
// COLLECT POST DATA
// ============================================================================
$data_utama = array();
$dt_tamb    = array();

for ($i = 1; $i <= 20; $i++) {
    $data_utama[$i] = $_POST['utama' . $i] ?? 0;
    $dt_tamb[$i]    = $_POST['tamb' . $i] ?? 0;
    
    // Treat "undefined" strings as 0
    if ($data_utama[$i] == "undefined") $data_utama[$i] = 0;
    if ($dt_tamb[$i] == "undefined")     $dt_tamb[$i] = 0;
}

// ============================================================================
// CALCULATE RESULTS
// ============================================================================
$data_hasil_hitam = 1; // placeholder, not used in current logic
$data_hasil_utama = (
    $data_utama[5] * $data_utama[6] * $data_utama[7] * $data_utama[8] *
    $data_utama[9] * $data_utama[10] * $data_utama[11] * $data_utama[12] *
    $data_utama[13] * $data_utama[14]
);

$data_hasil_tamb = (
    $dt_tamb[1] * $dt_tamb[2] * $dt_tamb[3] * $dt_tamb[4] *
    $dt_tamb[5] * $dt_tamb[6] * $dt_tamb[7] * $dt_tamb[8] *
    $dt_tamb[9] * $dt_tamb[10]
);

// ============================================================================
// DETERMINE FINAL STATUS
// ============================================================================
if ($data_hasil_hitam == '0') {
    // This branch likely never executes because $data_hasil_hitam is always 1
    echo json_encode(array(
        'status'       => "Not ok",
        'status_utama' => "Di Tolak di Pos 1"
    ));
} else {
    if ($data_hasil_utama == '1') {
        if ($data_hasil_tamb == '1') {
            echo json_encode(array(
                'status'       => "ok",
                'status_utama' => "Layak di Operasikan (Stiker Hijau)"
            ));
        }
        if ($data_hasil_tamb == '0') {
            echo json_encode(array(
                'status'       => "ok",
                'status_utama' => "Perlu Perbaikan (Stiker Kuning)"
            ));
        }
    }
    if ($data_utama[5] == '0') {
        echo json_encode(array(
            'status'       => "Not ok",
            'status_utama' => "Tidak Layak di Operasikan (Stiker Merah)"
        ));
    }
}
?>