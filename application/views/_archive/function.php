<script type="text/javascript">


function utama(){
                //var data_utama1=$('input:radio[name=utama1]:checked').val();
                //var data_utama2=$('input:radio[name=utama2]:checked').val();
                //var data_utama3=$('input:radio[name=utama3]:checked').val();
                //var data_utama4=$('input:radio[name=utama4]:checked').val();

                
                
            

                
                    var data_utama=[];
                    var data_tamb=[];

                    for (var i = 1; i <= 20; i++) {
                        
                    data_utama[i]=$('input:checkbox[name=utama'+i+']:checked').val();
                    data_tamb[i]=$('input:checkbox[name=tamb'+i+']:checked').val();

                    }

                







                var datas="utama1="+data_utama[1]+"&utama2="+data_utama[2]+"&utama3="+data_utama[3]+"&utama4="+data_utama[4]+"&utama5="+data_utama[5]+"&utama6="+data_utama[6]+"&utama7="+data_utama[7]+"&utama8="+data_utama[8]+"&utama9="+data_utama[9]+"&utama10="+data_utama[10]+"&utama11="+data_utama[11]+"&utama12="+data_utama[12]+"&utama13="+data_utama[13]+"&utama14="+data_utama[14]+"&utama15="+data_utama[15]+"&utama16="+data_utama[16]+"&utama17="+data_utama[17]+"&utama18="+data_utama[18]+"&utama19="+data_utama[19]+"&utama20="+data_utama[20]+

                    "&tamb1="+data_tamb[1]+"&tamb2="+data_tamb[2]+"&tamb3="+data_tamb[3]+"&tamb4="+data_tamb[4]+"&tamb5="+data_tamb[5]+"&tamb6="+data_tamb[6]+"&tamb7="+data_tamb[7]+"&tamb8="+data_tamb[8]+"&tamb9="+data_tamb[9]+"&tamb10="+data_tamb[10]+"&tamb11="+data_tamb[11]+"&tamb12="+data_tamb[12]+"&tamb13="+data_tamb[13]+"&tamb14="+data_tamb[14]+"&tamb15="+data_tamb[15]+"&tamb16="+data_tamb[16]+"&tamb17="+data_tamb[17]+"&tamb18="+data_tamb[18]+"&tamb19="+data_tamb[19]+"&tamb20="+data_tamb[20];




                $.ajax({

                    type:"POST",
                    url:"status_utama.php",
                    data:datas,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#hasil').val(result.status_utama);
                        }
                        else {

                            $('#hasil').val(result.status_utama);

                        }

                    }

                });
                
            }




    function tambahan(){
                    //alert();
                                  
                    var data_utama=[];
                    var data_tamb=[];

                    for (var i = 1; i <= 20; i++) {
                        
                    data_utama[i]=$('input:checkbox[name=utama'+i+']:checked').val();
                    data_tamb[i]=$('input:checkbox[name=tamb'+i+']:checked').val();

                    }


                var datas="utama1="+data_utama[1]+"&utama2="+data_utama[2]+"&utama3="+data_utama[3]+"&utama4="+data_utama[4]+"&utama5="+data_utama[5]+"&utama6="+data_utama[6]+"&utama7="+data_utama[7]+"&utama8="+data_utama[8]+"&utama9="+data_utama[9]+"&utama10="+data_utama[10]+"&utama11="+data_utama[11]+"&utama12="+data_utama[12]+"&utama13="+data_utama[13]+"&utama14="+data_utama[14]+"&utama15="+data_utama[15]+"&utama16="+data_utama[16]+"&utama17="+data_utama[17]+"&utama18="+data_utama[18]+"&utama19="+data_utama[19]+"&utama20="+data_utama[20]+

                    "&tamb1="+data_tamb[1]+"&tamb2="+data_tamb[2]+"&tamb3="+data_tamb[3]+"&tamb4="+data_tamb[4]+"&tamb5="+data_tamb[5]+"&tamb6="+data_tamb[6]+"&tamb7="+data_tamb[7]+"&tamb8="+data_tamb[8]+"&tamb9="+data_tamb[9]+"&tamb10="+data_tamb[10]+"&tamb11="+data_tamb[11]+"&tamb12="+data_tamb[12]+"&tamb13="+data_tamb[13]+"&tamb14="+data_tamb[14]+"&tamb15="+data_tamb[15]+"&tamb16="+data_tamb[16]+"&tamb17="+data_tamb[17]+"&tamb18="+data_tamb[18]+"&tamb19="+data_tamb[19]+"&tamb20="+data_tamb[20];




                $.ajax({

                    type:"POST",
                    url:"status_tambahan.php",
                    data:datas,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#hasil').val(result.status_utama);
                        }
                        else {

                            $('#hasil').val(result.status_utama);

                        }

                    }

                });
                
            }     



    function tambahan_qa(){
                
                    var data_qa=[];

                    for (var i = 1; i <= 10; i++) {
                        
                    data_qa[i]=$('input:checkbox[name=qa'+i+']:checked').val();

                    }


                var datas="qa1="+data_qa[1]+"&qa2="+data_qa[2]+"&qa3="+data_qa[3]+"&qa4="+data_qa[4]+"&qa5="+data_qa[5]+"&qa6="+data_qa[6]+"&qa7="+data_qa[7]+"&qa8="+data_qa[8]+"&qa9="+data_qa[9]+"&qa10="+data_qa[10];




                $.ajax({

                    type:"POST",
                    url:"status_tambahan_qa.php",
                    data:datas,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#hasil').val(result.status_utama);
                        }
                        else {

                            $('#hasil').val(result.status_utama);

                        }

                    }

                });
                
            }        






     function all_cek(){
                
                    var data_utama=[];
                    var data_tamb=[];

                    for (var i = 1; i <= 20; i++) {
                        
                    data_utama[i]=$('input:checkbox[name=utama'+i+']:checked').val();
                    data_tamb[i]=$('input:checkbox[name=tamb'+i+']:checked').val();

                    }


                var datas="utama1="+data_utama[1]+"&utama2="+data_utama[2]+"&utama3="+data_utama[3]+"&utama4="+data_utama[4]+"&utama5="+data_utama[5]+"&utama6="+data_utama[6]+"&utama7="+data_utama[7]+"&utama8="+data_utama[8]+"&utama9="+data_utama[9]+"&utama10="+data_utama[10]+"&utama11="+data_utama[11]+"&utama12="+data_utama[12]+"&utama13="+data_utama[13]+"&utama14="+data_utama[14]+"&utama15="+data_utama[15]+"&utama16="+data_utama[16]+"&utama17="+data_utama[17]+"&utama18="+data_utama[18]+"&utama19="+data_utama[19]+"&utama20="+data_utama[20]+

                    "&tamb1="+data_tamb[1]+"&tamb2="+data_tamb[2]+"&tamb3="+data_tamb[3]+"&tamb4="+data_tamb[4]+"&tamb5="+data_tamb[5]+"&tamb6="+data_tamb[6]+"&tamb7="+data_tamb[7]+"&tamb8="+data_tamb[8]+"&tamb9="+data_tamb[9]+"&tamb10="+data_tamb[10]+"&tamb11="+data_tamb[11]+"&tamb12="+data_tamb[12]+"&tamb13="+data_tamb[13]+"&tamb14="+data_tamb[14]+"&tamb15="+data_tamb[15]+"&tamb16="+data_tamb[16]+"&tamb17="+data_tamb[17]+"&tamb18="+data_tamb[18]+"&tamb19="+data_tamb[19]+"&tamb20="+data_tamb[20];




                $.ajax({

                    type:"POST",
                    url:"status_all.php",
                    data:datas,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#hasil').val(result.status_utama);
                        }
                        else {

                            $('#hasil').val(result.status_utama);

                        }

                    }

                });
                
            }       
               
     
    


	function kode(){
                var datadb="kode="+$("#kendaraan").val();
                $.ajax({

                    type:"POST",
                    url:"cari.php",
                    data:datadb,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#nopol').val(result.namai);
                            $('#tipe_truck').val(result.namai1);
                            $('#tahun').val(result.namai2);
                        }
                        else {

                            alert(result.namai);
                            $('#nopol').val(result.namai);

                        }

                    }

                });
                
            }

    function carisopir(){
                var datadb1="sopir="+$("#kode_sopir").val();
                $.ajax({

                    type:"POST",
                    url:"cari_sopir.php",
                    data:datadb1,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            $('#nama_sopir').val(result.nami);
                            $('#nama_transporter').val(result.nami1);
                        }
                        else {

                            alert(result.nami);
                            $('#nama_sopir').val(result.nami);

                        }

                    }

                });
                
            }


    function simpan(){


                var kode_kendaraan=$("#kendaraan").val();
                var kode_sopir=$("#kode_sopir").val();
                var petugas=$("#petugas").val();
                var tujuan=$("#tujuan").val();
                var nopol=$("#nopol").val();
                var nama_transporter=$("#nama_transporter").val();
                var nama_sopir=$("#nama_sopir").val();
                var tipe_truck=$("#tipe_truck").val();
                var tahun=$("#tahun").val();
                var tgl=$("#tgl").val();
                var jam=$("#jam").val();
                var lokasi=$("#lokasi").val();
                var hasil=$("#hasil").val();
                var komentar=$("#komentar").val();
                var tindakan=$("#tindakan").val();
                var utama1=$("#utama1").val();

                if (nopol=='') {
                    alert('Nopol belum di isi');
                    location.reload();
                    exit;}
                if (nama_sopir=='') {
                    alert('Nama Sopir belum di isi');
                    location.reload();
                    exit;}
                if (nama_transporter=='') {
                    alert('Transporter belum di isi');
                    location.reload();
                    exit;}
                if (hasil=='') {
                    alert('Anda belum CekList');
                    location.reload();
                    exit;}    




           //     var utama1=$('input:radio[name=ban_layak]:checked').val();
            //    var utama2=$('input:radio[name=rem_layak]:checked').val();
            //    var utama3=$('input:radio[name=rem_tangan_layak]:checked').val();
            //    var utama4=$('input:radio[name=ddt]:checked').val();
            //    var utama5=$('input:radio[name=utama5]:checked').val();
            //    var utama6=$('input:radio[name=utama6]:checked').val();
            //    var utama7=$('input:radio[name=utama7]:checked').val();





             //   var tamb1=$('input:radio[name=tamb1]:checked').val();
              //  var tamb2=$('input:radio[name=tamb2]:checked').val();
             //   var tamb3=$('input:radio[name=tamb3]:checked').val();
             //   var tamb4=$('input:radio[name=tamb4]:checked').val();




                    var utama=[];
                    var tamb=[];

                    for (var a = 1; a <= 20; a++) {
                        
                    utama[a]=$('input:checkbox[name=utama'+a+']:checked').val();
                    tamb[a]=$('input:checkbox[name=tamb'+a+']:checked').val();

                    }

                    







                var datastring="kode_kendaraan="+kode_kendaraan+"&kode_sopir="+kode_sopir+"&petugas="+petugas+"&tujuan="+tujuan+"&nopol="+nopol+"&nama_transporter="+nama_transporter+"&nama_sopir="+nama_sopir+"&tipe_truck="+tipe_truck+"&tahun="+tahun+"&tgl="+tgl+"&jam="+jam+"&lokasi="+lokasi
                    +"&utama1="+utama1+"&utama2="+utama[2]+"&utama3="+utama[3]+"&utama4="+utama[4]+"&utama5="+utama[5]+"&utama6="+utama[6]+"&utama7="+utama[7]+"&utama8="+utama[8]+"&utama9="+utama[9]+"&utama10="+utama[10]+"&utama11="+utama[11]+"&utama12="+utama[12]+"&utama13="+utama[13]+"&utama14="+utama[14]+"&utama15="+utama[15]+"&utama16="+utama[16]+"&utama17="+utama[17]+"&utama18="+utama[18]+"&utama19="+utama[19]+"&utama20="+utama[20]


                    +"&tamb1="+tamb[1]+"&tamb2="+tamb[2]+"&tamb3="+tamb[3]+"&tamb4="+tamb[4]+"&tamb5="+tamb[5]+"&tamb6="+tamb[6]+"&tamb7="+tamb[7]+"&tamb8="+tamb[8]+"&tamb9="+tamb[9]+"&tamb10="+tamb[10]+"&tamb11="+tamb[11]+"&tamb12="+tamb[12]+"&tamb13="+tamb[13]+"&tamb14="+tamb[14]+"&tamb15="+tamb[15]+"&tamb16="+tamb[16]+"&tamb17="+tamb[17]+"&tamb18="+tamb[18]+"&tamb19="+tamb[19]+"&tamb20="+tamb[20]+"&hasil="+hasil+"&komentar="+komentar+"&tindakan="+tindakan;



                    



                //alert(datastring);
                //return false;

                $.ajax({

                    type:"POST",
                    url:"simpan_db.php",
                    data:datastring,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            //$('#nama_baca1').val(result.namai);
                            
                            
                            alert("Berhasil Simpan");
                            location.reload();

                        }
                        else {

                            alert("gagal simpan");
                            //$('#nama_baca1').val(result.namai);
                        }

                    }

                });
                
            }




