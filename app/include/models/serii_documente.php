<?php
class SeriiDocumente extends Model
{
	var $tbl="serii_documente";
	var $_relations = array(
		"serie" => array("type" => "one", "model" => "SeriiNumerice", "key" => "serie_id")
		);
	var $_defaultForm = array(
		"serii_facturi_id" => array("type" => "hidden"),
		);	
		
	function getByGestiuneAndTip($gestiune_id, $tip) {
		$this -> fromString("where gestiune_id='$gestiune_id' and tip_doc='$tip'");
	}
}
?>