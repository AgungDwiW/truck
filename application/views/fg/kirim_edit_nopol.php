<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database (application/config/db_product_release.php)
require APP_DIR . 'config/db_product_release.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nopol           = $_POST['nopol'];
    $tipe_truck      = htmlspecialchars($_POST['tipe_truck']);
    $tahun_pembuatan = htmlspecialchars($_POST['tahun_pembuatan']);
    $kir_date        = $_POST['kir_date'];
    $update_by       = htmlspecialchars($_POST['update_by']);

    // Update tanpa kolom plant_update_id & plant_update_desc
    $stmt = $con73->prepare("
        UPDATE tbm_truck 
        SET tipe_truck = ?, tahun_pembuatan = ?, kir_date = ?, update_by = ?
        WHERE nopol = ?
    ");

    if ($stmt === false) {
        $_SESSION['pesan'] = "❌ Gagal menyiapkan query: " . $con73->error;
        $_SESSION['pesan_tipe'] = "danger";
        header("Location: fg?action=edit_nopol&nopol=" . urlencode($nopol));
        exit();
    }

    // Bind parameter: 4 string + 1 string (total 5)
    $stmt->bind_param("sssss", $tipe_truck, $tahun_pembuatan, $kir_date, $update_by, $nopol);

    if ($stmt->execute()) {
        $_SESSION['pesan'] = "✅ Data truk berhasil diperbarui.";
        $_SESSION['pesan_tipe'] = "success";
    } else {
        $_SESSION['pesan'] = "❌ Gagal memperbarui data: " . $stmt->error;
        $_SESSION['pesan_tipe'] = "danger";
    }

    $stmt->close();
    $con73->close();

    header("Location: common?action=start");
    exit();
}
?>
