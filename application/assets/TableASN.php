<?php

$base_path = dirname(__FILE__);
$base_path2 = dirname(__FILE__,3);

include_once "{$base_path2}/application/config/connectionASN.php";
include_once "{$base_path2}/application/config/connectionSL.php";


include_once "{$base_path}/utility_function_withoutJS.php";
include_once "{$base_path}/Cache.php";

$table_by_supplier 	= [];
$table_ho 			= [];
$table_master 		= [];
$db_supplier 		= [];
$outbound_folder 	= "C:/inbound";
$outbound_table		= ['tbl_item_pengiriman', 'tbl_pengiriman', 'tbl_permintaan_kirim', 'tbl_resv_gi', 'tbl_good_receipt_doc'];
class TableASN{
	public $mode;
	public $field_insert="*";
	public $data_insert = [];
	public $join 		= '';
	public $condition 	= 'TRUE';
	public $group 		= '';
	public $limit 		= '';
	public $order 		= '';
	public $as 			= '';
	public $field_update= '';
	public $data_update = [];
	public $value_insert = '';
	public $column_insert = '';

	public $db_name;
	public $con;
	public $supplier_id = '';
	public $query = '';
	public $hard_db;

	public $fetched = 0;
	public $sql = '';
	public $outbound_flag = 0;
	public $transaction = '';

	public $condition_param 	= [];

	function __construct($table_name, $supplier = '') {
		global $conASNPDO;
		if ($supplier != ''){ // $supplier is id supplier is a number
			if (!is_numeric($supplier))
				$supplier = '';
		}
		$this->supplier_id 		= $supplier;
		$this->db_name 			= $this->getdbName($table_name);
        $this->table_name 		= $table_name;
        $this->con 				= $conASNPDO;
        $this->resetVariables();

        return $this;
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
    		// printpre($_POST);
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
		$this->condition_param 	= [];

		return $this;
    }

    function insertID(){
    	return $this->con->lastInsertId();
    }

    function seedSupplier($db){

		global $con;
		global $db_host;
		global $db_user;
		global $db_pswd;
		global $db_supplier;
		global $cache;
		global $debug;
		// $debug =1;
		// printpre($db,1);
		$str 	=  "SELECT * from `dbasnho`.tbm_db_supplier where name = '{$db}'";
		$result = mysqli_query($con, $str);
		if(mysqli_num_rows($result) >0){
			$db_supplier[]=$db;
			$cache->set("db_supplier", $db_supplier);
			// return;
		}
		else{
			$str 	= "INSERT INTO `dbasnho`.tbm_db_supplier (name) VALUES ('{$db}')";
			$db_supplier[]=$db;
			$cache->set("db_supplier", $db_supplier);
				
		}
		
		printpre($str);
		mysqli_query($con, $str);

		$str = "CREATE DATABASE IF NOT EXISTS {$db}";

		mysqli_query($con, $str);

		$str = "USE {$db}";
		mysqli_query($con, $str);


		$str = file_get_contents("application/models/migrations/dbasn_supplier.sql");
		$mysqli = new mysqli($db_host, $db_user, $db_pswd, $db);
		if ($mysqli->multi_query($str)) {
		    do {
		        if ($result = $mysqli->store_result()) {
		            $result->free();
		        }

		    } while ($mysqli->more_results() && $mysqli->next_result());
		}
		else{
			printpre($mysqli->error);
		}


		$db_supplier[]=$db;
		global $table_by_supplier;
		foreach ($table_by_supplier as $table){
			$sql_atom = [];
			$sql = "CREATE OR REPLACE VIEW dbasnho.{$table}_combined AS ";
			foreach($db_supplier as $name){
				$sql_atom [] = "SELECT * FROM {$name}.{$table} ";
			}
			$sql.= implode(" UNION ALL ",$sql_atom);
			printpre($sql);
			mysqli_query($con, $sql);	
		}
		

		$cache->set("db_supplier", $db_supplier);
	}



