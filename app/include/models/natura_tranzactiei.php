<?php
class NaturaTranzactiei extends Model
{
	var $tbl="natura_tranzactie";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
		
	function getByCod($cod) {
		$this -> fromString("where cod = '$cod'");
	}	
		
	function select_copil($selected=0) {
		$nr_r = count($this);
		$out = '<select  name="natura_tranzactie_b" id="natura_tranzactie_b">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> cod .'" '. $sel .'>'. $this -> descriere .'</option>';			
		}
		if(!$nr_r) {
			$out .= '<option value="0" >Nu se aplica</option>';	
		}
		$out .= '</select>';
		return $out;
	}	
}
?>