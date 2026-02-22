<?php
$seq=$_POST['seq'];
$idref=$_POST['idref'];
$ccp=$_POST['ccp'];
$nopol=$_POST['nopol'];
$petugas=$_POST['petugas'];
$lokasi=$_POST['lokasi'];



$username=User::$username;
$data_utama = mysqli_query($con,"SELECT utama".$ccp.", seq_foto from tb_ceklist where idref='$idref'");
while($row = mysqli_fetch_assoc($data_utama)){
$utama=$row["utama".$ccp.""];
$seq_foto=$row["seq_foto"];
}
if ($utama==0 AND $seq_foto==0) {
  mysqli_query($con,"UPDATE tb_ceklist SET utama".$ccp."=1 where idref='$idref'");
  mysqli_query($con,"DELETE FROM tb_foto where idref='$idref' and utama='$ccp' and username='$username'");
  header("location:main?action=gate1");
}

?>


<div class="container-fluid text-center" style="margin-top: 0px">
<div style="padding-top: 60px; margin-left: 10px">

<form method="post" action="main?action=gate1_temp"> 
<input type="text" id="temp" name="temp" value="1" hidden>
<input type="text" id="seq" name="seq" value="<?php echo $seq ?>" hidden>
<input type="text" id="idref" name="idref" value="<?php echo $idref ?>" hidden>
<input type="text" id="ccp" name="ccp" value="<?php echo $ccp ?>" hidden>
<input type="text" id="nopol" name="nopol" value="<?php echo $nopol; ?>" hidden>
<input type="text" id="petugas" name="petugas" value="<?php echo $petugas; ?>" hidden>
<input type="text" id="lokasi" name="lokasi" value="<?php echo $lokasi; ?>" hidden>

<button class="btn btn-primary">Back<span class="sr-only">(current)</span></button>
</form>



</div>

<div class="row">


  <div class="col">
  <br>
      <h2>  <label><strong>Control Point Gate 1</strong></label> </h2>

<?php
$query_item = mysqli_query($con,"SELECT ceklist_utama from tb_ceklist_utama where no='$ccp'");
while($row_item = mysqli_fetch_assoc($query_item)){
$item_utama=$row_item["ceklist_utama"];
}

?>
      <h3>  <label style="color: blue"><strong><?php echo $item_utama ?></strong></label> </h3>


  </div>
</div>




<hr>
<div class="form-group col-md-12 text-center">  
        <label><strong>Deskripsi Temuan</strong></label>
        <textarea class="form-control input-sm" style="background-color: red; color: white" type="text" id="temuan" name="temuan" onchange="ambil()"></textarea>
</div>

<hr>

<div>
    <div class="col">
      <canvas id="myCanvas" name="myCanvas" width="100" height="100" >xx</canvas>
      <!-- <img src="img/gbr.jpg" width="200" height="200"> -->
    </div>
</div>
<hr>


<div class="row">
    <div class="col" >
          <div class="icon_camera">
          <label for="upload-Image">
            <img src="static/css/img/icon_camera.png" width="70" height="70">
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
      
            <input type="text" id="idref" name="idref" value="<?php echo $idref ?>" hidden>
            <input type="text" id="ccp" name="ccp" value="<?php echo $ccp ?>" hidden>
            <input type="text" id="seq" name="seq" value="<?php echo $seq ?>" hidden>
            <input type="text" id="tem" name="tem" hidden>
            <input type="text" id="nopol" name="nopol" value="<?php echo $nopol; ?>" hidden>
            <input type="text" id="petugas" name="petugas" value="<?php echo $petugas; ?>" hidden>
            <input type="text" id="lokasi" name="lokasi" value="<?php echo $lokasi; ?>" hidden>
            <input type="text" id="item_utama" name="item_utama" value="<?php echo $item_utama ?>" hidden>


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
                xhr.open('POST', 'main?action=upload_fail', true);

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = (e.loaded / e.total) * 100;
                        console.log(percentComplete + '% uploaded');
                         alert('Succesfully uploaded');
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
            };


             function ambil(){
                var temuan=document.getElementById("temuan").value;
                    document.getElementById("tem").value=temuan;


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