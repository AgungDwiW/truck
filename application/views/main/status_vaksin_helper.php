<?php
/**
 * Helper Vaccination Status Selection Page
 * 
 * This page allows the user to select the helper's vaccination status
 * (2 doses, 1 dose, exception, not vaccinated) and proceeds accordingly.
 */
$nama_driver = $_POST['nama_driver'] ?? '';
$nik         = $_POST['nik'] ?? '';
$nama_trans  = $_POST['nama_trans'] ?? '';
$orang       = $_POST['orang'] ?? '';
$plant_name  = $_POST['plant_name'] ?? '';
$plant_id    = $_POST['plant_id'] ?? '';
?>


    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Vaccination Status</title>
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
        
        .tombol_hijau {
            background: green;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_yellow {
            background: yellow;
            color: black;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_except {
            background: purple;
            color: white;
            font-size: 20pt;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 3px;
            padding: 20px 20px;
        }
        
        .tombol_merah {
            background: red;
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
    
    <!-- 2 doses -->
    <form method="post" action="<?= route('foto_vaksin')?>">
        <input type="hidden" name="nama_driver" value="<?= htmlspecialchars($nama_driver) ?>">
        <input type="hidden" name="nik" value="<?= htmlspecialchars($nik) ?>">
        <input type="hidden" name="nama_trans" value="<?= htmlspecialchars($nama_trans) ?>">
        <input type="hidden" name="orang" value="<?= htmlspecialchars($orang) ?>">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <input type="hidden" name="dosis" value="2 dosis">
        <button type="submit" class="btn btn-primary tombol_hijau">2 dosis vaksin</button>
    </form>
    
    <br>
    
    <!-- 1 dose -->
    <form method="post" action="<?= route('foto_vaksin')?>">
        <input type="hidden" name="nama_driver" value="<?= htmlspecialchars($nama_driver) ?>">
        <input type="hidden" name="nik" value="<?= htmlspecialchars($nik) ?>">
        <input type="hidden" name="nama_trans" value="<?= htmlspecialchars($nama_trans) ?>">
        <input type="hidden" name="orang" value="<?= htmlspecialchars($orang) ?>">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <input type="hidden" name="dosis" value="1 dosis">
        <button type="submit" class="btn btn-primary tombol_yellow">1 dosis vaksin</button>
    </form>
    
    <br>
    
    <!-- Exception -->
    <form method="post" action="<?= route('foto_vaksin')?>">
        <input type="hidden" name="nama_driver" value="<?= htmlspecialchars($nama_driver) ?>">
        <input type="hidden" name="nik" value="<?= htmlspecialchars($nik) ?>">
        <input type="hidden" name="nama_trans" value="<?= htmlspecialchars($nama_trans) ?>">
        <input type="hidden" name="orang" value="<?= htmlspecialchars($orang) ?>">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <input type="hidden" name="dosis" value="exception">
        <button type="submit" class="btn btn-primary tombol_except">Exception</button>
    </form>
    
    <br>
    
    <!-- Not vaccinated (go to start) -->
    <form method="post" action="<?=route('index')?>">
        <input type="hidden" name="nama_driver" value="<?= htmlspecialchars($nama_driver) ?>">
        <input type="hidden" name="nik" value="<?= htmlspecialchars($nik) ?>">
        <input type="hidden" name="nama_trans" value="<?= htmlspecialchars($nama_trans) ?>">
        <input type="hidden" name="orang" value="<?= htmlspecialchars($orang) ?>">
        <input type="hidden" id="plant_name" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>">
        <input type="hidden" id="plant_id" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
        <input type="hidden" name="dosis" value="belum">
        <button type="submit" class="btn btn-primary tombol_merah">Belum Vaksin</button>
    </form>
    
    <br>
    
    <!-- Skip (go directly to start) -->
    <form method="post" action="<?=route('index')?>">
        <input type="hidden" name="dosis" value="skip">
        <button type="submit" class="btn btn-primary tombol_no">SKIP</button>
    </form>
</div>

