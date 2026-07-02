<?= static_css('css/pikaday.css') ?>
<?php
/*
# Auth  : Dyah PP Wardhana (c) DamarTeduh 2018
# Create: 2018-07-28 01:25 AM
# Ket   : Function pilih periode tanggal
# Rev   : 
*/



# Hanya di pakai di VMI Project
function cmbMinggu($name,$id,$cls,$ref,$set,$start_week,$end_week){
	$kode='<select name="'.$name.'" class="'.$cls.'" id="'.$id.'" '.$set.' >'."\n";
	for($i=$start_week;$i<=$end_week;$i++){
		$kode .='<option value="'.$i.'"';
		if(trim($i)==trim($ref)) {
			  $kode .=' selected';
			}
		$kode .='>'.$i.'</option>'."\n";
		}
	$kode .='</select>'."\n";
	return $kode;
}

function dataEmpty($result, $comment){
	if(mysqli_num_rows($result)==0){
		$kode = "<div style='margin-top: 20px;padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> ".$comment."</div>";
	}
	else {
		$kode = "";
	}
		return $kode;
}

function scrollBawah(){
	$kode  = '<button id="scrollToBottom" OnClick="scrollToBottom();"><i class="fa fa-arrow-circle-down"></i></button>';
	$kode .= '<script type="text/javascript">function scrollToBottom(){var elmnt = document.getElementById("submitProposed");
	elmnt.scrollIntoView();}</script>';
	return $kode;
}

function formClosed($reference){
	$kode  = '<a class="btnClosed" href="'.$reference.'"><i class="icon-cancel"></i></a>';
	return $kode;
}

/*function tglPicker($name,$class,$id,$tglSelect,$enab){
	$kode ='<input type="text" class="'.$class.'" name="'.$name.'" id="'.$id.'" value="'.$tglSelect.'" '.$enab.' readonly>';
	$kode.= '<script type="text/javascript">$(document).ready(function(){$("#'.$id.'").datepicker({
		dateFormat : "yy-mm-dd",firstDay: 1,changeMonth : true,changeYear : true});});</script>';
	return $kode;
}*/


function tglPicker($name,$tglSelect){
	$kode ='<i class="fa fa-calendar"></i> <input type="text" name="'.$name.'" id="start" value="'.$tglSelect.'" readonly>';
	$kode .= '<script type="text/javascript">var startDate,	updateStartDate = function() {startPicker.setStartRange(startDate);}, startPicker = new Pikaday({field: document.getElementById("start"), format: "YYYY-MM-DD", onSelect: function(){startDate = this.getDate();updateStartDate();} }), _startDate = startPicker.getDate(); if (_startDate){startDate = _startDate;updateStartDate();}</script>';
	return $kode;
}

function cmbBox($name,$class,$id,$ref,$dataCat,$disabled){
	$kode='<select class="'.$class.'" name="'.$name.'" id="'.$id.'" '.$disabled.'>'."\n";
	foreach ($dataCat AS $row) {
		$kode .='<option value="'.key($dataCat).'"';
		if(key($dataCat)==$ref) {
        	$kode .=' selected';
        }
		$kode .='>'.$row.'</option>'."\n";
		next($dataCat);
		}
		$kode .='</select>'."\n";
		return $kode;
}

function cmbTahun($name,$id,$cls,$ref,$set,$start){
  $kode='<select name="'.$name.'" class="'.$cls.'" id="'.$id.'" '.$set.' >'."\n";
  for($i = $start; $i < date("Y")+1; $i++){
    $kode .='<option value="'.$i.'"';
    if(trim($i)==trim($ref)) {
          $kode .=' selected';
        }
    $kode .='>'.$i.'</option>'."\n";
    }
  $kode .='</select>'."\n";
  return $kode;
}

function cmbBulan($name,$id,$cls,$ref,$set){
  $bln = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
  $kode='<select name="'.$name.'" class="'.$cls.'" id="'.$id.'" '.$set.' >'."\n";
  for($i=1; $i<13; $i++){
    $kode .='<option value="'.$i.'"';
    if(trim($i)==trim($ref)) {
          $kode .=' selected';
        }
    $kode .='>'.$bln[$i].'</option>'."\n";
    }
  $kode .='</select>'."\n";
  return $kode;
}

# 20180808
function getPreValue($sessionName,$defaultValue){
	$_SESSION[$sessionName]= isset($_SESSION[$sessionName])?$_SESSION[$sessionName]:$defaultValue;
	$_SESSION[$sessionName]= isset($_POST['cmdKirim'])?$_POST[$sessionName]:$_SESSION[$sessionName];
	return $_SESSION[$sessionName];
}
function getPreValueGet($sessionName,$defaultValue){
	$_SESSION[$sessionName]= isset($_SESSION[$sessionName])?$_SESSION[$sessionName]:$defaultValue;
	$_SESSION[$sessionName]= isset($_GET[$sessionName])?$_GET[$sessionName]:$_SESSION[$sessionName];
	return $_SESSION[$sessionName];
}


function pageHeader($judul, $otherMenu){
	$kode = '<div class="damarheader"><form method="POST">'.$judul.' | ';
	$kode.= (isset($otherMenu) AND $otherMenu!='')?$otherMenu:'';
	$kode.= '</form></div>';
	return $kode;
}

