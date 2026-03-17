<?php
/**
 * Edit User Registration Page
 * 
 * This page displays a form to register a new user (full name, NIK) for a specific plant.
 * The plant information is pre‑filled from the previous page.
 */
$plant_name = $_POST['plant_name'] ?? '';
$plant_id   = $_POST['plant_id'] ?? '';
?>

<div class="row">
    <div class="col-md-2">
        <label class="form-control text-center" style="background-color: yellow; color: black; font-size: 20px">Description</label>
    </div>
    <div class="col-md-4">
        <label class="form-control text-center" style="background-color: yellow; color: black; font-size: 20px">Current Value</label>
    </div>
</div>

<form method="post" action="reg_user">
    
    <!-- Full Name -->
    <div class="row">
        <div class="col-md-2">
            <label class="form-control text-center" style="background-color: blue; color: white">FULLNAME</label>
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="fullname" required>
        </div>
    </div>
    
    <!-- NIK -->
    <div class="row">
        <div class="col-md-2">
            <label class="form-control text-center" style="background-color: blue; color: white">NIK</label>
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="nik" required>
        </div>
    </div>
    
    <!-- Plant Name (read‑only) -->
    <div class="row">
        <div class="col-md-2">
            <label class="form-control text-center" style="background-color: blue; color: white">PLANT NAME</label>
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="plant_name" value="<?= htmlspecialchars($plant_name) ?>" readonly>
        </div>
    </div>
    
    <!-- Plant ID (read‑only) -->
    <div class="row">
        <div class="col-md-2">
            <label class="form-control text-center" style="background-color: blue; color: white">PLANT ID</label>
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>" readonly>
        </div>
    </div>
    
    <br><br>
    
    <!-- Hidden fields -->
    <input type="hidden" name="no" value="<?= htmlspecialchars($no ?? '') ?>">
    <input type="hidden" name="save" value="1">
    
    <!-- Submit button -->
    <button type="submit" class="btn" style="background-color: green; color: white; font-size: 30px">Save</button>
</form>