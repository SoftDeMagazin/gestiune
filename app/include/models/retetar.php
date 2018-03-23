<?php
class Retetar extends Model
{
	var $tbl="retetar";
	var $_relations = array(
			"componenta" => array(
				"type" => "one",
				"model" => "Produse",
				"key" => "componenta_id",
				"model_key" => "produs_id",
			),	
			"produs" => array(
				"type" => "one",
				"model" => "Produse",
				"key" => "produs_id",
				"model_key" => "produs_id",
			)
		);
	var $_defaultForm = array(
			"retetar_id" => array("type" => "hidden"),
			"produs_id" => array("type" => "hidden"),
			"componenta_id" => array("type" => "hidden"),
			"cantitate" => array("type" => "text", "label" => "Cantitate"),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("UM");
		$dg -> addHeadColumn("Tip");
		
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Pret");
	$dg -> addHeadColumn("Val.");
		$dg -> addHeadColumn("Edit");
		$dg -> addHeadColumn("Sterge");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$componenta = $this -> componenta;
			$dg -> addColumn(Html::onClickLink($componenta -> denumire, "xajax_gblInfoLoturi('". $this -> componenta_id ."', '". $_SESSION['user'] -> gestiune_id ."')"));
			$dg -> addColumn($componenta -> unitate_masura -> denumire);
			$dg -> addColumn($componenta -> tip -> descriere);
			
			$dg -> addColumn($this -> cantitate);
			$pret = douazecimale($componenta -> getPretMediuAchizitie($_SESSION['user'] -> gestiune_id));
			$dg -> addColumn($pret);
			$dg -> addColumn(treizecimale($pret*$this -> cantitate));
			$dg -> addColumn(iconEdit("xajax_frmComponenta('". $this -> id ."');return false;"), array("align" => "center"));
			$dg -> addColumn(iconRemove("xajax_stergeComponenta('". $this -> id ."');return false;"), array("align" => "center"));
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
	
	function listaPrintNomenclator()
	{
		$dg = new DataGrid(array("style" => "width:100%;margin:0px auto;" , "border" => "1", "id" => "tbl_". $this -> tbl ."", "class" => "", "cellspacing" => 0));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("UM");
		$dg -> addHeadColumn("Tip");
		$dg -> addHeadColumn("Cantitate");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$componenta = $this -> componenta;
			$dg -> addColumn($componenta -> denumire);
			$dg -> addColumn($componenta -> unitate_masura -> denumire);
			$dg -> addColumn($componenta -> tip -> descriere);
			$dg -> addColumn($this -> cantitate);
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$dg -> index();
			}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function listaReteteDenumiri() {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Reteta");
		$dg -> addHeadColumn("Cantitate");

		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$componenta = $this -> componenta;
			$dg -> addColumn($this -> produs -> denumire);
			$dg -> addColumn($this -> cantitate);
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver" => "$(this).addClass('rowhover')", 
			"onMouseOut" => "$(this).removeClass('rowhover')",
			"onClick" => "". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick" => "$dck",
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
		
}
?>