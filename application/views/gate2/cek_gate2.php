<?php
/**
 * Gate 2 Inspection Page
 * * This page displays the Gate 2 inspection form, including driver and vehicle
 * information, and allows inspectors to check completeness of main and additional
 * checkpoints.
 * * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include "application/config/connection.php";
// include "application/assets/function.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================

$kode = mysqli_real_escape_string($con, $_GET['kode'] ?? '');

$muat = $nopol = $nama_sopir = $plant_id = $date = '';
$data = array();

$umul = $status_usia = $color_usia = $tipe_sim = $valid = $valid_ddt = '';
$status_sim = $status_ddt = $valid_date = $valid_date_ddt = '';
$is_sim_valid = false;
$is_ddt_valid = false;

$utama_rows = array();
$tambahan_rows = array();

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch base checklist data
// ----------------------------------------------------------------------------
$query_mysql = mysqli_query($con, 
    "SELECT * FROM tbl_checklist WHERE no = '$kode' AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2'"
) or die(mysqli_error($con));

if (mysqli_num_rows($query_mysql) == 0) {
    die("<span style='color:red'>Data tidak ditemukan.</span>");
}

$data = mysqli_fetch_array($query_mysql);

$muat      = $data['muatan'];
$nopol     = $data['nopol'];
$nama_sopir = $data['nama_sopir'] ?? '';
$plant_id  = User::$plantid;
$date      = date("Y-m-d");

// ----------------------------------------------------------------------------
// 2. If muatan is FG, fetch e‑Visitor data and perform validations
// ----------------------------------------------------------------------------
if ($muat == 'FG') {
    $clean_nopol = str_replace(' ', '', $nopol);
    $nama_sopir = '';
    $seq_visitor = 0;
}

// ----------------------------------------------------------------------------
// 3. Fetch main checkpoints (utama) for display
// ----------------------------------------------------------------------------
$result_utama = mysqli_query($con, 
    "SELECT *, 
            id AS namee,
            CONCAT(param_name, id, id) AS idgreen 
     FROM tbl_checklist_param 
     WHERE id > 4 AND param_type = 'utama'"
);

if ($result_utama) {
    while ($row = mysqli_fetch_assoc($result_utama)) {
        $utama_rows[] = $row;
    }
}

// ----------------------------------------------------------------------------
// 4. Fetch additional checkpoints (tambahan) for display
// ----------------------------------------------------------------------------
$result_tambahan = mysqli_query($con, 
    "SELECT *, 
            id AS namee,
            CONCAT(param_name, id, id) AS idgreen 
     FROM tbl_checklist_param
     where param_type = 'tambahan'
     "
);

if ($result_tambahan) {
    while ($row = mysqli_fetch_assoc($result_tambahan)) {
        $tambahan_rows[] = $row;
    }
}

// ============================================================================
// HTML OUTPUT STARTS HERE
// ============================================================================
?>
    <style type="text/css">
        * {
            box-sizing: border-box;
        }
        body { 
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background-color: #f0f2f5; 
            color: #1e293b;
            padding: 24px 16px;
            margin: 0;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Card */
        .header-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 24px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            color: white;
        }

        .header-card h4 {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.3px;
        }

        /* Form Wrapper */
        .form-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            padding: 32px 28px;
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f3b5c;
            border-bottom: 2px solid #eef2f6;
            padding-bottom: 12px;
            margin-top: 32px;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        /* Custom Form Inputs */
        .form-label-custom {
            font-weight: 600;
            color: #475569;
            font-size: 1.3rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.2s;
            width: 100%;
            background-color: #fff;
            color: #1e293b;
        }

        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
            outline: none;
        }

        .form-control[readonly], .bg-light {
            background-color: #f8fafc;
            color: #64748b;
        }

        .row-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Checkpoint Cards */
        .checkpoint-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .checkpoint-card {
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 24px 16px;
            height: 100%;
            transition: all 0.2s;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .checkpoint-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.06);
        }

        .checkpoint-card.utama {
            border-top: 5px solid #ef4444; /* red */
        }

        .checkpoint-card.tambahan {
            border-top: 5px solid #f59e0b; /* yellow */
        }

        .checkpoint-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 24px;
            font-size: 1.1rem;
            line-height: 1.4;
        }

        /* Checkbox Styling */
        .contain {
            display: block; 
            position: relative; 
            cursor: pointer; 
            user-select: none; 
            height: 48px;
            margin-top: auto;
        }
        
        .contain input { 
            position: absolute; 
            opacity: 0; 
            cursor: pointer; 
            height: 0; 
            width: 0;
        }

        .checkmark {
            position: absolute; 
            top: 0; 
            left: 50%; 
            transform: translateX(-50%);
            height: 48px; 
            width: 48px; 
            background-color: #ef4444; 
            border-radius: 12px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .contain input:checked ~ .checkmark { 
            background-color: #10b981; 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .checkmark:after {
            content: ""; 
            position: absolute; 
            display: none;
            left: 18px; 
            top: 8px; 
            width: 12px; 
            height: 24px;
            border: solid white; 
            border-width: 0 4px 4px 0; 
            transform: rotate(45deg);
        }

        .contain input:checked ~ .checkmark:after { 
            display: block; 
            animation: popIn 0.3s ease forwards; 
        }

        @keyframes popIn {
            0% { opacity: 0; transform: rotate(45deg) scale(0.5); }
            100% { opacity: 1; transform: rotate(45deg) scale(1); }
        }

        /* Info Banner */
        .alert-info-custom {
            background-color: #fef9e6;
            border-left: 4px solid #f59e0b;
            padding: 14px 20px;
            border-radius: 12px;
            color: #92400e;
            font-size: 1.3rem;
            margin: 16px 0;
            font-weight: 500;
        }

        /* Submit Button */
        .btn-gate2 {
            background: linear-gradient(95deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 16px 24px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1.25rem;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 10px rgba(16,185,129,0.2);
            width: 100%;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-gate2:hover {
            background: linear-gradient(95deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16,185,129,0.3);
        }

        .btn-gate2:active {
            transform: translateY(1px);
        }

        .text-uppercase { text-transform: uppercase; }
        .text-white { color: white !important; }
        .fw-bold { font-weight: 700 !important; }
        .text-center { text-align: center; }
        hr.divider { border: 0; height: 1px; background: #e2e8f0; margin: 40px 0; }
        
        @media (max-width: 640px) {
            .form-wrapper { padding: 20px 16px; }
            .header-card { padding: 18px 20px; }
            .header-card h4 { font-size: 1.3rem; }
            .btn-gate2 { font-size: 1.1rem; padding: 14px 20px; }
        }
    </style>

<div class="main-container">
    <div class="header-card">
        <h4>Gate 2 Inspection Form</h4>
    </div>

    <div class="form-wrapper">
        <form method="post" action="<?=route('lanjut_gate2')?>">
            <input type="hidden" id="code" name="code" value="<?= htmlspecialchars($kode) ?>">

            <h4 class="section-title">Informasi Kendaraan & Pengemudi</h4>

            <div class="row-grid">
                <div>
                    <label class="form-label-custom">No Polisi</label>
                    <input type="text" class="form-control text-uppercase bg-light" 
                           name="nopol" value="<?= htmlspecialchars($nopol) ?>" readonly>
                </div>
                <div>
                    <label class="form-label-custom">Tanggal & Jam Pemeriksaan</label>
                    <input type="text" class="form-control bg-light" 
                           value="<?= htmlspecialchars($data['tgl_pemeriksaan'] . ' ' . $data['jam_pemeriksaan']) ?>" readonly>
                    <input type="hidden" name="tgl" value="<?= htmlspecialchars($data['tgl_pemeriksaan']) ?>">
                    <input type="hidden" name="jam" value="<?= htmlspecialchars($data['jam_pemeriksaan']) ?>">
                </div>
            </div>

            <div class="row-grid">
                <div>
                    <label class="form-label-custom">Nama Sopir</label>
                    <input type="text" class="form-control text-uppercase" 
                           name="nama_sopir" value="<?= htmlspecialchars($nama_sopir) ?>" >
                </div>
                <div>
                    <label class="form-label-custom">Nama Transporter</label>
                    <input type="text" class="form-control text-uppercase bg-light" 
                           name="nama_transporter" value="<?= htmlspecialchars($data['nama_transporter']) ?>" readonly>
                </div>
            </div>

            <div class="row-grid">
                <div>
                    <label class="form-label-custom">Jenis Kendaraan</label>
                    <input type="text" class="form-control" 
                           name="tipe_truck" value="<?= htmlspecialchars($data['jenis_kendaraan']) ?>" >
                </div>
                <div>
                    <label class="form-label-custom">Lokasi & Petugas</label>
                    <input type="text" class="form-control bg-light" 
                           name="lokasi" value="<?= htmlspecialchars($data['lokasi_pemeriksaan'] . ' - ' . User::$username) ?>" readonly>
                    <input type="hidden" name="petugas" value="<?= htmlspecialchars(User::$username) ?>">
                </div>
            </div>

            <?php if ($muat == 'FG'): ?>
                <hr class="divider">
                <h4 class="section-title" style="color: #059669; border-bottom-color: #d1fae5;">Kelengkapan FG (Finished Goods)</h4>

                <div class="row-grid">
                    <div>
                        <label class="form-label-custom">ID Shipment</label>
                        <input type="text" class="form-control text-uppercase bg-light" 
                            name="kode_kirim" value="<?= htmlspecialchars($data['id_barang']) ?>" readonly>
                    </div>
                    
                    <!-- Usia Sopir - editable number input -->
                    <div>
                        <label class="form-label-custom">Usia Sopir (tahun)</label>
                        <input type="number" id="usia_sopir" class="form-control" 
                            name="usia" value="<?= htmlspecialchars($umul) ?>" 
                            min="17" max="70" step="1" 
                            placeholder="Masukkan usia sopir">
                        <input type="hidden" id="status_usia" name="status_usia" value="<?= htmlspecialchars($status_usia) ?>">
                    </div>
                    
                    <div>
                        <label class="form-label-custom">Jenis SIM</label>
                        <input type="text" class="form-control" name="tipe_sim" 
                            value="<?= htmlspecialchars($tipe_sim) ?>" placeholder="Contoh: B1 Umum">
                    </div>
                </div>

                <div class="row-grid">
                    <!-- Status SIM - Dropdown -->
                    <div>
                        <label class="form-label-custom">Status SIM</label>
                        <select id="status_sim_select" class="form-control">
                            <option value="valid" <?= ($status_sim == 'valid') ? 'selected' : '' ?>>Valid</option>
                            <option value="tidak_valid" <?= ($status_sim == 'tidak_valid') ? 'selected' : '' ?>>Tidak Valid</option>
                        </select>
                        <input type="hidden" id="expired_sim" name="expired_sim" value="<?= htmlspecialchars($valid) ?>">
                        <input type="hidden" id="status_sim_hidden" name="status_sim" value="<?= htmlspecialchars($status_sim) ?>">
                    </div>
                    
                    <!-- Status ID DDT - Dropdown -->
                    <div>
                        <label class="form-label-custom">Status ID DDT</label>
                        <select id="status_ddt_select" class="form-control">
                            <option value="valid" <?= ($status_ddt == 'valid') ? 'selected' : '' ?>>Valid</option>
                            <option value="tidak_valid" <?= ($status_ddt == 'tidak_valid') ? 'selected' : '' ?>>Tidak Valid</option>
                        </select>
                        <input type="hidden" id="expired_ddt" name="expired_ddt" value="<?= htmlspecialchars($valid_ddt) ?>">
                        <input type="hidden" id="status_ddt_hidden" name="status_ddt" value="<?= htmlspecialchars($status_ddt) ?>">
                    </div>
                </div>

                <!-- Optional: Display area for SIM/DDT expiry dates (if needed) -->
                <div class="row-grid" style="display: none;">
                    <!-- You can keep original expiry date inputs if required -->
                </div>

                <script>
                    // =============================================
                    // USIA SOPIR -> update status_usia hidden input
                    // =============================================
                    function updateUsiaStatus() {
                        const usiaInput = document.getElementById('usia_sopir');
                        const statusUsiaHidden = document.getElementById('status_usia');
                        
                        if (!usiaInput || !statusUsiaHidden) return;
                        
                        let usia = parseInt(usiaInput.value, 10);
                        // Default if empty or not a number
                        if (isNaN(usia)) usia = 0;
                        
                        // Example rule: age >= 21 => valid, else tidak valid
                        // Adjust the threshold (21) according to your company policy
                        const isValid = usia >= 21;
                        const statusValue = isValid ? 'valid' : 'tidak_valid';
                        
                        // Update hidden field
                        statusUsiaHidden.value = statusValue;
                        
                        // Optionally change background color of usia field to give visual feedback
                        if (isValid) {
                            usiaInput.style.backgroundColor = '#d1fae5'; // light green
                            usiaInput.style.borderLeft = '4px solid #10b981';
                        } else {
                            usiaInput.style.backgroundColor = '#fee2e2'; // light red
                            usiaInput.style.borderLeft = '4px solid #ef4444';
                        }
                    }

                    // =============================================
                    // STATUS SIM & STATUS DDT -> update hidden fields + visual
                    // =============================================
                    function updateSimStatus() {
                        const selectSim = document.getElementById('status_sim_select');
                        const hiddenSim = document.getElementById('status_sim_hidden');
                        const expiredSimHidden = document.getElementById('expired_sim');
                        
                        if (!selectSim || !hiddenSim) return;
                        
                        const selectedValue = selectSim.value; // 'valid' or 'tidak_valid'
                        hiddenSim.value = selectedValue;
                        
                        // Update the expired_sim hidden accordingly (if needed by backend)
                        // Example: set expired_sim to 'Aktif' for valid, 'Kadaluarsa' for tidak valid
                        if (expiredSimHidden) {
                            expiredSimHidden.value = (selectedValue === 'valid') ? 'Aktif' : 'Kadaluarsa';
                        }
                        
                        // Change background color of the select for visual feedback
                        if (selectedValue === 'valid') {
                            selectSim.style.backgroundColor = '#d1fae5';
                            selectSim.style.borderLeft = '4px solid #10b981';
                        } else {
                            selectSim.style.backgroundColor = '#fee2e2';
                            selectSim.style.borderLeft = '4px solid #ef4444';
                        }
                    }

                    function updateDdtStatus() {
                        const selectDdt = document.getElementById('status_ddt_select');
                        const hiddenDdt = document.getElementById('status_ddt_hidden');
                        const expiredDdtHidden = document.getElementById('expired_ddt');
                        
                        if (!selectDdt || !hiddenDdt) return;
                        
                        const selectedValue = selectDdt.value;
                        hiddenDdt.value = selectedValue;
                        
                        if (expiredDdtHidden) {
                            expiredDdtHidden.value = (selectedValue === 'valid') ? 'Aktif' : 'Kadaluarsa';
                        }
                        
                        if (selectedValue === 'valid') {
                            selectDdt.style.backgroundColor = '#d1fae5';
                            selectDdt.style.borderLeft = '4px solid #10b981';
                        } else {
                            selectDdt.style.backgroundColor = '#fee2e2';
                            selectDdt.style.borderLeft = '4px solid #ef4444';
                        }
                    }

                    // =============================================
                    // Attach event listeners when DOM is ready
                    // =============================================
                    document.addEventListener('DOMContentLoaded', function() {
                        // Usia input
                        const usiaField = document.getElementById('usia_sopir');
                        if (usiaField) {
                            usiaField.addEventListener('input', updateUsiaStatus);
                            // Initial run
                            updateUsiaStatus();
                        }
                        
                        // SIM select
                        const simSelect = document.getElementById('status_sim_select');
                        if (simSelect) {
                            simSelect.addEventListener('change', updateSimStatus);
                            updateSimStatus(); // set initial state
                        }
                        
                        // DDT select
                        const ddtSelect = document.getElementById('status_ddt_select');
                        if (ddtSelect) {
                            ddtSelect.addEventListener('change', updateDdtStatus);
                            updateDdtStatus();
                        }
                    });

                    // Your existing tambahan() function remains unchanged
                    // ... (keep your original tambahan() code here)

                    </script>

                <?php endif; ?>

            <h4 class="section-title" style="color: #b91c1c; border-bottom-color: #fee2e2; margin-top: 40px;">Kelengkapan Utama</h4>
            <div class="checkpoint-grid">
                <?php foreach ($utama_rows as $row): ?>
                <div class="checkpoint-card utama">
                    <p class="checkpoint-title"><?= htmlspecialchars($row['param_name']) ?></p>
                    <label class="contain w-100">
                        <input type="checkbox" id="<?= htmlspecialchars($row['idgreen']) ?>" 
                               name="param_<?= htmlspecialchars($row['namee']) ?>" value="1" onchange="tambahan()">
                        <span class="checkmark"></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <h4 class="section-title" style="color: #b45309; border-bottom-color: #fef3c7; margin-top: 40px;">Kelengkapan Tambahan</h4>
            
            <div class="alert-info-custom text-center">
                Jika ada point <strong>Kelengkapan Tambahan</strong> tidak terpenuhi maka segera dilakukan tindakan perbaikan 
                sesuai batas waktu yang telah ditentukan.
            </div>

            <div class="checkpoint-grid">
                <?php foreach ($tambahan_rows as $row): ?>
                <div class="checkpoint-card tambahan">
                    <p class="checkpoint-title"><?= htmlspecialchars($row['param_name']) ?></p>
                    <label class="contain w-100">
                        <input type="checkbox" id="<?= htmlspecialchars($row['idgreen']) ?>" 
                               name="param_<?= htmlspecialchars($row['namee']) ?>" value="1" onchange="tambahan()">
                        <span class="checkmark"></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <hr class="divider">
            <h4 class="section-title">Kesimpulan Pemeriksaan</h4>
            
            <div class="row-grid" style="grid-template-columns: 1fr;">
                <div>
                    <label class="form-label-custom">Hasil Pemeriksaan:</label>
                    <input type="text" class="form-control text-white fw-bold text-center" 
                           id="hasil" name="hasil" style="background: #0ea5e9; font-size: 1.15rem; border: none;" readonly>
                </div>

                <div>
                    <label class="form-label-custom">Komentar Kerusakan:</label>
                    <input type="text" class="form-control" id="komentar" name="komentar" 
                           required placeholder="Tuliskan komentar...">
                </div>

                <div>
                    <label class="form-label-custom">Tindakan Perbaikan:</label>
                    <input type="text" class="form-control" id="tindakan" name="tindakan" 
                           required placeholder="Tuliskan tindakan perbaikan...">
                </div>
            </div>

            <div>
                <button type="submit" class="btn-gate2">
                    Simpan Data Gate 2
                </button>
            </div>

        </form>
    </div>
</div>
<script>
function tambahan() {
    // 1. Grab all checkboxes based on their parent card's class
    const utamaCheckboxes = document.querySelectorAll('.checkpoint-card.utama input[type="checkbox"]');
    const tambahanCheckboxes = document.querySelectorAll('.checkpoint-card.tambahan input[type="checkbox"]');
    const hasilInput = document.getElementById('hasil');

    // If there are no checkboxes (e.g., failed to load), exit early
    if (!hasilInput || utamaCheckboxes.length === 0) return;

    // 2. Check if ALL Utama are fulfilled
    let isUtamaAllChecked = true;
    for (let i = 0; i < utamaCheckboxes.length; i++) {
        if (!utamaCheckboxes[i].checked) {
            isUtamaAllChecked = false;
            break;
        }
    }

    // 3. Check if ALL Tambahan are fulfilled
    let isTambahanAllChecked = true;
    for (let i = 0; i < tambahanCheckboxes.length; i++) {
        if (!tambahanCheckboxes[i].checked) {
            isTambahanAllChecked = false;
            break;
        }
    }

    // 4. Apply the logic and update the input field
    if (!isUtamaAllChecked) {
        // If ANY Utama is missing
        hasilInput.value = "Tidak Layak di Operasikan (Stiker Merah)";
        hasilInput.style.backgroundColor = "#ef4444"; // Tailwind Red-500
        hasilInput.style.color = "white";
    } else {
        // All Utama are checked, now look at Tambahan
        if (isTambahanAllChecked) {
            // All Utama AND All Tambahan checked
            hasilInput.value = "Layak di Operasikan (Stiker Hijau)";
            hasilInput.style.backgroundColor = "#10b981"; // Tailwind Emerald-500
            hasilInput.style.color = "white";
        } else {
            // All Utama checked, but ANY Tambahan is missing
            hasilInput.value = "Perlu Perbaikan (Stiker Kuning)";
            hasilInput.style.backgroundColor = "#f59e0b"; // Tailwind Amber-500
            hasilInput.style.color = "black";
        }
    }
}

// 5. Run the function once when the page loads to set the initial state
document.addEventListener('DOMContentLoaded', function() {
    tambahan();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkpoint cards
    const cards = document.querySelectorAll('.checkpoint-card');
    
    cards.forEach(card => {
        card.addEventListener('click', function(event) {
            // If the click came from a label, checkbox, or custom span inside label, let the default behavior happen
            if (event.target.closest('label, input')) {
                return; // Don't interfere with label's natural toggle
            }
            
            // Find the checkbox inside this card
            const checkbox = this.querySelector('input[type="checkbox"]');
            if (checkbox) {
                // Toggle the checkbox state
                checkbox.checked = !checkbox.checked;
                // Manually trigger the change event to call tambahan()
                const changeEvent = new Event('change', { bubbles: true });
                checkbox.dispatchEvent(changeEvent);
            }
        });
    });
});
</script>