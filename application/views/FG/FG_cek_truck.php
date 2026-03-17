<div class="body-wrap-with-navbar">

<?php
/** * 1. INITIALIZATION & DATA FETCHING 
 */
$muat        = $_POST['muat'] ?? '';
$nopol       = str_replace(' ', '', $_POST['nopol'] ?? '');
$id_shipment = $_POST['id_shipment'] ?? '';
$username    = User::$username;
$plant_name  = User::$plant_name;
$plant_id    = User::$plantid;

$nama_sopir = 'Trial Driver';
$seq_visitor = 'seq_trial';
$id_barang  = 'Muat Trial';

// Fetch Driver Info if not trial
if ($id_shipment !== 'trial') {
    $sql_nop = mysqli_query($con_3, "SELECT * FROM tbl_visit WHERE no_pol='$nopol' ORDER BY tanggal_datang DESC LIMIT 1");
    if (mysqli_num_rows($sql_nop) == 0) {
        echo "<script>alert('No Pol belum di input di e_Visitor!'); window.location='FG_cek_nopol';</script>";
        exit;
    }
    $row_nop    = mysqli_fetch_assoc($sql_nop);
    $nama_sopir  = $row_nop['nama_visitor'];
    $seq_visitor = $row_nop['seq_visitor'];
    $id_barang   = $row_nop['id_barang'];
}

/** * 2. VALIDATION LOGIC (License & Age)
 */
if ($seq_visitor !== 'seq_trial') {
    $row_visitor = mysqli_fetch_assoc(mysqli_query($con_3, "SELECT * FROM tbm_visitor WHERE seq='$seq_visitor'"));
    $tgl_lahir   = $row_visitor['tanggal_lahir'];
    $valid_sim   = $row_visitor['valid_id_date'];
    $tipe_sim    = $row_visitor['tipe_id'];
    $valid_ddt   = $row_visitor['valid_ddt_date'];
} else {
    $tgl_lahir = '1987-03-17'; $valid_sim = '2022-03-17'; $tipe_sim = 'SIM B2'; $valid_ddt = '2020-12-31';
}

$dt_lahir   = new DateTime($tgl_lahir);
$dt_sim     = new DateTime($valid_sim);
$dt_ddt     = new DateTime($valid_ddt);
$dt_today   = new DateTime();

// Age Calculation
$umur = $dt_today->diff($dt_lahir)->y;
if ($umur <= 55) { $color_usia = 'green'; $status_usia = 'Low Risk'; $text_usia = 'white'; }
elseif ($umur <= 60) { $color_usia = 'yellow'; $status_usia = 'Medium Risk'; $text_usia = 'black'; }
else { $color_usia = 'red'; $status_usia = 'High Risk'; $text_usia = 'white'; }

// SIM & DDT Expiry Check
$is_sim_expired = $dt_today > $dt_sim;
$is_ddt_expired = $dt_today > $dt_ddt;

$status_sim = $is_sim_expired ? 'SIM Sudah Kadaluwarsa' : 'SIM Masih Berlaku';
$color_sim  = $is_sim_expired ? 'red' : 'green';

$status_ddt = $is_ddt_expired ? 'ID DDT Sudah Kadaluwarsa' : 'ID DDT Masih Berlaku';
$color_ddt  = $is_ddt_expired ? 'red' : 'green';

// Constants
$current_date = date("Y-m-d");
$current_time = date("H:i:s");
$idref        = time(); 
$seq          = 1;
?>

