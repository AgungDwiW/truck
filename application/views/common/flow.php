<?php
/*
# e_Truck Inspection — Langkah awal (index -> start -> pilih_gate) jadi SATU halaman.
# Perpindahan langkah pakai JavaScript + transisi CSS, tanpa reload/redirect ke file lain.
# View lama (index.php, start.php, pilih_gate.php) sekarang hanya 1 baris wrapper:
#     <?php $flow_step='...'; require __DIR__ . '/flow.php';
# URL lama tetap jalan: main?action=index | main?action=start | main?action=pilih_gate
# dan nilai POST/GET lama (muat = FG | Material) tetap didukung.
# Rev : 2026-09-28
*/

if (!isset($flow_step)) $flow_step = 'index';
$flow_steps = array('index', 'start', 'pilih_gate');
if (!in_array($flow_step, $flow_steps, true)) $flow_step = 'index';

# --- data user (dulu di views/common/index.php) ---
$username   = $_SESSION[APP_NAME]["username"];
$plant_name = '';
$plant_id   = '';

$sql_username = mysqli_query($con, "  SELECT * from tbm_user where nama='$username' ");

while ($rowuser = mysqli_fetch_assoc($sql_username)) {
    $plant_name = $rowuser["plant_name"];
    $plant_id   = $rowuser["plant_id"];
}

# --- 'muat' (FG | Material): dari POST, GET, atau bawaan ---
$muat = '';
if (isset($_POST['muat']))      $muat = $_POST['muat'];
elseif (isset($_GET['muat']))   $muat = $_GET['muat'];
if ($muat !== 'FG' && $muat !== 'Material') $muat = 'FG';

$link_gate1 = ($muat === 'FG') ? 'FG_cek_nopol' : 'cek_nopol';
?>
<link rel="stylesheet" href="static/css/kotak.css">

<style type="text/css">
body{
  font-family: sans-serif;
  background-color: black;
}

h1{
  text-align: center;
  padding-top: 0px;
  font-weight: 300;
  color: white;
}

label{ font-size: 11pt; color: white; }

.kotak_sq{
  width: 250px;
  background: blue;
  margin: 0 auto;
  padding: 50px 20px;
  box-shadow: 0px 0px 100px 4px #d6d6d6;
}

.tombol_safety,
.tombol_quality,
.tombol_gate1,
.tombol_gate2,
.tombol_hijau,
.tombol_yellow,
.tombol_merah,
.tombol_except,
.tombol_no,
.tombol_submit{
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}
.tombol_safety{ background: red; }
.tombol_quality{ background: orange; }
.tombol_gate1{ background: black; }
.tombol_gate2{ background: yellow; color: black; }
.tombol_hijau{ background: green; }
.tombol_yellow{ background: yellow; color: black; }
.tombol_merah{ background: red; }
.tombol_except{ background: purple; }
.tombol_no{ background: white; color: black; }
.tombol_submit{ background: black; }

