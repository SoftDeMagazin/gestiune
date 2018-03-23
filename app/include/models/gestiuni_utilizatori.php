<?php
class GestiuniUtilizatori extends Model{

	var $tbl="gestiuni_utilizatori";
	var $_relations = array(
		"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
		"utilizator" => array("type"=>"one", "model"=>"Utilizatori", "key"=>"utilizator_id", "value" => "nume"),
	);
	var $_defaultForm = array(
		"gestiune_utilizator_id" => array("type" => "hidden"),
		"gestiune" => array("label"=>"Gestiune", "attributes" => array("tabindex" => 1, "style" => "width:200px;")),
		"utilizator" => array("label"=>"Utilizator", "attributes" => array("tabindex" => 2, "style" => "width:200px;")),
	);

	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Utilizator");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> gestiune -> denumire .' - '. $this -> gestiune->punct_lucru->societate->denumire);
			$dg -> addColumn($this -> utilizator -> nume);
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