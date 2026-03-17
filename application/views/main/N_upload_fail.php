<?php
/**
 * Photo Upload Failure Handling Page
 * 
 * This page processes photo uploads for inspection failures, stores the photo
 * metadata in the database, and updates the inspection status accordingly.
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
$user      = User::$username;
$sukses    = 0;
$temuan    = $_POST['tem'] ?? '';

$idref     = $_POST['idref'] ?? '';
$ccp       = $_POST['ccp'] ?? '';
$utama     = $_POST['utama'] ?? '';
$nopol     = $_POST['nopol'] ?? '';
$lokasi    = $_POST['lokasi'] ?? '';
$kode_kirim = $_POST['kode_kirim'] ?? '';
$driver    = $_POST['driver'] ?? '';
$supplier  = $_POST['supplier'] ?? '';

$nama_file = ''; // Will hold the uploaded file name if successful

// ============================================================================
// DATABASE QUERIES - FILE UPLOAD PROCESSING
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Validate and process file upload if temuan is provided
// ----------------------------------------------------------------------------
if (!empty($temuan)) {
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
                // Insert file record into upload table
                $query = mysqli_query($con, 
                    "INSERT INTO upload SET nama_file = '$nama'"
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
} else {
    echo "<script type='text/javascript'>alert('No Data... Pastikan Temuan di isi...!!!')</script>";
}

// ----------------------------------------------------------------------------
// 2. If upload successful and temuan exists, insert failure record and update status
// ----------------------------------------------------------------------------
if ($sukses == 1 && !empty($temuan)) {
    // Insert failure photo metadata
    mysqli_query($con, 
        "INSERT INTO tb_foto 
         SET idref        = '$idref',
             item_utama   = '$ccp',
             description  = '$temuan',
             petugas      = '$user',
             foto_name    = '$nama_file',
             username     = '$user',
             utama        = '$utama',
             nopol        = '$nopol',
             lokasi       = '$lokasi'"
    );
    
    // Update the specific utama field in tb_ceklist based on $utama value
    if ($utama == 1) {
        mysqli_query($con, 
            "UPDATE tb_ceklist SET utama1 = 0 WHERE idref = '$idref'"
        );
    }
    if ($utama == 2) {
        mysqli_query($con, 
            "UPDATE tb_ceklist SET utama2 = 0 WHERE idref = '$idref'"
        );
    }
    if ($utama == 3) {
        mysqli_query($con, 
            "UPDATE tb_ceklist SET utama3 = 0 WHERE idref = '$idref'"
        );
    }
    if ($utama == 4) {
        mysqli_query($con, 
            "UPDATE tb_ceklist SET utama4 = 0 WHERE idref = '$idref'"
        );
    }
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
    <title>Upload Failure</title>
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
</head>
<body>

<br><br><br><br><br>

<div class="kotak_sq">
    
    <!-- BACK button form -->
    <form method="post" action="N_gate1">
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        
        <button type="submit" class="btn btn-success tombol_pass">BACK</button>
        <br><br>
    </form>
    
    <!-- Tambah Foto button form -->
    <form method="post" action="N_foto_gate1">
        <input type="hidden" name="utama" value="<?= htmlspecialchars($utama) ?>">
        <input type="hidden" name="idref" value="<?= htmlspecialchars($idref) ?>">
        <input type="hidden" name="ceklist" value="<?= htmlspecialchars($ccp) ?>">
        <input type="hidden" name="nopol" value="<?= htmlspecialchars($nopol) ?>">
        <input type="hidden" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>">
        <input type="hidden" name="kode_kirim" value="<?= htmlspecialchars($kode_kirim) ?>">
        <input type="hidden" name="tambah_foto" value="1">
        <input type="hidden" name="driver" value="<?= htmlspecialchars($driver) ?>">
        <input type="hidden" name="supplier" value="<?= htmlspecialchars($supplier) ?>">
        
        <button type="submit" class="btn btn-warning tombol_na">Tambah Foto</button>
        <br><br>
    </form>
    
</div>

</body>
</html>