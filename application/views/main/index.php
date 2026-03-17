<?php
// Retrieve User data
$username   = User::$username;
$plant_name = User::$plant_name;
$plant_id   = User::$plantid;
?>

<style type="text/css">
  body {
    font-family: sans-serif;
    background-color: black;
  }
  
  h1 {
    text-align: center;
    font-weight: 300;
    color: white;
  }

  h1 label {
    font-size: 20px;
    cursor: default;
  }

  .kotak_sq {
    width: 250px;
    background: blue;
    margin: 50px auto; /* Adjusted margin to fit all steps smoothly */
    padding: 50px 20px;
    box-shadow: 0px 0px 100px 4px #d6d6d6;
    border-radius: 5px;
  }

  .btn {
    font-size: 20pt;
    width: 100%;
    border: none;
    border-radius: 3px;
    padding: 20px 20px;
    cursor: pointer;
    margin-bottom: 20px;
    display: block;
    box-sizing: border-box;
  }

  /* Specific Button Colors */
  .tombol_hijau   { background: green; color: white; }
  .tombol_merah   { background: red; color: white; margin-bottom: 0; }
  .tombol_safety  { background: red; color: white; } /* FG Truck */
  .tombol_quality { background: orange; color: white; margin-bottom: 0; } /* Material Truck */
  .tombol_gate1   { background: black; color: white; border: 1px solid #333; }
  .tombol_gate2   { background: yellow; color: black; margin-bottom: 0; }

  /* JavaScript Step Handling Classes */
  .step-container { display: none; }
  .active-step    { display: block; animation: fadeIn 0.3s; }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
</style>

<div id="step1" class="step-container active-step">
  <h1><label>Driver & Helper sudah Register..???</label></h1>
  <div class="kotak_sq">    
    <button type="button" class="btn tombol_hijau" onclick="goToStep(2)">Sudah</button>

    <form method="post" action="<?=route('pilih_driver')?>" style="margin: 0;">
      <input type="hidden" name="muat" value="">
      <input type="hidden" name="plant_name" value="<?php echo $plant_name; ?>">
      <input type="hidden" name="plant_id" value="<?php echo $plant_id; ?>">
      <button type="submit" class="btn tombol_merah">Belum</button>
    </form>	
  </div>
</div>

<div id="step2" class="step-container">
  <h1><label>Pilih Jenis Muatan</label></h1>
  <div class="kotak_sq">    
    <button type="button" class="btn tombol_safety" onclick="selectTruck('FG')">FG Truck</button>
    <button type="button" class="btn tombol_quality" onclick="selectTruck('Material')">Material Truck</button>
  </div>
</div>

<div id="step3" class="step-container">
  <h1><label>Pilih Gate</label></h1>
  <div class="kotak_sq">
    <form id="formGate1" method="get" action="" style="margin: 0;">
      <input type="hidden" name="muat" id="muatGate1" value="">
      <button type="submit" class="btn tombol_gate1">GATE 1</button>
    </form>	
    
    <br>

    <form method="get" action="<?=route('db_waiting')?>" style="margin: 0;">
      <input type="hidden" name="muat" id="muatGate2" value="">
      <button type="submit" class="btn tombol_gate2">GATE 2</button>
    </form>	
  </div>
</div>

<script>
  // Function to switch between UI steps
  function goToStep(stepNumber) {
    document.querySelectorAll('.step-container').forEach(function(el) {
      el.classList.remove('active-step');
    });
    document.getElementById('step' + stepNumber).classList.add('active-step');
  }

  // Function to capture truck type and prepare the Gate forms
  function selectTruck(truckType) {
    // Inject the selected truck type into the hidden inputs for the final submit
    document.getElementById('muatGate1').value = truckType;
    document.getElementById('muatGate2').value = truckType;

    // Dynamically set the Gate 1 URL based on the PHP logic requirement
    var formGate1 = document.getElementById('formGate1');
    if (truckType === 'FG') {
      formGate1.action = '<?=route("FG_cek_nopol")?>';
    } else {
      formGate1.action = 'cek_nopol';
    }

    // Proceed to Step 3
    goToStep(3);
  }
</script>