<style type="text/css">
    body, html { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; }
    .body-wrap-with-navbar {
        display: flex; justify-content: center; align-items: center;
        min-height: 100vh; background-color: #f0f2f5; padding: 20px;
    }
    .main-card {
        background: white; padding: 30px; border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1); width: 100%; max-width: 700px;
    }
    .form-label { font-weight: 600; color: #444; font-size: 0.9rem; margin-bottom: 5px; display: block; }
    .form-control-static { 
        padding: 10px; border-radius: 6px; border: 1px solid #ddd; 
        background-color: #f8f9fa; margin-bottom: 15px; width: 100%; box-sizing: border-box;
    }
    .grid-row { display: flex; gap: 15px; margin-bottom: 5px; }
    .grid-col { flex: 1; }
    .badge-status { 
        padding: 10px; border-radius: 6px; font-weight: bold; text-align: center; 
        color: white; margin-bottom: 15px; border: none; width: 100%;
    }
    .btn-submit {
        background-color: #28a745; color: white; padding: 15px; border: none;
        border-radius: 8px; width: 100%; font-size: 1.1rem; font-weight: bold;
        cursor: pointer; transition: 0.3s; margin-top: 10px;
    }
    .btn-submit:hover { background-color: #218838; }
    hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
</style>

<div class="main-card">
    <h4 style="margin-top:0; color: #333;">Checklist Pemeriksaan</h4>
    <form method="post" action="N_gate1">
        
        <div class="grid-row">
            <div class="grid-col">
                <label class="form-label">Nama Supplier</label>
                <select class="form-control-static" name="supplier" required>
                    <option value="">-- Pilih --</option>
                    <?php
                    $res = mysqli_query($con, "SELECT nama_supplier FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' GROUP BY nama_supplier");
                    while($r = mysqli_fetch_assoc($res)) echo "<option value='{$r['nama_supplier']}'>{$r['nama_supplier']}</option>";
                    ?>
                </select>
            </div>
            <div class="grid-col">
                <label class="form-label">Nama Transporter</label>
                <select class="form-control-static" name="transporter" required>
                    <option value="">-- Pilih --</option>
                    <?php
                    $sql_t = ($plant_id=='90A8') ? "SELECT planned_transporter_name as n FROM tbl_otm_upload GROUP BY n" : "SELECT nama_transporter as n FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' GROUP BY n";
                    $res_t = mysqli_query(($plant_id=='90A8' ? $con_140 : $con), $sql_t);
                    while($r = mysqli_fetch_assoc($res_t)) echo "<option value='{$r['n']}'>{$r['n']}</option>";
                    ?>
                </select>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col">
                <label class="form-label">No Polisi</label>
                <input type="text" class="form-control-static" name="nopol" value="<?php echo $nopol; ?>" readonly>
            </div>
            <div class="grid-col">
                <label class="form-label">Nama Sopir</label>
                <input type="text" class="form-control-static" name="driver" value="<?php echo $nama_sopir; ?>">
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-col">
                <label class="form-label">Usia Driver</label>
                <div class="badge-status" style="background-color: <?php echo $color_usia; ?>; color: <?php echo $text_usia; ?>;">
                    <?php echo $umur; ?> Tahun (<?php echo $status_usia; ?>)
                </div>
            </div>
            <div class="grid-col">
                <label class="form-label">Jenis SIM</label>
                <input type="text" class="form-control-static" value="<?php echo $tipe_sim; ?>" readonly>
            </div>
        </div>

        <label class="form-label">Masa Berlaku SIM</label>
        <div class="badge-status" style="background-color: <?php echo $color_sim; ?>;">
            <?php echo $status_sim; ?> | Exp: <?php echo $valid_sim; ?>
        </div>

        <label class="form-label">Masa Berlaku ID DDT</label>
        <div class="badge-status" style="background-color: <?php echo $color_ddt; ?>;">
            <?php echo $status_ddt; ?> | Exp: <?php echo $valid_ddt; ?>
        </div>

        <div class="grid-row">
            <div class="grid-col">
                <label class="form-label">Waktu</label>
                <input type="text" class="form-control-static" value="<?php echo $current_time . ' / ' . $current_date; ?>" readonly>
            </div>
            <div class="grid-col">
                <label class="form-label">Lokasi</label>
                <input type="text" class="form-control-static" value="<?php echo $plant_name; ?>" readonly>
            </div>
        </div>

        <input type="hidden" name="usia" value="<?php echo $umur; ?>">
        <input type="hidden" name="status_usia" value="<?php echo $status_usia; ?>">
        <input type="hidden" name="expired_sim" value="<?php echo $valid_sim; ?>">
        <input type="hidden" name="status_sim" value="<?php echo $status_sim; ?>">
        <input type="hidden" name="expired_ddt" value="<?php echo $valid_ddt; ?>">
        <input type="hidden" name="status_ddt" value="<?php echo $status_ddt; ?>">
        <input type="hidden" name="jam" value="<?php echo $current_time; ?>">
        <input type="hidden" name="tgl" value="<?php echo $current_date; ?>">
        <input type="hidden" name="petugas" value="<?php echo $username; ?>">
        <input type="hidden" name="seq" value="<?php echo $seq; ?>">
        <input type="hidden" name="idref" value="<?php echo $idref; ?>">
        <input type="hidden" name="muat" value="<?php echo $muat; ?>">
        <input type="hidden" name="plant_id" value="<?php echo $plant_id; ?>">
        <input type="hidden" name="id_barang" value="<?php echo $id_shipment; ?>">

        <button type="submit" class="btn-submit">Lanjutkan ke Checklist</button>
    </form>
</div>

</div>
