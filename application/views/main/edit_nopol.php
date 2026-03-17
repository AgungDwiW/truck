<?php
/**
 * Edit Truck Data Page
 * 
 * This page allows authorized users to update truck information (type, year, KIR expiry).
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
    die("Koneksi gagal: " . $con->connect_error);
}

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$username = User::$username ?? '';
$plant_id = '';
$plant_name = '';
$nopol = $_GET['nopol'] ?? '';
$data = array();

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch user's plant information
// ----------------------------------------------------------------------------
if (!empty($username)) {
    $sql_username = mysqli_query($con, 
        "SELECT * FROM tbm_user WHERE nama = '$username'"
    );
    
    if ($sql_username && mysqli_num_rows($sql_username) > 0) {
        $rowuser = mysqli_fetch_assoc($sql_username);
        $plant_name = $rowuser["plant_name"];
        $plant_id   = $rowuser["plant_id"];
    }
}

// ----------------------------------------------------------------------------
// 2. Fetch truck data based on license plate (nopol)
// ----------------------------------------------------------------------------
if (!empty($nopol)) {
    $stmt = $con->prepare("SELECT * FROM tbm_truck WHERE nopol = ?");
    $stmt->bind_param("s", $nopol);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Truck</title>
    <link rel="stylesheet" href="plugins/bootstrap-3.4.1-dist/css/bootstrap.min.css">
    <style>
        .form-container {
            margin: 50px auto;
            max-width: 700px;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        
        .nopol-group input {
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="index">
                <img src="plugins/icon.png" alt="Logo" style="height: 24px; display: inline-block; margin-top: -4px;">
                Home
            </a>
        </div>
        <ul class="nav navbar-nav">
            <!-- Additional nav items can be added here -->
        </ul>
    </div>
</nav>
<br>

<div class="container form-container">
    <h3 class="text-center">Update Data KIR Kendaraan</h3>
    
    <?php
    // Display session messages
    if (isset($_SESSION['pesan'])) {
        echo '<div class="alert alert-' . $_SESSION['pesan_tipe'] . '" role="alert">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo $_SESSION['pesan'];
        echo '</div>';
        unset($_SESSION['pesan']);
        unset($_SESSION['pesan_tipe']);
    }
    ?>
    
    <form action="kirim_edit_nopol" method="POST">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($data['nopol'] ?? '') ?>">
        <input type="hidden" name="update_by" value="<?= htmlspecialchars($username) ?>">
        
        <!-- No Polisi (disabled) -->
        <div class="form-group">
            <label>Nomor Polisi</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nopol'] ?? '') ?>" disabled>
        </div>
        
        <!-- Tipe Truck dropdown -->
        <div class="form-group">
            <label for="tipe_truck">Tipe Truck</label>
            <select class="form-control" name="tipe_truck" required>
                <option value="">-- Pilih Tipe Truck --</option>
                <?php
                $tipeList = ["Pick Up", "CDE", "CDD", "Tronton", "Wingbox"];
                foreach ($tipeList as $tipe) {
                    $selected = (($data['tipe_truck'] ?? '') == $tipe) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($tipe) . "' $selected>" . htmlspecialchars($tipe) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- Tahun Pembuatan dropdown -->
        <div class="form-group">
            <label for="tahun_pembuatan">Tahun Pembuatan</label>
            <select class="form-control" name="tahun_pembuatan" required>
                <option value="">-- Pilih Tahun --</option>
                <?php
                $tahun_sekarang = date('Y');
                for ($i = $tahun_sekarang; $i >= 2000; $i--) {
                    $selected = (($data['tahun_pembuatan'] ?? '') == $i) ? 'selected' : '';
                    echo "<option value='" . $i . "' $selected>" . $i . "</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- Tanggal Expired KIR -->
        <div class="form-group">
            <label for="kir_date">Tanggal Expired KIR</label>
            <input type="date" class="form-control" name="kir_date" 
                   value="<?= htmlspecialchars($data['kir_date'] ?? '') ?>" required>
        </div>
        
        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
    </form>
</div>

<script src="plugins/js/jquery-3.6.0.min.js"></script>
<script src="plugins/bootstrap-3.4.1-dist/js/bootstrap.min.js"></script>
</body>
</html>