<?php 
$base_path = dirname(__FILE__);
include_once "{$base_path}/utility_db.php";


//===============================================================================================================================
// 														DEBUGGING
//===============================================================================================================================


	$debug = false;
	$counter = 0;
	function printpre_log($msg){
		global $controller;
		global $action;
		global $base_path;
		global $counter;
		if (!isset($action))
			$action = '';

		if (!isset($controller))
			$controller = '';

		$folderPath = "{$base_path}/log/";

		if (!is_dir($folderPath)) {
			// If it doesn't exist, create it with permissions (e.g., 0755 for read/write access)
			if (mkdir($folderPath)) {
				// echo "Folder created successfully: $folderPath";
			} else {
				echo "Failed to create folder: $folderPath";
			}
		}
		$tgl = date("Y-m-d-H-i-s");

		$filename 	= "{$folderPath}/{$tgl}_{$counter}_{$controller}_{$action}.txt";
		$counter+=1;
		$datajson 	= json_encode($msg);
		$fileHandle = fopen($filename, "w");
		fwrite($fileHandle, $datajson);
	}

	function printpre($str, $fl = -1){
		//function to print string for debug purpose
		//change $debug variable to enable
		//use second argument to override global debug variable
		global $MODE;


		global $debug;
		if ($fl == -1)
			$fl = $debug;

		if ($fl){
			if ($MODE == 'PRODUCTION'){
				printpre_log($str);
				return 0;
			}

			echo "<pre style='background-color:ecf1fa; border:solid 1px black; padding:5px; overflow-x:auto'>";
			print_r ($str);
			echo "</pre>";	
		}

		
		return 0;
	}

	function printpre_err($str, $fl = -1){
		//function to print string for debug purpose
		//change $debug variable to enable
		//use second argument to override global debug variable
		global $MODE;
		global $debug;
		if ($fl == -1)
			$fl = $debug;

		if ($fl){
			if ($MODE == 'PRODUCTION'){
				printpre_log($str);
				return 0;
			}
			echo "<pre style='background-color:#ddacac; border:solid 1px black; padding:5px; overflow-x:auto'>";
			print_r ($str);
			echo "</pre>";	
		}
		
		return 0;
	}


	function printpre_err_high($str){
		//function to print string for debug purpose
		//change $debug variable to enable
		//use second argument to override global debug variable
		
			echo "<pre style='background-color:#ddacac; border:solid 1px black; padding:5px; overflow-x:auto'>";
			print_r ($str);
			echo "</pre>";	
		
	}



	function printpre_warn($str, $fl = -1){
		//function to print string for debug purpose
		//change $debug variable to enable
		//use second argument to override global debug variable
		global $MODE;
		global $debug;
		if ($fl == -1)
			$fl = $debug;

		if ($fl){
			if ($MODE == 'PRODUCTION'){
				printpre_log($str);
				return 0;
			}
			echo "<pre style='background-color:#f3f2b8; border:solid 1px black; padding:5px; overflow-x:auto'>";
			print_r ($str);
			echo "</pre>";	
		}
		
		return 0;
	}
//===============================================================================================================================
// 														DATE AND TIME
//===============================================================================================================================
	function date_from_week($week, $year){
		$dto = new DateTime();
		$dto->setISODate($year, $week);
		$ret['week_start'] = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		$ret['week_end'] = $dto->format('Y-m-d');
		return $ret;
	}
		
	function getIsoWeeksInYear($year) {
		$date = new DateTime;
		$date->setISODate($year, 53);
		return ($date->format("W") === "53" ? 53 : 52);
	}

	function date_week($date, $is_after){
		$week = date("W", strtotime($date));
		printpre(["weeek", $week]);
		// $week+=1;
		if (date("w", strtotime($date)) == 0){
			$week+=1;
			if ($is_after)
				$week-=1;
		}

		elseif (date("w", strtotime($date)) >0 && date("w", strtotime($date))<6){
			if($is_after){
				$week-=1;
			}
			else{
				// if(date("w", strtotime($date)) != 1)
				//     $week+=1;
			}
		}
		printpre(["weeekProcess", $week, $date, $is_after]);
		return intval($week);
	}

	function date_month($date, $is_after){
		$month      = date("m", strtotime($date));
		$last_day   = date("t", strtotime($date));
		$day        = date("d", strtotime($date));
		if ($is_after){
			if ($last_day != $day){
				$month-=1;
			}
		}
		else {
			if ($day != '01'){
				$month +=1;
			}
		}

		return intval($month);
	}
	function date_year($date){
		return date("Y", strtotime($date));
	}

	function getDate_from_week($week, $year,$is_after){
		$gendate = new DateTime();

		$gendate->setISODate($year,$week,0); //year , week num , day
		if ($is_after)
			$gendate->setISODate($year,$week,6);
		// printpre([$week, $year,$is_after, $gendate->format('Y-m-d')],1);
		return $gendate->format('Y-m-d'); //"prints"  26-12-2013
	}
	function getDate_from_month($month, $year, $is_after){
		if ($month == 0){
			$year-=1;
			$month = 12;
		}
		else if ($month == 13){
			$year+=1;
			$month = 1;
		}
		if ($is_after){
			$date = new DateTime("{$year}-{$month}-1");
			$date->modify('last day of');
			return $date->format("Y-m-d");;
		}
		return "{$year}-{$month}-1";
	}
	
	function date_compability($date){
		$date = str_replace("/","-",$date);

	// d m y to y m d 
	list($a,$b,$c) = explode("-", $date);
	if (strlen($c)==4) $date = "{$c}-{$b}-{$a}";
	else if (strlen($c)==2 and strlen($a) ==2) $date = "20{$c}-{$b}-{$a}";
	return $date;
	}


