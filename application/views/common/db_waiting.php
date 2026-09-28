<div class="body-wrap-with-navbar">
<?php
    $muat = @$_POST['muat'];
    $PLANT = $_SESSION[APP_NAME]['plant_id'];
    $MUATAN = $muat=='FG'?"FG":"Material";
    $TGLTRUCK = date('Y-m-d', strtotime(date('Y-m-d') .' -2 day'));
    $TGLEVISITOR = date('Y-m-d', strtotime(date('Y-m-d') .' -21 day'));
?>

<style type="text/css">
  span.btn { background: red; color: white; padding: 5px; border: 1px solid red; border-radius: 5px; }
  #demoA thead, #demoA tbody { display: block; }
  #demoA tbody {
    max-height: 350px;
    overflow: auto;
    font-size: 13px;
  }
  #demoA { width:100%; }
  #demoA th, #demoA td {
    width: 500px;
    font-size: 20px;
    padding: 10px;
    text-align: left;
    font-size: 13px;
  }
  /*#demoA thead { background: black; }*/
</style>

<table id="demoA">
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
  <tbody>
  <?php 
    // SELECT * FROM (SELECT `no`, `petugas_pemeriksa`, plant_name, tgbaca, nopol, muatan, DATE(tgbaca) TGLPERIKSA FROM dbtruck.tb_ceklist WHERE DATE(tgbaca) BETWEEN '2022-06-30' AND '2022-07-02' AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' and plant_id = '9009' and muatan='FG') T1 LEFT JOIN (SELECT DATE(tanggal_datang) TGLDATANG, TRIM(no_pol) NOPOL FROM evisitor.tbl_visit WHERE plant_id = '9009' AND tanggal_datang BETWEEN '2022-06-25' AND '2022-07-02') T2 ON UPPER(T2.NOPOL)=UPPER(T1.nopol)

    //$str = "SELECT * FROM (SELECT `no`, `petugas_pemeriksa`, plant_name, tgbaca, nopol, muatan, DATE(tgbaca) TGLPERIKSA  FROM dbtruck.tb_ceklist WHERE MONTH(tgbaca) = ".$bln." AND YEAR(tgbaca) = ".$THN." AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' and plant_id = '".$PLANT."' and muatan='".$MUATAN."') T1 LEFT JOIN (SELECT DATE(tanggal_datang) TGLDATANG, TRIM(no_pol) NOPOL FROM evisitor.tbl_visit WHERE plant_id = '".$PLANT."') T2 ON T2.NOPOL=T1.nopol AND T1.TGLPERIKSA = T2.TGLDATANG";
  
    $str = "SELECT * FROM (SELECT `no`, `petugas_pemeriksa`, plant_name, tgbaca, nopol, muatan, DATE(tgbaca) TGLPERIKSA  FROM dbtruck.tb_ceklist WHERE DATE(tgbaca) BETWEEN '".$TGLTRUCK."' AND '".date("Y-m-d")."' AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' and plant_id = '".$PLANT."' and muatan='".$MUATAN."') T1 LEFT JOIN (SELECT TRIM(no_pol) NOPOL, DATE(tanggal_datang) TGLDATANG FROM evisitor.tbl_visit WHERE plant_id = '".$PLANT."' AND DATE(tanggal_datang) BETWEEN '".$TGLEVISITOR."' AND '".date("Y-m-d")."' AND no_pol != '' GROUP BY TRIM(no_pol)) T2 ON T2.NOPOL=T1.nopol";
    $now = date("Y-m-d");
    $str = "SELECT 
                    ck.`no`, 
                    ck.`petugas_pemeriksa`, 
                    ck.plant_name, 
                    ck.tgbaca, 
                    ck.nopol, 
                    ck.muatan, 
                    DATE(ck.tgbaca) AS TGLPERIKSA ,
                    DATE(vis.tanggal_datang) AS TGLDATANG 
                FROM dbtruck.tb_ceklist ck
                left join evisitor.tbl_visit vis
                ON trim(ck.NOPOL) = trim(vis.no_pol)
                WHERE 
                    DATE(tgbaca) BETWEEN '{$TGLTRUCK}' AND '{$now}'
                    AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
                    AND ck.plant_id = '{$PLANT}' 
                    AND muatan = '{$MUATAN}'
                    AND DATE(tanggal_datang) BETWEEN '{$TGLEVISITOR}' AND '{$now}'
                    AND no_pol != '' 
            ";
    $str = "SELECT 
                    ck.`no`, 
                    ck.`petugas_pemeriksa`, 
                    ck.plant_name, 
                    ck.tgbaca, 
                    ck.nopol, 
                    ck.muatan, 
                    DATE(ck.tgbaca) AS TGLPERIKSA
                FROM dbtruck.tb_ceklist ck
                WHERE 
                    DATE(tgbaca) BETWEEN '{$TGLTRUCK}' AND '{$now}'
                    AND hasil_pemeriksaan = 'Lanjut Pemeriksaan Gate 2' 
                    AND ck.plant_id = '{$PLANT}' 
                    AND muatan = '{$MUATAN}'
            ";
    // echo "<pre>";
    // print_r($str);
    // echo "</pre>";

    // echo $str;

      $result = mysqli_query($conSL, $str);
    while ($data = mysqli_fetch_assoc($result)) {
      // echo "<pre>";
      // print_r($data);
      // echo "</pre>";
      $str = "SELECT count(*) c FROM tbl_visit WHERE 
            no_pol = '{$data['nopol']}' AND  
            DATE(tanggal_datang) BETWEEN '{$TGLEVISITOR}' AND '{$now}'";
      // echo "<pre>";
      // print_r($str);
      // echo "</pre>";
      $result2 = mysqli_query($con_3, $str);
      $count = mysqli_fetch_assoc($result2);
      // $count = 0;  
      $BTN = "<a href='https://adop.danet/evisitor/tamu?ac=regtamu2'><span class='btn'>Input eVisitor</span></a>";
      if(isset($count['c']) && $count['c'] >0) 
        $BTN = "<button type='submit' class='btn btn-success' name='kode' value='".$data['no']."'>Lanjut Gate 2</button>";
  ?>
      <tr>
        <td><?=$data['no']; ?></td>
        <td><?=$data['petugas_pemeriksa']; ?></td>
        <td><?=$data['plant_name']; ?></td>
        <td><?=$data['tgbaca']; ?></td>
        <td class="text-uppercase"><?=$data['nopol']; ?></td>
        <td class="text-uppercase"><?=$data['muatan']; ?></td>
        <td>
          <form method="post" action="main?action=cek_gate2">
          <?=$BTN;?>
          </form>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
