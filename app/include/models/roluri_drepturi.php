<?php
class RoluriDrepturi extends Model{

	var $tbl="roluri_drepturi";
	var $_relations = array(
		"rol" => array("type"=>"one", "model"=>"Roluri", "key"=>"rol_id", "value" => "rol"),
		"modul" => array("type"=>"one", "model"=>"Module", "key"=>"modul_id", "value" => "denumire"),
		"drept" => array("type"=>"one", "model"=>"Drepturi", "key"=>"drept_id", "value" => "denumire"),
	);
	var $_defaultForm = array(
		"rol_drept_id" => array("type" => "hidden"),
		"rol" => array("label"=>"Rol", "attributes" => array("tabindex" => 1, "style" => "width:200px;")),
		"modul" => array("label"=>"Modul", "attributes" => array("tabindex" => 2, "style" => "width:200px;")),
		"drept" => array("label"=>"Drepturi", "attributes" => array("tabindex" => 3, "style" => "width:200px;")),
	);

	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Rol");
		$dg -> addHeadColumn("Modul");
		$dg -> addHeadColumn("Drept");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> rol -> rol);
			$dg -> addColumn($this -> modul -> denumire);
			$dg -> addColumn($this -> drept -> denumire);
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