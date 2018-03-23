<?php
class ArrayList {
	public $_dataSource = array();
	public $_index;	
	
	function __construct($dataSource = array()) 
	{
		$this -> _dataSource = $dataSource;
		$this -> _reset();
		
	}
	public	function add($var) 
	{
		$this -> _dataSource[] = $var;
	}
	
	private function _reset() 
	{
		$this -> _index = ($this -> count) ? $this -> count : -1;
	}	
	
	function __get($name) 
	{
		switch($name) {
			case "count": {
				return count($this -> _dataSource);
			}break;
		}
	}
	
	function __call($method, $arguments) 
	{
		switch($name) {
			case "count": {
				return count($this -> _dataSource);
			}break;
		}	
	}
}
?>