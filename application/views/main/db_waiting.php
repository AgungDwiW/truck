<?php
/**
 * Waiting List for Gate 2 Inspections
 * 
 * This page displays a list of trucks that have passed Gate 1 and are waiting
 * for Gate 2 inspection. It allows inspectors to proceed to Gate 2.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include_once "application/config/connectionSL.php";
include_once "application/config/connectionEvisitor.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$muat = $_POST['muat'] ?? '';
$PLANT = User::$plantid;
$MUATAN = ($muat == 'FG') ? "FG" : "Material";
$TGLTRUCK = date('Y-m-d', strtotime(date('Y-m-d') . ' -2 day'));
$TGLEVISITOR = date('Y-m-d', strtotime(date('Y-m-d') . ' -21 day'));
$now = date("Y-m-d");

$waiting_list = array(); // will store inspection records

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch inspections waiting for Gate 2
// ----------------------------------------------------------------------------
$str = "
    SELECT 
        ck.`no`, 
        ck.`petugas_pemeriksa`, 
        ck.plant_name, 
        ck.tgbaca, 
        ck.nopol, 
        ck.muatan, 
        DATE(ck.tgbaca) AS TGLPERIKSA
    FROM dbtruck.tb_ceklist ck
    WHERE 
        DATE(tgbaca) BETWEEN '{$TGLTRUCK}' AND '{$now}'
        AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
        AND ck.plant_id = '{$PLANT}' 
        AND muatan = '{$MUATAN}'
";

$result = mysqli_query($conSL, $str);
if ($result) {
    while ($data = mysqli_fetch_assoc($result)) {
        $waiting_list[] = $data;
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
    <title>Waiting List Gate 2</title>
    <style type="text/css">
        span.btn { 
            background: red; 
            color: white; 
            padding: 5px; 
            border: 1px solid red; 
            border-radius: 5px; 
        }
        
        #demoA thead, #demoA tbody { 
            display: block; 
        }
        
        #demoA tbody {
            max-height: 350px;
            overflow: auto;
            font-size: 13px;
        }
        
        #demoA { 
            width: 100%; 
            background-color: #566b91;
        }
        
        #demoA th, #demoA td {
            width: 500px;
            font-size: 20px;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
    </style>
</head>
<body>

<table id="demoA">
    <thead>
        <tr>
            <th>No</th>
            <th>Pemeriksa</th>
            <th>Plant</th>
            <th>Tanggal</th>
            <th>Nopol</th>
            <th>Muatan</th>
            <th>Cek Gate</th>   
        </tr>
    </thead>
    <tbody>
    <?php foreach ($waiting_list as $data): ?>
        <tr>
            <td><?= htmlspecialchars($data['no']) ?></td>
            <td><?= htmlspecialchars($data['petugas_pemeriksa']) ?></td>
            <td><?= htmlspecialchars($data['plant_name']) ?></td>
            <td><?= htmlspecialchars($data['tgbaca']) ?></td>
            <td class="text-uppercase"><?= htmlspecialchars($data['nopol']) ?></td>
            <td class="text-uppercase"><?= htmlspecialchars($data['muatan']) ?></td>
            <td>
                <form method="post" action="cek_gate2">
                    <button type="submit" class="btn btn-success" name="kode" value="<?= htmlspecialchars($data['no']) ?>">
                        Lanjut Gate 2
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>