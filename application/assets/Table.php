<?php

include_once "concloud.php";
include_once "application/assets/utility_function_withoutJS.php";
include_once "application/assets/Cache.php";

$debug 				= 0;
$table_by_supplier 	= [];
$table_ho 			= [];
$table_master 		= [];
$db_supplier 		= [];
$outbound_folder 	= "D:/outbound";
$outbound_table		= ['tbl_item_pengiriman', 'tbl_pengiriman'];
$db_default 		= 'smartlogistic';
class Table{
	public $mode;
	public $field_insert="*";
	public $join 		= '';
	public $condition 	= 'TRUE';
	public $group 		= '';
	public $limit 		= '';
	public $order 		= '';
	public $as 			= '';
	public $field_update= '';

	public $value_insert 	= '';
	public $column_insert 	= '';

	public $db_name;
	public $db_model_name;
	public $db_model_table 	= [];

	public $con2;
	public $supplier_id = '';
	public $query = '';
	public $hard_db;
	public $outbound_flag = 0;


	function __construct($table_name, $supplier = '', $conn = '') {
		global $conSL;
		global $db_model;
		$this->supplier_id 		= $supplier;
		if ($conn == '')
        	$this->con 				= $conSL;
        else
        	$this->con 				= $conn;
        if (isset($db_model) && $db_model ){
        	$this->db_model_name = $db_model;
        	$this->getTableList();
        }

        $this->db_name 			= $this->getdbName($table_name);
        $this->table_name 		= $table_name;

        if (in_string($table_name, ".")){
        	$table_name = str_replace("`", '', $table_name);
        	$key = explode(".", $table_name);
        	$this->table_name = $key[1];
        	$this->db_name = $key[0];
        }
        $this->resetVariables();
        return $this;
    }

    function getTableList(){
    	global $db_tbl;
    	if (isset($db_tbl) && $db_tbl[$this->db_model_name] ){
    		$this->db_model_table = $db_tbl[$this->db_model_name];
    		return;
    	}
    	$con =  $this->con;
    	$cache = new Cache();
		if($cache->check("table_".$this->db_model_name)){
			$tbl = $cache->get("table_".$this->db_model_name);
		}
		else{
			$str 	= "SELECT table_name FROM information_schema.tables
					WHERE table_schema = '{$this->db_model_name}'";
			$result = mysqli_query($con, $str);
			printpre($str);
			while($row= mysqli_fetch_assoc($result)){
				$tbl[]=$row['table_name'];
			}

			$cache->set("table_".$this->db_model_name, $tbl);
		}
		$this->db_model_table = $tbl;

    }


    function as($str){
    	$this->as .= $str;
    	return $this;
    }

    function outbound($transaction_name='-1'){
    	if ($transaction_name == -1){
    		global $controller;
    		global $_POST;
    		$cmd = isset($_POST['cmd'])?$_POST['cmd']:(isset($_POST['nket'])?$_POST['nket']:'');
    		printpre($_POST);
    		$transaction_name = "{$controller}_{$cmd}";
    	}
    	$this->outbound_flag = 1;
    	$this->transaction = $transaction_name;
    	return $this;
    }

    function resetVariables(){
    	$this->mode 			= '';
		$this->field_insert		= "*";
		$this->data_insert		= [];
		$this->join    			= '';
		$this->condition   		= 'TRUE';
		$this->group     		= '';
		$this->limit     		= '';
		$this->field_update		= '';
		$this->data_update		= [];
		$this->value_insert 	= '';
		$this->column_insert 	= '';
		$this->sql 				= '';
		$this->transaction_name = '';
		$this->outbound_flag 	= 0;

		return $this;
    }

    function insertID(){
    	return mysqli_insert_id($this->con);
    }

    function getdbName($table_name){

    	global $db_default;
    	if (in_array(  $table_name, $this->db_model_table))
    		return $this->db_model_name;
    	
    	return $db_default;
    }

	function get(...$fields){
		$this->mode = "SELECT";

		if ($fields){

			if ($this->field_insert !='*')
				$this->field_insert.=",";
			else
				$this->field_insert = '';
			$this->field_insert .= implode(",", $fields);
		}
		else 
			$this->field_insert = '*';
		return $this;
	}

