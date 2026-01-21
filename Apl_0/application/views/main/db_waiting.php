<div class="body-wrap-with-navbar">

    <?php
    $bln = date("m");
 
    $query_1 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as count
    FROM tb_ceklist
    WHERE hasil_pemeriksaan<>'Di Tolak di Pos 1' and month(tgbaca)='".$bln."'")or die(mysql_error());
    $A = mysqli_fetch_array($query_1);

    $query_2 = mysqli_query($con,"SELECT COUNT(hasil_pemeriksaan) as sisa
    FROM tb_ceklist
    WHERE hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2'")or die(mysql_error());
    $B = mysqli_fetch_array($query_2);
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
        <th>Tanggal</th>
        <th>Nopol</th>
        <th>Cek Gate</th>   
      </tr>
    </thead>
    </table>
    </div>
    <div  class="tbl-content">
    <table cellpadding="0" cellspacing="30" border="0">
     <form method="post" action="main?action=cek_gate2">
    <tbody>

    <?php 
    
    $query_mysql = mysqli_query($con,"SELECT * from tb_ceklist where MONTH(tgbaca)='".$bln."' and hasil_pemeriksaan='Lanjut Pemeriksaan Gate 2'")or die(mysql_error());
    $nomor = 1;
    while($data = mysqli_fetch_array($query_mysql)){
    ?>
   
    <tr>
      <td><?php echo $nomor++; ?></td>
      <td><?php echo $data['tgl_pemeriksaan']; ?></td>
      <td class="text-uppercase"><?php echo $data['nopol']; ?></td>
      <td>
        
        <input type="text" id="kode" name="kode" value="<?php echo $data['no']; ?>" hidden></input>
        <button type="submit" class="btn btn-warning">Gate 2</button>

      </td>
    </tr>
    
    <?php } ?>
    </form>
    </tbody>
  </table>
  </div>
  </section>
