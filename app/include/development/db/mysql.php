<?php
class MySQL implements DBInterface
{
	public $link;
	public $server="localhost";
	public $user="root";
	public $pass="dantes";
	public $defaultDb="pitosgreen";
	
	public function __construct()
	{
		$this -> connect();
		$this -> selectDb();
	}
	
	public function connect() {
		$this -> link = mysql_connect($this -> server, $this -> user, $this -> $pass);
	}
	
	public function selectDb($db=NULL) {
		if($db) mysql_select_db($db);
		else mysql_select_db($this -> defaultDb);
	}
	
	public function query($sql) {
		$result = mysql_query($sql) or die(mysql_error());
		return $result;
	}
	
	function getRows($sql) {
		if(!is_resource($sql)) $result = $this -> query($sql);
		else {
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
		else {
			$result = $sql;
		}
		$array = NULL;
		$i = 0;
		while($ar = mysql_fetch_array($result, MYSQL_NUM)) {
			$array[$i] = $ar;
			$i++;
		}
		mysql_free_result($result);	
		return $array;	
	}
}

interface DBInterface
{
	public function query($sql);
	public function connect();
	public function selectDb($db=NULL);
}
?>