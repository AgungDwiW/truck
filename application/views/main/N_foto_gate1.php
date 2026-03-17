
<?php
$utama=$_POST['utama'];
$idref=$_POST['idref'];
$ceklist=$_POST['ceklist'];
$ccp=$_POST['ceklist'];
$nopol=$_POST['nopol'];
$lokasi=$_POST['lokasi'];
$tambah_foto=@$_POST['tambah_foto'];
$kode_kirim=$_POST['kode_kirim'];
$driver       = $_POST['driver'];
$supplier     = $_POST['supplier'];
$temuan='';


$utama1=1;
$utama2=1;
$utama3=1;
$utama4=1;


if($tambah_foto<>1) {

$cek_utama = mysqli_query($con,"  SELECT * from tb_ceklist where idref='$idref' ");
while($row1 = mysqli_fetch_assoc($cek_utama)){
          $utama1=$row1["utama1"];
          $utama2=$row1["utama2"];
          $utama3=$row1["utama3"];
          $utama4=$row1["utama4"];
     

          }

        if ($utama==1 and $utama1==0) { 

          mysqli_query($con,"UPDATE tb_ceklist SET utama1=1  where idref='$idref' ");  ?>


          <div class="kotak_sq">
          <form method="post" action="N_gate1"> 
             <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
             <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
             <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
             <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
             <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
             <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

             <h2><strong><label style="color: red" class="label center-block">Ceklist akan dikembalikan ke PASS...!!!</label></strong></h2> 

             <button type="submit" class="btn btn-success tombol_ic center-block">OK</button>
          </form>
          </div>

                <?php  }  ?> 



         <?php       

         if ($utama==2 and $utama2==0) { 

          mysqli_query($con,"UPDATE tb_ceklist SET utama2=1  where idref='$idref' ");  ?>


          <div class="kotak_sq">
          <form method="post" action="N_gate1"> 
             <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
             <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
             <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
             <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
             <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
             <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

             <h2><strong><label style="color: red" class="label center-block">Ceklist akan dikembalikan ke PASS...!!!</label></strong></h2> 

             <button type="submit" class="btn btn-success tombol_ic center-block">OK</button>
          </form>
          </div>

                <?php  }  ?>              



          <?php       

         if ($utama==3 and $utama3==0) { 

          mysqli_query($con,"UPDATE tb_ceklist SET utama3=1  where idref='$idref' ");  ?>


          <div class="kotak_sq">
          <form method="post" action="N_gate1"> 
             <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
             <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
             <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
             <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
             <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
             <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

             <h2><strong><label style="color: red" class="label center-block">Ceklist akan dikembalikan ke PASS...!!!</label></strong></h2> 

             <button type="submit" class="btn btn-success tombol_ic center-block">OK</button>
          </form>
          </div>

                <?php  }  ?>  







          <?php       

         if ($utama==4 and $utama4==0) { 

          mysqli_query($con,"UPDATE tb_ceklist SET utama4=1  where idref='$idref' ");  ?>


          <div class="kotak_sq">
          <form method="post" action="N_gate1"> 
             <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
             <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
             <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
             <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
             <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
             <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

             <h2><strong><label style="color: red" class="label center-block">Ceklist akan dikembalikan ke PASS...!!!</label></strong></h2> 

             <button type="submit" class="btn btn-success tombol_ic center-block">OK</button>
          </form>
          </div>

                  <?php  } }  ?>                







<?php

if ($utama==1 and $utama1<>0) {  ?>

<script type="text/javascript">window.refresh();</script>

<div class="container-fluid text-center">



<form method="post" action="N_gate1"> 
   <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
   <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
   <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
   <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
   <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
   <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

   <button type="submit" class="btn btn-danger">BACK</button>
</form>

<div class="row">
  <div class="col">
  <br>
  <h2>  <label style="color: blue"><strong>Control Check Point</strong></label> </h2>
  </div>
</div>


<hr>

<div class="form-group col-md-12 text-center">  
       <h2><label style="color: green"><strong>Temuan</strong></label></h2>  
        <h2><label><strong><?php echo $ceklist;?></strong></label></h2>  
        <textarea class="form-control input-sm" style="background-color: red; color: white" type="text" id="temuan" name="temuan"  onchange="ambil()"> </textarea>
</div>

<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
    </div>
</div>
<hr>
<form action="N_upload_fail" method="post" enctype="multipart/form-data"> 

<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <img src="static/css/img/icon_camera.png" width="70" height="70">
          </label>
          <input type="file" name="file" id="upload-Image" capture="capture"  onchange="loadImageFile()"/>
          <div hidden>Origal Img - <img id="original-Img"/></div>
          <div hidden>Compress Img - <img id="upload-Preview"/></div>

          </div>
    </div>
</div>

<div class="row" >
    <div class="col" >
          
            <input type="text" id="tem" name="tem" value="<?php echo $temuan; ?>" hidden>
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden> 
            <input type="text" name="ccp" value="<?php echo $ccp; ?>" hidden> 
            <input type="text" name="utama" value="<?php echo $utama; ?>" hidden> 
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden> 
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden> 
            <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 
          
  
          

         <div class="fileUpload btn btn-success">   
         <span>Upload</span>
         <input class="upload" type="submit" name="upload" id="upload">  
         <input name="hidden_data" id='hidden_data' type="hidden"/>
         </div>



         </form>
    
    </div>
</div>


</div>




<?php } ?>


