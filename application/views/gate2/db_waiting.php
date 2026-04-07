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
include_once "application/config/connection.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$muat = $_GET['muat'] ?? '';
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
    FROM dbtruck.tbl_checklist ck
    WHERE 
        DATE(tgbaca) BETWEEN '{$TGLTRUCK}' AND '{$now}'
        AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
        AND ck.plant_id = '{$PLANT}' 
        AND muatan = '{$MUATAN}'
";
$result = mysqli_query($con, $str);
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Waiting List Gate 2</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #f0f2f5;
            padding: 24px 16px;
            color: #1e293b;
        }

        .container {
            width:100%
        }

        /* Header Card */
        .header-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 24px;
            padding: 24px 28px;
            margin-bottom: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            color: white;
        }

        .header-card h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .header-card p {
            opacity: 0.85;
            font-size: 0.95rem;
        }

        .badge-muatan {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 12px;
        }

        /* Table Wrapper */
        .table-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            overflow-x: auto;
            transition: all 0.2s;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 2rem;           /* Increased from 0.9rem for better readability */
            min-width: 700px;
        }

        .modern-table thead tr {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table th {
            padding: 18px 16px;
            text-align: left;
            font-weight: 700;           /* Bolder header */
            color: #1e293b;
            font-size: 2rem;           /* Increased from 0.9rem for better readability */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-table td {
            padding: 16px;
            border-bottom: 1px solid #eef2f6;
            vertical-align: middle;
            color: #1e293b;
            font-size: 1.8rem;           /* Increased from 0.9rem for better readability */
            font-weight: 500;
        }

        .modern-table tbody tr {
            transition: background 0.2s;
        }

        .modern-table tbody tr:hover {
            background: #fef9e6;
            cursor: pointer;
        }

        /* Styling for Nopol and Muatan - even clearer */
        .nopol-cell {
            font-weight: 800;
            font-family: 'Courier New', 'SF Mono', monospace;
            font-size: 1rem;
            letter-spacing: 0.8px;
            color: #0f3b5c;
        }

        .muatan-badge {
            background: #e6f7e6;
            color: #2e7d32;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
        }

        /* Button Modern */
        .btn-gate2 {
            background: linear-gradient(95deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 1.5rem;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-gate2:hover {
            background: linear-gradient(95deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(16,185,129,0.2);
        }

        .btn-gate2:active {
            transform: translateY(1px);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        }

        .empty-state svg {
            opacity: 0.5;
            margin-bottom: 16px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Responsive touches */
        @media (max-width: 640px) {
            body {
                padding: 16px 12px;
            }
            .header-card {
                padding: 18px 20px;
            }
            .header-card h1 {
                font-size: 1.4rem;
            }
            .btn-gate2 {
                padding: 6px 14px;
                font-size: 0.75rem;
            }
            .modern-table th,
            .modern-table td {
                font-size: 0.85rem;   /* Slightly smaller on mobile but still clear */
                padding: 12px 10px;
            }
            .nopol-cell {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header Section -->
    <div class="header-card" style='padding-top:1rem; padding-bottom:1rem'>
        <h4>Gate 2 Inspection Queue for <?=$MUATAN?></h4>
    </div>

    <?php if (empty($waiting_list)): ?>
        <!-- Empty state -->
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <p>Tidak ada antrian untuk Gate 2 saat ini.</p>
            <p style="font-size: 0.85rem;">Semua kendaraan sudah diproses atau belum masuk jadwal.</p>
        </div>
    <?php else: ?>
        <!-- Table with modern styling & larger fonts -->
        <div class="table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pemeriksa</th>
                        <th>Plant</th>
                        <th>Tanggal</th>
                        <th>No Polisi</th>
                        <th>Muatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($waiting_list as $data): ?>
                    <tr>
                        <td><?= htmlspecialchars($data['no']) ?></td>
                        <td><?= htmlspecialchars($data['petugas_pemeriksa']) ?></td>
                        <td><?= htmlspecialchars($data['plant_name']) ?></td>
                        <td><?= htmlspecialchars($data['tgbaca']) ?></td>
                        <td class="nopol-cell"><?= htmlspecialchars($data['nopol']) ?></td>
                        <td><span class="muatan-badge"><?= htmlspecialchars($data['muatan']) ?></span></td>
                        <td>
                            <form method="get" action="<?= route('cek_gate2') ?>" style="margin:0;">
                                <button type="submit" class="btn-gate2" name="kode" value="<?= htmlspecialchars($data['no']) ?>">
                                    ➡ Lanjut Gate 2
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>