//===============================================================================================================================
// 														Utilities
//===============================================================================================================================

	//---------------------------------------------------------------------------------------------------------------------------
	// 														STRING
	//---------------------------------------------------------------------------------------------------------------------------
		function in_string($str, $substr){
			// printpre([$str, $substr,strpos($str, $substr)!== false?"true":"false"]);
			if (strpos((string)$str, (string)$substr) === false)
				return false;
			else
				return true;
			
		}
		function trimALL(&$arr){
			for($x = 0; $x < count($arr); $x++){
				$arr[$x] = trim($arr[$x]);
			}
		}
		
		function endsWith( $haystack, $needle ) {
			$length = strlen( $needle );
			if( !$length ) {
				return true;
			}
			return substr( $haystack, -$length ) === $needle;
		}
		function random_str(
				$length,
				$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
			) {
				$str = '';
				$max = mb_strlen($keyspace, '8bit') - 1;
				if ($max < 1) {
					throw new Exception('$keyspace must be at least two characters long');
				}
				for ($i = 0; $i < $length; ++$i) {
					$str .= $keyspace[random_int(0, $max)];
				}
				return $str;
		}

		function format_uuidv4($data)
		{
			assert(strlen($data) == 16);

			$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
				
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}
		function csvIndexing($firstRow, $row){
			$data = [];
			foreach ($firstRow as $num => $name){
	
				$name = trim($name);
				$name = preg_replace('/[^\p{L}\p{N}\s_[:punct:]]/u', '', $name);
				if (!isset($row[$num]))
					continue;
				$row[$num] = trim($row[$num]);
				$row[$num] = preg_replace('/[^\p{L}\p{N}\s_[:punct:]]/u', '', $row[$num]);
				$data[$name] = $row[$num];
	
			}
			return $data;
		}
	
		function removeNewLIne($str){
			$str = str_replace("\r",'', $str);
			$str = str_replace("\n",'', $str);
			return $str;
		}
	
		function is_json($string) {
				if(is_array($string))
					return 0;
			$a = json_decode($string);
			if ($a)
				return 1;
			else
				return 0;
			// return (json_last_error() == JSON_ERROR_NONE);
		}
		
		function replaceFirst($searchString, $replacementString, $originalString){
			return preg_replace('/' . preg_quote($searchString, '/') . '/', $replacementString, $originalString, 1);
		}
	
	//---------------------------------------------------------------------------------------------------------------------------
	// 														Array
	//---------------------------------------------------------------------------------------------------------------------------

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
			return $arr;
				
			
		}
		
		function sanitizePost(){

			$post = $_POST;
			foreach ($post as $key => $value){
				if (is_array($value)){
					$vv = $value;
					foreach($value as $key=> $v){
						if (is_json($v)){
							continue;
						}
						if(is_string($v)){
							$va = stripcslashes($v);
						$va = str_replace("'", "\\'", $v);
						$va = str_replace('"', '\\"', $v);
						$vv[$key] = $va;	
						}
							
					}
					$v = $vv;
				}
				else{
					if (is_json($value)){
						$v = $value;
						continue;
					}
					$v = stripcslashes($value);
					$v = str_replace("'", "\\'", $v);
					$v = str_replace('"', '\\"', $v);
						
				}

				$_POST[$key] = $v;
			}

			$post = $_GET;
			foreach ($post as $key => $value){
				if (is_array($value)){
					$vv = $value;
					foreach($value as $key=> $v){
						if (is_json($v)){
							continue;
						}
						$va = stripcslashes($v);
						$va = str_replace("'", "\\'", $v);
						$va = str_replace('"', '\\"', $v);
						$vv[$key] = $va;
					}
					$v = $vv;
				}
				else{
					if (is_json($value)){
						$v = $value;
						continue;
					}
					$v = stripcslashes($value);
					$v = str_replace("'", "\\'", $v);
					$v = str_replace('"', '\\"', $v);
						
				}

				$_GET[$key] = $v;
			}
			

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

				
		function checkEmpty($arr, $str, $replace){
			if (!isset($arr[$str]) || $arr[$str] == '')
				return $replace;
			else
				return $arr[$str];
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



	//---------------------------------------------------------------------------------------------------------------------------
	// 														Redirection
	//---------------------------------------------------------------------------------------------------------------------------
		function redirect_back($additional_get=[]){
			global $debug;
			$location = $_SERVER['HTTP_REFERER'];
			$replaced = [];
			if (! in_string($location, "?")){
				$location.="?";
			}
			else{
				$location2  = explode("?", $location);
				printpre($location2, $debug);
				$location_b = $location2[0];
				$location_s = $location2[1];
				printpre(["b"=>$location_b, 's'=>$location_s],$debug);
				$location_s = explode("&", $location_s); 
				printpre(["b"=>$location_b, 's'=>$location_s],$debug);
				$gets 		= [];
				foreach($location_s as $get){
					$keyval = explode("=", $get);
					foreach($additional_get as $key => $value){
						if ($key == $keyval[0]){
							$keyval[1] = $value;
							$replaced[]=$key;
						}
					}
					$keyval  	= implode("=", $keyval);
					if ($keyval)
						$gets[] 	= $keyval;
				}
				$location_s = implode("&", $gets);
				$location = $location_b."?".$location_s;

			}
			printpre($location,$debug);

			foreach($additional_get as $key => $value){
				if (in_array($key, $replaced))
					continue;
				$location.="&{$key}={$value}";
			}
			if ($debug == 0){
				header('location:' . $location);
				echo "<script> window.location = '".$location."'; </script>";
			}

		}

		function redirect_page($page){
			global $debug;
			if (!$debug){
				header('location:'.$page);
				echo "<script> window.location = '".$page."'; </script>";
			}
		}
		
	//---------------------------------------------------------------------------------------------------------------------------
	// 														API MYSQL
	//---------------------------------------------------------------------------------------------------------------------------
		
		function sendPOST($url, $fields){
			if (is_array($fields))
				$fields_string = http_build_query($fields);
			else 
				$fields_string = $fields;

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($fields)
			));

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ❌ Disable peer verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // ❌ Disable host verification


			//execute post
			$result = curl_exec($ch);
			return $result;
		}
		function run_query($con, $str){
			try {
				$result= mysqli_query($con, $str);
				return $result;
			} catch (\Throwable $th) {
				printpre_warn($str,1);
				printpre_err(mysqli_error($con),1);
				$filename = date('Y-m-d') . ".log";

				// Define log directory (make sure it's writable)
				$logDir =  dirname(__FILE__) . "/log/"; 
				$filepath = $logDir . $filename;

				// Append data with timestamp
				global $controller;
				$entry = "[" . date('H:i:s') . "] -----------------------------------------" . $controller . PHP_EOL;
				file_put_contents($filepath, $entry, FILE_APPEND);
				$entry = "[" . date('H:i:s') . "] " . $str . PHP_EOL;
				file_put_contents($filepath, $entry, FILE_APPEND);
				$entry = "[" . date('H:i:s') . "] " . mysqli_error($con) . PHP_EOL;
				file_put_contents($filepath, $entry, FILE_APPEND);

				exit();
				return 0;
				
			}
			

		}
	//---------------------------------------------------------------------------------------------------------------------------
	// 														FILE
	//---------------------------------------------------------------------------------------------------------------------------
		function createDirIfNotExist($path){
			if (!is_dir($path)) {
				mkdir($path,0777, 1);
			}
		}
		
		function upload($type, $target_dir, $filename) {
			if (!is_dir($target_dir)) {
				mkdir($target_dir, 0777, true);
			}
			$target_file = rtrim($target_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
			if (move_uploaded_file($_FILES[$type]['tmp_name'], $target_file)) {
				return $target_file;
			} else {
				return false;
			}
		}

	//---------------------------------------------------------------------------------------------------------------------------
	// 														Other
	//---------------------------------------------------------------------------------------------------------------------------
		function create_data($arr){
			$str = '';
			foreach($arr as $key => $value){
				$str.= " data-{$key} = '{$value}'";
			}
			return $str;
		}



		$__startTime = 0;
		function startExecutionTime() {
				global $__startTime;
			$__startTime =  microtime(true);
		}

		function endExecutionTime() {

				global $__startTime;
				$startTime = $__startTime;
			$endTime = microtime(true);
			$executionTime = $endTime - $startTime;
			printpre_warn($executionTime);
		}

		


		function getError($key, $message, $value=null){
			if 
				(
					isset($_GET[$key]) AND 
					(
						$value!=null and 
						$value == $_GET[$key]
					)
				){

					echo "<script> $(document).ready(function(){ alert(`{$message}`) })</script>";
				
			}
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

				
		function encaseValue($value, $prefix = '"', $suffix = '"') {
			// Check if the input is an array
			if (is_array($value)) {
				// Recursively encase each element of the array
				return array_map(function($item) use ($prefix, $suffix) {
					return $prefix . $item . $suffix;
				}, $value);
			}

			// Handle non-array inputs
			return $prefix . $value . $suffix;
		}



//===============================================================================================================================
// 														ACCESS
//===============================================================================================================================



?>