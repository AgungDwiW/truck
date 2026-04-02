<?php
/**
 * Gate 1 Inspection Form
 * 
 * This page displays the inspection form for trucks that have passed initial checks.
 * Styled consistently with the Gate 2 Waiting List page.
 */

include "application/config/connection.php";

// API call (kept for compatibility, not used directly in UI)
$data = ApiCall("POST","https://adop.co.id/sandbox_api/Customer/GetOrders/", json_encode(
    [
    "orderIds"=> ["8000000303"],
    "orderType"=> null,
    "OrderStatusId"=> "10",
    "siteIds"=> [
        "9045"
    ],
    "materialIds"=> null,
    "pdtStart"=> "2025-02-01",
    "pdtEnd"=> "2026-02-11",
    "customerIds"=> ["666"],
    "customerShipToes"=> ["666"],
    "transporterId"=> null,
    "modifiedBy"=> "string",
    "createdBy"=> "string",
    "skip"=> 0,
    "take"=> 1000
    ]
));

$muat        = $_GET['muat'] ?? '';
$nopol       = str_replace(' ', '', $_GET['nopol'] ?? '');
$id_shipment = $_GET['id_shipment'] ?? '';
$username    = User::$username;
$plant_name  = User::$plant_name;
$plant_id    = User::$plantid;
$current_date = date("Y-m-d");
$current_time = date("H:i:s");
$idref        = time();
$seq          = 1;

// Determine muatan type for display
$MUATAN_TYPE = ($muat == 'FG') ? "FG" : "Material";
?>

    <style>
        /* ========== GLOBAL STYLES (mirroring db_waiting) ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: white;
            padding: 24px 16px;
            color: #1e293b;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header Card - identical to db_waiting */
        .header-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 24px 28px;
            margin-bottom: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            color: white;
        }

        .header-card h1 {
            font-size: 1.3rem;
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
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 12px;
        }

        /* Form Container - styled like the table wrapper in db_waiting */
        .form-container {
        }

        /* Form Elements Styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
            min-width: 180px;
        }

        .form-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-control, select.form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 1.3rem;
            font-weight: 500;
            border: 1px solid #cbd5e1;
            background-color: #fff;
            transition: 0.2s;
            font-family: inherit;
            color: #0f172a;
        }

        .form-control:focus, select.form-control:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.2);
        }

        .form-control[readonly] {
            background-color: #f8fafc;
            cursor: default;
            font-weight: 500;
            color: #1e293b;
            border-color: #e2e8f0;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2em;
        }

        /* Button - matches db_waiting's .btn-gate2 style */
        .btn-submit {
            background: linear-gradient(95deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
            margin-top: 0.5rem;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            background: linear-gradient(95deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(16,185,129,0.2);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        hr {
            margin: 1.8rem 0;
            border: 0;
            border-top: 2px solid #eef2f6;
        }

        /* Responsive */
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
            .form-container {
                padding: 20px 18px;
            }
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
            .btn-submit {
                font-size: 1.3rem;
                padding: 12px 20px;
            }
            .form-control, select.form-control {
                padding: 10px 12px;
            }
        } </style>
<div class="container">
    

    <!-- Form Container (replaces the old gate-card) -->
    <div class="form-container">
        <form method="post" action="<?= route("N_gate1") ?>">
            <!-- Supplier Dropdown -->
            <div class="form-group">
                <label class="form-label">Nama Supplier</label>
                <select class="form-control text-uppercase" name="supplier" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php
                    $res_sup = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' GROUP BY nama_supplier");
                    while ($row = mysqli_fetch_assoc($res_sup)) {
                        echo "<option value='" . htmlspecialchars($row['nama_supplier']) . "'>" . htmlspecialchars($row['nama_supplier']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Transporter Dropdown -->
            <div class="form-group">
                <label class="form-label">Nama Transporter</label>
                <select class="form-control text-uppercase" name="transporter" required>
                    <option value="">-- Pilih Transporter --</option>
                    <?php
                    if ($plant_id == '90A8') {
                        $res_trans = mysqli_query($con_140, "SELECT planned_transporter_name as name FROM tbl_otm_upload GROUP BY planned_transporter_name ASC");
                    } else {
                        $res_trans = mysqli_query($con, "SELECT nama_transporter as name FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' GROUP BY nama_transporter");
                    }
                    while ($row = mysqli_fetch_assoc($res_trans)) {
                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Row: No Polisi + ID Shipment -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">No Polisi</label>
                    <input type="text" class="form-control text-uppercase" name="nopol" value="<?= htmlspecialchars($nopol) ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Shipment</label>
                    <input type="text" class="form-control text-uppercase" value="<?= htmlspecialchars($id_shipment) ?>" readonly>
                </div>
            </div>

            <!-- Row: Jam + Tanggal -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam</label>
                    <input type="text" class="form-control" name="jam" value="<?= htmlspecialchars($current_time) ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control" name="tgl" value="<?= htmlspecialchars($current_date) ?>" readonly>
                </div>
            </div>

            <!-- Row: Petugas + Lokasi -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Petugas</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($plant_name) ?>" readonly>
                </div>
            </div>

            <!-- Hidden fields (unchanged) -->
            <input type="hidden" name="seq" value="<?= $seq ?>">
            <input type="hidden" name="idref" value="<?= $idref ?>">
            <input type="hidden" name="muat" value="<?= htmlspecialchars($muat) ?>">
            <input type="hidden" name="plant_id" value="<?= htmlspecialchars($plant_id) ?>">
            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($id_shipment) ?>">

            <hr>

            <button type="submit" class="btn-submit">
                ➡ Lanjut ke Checklist
            </button>
        </form>
    </div>
</div>