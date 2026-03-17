<?php
/**
 * Driver Registration Page
 * 
 * This page prompts the user to enter driver information (name, NIK, transporter)
 * before proceeding to the vaccination status screen.
 */
$plant_name = $_POST['plant_name'] ?? '';
$plant_id   = $_POST['plant_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration</title>
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
</head>
<body>

<div class="kotak_sq" style="padding-top: 0px;">
    <h1><label style="font-size: 20px">DRIVER</label></h1>
    
    <form method="post" action="status_vaksin">
        <input type="hidden" id="orang" name="orang" value="driver">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        
        <!-- Driver Name -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center font-weight-bold">
                    <input type="text" class="form-control" id="nama_driver" name="nama_driver" 
                           placeholder="Nama Driver (Sesuai KTP)" required>
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
        
        <!-- Submit button -->
        <button type="submit" class="btn btn-primary tombol_submit">Submit</button>
    </form>
    
    <br>
    
    <!-- Skip button (go directly to helper registration) -->
    <form method="post" action="pilih_helper">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <input type="hidden" name="muat" value="Material">
        <button type="submit" class="btn btn-primary tombol_no">SKIP</button>
    </form>
</div>

</body>
</html>