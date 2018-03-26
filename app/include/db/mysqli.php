<?php
class MySQL
{	
	var $Link;
	var $Server=DB_HOST;
	var $User=DB_USER;
	var $Pass=DB_PASS;
	var $Db=DB_DATABASE;
	var $query="";
	var $debug=false;
	
	var $modelColumns = array();
	
	function __construct($connect=TRUE)
	{
		if($connect) $this -> connect();
	}

	function __destruct()
	{
		if(is_resource($this -> Link)) mysqli_close($this -> Link);
		if($this -> debug) {
			$h = fopen("c:\\query.txt", "w+");
			fwrite($h, "".$this -> query."");
			fclose($h);
		}
	}
	
	/**
	 * se conecteaza la db
	 * @return 
	 */
	function connect()
	{
		$this -> Link = mysqli_connect($this -> Server, $this -> User, $this -> Pass)
		or die("Could not connect : ");
		mysqli_select_db($this -> Link, $this -> Db) or die("Could not select database");
	}
	
	/**
	 * executa un query 
	 * @param object $sql query sql
	 * @return $result mysql result
	 */
	function query($sql)
	{
		if($this -> debug) $this -> query .= $sql."\r\n";
		$result = mysqli_query($this -> Link, $sql) or die(mysqli_error($this -> Link).' in query: '.$sql.'');
		return $result;
	}
		
	/**
	 * insereaza un rand returneaza id inserat
	 * @param object $sql
	 * @return int $id returneaza id-ul randului inserat
	 */	
	function insertRow($sql)
	{
		$result = $this -> query($sql);
		$lastid = mysqli_insert_id($this -> Link);
		return $lastid;
	}
	
	function numRows($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else $result = $sql;
		return mysqli_num_rows($result);
	}
	
	function numRowsResult($result)
	{
		return mysqli_num_rows($result);
	}
			
	function tableColumns($table)
	{
		if(!is_array($table)) {
			$rows = $this -> tableInfo($table);
		}
		else {
			$rows = $table;
		}	
		$columns = array();
		foreach($rows as $row) {
			$columns[] = $row['Field'];
		}
		return $columns;
	}
	
	function tableKey($table) {
		if(!is_array($table)) {
			$rows = $this -> tableInfo($table);
		}
		else {
			$rows = $table;
		}
		
		$columns = array();
		foreach($rows as $row) {
			if($row['Key'] == 'PRI') return $row['Field'];
		}
		return NULL;
	}
	
	function tableInfo($table) {
		if(!array_key_exists($table, $this -> modelColumns)) {
			$this -> modelColumns[$table] = $this -> getRows("SHOW COLUMNS FROM `". $table ."`");
		}
			
		return $this -> modelColumns[$table];
	}
	
	function showTables() {
		$tbl = $this -> getRowsNum("SHOW TABLES FROM ". $this -> Db ."");
		return $tbl;
	}
	
	function getRow($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		$array = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_free_result($result);
		return $array;
	}
		
	function getRows($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		if(function_exists("mysqli_fetch_all")) {
			$array = mysqli_fetch_all($result, MYSQLI_ASSOC);
		}
		else {
			$array = array();
			while($ar = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$array[] = $ar;
			}
		}	
		mysqli_free_result($result);
		return $array;	
	}
	
	function callProc($sql) {
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		if(function_exists("mysqli_fetch_all")) {
			$array = mysqli_fetch_all($result, MYSQLI_ASSOC);
		}
		else {
			$array = array();
			while($ar = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$array[] = $ar;
			}
		}	
		$this -> cleanConnection();	
		return $array;	
	}
	
	function getRowsNum($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		$array = NULL;
		if(function_exists("mysqli_fetch_all")) {
			$array = mysqli_fetch_all($result, MYSQLI_NUM);
		}
		else {
			$array = array();
			while($ar = mysqli_fetch_array($result, MYSQLI_NUM)) {
				$array[] = $ar;
			}
		}	
		mysqli_free_result($result);	
		return $array;	
	}
	
	function cleanConnection() 
	{ 
		while(mysqli_more_results($this -> Link)) { 
			if(mysqli_next_result($this -> Link)) 
			{ 
			$result = mysqli_use_result($this -> Link); 
			mysqli_free_result($result); 
			} 
		} 
	} 

	
	function getObjects($sql)
	{
		$result = $this -> query($sql);
		$array = NULL;
		$i = 0;
		while($ar = mysqli_fetch_object($result))
		{
			$array[$i] = $ar;
			$i++;
		}
		mysqli_free_result($result);	
		return $array;	
	}
		
	
	function escape($value) {
		return mysqli_real_escape_string($this -> Link, $value);
	}	
	
