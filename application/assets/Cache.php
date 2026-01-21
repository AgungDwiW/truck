<?php

class Cache{
	public $cacheFolder = "application/assets/cache2/";
	public $cacheMasterDir = "application/assets/cache2/master.json";
	public $cachelist = [];
	public $cached = [];
	function __construct(){
		$this->cachelist = $this->loadJSON($this->cacheMasterDir);
		printpre($this->cachelist);
	}
	function loadJSON($filename){

		if (!file_exists($filename))
			return [];
		
		$f 			= fopen($filename, "r");
		if(filesize($filename) <= 0)
			return [];	
		$file 		= fread($f,filesize($filename)); 	
		
		fclose($f);
		return json_decode($file, true);
	}
	function writeJSON($filename, $array){
		$file 		= fopen($filename, "w");
		$json 		= json_encode($array);
		fwrite($file, $json);
		fclose($file);
	}

	function check($name){	
		$now = date_timestamp_get(date_create());
		if(!isset($this->cachelist[$name]))
			return false;
		if 	(($now - $this->cachelist[$name]['timestamp'])/60  > $this->cachelist[$name]['lifetime']){
			return false;
		}
		return true;

	}
	function set($name, $data, $lifetime = '180'){
		if ($name=='master'){
			printpre("cant assign cache with name master",1);
			return;
		}

		// set cache 
		// lifetime lifetime cache in minutes
		$now = date_timestamp_get(date_create());
		$this->cachelist[$name] = [	"timestamp" => $now,
									"lifetime" => $lifetime];
		$this->cached[$name] 	= $data;
		$filename = "{$this->cacheFolder}{$name}.json";
		$this->writeJSON($filename , $data);
		$this->writeJSON($this->cacheMasterDir, $this->cachelist);
	}
	function get($name){
		if ($this->check($name)){
			$filename = "{$this->cacheFolder}{$name}.json";
			$this->cached[$name] = $this->loadJSON($filename);
			return $this->cached[$name];			
		}
	}
}
?>