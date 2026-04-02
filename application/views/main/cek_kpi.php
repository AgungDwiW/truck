<?php
/**
 * KPI Dashboard Page
 * 
 * This page displays Key Performance Indicators (KPIs) for Gate 1 and Gate 2
 * inspections, both daily and monthly.
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
$plant_id = '';

$bln = date("m");
$day = date("d");

$hasil = 0;      // Monthly KPI for Gate 2
$hasil_day = 0;  // Daily KPI for both gates (same calculation currently)

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Get plant_id of the current user
// ----------------------------------------------------------------------------
$sql_user = mysqli_query($con, 
    "SELECT * FROM tbm_user WHERE nama = '$user'"
);

if ($sql_user && mysqli_num_rows($sql_user) > 0) {
    $row_user = mysqli_fetch_assoc($sql_user);
    $plant_id = $row_user["plant_id"];
}

// ----------------------------------------------------------------------------
// 2. Monthly KPI calculation
// ----------------------------------------------------------------------------
// Total inspections this month (excluding 'Di Tolak di Pos 1')
$query_month_total = mysqli_query($con, 
    "SELECT COUNT(hasil_pemeriksaan) AS count
     FROM tb_ceklist
     WHERE hasil_pemeriksaan <> 'Di Tolak di Pos 1' 
       AND MONTH(tgbaca) = '$bln' 
       AND plant_id = '$plant_id'"
);
$A = mysqli_fetch_array($query_month_total);
$month_total = $A['count'] ?? 0;

// Inspections still pending Gate 2 this month
$query_month_pending = mysqli_query($con, 
    "SELECT COUNT(hasil_pemeriksaan) AS sisa
     FROM tb_ceklist
     WHERE hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
       AND MONTH(tgbaca) = '$bln' 
       AND plant_id = '$plant_id'"
);
$B = mysqli_fetch_array($query_month_pending);
$month_pending = $B['sisa'] ?? 0;

// Monthly KPI percentage (completed inspections)
if ($month_total > 0) {
    $hasil = round((($month_total - $month_pending) / $month_total) * 100);
}

// ----------------------------------------------------------------------------
// 3. Daily KPI calculation
// ----------------------------------------------------------------------------
// Total inspections today (excluding 'Di Tolak di Pos 1')
$query_day_total = mysqli_query($con, 
    "SELECT COUNT(hasil_pemeriksaan) AS count
     FROM tb_ceklist
     WHERE hasil_pemeriksaan <> 'Di Tolak di Pos 1' 
       AND DAY(tgbaca) = '$day' 
       AND plant_id = '$plant_id'"
);
$C = mysqli_fetch_array($query_day_total);
$day_total = $C['count'] ?? 0;

// Inspections still pending Gate 2 today
$query_day_pending = mysqli_query($con, 
    "SELECT COUNT(hasil_pemeriksaan) AS sisa
     FROM tb_ceklist
     WHERE hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
       AND DAY(tgbaca) = '$day' 
       AND plant_id = '$plant_id'"
);
$D = mysqli_fetch_array($query_day_pending);
$day_pending = $D['sisa'] ?? 0;

// Daily KPI percentage (completed inspections)
if ($day_total > 0) {
    $hasil_day = round((($day_total - $day_pending) / $day_total) * 100);
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Dashboard</title>
    <?= static_css('css/kotak.css') ?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead tr {
            background-color: yellow;
        }
        
        td {
            padding: 15px;
            text-align: center;
            font-size: 20px;
            color: white;
        }
        
        thead td {
            color: black;
            font-size: 30px;
            font-weight: bold;
        }
    </style>

<div class="container-fluid text-center">
    <a class="btn btn-danger" href="index">BACK</a>
</div>

<br><br><br>

<table>
    <thead>
        <tr>
            <td>% KPI</td>
            <td>DAILY</td>
            <td>MONTHLY</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="color: white">Gate 1</td>
            <td style="color: white"><?= $hasil_day ?>%</td>
            <td style="color: white"><?= $hasil_day ?>%</td>
        </tr>
        <tr>
            <td style="color: white">Gate 2</td>
            <td style="color: white"><?= $hasil_day ?>%</td>
            <td style="color: white"><?= $hasil ?>%</td>
        </tr>
    </tbody>
</table>

