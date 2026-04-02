<?php
/**
 * Save Gate 1 Inspection Results
 * 
 * This script saves the Gate 1 inspection results, sends email notifications
 * if the truck is rejected, and updates the database.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include 'application/assets/class.phpmailer.php';

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$idref             = $_POST['idref'] ?? '';
$nopol             = $_POST['nopol'] ?? '';
$driver            = $_POST['driver'] ?? '';
$supplier          = $_POST['supplier'] ?? '';
$lokasi            = $_POST['lokasi'] ?? '';
$hasil             = $_POST['hasil'] ?? '';
$komentar          = $_POST['komentar'] ?? '';
$tindakan          = $_POST['tindakan'] ?? '';
$kode_kirim        = $_POST['kode_kirim'] ?? '';
$petugas_pemeriksa = User::$username;

$plant_id = $_SESSION[APP_NAME]["plant_id"] ?? '';
$region   = '';
$to       = array(); // email recipients

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Get region for current plant
// ----------------------------------------------------------------------------
if (!empty($plant_id)) {
    $sql = mysqli_query($con, 
        "SELECT region FROM tbm_plant WHERE plant_id = '$plant_id'"
    );
    
    if ($sql && mysqli_num_rows($sql) > 0) {
        $row_region = mysqli_fetch_assoc($sql);
        $region = $row_region["region"];
    }
}

// ----------------------------------------------------------------------------
// 2. Fetch email addresses for notification (active emails for region/plant)
// ----------------------------------------------------------------------------
$str = "
    SELECT email 
    FROM (
        SELECT * FROM (
            SELECT email, active, region 
            FROM `dbtruck`.tbm_email_to 
            WHERE plant_id = '$plant_id' OR plant_id = '9000'
        ) AS a 
        WHERE region = '$region' OR region = '0'
    ) AS b 
    WHERE active = 1
";

$result = mysqli_query($con, $str);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $to[] = $row["email"];
    }
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Send email notification via SMTP.
 * 
 * @param array  $mailto     Recipient email addresses.
 * @param string $subject    Email subject.
 * @param string $msgcontent Email body (HTML).
 */
function sendEmail($mailto, $subject, $msgcontent) {
    $mail = new PHPMailer;
    $mail->IsSMTP();
    $mail->SMTPAuth   = false;
    $mail->Host       = "mail.nead.danet";
    $mail->SMTPDebug  = 0; // Set to 0 to avoid output breaking redirects
    $mail->Port       = 25;
    $mail->SetFrom("achmad.afandi@danone.com", "ADOP No-Reply");
    $mail->Subject    = $subject;
    
    $body  = "$msgcontent <br><br> Please follow the link below.<br>";
    $body .= "<a href='http://10.203.121.73:83/truck/temuan.php'>http://10.203.121.73:83/truck</a><br><br>";
    $body .= "Sekian<br>Terimakasih";
    
    $mail->MsgHTML($body);
    
    foreach ($mailto as $email) {
        if (!empty($email)) {
            $mail->AddAddress($email);
        }
    }
    
    $mail->Send();
}

// ============================================================================
// BUSINESS LOGIC
// ============================================================================

// ----------------------------------------------------------------------------
// 3. Send email notification if truck is rejected
// ----------------------------------------------------------------------------
if ($hasil == 'Di Tolak di Pos 1' && !empty($to)) {
    $message  = "Truck Anda Ditolak dengan ID Ref $idref<br>";
    $message .= "Nopol               : " . strtoupper($nopol) . "<br>";
    $message .= "Petugas Pemeriksa   : " . strtoupper($petugas_pemeriksa) . "<br>";
    $message .= "Ekspedisi           : " . strtoupper($supplier) . "<br>";
    $message .= "Ditolak dari Plant  : " . strtoupper($lokasi) . "<br>";
    $message .= "Kerusakan           : " . strtoupper($komentar);
    
    sendEmail($to, "Truck Anda Di Tolak", $message);
}

// ----------------------------------------------------------------------------
// 4. Update inspection record in database
// ----------------------------------------------------------------------------
$kode_str = !empty($kode_kirim) ? ", kode_kirim = '$kode_kirim'" : "";
$update_ceklist = "
    UPDATE tb_ceklist 
    SET hasil_pemeriksaan   = '$hasil',
        komentar_kerusakan  = '$komentar',
        tindakan_perbaikan  = '$tindakan'
        $kode_str
    WHERE idref = '$idref'
";

mysqli_query($con, $update_ceklist);

if (mysqli_error($con)) {
    die("Database Error: " . mysqli_error($con));
}

// ============================================================================
// REDIRECT
// ============================================================================
header("location: index");
exit();
?>