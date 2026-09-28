
<!-- <div class="body-wrap-with-navbar"> -->
<!-- <link rel="stylesheet" href="static/css/kotak.css"> -->

</br>
</br>
</br>


<!-- <div class="kotak_sq">
      <a class="btn btn-primary tombol_safety" href="main?action=pilih_gate">FG Truck</a>
      </br>
      </br>
      <a class="btn btn-primary tombol_quality" href="main?action=pilih_gate">Material Truck</a>
      </br>
      <!-- <center>
        <a class="link" href="index.php">BACK</a>
      </center>
</div> -->

<?php
$username=$_SESSION[APP_NAME]["username"];

$sql_username = mysqli_query($con,"  SELECT * from tbm_user where nama='$username' ");


          while($rowuser = mysqli_fetch_assoc($sql_username)){
          $plant_name=$rowuser["plant_name"];
          $plant_id=$rowuser["plant_id"];

          }

?>         


  
<h1><label style="font-size: 20px">Driver & Helper sudah Register..???</label></h1>

<div class="kotak_sq" style="margin-top: 5px;">    


<form method="post" action="main?action=start">
 		<input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>
		<button type="submit" class="tombol_hijau">Sudah</button>
</form>	

</br>
</br>

<form method="post" action="main?action=pilih_driver">
 		<input type="text" name="muat" value="<?php echo $muat; ?>" hidden></input>
    <input type="text" name="plant_name" value="<?php echo $plant_name; ?>" hidden></input>
    <input type="text" name="plant_id" value="<?php echo $plant_id; ?>" hidden></input>
		<button type="submit" class="tombol_merah">Belum</button>
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