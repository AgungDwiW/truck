<?php 
// for global function usable in post page
function in_string($str, $substr){
	// printpre([$str, $substr,strpos($str, $substr)!== false?"true":"false"]);
	if (strpos($str, $substr) !== false)
	    return true;
	else
	    return false;
      
}

// include_once "application/assets/Table.php";
include_once "application/assets/Table.php";

$debug = 0; //DON'T CHANGE THIS

function printpre_err($str, $fl = -1){
	//function to print string for debug purpose
	//change $debug variable to enable
	//use second argument to override global debug variable
	global $debug;
	if ($fl == -1)
		$fl = $debug;

	if ($fl){
		echo "<pre style='background-color:ddacac; border:solid 1px black; padding:5px; overflow-x:auto'>";
		print_r ($str);
		echo "</pre>";	
	}
	
	return 0;
}


function printpre_warn($str, $fl = -1){
	//function to print string for debug purpose
	//change $debug variable to enable
	//use second argument to override global debug variable
	global $debug;
	if ($fl == -1)
		$fl = $debug;

	if ($fl){
		echo "<pre style='background-color:f3f2b8; border:solid 1px black; padding:5px; overflow-x:auto'>";
		print_r ($str);
		echo "</pre>";	
	}
	
	return 0;
}

function printpre($str, $fl = -1){
	//function to print string for debug purpose
	//change $debug variable to enable
	//use second argument to override global debug variable
	global $debug;
	if ($fl == -1)
		$fl = $debug;

	if ($fl){
		// echo "";
		echo "<pre>";
		print_r ($str);
		echo "</pre>";	
	}
	
	return 0;
}



function conv_uom_buom($con, $qty, $uom, $material_id ){
	//function to convert uom to buom 
	// return array ['qty_buom' => qty, 'buom' => buom]
	$table 	= new Table('tbm_material_conversion');
	$result = $table->get(
						"{$qty} * (conversion_numerator_buom/conversion_denominator_buom) AS qty_buom", 
						'base_unit_of_measure'
						)
					->where("
						
						material_code = '{$material_id}' AND 
						alt_uom_sku = '{$uom}'
						")
					->execute();
    // $result = mysqli_query($con, $str);
    $row=mysqli_fetch_assoc($result);
    return $row;

}

function week_from_date($date){
	$date = strtotime($date);
	$year = $date->format('Y');
	
}

function redirect_back($additional_get=[]){
	global $debug;
	$location = $_SERVER['HTTP_REFERER'];
	foreach($additional_get as $key => $value){
		$location.="&{$key}={$value}";
	}
	if (!$debug)
		header('location:' . $location);
}

function redirect($url){
	global $debug;
	$location = $url;
	if (!$debug)
		header('location:' . $location);
}

function trimALL(&$arr){
	foreach($arr as $key => $value){
		$arr[$key] = trim($value);
	}
	
}


function checkArr(&$arr, $index, $def, $accum=false, $sign =false){
	// check if $arr[$index] is exist
	// if not assign $arr[$index] = $def
	if ($accum){
		if (!isset($arr[$index])){	
    		$arr[$index] = $def;
    	}
    	else{
    		if ($sign == '-')
    			$arr[$index] -= $def;	
    		else
    			$arr[$index] += $def;	
    	}
    	return $arr;
   	}
    else if (!isset($arr[$index])){

        $arr[$index] = $def;
        return $arr;
    }
    
    
}

function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}



function check_expired($row){
	$is_expired 	= false;
	$start 	= date_create($row['create_date'])->getTimestamp();
	$end 		= new DateTime();
	$end 		= $end->getTimestamp();
	$diff = abs( ($start - $end) / ( 60 * 60 ));

	$remaining = 0;

	if($row['konfirmasi_vendor']==""){
		if ($row['ktg_permintaan'] == 'u' && $diff>6) {
			$is_expired 	= true;
		}
		else if ($row['ktg_permintaan'] == 's' && $diff>48){
			$is_expired 	= true;
		}
		else if ($row['ktg_permintaan'] == 'a' && $diff>24){
			$is_expired 	= true;
		}
	
	}
	if ($row['konfirmasi_vendor'] == 'EX'){
		$is_expired = true;
	}
	return $is_expired;
}

