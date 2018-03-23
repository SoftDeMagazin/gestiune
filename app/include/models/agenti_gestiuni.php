<?php
class AgentiGestiuni extends Model
{
	var $tbl="agenti_gestiuni";
	var $_relations = array(
			"agent" => array("type" => "one", "model" => "Agenti", "key" => "agent_id"),
		);
	var $_defaultForm = array(
		);	
		
	function select($selected=0) {
		$nr_r = count($this);
		$out = '<select  name="agent_id" id="agent_id">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> agent_id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> agent -> id .'" '. $sel .'>'. $this -> agent -> nume .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
}
?>