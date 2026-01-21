<div class="body-wrap-with-navbar">
<?php

$idref=$_POST['idref'];
?>

<style type="text/css">
    body {
    background-color: yellow;
}
</style>

<form method="post" action="temuan.php"> 
   <button type="submit" style="background-color: red; color: white">BACK</button>
</form>
</br>

</br>

<?php
$db_host = '127.0.0.1';
$db_user = 'afandiach';
$db_pswd = '4d0pd4n60';
$db_name = 'dbtruck';

$con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
    die("<div style='padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> Server Connection Lost...</div>" . mysql_error());

$query = mysqli_query($con,"SELECT * from tb_foto where idref='$idref' ");
$data1 = mysqli_fetch_array($query);    
?>

<table style="margin-left: 50px">
    <tr>
      <td style="color: black; font-size: 30px" width="200px">Id Ref</td>
      <td style="color: black; font-size: 20px">:  <?php echo $data1['idref'];?></td>
    </tr>
    <tr>
      <td style="color: black; font-size: 30px" width="200px">NOPOL</td>
      <td style="color: black; font-size: 20px">:  <?php echo strtoupper($data1['nopol']);?></td>
    </tr>  

    <tr>
      <td style="color: black; font-size: 30px" width="200px">TGL MASUK</td>
      <td style="color: black; font-size: 20px">:  <?php echo $data1['tgbaca'];?></td>
    </tr>

    <tr>
      <td style="color: black; font-size: 30px" width="200px">PEMERIKSA</td>
      <td style="color: black; font-size: 20px">:  <?php echo strtoupper($data1['petugas']);?></td>
    </tr>  


</table>







<table border="1px" style="text-align: center; margin-left: 50px">
  <thead>
    <tr style="background-color: yellow">
      <td style="color: black; text-align: center; font-size: 30px">NO</td>
      <td style="color: black; text-align: center; font-size: 30px" width="300px">Kelengkapan Utama</td>
      <td style="color: black; text-align: center; font-size: 30px" width="500px">TEMUAN</td>
      <td style="color: black; text-align: center; font-size: 30px" width="600px">FOTO</td>
    </tr>
  </thead>
<tbody>



<?php

$query_mysql = mysqli_query($con,"SELECT * from tb_foto where idref='$idref' ");

$no=1;
    while($data = mysqli_fetch_array($query_mysql)){
?>

<tr>
      <td style="color: black; text-align: center; font-size: 20px"><?php echo $no;?></td>
      <td style="color: black; text-align: center; font-size: 20px"><?php echo $data['item_utama'];?></td>
      <td style="color: black; text-align: center; font-size: 20px"><?php echo $data['description']; ?></td>
      <td style="color: black; text-align: center; font-size: 20px">
      <img src="application/views/main/capture\<?php echo $data['foto_name']; ?>" width="320" height="280">
      </td>

</tr>

<?php
$no++;
} 
?>


</tbody>
</table>