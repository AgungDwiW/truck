<?php
// Initialize variables cleanly
$idref       = $_POST['idref'] ?? '';
$nopol       = $_POST['nopol'] ?? '';
$lokasi      = User::$plant_name;
$kode_kirim  = $_POST['kode_kirim'] ?? '';
$muat        = $_POST['muat'] ?? '';
$driver      = $_POST['driver'] ?? '';
$supplier    = $_POST['supplier'] ?? '';
$transporter = $_POST['transporter'] ?? '';
$usia        = !empty($_POST['usia']) ? $_POST['usia'] : 0;
$tipe_sim    = $_POST['tipe_sim'] ?? '';
$expired_sim = !empty($_POST['expired_sim']) ? $_POST['expired_sim'] : '2999-12-30';
$expired_ddt = !empty($_POST['expired_ddt']) ? $_POST['expired_ddt'] : '2999-12-30';
$status_sim  = $_POST['status_sim'] ?? '';
$status_ddt  = $_POST['status_ddt'] ?? '';
$status_usia = $_POST['status_usia'] ?? '';
$id_barang   = $_POST['id_barang'] ?? '';
$tipe_truck  = '';

if ($muat == 'FG') {
    $seq      = $_POST['seq'] ?? '';
    $supplier = $_POST['supplier'] ?? '';
    $driver   = $_POST['driver'] ?? '';
    $jam      = $_POST['jam'] ?? '';
    $date     = $_POST['tgl'] ?? '';
    $plant_id = $_POST['plant_id'] ?? '';  

    $query_truck = "SELECT jenis_truck FROM tbm_tempat_muat WHERE id_tempat_muat='$plant_id' AND nama_supplier='$supplier' AND nama_transporter='$transporter' GROUP BY nama_transporter LIMIT 1";
    $cari_tipe_truck = mysqli_query($con, $query_truck);
    
    if ($row = mysqli_fetch_assoc($cari_tipe_truck)){
        $tipe_truck = $row['jenis_truck'];
    }

    $username = User::$username;
    $query_insert = "INSERT INTO tb_ceklist SET seq='$seq', idref='$idref', petugas_pemeriksa='$username', nopol='$nopol', nama_supplier='$supplier', nama_transporter='$transporter', jenis_kendaraan='$tipe_truck', plant_id='$plant_id', plant_name='$lokasi', nama_sopir='$driver', tgl_pemeriksaan='$date', jam_pemeriksaan='$jam', lokasi_pemeriksaan='$lokasi', muatan='$muat', usia='$usia', jenis_sim='$tipe_sim', expired_date_sim='$expired_sim', expired_date_ddt='$expired_ddt', status_sim='$status_sim', status_ddt='$status_ddt', status_usia='$status_usia', id_barang='$id_barang'";
    
    mysqli_query($con, $query_insert);
}
?>

<style type="text/css">
  body {
    background-color: transparent;
  }
  /* Remove rounded corners from bottom to connect with inputs */
  .header-box { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }
  .input-box { border-top-left-radius: 0; border-top-right-radius: 0; }
</style>

