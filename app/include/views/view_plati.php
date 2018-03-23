<?php
class ViewPlati extends Model
{
	var $tbl="view_plati";
	var $key="plata_id";
	var $_relations = array(
			"mod_plata" => array("type" => "one", "model" => "ModalitatiPlata", "key" => "mod_plata_id", "value" => "descriere"),
			"tert" => array("type" => "one", "model" => "Terti", "key" => "tert_id")
		);
	var $_defaultForm = array(
		);

	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Tip Doc");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Explicatie");
		$dg -> addHeadColumn("Suma");
		$dg -> addHeadColumn("Asociat");
		$dg -> addHeadColumn("Ramas");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn($this -> mod_plata -> descriere);	
			$dg -> addColumn(c_data($this -> data_doc));	
			$dg -> addColumn($this -> explicatie);
			$dg -> addColumn(money($this -> suma, $this -> tert -> valuta));
			$dg -> addColumn(money($this -> sumaAsociata(), $this -> tert -> valuta));
			$dg -> addColumn(money($this -> sumaNeasociata(), $this -> tert -> valuta));
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
	
	function sumaAsociata() {
		return $this -> asociat;
	}
	
	function sumaNeasociata() {
		return $this -> suma - $this -> asociat;
	}
		
}
?>