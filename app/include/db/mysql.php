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
	
	function __construct($connect=TRUE)
	{
		if($connect) $this -> connect();
	}

	function __destruct()
	{
		if(is_resource($this -> Link)) mysql_close($this -> Link);
		if($this -> debug) {
			$h = fopen("c:\\query.txt", "w+");
			fwrite($h, "".$this -> query."");
			fclose($h);
		}
	
	}
	
	function connect()
	{
		$this -> Link = mysql_pconnect($this -> Server, $this -> User, $this -> Pass) 
		or die("Could not connect : " . mysql_error());
		mysql_select_db($this -> Db) or die("Could not select database");
	}
	
	function query($sql)
	{
		if($this -> debug) $this -> query .= $sql."\r\n";
		$result = mysql_query($sql) or die(mysql_error().' in query: '.$sql.'');
		return $result;
	}
		
	function insertRow($sql)
	{
		$result = $this -> query($sql);
		$lastid = mysql_insert_id($this -> Link);
		return $lastid;
	}
	
	function numRows($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else $result = $sql;
		return mysql_num_rows($result);
	}
	
	function numRowsResult($result)
	{
		return mysql_num_rows($result);
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
		return $this -> getRows("SHOW COLUMNS FROM ". $table ."");
	}
	
	function getRow($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		$array = mysql_fetch_array($result, MYSQL_ASSOC);
		mysql_free_result($result);
		return $array;
	}
		
	function getRows($sql)
	{
		if(!is_resource($sql)) $result = $this -> query($sql);
		else
		{
			$result = $sql;
		}
		$array = NULL;
		$i = 0;
		while($ar = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$array[$i] = $ar;
				$i++;
			}
		mysql_free_result($result);	
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
		$i = 0;
		while($ar = mysql_fetch_array($result, MYSQL_NUM))
		{
			$array[$i] = $ar;
			$i++;
		}
		mysql_free_result($result);	
		return $array;	
	}
	
	function getObjects($sql)
	{
		$result = $this -> query($sql);
		$array = NULL;
		$i = 0;
		while($ar = mysql_fetch_object($result))
		{
			$array[$i] = $ar;
			$i++;
		}
		mysql_free_result($result);	
		return $array;	
	}
		
	
	function escape($value) {
		return mysql_real_escape_string($value, $this -> Link);
	}	
	
	function getObject($sql)
	{
		$result = $this -> query($sql);
		$array = mysql_fetch_object($result);
		mysql_free_result($result);
		return $array;
	}
	
	function insertArray(&$obj, $table, $id)
	{
		$sql = "INSERT INTO $table (";
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
		$getSql = "SELECT * FROM $table WHERE $id = '$lastid'";
		$obj = $this -> getRow($getSql);
		return $obj;
	}
		
	function updateArray(&$obj, $table, $id)
	{
		$sql = "UPDATE $table SET ";
		$i = 0;	
		foreach ($obj as $name => $value) {
			if($name != $id && (!empty($value) || $value === 0 || $value === '0' || $value == '(NULL)'))
			{
				if($value!='(NULL)') {
					if($i==0) $sql .= "`$name` = '$value'";
    				else $sql .=", `$name` = '".$this -> escape($value, $this -> Link)."'";
			}
			else {
				if($i==0) $sql .= "`$name` = ''";
    			else $sql .=", `$name` = ''";
				}
				$i++;
			} 
		}
		$sql .= " WHERE $id = '". $obj[$id] ."'";	
		$this -> query($sql);
		$getSql = "SELECT * FROM $table WHERE $id = '". $obj[$id] ."'";
		$obj = $this -> getRow($getSql);
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
	/* mysql helpers */
	function columns($options = array(), $tbl="")
	{
		$sql = "";
		if(isset($options)) {
			$i = 0;
			if($tbl) $table = "`$tbl`.";
			foreach($options as $key=>$option) {
				if($i) $sql .= ", ". $table ."`".$option."`";
				else $sql .= " ". $table ."`".$option."`";
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
}
?>