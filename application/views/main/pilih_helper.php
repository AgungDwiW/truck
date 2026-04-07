<?php
/**
 * Helper Registration Page
 * 
 * This page allows registration of helper information (name, NIK, transporter)
 * and optionally records vaccination status.
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
$plant_name = $_POST['plant_name'] ?? '';
$plant_id   = $_POST['plant_id'] ?? '';
$dosis      = $_POST['dosis'] ?? '';

$nama_driver = $_POST['nama_driver'] ?? '';
$nik         = $_POST['nik'] ?? '';
$nama_trans  = $_POST['nama_trans'] ?? '';
$orang       = $_POST['orang'] ?? '';

// ============================================================================
// DATABASE QUERIES - DATA STORAGE
// ============================================================================

// ----------------------------------------------------------------------------
// 1. If vaccination status is 'belum', insert a record without a photo
// ----------------------------------------------------------------------------
if ($dosis == 'belum') {
    mysqli_query($con, 
        "INSERT INTO tb_vaksin 
         SET pengendara       = '$orang',
             nama             = '$nama_driver',
             nik_8_digit_awal = '$nik',
             nama_transporter = '$nama_trans',
             status_vaksin    = '$dosis',
             plant_name       = '$plant_name'"
    );
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Registration</title>
    <style type="text/css">
        body {
            font-family: sans-serif;
            background-color: black;
        }
        
        h1 {
            text-align: center;
            padding-top: 0px;
            font-weight: 300;
            color: white;
        }
        
        .kotak_sq {
            width: 250px;
            background: blue;
            margin: 100px auto;
            padding: 50px 20px;
            box-shadow: 0px 0px 100px 4px #d6d6d6;
        }
        
        .tombol_submit {
            background: black;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_no {
            background: white;
            color: black;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
    </style>

<div class="kotak_sq" style="padding-top: 0px;">
    <h1><label style="font-size: 20px">HELPER</label></h1>
    
    <form method="post" action="<?=route('status_vaksin_helper')?>">
        <input type="hidden" id="orang" name="orang" value="helper">
        
        <!-- Helper Name -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center font-weight-bold">
                    <input type="text" class="form-control" id="nama_driver" name="nama_driver" 
                           placeholder="Nama Helper (Sesuai KTP)" required>
                </div>
            </div>
        </div>
        <br>
        
        <!-- NIK (first 8 digits) -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center font-weight-bold">
                    <input type="text" class="form-control" id="nik" name="nik" maxlength="8" 
                           placeholder="8 digit awal NIK" required>
                </div>
            </div>
        </div>
        <br>
        
        <!-- Transporter Name -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center font-weight-bold">
                    <input type="text" class="form-control" id="nama_trans" name="nama_trans" 
                           placeholder="Nama Transporter" required>
                </div>
            </div>
        </div>
        <br>
        
        <!-- Hidden plant info -->
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        
        <!-- Submit button -->
        <button type="submit" class="btn btn-primary tombol_submit">Submit</button>
    </form>
    
    <br>
    
    <!-- Skip button -->
    <form method="post" action="<?=route('index')?>">
        <input type="hidden" name="muat" value="Material">
        <button type="submit" class="btn btn-primary tombol_no">SKIP</button>
    </form>
</div>

