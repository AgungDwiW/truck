<?php
/**
 * Truck Check & Synchronization Page
 * 
 * This page verifies delivery codes, synchronizes data between cloud and local
 * databases, and prepares the Gate 1 inspection form.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include  "application/config/connection.php";

// API call (kept for compatibility, not used directly in UI)
$plant_id = User::$plantid;

$nopol = $_POST['nopol'];
$date = date("Y-m-d");
$jam  = date("H:i:s");
$idref = time();
$seq   = 1;
$username = User::$username;

// ----------------------------------------------------------------------------
// Fetch tujuan_pengiriman options for dropdown (if needed in future)
// ----------------------------------------------------------------------------
$tujuan_result = mysqli_query($con, 
    "SELECT * FROM tujuan_pengiriman ORDER BY tujuan ASC"
);
while ($row = mysqli_fetch_assoc($tujuan_result)) {
    $tujuan_options[] = $row;
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Truck Check</title>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style type="text/css">
    /* ========== GLOBAL STYLES ========== */
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
        padding: 10px 12px;
        border-radius: 4px;
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

    /* Button Styles */
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
        border-radius: 4px;
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

    /* Select2 overrides to match UI */
    .select2-container .select2-selection--single {
        height: 48px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px;
        font-size: 1.2rem;
        color: #0f172a;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }

    @media (max-width: 640px) {
        body { padding: 16px 12px; }
        .form-row { flex-direction: column; gap: 1rem; }
        .btn-submit { font-size: 1.3rem; padding: 12px 20px; }
    }
</style>

<div class='container'>
    <div class="form-container">
        <form method="post" action="<?=route("N_gate1")?>">
            <input type='hidden' name='id_checklist' value='<?=$id_checklist ?? ''?>'>
            
            <div class="form-group">
                <label class="form-label">No DN</label>
                <input type="text" class="form-control text-uppercase" id="no_dn" name="no_dn" 
                       value="<?= htmlspecialchars($_POST['no_dn'] ?? '') ?>" readonly>
            </div>

            <!-- Supplier Field -->
            <div class="form-group">
                <label class="form-label">Nama Supplier</label>
                <select class="form-control text-uppercase" id="supplier" name="supplier" required>
                    <?php if(!empty($supplier)): ?>
                        <option value="<?= htmlspecialchars($supplier) ?>" selected>
                            <?= htmlspecialchars($supplier) ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Transporter Field -->
            <div class="form-group">
                <label class="form-label">Nama Transporter</label>
                <select class="form-control text-uppercase" id="transporter" name="transporter" required>
                    <!-- Populated by Select2 -->
                </select>
            </div>

            <!-- Row: No Polisi -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">No Polisi</label>
                    <input type="text" class="form-control text-uppercase" id="nopol" name="nopol" 
                           value="<?= htmlspecialchars($nopol) ?>" readonly>
                </div>
            </div>
            
            <!-- Row: Jam & Tanggal -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Pemeriksaan</label>
                    <input type="text" class="form-control" id="jam" name="jam" 
                           value="<?= date('H:i:s') ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Pemeriksaan</label>
                    <input type="text" class="form-control text-uppercase" id="tgl" name="tgl" 
                           value="<?= date('Y-m-d') ?>" readonly>
                </div>
            </div>
            
            <!-- Row: Petugas & Lokasi -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Petugas Pemeriksa</label>
                    <input type="text" class="form-control" id="petugas" name="petugas" 
                           value="<?= htmlspecialchars(User::$username) ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi Plant</label>
                    <input type="text" class="form-control" id="lokasi" name="lokasi" 
                           value="<?= htmlspecialchars(User::$plant_name) ?>" readonly>
                </div>
            </div>
            
            <!-- Hidden fields -->
            <input type="hidden" id="seq" name="seq" value="<?= $seq ?>">
            <input type="hidden" id="idref" name="idref" value="<?= $idref ?>">
            <input type="hidden" name="muat" value="Material">
            
            <hr>
            
            <button type="submit" class="btn-submit">➡ Lanjut ke Checklist</button>
            
        </form>
    </div>
</div>

<!-- jQuery (Required for Select2) if not already loaded -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    
    // 1. Initialize Supplier Select2
    $('#supplier').select2({
        placeholder: "Ketik untuk mencari supplier...",
        minimumInputLength: 2, 
        ajax: {
            url: '<?= route('ajax')?>', 
            type: 'POST',
            dataType: 'json',
            delay: 250, 
            data: function (params) {
                return {
                    searchKey: params.term || "", 
                    siteId: "<?= User::$plantid ?>", 
                    skip: 0,
                    take: 30,
                    type: "supplier"
                };
            },
            processResults: function (data) {
                var mappedResults = $.map(data, function (item) {
                    var id = item.vendorId || item.partnerNumber;
                    var name = item.vendorName || item.firstName;
                    return {
                        id: id + ' - ' + name, 
                        text: id + ' - ' + name 
                    };
                });
                return { results: mappedResults };
            },
            cache: true
        }
    });

    // 2. Initialize Transporter Select2
    $('#transporter').select2({
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
});
</script>