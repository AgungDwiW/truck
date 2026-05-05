<?php
/**
 * Gate 1 Inspection Form
 * 
 * This page displays the inspection form for trucks that have passed initial checks.
 * Styled consistently with the Gate 2 Waiting List page.
 */

include "application/config/connection.php";
include "application/config/connectionSL.php";

$plant = User::$plantid;
$dataShipment = ApiCall("GET", API_SERVER. "Orders?OrderIds={$_GET['id_shipment']}",[]);
$dataShipment = json_decode($dataShipment,1);

if(isset($dataShipment[0]) and isset($dataShipment[0]['siteId']) and $dataShipment[0]['siteId']!=User::$plantid){
    $plant = User::$plantid;
     echo "<script>
            window.alert('Plant order ({$dataShipment[0]['siteId']}) tidak sesuai dengan plant user ({$plant})!!!');
            window.location = 'cek_nopol';
          </script>";
    exit();
}

$supplier_id = '';
$supplier_name = '';
$transporter_id = '';
$transporter_name = '';
$DN_number = '';

if($dataShipment and count($dataShipment)>0){
    $dataShipment   = $dataShipment[0];
    
    //-------------------------------------------------------------------------
    //                              GETTING DATA
    //-------------------------------------------------------------------------
    
    // Check if Customer/Supplier is already tied
    if (isset($dataShipment['customer'])){
        $supplier_id    = $dataShipment['customer']['customerId'];
        $supplier_name  = $dataShipment['customer']['customerName'];
    }
    
    // Check if Transporter is already tied
    if (isset($dataShipment['transporterId']) && $dataShipment['transporterId']){
        $transporter_id = $dataShipment['transporterId'];
        
        // Fetch just this specific transporter's name to display it
        $_dataTransporter = ApiCall("GET", API_SERVER. "Transporters?isActive=true&TransporterIds={$transporter_id}","");
        $_dataTransporter = json_decode($_dataTransporter,1);
        if (isset($_dataTransporter[0])) {
            $transporter_name = $_dataTransporter[0]['transporterName'];
        } else {
            $transporter_name = 'undefined';
        }
    }

    //-------------------------------------------------------------------------
    //                              Insert DN & OTM
    //-------------------------------------------------------------------------
    $plant = User::$plantid;
    $DN             = ApiCall("POST", API_SERVER. "DeliveryNotes/ConvertOrders",'["'.$_GET['id_shipment'].'"]');
    $DN             = json_decode($DN,1);
    $DN_number      = $DN[0]['deliveryNumber'];
    
    $dataOTM = [
        'shipment_id'               => "S".$_GET['id_shipment'],
        'pk'                        => $_GET['id_shipment'],
        'order_release_id'          => $_GET['id_shipment'],
        'so_sto_no'                 => $_GET['id_shipment'],
        'dn_number_upload'          => $DN_number,
        'service_provider_id'       => $transporter_id,
        'transporter_name'          => $transporter_name,
        'source_location_id'        => User::$plantid,
        'source_location_name'      => User::$plant_name,
        'destination_location_id'   => $supplier_id,
        'destination_location_name' => $supplier_name,
        "pickup_start_date"         => date("Y-m-d"),
        "pickup_end_date"           => date("Y-m-d"),
        "movement_type"             => "FACTORY TO DISTRIBUTOR",
        "pick_up_window"            => "1",
        "delivery_type"             => "DISTRIBUTOR PICKUP",
        "domain_name"               => "VIT",
        "user_id_upload"            => User::$username,
        "indicator"                 => ' ',
        "expiration_date"           => date('Y-m-d', strtotime('+2 years')),
        "latest_event_date"         => date('Y-m-d'),
        "latest_event_description"  => "create from truck",
        "mode"                      => ' ',
        "truck_id"                  => ' ',
        "driver_name"               => ' ',
        "driver_mobile_no"          => ' ',
        "container_no"              => ' ',
        "seal_number"               => ' ',
        "total_item_package_count"  => ' ',
        "first_equipment_group_id"  => ' ',
    ];
    $otm = new Table("tbl_picking_shipment_otm_upload", "smartlogistic", $conSL);
    $otm->replace($dataOTM)->execute();
}

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

