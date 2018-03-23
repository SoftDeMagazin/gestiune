<?php
class Delegati extends Model
{
	var $tbl="delegati";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"delegat_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"nume" => array("type"=>"text", "label"=>"Denumire", "attributes" => array( "style" => "width:400px;")),
		"cnp" => array("type"=>"text", "label"=>"Reg com", "attributes" => array( "style" => "width:400px;")),
		);
		
}
?>