function redirect_page(){
	global $debug;
	if (!$debug)
		header('location:'.$_SERVER['HTTP_REFERER']);
}


function createTD($str='',$class='', $id =''){
	echo "<td class='{$class}'>{$str}</td>";
}
function createTR($col, $class = '', $id =''){
	echo "<tr class = '{$class}' id= '{$id}'>";
	foreach($col as  $element){
		if (is_array($element))
			createTD($element[0], $element[1], $element[2]);
		else 
			createTD ($element);
	}
	echo "<tr>";
}

function run_query($con, $str){
	global $debug;
	$start = microtime(true);
	printpre($str);
	$result= mysqli_query($con, $str);
	$end = microtime(true);

	printpre($end  - $start);
	if($result){
		
		return $result;
	}
	else{
		printpre($str,1);
		printpre(mysqli_error($con),1);
		return 0;
	}
}
function easyReplace($data, $con){
	$column = array_keys($data);
	$field_arr 	= array_keys($data[0]);
	$field 		= implode(",", array_keys($data[0]));
	$data_arr 	= [];
	foreach($data as $row){
		$d 		= [];
		foreach($field_arr as $col){ //ensure in same order
			// $row[$col] =str_replace("'", "\'", $row[$col]);
			$d[] = $row[$col];
		}
		// $d 	=  implode("','", array_values($d));;

		$d 		= $this->encaseValue($d);
		$d 	= "({$d})";
		$data_arr[] = $d;
	}
	$content = implode(", ", $data_arr);
}

function strEndsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}
function pageHeaderGet($judul, $otherMenu){
		$kode = '<div class="damarheader"><form method="GET" id="formPageHeader"><h4>'.$judul.' | ';
		$kode.= "<input type='hidden' name='action' value ='{$_GET['action']}'> ";
		$kode.= (isset($otherMenu) AND $otherMenu!='')?$otherMenu:'';
		$kode.= '</h4></form></div>';
		return $kode;
	}
function getPreValueGet($sessionName,$defaultValue){
		$_SESSION[$sessionName]= isset($_SESSION[$sessionName])?$_SESSION[$sessionName]:$defaultValue;
		$_SESSION[$sessionName]= isset($_GET[$sessionName])?$_GET[$sessionName]:$_SESSION[$sessionName];
		return $_SESSION[$sessionName];
}

function checkEmpty($str, $replace){
	if (isset($str) || $str == '')
		return $replace;
	else
		return $str;
}
function checkTableExist($db, $table, $cone){
	$str = "SELECT * 
			FROM information_schema.tables
			WHERE table_schema = '{$db}' 
			    AND table_name = '{$table}'
			LIMIT 1;";
	$result = run_query($cone, $str);	
	return (mysqli_num_rows($result) == 1);
}

function emptyOrNull($column, $data){
	if ($data == '')
		return "({$column} = '' or isnull({$column})) ";
	else
		return "{$column} = '{$data}'";
}

function filledOrTrue($column, $data, $operation = '='){
	if ($data != ''){
		if ($operation == 'in')
			return "{$column} like '%{$data}%' "	;
		return "{$column} = '{$data}'";
	}
	else
		return "TRUE";
}



function sendPOST($data,$uri){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$uri);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $output;
}

function flattenArray($array) {
	$result = [];
	foreach ($array as $value) {
		if (is_array($value)) {
			$result = array_merge($result, flattenArray($value));
		} else {
			$result[] = $value;
		}
	}
	return $result;
}


function replaceFirst($searchString, $replacementString, $originalString){
	return preg_replace('/' . preg_quote($searchString, '/') . '/', $replacementString, $originalString, 1);
}


	
?>