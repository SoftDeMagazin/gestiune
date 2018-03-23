<?php
class RegistruCasa extends Model
{
	var $tbl="registru_casa";
	var $_relations = array(
		"mod_plata" => array("type" => "one", "model" => "ModalitatiPlata", "key" => "mod_plata_id", "value" => "denumire")
		);
	var $_defaultForm = array(
		"registru_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"societate_id" => array("type" => "hidden"),
		"mod_plata" => array("label" => "Mod Plata"), 
		"data_doc" => array("type"=>"text", "label"=>"Data Document", "attributes" => array( "style" => "width:400px;", "class" => "calendar")),
		"numar_doc" => array("type"=>"text", "label"=>"Numar Document", "attributes" => array( "style" => "width:400px;")),
		"suma" => array("type"=>"text", "label"=>"Suma", "attributes" => array( "style" => "width:400px;")),
		"tip_operatie" => array("type" => "select", "options" => array("incasare" => "Incasare", "plata" => "Plata"))
		);
}
?>