<?php

if ($utama==2 and $utama2<>0) {  ?>

<script type="text/javascript">window.refresh();</script>

<div class="container-fluid text-center">



<form method="post" action="N_gate1"> 
   <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
   <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
   <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
   <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
   <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
   <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

   <button type="submit" class="btn btn-danger">BACK</button>
</form>

<div class="row">
  <div class="col">
  <br>
  <h2>  <label style="color: blue"><strong>Control Check Point</strong></label> </h2>
  </div>
</div>


<hr>

<div class="form-group col-md-12 text-center">  
       <h2><label style="color: green"><strong>Temuan</strong></label></h2>  
        <h2><label><strong><?php echo $ceklist;?></strong></label></h2>  
        <textarea class="form-control input-sm" style="background-color: red; color: white" type="text" id="temuan" name="temuan"  onchange="ambil()"></textarea>
</div>

<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
    </div>
</div>
<hr>
<form action="N_upload_fail" method="post" enctype="multipart/form-data"> 

<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <img src="static/css/img/icon_camera.png" width="70" height="70">
          </label>
          <input type="file" name="file" id="upload-Image" capture="capture"  onchange="loadImageFile()"/>
          <div hidden>Origal Img - <img id="original-Img"/></div>
          <div hidden>Compress Img - <img id="upload-Preview"/></div>

          </div>
    </div>
</div>

<div class="row" >
    <div class="col" >
          
            <input type="text" id="tem" name="tem" value="<?php echo $temuan; ?>" hidden>
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden> 
            <input type="text" name="ccp" value="<?php echo $ccp; ?>" hidden> 
            <input type="text" name="utama" value="<?php echo $utama; ?>" hidden> 
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden> 
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden> 
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 
          
  
          

         <div class="fileUpload btn btn-success">   
         <span>Upload</span>
         <input class="upload" type="submit" name="upload" id="upload">  
         <input name="hidden_data" id='hidden_data' type="hidden"/>
         </div>



         </form>
    
    </div>
</div>


</div>




<?php } ?>



<?php

if ($utama==3 and $utama3<>0) {  ?>

<script type="text/javascript">window.refresh();</script>

<div class="container-fluid text-center">



<form method="post" action="N_gate1"> 
   <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
   <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
   <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
   <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
   <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
   <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

   <button type="submit" class="btn btn-danger">BACK</button>
</form>

<div class="row">
  <div class="col">
  <br>
  <h2>  <label style="color: blue"><strong>Control Check Point</strong></label> </h2>
  </div>
</div>


<hr>

<div class="form-group col-md-12 text-center">  
       <h2><label style="color: green"><strong>Temuan</strong></label></h2>  
        <h2><label><strong><?php echo $ceklist;?></strong></label></h2>  
        <textarea class="form-control input-sm" style="background-color: red; color: white" type="text" id="temuan" name="temuan"  onchange="ambil()"></textarea>
</div>

<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
    </div>
</div>
<hr>
<form action="N_upload_fail" method="post" enctype="multipart/form-data"> 

<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <img src="static/css/img/icon_camera.png" width="70" height="70">
          </label>
          <input type="file" name="file" id="upload-Image" capture="capture"  onchange="loadImageFile()"/>
          <div hidden>Origal Img - <img id="original-Img"/></div>
          <div hidden>Compress Img - <img id="upload-Preview"/></div>

          </div>
    </div>
</div>

<div class="row" >
    <div class="col" >
          
            <input type="text" id="tem" name="tem" value="<?php echo $temuan; ?>" hidden>
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden> 
            <input type="text" name="ccp" value="<?php echo $ccp; ?>" hidden> 
            <input type="text" name="utama" value="<?php echo $utama; ?>" hidden> 
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden> 
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden> 
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 
          
  
          

         <div class="fileUpload btn btn-success">   
         <span>Upload</span>
         <input class="upload" type="submit" name="upload" id="upload">  
         <input name="hidden_data" id='hidden_data' type="hidden"/>
         </div>



         </form>
    
    </div>
</div>


</div>




<?php } ?>




<?php

