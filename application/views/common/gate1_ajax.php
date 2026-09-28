<?php
/* ============================================================================
# gate1_ajax — endpoint JSON untuk halaman N_gate1 (dipanggil jQuery AJAX)
# Supaya simpan hasil per item TIDAK perlu pindah halaman.
#
#   op=ok    : tb_ceklist.utamaN = 1   (item OK)
#   op=fail  : simpan foto + temuan  -> tb_foto, tb_ceklist.utamaN = 0
#
# Logika upload & kolom sama dengan views/common/N_upload_fail.php (versi halaman),
# bedanya keluaran JSON dan tipe kolom mengikuti information_schema:
#   tb_ceklist.utamaN  int(10)        -> tanpa kutip
#   tb_foto.*          varchar/text   -> escape + kutip
# ============================================================================ */
header('Content-Type: application/json');

$user   = $_SESSION[APP_NAME]["username"] ?? '';
$op     = (string) ($_POST['op'] ?? '');
$idref  = (string) ($_POST['idref'] ?? '');
$utama  = (int)    ($_POST['utama'] ?? 0);
$nopol  = (string) ($_POST['nopol'] ?? '');
$lokasi = (string) ($_POST['lokasi'] ?? '');
$ccp    = (string) ($_POST['ccp'] ?? '');
$temuan = trim((string) ($_POST['tem'] ?? ''));

$balas = array('ok' => 0, 'op' => $op, 'msg' => '');

if ($idref === '' || $utama < 1 || $utama > 4) {
    $balas['msg'] = 'Data tidak lengkap (idref / utama).';
    echo json_encode($balas);
    exit;
}

$kolom = 'utama' . $utama;              /* int — aman, berasal dari cast integer */
$idref_sql = mysqli_real_escape_string($con, $idref);

/* --- 1. OP = OK ---------------------------------------------------------- */
if ($op === 'ok') {
    mysqli_query($con, "UPDATE tb_ceklist SET $kolom=1 WHERE idref='$idref_sql'");
    $balas['ok'] = 1;
    $balas['status'] = 'OK';
    echo json_encode($balas);
    exit;
}

/* --- 2. OP = FAIL (ada temuan + foto) ------------------------------------ */
if ($op === 'fail') {
    if ($temuan === '') {
        $balas['msg'] = 'Temuan wajib diisi.';
        echo json_encode($balas);
        exit;
    }

    /* Nama file: pakai ekstensi dari file yang dikirim (webcam -> .jpg),
       fallback .png. Nama user disaring supaya aman jadi nama file. */
    $nama_asli = isset($_FILES['file']['name']) ? (string) $_FILES['file']['name'] : '';
    $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    if (!in_array($ekstensi, array('png', 'jpg', 'jpeg'), true)) { $ekstensi = 'png'; }
    $user_aman = preg_replace('/[^A-Za-z0-9_]/', '', (string) $user);
    $nama      = date('ymdhis') . '_' . ($user_aman !== '' ? $user_aman : 'anon') . '.' . $ekstensi;
    $ukuran    = isset($_FILES['file']['size']) ? (int) $_FILES['file']['size'] : 0;
    $file_tmp  = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
    if ($ukuran <= 0 || $ukuran >= 1200000 || $file_tmp === '' || !is_uploaded_file($file_tmp)) {
        $balas['msg'] = 'Tidak ada foto yang di-upload (maks 1 MB).';
        echo json_encode($balas); exit;
    }
    /* __DIR__ = application/views/common -> absolut, tidak bergantung CWD php-fpm
       (dulu relatif 'application/views/...', gagal kalau DocumentRoot bukan root app) */
    $dir_capture = __DIR__ . '/capture/';
    if (!is_dir($dir_capture)) {
        $balas['msg'] = 'Folder capture tidak ada: ' . $dir_capture;
        echo json_encode($balas); exit;
    }
    if (!is_writable($dir_capture)) {
        $balas['msg'] = 'Folder capture tidak bisa ditulis: ' . $dir_capture;
        echo json_encode($balas); exit;
    }
    if (!move_uploaded_file($file_tmp, $dir_capture . $nama)) {
        $balas['msg'] = 'Gagal menyimpan file foto.';
        echo json_encode($balas); exit;
    }

    /* tb_foto.description varchar(100) -> dipotong, jangan sampai error/terpotong diam2 */
    $dipotong = false;
    if (mb_strlen($temuan) > 100) { $temuan = mb_substr($temuan, 0, 100); $dipotong = true; }

    $nama_sql = mysqli_real_escape_string($con, $nama);
    mysqli_query($con, "INSERT INTO upload SET nama_file='$nama_sql'");

    /* tb_foto: idref/item_utama/username/foto_name/description/nopol/petugas/lokasi = varchar,
       utama = varchar(50) -> dikutip juga (bukan int), sesuai information_schema */
    $sql_foto = "INSERT INTO tb_foto SET
        idref       = '" . $idref_sql . "',
        item_utama  = '" . mysqli_real_escape_string($con, $ccp) . "',
        description = '" . mysqli_real_escape_string($con, $temuan) . "',
        petugas     = '" . mysqli_real_escape_string($con, $user) . "',
        foto_name   = '$nama_sql',
        username    = '" . mysqli_real_escape_string($con, $user) . "',
        utama       = '$utama',
        nopol       = '" . mysqli_real_escape_string($con, $nopol) . "',
        lokasi      = '" . mysqli_real_escape_string($con, $lokasi) . "'";
    $ok_foto = mysqli_query($con, $sql_foto);

    if (!$ok_foto) {
        /* jangan tandai item jadi PERIKSA kalau datanya gagal disimpan */
        @unlink(__DIR__ . '/capture/' . $nama);
        $balas['msg'] = 'Gagal menyimpan temuan: ' . mysqli_error($con);
        echo json_encode($balas);
        exit;
    }

    if ($dipotong) { $balas['msg'] = 'Komentar dipotong (maks 100 karakter).'; }

    /* tb_ceklist.utamaN int(10) -> tanpa kutip */
    mysqli_query($con, "UPDATE tb_ceklist SET $kolom=0 WHERE idref='$idref_sql'");

    $balas['ok'] = 1;
    $balas['status'] = 'PERIKSA';
    $balas['foto'] = $nama;
    echo json_encode($balas);
    exit;
}

$balas['msg'] = 'Operasi tidak dikenal.';
echo json_encode($balas);
