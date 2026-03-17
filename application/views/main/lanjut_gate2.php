<?php
/**
 * Gate 2 Inspection Processing Script
 * 
 * This script processes the Gate 2 inspection results, updates the database,
 * and determines the final safety status of the truck.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include 'application/assets/Table.php';
// Note: $con and $con2 database connections are assumed to be already available.

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$kode           = $_POST['kode'] ?? '';
$petugas        = $_POST['petugas'] ?? '';
$tujuan         = $_POST['tujuan'] ?? '';
$nopol          = $_POST['nopol'] ?? '';
$nama_transporter = $_POST['nama_transporter'] ?? '';
$nama_sopir     = $_POST['nama_sopir'] ?? '';
$tipe_truck     = $_POST['tipe_truck'] ?? '';
$tahun          = $_POST['tahun'] ?? '';
$tgl            = $_POST['tgl'] ?? '';
$jam            = $_POST['jam'] ?? '';
$lokasi         = $_POST['lokasi'] ?? '';
$komentar       = $_POST['komentar'] ?? '';
$tindakan       = $_POST['tindakan'] ?? '';
$petugas_gate2  = $_POST['petugas'] ?? ''; // Note: same as $petugas?
$kode_kirim     = $_POST['kode_kirim'] ?? '';

$utama = array();
$tamb  = array();
for ($i = 1; $i <= 20; $i++) {
    $utama[$i] = $_POST['utama'.$i] ?? '';
    $tamb[$i]  = $_POST['tamb'.$i] ?? '';
}

$hasil_periksa = $_POST['hasil'] ?? '';

$warna_hasil = '';
$status      = '';
$komentar    = '';

// ============================================================================
// DATABASE QUERIES - DATA PROCESSING
// ============================================================================

// ----------------------------------------------------------------------------
// Determine inspection result and set status/comment
// ----------------------------------------------------------------------------
$petugas_pemeriksa = $petugas_gate2;

if ($hasil_periksa == "Layak di Operasikan (Stiker Hijau)") {
    $warna_hasil = "green";
    $status      = 'PASS GATEIN';
    $komentar    = 'SAFETY G1 G2 OK';
    
    // Optional updates for other systems (currently commented)
    // $query2 = "UPDATE tbl_pengiriman SET status='PASS GATEIN', keterangan='SAFETY G1 G2 OK', 
    //            security_gate_in='$petugas_gate2', tgl_gate_in='$datetime' 
    //            WHERE kode_pengiriman='$kode_kirim'";
    // mysqli_query($con2, $query2);
    //
    // $str = "UPDATE tbl_item_pengiriman SET `keterangan`='PASS GATEIN' 
    //         WHERE pengiriman_id= '$kode_kirim'";
    // mysqli_query($con2, $str);
}

if ($hasil_periksa == "Tidak Layak di Operasikan (Stiker Merah)") {
    $warna_hasil = "red";
    $status      = 'NOT PASS GATEIN';
    $komentar    = 'TIDAK LAYAK DI OPERASIKAN';
    
    // $query2 = "UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', 
    //            keterangan='TIDAK LAYAK DI OPERASIKAN', 
    //            security_gate_in='$petugas_gate2', tgl_gate_in='$datetime' 
    //            WHERE kode_pengiriman='$kode_kirim'";
    // mysqli_query($con2, $query2);
}

if ($hasil_periksa == "Perlu Perbaikan (Stiker Kuning)") {
    $warna_hasil = "yellow";
    $status      = 'NOT PASS GATEIN';
    $komentar    = 'PERLU PERBAIKAN';
    
    // $query2 = "UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', 
    //            keterangan='PERLU PERBAIKAN', 
    //            security_gate_in='$petugas_gate2', tgl_gate_in='$datetime'  
    //            WHERE kode_pengiriman='$kode_kirim'";
    // mysqli_query($con2, $query2);
}

if ($hasil_periksa == "Di Tolak di Pos 1") {
    $warna_hasil = "black";
    $status      = 'NOT PASS GATEIN';
    $komentar    = 'SEHARUSNYA DITOLAK';
    
    // $query2 = "UPDATE tbl_pengiriman SET status='NOT PASS GATEIN', 
    //            keterangan='SEHARUSNYA DITOLAK', 
    //            security_gate_in='$petugas_gate2', tgl_gate_in='$datetime' 
    //            WHERE kode_pengiriman='$kode_kirim'";
    // mysqli_query($con2, $query2);
}

// ----------------------------------------------------------------------------
// Validate required inspection result
// ----------------------------------------------------------------------------
if (empty($hasil_periksa)) {
    echo "<script>alert('Anda belum Ceklist');history.go(-1)</script>";
    exit;
}

// ----------------------------------------------------------------------------
// Update main inspection record (tb_ceklist) with all utama & tambahan values
// ----------------------------------------------------------------------------
$query = "
    UPDATE tb_ceklist 
    SET 
        utama5    = '{$utama[5]}',
        utama6    = '{$utama[6]}',
        utama7    = '{$utama[7]}',
        utama8    = '{$utama[8]}',
        utama9    = '{$utama[9]}',
        utama10   = '{$utama[10]}',
        utama11   = '{$utama[11]}',
        utama12   = '{$utama[12]}',
        utama13   = '{$utama[13]}',
        utama14   = '{$utama[14]}',
        utama15   = '{$utama[15]}',
        utama16   = '{$utama[16]}',
        utama17   = '{$utama[17]}',
        utama18   = '{$utama[18]}',
        utama19   = '{$utama[19]}',
        utama20   = '{$utama[20]}',
        tambahan1 = '{$tamb[1]}',
        tambahan2 = '{$tamb[2]}',
        tambahan3 = '{$tamb[3]}',
        tambahan4 = '{$tamb[4]}',
        tambahan5 = '{$tamb[5]}',
        tambahan6 = '{$tamb[6]}',
        tambahan7 = '{$tamb[7]}',
        tambahan8 = '{$tamb[8]}',
        tambahan9 = '{$tamb[9]}',
        tambahan10= '{$tamb[10]}',
        tambahan11= '{$tamb[11]}',
        tambahan12= '{$tamb[12]}',
        tambahan13= '{$tamb[13]}',
        tambahan14= '{$tamb[14]}',
        tambahan15= '{$tamb[15]}',
        tambahan16= '{$tamb[16]}',
        tambahan17= '{$tamb[17]}',
        tambahan18= '{$tamb[18]}',
        tambahan19= '{$tamb[19]}',
        tambahan20= '{$tamb[20]}',
        hasil_pemeriksaan = '$hasil_periksa',
        warna             = '$warna_hasil',
        tindakan_perbaikan= '$tindakan',
        komentar_kerusakan= '$komentar',
        petugas_gate2     = '$petugas_gate2',
        status_gate1      = '0',
        list_gate1        = '0',
        tujuan_kirim      = '$tujuan',
        nopol             = '$nopol',
        nama_transporter  = '$nama_transporter',
        nama_sopir        = '$nama_sopir'
    WHERE no = '$kode'
";

mysqli_query($con, $query);

// ----------------------------------------------------------------------------
// Additional update: if all utama and tambahan are 1, set result to 'Layak di Operasikan (Stiker Hijau)'
// ----------------------------------------------------------------------------
$str = "
    UPDATE tb_ceklist 
    SET hasil_pemeriksaan = 'Layak di Operasikan (Stiker Hijau)' 
    WHERE 
        utama1 = 1 AND utama2 = 1 AND utama3 = 1 AND utama4 = 1 AND 
        utama5 = 1 AND utama6 = 1 AND utama7 = 1 AND utama8 = 1 AND 
        utama9 = 1 AND utama10 = 1 AND utama11 = 1 AND utama12 = 1 AND 
        utama13 = 1 AND utama14 = 1 AND 
        tambahan1 = 1 AND tambahan2 = 1 AND tambahan3 = 1 AND tambahan4 = 1 AND 
        tambahan5 = 1 AND tambahan6 = 1 AND tambahan7 = 1 AND tambahan8 = 1 AND 
        tambahan9 = 1 AND tambahan10 = 1 AND 
        hasil_pemeriksaan = 'Tidak Layak di Operasikan (Stiker Merah)' 
        AND no = '$kode'
";

mysqli_query($con, $str);

// ----------------------------------------------------------------------------
// Close database connections
// ----------------------------------------------------------------------------
mysqli_close($con);
if (isset($con2)) {
    mysqli_close($con2);
}

// ============================================================================
// REDIRECT & NOTIFICATION
// ============================================================================
echo "<script>alert('Berhasil di Simpan');</script>";
header("location: " . route("db_waiting"));
exit;
?>