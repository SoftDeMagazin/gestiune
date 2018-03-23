<?php 
class Module extends Model{
	var $tbl="module";

	function select($onChange="")
	{
		$nr_r = count($this);
		$out = '<select name="modul_id" id="modul_id" style="width:130px" onChange="'. $onChange .'">';
		$out .= '<option value="0">Selectare</option>';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
	function selectSize($onChange="", $size=20)
	{
		$nr_r = count($this);
		$out = '<select size="'. $size .'" name="modul_id" id="modul_id" style="width:245px" onChange="'. $onChange .'">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .'</option>';
		}
		$out .= '</select>';
		return $out;
	}

}
?>