	function getObject($sql)
	{
		$result = $this -> query($sql);
		$array = mysqli_fetch_object($result);
		mysqli_free_result($result);
		return $array;
	}
	
	function insertArray(&$obj, $table, $id)
	{
		$sql = "INSERT INTO `$table` (";
		$i = 0;	
		foreach ($obj as $name => $value) {
			if($name != $id && !empty($value)) {
				if($i==0) $sql .= "`$name`";
    			else $sql .=", `$name`";
				$i++;
			} 
		}
		$sql .= ") VALUES (" ;	
		
		$i = 0;	
		foreach ($obj as $name => $value) {
			if($name != $id && !empty($value)) {
				if($i==0) $sql .= "'$value'";
    			else $sql .=", '".$this -> escape($value, $this -> Link)."'";
				$i++;
			} 
		}
		$sql .= ");";		
	   	$lastid = $this -> insertRow($sql);
		$getSql = "SELECT * FROM `$table` WHERE `$id` = '$lastid'";
		$obj = $this -> getRow($getSql);
		return $obj;
	}
		
	function updateArray(&$obj, $table, $id)
	{
		$sql = "UPDATE `$table` SET ";
		$i = 0;	
		foreach ($obj as $name => $value) {
			if($name != $id && (!empty($value) || $value === 0 || $value === '0' || $value == '(NULL)'))
			{
				if($value!='(NULL)') {
					if($i==0) $sql .= "`$name` = '$value'";
    				else $sql .=", `$name` = '".$this -> escape($value, $this -> Link)."'";
			}
			else {
					if($i===0) $sql .= "`$name` = '0'";
    				else $sql .=", `$name` = '0'";
				}
				$i++;
			} 
		}
		$sql .= " WHERE `$id` = '". $obj[$id] ."'";	
		$this -> query($sql);
		//$getSql = "SELECT * FROM `$table` WHERE $id = '". $obj[$id] ."'";
		//$obj = $this -> getRow($getSql);
		return $obj;
	}			
	
		
	/* object to sql */
	function insertObject(&$obj, $table, $id)
	{
		$class_vars = get_object_vars($obj);
		$sql = "INSERT INTO $table (";
		$i = 0;	
		foreach ($class_vars as $name => $value) {
			if($name != $id && !empty($value)) {
				if($i==0) $sql .= "`$name`";
    			else $sql .=", `$name`";
				$i++;
			} 
		}
		$sql .= ") VALUES (" ;	
		
		$i = 0;	
		foreach ($class_vars as $name => $value) {
			if($name != $id && !empty($value)) {
				if($i==0) $sql .= "'$value'";
    			else $sql .=", '$value'";
				$i++;
			} 
		}
		$sql .= ");";		
	   	$lastid = $this -> insertRow($sql);
		$getSql = "SELECT * FROM $table WHERE $id = '$lastid'";
		$obj = $this -> getObject($getSql);
	}
	
	function updateObject(&$obj, $table, $id)
	{
		$class_vars = get_object_vars($obj);
		$sql = "UPDATE $table SET ";
		$i = 0;	
		foreach ($class_vars as $name => $value) {
			if($name != $id && (!empty($value) || $value == 0)) {
				if($i==0) $sql .= "`$name` = '$value'";
    			else $sql .=", `$name` = '$value'";
				$i++;
			} 
		}
		$sql .= " WHERE $id = '". $class_vars[$id] ."'";	
		$this -> query($sql);
		$getSql = "SELECT * FROM $table WHERE $id = '". $class_vars[$id] ."'";
		$obj = $this -> getObject($getSql);
	}			
	
	function deleteObject($obj, $table, $id)
	{
		$class_vars = get_object_vars($obj);
		$sql = "DELETE FROM $table WHERE $id = '". $class_vars[$id] ."'";
		$this -> query($sql); 
	}	
		
	function arrayToSql($options = array())
	{
		$sql = "";
		if(isset($options)) {
			foreach($options as $key=>$option) {
				if(!is_numeric($key)) $sql .= " $key ".$option;
				else $sql .= " ".$option;
			}
		}
		return $sql;
	}
	/* mysqli helpers */
	function columns($options = array(), $tbl="")
	{
		$sql = "";
		if($tbl) $tbl = "`$tbl`.";
		if(isset($options)) {
			$i = 0;
			foreach($options as $key=>$option) {
				if($i) $sql .= ", $tbl".$option."";
				else $sql .= " $tbl".$option."";
				$i++;
			}
		}
		else {
			$sql = "*";
		}
		return $sql;
	}
	function between($s1, $s2)
	{
		return "BETWEEN '$s1' AND '$s2'";
	}		
	function equal($s)
	{
		return "= '$s'";
	}
	
	function inArray($array) {
		return "in ('".implode("','", $array) ."')";
	}
}
?>