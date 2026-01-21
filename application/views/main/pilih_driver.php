
<!-- <div class="body-wrap-with-navbar"> -->
<!-- <link rel="stylesheet" href="static/css/kotak.css"> -->



<?php
$plant_name = @$_POST['plant_name']; 
$plant_id = @$_POST['plant_id']; 



?>  


<div class="kotak_sq" style="padding-top: 0px;">   

<h1><label style="font-size: 20px">DRIVER</label></h1>

<form method="post" action="main?action=status_vaksin">

<input type="text" id="orang" name="orang" value="driver" hidden >
<input type="text" id="plant_name" name="plant_name" value="<?php echo $plant_name ;  ?>" hidden >
<input type="text" id="plant_id" name="plant_id" value="<?php echo $plant_id ;  ?>" hidden >

<div class="row" >
    <div class="col-md-12">
    <div class="text-center font-weight-bold" >

    <div class="bg-white text-dark" >
    <input type="text" class="form-control" id="nama_driver" name="nama_driver" placeholder="Nama Driver (Sesuai KTP)" >
    </div>

    </div>
    </div>
</div>

</br>

<div class="row" >
    <div class="col-md-12">
    <div class="text-center font-weight-bold" >

    <div class="bg-white text-dark" >
    <input type="text" class="form-control" id="nik" name="nik" maxlength="8" placeholder="8 digit awal NIK" >
    </div>

    </div>
    </div>
</div>

</br>

<div class="row" >
    <div class="col-md-12">
    <div class="text-center font-weight-bold" >

    <div class="bg-white text-dark" >
    <input type="text" class="form-control" id="nama_trans" name="nama_trans" placeholder="Nama Transporter" >
    </div>

    </div>
    </div>
</div>

</br>

<button type="submit" class="btn btn-primary tombol_submit">Submit</button>
</form> 



</br>

<form method="post" action="main?action=pilih_helper">
    <input type="text" id="plant_name" name="plant_name" value="<?php echo $plant_name ;  ?>" hidden >
    <input type="text" id="plant_id" name="plant_id" value="<?php echo $plant_id ;  ?>" hidden >
    <input type="text" name="muat" value="Material" hidden></input>
    <button type="submit" class="btn btn-primary tombol_no">SKIP</button>
</form> 




</div>


</body>
</html>



<style type="text/css">

body{
  font-family: sans-serif;
  /*background: #ebf9fb;*/
  background-color: black;
}

h1{
  text-align: center;
  /*ketebalan font*/
  padding-top: 0px;
  font-weight: 300;
  color: white;
}

.tulisan_login{
  text-align: center;
  /*membuat semua huruf menjadi kapital*/
  text-transform: uppercase;
  color: white;
  font-size: 15pt;
}

.kotak_login{
  width: 250px;
  background: red;
  /*meletakkan form ke tengah*/
  margin: 10px auto;
  padding: 30px 20px;
  box-shadow: 0px 0px 100px 4px #d6d6d6;
}

.kotak_sq{
  width: 250px;
  background: blue;
  /*meletakkan form ke tengah*/
  margin: 100px auto;
  padding: 50px 20px;
  box-shadow: 0px 0px 100px 4px #d6d6d6;
}





label{
  font-size: 11pt;
  color: white;
}

.form_login{
  /*membuat lebar form penuh*/
  box-sizing : border-box;
  width: 100%;
  padding: 10px;
  font-size: 11pt;
  margin-bottom: 20px;
}

.tombol_safety{
  background: red;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_quality{
  background: orange;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_gate1{
  background: black;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_gate2{
  background: yellow;
  color: black;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}




.link{
  color: white;
  text-decoration: none;
  font-size: 20pt;
}

.alert{
  background: #e44e4e;
  color: white;
  padding: 10px;
  text-align: center;
  border:1px solid #b32929;
}  

.tombol_hijau{
  background: green;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_yellow{
  background: yellow;
  color: black;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_merah{
  background: red;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_except{
  background: purple;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_no{
  background: white;
  color: black;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}

.tombol_submit{
  background: black;
  color: white;
  font-size: 20pt;
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
}


</style>