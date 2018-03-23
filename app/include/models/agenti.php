<?php
class Agenti extends Model
{
	var $tbl="agenti";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"agent_id" => array("type" => "hidden"),
		"gest" => '<div id="div_frm_gest">Gestiune</div>',
		"nume" => array("type" => "text", "label" => "Nume Agent"),
		);
		
		
	
		
	function getTertiAsociati($gestiune_id) {
		$ats = new AgentiTerti("where agent_id = '". $this -> id ."' and gestiune_id = '". $gestiune_id ."'");
		$out = array();
		foreach($ats as $at) {
			$out[] = $at -> tert_id;
		}
		return $out;
	}	
		
	
	/**
	 * disociaza gestiunile care nu sunt in array
	 * @param array $gestiuni vector id-uri gestiuni array($id1, $id2...)
	 */
	 
	 function disociazaGestiuni($gestiuni=array()) {
	 	$gestiuni_asociate = $this -> getGestiuniAsociate();
	 	foreach($gestiuni_asociate as $gest_id) {
			if(!in_array($gest_id, $gestiuni)) {
				$cg = new AgentiGestiuni(" where `gestiune_id` = '$gest_id' and tert_id = '". $this -> id ."'");
				$cg -> delete();
			}
		}
	 } 
	
	/**
	 *  asociaza categorie cu gestiunile din array ... 
	 *	@param array $gestiuni vector id-uri gestiuni($id1, $id2, ...)
	 */  
	function asociazaCuGestiuni($gestiuni=array()) {
		$gestiuni_asociate = $this -> getGestiuniAsociate();
		foreach($gestiuni as $gest_id) {
			if(!in_array($gest_id, $gestiuni_asociate)) {
				$cg = new AgentiGestiuni();
				$cg -> gestiune_id = $gest_id;
				$cg -> agent_id = $this -> id;
				$cg -> save();
			}
		}
	}
		
	function getGestiuniAsociate() {
		$rows = $this -> db -> getRowsNum("select gestiune_id from agenti_gestiuni where agent_id = '". $this -> id ."'");
		$out = array();
		foreach($rows as $row) {
			$out[] = $row[0];
		}
		return $out;
	}
	
	function select($selected=0) {
		$nr_r = count($this);
		$out = '<select  name="agent_id" id="agent_id">';
		$out .= '<option value="0" >Select Agent</option>';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> nume .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
}
?>