<?php
$muat = @$_POST['muat'];
$username = User::$username;
$sql_username = mysqli_query($con, "SELECT * FROM tbm_user WHERE nama='$username'");

while($rowuser = mysqli_fetch_assoc($sql_username)){
  $plant_name = $rowuser["plant_name"];
  $plant_id = $rowuser["plant_id"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Truck</title>
    <link rel="stylesheet" href="plugins/bootstrap-3.4.1-dist/css/bootstrap.min.css">
    <style>
        .form-container {
            margin: 50px auto;
            max-width: 700px;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .nopol-group input {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
    <a class="navbar-brand" href="index">
      <img src="plugins/icon.png" alt="Logo" style="height: 24px; display: inline-block; margin-top: -4px;">
      Home
    </a>

    </div>
    <ul class="nav navbar-nav">
     
   
  </div>
</nav>
<br>



<div class="container form-container">
    <h3 class="text-center">Input Data KIR Kendaraan</h3>

    <?php
    if (isset($_SESSION['pesan'])) {
        echo '<div class="alert alert-' . $_SESSION['pesan_tipe'] . '" role="alert">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo $_SESSION['pesan'];
        echo '</div>';
        unset($_SESSION['pesan']);
        unset($_SESSION['pesan_tipe']);
    }
    ?>

    <form action="kirim_input_nopol" method="POST">
        <input type="hidden" class="form-control" name="plant_update_id" value="<?= $plant_id; ?>" required>
        <input type="hidden" class="form-control" name="plant_update_desc" value="<?= $plant_name; ?>" required>
        <input type="hidden" class="form-control" name="update_by" value="<?= $username; ?>" required>
        
        <label>Nomor Polisi</label>
        <div class="form-group nopol-group">
            <div class="row">
                <div class="col-xs-3">
                    <input type="text" class="form-control" name="nopol_prefix" maxlength="2" placeholder="B" required>
                </div>
                <div class="col-xs-5">
                    <input type="text" class="form-control" name="nopol_number" maxlength="4" placeholder="1234" required>
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control" name="nopol_suffix" maxlength="3" placeholder="XYZ" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="tipe_truck">Tipe Truck</label>
            <select class="form-control" name="tipe_truck" required>
                <option value="">-- Pilih Tipe Truck --</option>
                <option value="Pick Up">Pick Up</option>
                <option value="CDE">CDE</option>
                <option value="CDD">CDD</option>
                <option value="Tronton">Tronton</option>
                <option value="Wingbox">Wingbox</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tahun_pembuatan">Tahun Pembuatan</label>
            <select class="form-control" name="tahun_pembuatan" required>
                <option value="">-- Pilih Tahun --</option>
                <?php
                $tahun_sekarang = date('Y');
                for ($i = $tahun_sekarang; $i >= 2000; $i--) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="kir_date">Tanggal Expired KIR</label>
            <input type="date" class="form-control" name="kir_date" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Simpan Data</button>
    </form>
</div>

<script src="plugins/js/jquery-3.6.0.min.js"></script>
<script src="plugins/bootstrap-3.4.1-dist/js/bootstrap.min.js"></script>
</body>
</html>

