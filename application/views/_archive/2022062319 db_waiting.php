<div class="body-wrap-with-navbar">

    <?php

    $muat = @$_POST['muat'];

    if ($muat=='FG') {$muatan='FG';}
    if ($muat<>'FG') {$muatan='Material';}


    $bln = date("m");
    $THN = date("Y");
    $PLANT = $_SESSION[APP_NAME]['plant_id'];
    $str = "SELECT COUNT(hasil_pemeriksaan) as count FROM tb_ceklist WHERE hasil_pemeriksaan<>'Di Tolak di Pos 1' AND MONTH(tgbaca)=".$bln." AND YEAR(tgbaca)=2022 AND plant_id = '".$PLANT."'";
    $query_1 = mysqli_query($con, $str);
    $A = mysqli_fetch_assoc($query_1);

    $query_2 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as sisa FROM tb_ceklist WHERE hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2'");
    $B = mysqli_fetch_assoc($query_2);
    $hasil= round((($A['count']-$B['sisa']) / $A['count'])*100);

    ?>






<style type="text/css">
.table-area {
  position: relative;
  z-index: 0;
  margin-top: 130px;
}

table.responsive-table {
  display: table;
  /* required for table-layout to be used (not normally necessary; included for completeness) */
  table-layout: fixed;
  /* this keeps your columns with fixed with exactly the right width */
  width: 100%;
  /* table must have width set for fixed layout to work as expected */
  height: 100%;
}

table.responsive-table thead {
  position: fixed;
  top: 50px;
  left: 0;
  right: 0;
  width: 100%;
  height: 50px;
  line-height: 3em;
  background: #eee;
  table-layout: fixed;
  display: table;
}

table.responsive-table th {
  background: #eee;
}

table.responsive-table td {
  line-height: 2em;
}

table.responsive-table tr > td,
table.responsive-table th {
  text-align: left;
}
</style>

</br>
</br>


    <section> 
    <div  class="tbl-header">
    <table cellpadding="0" cellspacing="30" border="0">
    <thead>

      <tr>
        <th>No</th>
        <th>Pemeriksa</th>
        <th>Plant</th>
        <th>Tanggal</th>
        <th>Nopol</th>
        <th>Muatan</th>
        <th>Cek Gate</th>   
      </tr>
    </thead>
    </table>
    </div>
    <div  class="tbl-content">
    <table cellpadding="0" cellspacing="30" border="0">
     
    <tbody>

    <?php 
    $username=$_SESSION[APP_NAME]["username"];
    $sql_username = mysqli_query($con,"  SELECT * from tbm_user where nama='$username' ");


          while($rowuser = mysqli_fetch_assoc($sql_username)){
          $plant_name=$rowuser["plant_name"];
          $plant_id=$rowuser["plant_id"];

          }



      
    // echo "SELECT * from tb_ceklist where MONTH(tgbaca)='".$bln."' and hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2' and plant_id='$plant_id' and muatan='$muatan' order by tgbaca desc";
    
    $query_mysql = mysqli_query($con,"SELECT * from tb_ceklist where MONTH(tgbaca)='".$bln."' AND YEAR(tgbaca)=2022 and hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2' and plant_id='$plant_id' and muatan='$muatan' order by tgbaca desc  LIMIT 100");
    $nomor = 1;
    while($data = mysqli_fetch_array($query_mysql)){
    $dtnopol= str_replace(' ', '', $data['nopol']); 
    $date=date("Y-m-d");

    //$sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where REPLACE(no_pol,' ','')='$dtnopol' AND DATE(tanggal_datang)='$date' LIMIT 1");
    $sql_nop=mysqli_query($con_3,"SELECT * from tbl_visit where REPLACE(no_pol,' ','')='$dtnopol' ORDER BY tanggal_datang DESC LIMIT 1");

    $count_nopol=mysqli_num_rows($sql_nop); 

    ?>
   
    <tr>
      <td><?php echo $data['no']; ?></td>
      <td><?php echo $data['petugas_pemeriksa']; ?></td>
      <td><?php echo $data['plant_name']; ?></td>
      <td><?php echo $data['tgbaca']; ?></td>
      <td class="text-uppercase"><?php echo $dtnopol; ?></td>
      <td class="text-uppercase"><?php echo $data['muatan']; ?></td>
      <td>
        <form method="post" action="main?action=cek_gate2">
        <input type="text" id="kode" name="kode" value="<?php echo $data['no']; ?>" hidden></input>

        <!-- <button type="submit" class="btn btn-warning">Gate 2</button> -->
        <?php
        if ($count_nopol==0){
        ?>  
        <button type="submit" class="btn" style="background-color: red">Input eVisitor</button>
        <?php } ?>
        <?php
        if ($count_nopol<>0){
        ?> 
        <button type="submit" class="btn" style="background-color: green">Lanjut Gate 2</button>
        <?php } ?>

        </form>

      </td>
    </tr>
    
    <?php } ?>
    </tbody>
  </table>
  </div>
  </section>
