<?php
class Proc extends DataSource{
	var $arg_list;
	var $proc_name = "";
	function __construct() {
		$args = func_get_args();
		$this -> setProcName($args[0]);
		for($i=1;$i<count($args);$i++)	{
			$this -> addArg($args[$i]);
		}
		$this -> call();
	}
	
	function setArgList($args = array()) {
		$this -> arg_list = args;
	}
	
	function addArg($value) {
		$this -> arg_list[] = $value;
	}
	
	function setProcName($name) {
		$this -> proc_name = $name;
	}
	
	function call() {
		global $db;
		$sql = "call `". $this -> proc_name ."`(";
		$sql .= "'";
		$sql .= implode("','", $this -> arg_list);
		$sql .= "');";
		$this -> _dataSource = $db -> callProc($sql);
		$this -> _data = $this -> _dataSource[0];
	}
}
?>