	function where($condition){
		if ($this->condition == 'TRUE')
			$this->condition = '';	
		$this->condition .= " ".$condition;
		return $this;
	}

	function join($tbl = '', $glue=[]){
		$db2 	=  $this->getdbName($tbl);
		if (in_string($tbl, '.')){
			$tbl = explode(".", $tbl);
			$db2 = $tbl[0];
			$tbl = $tbl[1];
		}
		$db2 =str_replace("`", "", $db2);
		$tbl =str_replace("`", "", $tbl);

		$join 	= " LEFT JOIN `{$db2}`.`{$tbl}` ON ";
		
		$glues = '';
		if ( is_array($glue[0]) ){
			foreach ($glue as $g){
				$glues.= "`{$this->db_name}`.`{$this->table_name}`.$g[0] = `{$db2}`.`{$tbl}`.$g[1] ";
				if ($this->as)
					$glues.= "`{$this->db_name}`.`{$this->as}`.$g[0] = `$name`.$g[1] ";
				if (in_string($g[0], "."))
					$glues = "$g[0] = `{$db2}`.`{$tbl}`.$g[1] ";
				if (in_string($g[1], '.')){
					$glues = explode(" = ")[0];
					$glues = "{$glues} = $g[1]";
				}

				if (in_array( 'OR', $glue))
					$glues.= "OR ";
				else
					$glues.= "AND ";
			}
		}
		else{

			$glues = "`{$this->db_name}`.`{$this->table_name}`.$glue[0] = `{$db2}`.`{$tbl}`.$glue[1] ";
			if ($this->as)
					$glues = "`{$this->db_name}`.`{$this->as}`.$glue[0] = `{$db2}`.`{$tbl}`.$glue[1] ";
			if (in_string($glue[0], "."))
				$glues = "$glue[0] = `{$db2}`.`{$tbl}`.$glue[1] ";
			
			if (in_string($glue[1], '.')){
					$glues = explode(" = ", $glues)[0];
					$glues = "{$glues} = $glue[1]";
			}

		}
		$glues =  rtrim($glues, "OR ");
		$glues =  rtrim($glues, "AND ");
		$this->join .= "
				{$join}
				{$glues}
				";
		return $this;
	}
	function joinSQL($sql, $name, $glue=[]){
		$join 	= " LEFT JOIN ({$sql}){$name} ON ";
		$glues = '';
		if ( is_array($glue[0]) ){
			foreach ($glue as $g){
				$glues.= "`{$this->db_name}`.`{$this->table_name}`.$g[0] = `$name`.$g[1] ";
				if ($this->as)
					$glues.= "`{$this->db_name}`.`{$this->as}`.$g[0] = `$name`.$g[1] ";
				if (in_string($g[0], "."))
					$glues = "$g[0] = `{$db2}`.`{$tbl}`.$glue[1] ";
				if (in_array( 'OR', $glue))
					$glues.= "OR ";
				else
					$glues.= "AND ";
			}
		}
		else{
			$glues = "`{$this->db_name}`.`{$this->table_name}`.$glue[0] = `{$name}`.$glue[1] ";
			if ($this->as)
				$glues = "`{$this->db_name}`.`{$this->as}`.$glue[0] = `{$name}`.$glue[1] ";
			if (in_string($glue[0], "."))
				$glues = "$glue[0] = `{$db2}`.`{$tbl}`.$glue[1] ";
		}
		$glues =  rtrim($glues, "OR ");
		$glues =  rtrim($glues, "AND ");
		$this->join .= $join.$glues;
		return $this;

	}
	function group(...$groups){
		$this->group = "GROUP BY ". implode(",", $groups);
		return $this;	
	}

	function limit($offset = 0, $limit){
		$this->limit = "LIMIT {$offset}, {$limit}";
		return $this;
	}

	function order(...$orders){
		$this->order = "ORDER BY ";
		$this->order .= implode(", ", $orders);
		return $this;
	}
	
