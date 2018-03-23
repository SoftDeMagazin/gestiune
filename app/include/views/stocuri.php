<?php
class Stocuri extends Model
{
	var $tbl="stocuri";
	var $key="produs_id";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);	
		
	function getStoc($produs_id, $gestiune_id) {
		$this -> fromString("where produs_id = '$produs_id' and gestiune_id = '$gestiune_id'");
	}	
}		
?>