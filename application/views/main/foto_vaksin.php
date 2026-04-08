<?php


$nama_driver = @$_POST['nama_driver']; 
$nik = @$_POST['nik']; 
$nama_trans = @$_POST['nama_trans']; 
$orang = @$_POST['orang']; 

$plant_name = @$_POST['plant_name']; 
$plant_id = @$_POST['plant_id']; 




$dosis = @$_POST['dosis'];



?>


<div class="container-fluid text-center" style="margin-top: 0px">
<div style="padding-top: 60px; margin-left: 10px">





</div>

<div class="row">


  <div class="col">
  <br>


      <h3>  <label style="color: blue"><strong>Foto Vaksin</strong></label> </h3>

<?php if ($orang=='driver') { ?>

      <h3>  <label style="color: blue"><strong>Nama Driver : <?php echo $nama_driver;  ?></strong></label> </h3>

<?php } ?>

<?php if ($orang=='helper') { ?>

      <h3>  <label style="color: blue"><strong>Nama Helper : <?php echo $nama_driver;  ?></strong></label> </h3>

<?php } ?>



      <h3>  <label style="color: blue"><strong>Status Vaksin : <?php echo $dosis;  ?></strong></label> </h3>


  </div>
</div>






<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
      <!-- <img src="img/gbr.jpg" width="200" height="200"> -->
    </div>
</div>
<hr>

<form action="<?= route('upload_vaksin')?>" method="post" enctype="multipart/form-data"> 
<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <?= static_img('css/img/icon_camera.png', ['width' => '70', 'height' => '70']) ?>
          </label>
          <input type="file" name="file" id="upload-Image" capture="capture" onchange="loadImageFile()"/>
          <div hidden>Origal Img - <img id="original-Img"/></div>
          <div hidden>Compress Img - <img id="upload-Preview"/></div>

          </div>
    </div>
</div>
</br>
</br>
<div class="row" >

    <div class="col" >

         <form method="post" accept-charset="utf-8" name="form1">   
      
            <input type="text" id="nama_driver" name="nama_driver" value="<?php echo $nama_driver ?>" hidden>
            <input type="text" id="nik" name="nik" value="<?php echo $nik ?>" hidden>
            <input type="text" id="nama_trans" name="nama_trans" value="<?php echo $nama_trans ?>" hidden>
            <input type="text" id="orang" name="orang" value="<?php echo $orang ?>" hidden>
            <input type="text" id="dosis" name="dosis" value="<?php echo $dosis ?>" hidden>
            <input type="text" id="plant_name" name="plant_name" value="<?php echo $plant_name ;  ?>" hidden >
            <input type="text" id="plant_id" name="plant_id" value="<?php echo $plant_id ;  ?>" hidden >
            


          <button class="btn btn-success" name="upload" id="upload" onclick="uploadEx();">Upload<span class="sr-only">(current)</span></button>

         <input name="hidden_data" id='hidden_data' type="hidden"/>
        
         </form>

      


    </div>
</div>






</div>

<script type="text/javascript">
var fileReader = new FileReader();
var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

fileReader.onload = function (event) {
  var image = new Image();
  
  image.onload=function(){
      document.getElementById("original-Img").src=image.src;
      var canvas=document.createElement("canvas");
      var context=canvas.getContext("2d");
      canvas.width=image.width/12;
      canvas.height=image.height/12;
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
        destinationCanvas.width=image.width/12;
        destinationCanvas.height=image.height/12;
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
        //var desc1 = document.getElementById('desc');




        console.log(canvas);
                var dataURL = canvas.toDataURL("image/png");
                document.getElementById('hidden_data').value = dataURL;
                var fd = new FormData(document.forms["form1"]);
 
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?=route('upload_vaksin') ?>', true);

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = (e.loaded / e.total) * 100;
                        console.log(percentComplete + '% uploaded');
                        // alert('Succesfully uploaded');
                        // window.location="cek_peritem1.php";
                        // window.location="cek_peritem1.php";
                        // window.location="cek_peritem1.php";
                        // window.location="cek_peritem1.php";
                        // window.location="cek_peritem1.php";
                        
                    }
                };
 
                xhr.onload = function() {
 
                };
                xhr.send(fd);
            }   


</script>





<style type="text/css">
  
body {
  background-color: yellow;
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





</body>
</html>