<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form Input Truck</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="plugins/bootstrap-3.4.1-dist/css/bootstrap.min.css">
  <style>
    body {
      padding-top: 70px;
    }
    .form-group input {
      text-align: center;
      font-size: 24px;
      text-transform: uppercase;
    }
    .btn-cari {
      font-size: 16px;
      padding: 6px 12px;
    }
    .panel-heading {
      font-size: 18px;
      text-align: center;
    }
    .panel-body {
      padding: 25px;
    }
    .btn-lg {
      font-size: 20px;
      padding: 10px;
    }
    @media (max-width: 480px) {
      .btn-cari {
        height: auto;
        margin-top: 10px;
        width: 100%;
      }
    }
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
    #modalTruck .table th,
    #modalTruck .table td {
      color: black;
    }

    .text-success {
      color: green;
      font-weight: bold;
    }

    .text-danger {
      color: red;
      font-weight: bold;
    }
  </style>



</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
    <a class="navbar-brand" href="main?action=index">
      <img src="plugins/icon.png" alt="Logo" style="height: 24px; display: inline-block; margin-top: -4px;">
      Home
    </a>

    </div>
    <ul class="nav navbar-nav">
     
   
  </div>
</nav>

<!-- PHP Session & Link Routing -->
<?php
$muat = @$_POST['muat'];
$username = $_SESSION[APP_NAME]["username"];
$sql_username = mysqli_query($con, "SELECT * FROM tbm_user WHERE nama='$username'");

while($rowuser = mysqli_fetch_assoc($sql_username)){
  $plant_name = $rowuser["plant_name"];
  $plant_id = $rowuser["plant_id"];
}

$link = ($plant_id == '90A8') ? 'FG_cek_truck' : 'FG_cek_truck_rev';
?>
<br>
<br>
<br>
<!-- Container -->
<div class="container">
  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['pesan_tipe'] ?>" role="alert">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?= $_SESSION['pesan']; ?>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['pesan_tipe']); ?>
  <?php endif; ?>

  <div class="panel panel-primary">
    <div class="panel-heading"><strong>Cek Informasi Truck</strong></div>
    <div class="panel-body">

    <form id="formTruck" method="post" action="main?action=<?php echo $link; ?>">
        <div class="form-group">
          <a href="main?action=input_nopol" class="btn btn-info btn-sm" style="margin-bottom: 10px;">
            + Tambah Nopol Baru
          </a>
          <br>
          <label class="text-uppercase"><strong>Input Nopol</strong></label>
          <div class="row">
            <div class="col-xs-3">
              <input type="text" id="nopol_prefix" class="form-control" placeholder="B" maxlength="2">
            </div>
            <div class="col-xs-4">
              <input type="text" id="nopol_number" class="form-control" placeholder="1234" maxlength="4">
            </div>
            <div class="col-xs-3">
              <input type="text" id="nopol_suffix" class="form-control" placeholder="XYZ" maxlength="3">
            </div>
            <div class="col-xs-2">
              <button type="button" class="btn btn-primary form-control" onclick="gabungNopol()">Cek</button>
            </div>
          </div>
          <input type="hidden" name="nopol" id="nopol">
          <input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>
        </div>

        <div class="form-group">
          <label class="text-uppercase"><strong>Input ID Shipment</strong></label>
          <input type="text" class="form-control text-uppercase" name="id_shipment" required>
        </div>

        <button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
      </form>

      <div class="alert alert-info" style="margin-top: 30px;">
      <h4><strong>Cara Penginputan:</strong></h4>
      <ol style="padding-left: 20px;">
        <li>Nomor kendaraan truck <strong>wajib diregistrasi</strong> berdasarkan data KIR.</li>
        <li>Cara registrasi: klik tombol <strong>Tambah Nopol Baru</strong>.</li>
        <li>Bila data KIR truck sudah terdaftar, <strong>masukan Nomor Kendaraan dan klick tombol CEK </strong> maka <strong>Data KIR akan muncul otomatis</strong>.</li>
        <li>Pastikan <strong>tanggal expired KIR valid</strong>.</li>
        <li>Klik tombol <strong>Tanggal KIR Valid</strong>, ketik nomor shipment, lalu klik tombol <strong>Submit</strong>.</li>
      </ol>
    </div>


    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalTruck" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Data Truck</h4>
      </div>

      <div class="modal-body">
        <table class="table table-bordered table-striped">
          <tbody>
            <tr><th>Nopol</th><td id="modalNopol"></td></tr>
            <tr><th>Tipe Truck</th><td id="modalTipe"></td></tr>
            <tr><th>Tahun Pembuatan</th><td id="modalTahun"></td></tr>
            <tr><th>Tanggal Expired KIR</th><td id="modalKir" class="kir-status"></td></tr>
            <tr><th>Petugas Registrasi</th><td id="modaluser"></td></tr>
            <tr><th>Plant Registrasi</th><td id="modelplant"></td></tr>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Tanggal KIR Valid</button>
        <a href="#" id="btnUpdateKir" class="btn btn-warning">Update KIR</a>
      </div>

    </div>
  </div>
