<?php
/**
 * User Registration Overview Page
 * 
 * This page lists all plants and shows the number of registered users per plant,
 * allowing administrators to add new users or plants.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include "application/assets/utility_function.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$plants = array();
$user_counts = array(); // plant_id => count

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch all plants
// ----------------------------------------------------------------------------
$sql = mysqli_query($con, 
    "SELECT * FROM tbm_plant ORDER BY plant_name ASC"
);

if ($sql) {
    while ($row = mysqli_fetch_assoc($sql)) {
        $plants[] = $row;
    }
}

// ----------------------------------------------------------------------------
// 2. Fetch user counts per plant in a single query
// ----------------------------------------------------------------------------
if (!empty($plants)) {
    $plant_ids = array_column($plants, 'plant_id');
    $ids_string = implode("','", $plant_ids);
    
    $count_sql = mysqli_query($con, 
        "SELECT plant_id, COUNT(*) AS user_count 
         FROM tbm_user 
         WHERE plant_id IN ('$ids_string') 
         GROUP BY plant_id"
    );
    
    if ($count_sql) {
        while ($row = mysqli_fetch_assoc($count_sql)) {
            $user_counts[$row['plant_id']] = $row['user_count'];
        }
    }
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration Overview</title>
    <style>
        table#fixed {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #330066;
            color: white;
            text-align: center;
            padding: 10px;
        }
        
        td {
            color: white;
            text-align: center;
            padding: 8px;
        }
        
        tr:nth-child(even) {
            background-color: #444;
        }
        
        .btn {
            padding: 5px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: white;
        }
    </style>

<table id="fixed" class="stripe row-border">
    <thead>
        <tr>
            <th>No</th>
            <th>PLANT NAME</th>
            <th>PLANT ID</th>
            <th>Qty USER</th>
            <th>EDIT</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    foreach ($plants as $row):
        $plant_id = $row['plant_id'];
        $count_user = $user_counts[$plant_id] ?? 0;
    ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= htmlspecialchars($row['plant_name']) ?></td>
            <td><?= htmlspecialchars($plant_id) ?></td>
            <td><?= $count_user ?></td>
            <td width="100px">
                <form method="post" action="<?=route('edit_user')?>">
                    <input type="hidden" name="plant_name" value="<?= htmlspecialchars($row['plant_name']) ?>">
                    <input type="hidden" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
                    <button type="submit" class="btn" style="background-color: blue">Add</button>
                </form>
            </td>
        </tr>
    <?php
        $no++;
    endforeach;
    ?>
    </tbody>
</table>
<br>

<!-- Add Plant button -->
<form method="post" action="<?=route('add_reg_user')?>">
    <input type="hidden" name="plant_id" value="<?= htmlspecialchars($nPlant ?? '') ?>">
    <button type="submit" class="btn" style="background-color: yellow"><strong>Add Plant</strong></button>
</form>

