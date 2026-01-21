<!DOCTYPE html>
<html lang="en">
  <head>

      <link href="css/bootstrap.min.css" rel="stylesheet">
      <script src="static/js/js_ori/highcharts.js"></script>

  </head>
  <body>
    <?php
        $db_host = '127.0.0.1';
        $db_user = 'afandiach';
        $db_pswd = '4d0pd4n60';
        $db_name = 'dbtruck';

        $con = @mysqli_connect($db_host, $db_user, $db_pswd, $db_name) or
            die("<div style='padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> Server Connection Lost...</div>" . mysql_error());

    ?>

 <!--    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <div class='container-fluid'>
   

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="report.php">Graph <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="online/routing?rpt=truck">Report <span class="sr-only">(current)</span></a>
          </li>
      
          
        </ul>
    
      </div>
      </div>
    </nav> -->




    
<div class="row text-white">
<label>#</label>
</div>
<div class="row text-white">
<label>#</label>
</div>

<?php

          $result = mysqli_query($con,"SELECT hasil_pemeriksaan, warna,
          ROUND((COUNT(hasil_pemeriksaan)/(SELECT COUNT(*) FROM tb_ceklist))*100) AS Prosentase
          FROM tb_ceklist where hasil_pemeriksaan<>'Lanjut Pemeriksaan Gate 2' GROUP BY hasil_pemeriksaan desc");
            while($row = mysqli_fetch_assoc($result))
             {
              $hasil_chart[]=floatval($row["Prosentase"]);
              $name_chart[]=($row["hasil_pemeriksaan"]);
              $color_chart[]=($row["warna"]);
             }
?>


<div class="container">
<div class="row">
<div class="col">

<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
<script type="text/javascript">

Highcharts.chart('container', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: 'Hasil Pemeriksaan'
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
        style: {
          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
        }
      }
    }
  },
  series: [{
    name: 'Pie Chart Hasil',
    colorByPoint: true,
    data: [{
      name: '<?php echo (isset($name_chart[0])? $name_chart[0] : ''); ?>',
      y: <?php echo (isset($hasil_chart[0])? $hasil_chart[0] : 0); ?>,
      color:'<?php echo (isset($color_chart[0])? $color_chart[0] : 'black'); ?>'
    }, {
      name: '<?php echo (isset($name_chart[1])? $name_chart[1] : ''); ?>',
      y: <?php echo (isset($hasil_chart[1])? $hasil_chart[1] : 0); ?>,
      color:'<?php echo (isset($color_chart[1])? $color_chart[1] : 'black'); ?>'
    }, {
      name: '<?php echo (isset($name_chart[2])? $name_chart[2] : ''); ?>',
      y: <?php echo (isset($hasil_chart[2])? $hasil_chart[2] : 0); ?>,
      color:'<?php echo (isset($color_chart[2])? $color_chart[2] : 'black'); ?>'
    }, {
      name: '<?php echo (isset($name_chart[3])? $name_chart[3] : ''); ?>',
      y: <?php echo (isset($hasil_chart[3])? $hasil_chart[3] : 0); ?>,
      color:'<?php echo (isset($color_chart[3])? $color_chart[3] : 'black'); ?>'
  
    }]
  }]
});

</script>
</div>


</div>

<div class="row">
<div class="col-md-6">
<div id="container_1" ></div>

