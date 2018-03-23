<?php
class Iesiri {

	var $modelName;
	var $model;
	function __construct($data, $model) {
		$this -> modelName = $model;
		$this -> model = new $model();
		
		$this -> model -> fromArray($data);
	}
	
	function save() {
		$this -> model -> save();
	}
}
?>