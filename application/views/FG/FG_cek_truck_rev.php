<div class="body-wrap-with-navbar">

<?php

include "application/config/connection.php";

$data = sendPOST("https://adop.co.id/sandbox_api/Customer/GetOrders/", json_encode(
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
// Debuger::show();
// Debuger::dump($data);
$muat        = $_POST['muat'] ?? '';
$nopol       = str_replace(' ', '', $_POST['nopol'] ?? '');
$id_shipment = $_POST['id_shipment'] ?? '';
$username    = User::$username;
$plant_name  = User::$plant_name;
$plant_id    = User::$plantid;
$current_date = date("Y-m-d");
$current_time = date("H:i:s");
$idref        = time(); // Fixed mktime() error for PHP 8+
$seq          = 1;


?>
<style type="text/css">
    /* Ensure the card is always 250px from top, never under navbar */
    body, html {
        height: 100%;
        margin: 0;
    }

    .body-wrap-with-navbar {
        padding-top: 250px;           /* Fixed space from top */
        min-height: 100vh;
        margin: 0;                     /* Remove any negative margin */
        display: flex;
        justify-content: center;       /* Keep horizontal centering */
        align-items: flex-start;       /* Align to top (padding handles spacing) */
    }

    .main-card {
        background-color: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 600px;
        margin: 0;                      /* No extra margin – padding on parent controls top */
    }

    .form-label {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .btn-submit {
        background-color: #28a745;
        color: white;
        padding: 15px;
        font-size: 1.2rem;
        border-radius: 8px;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-submit:hover {
        background-color: #218838;
    }

    .row-custom {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .col-custom {
        flex: 1;
    }
</style>

<div class="main-card">
    <form method="post" action=<?=route("N_gate1")?>>
        
        <div class="mb-4">
            <label class="form-label">Nama Supplier</label>
            <select class="form-control text-uppercase" name="supplier" required>
                <option value="">-- Pilih Supplier --</option>
                <?php
                $res_sup = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' GROUP BY nama_supplier");
                while ($row = mysqli_fetch_assoc($res_sup)) {
                    echo "<option value='{$row['nama_supplier']}'>{$row['nama_supplier']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-4">
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
                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="row-custom">
            <div class="col-custom">
                <label class="form-label">No Polisi</label>
                <input type="text" class="form-control text-uppercase" name="nopol" value="<?php echo $nopol; ?>" readonly>
            </div>
            <div class="col-custom">
                <label class="form-label">ID Shipment</label>
                <input type="text" class="form-control text-uppercase" value="<?php echo $id_shipment; ?>" readonly>
            </div>
        </div>

        <div class="row-custom">
            <div class="col-custom">
                <label class="form-label">Jam</label>
                <input type="text" class="form-control" name="jam" value="<?php echo $current_time; ?>" readonly>
            </div>
            <div class="col-custom">
                <label class="form-label">Tanggal</label>
                <input type="text" class="form-control" name="tgl" value="<?php echo $current_date; ?>" readonly>
            </div>
        </div>

        <div class="row-custom">
            <div class="col-custom">
                <label class="form-label">Petugas</label>
                <input type="text" class="form-control" value="<?php echo $username; ?>" readonly>
            </div>
            <div class="col-custom">
                <label class="form-label">Lokasi</label>
                <input type="text" class="form-control" value="<?php echo $plant_name; ?>" readonly>
            </div>
        </div>

        <input type="hidden" name="seq" value="<?php echo $seq; ?>">
        <input type="hidden" name="idref" value="<?php echo $idref; ?>">
        <input type="hidden" name="muat" value="<?php echo $muat; ?>">
        <input type="hidden" name="plant_id" value="<?php echo $plant_id; ?>">
        <input type="hidden" name="id_barang" value="<?php echo $id_shipment; ?>">

        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">

        <button type="submit" class="btn-submit">Go Ceklist</button>
    </form>
</div>

</div>
