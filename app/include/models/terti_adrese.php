<?php
class TertiAdrese extends Model
{
	var $tbl="terti_adrese";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"tert_adresa_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"adresa" => array("type"=>"textarea", "label"=>"Adresa", "attributes" => array( "style" => "width:400px;", "rows" => "2")),
		"cod_tara" => array("type" => "select", "label"=>"Tara", "value" => "RO", "options" => "SELECT cod, concat(cod, ' - ', denumire) from tari", "attributes" => array( "style" => "width:400px;")),
	);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Adresa");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> adresa);
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
		$out = '<select  name="adresa_id" id="adresa_id" onChange="'. $onChange .'">';
		if($selected == 0 ) {
			$out .= '<option value="0" selected>Selectati</option>';
		}
		else {
			$out .= '<option value="0" >Selectati</option>';
		}	
		
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> adresa .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
		
}
?>