</div>



<!-- Scripts -->
<script src="plugins/bootstrap-3.4.1-dist/js/jquery-2.2.4.min.js"></script>
<script src="plugins/bootstrap-3.4.1-dist/js/bootstrap.min.js"></script>

<script>
function gabungNopol() {
  var prefix = document.getElementById("nopol_prefix").value.toUpperCase().trim();
  var number = document.getElementById("nopol_number").value.trim();
  var suffix = document.getElementById("nopol_suffix").value.toUpperCase().trim();

  var fullNopol = prefix + number + suffix;

  if (prefix && number && suffix) {
    document.getElementById("nopol").value = fullNopol;

    fetch("main?action=cari_truck&nopol=" + encodeURIComponent(fullNopol))
      .then(res => res.text())
      .then(text => {
        const cleanText = text.replace(/^\uFEFF/, ''); // hapus karakter BOM jika ada
        return JSON.parse(cleanText);
      })
      .then(data => {
        if (data && data.nopol) {
          document.getElementById("modalNopol").innerText = data.nopol;
          document.getElementById("modalTipe").innerText = data.tipe_truck;
          document.getElementById("modalTahun").innerText = data.tahun_pembuatan;
          document.getElementById("modaluser").innerText = data.update_by;
          document.getElementById("modelplant").innerText = data.plant_update_desc;

          // Tangani tanggal KIR dan warnai
          const kirCell = document.getElementById("modalKir");
          kirCell.innerText = data.kir_date;

          // Hapus class lama jika ada
          kirCell.classList.remove("text-success", "text-danger");

          // Konversi tanggal KIR
          let kirDate;
          if (data.kir_date.includes("-")) {
            const parts = data.kir_date.split("-");
            if (parts[0].length === 4) {
              // Format YYYY-MM-DD
              kirDate = new Date(data.kir_date);
            } else {
              // Format DD-MM-YYYY
              kirDate = new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
            }
          } else {
            kirDate = new Date(data.kir_date); // fallback
          }

          const today = new Date();
          today.setHours(0, 0, 0, 0);

          if (kirDate >= today) {
            kirCell.classList.add("text-success");
          } else {
            kirCell.classList.add("text-danger");
          }

          // Set link tombol Update KIR
          document.getElementById("btnUpdateKir").href = "main?action=edit_nopol&nopol=" + encodeURIComponent(data.nopol);

          // Tampilkan modal setelah semua data di-set
          $('#modalTruck').modal('show');
        } else {
          alert("Data truck tidak ditemukan.");
        }
      })
      .catch(err => {
        alert("Truck Belum di Register, Klick Tombah Nopol Baru !");
        console.error("Fetch error:", err);
      });
  } else {
    alert("Harap isi semua bagian NOPOL.");
  }
}
</script>

<script>
document.getElementById("formTruck").addEventListener("submit", function(e) {
  const nopol = document.getElementById("nopol").value.trim();
  if (!nopol) {
    e.preventDefault(); // cegah form dikirim
    alert("Silakan cek dan isi data NOPOL terlebih dahulu sebelum submit.");
  }
});
</script>





</body>
</html>
