<?php
class ModalitatiPlata extends Model
{
	var $tbl="modalitati_plata";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);

	function select($selected="")
	{
		$nr_r = count($this);
		$out = '<select  name="mod_plata_id" id="mod_plata_id">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> descriere .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}

	function selectMulti($selected="")
	{
		$nr_r = count($this);
		$out = '<select multiple  name="mod_plata_id[]" id="mod_plata_id">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> descriere .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}

}
?>