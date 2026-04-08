<?php
include_once "application/config/connection.php";
include_once "application/config/connection140.php";

$muat       = $_GET['muat'];
$username   = User::$username;
$plant_name = User::$plant_name;
$plant_id   = User::$plantid;

// Determine route based on plant_id
if ($plant_id == '90A8') {
    $link = route('FG_cek_truck');
} else {
    $link = route('FG_cek_truck_rev');
}
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<div class="kotak_sq">
    <form method="get" action="<?php echo $link; ?>">

        <h2>
            <strong>
                <label class="center-block" style="color: yellow; text-align: center;">
                    INPUT NOPOL
                </label>
            </strong>
        </h2>

        <input
            type="text"
            name="nopol"
            class="center-block text-uppercase"
            style="width: 300px; height: 80px; font-size: 50px; background: white; color: black; text-align: center;"
            required
        >

        <br>

        <h2>
            <strong>
                <label class="center-block" style="color: yellow; text-align: center;">
                    INPUT ID SHIPMENT
                </label>
            </strong>
        </h2>

        <input
            type="text"
            name="id_shipment"
            class="center-block text-uppercase"
            style="width: 480px; height: 80px; font-size: 50px; background: white; color: black; text-align: center;"
            required
        >

        <input
            type="hidden"
            name="muat"
            value="<?php echo $muat; ?>"
        >

        <br><br>

        <button type="submit" class="btn btn-success tombol_ic center-block">
            Submit
        </button>

    </form>
</div>

<style type="text/css">
.tombol_ic {
    color: white;
    font-size: 20pt;
    width: 200px;
    height: 80px;
    border: none;
    border-radius: 3px;
    padding: 20px;
}

.label {
    color: white;
    font-size: 30pt;
    width: 300px;
    height: 100px;
    border: none;
    border-radius: 3px;
    padding: 20px;
    background: black;
}
</style>