<?= static_css('css/select2.min.css') ?>
<?= static_js('js/select2.min.js') ?>
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
        cursor: not-allowed;
        font-weight: 500;
        color: #64748b;
        border-color: #e2e8f0;
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.2em;
    }

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

    @media (max-width: 640px) {
        body { padding: 16px 12px; }
        .form-row { flex-direction: column; gap: 1rem; }
        .btn-submit { font-size: 1.3rem; padding: 12px 20px; }
        .form-control, select.form-control { padding: 10px 12px; }
    }
</style>

<div class="container">
    <div class="form-container">
        <form method="post" action="<?= route("N_gate1") ?>">
            
            <div class="form-group">
                <label class="form-label">DN</label>
                <input type='text' class="form-control" name="no_dn" readonly value='<?= htmlspecialchars($DN_number) ?>'>
            </div>

            <!-- Supplier Field -->
            <div class="form-group">
                <label class="form-label">Customer / Supplier</label>
                <?php if(!empty($supplier_id)): ?>
                    <!-- If tied, make it readonly -->
                    <input type='text' name='supplier' class="form-control text-uppercase" readonly value='<?= htmlspecialchars($supplier_id . " - " . $supplier_name) ?>'>
                <?php else: ?>
                    <!-- If not tied, show search dropdown -->
                    <select class="form-control text-uppercase" id='supplier_select' required name='supplier'>
                    </select>
                <?php endif;?>
            </div>

            <!-- Transporter Field -->
            <div class="form-group">
                <label class="form-label">Nama Transporter</label>
                <?php if(!empty($transporter_id)): ?>
                    <!-- If tied, make it readonly -->
                    <input type='text' class="form-control text-uppercase" name='transporter' readonly value='<?= htmlspecialchars($transporter_id . " - " . $transporter_name) ?>'>
                <?php else: ?>
                    <!-- If not tied, show search dropdown -->
                    <select class="form-control text-uppercase" required name='transporter' id='transporter_select'>
                    </select>
                <?php endif;?>
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
                    <input type="text" class="form-control" name="petugas" value="<?= htmlspecialchars($username) ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" class="form-control" name="lokasi" value="<?= htmlspecialchars($plant_name) ?>" readonly>
                </div>
            </div>

            <!-- Hidden fields -->
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

<script>
$(document).ready(function() {
    
    // 1. Initialize Supplier Select2 (Only runs if the element exists)
    if ($('#supplier_select').length) {
        $('#supplier_select').select2({
            placeholder: "Ketik untuk mencari customer/supplier...",
            minimumInputLength: 2,
            ajax: {
                url: '<?= route("ajax") ?>',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: 'supplier', 
                        searchKey: params.term || "",
                        siteId: "<?= User::$plantid ?>",
                        skip: 0,
                        take: 30
                    };
                },
                processResults: function (data) {
                    var mappedResults = $.map(data, function (item) {
                        var id = item.vendorId || item.sapCustomerId;
                        var name = item.vendorName || item.customerName;
                        return { id: id + ' - ' + name, text: id + ' - ' + name };
                    });
                    return { results: mappedResults };
                },
                cache: true
            }
        });
    }

    // 2. Initialize Transporter Select2 (Only runs if the element exists)
    if ($('#transporter_select').length) {
        $('#transporter_select').select2({
            placeholder: "Ketik untuk mencari transporter...",
            minimumInputLength: 2,
            ajax: {
                url: '<?= route("ajax") ?>',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: 'transporter', 
                        searchKey: params.term || "",
                        skip: 0,
                        take: 30
                    };
                },
                processResults: function (data) {
                    var mappedResults = $.map(data, function (item) {
                        return { 
                            id: item.transporterId + ' - ' + item.transporterName, 
                            text: item.transporterId + ' - ' + item.transporterName 
                        };
                    });
                    return { results: mappedResults };
                },
                cache: true
            }
        });
    }
});
</script>