	function update($fields, $add_parenthesis = 1){
		$this->mode = 'UPDATE';
		$f = [];
		foreach($fields as $key => $value){
			if ($add_parenthesis)
				$f[] = "`{$key}` = '$value'";
			else 
				$f[] = "`{$key}` = $value";
		}
		if ($this->field_update!='')
			$this->field_update.=", "	;
		$this->field_update .= implode(",", $f);
		return $this;
	}


	function encaseValueCheckEscape($str){
		$escapeValue = ["NOW()", "NULL"];
		return in_array($str, $escapeValue);
	}

	function encaseValue($arr, $add_parenthesis=1){
		$ret = [];
		
		foreach($arr as $key=>$value){
			$value 		=str_replace("'", "\'", $value);
			if ($this->encaseValueCheckEscape($value))
				$ret[] 	= $value; 
			else if($add_parenthesis)
				$ret[] 	= "'{$value}'";
			else
				$ret[] 	= $value; 

		}
		return implode(", ", $ret);
	}



	function insert($data, $add_parenthesis = 1){
		$this->mode = 'INSERT';
		$field 		= implode(",", array_keys($data));
    	$data 		= array_values($data);
    	// if ($add_parenthesis)
    	// 	$content 	= implode("','", array_values($data));
    	// else
    	// 	$content 	= implode(",", array_values($data));
    	// $content 	= "'{$content}'";
    	$content 	= $this->encaseValue($data, $add_parenthesis);
    	$this->field_insert = $field;
    	if ($this->value_insert!= ''){
    		$this->value_insert.=",";
    	}

    	$this->value_insert .= $content;
    	return $this;
	}
	function multiInsert($data){
		$this->mode = 'MULTIINSERT';
		$field_arr 	= array_keys($data[0]);
		$field 		= implode(",", array_keys($data[0]));
		$this->field_insert = $field;
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
    	$this->value_insert = $content;
    	return $this;
	}
	function replace($data, $add_parenthesis= 1){
		$this->mode = 'REPLACE';
		$field 		= implode("`,`", array_keys($data));
		$field 		= "`{$field}`";
    	$data 		= array_values($data);

    	// $content 	= implode("','", array_values($data));;
    	// $content 	= "'{$content}'";
    	$content 	= $this->encaseValue($data, $add_parenthesis);

    	$this->field_insert = $field;
    	$this->value_insert = $content;
    	return $this;	
	}

	function multiReplace($data){
		$this->mode = 'MULTIREPLACE';
		$field_arr 	= array_keys($data[0]);
		$field 		= implode("`,`", array_keys($data[0]));
		$field 		= "`{$field}`";
		$this->field_insert = $field;
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
    	$this->value_insert = $content;
    	return $this;
	}
	
	function delete(){
		$this->mode = 'DELETE';
		return $this;
	}

