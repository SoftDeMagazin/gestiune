<?php
class CoteTva extends Model
{
	var $tbl="cote_tva";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"cota_tva_id" => array("type" => "hidden"),
		"cod_tva" => array("type"=>"text", "label"=>"Cod Casa Fiscala", "attributes" => array("tabindex" => 1, "style" => "width:400px;")),
		"valoare" => array("type"=>"text", "label"=>"Valoare", "attributes" => array("tabindex" => 2, "style" => "width:400px;")),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Cod Casa Fiscala");
		$dg -> addHeadColumn("Valoare");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> cod_tva);
			$dg -> addColumn($this -> valoare);
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
	
	function getTvaZero() {
		$this -> fromString("where `valoare` = 0");
	}
}
?>