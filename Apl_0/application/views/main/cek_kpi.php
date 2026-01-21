<div class="body-wrap-with-navbar">
<link rel="stylesheet" href="static/css/kotak.css">

<?php


$bln = date("m");
$day = date("d");
 
    $query_1 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as count
    FROM tb_ceklist
    WHERE hasil_pemeriksaan<>'Di Tolak di Pos 1' and month(tgbaca)='".$bln."'")or die(mysql_error());
    $A = mysqli_fetch_array($query_1);

    $query_2 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as sisa
    FROM tb_ceklist
    WHERE hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2'")or die(mysql_error());
    $B = mysqli_fetch_array($query_2);
    $hasil= round((($A['count']-$B['sisa']) / $A['count'])*100);


    $query_2 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as count
    FROM tb_ceklist
    WHERE hasil_pemeriksaan<>'Di Tolak di Pos 1' and day(tgbaca)='".$day."'")or die(mysql_error());
    $C = mysqli_fetch_array($query_2);

    $query_4 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as sisa
    FROM tb_ceklist
    WHERE hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2' and day(tgbaca)='".$day."'")or die(mysql_error());
    $D = mysqli_fetch_array($query_4);


    $hasil_day= round((($C['count']-$D['sisa']) / $C['count'])*100);


?>

<div class="container-fluid text-center" >
<a class="btn btn-danger" href="main?action=index">BACK</a>
</div>
</br>
</br>
</br>

<table>
  <thead>
    <tr style="background-color: yellow">
      <td style="color: black; text-align: center; font-size: 30px">% KPI</td>
      <td style="color: black; text-align: center; font-size: 30px">DAILY</td>
      <td style="color: black; text-align: center; font-size: 30px">MONTLY</td>
    </tr>
  </thead>
<tbody>



<tr>
      <td style="color: white; text-align: center; font-size: 20px">Gate 1</td>
      <td style="color: white; text-align: center; font-size: 20px"><?php echo $hasil_day;?>%</td>
      <td style="color: white; text-align: center; font-size: 20px"><?php echo $hasil_day;?>%</td>

</tr>

<tr>
      <td style="color: white; text-align: center; font-size: 20px">Gate 2</td>
      <td style="color: white; text-align: center; font-size: 20px"><?php echo $hasil_day;?>%</td>
      <td style="color: white; text-align: center; font-size: 20px"><?php echo $hasil;?>%</td>

</tr>




</tbody>
</table>