	function SQLRemoveTrailing($str, $trail){
		// $str = trim($str);
		$str = rtrim($str, $trail);
		return $str;
	}
	function SQL(){
		$this->field_insert = $this->SQLRemoveTrailing($this->field_insert, ",");
		$this->field_update = $this->SQLRemoveTrailing($this->field_update, ",");
		$this->field_insert = $this->SQLRemoveTrailing($this->field_insert, ",");
		$this->value_insert = $this->SQLRemoveTrailing($this->value_insert, ",");
		$this->group 		= $this->SQLRemoveTrailing($this->group, ",");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, "AND");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, "and");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, "or");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, "OR");


		if($this->condition == '')
			$this->condition = "TRUE";
		switch ($this->mode){
			case "SELECT":
				$sql = "SELECT
							{$this->field_insert}
						FROM
							`{$this->db_name}`.{$this->table_name} {$this->as}
						{$this->join}
						WHERE 
							{$this->condition}
						{$this->group}
						{$this->order}
						{$this->limit}
						";
				break;
			case "UPDATE":
				$sql = "
						UPDATE
							`{$this->db_name}`.`{$this->table_name}`
						{$this->join}
						SET 
							{$this->field_update}
						WHERE
							{$this->condition}
						{$this->limit}
						";
				break;
			case "INSERT":
				$sql  ="
						INSERT INTO `{$this->db_name}`.`{$this->table_name}`
							({$this->field_insert}) 
						VALUES 
							($this->value_insert)
						";
				break;
			case "REPLACE":
				$sql  ="
						REPLACE INTO `{$this->db_name}`.`{$this->table_name}`
							({$this->field_insert}) 
						VALUES 
							($this->value_insert)
						";
				break;
			case "DELETE":
				$sql = "
						DELETE FROM 
							`{$this->db_name}`.`{$this->table_name}`
						WHERE 
							{$this->condition}
						{$this->limit}
						";
				break;
			case "MULTIINSERT":
				$sql = "
						INSERT INTO `{$this->db_name}`.`{$this->table_name}`
						({$this->field_insert}) 
						VALUES 
							$this->value_insert
					";
				break;
			case "MULTIREPLACE":
				$sql = "
						REPLACE INTO `{$this->db_name}`.`{$this->table_name}`
						({$this->field_insert}) 
						VALUES 
							$this->value_insert
					";
				break;
		}
		return $sql;
	}


	function execute($reset = true){
		//execute the query thats been build
		//return mysql result if success
		//return 0 if failed and display the error
		//if the query is insert will return the last inserted id

		

		$str = $this->SQL();
		
		global $debug;
		printpre($str);
		if (in_string($this->mode, "MULTI")){
			$result= mysqli_multi_query($this->con, $str);
		}
		else{
			$result= mysqli_query($this->con, $str);
		}

		

		if($result){
			$id = $this->insertID();
			global $outbound_table;
			if ($this->outbound_flag){
				$this->makeOutbound();
			}
			else{
				printpre([$this->table_name, $outbound_table, in_array($this->table_name, $outbound_table), $this->mode != 'SELECT']);
				if ( in_array($this->table_name, $outbound_table)   AND $this->mode != 'SELECT'){
					printpre(["out!!"]);
					$this->outbound();$this->makeOutbound();
				}
			}


			if ($reset)
				$this->resetVariables();
			
			if(in_string($this->mode, "INSERT"))
				return $id;
			
			return $result;
		}
		else{
			printpre_warn($str,1);
			printpre_err(mysqli_error($this->con),1);
			return 0;
		}

		

	}
	function fetch(){
		$result = $this->execute();
		if (mysqli_num_rows($result) == 1){
			return mysqli_fetch_assoc($result);
		}
		else{
			$table = [];
			while($row = mysqli_fetch_assoc($result)){
				$table[] = $row;
			}
			return $table;
		}
	}

	function fetchOne(){
		$this->limit(0,1);
		$result = $this->execute();
		if (mysqli_num_rows($result) == 1){
			return mysqli_fetch_assoc($result);
		}
		else{
			$table = [];
			while($row = mysqli_fetch_assoc($result)){
				$table[] = $row;
			}
			return $table;
		}
	}

	function fetchALL(){
		$result = $this->execute();
		// printpre($result);
		$table = [];
		while($row = mysqli_fetch_assoc($result)){
			$table[] = $row;
			// printpre($row);
		}
		return $table;
	
	}
	
	function makeOutbound(){
		// for outbound transaction
		global $outbound_folder;
		global $controller;

		global $OUTBOUNDAPI;
		if ($this->mode == 'SELECT')
			return;		

		$cacher = 0;
		$cacher = new Cache();
		// $debug = 1;
		$counter = $cacher->loadJSON(dirname(__FILE__)."\\tableCounter.json");
		printpre([$counter, $counter == []]);
		if ($counter == [])
			$counter = ['document_id' => 0];
		$counter['document_id'] += 1;
		printpre($counter);
		$cacher->writeJSON(dirname(__FILE__)."\\tableCounter.json", $counter);


		// ==============================================================
		// ================= ADD ID =====================================
		// ==============================================================
		// $outbound_table		= ['tbl_item_pengiriman', 'tbl_pengiriman', 'tbl_permintaan_kirim'];	
		if ($this->mode =='INSERT'){
			if ($this->table_name == 'tbl_item_pengiriman' ){
				$id = mysqli_insert_id($this->con);
				$this->field_insert .= ', kode_item_kirim';
				$this->value_insert .= "'{$id}'";
			}
			else if ($this->table_name == 'tbl_pengiriman' ){
				$id = mysqli_insert_id($this->con);
				$this->field_insert .= ', kode_pengiriman';
				$this->value_insert .= "'{$id}'";
			}
			else if ($this->table_name == 'tbl_permintaan_kirim' ){
				$id = mysqli_insert_id($this->con);
				$this->field_insert .= ', kode_permintaan';
				$this->value_insert .= ", '{$id}'";
			}
		}


		// ==============================================================
		// ================= GETTING TENANTS ID =========================
		// ==============================================================
		global $debug;
		// $debug =1;
		$cnd = $this->condition;
		$join = $this->join;
		$str = "SELECT * from smartlogistic.tbm_tenants 
				where 
					method 					= '{$this->mode}' AND 
					db_name 				= '{$this->db_name}' AND 
					`table` 				= '{$this->table_name}' AND 
					`join` 					= '{$join}' AND 
					transaction_name 		= '{$this->transaction}' 
					";
		$con_tenants = $this->con;
		$result = mysqli_query($con_tenants, $str);
		printpre ($str);
		// printpre (mysqli_error($con_tenants));

		if (mysqli_num_rows($result) == 0){
			$str = "INSERT INTO smartlogistic.tbm_tenants (method,  db_name, `table`, `join`,  transaction_name, domain)
					VALUES (
							'{$this->mode}',
							'{$this->db_name}',
							'{$this->table_name}',
							'{$join}',
							'{$this->transaction}',
							'MOS'
						)
				";
			printpre($str);
			mysqli_query($con_tenants, $str);
			$id = mysqli_insert_id($con_tenants);
		}
		else{
			$id = mysqli_fetch_assoc($result);
			$id = $id['id'];
		}


		// ==============================================================
		// ================= SEND API 		=============================
		// ==============================================================

		$data = [

			'field'					=> $this->mode == 'INSERT'? $this->field_insert:'',
			'data'					=> $this->mode == 'INSERT'? $this->value_insert: ($this->mode = 'UPDATE'? $this->field_update: []),
			'tenant_id'				=> $id,
			'condition'				=> $cnd,
			'supplier' 				=> $this->supplier_id
			
			];

		printpre($data);
			// sendPOST($OUTBOUNDAPI, $data);

		// ==============================================================
		// ================= MAKE JSON FILE =============================
		// ==============================================================

		if (!is_dir($outbound_folder)) {
		    // If it doesn't exist, create it with permissions (e.g., 0755 for read/write access)
		    if (mkdir($outbound_folder)) {
		        // echo "Folder created successfully: $folderPath";
		    } else {
		        echo "Failed to create folder: $folderPath";
		    }
		}
		
		$folderPath = "{$outbound_folder}/{$controller}/";

		if (!is_dir($folderPath)) {
		    // If it doesn't exist, create it with permissions (e.g., 0755 for read/write access)
		    if (mkdir($folderPath)) {
		        // echo "Folder created successfully: $folderPath";
		    } else {
		        echo "Failed to create folder: $folderPath";
		    }
		}
		$folder_structure = ['01_Original', "02_Backup", "03_Processing", "10_Logs", "99_Archived"];
		foreach($folder_structure as $folder){
			$folderPath = "{$outbound_folder}/{$controller}/{$folder}/";
			
			if (!is_dir($folderPath)) {
				// If it doesn't exist, create it with permissions (e.g., 0755 for read/write access)
				if (mkdir($folderPath)) {
					// echo "Folder created successfully: $folderPath";
				} else {
					echo "Failed to create folder: $folderPath";
				}
			}
		}
		$folderPath = "{$outbound_folder}/{$controller}/{$folder_structure[0]}/";

		$filename = $folderPath . "{$counter['document_id']}_{$this->transaction}_{$id}_". date("Y-m-d-H-i-s").".json" ;
		printpre($filename);
		$fileHandle = fopen($filename, "w");
		$datajson = json_encode($data);
		printpre($datajson);
		fwrite($fileHandle, $datajson);
		fclose($fileHandle);
	}

}

?>