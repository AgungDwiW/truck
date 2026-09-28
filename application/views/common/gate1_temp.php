<?php
$seq=$_POST['seq'];
$idref=$_POST['idref'];
$ccp=$_POST['ccp'];
$nopol=$_POST['nopol'];
$petugas=$_POST['petugas'];
$lokasi=$_POST['lokasi'];



$query_foto = mysqli_query($con,"SELECT seq_foto from tb_ceklist where idref='$idref'");
while($row = mysqli_fetch_assoc($query_foto)){
$seq_foto=$row["seq_foto"];
}
if ($seq_foto==1) {
mysqli_query($con,"UPDATE tb_ceklist SET seq_foto=0, utama".$ccp."=0 where idref='$idref'");}
header("location:main?action=N_gate1");



?>