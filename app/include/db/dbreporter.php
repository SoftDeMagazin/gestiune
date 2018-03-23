<?php
class dbReporter {
	var $tbls = array();
	var $db;
	
	var $tbl_list = array();
	
	var $columns = array();
	
	var $functions = array("sum", "avg", "count", "min", "max");
	
	function __construct() {
		global $db;
		$this -> db = $db;
		$this -> getTables();
	}
	
	function getTables() {
		$tbls = $this -> db -> showTables();
		foreach($tbls as $tbl) {
			$this -> tbls[] = $tbl[0];
		} 
	}
	
	function selectTables() {
		$out = '<select multiple size="10" name="table[]" id="table" style="">';
		foreach($this -> tbls as $tbl) {
			$out .= '<option value="'. $tbl .'">'. $tbl .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
	
	function tableColumns($table) {
		return $this -> db -> tableColumns($table);
	}
	
	
	function selectColumns($table) {
		$clmns = $this -> tableColumns($table);
		$out = '<select name="table" id="table" style="">';
		$out .= '<option value="*">*</option>';	
		foreach($clmns as $clmn) {
			$out .= '<option value="'. $clmn .'">'. $clmn .'</option>';	
		}
		$out .= '</select>';
		return $out;
	}
	
	function selectFunctions() {
		$out = '<select name="table" id="table" style="">';
		$out .= '<option value=""></option>';	
		foreach($this -> functions as $fn) {
			$out .= '<option value="'. $fn .'">'. $fn .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
	
	function functions() {
		
	}
}
?>