function tableHeader($judul, $otherMenu){
	$kode ='<div class="damarheader"><form method="POST">'.$judul.' | <small><a href="#" id="cmdXls"><i class="fa fa-download"></i> Excel</a> </small>';
	$kode .= (isset($otherMenu) AND $otherMenu!='')?$otherMenu:'';
	$kode .= ' </form></div>';
	$kode .= '<script type="text/javascript">$(document).ready(function(){ $("#cmdXls").click(function(){export_table_to_excel("myTable");}); });</script>';
	return $kode;
}

function cariKey(){
	$kode = '<span style="border:1px solid #34AEFB; padding:3px; background:#34AEFB;"><i class="fa fa-search"></i></span><input type="text" id="cmdSearch" class="tablesorter" placeholder="search" onkeyup="cari(this);">';
	$kode .= '<script type="text/javascript">function cari(e){ var that = e.value; var elems = document.getElementsByClassName("cari");	for (var i = 0; i < elems.length; ++i) { if(elems[i].innerHTML.toLowerCase().indexOf(that.toLowerCase()) == -1){ elems[i].style.display = "none"; }	else { elems[i].style.display = "";	} } }</script>';
	return $kode;
}

function uploadFile($judul,$action){
	$kode = '<div id="fgFrm01"><div id="fgHeader"><i class="fa fa-pencil"></i> '.$judul.'</div><div id="frmInner"><form action="'.$action.'" method="POST" enctype="multipart/form-data"><div class="dt-form-group"><label>Select File (xlsx)</label><input type="file" name="cFile" required></div><div class="dt-form-group"><label></label><button type="submit" name="submit" >Submit</button> <button id="cmdCancel">Cancel</button></div></form></div></div><div id="bgFrm01"></div>';
	$kode .= '<script type="text/javascript">$(document).ready(function(){$("#cmdUpload1").click(function(){	fg_popup_form("fgFrm01","frmInner","bgFrm01"); }); $("#cmdCancel").click(function(){ fg_hideform("fgFrm01","bgFrm01"); }); }); </script>';
	return $kode;
}

function setPeriode($judul,$tglStart,$tglEnd,$otherMenu){
	$kode ='<div class="damarheader"><form method="POST">'.$judul.' | <small><a href="#" id="cmdXls"><i class="icon-download-2"></i> Excel</a></small>';
	$kode .= $otherMenu!=''?$otherMenu:'';
	$kode .=' <small><i class="icon-calendar"></i> <input class="taggal" type="text" name="tglStart" id="start" value="'.$tglStart.'" readonly> to <input class="taggal" type="text" name="tglEnd" id="end" value="'.$tglEnd.'"> <input type="submit" name="cmdKirim" value="submit"></small></form></div>';
	$kode .= '<script type="text/javascript">var startDate, endDate, updateStartDate = function(){startPicker.setStartRange(startDate); endPicker.setStartRange(startDate); endPicker.setMinDate(startDate); }, updateEndDate = function(){ startPicker.setEndRange(endDate); startPicker.setMaxDate(endDate);	endPicker.setEndRange(endDate);	}, startPicker = new Pikaday({field: document.getElementById("start"), format: "YYYY-MM-DD", maxDate: new Date(2020, 12, 31), onSelect: function(){	startDate = this.getDate();	updateStartDate(); }}), endPicker = new Pikaday({ field: document.getElementById("end"), format: "YYYY-MM-DD", maxDate: new Date(2020, 12, 31),	onSelect: function() { 	endDate = this.getDate(); updateEndDate(); }}), _startDate = startPicker.getDate(),	_endDate = endPicker.getDate(); if (_startDate){startDate = _startDate; updateStartDate();} if (_endDate){ endDate = _endDate; updateEndDate();} $(document).ready(function(){ $("#cmdXls").click(function(){export_table_to_excel("myTable");}); });</script>';
	return $kode;
}
?>

<!-- Expport to Excel -->
<?= static_js('js/xlsx.core.min.js') ?>
<?= static_js('js/Blob.js') ?>
<?= static_js('js/FileSaver.js') ?>
<?= static_js('js/Export2Excel.js') ?>
<style type="text/css">
	.damarheader {margin: 15px 0;}
	.damarheader > form {vertical-align: top; }
	.damarheader > form input[type=text], .damarheader > form select, .damarheader > form input[type=text].taggal {
		border: 1px solid #DBDADA;
		padding: 5px;
		margin-right: 5px;
	}
	.damarheader > form input[type=submit] {padding: 4px; border: 1px solid #DBDADA;}
	.damarheader > form input[readonly] { background-color: #F2F1F1; }
	/*.damarheader > small {margin: 0 20px; padding: 0 10px; font-style: 10px;}*/
	#cmdXls, #cmdNew { margin: 0 10px; }
/* return to bottom */
	button#scrollToBottom {
		border-style: none;
		opacity: 0.6;
		background-color: transparent;
		color: #000;
		font-size: 30px;
		position: fixed;
		right: 10px;
	}
	a.btnClosed {
		border-style: none;
		opacity: 0.6;
		background-color: transparent;
		color: red;
		font-size: 20px;
		position: fixed;
		right: 15px;	
	}
</style>

<!-- Date Picker -->



<?= static_js('js/moment.min.js') ?>
<?= static_js('js/pikaday.js') ?>