    function getdbName($table_name, $create_new = 0){
    	if (isset($this->db_name) and $create_new ==0)
    		return $this->db_name;
    	// return 'smartlogistic';
    	global $table_by_supplier;
    	global $table_ho;
    	global $table_master;
    	global $db_supplier;
    	if (in_array(  $table_name, $table_ho))
			return 'dbasnho';
		else if (in_array(  $table_name, $table_by_supplier)){
			if ($this->supplier_id){
				$db = "dbasn_{$this->supplier_id}";
				if(!in_array($db, $db_supplier)){
					//database not yet created so create new db
					$this->seedSupplier($db);
				} 
				// printpre([$db, in_array($db, $db_supplier)],1);
					
				return $db;
			}
			return false;
		}
		else if (in_array(  $table_name, $table_master)){
			return 'dbmaster';
		}
		else{
			return 'smartlogistic';
		}
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




	function where($condition, $param = []){
		// condition is condition string
		// using param to prevent sql injection 
		// use ? to insert param into the condition
		// ie 
		// 		condition = "username = ? AND password = ?"
		//		param = ['user', 'password']

		$this->condition = $condition;
		// printpre("===============");
		// printpre($param);
		$param = flattenArray($param);
		// printpre($param);
		// printpre("===============");
		// printpre($param
		// $param_n = [];
		// foreach($param as $p){
		// 	if (is_array($p)){
		// 		$param_n = array_merge($param_n, $p);
		// 	}
		// 	else
		// 		$param_n[] = $p;
		// }

		// $param_n2 = [];
		// foreach($param_n as $p){
		// 	if (is_array($p)){
		// 		$param_n2 = array_merge($param_n2, $p);
		// 	}
		// 	else
		// 		$param_n2[] = $p;
		// }

		$this->condition_param = $param;
		return $this;

	}

	function join($tbl = '', $glue=[]){
		$db2 	=  $this->getdbName($tbl,1);
		$join 	= " LEFT JOIN `{$db2}`.`{$tbl}` ON ";
		$glues = '';
		if ( is_array($glue[0]) ){
			foreach ($glue as $g){
				$glues.= "`{$this->db_name}`.`{$this->table_name}`.$g[0] = `{$db2}`.`{$tbl}`.$g[1] ";
				if ($this->as)
					$glues.= "`{$this->db_name}`.`{$this->as}`.$g[0] = `$name`.$g[1] ";
				if (in_string($g[0], "."))
					$glues = "$g[0] = `{$db2}`.`{$tbl}`.$g[1] ";
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
	
	// function update($fields, $add_parenthesis = 1){
	// 	$this->mode = 'UPDATE';
	// 	$f = [];
	// 	$this->data_update +=  $fields;
	// 	foreach($fields as $key => $value){
	// 		if ($add_parenthesis)
	// 			$f[] = "`{$key}` = '$value'";
	// 		else 
	// 			$f[] = "`{$key}` = $value";
	// 	}
	// 	if ($this->field_update!='')
	// 		$this->field_update.=", "	;
	// 	$this->field_update .= implode(",", $f);
	// 	return $this;
	// }

	function update($fields, $add_parenthesis = 1){
		$this->mode = 'UPDATE';
		$f = [];
		if ($add_parenthesis)
			$this->data_update +=  $fields;
		foreach($fields as $key => $value){
			if ($add_parenthesis)
				$f[] = "`{$key}` = ?";
			else 
				$f[] = "`{$key}` = $value";
		}
		if ($this->field_update!='')
			$this->field_update.=", "	;
		$this->field_update .= implode(",", $f);
		return $this;
	}



	function insert($data, $add_parenthesis = 1){
		$this->mode = 'INSERT';
		
		$field 		= implode(",", array_keys($data));
    	// $data 		= array_values($data);
    	// printpre($data);
    	if ($add_parenthesis ==1){
    		$this->data_insert += $data;
    		// $content 	= implode("','", array_values($data));
    		// $content 	= "'{$content}'";
    		$content = [];
    		foreach ($data as $key => $value){
    			$content[] =  "?";
    		}
    		$content 	= implode(", ", array_values($content));

    		// $content .= ")";

    	}
    	else
    		$content 	= implode(",", array_values($data));

    	
    	if ($this->field_insert!= '*')
    		$this->field_insert .= ",";	
    	else
    		$this->field_insert = '';
		$this->field_insert .= $field;
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
		// $this->data_insert += $data;
		$data_arr 	= [];
		// printpre(['DDDDDD', $data]);
		foreach($data as $row){
			// printpre(["RRRR", $row]);
			$d 		= [];
			foreach($field_arr as $col){ //ensure in same order
				// printpre($row[$col]);
				$row[$col] =str_replace("'", "\'", $row[$col]);
				$d[] = $row[$col];
			}
			$d 	=  implode("','", array_values($d));;
			$d 	= "('{$d}')";
			$data_arr[] = $d;
		}
		$content = implode(", ", $data_arr);
		if ($this->value_insert)
			$this->value_insert .= "," . $content;	
		else
	    	$this->value_insert = $content;
    	return $this;
	}
	function replace($data, $add_parenthesis = 1){
		$this->mode = 'REPLACE';
		$field 		= implode(",", array_keys($data));
    	// $data 		= array_values($data);
    	// printpre($data);
    	if ($add_parenthesis ==1){
    		$this->data_insert += $data;
    		// $content 	= implode("','", array_values($data));
    		// $content 	= "'{$content}'";
    		$content = [];
    		foreach ($data as $key => $value){
    			$content[] =  "?";
    		}
    		$content 	= implode(", ", array_values($content));

    		// $content .= ")";

    	}
    	else
    		$content 	= implode(",", array_values($data));

    	
    	if ($this->field_insert!= '*')
    		$this->field_insert .= ",";	
    	else
    		$this->field_insert = '';
		$this->field_insert .= $field;
    	if ($this->value_insert!= ''){
    		$this->value_insert.=",";
    	}

    	$this->value_insert .= $content;
    	return $this;
	}

	function multiReplace($data){
		$this->mode = 'MULTIREPLACE';
		$field_arr 	= array_keys($data[0]);
		$field 		= implode(",", array_keys($data[0]));
		$this->field_insert = $field;
		// $this->data_insert += $data;
		$data_arr 	= [];
		foreach($data as $row){
			$d 		= [];
			foreach($field_arr as $col){ //ensure in same order
				$row[$col] =str_replace("'", "\'", $row[$col]);
				$d[] = $row[$col];
			}
			$d 	=  implode("','", array_values($d));;
			$d 	= "('{$d}')";
			$data_arr[] = $d;
		}
		$content = implode(", ", $data_arr);
    	if ($this->value_insert)
			$this->value_insert .= "," . $content;	
		else
	    	$this->value_insert = $content;
    	return $this;
	}
	
	function delete(){
		$this->mode = 'DELETE';
		return $this;
	}

	function SQLRemoveTrailing($str, $trail){
		// $str = trim($str);
		// printpre("======================",1);
		// printpre($str,1);
		if (is_string($str))
			$str = rtrim($str, $trail);
		// printpre("======================",1);
		return $str;
	}
	function SQL(){
		$this->field_insert = $this->SQLRemoveTrailing($this->field_insert, ",");
		$this->field_update = $this->SQLRemoveTrailing($this->field_update, ",");
		$this->field_insert = $this->SQLRemoveTrailing($this->field_insert, ",");
		$this->value_insert = $this->SQLRemoveTrailing($this->value_insert, ",");
		$this->group 		= $this->SQLRemoveTrailing($this->group, ",");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, " AND");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, " and");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, " or");
		$this->condition 	= $this->SQLRemoveTrailing($this->condition, " OR");

		$sql = '';
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
		if ($this->db_name == ''){
			// printpre_err("no db name",1);
			return [];
		}
		// printpre_err("execute",1);
		global $debug;

		//===================== PREPARING THE QUERY =============================
		$sql = $this->SQL();			

		$stmt = $this->con->prepare($sql); //prepare
		if (!$stmt){
			return Null;
		}
		//===================== PREPARING THE QUERY =============================

		//===================== EXECUTING THE QUERY =============================

		// ------------------- binding the field ---------------------------
		$counter = 1;
		$sql2= $sql;
		foreach($this->data_update as $key =>$value){
			$sql2 = replaceFirst("?", $value, $sql2);
			$stmt->bindValue($counter, $value);
			// printpre(['binding', $counter, $value]);
			$counter+=1;
		}
		foreach($this->data_insert as $key =>$value){
			if (is_array($value))
				continue;
			// printpre($value);
			$sql2 = replaceFirst("?", $value, $sql2);
			$stmt->bindValue($counter, $value);
			// printpre(['binding', $counter, $value]);
			$counter+=1;
		}
		// --------------- binding the condition ---------------------------
		
		foreach($this->condition_param as $condition_value){
			$sql2 = replaceFirst("?", $condition_value, $sql2);
			$stmt->bindValue($counter, $condition_value);
			printpre(['binding', $counter, $condition_value]);
			$counter+=1;
		}
		// -----------------------------------------------------------------
		printpre($this->condition_param);
		printpre($sql2);
		// printpre($stmt->queryString);
		$res = $stmt->execute();
		$errorInfo = $stmt->errorInfo();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//===================== EXECUTING THE QUERY =============================
		// ------------------- checking error --------------------------
		// printpre([
		// 			'col' => count($res), 
		// 			// 'res'=> $res, 
		// 			'sql' => $stmt->queryString, 'cond' => $this->condition_param,
		// 			'boundVariablesCount ' => $stmt->rowCount()
		// 		],1);
		if($errorInfo[2] != ''){
			
			printpre_err(['str' => $sql2, 'var' => $this->condition_param],1);
			printpre_err($errorInfo,1);

		}
		else{
			// ------------------- make all return have same type --------------------------
			// return is array of row
			$this->col_num = $stmt->rowCount();
			if (is_array($res)){
				// printpre([
				// 	'col' => count($res), 
				// 	// 'res'=> $res, 
				// 	'sql' => $stmt->queryString, 'cond' => $this->condition_param,
				// 	'boundVariablesCount ' => $stmt->rowCount()
				// ]);
			}
			// ----------------------------------------------------------------------------
			
			
			// ------------------------------- Make outboud -------------------------------
			global $outbound_table;
			if ($this->outbound_flag){
				$this->makeOutbound();
			}
			else{
				global $action;
				if ( in_array($this->table_name, $outbound_table)   AND $this->mode != 'SELECT' AND $action !='send_master'){
					$this->outbound();$this->makeOutbound();
				}
			}
			// ----------------------------------------------------------------------------


			if ($reset)
				$this->resetVariables();


			$id = $this->insertID();
			if(in_string($this->mode, "INSERT") OR in_string($this->mode, "REPLACE")) 
				return $id;
			else{
				if (!is_array($res))
					$res = [];
				return $res;
			}

		}

	}
	function fetch(){
		// deprecated, execute already return array of rows
		$result = $this->execute();
		return $result;
			
	}
	
	function fetchOne(){
		// to get only one row and return the column value of the row

		$this->limit(0,1);
		$result = $this->execute();
		if (isset($result[0]))
			return $result[0];
		else
			return [];
		// if (count($result) == 0)
		// 	return [];
		// else
		// 	return $result[0];

	}


	function fetchAll(){
		// deprecated, execute already return array of rows
		$result = $this->execute();
		return $result;
	}

	function fetch_next(){
		$this->limit($this->fetched, 1);
		$this->fetched+=1;
		$result = $this->execute(0);
		printpre($this);
		return mysqli_fetch_assoc($result);;
	}
	function transaction($transaction){
		$transaction->addTransactionSQL($this->SQL());
		return $transaction;
	}

	function makeOutbound(){
		// $debug = 1;
		global $conSL;
		$table = new Table($this->table_name);
		$table->mode = $this->mode;
		$table->condition = $this->condition;
		if ($this->mode == 'UPDATE'){
			$data = $this->field_update;
			$data = str_replace('?', "'%s'", $data);
			$data = sprintf($data, ...array_values($this->data_update));
			$table->field_update = $data;

		}
		else{
			$data = $this->value_insert;
			$data = str_replace('?', "'%s'", $data);
			$data = sprintf($data, ...array_values($this->data_insert));
			$table->value_insert = $data;
			$table->field_insert = $this->field_insert;

		}
		$sql = $table->SQL();
		printpre($sql);
		$result = mysqli_query($conSL, $sql);
		// $debug = 0;
		return;
	}

}

$cache 	= new Cache();

if($cache->check("table_by_supplier")){
	$table_by_supplier = $cache->get("table_by_supplier");
}
else{
	$str 	= "SELECT table_name FROM information_schema.tables
			WHERE table_schema = 'dbasn_master'";
	$result = mysqli_query($conASN, $str);
	while($row= mysqli_fetch_assoc($result)){
		$table_by_supplier[]=$row['table_name'];
	}

	$cache->set("table_by_supplier", $table_by_supplier);
}


if($cache->check("table_master")){
	$table_master = $cache->get("table_master");
}
else{

	$str 	= "SELECT table_name FROM information_schema.tables
				WHERE table_schema = 'dbmaster'";
	$result = mysqli_query($conASN, $str);
	while($row= mysqli_fetch_assoc($result)){
		$table_master[]=$row['table_name'];
	}
	$cache->set("table_master", $table_master);
}


if($cache->check("table_ho")){
	$table_ho = $cache->get("table_ho");
}
else{
	$str 	= "SELECT table_name FROM information_schema.tables
				WHERE table_schema = 'dbasnho'";
	$result = mysqli_query($conASN, $str);
	while($row= mysqli_fetch_assoc($result)){
		$table_ho[]=$row['table_name'];
	}
	$cache->set("table_ho", $table_ho);
}
// $db_supplier = ['smartlogistic_1'];
if($cache->check("db_supplier")){
	$db_supplier = $cache->get("db_supplier");
}
else{
	// $
	// $__sup = new Table("tbm_db_supplier");
	// $res   = $__sup->get("DISTINCT(name)")->execute();
	// while($r=mysqli_fetch_assoc($res)){
	// 	$db_supplier[]=$r['name'];
	// }
	$str 	= "SHOW DATABASES LIKE 'dbasn_%'";
	$result = mysqli_query($conASN, $str);
	while($row= mysqli_fetch_assoc($result)){
		if ($row['Database (dbasn_%)'] == 'dbasnho')
			continue;
		$db_supplier[]=$row['Database (dbasn_%)'];
	}

	$cache->set("db_supplier", $db_supplier);
}
$debug = 0;

// printpre($table_ho);
// printpre($table_master);
// printpre($table_by_supplier);
// printpre($db_supplier,1);
?>