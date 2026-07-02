<?php
include "application/config/connectionSL.php";
include "application/assets/utility_function.php";
//---------------------------------------------------------------------------------------------------------
//                                          Master Data
//---------------------------------------------------------------------------------------------------------


//---------------------------------------------------------------------------------------------------------
//                                          FILTER
//---------------------------------------------------------------------------------------------------------

$TGLSTART    = getPreValueGet("TGLSTART",date("Y-m-d"));
$TGLEND    = getPreValueGet("TGLEND",date("Y-m-d"));

$TIPE    = getPreValueGet("TIPE","");
$CARI    = getPreValueGet("CARI","");
$MUATAN    = getPreValueGet("MUATAN","");

$muatan_str = "TRUE";
if ($MUATAN !='')
    $muatan_str  = "muatan = '{$MUATAN}'";

$table  = new Table("tbl_checklist");
$data   = $table->get()->where("
        tgl_pemeriksaan between '{$TGLSTART}' 
        AND '{$TGLEND}' 
        AND {$muatan_str} 
        ")->fetchAll();
?>

<link rel="stylesheet" href="<?= BASE_URL?>/static/css/card-custom.css">
<style>
    #print { margin-right: 20px; margin-left: 10px; }
    .MERAH { color: red; }
    .uom { background #EEF8FD; }
    body{
        background-color:white;
    }
    .container-fluid{
        MARG
    }
</style>
<div class="card">
        <div class="card-body">
            
            <div class='card_header row'>
                <form method="GET">
                    <div class='col-lg-12' style='vertical-align: middle;'>
                        
                        <div class='col-lg-4'>
                           
                            <div class='col-lg-6'>
                                <input type="date" class='form-control filter_data' title='Tanggal Rencana' name="TGLSTART" value="<?=$TGLSTART?>"  >
                            </div>
                            <div class='col-lg-6'>
                                <input type="date" class='form-control filter_data' title='Tanggal Rencana' name="TGLEND" value="<?=$TGLEND?>" >
                            </div>
                        </div>
                        
                        <div class='col-lg-2'>
                        
                            <select class='form-control filter_data' title='MUATAN' name="MUATAN" id="MUATAN" value="<?=$MUATAN?>" >
                                <option value='FG'      <?=$MUATAN =='FG' ? 'selected':''?>>FG </option>
                                <option value='MATERIAL' <?=$MUATAN =='MATERIAL' ? 'selected':''?>>Material </option>
                                <option value=''         <?=$MUATAN =='' ? 'selected':''?>>ALL </option>

                            </select>
                        </div>
                        <!-- <div class='col-lg-2'>
                          
                            <input type='text' placeholder='cari...' class='form-control' name = "CARI"> 
                        </div> -->
                        <div class='col-lg-4'>
                            <button type="submit" class="btn btn-primary form-control"><i class="fa fa-search"> </i></button>
                        </div>
                    </div>        
                </form>           
            </div>
    
                
            <div class='card_body'>
                    
                <table class="table-master">
                    <thead>
                        <tr>
                            <th class="text-left" >No</th>
                            <th class="text-left" >Petugas pemeriksaan</th>
                            <th class="text-left" >Waktu pemeriksaan</th>
                            <th class="text-left" >No pol</th>
                            <th class="text-left" >No CO / DN</th>
                            <th class="text-left" >Muatan</th>
                            <th class="text-left" >Supplier</th>
                            <th class="text-left" >Transporter</th>
                            <th class="text-left" >Status</th>
                            <th class="text-left" ></th>
                        </tr>
                    </thead>
                    <tbody style='color:black!important'>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?=$row['no']?></td>
                                <td><?=$row['petugas_pemeriksa']?></td>
                                <td><?=$row['tgbaca']?></td>
                                <td><?=$row['nopol']?></td>
                                <td><?=$row['no_po']?></td>
                                <td><?=$row['muatan']?></td>
                                <td><?=$row['kode_supplier']?> - <?=$row['nama_supplier']?></td>
                                <td><?=$row['kode_transporter']?> - <?=$row['nama_transporter']?></td>
                                <td><?=$row['hasil_pemeriksaan']!=''?$row['hasil_pemeriksaan']:'-'?></td>
                                <td><button class='btn btn-danger' onclick='del("<?=$row['no']?>")'>Delete </button></td>
                            </tr>
                        <?php endforeach;?>                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function del(id){
        $.post("<?=route("report_api",['action'=>"delete"])?>", 
            {
                id:id,
                csrf: '<?=$a?>'

            }, function(result){
                // location.reload()
            }
        )
    }
</script>