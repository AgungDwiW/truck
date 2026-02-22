<?php

$debug = 0; //DON'T CHANGE THIS
$debug_val = [];
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

if (!function_exists('printpre')){
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
}

?>