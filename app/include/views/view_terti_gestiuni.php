<?php
class ViewTertiGestiuni extends Terti
{
	var $tbl="view_terti_gestiuni";
	var $key="tert_id";
	var $_relations = array(
		"delegati" => array("type" => "many", "model" => "Delegati", "key" => "tert_id"),
		"categorie_tert" => array("type" => "one", "model" => "CategoriiTerti", "key" => "categorie_tert_id", "value" => "denumire"),	
		);
	var $_defaultForm = array(
		"tert_id" => array("type" => "hidden"),
		"gest" => '<div id="div_frm_gest">Gestiune</div>',
		"denumire" => array("type"=>"text", "label"=>"Denumire", "attributes" => array( "style" => "width:400px;")),
		"tip" => array("type"=>"select", "options" => "SELECT `tip_tert`, `descriere` FROM `tipuri_terti`","label" => "Tip"),
		"valuta" => array("type"=>"select", "value" => "LEI", "options" => "SELECT `descriere`, `descriere` FROM `valute`","label" => "Valuta"),
		"reg_com" => array("type"=>"text", "label"=>"Reg com", "attributes" => array( "style" => "width:400px;")),
		"cod_fiscal" => array("type"=>"text", "label"=>"Cod fiscal", "attributes" => array( "style" => "width:400px;")),
		"cod_tara" => array("type" => "select", "label"=>"Tara", "value" => "RO", "options" => "SELECT cod, concat(cod, ' - ', denumire) from tari", "default" => "Selectati Tara", "default_value" => "0", "attributes" => array( "style" => "width:400px;")),
		"sediul" => array("type"=>"textarea", "label"=>"Sediul", "attributes" => array( "style" => "width:400px;")),
		"judet" => array("type"=>"text", "label"=>"Judet", "attributes" => array( "style" => "width:400px;")),
		"iban" => array("type"=>"text", "label"=>"Iban", "attributes" => array("style" => "width:400px;", "class" => "iban")),
		"banca" => array("type"=>"text", "label"=>"Banca", "attributes" => array( "style" => "width:400px;")),
		"telefon" => array("type"=>"text", "label"=>"Telefon", "attributes" => array("style" => "width:400px;")),
		"email" => array("type"=>"text", "label"=>"Email", "attributes" => array("style" => "width:400px;")),
		"limita_credit_intern" => array("type"=>"text", "label"=>"Limita Credit Intern", "attributes" => array("style" => "width:400px;")),
		"limita_credit_asigurat" => array("type"=>"text", "label"=>"Limita Credit Asigurat", "attributes" => array("style" => "width:400px;")),
		"categorie_tert" => array("label" => "Categorie"),
		"scadenta_default" => array("type"=>"text", "label" => "Scadenta Plata Zile"),
		
		);
		
	var $_validator = array(
		"cod_tara" => array(array("required", "Trebuie sa selectati o tara"))
	);	
		function getByIds($tert_id, $gestiune_id) {
			$this -> fromString("WHERE `tert_id` = '". $tert_id ."' and `gestiune_id` = '". $gestiune_id ."'");
		}
}		
?>