.link{ color: white; text-decoration: none; font-size: 20pt; }
.alert{ background: #e44e4e; color: white; padding: 10px; text-align: center; border:1px solid #b32929; }

/* --- transisi antar langkah (satu halaman, tanpa reload) --- */
.flow-stage{
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  /* isi di tengah area yang kelihatan: tinggi navbar dikurangi oleh JS supaya
     panggung pas sampai bawah viewport (tanpa memicu scroll). */
  min-height: calc(100vh - 59px);
}
.flow-step{
  width: 100%;
  display: none;
  opacity: 0;
  transform: translateX(36px);
  transition: opacity .3s ease, transform .3s ease;
}
.flow-step.is-active{ display: block; opacity: 1; transform: translateX(0); }
.flow-step.is-leaving{
  display: block;
  position: absolute;
  top: 0; left: 0; right: 0;
  opacity: 0;
  transform: translateX(-36px);
}
.flow-btn{ display: block; text-align: center; text-decoration: none; cursor: pointer; }
.flow-gap{ height: 20px; }
</style>

<div class="flow-stage" id="flowStage"
     data-initial="<?php echo $flow_step; ?>"
     data-muat="<?php echo htmlspecialchars($muat, ENT_QUOTES); ?>">

  <!-- ============ LANGKAH 1: index — driver/helper sudah register? ============ -->
  <section class="flow-step" id="flow-step-index">
    <h1><label style="font-size: 20px">Driver & Helper sudah Register..???</label></h1>

    <div class="kotak_sq" style="margin-top: 5px;">
      <a class="flow-btn tombol_hijau" href="main?action=start" data-flow="start">Sudah</a>
      <div class="flow-gap"></div>
      <form method="post" action="main?action=pilih_driver">
        <input type="text" name="muat"       value="<?php echo htmlspecialchars($muat, ENT_QUOTES); ?>" hidden>
        <input type="text" name="plant_name" value="<?php echo htmlspecialchars($plant_name, ENT_QUOTES); ?>" hidden>
        <input type="text" name="plant_id"   value="<?php echo htmlspecialchars($plant_id, ENT_QUOTES); ?>" hidden>
        <button type="submit" class="tombol_merah">Belum</button>
      </form>
    </div>
  </section>

  <!-- ============ LANGKAH 2: start — FG atau Material ============ -->
  <section class="flow-step" id="flow-step-start">
    <div class="kotak_sq">
      <a class="flow-btn tombol_safety"  href="main?action=pilih_gate&amp;muat=FG"
         data-flow="pilih_gate" data-muat="FG">FG Truck</a>
      <div class="flow-gap"></div>
      <a class="flow-btn tombol_quality" href="main?action=pilih_gate&amp;muat=Material"
         data-flow="pilih_gate" data-muat="Material">Material Truck</a>
    </div>
  </section>

  <!-- ============ LANGKAH 3: pilih_gate — GATE 1 / GATE 2 ============ -->
  <section class="flow-step" id="flow-step-pilih_gate">
    <div class="kotak_sq">
      <form method="post" id="flowGate1" action="main?action=<?php echo $link_gate1; ?>">
        <input type="text" name="muat" id="flowGate1Muat"
               value="<?php echo htmlspecialchars($muat, ENT_QUOTES); ?>" hidden>
        <button type="submit" class="tombol_gate1">GATE 1</button>
      </form>
      <div class="flow-gap"></div>
      <form method="post" id="flowGate2" action="main?action=db_waiting">
        <input type="text" name="muat" id="flowGate2Muat"
               value="<?php echo htmlspecialchars($muat, ENT_QUOTES); ?>" hidden>
        <button type="submit" class="tombol_gate2">GATE 2</button>
      </form>
      <div class="flow-gap"></div>
      <div style="text-align: center;">
        <a class="link" href="main?action=start" data-flow="start">BACK</a>
      </div>
    </div>
  </section>

</div>

<script type="text/javascript">
/* Perpindahan langkah: tanpa reload, pakai transisi CSS + history (tombol back jalan). */
(function () {
  var stage = document.getElementById('flowStage');
  if (!stage) { return; }

  var STEPS = ['index', 'start', 'pilih_gate'];
  var state = {
    step: stage.getAttribute('data-initial') || 'index',
    muat: stage.getAttribute('data-muat') || 'FG'
  };
  var leaveTimer = null;

  function stepEl(name) { return document.getElementById('flow-step-' + name); }

  /* Bersihkan sisa kelas transisi. Bug lama: kelas "is-entering" tidak pernah
     dilepas, jadi langkah yang sudah ditinggalkan tetap display:block (opacity 0)
     -> kotak lama masih makan ruang, kelihatan hitam, langkah berikutnya kedorong. */
  function clearStates(keepActive) {
    for (var i = 0; i < STEPS.length; i++) {
      var s = stepEl(STEPS[i]);
      if (!s) { continue; }
      s.classList.remove('is-leaving');
      if (s !== keepActive) { s.classList.remove('is-active'); }
      s.style.display = '';
      s.style.top = '';
    }
  }

  function applyMuat() {
    var form1 = document.getElementById('flowGate1');
    var m1    = document.getElementById('flowGate1Muat');
    var m2    = document.getElementById('flowGate2Muat');

    if (m1) { m1.value = state.muat; }
    if (m2) { m2.value = state.muat; }
    if (form1) {
      form1.setAttribute('action', 'main?action=' + (state.muat === 'FG' ? 'FG_cek_nopol' : 'cek_nopol'));
    }
  }

  /* Navbar fixed (navbar-fixed-top): geser panggung setinggi navbar supaya isi
     benar-benar di tengah area yang kelihatan, bukan di tengah viewport. */
  function centerStage() {
    var nav = document.querySelector('.navbar');
    var h = nav ? Math.round(nav.getBoundingClientRect().height) : 0;
    if (!h) { h = 59; }
    /* navbar ini ada di normal flow (bukan fixed/overlay), jadi TIDAK perlu margin
       tambahan - kalau nanti diubah jadi fixed-top, margin-nya dipasang otomatis. */
    var fixed = nav ? (getComputedStyle(nav).position === 'fixed') : false;
    stage.style.marginTop = fixed ? h + 'px' : '0px';
    stage.style.minHeight = 'calc(100vh - ' + h + 'px)';
  }

  function showStep(name, push) {
    if (STEPS.indexOf(name) === -1) { name = 'index'; }

    var current = stepEl(state.step);
    var next    = stepEl(name);
    if (!next) { return; }

    if (push !== false) {
      if (window.history && history.pushState) {
        history.pushState({ step: name }, '', '#' + name);
      } else {
        location.hash = name;
      }
    }

    clearTimeout(leaveTimer);
    clearStates(next);

    if (current && current !== next) {
      /* kunci posisi lama dulu supaya kotak yang memudar tidak meloncat vertikal */
      current.style.top = current.offsetTop + 'px';
      current.classList.add('is-leaving');
      leaveTimer = setTimeout(function () {
        current.classList.remove('is-leaving');
        current.style.display = '';
        current.style.top = '';
      }, 340);
    }

    /* tampilkan dulu (masih opacity 0), commit, baru diaktifkan -> transisi jalan,
       dan display:inline-nya langsung dilepas supaya tidak ada sisa "nyangkut" */
    next.style.display = 'block';
    void next.offsetWidth;
    next.classList.add('is-active');
    next.style.display = '';

    state.step = name;
    applyMuat();
  }

  /* Klik apa pun yang punya data-flow -> pindah langkah (href tetap ada sebagai fallback non-JS). */
  document.addEventListener('click', function (ev) {
    var node = ev.target;
    while (node && node !== document.body) {
      if (node.getAttribute && node.getAttribute('data-flow')) {
        ev.preventDefault();
        var m = node.getAttribute('data-muat');
        if (m) { state.muat = m; }
        showStep(node.getAttribute('data-flow'), true);
        return;
      }
      node = node.parentNode;
    }
  }, false);

  window.addEventListener('popstate', function (ev) {
    var s = (ev.state && ev.state.step) ? ev.state.step : (location.hash || '#index').substring(1);
    showStep(s, false);
  });

  /* --- inisialisasi --- */
  var hash = (location.hash || '').substring(1);
  if (STEPS.indexOf(hash) !== -1) { state.step = hash; }

  var first = stepEl(state.step);
  if (first) {
    first.style.display = 'block';
    void first.offsetWidth;
    first.classList.add('is-active');
    first.style.display = '';
  }
  centerStage();
  window.addEventListener('resize', centerStage);

  applyMuat();

  if (window.history && history.replaceState) {
    history.replaceState({ step: state.step }, '', '#' + state.step);
  }
})();
</script>
