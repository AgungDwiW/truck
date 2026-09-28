<?php
// Koneksi langsung ke database
$host = "10.203.121.73";
$user = "uapp_productcode";
$pass = "ocr.productcode";
$dbname = "db_product_release";

$con73 = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($con73->connect_error) {
    die("Koneksi gagal: " . $con73->connect_error);
}

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

// Ambil parameter dari URL
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
