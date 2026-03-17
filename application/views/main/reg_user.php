

<?php

include "application/assets/utility_function.php";



?>


<table id="fixed" class="stripe row-border" style="width:100%">

  <thead>

    <tr bgcolor=#330066 style="text-align: center;color: white" align="right"  >
      <th style="text-align: center">No</th>
      <th style="text-align: center">PLANT NAME</th>
      <th style="text-align: center">PLANT ID</th>
      <th style="text-align: center">Qty USER</th>
      <th style="text-align: center">EDIT</th>
    </tr>

  </thead>

  <tbody>

<?php
$sql = mysqli_query($con, "SELECT * from tbm_plant order by plant_name asc ");
$no=1;
foreach ($sql as $row){  ?>
    <tr style="text-align: center">
      
    

      <td style="color: white ; text-align: center"><?php echo $no; ?></td>
      <td style="color: white; text-align: center"><?php echo $row['plant_name'];?></td>
      <td style="color: white; text-align: center"><?php echo $row['plant_id'];?></td>

      <?php
      $plant_id=$row['plant_id'];

      $qty_user = mysqli_query($con, "SELECT * from tbm_user where plant_id='$plant_id' ");
      $count_user=mysqli_num_rows($qty_user);

      ?>


      <td style="color: white; text-align: center"><?php echo $count_user; ?></td>

      

      <td width="100px" style="color: white; text-align: center">
      <form method="post" action="edit_user"> 

      <input type="text" name="plant_name" value="<?php echo $row['plant_name']; ?>" hidden></input> 
      <input type="text" name="plant_id" value="<?php echo $row['plant_id']; ?>" hidden></input> 




      <button type="submit" class="btn" style="background-color: blue">Add</button>
      </form>
     
 
      
    
    </tr>
  <?php $no++;} ?>


  </tbody>
</table>
<br>


<form method="post" action="add_reg_user"> 
<input type="text" name="plant_id" value="<?php echo $nPlant; ?>" hidden></input> 
<button type="submit" class="btn" style="background-color: yellow"><strong>Add Plant</strong></button>
</form>
