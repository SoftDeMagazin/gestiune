<?php
abstract class Rpt {
	var $data;
	var $filtre;
	
	function loadData($sql) {
		global $db;
		$this -> data = $db -> getRows($sql); 
	}
	
	abstract function getHtml();
	
}
?>