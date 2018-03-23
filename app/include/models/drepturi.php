<?php
class Drepturi extends Model{
	var $tbl="drepturi";

	function select($onChange="")
	{
		$nr_r = count($this);
		$out = '<select name="drept_id" id="drept_id" style="width:130px" onChange="'. $onChange .'">';
		$out .= '<option value="0">Selectare</option>';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
//	function multiselect()
//	{
//		$nr_r = count($this);
//		$out = '<select id="drept_id_multiple" name="drept_id_multiple"  multiple="multiple" title="Drepturi">';
//		for ($i = 0; $i < $nr_r; $i++) {
//			$this->fromDataSource($i);
//			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';
//		}
//		$out .= '</select>';
//		return $out;
//	}
}

?>