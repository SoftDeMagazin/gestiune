<?php
class TipuriGestiuni extends Model
{
	var $tbl="tipuri_gestiuni";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
	
	function select($onChange="") 
	{
		$nr_r = count($this);
		$out = '<select multiple size="1" name="tip_gestiune[]" id="tip_gestiune" style="" onChange="'. $onChange .'">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> tip .'">'. $this -> descriere .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
}
?>