<?php
class ProduseGestiuni extends Model
{
	var $tbl="produse_gestiuni";
	var $_relations = array(
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		);
	var $_defaultForm = array(
		"produs_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"produs_gestiune_id" => array("type" => "hidden"),
		"pret_ron" => array("type"=>"text", "label"=>"Pret vanzare LEI", "attributes" => array()),
		"pret_val" => array("type"=>"text", "label"=>"Pret vanzare EUR", "attributes" => array()),
		);
	
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Pret Vanzare EUR");
		$dg -> addHeadColumn("Pret Vanzare LEI");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> gestiune -> denumire);
			$dg -> addColumn(douazecimale($this -> pret_val));
			$dg -> addColumn(douazecimale($this -> pret_ron));

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
}
?>