<div class="container-fluid mt-3 mb-5" style="max-width: 800px;">

    <div class="row text-center bg-danger text-white py-2 mb-3 rounded shadow-sm">
        <div class="col">
            <h3 class="m-0 fw-bold" style="font-size: 26px;">Kelengkapan Utama</h3>
        </div>
    </div>

    <?php
    mysqli_query($con,"UPDATE tb_ceklist_utama SET status_temp=1");
    
    // DB Optimization: Fetch user's current checklist status ONCE outside the loop
    $row_utama = [];
    $ceklist_query = mysqli_query($con, "SELECT * FROM tb_ceklist WHERE idref='$idref' LIMIT 1");
    if ($ceklist_query) {
        $row_utama = mysqli_fetch_assoc($ceklist_query);
    }

    $result = mysqli_query($con,"SELECT *, CONCAT(name,no) as namee, CONCAT(ceklist_utama, no,no) as idgreen, CONCAT(ceklist_utama, no,no,no) as idred FROM tb_ceklist_utama WHERE no BETWEEN 0 AND 4;");

    $no = 1;
    $hasil = 'Lanjut Pemeriksaan Gate 2';
    $hasil_color = 'bg-success';

    while($row = mysqli_fetch_assoc($result)) {
        $noo = $no - 1; 
        $hid = ($no > 1) ? '' : 'd-none'; // Better hiding using Bootstrap
        $cek_utama = 1;

        if ($no > 1 && isset($row_utama["utama".$noo])) {
            $cek_utama = $row_utama["utama".$noo];
        }

        if ($cek_utama == 1) { 
            $cek = 'cekgreen.png'; 
        } else { 
            $cek = 'red.png'; 
            $hasil = 'Di Tolak di Pos 1'; 
            $hasil_color = 'bg-danger'; 
        }
    ?>    
        
        <form method="post" action="main?action=N_foto_gate1" class="mb-2 <?php echo $hid; ?>">   
            <div class="row align-items-center rounded shadow-sm mx-0" style="background-color: #212529; color: white;">
                <div class="col-10 py-2 fs-5">
                    <?php echo $row['ceklist_utama']; ?>
                </div>
                <div class="col-2 text-end py-2">  
                    <input type="hidden" name="utama" value="<?php echo $no-1; ?>"> 
                    <input type="hidden" name="idref" value="<?php echo $idref; ?>">
                    <input type="hidden" name="nopol" value="<?php echo $nopol; ?>">
                    <input type="hidden" name="lokasi" value="<?php echo $lokasi; ?>">
                    <input type="hidden" name="ceklist" value="<?php echo $row['ceklist_utama']; ?>">
                    <input type="hidden" name="kode_kirim" value="<?php echo $kode_kirim; ?>">
                    <input type="hidden" name="driver" value="<?php echo $driver; ?>">
                    <input type="hidden" name="supplier" value="<?php echo $supplier; ?>">
                    
                    <button type="submit" class="btn btn-light btn-sm p-1 rounded">
                        <img src="static/css/img/<?php echo $cek; ?>" width="28" height="28">
                    </button>
                </div>
            </div>  
        </form>

    <?php
        $no++;
    } 
    ?>    

    <hr class="my-4">

    <form method="post" action="main?action=simpan_gate1">
        
        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Hasil Pemeriksaan :</label>
            </div>
            <input type="text" class="form-control input-box text-white text-center fw-bold fs-5 py-2 <?php echo $hasil_color; ?> border-info" id="hasil" name="hasil" value="<?php echo $hasil; ?>" readonly>
        </div>

        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Komentar Kerusakan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" id="komentar" name="komentar" required placeholder="Tulis komentar disini...">
        </div>

        <div class="mb-4 shadow-sm" style='margin-bottom:10px'>
            <div class="bg-info text-dark text-center fw-bold py-2 rounded header-box">
                <label class="m-0" style="font-size: 18px;">Tindakan Perbaikan :</label>
            </div>
            <input type="text" class="form-control input-box text-center py-2 border-info" id="tindakan" name="tindakan" required placeholder="Tulis tindakan perbaikan...">
        </div>

        <input type="hidden" name="idref" value="<?php echo $idref; ?>">
        <input type="hidden" name="nopol" value="<?php echo $nopol; ?>">
        <input type="hidden" name="petugas" value="<?php echo $username ?? ''; ?>">
        <input type="hidden" name="lokasi" value="<?php echo $lokasi; ?>">
        <input type="hidden" name="kode_kirim" value="<?php echo $kode_kirim; ?>">
        <input type="hidden" name="driver" value="<?php echo $driver; ?>">
        <input type="hidden" name="supplier" value="<?php echo $supplier; ?>">

        <div class="d-grid gap-2 mt-4" style='width:100%'>
            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style='width:100%'>Simpan Data</button>
        </div>
    </form>
</div>