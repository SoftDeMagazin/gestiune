<?php
class TipuriProduse extends Model
{
	var $tbl="tipuri_produse";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
	
	function select($onChange="") 
	{
		$nr_r = count($this);
		$out = '<select multiple size="1" name="tip_produs[]" id="tip_produs" style="" onChange="'. $onChange .'">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> tip .'">'. $this -> descriere .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
}
?>