<?php
class Tari extends Model
{
	var $tbl="tari";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
		
	function select($nume="",$selected="") {
		$nr_r = count($this);
		$out = '<select name="'. $nume .'" id="'. $nume .'">';
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			if($this -> cod == $selected) $sel = "selected";
			else $sel = "";
			$out .= '<option value="'. $this -> cod .'" '. $sel .'>'. $this -> cod .' - '. substr($this -> denumire, 0, 12) .'</option>';
		}
		$out .= '</select>';
		return $out;	
	}	
		
}
?>