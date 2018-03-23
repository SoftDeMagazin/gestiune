<?php
class Delegati extends Model
{
	var $tbl="delegati";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"delegat_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"nume" => array("type"=>"text", "label"=>"Nume si Prenume", "attributes" => array( "style" => "width:200px;")),
		"cnp" => array("type"=>"text", "label"=>"Cnp", "attributes" => array( "style" => "width:200px;")),
		"act_identitate" => array("type"=>"text", "label"=>"Act Identitate", "attributes" => array( "style" => "width:200px;")),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Nume");
		$dg -> addHeadColumn("Cnp");
		$dg -> addHeadColumn("Act Identitate");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> nume);
			$dg -> addColumn($this -> cnp);
			$dg -> addColumn($this -> act_identitate);
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function select($selected=0, $onChange="") {
		$nr_r = count($this);
		$out = '<select  name="sel_delegat_id" id="sel_delegat_id" onChange="'. $onChange .'">';
		if($selected == 0 ) {
			$out .= '<option value="0" selected>Delegat nou</option>';
		}
		else {
			$out .= '<option value="0" >Delegat nou</option>';
		}	
		if($selected == -1) {
			$out .= '<option value="-1" selected>Fara delegat</option>';
		}
		else {
			$out .= '<option value="-1" >Fara delegat</option>';
		}	
		if($selected == -2) {
			$out .= '<option value="-2" selected>Curier</option>';
		}
		else {
			$out .= '<option value="-2" >Curier</option>';
		}	
		
		if($selected == -3) {
			$out .= '<option value="-3" selected>Transportator</option>';
		}
		else {
			$out .= '<option value="-3" >Transportator</option>';
		}	
		
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