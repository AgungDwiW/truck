<?php

$base_path = dirname(__FILE__);
$base_path2 = dirname(__FILE__, 3);
include  "application/config/connection.php";
$db_default = 'smartlogistic';

class Table {

	// ------------------------------------------------------------------------
    // Properties
    // ------------------------------------------------------------------------
	
	public $mode;
	public $field_insert = "*";
	public $data_insert = [];
	public $join = '';
	public $condition = 'TRUE';
	public $group = '';
	public $limit = '';
	public $order = '';
	public $having = '';
	public $as = '';
	public $field_update = '';
	public $data_update = [];
	public $value_insert = '';
	public $column_insert = '';
	public $__joins = [];
	public $db_name;
	public $con = NULL;
	public $supplier_id = '';
	public $query = '';
	public $hard_db;

	public $fetched = 0;
	public $sql = '';
	public $transaction = '';

	public $table_name = '';
	public $transaction_name = '';
	public $col_num = 0;
	public $db_model_table = [];
	public $db_model_name;
	public $condition_param = [];

	// ------------------------------------------------------------------------
    // Initialization
    // ------------------------------------------------------------------------

	function __construct($table_name, $dbname = '', $conn = '') {
		global $con, $db_model;
		$this->supplier_id = '';
		
		$this->con = ($conn == '') ? $con : $conn;
        
		if (isset($db_model) && $db_model) {
        	$this->db_model_name = $db_model;
        	$this->getTableList();
        }

		$this->db_name = ($dbname != '') ? $dbname : $this->getdbName($table_name);
        $this->table_name = $table_name;

        if (in_string($table_name, ".")) {
        	$table_name = str_replace("`", '', $table_name);
        	$key = explode(".", $table_name);
        	$this->table_name = $key[1];
        	$this->db_name = $key[0];
        }
		
        $this->resetVariables();
        return $this;
    }

