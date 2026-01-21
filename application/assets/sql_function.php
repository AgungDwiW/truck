<?php
/*
# Project: BlueFish 1.0
# Auth   : Dyah PP Wardhana (c) DamarTeduh.2018
# Create : 2018-09-29 03:24 AM
# Ket 	 :
# Rev 	 :
	20181025 04:24 AM [12] Penambahan akses level
*/

date_default_timezone_set("Asia/Jakarta");

# Call week dari database
function getWeek($start_week,$end_week){
	global $con;
	$str=sprintf("SELECT WEEK('%s',4) startWk, WEEK('%s',4) endWk",$start_week,$end_week);
	$result = mysqli_query($con, $str);
	$rangeWk=mysqli_fetch_assoc($result);
	return $rangeWk;
}

# Query akses level user
function getAkses(){
	global $con;
	// global $action;
	// global $controller;
	// $crud=array("insert"=>"N", "edit"=>"N", "delete"=>"N");
	// $str = sprintf("SELECT * FROM tbl_akses WHERE username='%s' AND modul='%s' AND action='%s'",$_SESSION[APP_NAME]["username"],$controller,$action);
	$str = sprintf("SELECT * FROM tbl_akses WHERE username='%s'",$_SESSION[APP_NAME]["username"]);
	$result = mysqli_query($con,$str);
	while ($row=mysqli_fetch_assoc($result)) {
		// $crud["insert"]=$row["buat"];
		// $crud["edit"]  =$row["rubah"];
		// $crud["delete"]=$row["hapus"];
		$crud[$row['modul']][$row['action']]["insert"]=$row["buat"];
		$crud[$row['modul']][$row['action']]["edit"]=$row["rubah"];
		$crud[$row['modul']][$row['action']]["delete"]=$row["hapus"];
	};
	mysqli_free_result($result);
	return $crud;
}

/* 	Contoh penggunaan
	------------------------------------------
	$json_data = '{
		"table" : "tbm_uom",
		"fields": ["uom_code", "uom_code"],
		"where" : "aktif=\'Y\'"
		}';
	$mUom = getMasterData($json_data);
*/
function getMasterData($jsondata){
	global $con;
	$data = json_decode($jsondata, true);
	$cari = isset($data["where"])?" WHERE ".$data["where"]:"";
	$str=sprintf("SELECT %s, %s FROM %s %s",$data["fields"][0],$data["fields"][1],$data["table"],$cari);
	$result=mysqli_query($con, $str);
	while($row=mysqli_fetch_assoc($result)){ $row_data[$row[$data["fields"][0]]]=$row[$data["fields"][1]]; }
	if(isset($row_data)) return $row_data;
}

function callAlert($str){
	return "<div style='margin-top: 20px;padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> ".$str."</div>";
}

function isEmpty(){
	$kode = "<div style='margin-top: 20px;padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> Data not available</div>";
	return $kode;
}

function sendQry($qry,$redirect){
	global $con;
	mysqli_query($con,$qry);
   	mysqli_close($con);
   	if($redirect!="")  	header(sprintf("location:%s",$redirect));
}

?>