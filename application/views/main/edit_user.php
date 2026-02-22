

<?php

$plant_name=$_POST['plant_name'];
$plant_id=$_POST['plant_id'];






?>


<div class="row">
<div class="col-md-2">
	<label class="form-control text-center" style="background-color: yellow; color: black; font-size: 20px">Description</label>
</div>
<div class="col-md-4">
	<label class="form-control text-center" style="background-color: yellow; color: black; font-size: 20px">Current Value</label>
</div>


</div>

<form method="post" action="main?action=reg_user"> 


<div class="row">
<div class="col-md-2">
	<label class="form-control text-center" style="background-color: blue; color: white">FULLNAME</label>
</div>
<div class="col-md-4">
	<input type="text" class="form-control" name="fullname" required ></input>
</div>

</div>



<div class="row">
<div class="col-md-2">
	<label class="form-control text-center" style="background-color: blue; color: white">NIK</label>
</div>
<div class="col-md-4">
	<input type="text" class="form-control" name="nik" required></input>
</div>

</div>


<div class="row">
<div class="col-md-2">
	<label class="form-control text-center" style="background-color: blue; color: white">PLANT NAME</label>
</div>
<div class="col-md-4">
	<input type="text" class="form-control" name="plant_name" value="<?php echo $plant_name;    ?>" required></input>
</div>

</div>


<div class="row">
<div class="col-md-2">
	<label class="form-control text-center" style="background-color: blue; color: white">PLANT ID</label>
</div>
<div class="col-md-4">
	<input type="text" class="form-control" name="plant_id" value="<?php echo $plant_id;    ?>" required></input>
</div>

</div>






</br>
</br>
<input type="text" name="no" value="<?php echo $no; ?>" hidden></input> 
<input type="text" name="save" value="1" hidden></input> 

<button type="submit" class="btn" style="background-color: green; color: white; font-size: 30px">Save</button>



</form>