<?php
            $result_1= mysqli_query($con,"SELECT nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'
              group by nama_transporter");
            while($row=mysqli_fetch_assoc($result_1)) { 
            $nama_transporter[]=$row["transporter"];
            $value[]=floatval($row["total"]);
            }


            $qty_truck_tidak_layak=mysqli_query($con,"SELECT sum(total) as jumlah from (select nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'
              group by nama_transporter) as a");
            while($row=mysqli_fetch_assoc($qty_truck_tidak_layak)) { 
            $truck=$row["jumlah"];
            }




?>
<script type="text/javascript">
Highcharts.chart('container_1', {
  chart: {
    plotBackgroundColor: 'red',
    type: 'column'
  },
  title: {
    text: 'Tidak Layak di Operasikan'
  },
  subtitle: {
    text: 'Jumlah Truck : <?php echo ($truck); ?>'
  },
  xAxis: {
    categories: <?php echo json_encode($nama_transporter); ?>,
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Qty'
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  series: [{
    name: 'Kontributor',
    data: <?php echo json_encode($value); ?>



  }]
});
</script>


</div>


<?php
//            $result_2= mysqli_query($con,"SELECT kelengkapan, sum(value) as total from (select *
//            from tb_kelengkapan_utama) as hasil
//            group by kelengkapan");
//            while($row=mysqli_fetch_assoc($result_2)) { 
            //$kelengkapan[]=$row["kelengkapan"];
//            $value_kelengkapan[]=floatval($row["total"]);
//            }

            $res_utam=mysqli_query($con,"SELECT ceklist_utama from tb_ceklist_utama
              where no>=5");
            while($row=mysqli_fetch_assoc($res_utam)) { 
            $kelengkapan[]=$row["ceklist_utama"];            
            }

            $val_utam5=mysqli_query($con,"SELECT COUNT(utama5)
                      FROM tb_ceklist
                      WHERE utama5=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
              while($row=mysqli_fetch_assoc($val_utam5)) { 
              $val_ut5=$row["COUNT(utama5)"];            
              }

            $val_utam6=mysqli_query($con,"SELECT COUNT(utama6)
                      FROM tb_ceklist
                      WHERE utama6=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
              while($row=mysqli_fetch_assoc($val_utam6)) { 
              $val_ut6=$row["COUNT(utama6)"];            
              }

            $val_utam7=mysqli_query($con,"SELECT COUNT(utama7)
                      FROM tb_ceklist
                      WHERE utama7=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
              while($row=mysqli_fetch_assoc($val_utam7)) { 
              $val_ut7=$row["COUNT(utama7)"];            
              }

            $val_utam8=mysqli_query($con,"SELECT COUNT(utama8)
                      FROM tb_ceklist
                      WHERE utama8=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam8)) { 
              $val_ut8=$row["COUNT(utama8)"];            
              }
            $val_utam9=mysqli_query($con,"SELECT COUNT(utama9)
                      FROM tb_ceklist
                      WHERE utama9=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam9)) { 
              $val_ut9=$row["COUNT(utama9)"];            
              }

            $val_utam10=mysqli_query($con,"SELECT COUNT(utama10)
                      FROM tb_ceklist
                      WHERE utama10=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam10)) { 
              $val_ut10=$row["COUNT(utama10)"];            
              }
            $val_utam11=mysqli_query($con,"SELECT COUNT(utama11)
                      FROM tb_ceklist
                      WHERE utama11=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam11)) { 
              $val_ut11=$row["COUNT(utama11)"];            
              }
            $val_utam12=mysqli_query($con,"SELECT COUNT(utama12)
                      FROM tb_ceklist
                      WHERE utama12=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam12)) { 
              $val_ut12=$row["COUNT(utama12)"];            
              }
            $val_utam13=mysqli_query($con,"SELECT COUNT(utama13)
                      FROM tb_ceklist
                      WHERE utama13=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam13)) { 
              $val_ut13=$row["COUNT(utama13)"];            
              }
            $val_utam14=mysqli_query($con,"SELECT COUNT(utama14)
                      FROM tb_ceklist
                      WHERE utama14=0 
                      and hasil_pemeriksaan='Tidak Layak di Operasikan (Stiker Merah)'");
            while($row=mysqli_fetch_assoc($val_utam14)) { 
              $val_ut14=$row["COUNT(utama14)"];            
              }

?>



<div class="col">
<div id="container_2" ></div>
<script type="text/javascript">

Highcharts.chart('container_2', {
  chart: {
    type: 'bar',
    plotBackgroundColor: 'red'
  },
  title: {
    text: 'Item yang Perlu di Perbaiki'
  },
  subtitle: {
    text: ''
  },

  xAxis: {
    categories: <?php echo json_encode($kelengkapan); ?>
    
  },
  yAxis: {
    min: 0
    
    },
  
  
  
  plotOptions: {
    bar: {
      dataLabels: {
        enabled: true
      }
    }
  },

  credits: {
    enabled: false
  },
  series: [{
    name: 'Item Perbaikan',
    data: [<?php echo ($val_ut6); ?>,<?php echo ($val_ut7); ?>,<?php echo ($val_ut8); ?>,<?php echo ($val_ut9); ?>,<?php echo ($val_ut10); ?>,<?php echo ($val_ut11); ?>,<?php echo ($val_ut12); ?>,<?php echo ($val_ut13); ?>,<?php echo ($val_ut14); ?>]
  }]
});


</script>
</div>
</div>





<div class="row">
<div class="col-md-6">
<div id="container_3" ></div>

<?php
//            $result_3= mysqli_query($con,"SELECT transporter, sum(value) as total from (select *
//            from tb_trigger_perlu_perbaikan) as hasil
//            group by transporter");
//            while($row=mysqli_fetch_assoc($result_3)) { 
//            $nama_transporter_perbaikan[]=$row["transporter"];
//            $value_perbaikan[]=floatval($row["total"]);
//            }

            $result_3= mysqli_query($con,"SELECT nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'
              group by nama_transporter");
            while($row=mysqli_fetch_assoc($result_3)) { 
            $nama_transporter_perbaikan[]=$row["transporter"];
            $value_perbaikan[]=floatval($row["total"]);
            }





//            $qty_truck_perbaikan=mysqli_query($con,"SELECT count(value) as count from tb_trigger_perlu_perbaikan
//            where value=1");
//            while($row=mysqli_fetch_assoc($qty_truck_perbaikan)) { 
//            $truck_perbaikan=$row["count"];
//            }


            $qty_truck_perbaikan=mysqli_query($con,"SELECT sum(total) as jumlah from (select nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'
              group by nama_transporter) as a");
            while($row=mysqli_fetch_assoc($qty_truck_perbaikan)) { 
            $truck_perbaikan=$row["jumlah"];
            }




?>
<script type="text/javascript">
Highcharts.chart('container_3', {
  chart: {
    plotBackgroundColor: 'orange',
    type: 'column'
  },
  title: {
    text: 'Perlu Perbaikan'
  },
  subtitle: {
    text: 'Jumlah Truck : <?php echo ($truck_perbaikan); ?>'
  },
  xAxis: {
    categories: <?php echo json_encode($nama_transporter_perbaikan); ?>,
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Qty'
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  series: [{
    name: 'Kontributor',
    data: <?php echo json_encode($value_perbaikan); ?>



  }]
});
</script>


</div>

<?php
//            $result_4= mysqli_query($con,"SELECT kelengkapan, sum(value) as total from (select *
//            from tb_kelengkapan_tambahan) as hasil
//            group by kelengkapan");
//            while($row=mysqli_fetch_assoc($result_4)) { 
//            $kelengkapan_tambahan[]=$row["kelengkapan"];
//            $value_kelengkapan_tambahan[]=floatval($row["total"]);
//            }

            $res_utam4=mysqli_query($con,"SELECT ceklist_tambahan from tb_ceklist_tambahan");
            while($row=mysqli_fetch_assoc($res_utam4)) { 
            $kelengkapan_tambahan[]=$row["ceklist_tambahan"];    
            }

              $val_tamb1=mysqli_query($con,"SELECT COUNT(tambahan1)
                      FROM tb_ceklist
                      WHERE tambahan1=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb1)) { 
              $val_tambahan1=$row["COUNT(tambahan1)"];            
              }

              $val_tamb2=mysqli_query($con,"SELECT COUNT(tambahan2)
                      FROM tb_ceklist
                      WHERE tambahan2=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb2)) { 
              $val_tambahan2=$row["COUNT(tambahan2)"];            
              }
               
              $val_tamb3=mysqli_query($con,"SELECT COUNT(tambahan3)
                      FROM tb_ceklist
                      WHERE tambahan3=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb3)) { 
              $val_tambahan3=$row["COUNT(tambahan3)"];            
              }     
              
              $val_tamb4=mysqli_query($con,"SELECT COUNT(tambahan4)
                      FROM tb_ceklist
                      WHERE tambahan4=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb4)) { 
              $val_tambahan4=$row["COUNT(tambahan4)"];            
              }     

              $val_tamb5=mysqli_query($con,"SELECT COUNT(tambahan5)
                      FROM tb_ceklist
                      WHERE tambahan5=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb5)) { 
              $val_tambahan5=$row["COUNT(tambahan5)"];            
              }

              $val_tamb6=mysqli_query($con,"SELECT COUNT(tambahan6)
                      FROM tb_ceklist
                      WHERE tambahan6=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb6)) { 
              $val_tambahan6=$row["COUNT(tambahan6)"];            
              }

              $val_tamb7=mysqli_query($con,"SELECT COUNT(tambahan7)
                      FROM tb_ceklist
                      WHERE tambahan7=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb7)) { 
              $val_tambahan7=$row["COUNT(tambahan7)"];            
              }

              $val_tamb8=mysqli_query($con,"SELECT COUNT(tambahan8)
                      FROM tb_ceklist
                      WHERE tambahan8=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb8)) { 
              $val_tambahan8=$row["COUNT(tambahan8)"];            
              }            

              $val_tamb9=mysqli_query($con,"SELECT COUNT(tambahan9)
                      FROM tb_ceklist
                      WHERE tambahan9=0 
                      and hasil_pemeriksaan='Perlu Perbaikan (Stiker Kuning)'");
              while($row=mysqli_fetch_assoc($val_tamb9)) { 
              $val_tambahan9=$row["COUNT(tambahan9)"];            
              }



?>








<div class="col">
<div id="container_4" ></div>
<script type="text/javascript">

Highcharts.chart('container_4', {
  chart: {
    type: 'bar',
    plotBackgroundColor: 'orange'
  },
  title: {
    text: 'Item yang Perlu di Perbaiki'
  },
  subtitle: {
    text: ''
  },

  xAxis: {
    categories: <?php echo json_encode($kelengkapan_tambahan); ?>
    
  },
  yAxis: {
    min: 0
    
    },
  
  
  
  plotOptions: {
    bar: {
      dataLabels: {
        enabled: true
      }
    }
  },

  credits: {
    enabled: false
  },
  series: [{
    name: 'Item Perbaikan',
    data: [<?php echo ($val_tambahan1); ?>,<?php echo ($val_tambahan2); ?>,<?php echo ($val_tambahan3); ?>,<?php echo ($val_tambahan4); ?>,<?php echo ($val_tambahan5); ?>,<?php echo ($val_tambahan6); ?>,<?php echo ($val_tambahan7); ?>,<?php echo ($val_tambahan8); ?>,<?php echo ($val_tambahan9); ?>]

  }]
});


</script>
</div>



</div>



<div class="row">
<div class="col-md-6">
<div id="container_5" ></div>

<?php
//            $result_5= mysqli_query($con,"SELECT transporter, sum(value) as total from (select *
//            from tb_trigger_tolak) as hasil
//            group by transporter");
//            while($row=mysqli_fetch_assoc($result_5)) { 
//            $nama_transporter_tolak[]=$row["transporter"];
//            $value_tolak[]=floatval($row["total"]);
//            }




            $result_5= mysqli_query($con,"SELECT nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Di Tolak di Pos 1'
              group by nama_transporter");
            while($row=mysqli_fetch_assoc($result_5)) { 
            $nama_transporter_tolak[]=$row["transporter"];
            $value_tolak[]=floatval($row["total"]);
            }

            
            

            





//            $qty_truck_tolak=mysqli_query($con,"SELECT count(value) as count from tb_trigger_tolak
//            where value=1");
//            while($row=mysqli_fetch_assoc($qty_truck_tolak)) { 
//            $truck_tolak=$row["count"];
//            }



            $qty_truck_tolak=mysqli_query($con,"SELECT sum(total) as jumlah from (select nama_transporter as transporter, count(hasil_pemeriksaan)as total from tb_ceklist
              where hasil_pemeriksaan='Di Tolak di Pos 1'
              group by nama_transporter) as a");
            while($row=mysqli_fetch_assoc($qty_truck_tolak)) { 
            $truck_tolak=$row["jumlah"];
            }




?>
<script type="text/javascript">
Highcharts.chart('container_5', {
  chart: {
    plotBackgroundColor: 'black',
    type: 'column'
  },
  title: {
    text: 'Di Tolak di Pos 1'
  },
  subtitle: {
    text: 'Jumlah Truck : <?php echo ($truck_tolak); ?>'
  },
  xAxis: {
    categories: <?php echo json_encode($nama_transporter_tolak); ?>,
    crosshair: true
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Qty'
    }
  },
  tooltip: {
    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
      '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
    footerFormat: '</table>',
    shared: true,
    useHTML: true
  },
  plotOptions: {
    column: {
      pointPadding: 0.2,
      borderWidth: 0
    }
  },
  series: [{
    name: 'Kontributor',
    data: <?php echo json_encode($value_tolak); ?>



  }]
});
</script>


</div>

<?php
//            $result_6= mysqli_query($con,"SELECT kelengkapan, sum(value) as total from (select *
//            from tb_kelengkapan_tolak_pos1) as hasil
//            group by kelengkapan");
//            while($row=mysqli_fetch_assoc($result_6)) { 
//            $kelengkapan_tolak[]=$row["kelengkapan"];
//            $value_kelengkapan_tolak[]=floatval($row["total"]);
//            }



            $res_utam6=mysqli_query($con,"SELECT ceklist_utama from tb_ceklist_utama
              where no between 1 and 4");
            while($row=mysqli_fetch_assoc($res_utam6)) { 
            $kelengkapan_tolak[]=$row["ceklist_utama"];    
            }

              $val_utama1=mysqli_query($con,"SELECT COUNT(utama1)
                      FROM tb_ceklist
                      WHERE utama1=0 
                      and hasil_pemeriksaan='Di Tolak di Pos 1'");
              while($row=mysqli_fetch_assoc($val_utama1)) { 
              $val_ut1=$row["COUNT(utama1)"];            
              }

              $val_utama2=mysqli_query($con,"SELECT COUNT(utama2)
                      FROM tb_ceklist
                      WHERE utama2=0 
                      and hasil_pemeriksaan='Di Tolak di Pos 1'");
              while($row=mysqli_fetch_assoc($val_utama2)) { 
              $val_ut2=$row["COUNT(utama2)"];            
              }

              $val_utama3=mysqli_query($con,"SELECT COUNT(utama3)
                      FROM tb_ceklist
                      WHERE utama3=0 
                      and hasil_pemeriksaan='Di Tolak di Pos 1'");
              while($row=mysqli_fetch_assoc($val_utama3)) { 
              $val_ut3=$row["COUNT(utama3)"];            
              }

              $val_utama4=mysqli_query($con,"SELECT COUNT(utama4)
                      FROM tb_ceklist
                      WHERE utama4=0 
                      and hasil_pemeriksaan='Di Tolak di Pos 1'");
              while($row=mysqli_fetch_assoc($val_utama4)) { 
              $val_ut4=$row["COUNT(utama4)"];            
              }

              




?>








<div class="col">
<div id="container_6" ></div>
<script type="text/javascript">

Highcharts.chart('container_6', {
  chart: {
    type: 'bar',
    plotBackgroundColor: 'black'
  },
  title: {
    text: 'Item yang Perlu di Perbaiki'
  },
  subtitle: {
    text: ''
  },

  xAxis: {
    categories: <?php echo json_encode($kelengkapan_tolak); ?>
    
  },
  yAxis: {
    min: 0
    
    },
  
  
  
  plotOptions: {
    bar: {
      dataLabels: {
        enabled: true
      }
    }
  },

  credits: {
    enabled: false
  },
  series: [{
    name: 'Item Perbaikan',
    data: [<?php echo ($val_ut1); ?>,<?php echo ($val_ut2); ?>,<?php echo ($val_ut3); ?>,<?php echo ($val_ut4); ?>]

  }]
});


</script>
</div>



</div>













</div>
</body>
</html>