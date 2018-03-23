<?php
class TransformariPf extends Model
{
	var $tbl="transformari_pf";
	var $_relations = array(
		"transformare" => array("type" => "one", "model" => "Transformari", "key" => "transformare_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		"mps" => array("type" => "many", "model" => "TransformariMp", "key" => "trans_pf_id"),
		);
	var $_defaultForm = array(
		);
	
	function getValoareMateriale() {
		$mps = $this -> mps;
		if(!count($mps)) {
			return 0;
		}
		$val_materiale = 0;
		foreach($mps as $mp) {
			$val_materiale += $mp -> getValoare();
		}
		return $val_materiale;
	}	
		
}
?>