if ($utama==4 and $utama4<>0) {  ?>

<script type="text/javascript">window.refresh();</script>

<div class="container-fluid text-center">



<form method="post" action="N_gate1"> 
   <input type="text" name="idref" value="<?php echo $idref; ?>" hidden></input> 
   <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden></input> 
   <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden></input> 
   <input type="text" name="kode_kirim" value="<?php echo $kode_kirim; ?>" hidden >
   <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
   <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 

   <button type="submit" class="btn btn-danger">BACK</button>
</form>

<div class="row">
  <div class="col">
  <br>
  <h2>  <label style="color: blue"><strong>Control Check Point</strong></label> </h2>
  </div>
</div>


<hr>

<div class="form-group col-md-12 text-center">  
       <h2><label style="color: green"><strong>Temuan</strong></label></h2>  
        <h2><label><strong><?php echo $ceklist;?></strong></label></h2>  
        <textarea class="form-control input-sm" style="background-color: red; color: white" type="text" id="temuan" name="temuan"  onchange="ambil()"></textarea>
</div>

<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
    </div>
</div>
<hr>
<form action="N_upload_fail" method="post" enctype="multipart/form-data"> 

<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <img src="static/css/img/icon_camera.png" width="70" height="70">
          </label>
          <input type="file" name="file" id="upload-Image" capture="capture"  onchange="loadImageFile()"/>
          <div hidden>Origal Img - <img id="original-Img"/></div>
          <div hidden>Compress Img - <img id="upload-Preview"/></div>

          </div>
    </div>
</div>

<div class="row" >
    <div class="col" >
          
            <input type="text" id="tem" name="tem" value="<?php echo $temuan; ?>" hidden>
            <input type="text" name="idref" value="<?php echo $idref; ?>" hidden> 
            <input type="text" name="ccp" value="<?php echo $ccp; ?>" hidden> 
            <input type="text" name="utama" value="<?php echo $utama; ?>" hidden> 
            <input type="text" name="nopol" value="<?php echo $nopol; ?>" hidden> 
            <input type="text" name="lokasi" value="<?php echo $lokasi; ?>" hidden> 
            <input type="text" name="driver" value="<?php echo $driver; ?>" hidden></input> 
            <input type="text" name="supplier" value="<?php echo $supplier; ?>" hidden></input> 
          
  
          

         <div class="fileUpload btn btn-success">   
         <span>Upload</span>
         <input class="upload" type="submit" name="upload" id="upload">  
         <input name="hidden_data" id='hidden_data' type="hidden"/>
         </div>



         </form>
    
    </div>
</div>


</div>




<?php } ?>



















<script type="text/javascript">
var fileReader = new FileReader();
var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

fileReader.onload = function (event) {
  var image = new Image();
  
  image.onload=function(){
      document.getElementById("original-Img").src=image.src;
      var canvas=document.createElement("canvas");
      var context=canvas.getContext("2d");
      canvas.width=image.width/10;
      canvas.height=image.height/10;
      context.drawImage(image,
          0,
          0,
          image.width,
          image.height,
          0,
          0,
          canvas.width,
          canvas.height
      );
      
      document.getElementById("upload-Preview").src = canvas.toDataURL();

      
    var destinationCanvas = document.getElementById("myCanvas");
        destinationCanvas.width=image.width/10;
        destinationCanvas.height=image.height/10;
    var destCtx = destinationCanvas.getContext('2d');
    destCtx.drawImage(image,
          0,
          0,
          image.width,
          image.height,
          0,
          0,
          canvas.width,
          canvas.height);

  }
  image.src=event.target.result;
};

var loadImageFile = function () {
  var uploadImage = document.getElementById("upload-Image");
  
  //check and retuns the length of uploded file.
  if (uploadImage.files.length === 0) { 
    return; 
  }
  
  //Is Used for validate a valid file.
  var uploadFile = document.getElementById("upload-Image").files[0];
  if (!filterType.test(uploadFile.type)) {
    alert("Please select a valid image."); 
    return;
  }
  
  fileReader.readAsDataURL(uploadFile);
}
</script>



<script>
            function uploadEx() {
              






        var canvas = document.getElementById('myCanvas');
        console.log(canvas);
                var dataURL = canvas.toDataURL("image/png");
                document.getElementById('hidden_data').value = dataURL;
                var fd = new FormData(document.forms["form1"]);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'N_upload_fail', true);
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = (e.loaded / e.total) * 100;
                        console.log(percentComplete + '% uploaded');
                        
                    }
                };

    
                xhr.onload = function() {
 
                };
                xhr.send(fd);



            };




              function ambil(){
                var temuan=document.getElementById("temuan").value;
                    document.getElementById("tem").value=temuan;


              }  

              function klik(){
                var xxx = document.getElementById("upload");
                xxx.disabled=true;


              }
              function klikk(){
                var xxx = document.getElementById("back");
                xxx.disabled=true;


              }


</script>



<style type="text/css">
  
body {
  background-color: yellow;
}

.tombol_ic{
  color: white;
  font-size: 20pt;
  width: 200px;
  height: 80px;
  border: none;
  border-radius: 3px;
  padding: 20px 20px;
  margin-top: 100px;
}

.fileUpload {
    position: relative;
    overflow: hidden;
    margin: 10px;
}
.fileUpload input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
}
.icon_camera > input
{
  display:none;
  left: 3px

}

div.relative {
    position: relative;
    left: 4px;
    top: 23px;
  }

div.cekmark {
    left: 0px;
  }

</style> 