function simpan_gate(){

                var code=$("#code").val();
                var hasil=$("#hasil").val();
                var komentar=$("#komentar").val();
                var tindakan=$("#tindakan").val();

                    var utama=[];
                    var tamb=[];

                    for (var a = 1; a <= 20; a++) {
                        
                    utama[a]=$('input:checkbox[name=utama'+a+']:checked').val();
                    tamb[a]=$('input:checkbox[name=tamb'+a+']:checked').val();

                    }


                var datastring="kode_kendaraan="+kode_kendaraan+"&kode_sopir="+kode_sopir+"&petugas="+petugas+"&tujuan="+tujuan+"&nopol="+nopol+"&nama_transporter="+nama_transporter+"&nama_sopir="+nama_sopir+"&tipe_truck="+tipe_truck+"&tahun="+tahun+"&tgl="+tgl+"&jam="+jam+"&lokasi="+lokasi
                    +"&utama1="+utama[1]+"&utama2="+utama[2]+"&utama3="+utama[3]+"&utama4="+utama[4]+"&utama5="+utama[5]+"&utama6="+utama[6]+"&utama7="+utama[7]+"&utama8="+utama[8]+"&utama9="+utama[9]+"&utama10="+utama[10]+"&utama11="+utama[11]+"&utama12="+utama[12]+"&utama13="+utama[13]+"&utama14="+utama[14]+"&utama15="+utama[15]+"&utama16="+utama[16]+"&utama17="+utama[17]+"&utama18="+utama[18]+"&utama19="+utama[19]+"&utama20="+utama[20]


                    +"&tamb1="+tamb[1]+"&tamb2="+tamb[2]+"&tamb3="+tamb[3]+"&tamb4="+tamb[4]+"&tamb5="+tamb[5]+"&tamb6="+tamb[6]+"&tamb7="+tamb[7]+"&tamb8="+tamb[8]+"&tamb9="+tamb[9]+"&tamb10="+tamb[10]+"&tamb11="+tamb[11]+"&tamb12="+tamb[12]+"&tamb13="+tamb[13]+"&tamb14="+tamb[14]+"&tamb15="+tamb[15]+"&tamb16="+tamb[16]+"&tamb17="+tamb[17]+"&tamb18="+tamb[18]+"&tamb19="+tamb[19]+"&tamb20="+tamb[20]+"&hasil="+hasil+"&komentar="+komentar+"&tindakan="+tindakan+"&code="+code;



                    



                //alert(datastring);
                //return false;

                $.ajax({

                    type:"POST",
                    url:"simpan_db_gate2.php",
                    data:datastring,
                    cache:false,
                    dataType:'json',
                    success: function(result){

                        if (result.status=='ok') {
                            //$('#nama_baca1').val(result.namai);
                            
                            
                            alert("Berhasil Simpan");
                            location.reload();

                        }
                        else {

                            alert("gagal simpan");
                            //$('#nama_baca1').val(result.namai);
                        }

                    }

                });
                
            }





</script>


<?php
function get_date(){
                echo date("d-m-Y");
            }

function get_jam(){
                echo date("H:i:s");
            }



?>