<?php
/*
# e_Truck Inspection — helper koneksi (dipakai semua file db_*.php)
# db_open($host, $user, $pass, $db, $label)
#   - connect timeout 5 detik: server mati / tidak terjangkau gagal cepat,
#     tidak menggantung sampai 60s (yang bikin 504 Gateway Timeout).
#   - kalau gagal: berhenti dengan halaman error ber-style.
*/
if (!function_exists('db_open')) {
    function db_open($host, $user, $pass, $db, $label)
    {
        $link = mysqli_init();
        if ($link === false) {
            return false;
        }
        @mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        if (!@mysqli_real_connect($link, $host, $user, $pass, $db)) {
            die('<body style="font-family: arial;"><div style="padding: 20px;border:dotted 1px gray;color: #f44336;"><b>ERROR !</b><small> Server Connection (' . $label . ') Lost ... ' . mysqli_connect_error() . '</small></div></body>');
        }
        return $link;
    }
}