    function getTableList() {
    	
    	$con = $this->con;
    	$cache = new Cache();
		if ($cache->check("table_" . $this->db_model_name)) {
			$tbl = $cache->get("table_" . $this->db_model_name);
		} else {
			$str = "SELECT table_name FROM information_schema.tables WHERE table_schema = '{$this->db_model_name}'";
			$result = mysqli_query($con, $str);
			if ($GLOBALS['debug']) Debuger::dump($str);
			
			$tbl = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$tbl[] = $row['table_name'];
			}
			$cache->set("table_" . $this->db_model_name, $tbl);
		}
		$this->db_model_table = $tbl;
    }

    function getdbName($table_name) {
    	global $db_default;
    	if (in_array($table_name, $this->db_model_table)) {
    		return $this->db_model_name;
		}
    	return $db_default;
    }

	// ------------------------------------------------------------------------
    // Query Builders (Select, Join, Where, etc.)
    // ------------------------------------------------------------------------

    function as($str) {
    	$this->as .= $str;
    	return $this;
    }

	function get(...$fields) {
		$this->mode = "SELECT";
		if ($fields) {
			$this->field_insert = ($this->field_insert != '*') ? $this->field_insert . "," : '';
			$this->field_insert .= implode(",", $fields);
		} else {
			$this->field_insert = '*';
		}
		return $this;
	}

	/**
	 * NOTE: Because this function accepts raw strings to preserve compatibility, 
	 * it cannot be automatically protected against SQL injection. 
	 * Please ensure variables passed to where() are manually escaped using mysqli_real_escape_string.
	 */
	
	function _where($condition){
		if ($this->condition == 'TRUE') $this->condition = '';	
		$this->condition .= " " . $condition;
		return $this;
	}
	function where($condition) {
		if (is_array($condition))
			return $this->whereQ($condition);
		else 
			return $this->_where($condition);
		
	}

	/**
	 * Django Q-like flexible and secure query builder.
	 * Allows nested conditions, OR/AND logic, and prevents SQL injection.
	 *
	 * @param array $conditions Structured array of conditions
	 * @return $this
	 * 
	 * Sample :
	 * $table->get()
     * ->whereQ([
     *     'OR' => [
     *         'AND' => [
     *             ['first_name', '=', 'Jane'],
     *             ['last_name', '=', 'Doe']
     *         ],
     *         'AND' => [
     *             ['first_name', '=', 'John'],
     *             ['last_name', '=', 'Smith']
     *         ]
     *     ],
     *     ['deleted_at', 'IS', 'NULL']
     * ])
     * ->fetch();
	 * SQL Output: WHERE (((`first_name` = 'Jane' AND `last_name` = 'Doe') OR (`first_name` = 'John' AND `last_name` = 'Smith')) AND `deleted_at` IS NULL)
	 * 
	 */

	function whereQ(array $conditions) {
		if (empty($conditions)) {
			return $this;
		}

		$parsed_sql = $this->_parseQ($conditions);

		if (!empty($parsed_sql)) {
			if ($this->condition === 'TRUE' || trim($this->condition) === '') {
				$this->condition = "({$parsed_sql})";
			} else {
				$this->condition .= " AND ({$parsed_sql})";
			}
		}

		return $this;
	}

	/**
	 * Recursive helper to parse the Q-array securely.
	 *
	 * @param array $conditions
	 * @param string $logical 'AND' or 'OR'
	 * @return string
	 */
	private function _parseQ(array $conditions, $logical = 'AND') {
		$sql_parts = [];
		$allowed_operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT'];

		// Check if it's a single condition definition: ['field', 'operator', 'value']
		if (isset($conditions[0]) && is_string($conditions[0]) && count($conditions) === 3 && in_array(strtoupper(trim($conditions[1])), $allowed_operators)) {
			$column = mysqli_real_escape_string($this->con, str_replace('`', '', $conditions[0]));
			$operator = strtoupper(trim($conditions[1]));
			$value = $conditions[2];

			// Handle IN and NOT IN (expects array of values)
			if ($operator === 'IN' || $operator === 'NOT IN') {
				if (!is_array($value) || empty($value)) return "1=0"; // Fail safely
				
				$safe_values = [];
				foreach ($value as $v) {
					$safe_v = mysqli_real_escape_string($this->con, $v);
					$safe_values[] = $this->encaseValueCheckEscape($v) ? $v : "'{$safe_v}'";
				}
				$val_string = implode(', ', $safe_values);
				return "`{$column}` {$operator} ({$val_string})";
			}

			// Handle standard operators
			if ($this->encaseValueCheckEscape($value)) {
				return "`{$column}` {$operator} {$value}";
			} else {
				$safe_val = mysqli_real_escape_string($this->con, $value);
				return "`{$column}` {$operator} '{$safe_val}'";
			}
		}

		// Otherwise, iterate through the array for nested logic
		foreach ($conditions as $key => $value) {
			if ($key === 'AND' || $key === 'OR') {
				// Handle explicit logical grouping
				$nested = $this->_parseQ($value, $key);
				if ($nested) {
					$sql_parts[] = "({$nested})";
				}
			} elseif (is_int($key) && is_array($value)) {
				// Handle sequential arrays (defaults to the current $logical operator)
				$nested = $this->_parseQ($value, $logical);
				if ($nested) {
					// Only wrap in parentheses if it's a complex nested string
					$sql_parts[] = (count($value) > 3 || isset($value['AND']) || isset($value['OR'])) ? "({$nested})" : $nested;
				}
			}
		}

		return implode(" {$logical} ", $sql_parts);
	}

	/**
	 * JOIN
	 * * @param string $tbl  The table to join. Supports 'db.table' and aliases 'table AS t'
	 * @param mixed  $glue The ON condition. Can be the old array format, an associative array, or a raw string.
	 * @param string $type The type of JOIN (LEFT, INNER, RIGHT). Defaults to 'LEFT'.
	 * @return $this
	 */
	function join($tbl, $glue, $type = 'LEFT') {
		$type = strtoupper(trim($type));
		$allowed_types = ['LEFT', 'RIGHT', 'INNER', 'OUTER', 'CROSS'];
		if (!in_array($type, $allowed_types)) {
			$type = 'LEFT'; // Fallback to safe default
		}

		// 1. Parse Database, Table Name, and Alias
		$db2 = $this->getdbName($tbl);
		$alias = '';

		// Check for alias (e.g., "users AS u" or "users u")
		if (preg_match('/(?:\s+AS\s+|\s+)([\w_]+)$/i', $tbl, $matches)) {
			$alias = $matches[1];
			$tbl = preg_replace('/(?:\s+AS\s+|\s+)' . preg_quote($alias, '/') . '$/i', '', $tbl);
		}

		if (in_string($tbl, '.')) {
			$parts = explode(".", $tbl);
			$db2 = $parts[0];
			$tbl = $parts[1];
		}

		$db2 = str_replace("`", "", $db2);
		$tbl = str_replace("`", "", $tbl);
		$alias_sql = $alias ? " AS `{$alias}`" : "";
		$target_table = $alias ? "`{$alias}`" : "`{$db2}`.`{$tbl}`";
		$source_table = $this->as ? "`{$this->as}`" : "`{$this->db_name}`.`{$this->table_name}`";

		$join_statement = " 
			{$type} JOIN `{$db2}`.`{$tbl}`{$alias_sql}
			ON ";

		// 2. Parse the Glue (ON conditions)
		$glues = '';

		if (is_string($glue) && !empty($glue)) {
			// Handle raw string condition: join('users', 'users.id = posts.user_id')
			$glues = $glue;

		} elseif (is_array($glue)) {
			$conditions = [];
			
			// Detect if it's a multidimensional array (multiple conditions)
			$is_multi = isset($glue[0]) && is_array($glue[0]);
			$glue_items = $is_multi ? $glue : [$glue];
			$logical_operator = in_array('OR', $glue, true) ? ' OR ' : ' AND ';

			foreach ($glue_items as $g) {
				if (!is_array($g) || count($g) < 2) continue; // Skip logical operators like 'OR' or malformed data

				$col1 = $g[0];
				$col2 = $g[1];

				// If the column doesn't already contain a dot, prefix it with the correct table/alias
				$left_side = in_string($col1, ".") ? $col1 : "{$source_table}.`{$col1}`";
				
				// Ensure the right side is properly formatted
				if (in_string($col2, ".")) {
					$right_side = $col2;
				} elseif (is_numeric($col2) || $this->encaseValueCheckEscape($col2)) {
					$right_side = $col2; // It's a raw value or number
				} else {
					$right_side = "{$target_table}.`{$col2}`"; // It's a column name
				}

				$conditions[] = "{$left_side} = {$right_side}";
			}

			$glues = implode($logical_operator, $conditions);
		}

		// 3. Append to the class property safely
		if (!empty($glues)) {
			$this->join .= "{$join_statement} {$glues} ";
		}

		return $this;
	}

	function joinSQL($sql, $name, $glue = []) {
		$join = " LEFT JOIN ({$sql}) {$name} ON ";
		$glues = '';
		if (is_array($glue[0])) {
			foreach ($glue as $g) {
				$glues .= "`{$this->db_name}`.`{$this->table_name}`.{$g[0]} = `{$name}`.{$g[1]} ";
				if ($this->as)
					$glues .= "`{$this->db_name}`.`{$this->as}`.{$g[0]} = `{$name}`.{$g[1]} ";
				if (in_string($g[0], "."))
					$glues = "{$g[0]} = `{$name}`.{$g[1]} ";
				
				$glues .= in_array('OR', $glue) ? "OR " : "AND ";
			}
		} else {
			$glues = "`{$this->db_name}`.`{$this->table_name}`.{$glue[0]} = `{$name}`.{$glue[1]} ";
			if ($this->as)
				$glues = "`{$this->db_name}`.`{$this->as}`.{$glue[0]} = `{$name}`.{$glue[1]} ";
			if (in_string($glue[0], "."))
				$glues = "{$glue[0]} = `{$name}`.{$glue[1]} ";
		}
		
		$glues = rtrim($glues, "OR ");
		$glues = rtrim($glues, "AND ");
		$this->join .= $join . $glues;
		return $this;
	}

	function group(...$groups) {
		$this->group = "GROUP BY " . implode(",", $groups);
		return $this;	
	}

	function limit($offset = 0, $limit = 1000) {
		$this->limit = "LIMIT {$offset}, {$limit}";
		return $this;
	}

	function having($param) {
		$this->having = "HAVING {$param}";
		return $this;
	}

	function order(...$orders) {
		$this->order = "ORDER BY " . implode(", ", $orders);
		return $this;
	}

	// ------------------------------------------------------------------------
    // Data Modifiers (Insert, Update, Delete, Replace)
    // ------------------------------------------------------------------------

	function update($fields, $add_parenthesis = 1) {
		$this->mode = 'UPDATE';
		$f = [];
		foreach($fields as $key => $value) {
			// Security: Escape columns and values
			$safe_key = mysqli_real_escape_string($this->con, $key);
			if ($this->encaseValueCheckEscape($value)) {
				if ($value == NULL)
					$value = "NULL";				
				$f[] = "`{$safe_key}` = {$value}";
			} else {
				$safe_val = mysqli_real_escape_string($this->con, $value);
				$f[] = $add_parenthesis ? "`{$safe_key}` = '{$safe_val}'" : "`{$safe_key}` = {$safe_val}";
			}
		}
		
		$this->field_update .= ($this->field_update != '') ? ", " : '';
		$this->field_update .= implode(", ", $f);
		return $this;
	}

	function insert($data, $add_parenthesis = 1) {
		$this->mode = 'INSERT';
		
		// Security: Escape columns
		$safe_keys = array_map(function($key) { return mysqli_real_escape_string($this->con, $key); }, array_keys($data));
		$field = "`" . implode("`,`", $safe_keys) . "`";
		
    	$data_values = array_values($data);
    	$content = $this->encaseValue($data_values, $add_parenthesis);
		
    	$this->field_insert = $field;
    	$this->value_insert .= ($this->value_insert != '') ? "," . $content : $content;
    	return $this;
	}

	/**
	 * Insert or Update (Upsert). 
	 * If a primary key or unique index conflicts, it updates the existing record.
	 * * @param array $data The data to insert (e.g., ['id' => 1, 'name' => 'John', 'status' => 'active'])
	 * @param array $update_fields Optional. The specific fields to update if duplicate. 
	 * If empty, it updates all fields provided in $data.
	 * @param int $add_parenthesis
	 * @return $this
	 */
	function upsert($data, $update_fields = [], $add_parenthesis = 1) {
		$this->mode = 'UPSERT';
		
		// 1. Prepare INSERT portion
		$safe_keys = array_map(function($key) { return mysqli_real_escape_string($this->con, $key); }, array_keys($data));
		$this->field_insert = "`" . implode("`,`", $safe_keys) . "`";
		
    	$data_values = array_values($data);
    	$this->value_insert = $this->encaseValue($data_values, $add_parenthesis);

		// 2. Prepare ON DUPLICATE KEY UPDATE portion
		// If no specific update fields are passed, default to updating all fields from the insert data
		if (empty($update_fields)) {
			$update_fields = array_keys($data);
		}

		$f = [];
		foreach($update_fields as $key => $value) {
			if (is_int($key)) {
				// Format: ['name', 'status'] -> updates using the values provided in the insert statement
				$safe_col = mysqli_real_escape_string($this->con, $value);
				$f[] = "`{$safe_col}` = VALUES(`{$safe_col}`)"; 
			} else {
				// Format: ['status' => 'inactive'] -> hardcoded override update values
				$safe_key = mysqli_real_escape_string($this->con, $key);
				if ($this->encaseValueCheckEscape($value)) {
					if ($value == NULL)
						$value = "NULL";	
					$f[] = "`{$safe_key}` = {$value}";
				} else {
					$safe_val = mysqli_real_escape_string($this->con, $value);
					$f[] = $add_parenthesis ? "`{$safe_key}` = '{$safe_val}'" : "`{$safe_key}` = {$safe_val}";
				}
			}
		}
		
		$this->field_update = implode(", ", $f);
    	return $this;
	}


	function multiInsert($data) {
		$this->mode = 'MULTIINSERT';
		
		// Security: Escape columns
		$safe_keys = array_map(function($key) { return mysqli_real_escape_string($this->con, $key); }, array_keys($data[0]));
		$field = "`" . implode("`,`", $safe_keys) . "`";
		$this->field_insert = $field;
		
		$data_arr = [];
		$field_arr = array_keys($data[0]);
		foreach($data as $row) {
			$d = [];
			foreach($field_arr as $col) {
				$d[] = $row[$col];
			}
			$d_encased = $this->encaseValue($d);
			$data_arr[] = "({$d_encased})";
		}
		
    	$this->value_insert = implode(", ", $data_arr);
    	return $this;
	}

	function replace($data, $add_parenthesis = 1) {
		$this->mode = 'REPLACE';
		
		// Security: Escape columns
		$safe_keys = array_map(function($key) { return mysqli_real_escape_string($this->con, $key); }, array_keys($data));
		$field = "`" . implode("`,`", $safe_keys) . "`";
		
    	$data_values = array_values($data);
    	$content = $this->encaseValue($data_values, $add_parenthesis);

    	$this->field_insert = $field;
    	$this->value_insert = $content;
    	return $this;	
	}

	function multiReplace($data) {
		$this->mode = 'MULTIREPLACE';
		
		// Security: Escape columns
		$safe_keys = array_map(function($key) { return mysqli_real_escape_string($this->con, $key); }, array_keys($data[0]));
		$field = "`" . implode("`,`", $safe_keys) . "`";
		$this->field_insert = $field;
		
		$data_arr = [];
		$field_arr = array_keys($data[0]);
		foreach($data as $row) {
			$d = [];
			foreach($field_arr as $col) {
				$d[] = $row[$col];
			}
			$d_encased = $this->encaseValue($d);
			$data_arr[] = "({$d_encased})";
		}
		
    	$this->value_insert = implode(", ", $data_arr);
    	return $this;
	}
	
	function delete() {
		$this->mode = 'DELETE';
		return $this;
	}

	// ------------------------------------------------------------------------
    // Executors and Fetchers
    // ------------------------------------------------------------------------

	function execute($reset = true) {
		$str = $this->SQL();
		
		Debuger::dump($str);
		
		if (in_string($this->mode, "MULTI")) {
			$result = mysqli_multi_query($this->con, $str);
		} else {
			$result = mysqli_query($this->con, $str);
		}

		if ($result) {
			$id = $this->insertID();
			if ($reset) $this->resetVariables();
			if (in_string($this->mode, "INSERT")) return $id;
			return $result;
		} else {
			$errorMsg = "MySQL Error: " . mysqli_error($this->con);
			
			Debuger::handleError(E_USER_ERROR, $errorMsg, __FILE__, __LINE__);
			
			Debuger::dump($str);
			return 0;
		}
	}

	function fetch() {
		$result = $this->execute();
		if (mysqli_num_rows($result) == 1) {
			return mysqli_fetch_assoc($result);
		} else {
			$table = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$table[] = $row;
			}
			return $table;
		}
	}

	function fetchOne() {
		$this->limit(0, 1);
		$result = $this->execute();
		if (mysqli_num_rows($result) == 1) {
			return mysqli_fetch_assoc($result);
		} else {
			$table = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$table[] = $row;
			}
			return $table;
		}
	}

	function fetchALL() {
		$result = $this->execute();
		$table = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$table[] = $row;
		}
		return $table;
	}

	// ------------------------------------------------------------------------
    // Utilities
    // ------------------------------------------------------------------------

    function resetVariables() {
    	$this->mode = '';
		$this->field_insert = "*";
		$this->data_insert = [];
		$this->join = '';
		$this->condition = 'TRUE';
		$this->group = '';
		$this->limit = '';
		$this->field_update = '';
		$this->data_update = [];
		$this->value_insert = '';
		$this->column_insert = '';
		$this->sql = '';
		$this->transaction_name = '';
		$this->having = '';
		return $this;
    }

    function insertID() {
    	return mysqli_insert_id($this->con);
    }

	function encaseValueCheckEscape($str) {
		$escapeValue = ["NOW()", "NULL",  null];
		return in_array($str, $escapeValue);
	}

	function encaseValue($arr, $add_parenthesis = 1) {
		$ret = [];
		foreach($arr as $key => $value) {
			if ($this->encaseValueCheckEscape($value)) {
				if ($value==null || $value=='NULL')
					$ret[] = "NULL"; 
				else
					$ret[] = $value; 
			} else {
				// Security: Escape values
				$safe_val = mysqli_real_escape_string($this->con, $value);
				$ret[] = $add_parenthesis ? "'{$safe_val}'" : $safe_val; 
			}
		}
		return implode(", ", $ret);
	}

	function SQLRemoveTrailing($str, $trail) {
		return rtrim($str, $trail);
	}

	function SQL() {
		$this->field_insert = $this->SQLRemoveTrailing($this->field_insert, ",");
		$this->field_update = $this->SQLRemoveTrailing($this->field_update, ",");
		$this->value_insert = $this->SQLRemoveTrailing($this->value_insert, ",");
		$this->group = $this->SQLRemoveTrailing($this->group, ",");
		
		$this->condition = $this->SQLRemoveTrailing($this->condition, "AND");
		$this->condition = $this->SQLRemoveTrailing($this->condition, "and");
		$this->condition = $this->SQLRemoveTrailing($this->condition, "or");
		$this->condition = $this->SQLRemoveTrailing($this->condition, "OR");

		if ($this->condition == '') $this->condition = "TRUE";
		
		switch ($this->mode) {
			case "SELECT":
				$sql = "SELECT {$this->field_insert} 
						FROM `{$this->db_name}`.`{$this->table_name}` {$this->as} 
						{$this->join} 
						WHERE {$this->condition} 
						{$this->group} {$this->having} {$this->order} {$this->limit}";
				break;
			case "UPDATE":
				$sql = "UPDATE `{$this->db_name}`.`{$this->table_name}` 
						{$this->join} 
						SET {$this->field_update} 
						WHERE {$this->condition} {$this->limit}";
				break;
			case "INSERT":
				$sql = "INSERT INTO `{$this->db_name}`.`{$this->table_name}` ({$this->field_insert}) 
						VALUES ($this->value_insert)";
				break;
			case "REPLACE":
				$sql = "REPLACE INTO `{$this->db_name}`.`{$this->table_name}` ({$this->field_insert}) 
						VALUES ($this->value_insert)";
				break;
			case "DELETE":
				$sql = "DELETE FROM `{$this->db_name}`.`{$this->table_name}` 
						WHERE {$this->condition} {$this->limit}";
				break;
			case "MULTIINSERT":
				$sql = "INSERT INTO `{$this->db_name}`.`{$this->table_name}` ({$this->field_insert}) 
						VALUES $this->value_insert";
				break;
			case "MULTIREPLACE":
				$sql = "REPLACE INTO `{$this->db_name}`.`{$this->table_name}` ({$this->field_insert}) 
						VALUES $this->value_insert";
				break;
			case "UPSERT":
				$sql = "INSERT INTO `{$this->db_name}`.`{$this->table_name}` ({$this->field_insert}) 
						VALUES ($this->value_insert) 
						ON DUPLICATE KEY UPDATE {$this->field_update}";
				break;
		}
		return $sql;
	}
}
?>