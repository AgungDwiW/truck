<?php
/**
 * Vaccination Photo Upload Page
 * 
 * This page handles photo uploads for driver/helper vaccination records and
 * stores the metadata in the database.
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
$user = User::$username;
$sukses = 0;

$nama_driver   = $_POST['nama_driver'] ?? '';
$nik           = $_POST['nik'] ?? '';
$nama_trans    = $_POST['nama_trans'] ?? '';
$orang         = $_POST['orang'] ?? '';
$dosis         = $_POST['dosis'] ?? '';
$plant_name    = $_POST['plant_name'] ?? '';
$plant_id      = $_POST['plant_id'] ?? '';

$nama_file = ''; // will hold the uploaded file name if successful

// ============================================================================
// FILE UPLOAD PROCESSING
// ============================================================================

$allowed_extensions = array('png', 'jpg', 'jpeg');
$nama = date('ymdhis') . "_" . $user . ".png";
$x = explode('.', $nama);
$ekstensi = strtolower(end($x));
$ukuran = $_FILES['file']['size'] ?? 0;
$file_tmp = $_FILES['file']['tmp_name'] ?? '';

if (in_array($ekstensi, $allowed_extensions)) {
    if ($ukuran < 1200000 && $ukuran != 0) {
        $upload_path = 'application/views/capture/' . $nama;
        
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Insert file record into upload_vaksin table
            $query = mysqli_query($con, 
                "INSERT INTO upload_vaksin SET nama_file = '$nama'"
            );
            
            if ($query) {
                $sukses = 1;
                $nama_file = $nama;
                echo "<script type='text/javascript'>alert('FOTO BERHASIL DI UPLOAD')</script>";
            } else {
                echo "<script type='text/javascript'>alert('GAGAL MENGUPLOAD FOTO')</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('Gagal memindahkan file')</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Tidak Ada Foto yang di Upload...!!! (Cek File or Max size 1 MB)')</script>";
    }
} else {
    echo "<script type='text/javascript'>alert('Tidak Ada Foto yang di Upload...!!!')</script>";
}

// ============================================================================
// DATABASE QUERIES - VACCINATION RECORD
// ============================================================================

if ($sukses == 1) {
    mysqli_query($con, 
        "INSERT INTO tb_vaksin 
         SET pengendara         = '$orang',
             nama               = '$nama_driver',
             nik_8_digit_awal   = '$nik',
             nama_transporter   = '$nama_trans',
             status_vaksin      = '$dosis',
             plant_name         = '$plant_name',
             foto_name          = '$nama_file'"
    );
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Vaksin</title>
    <style type="text/css">
        .kotak_sq {
            width: 250px;
            background: blue;
            margin: 50px auto;
            padding: 50px 20px;
            box-shadow: 0px 0px 100px 4px #d6d6d6;
        }
        
        .tombol_pass {
            background: green;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_fail {
            background: red;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_na {
            background: black;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
    </style>

<br><br><br><br><br>

<?php if ($orang == 'driver'): ?>
<div class="kotak_sq">
    <form method="post" action="<?=route('pilih_helper')?>">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <button type="submit" class="btn btn-success tombol_pass">Next Helper</button>
        <br><br>
    </form>
</div>
<?php endif; ?>

<?php if ($orang == 'helper'): ?>
<div class="kotak_sq">
    <form method="post" action="<?=route('start')?>">
        <button type="submit" class="btn btn-success tombol_pass">Lanjut</button>
        <br><br>
    </form>
</div>
<?php endif; ?>

