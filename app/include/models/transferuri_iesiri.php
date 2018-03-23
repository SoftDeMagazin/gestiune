<?php
class TransferuriIesiri extends Model
{
	var $tbl="transferuri_iesiri";
	var $_relations = array(
		"lot" => array("type" => "one", "model" => "Loturi", "key" => "lot_id"),
		"comp" => array("type" => "one", "model" => "TransferuriContinut", "key" => "comp_id", "model_key" => "continut_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		);
	var $_defaultForm = array(
		);
	
	function listaPrint()
	{
	$dg = new DataGrid(array("style" => "width:100%;margin:10px auto;" , "border" => "1", "id" => "tbl_". $this -> tbl ."", "cellpadding" => "2", "cellspacing" => "0"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs", array("width" => "50%"));
			$dg -> addHeadColumn("UM", array("width" => "10%"));
			$dg -> addHeadColumn("Cant", array("width" => "10%"));
			$dg -> addHeadColumn("Pret ach", array("width" => "15%"));
			$dg -> addHeadColumn("Valoare ach", array("width" => "15%"));
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire, array("width" => "50%"));
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire, array("width" => "10%", "style" => "text-align:center"));
				$dg -> addColumn(treizecimale($this -> cantitate), array("width" => "10%", "style" => "text-align:right"));
				$pret_intrare = $this -> lot -> pret_intrare_ron;
				$dg -> addColumn(treizecimale($pret_intrare), array("width" => "15%", "style" => "text-align:right"));
				$val = douazecimale($this -> cantitate * $pret_intrare);
				$dg -> addColumn($val, array("width" => "15%", "style" => "text-align:right"));
				$total += $val;
				$dg -> index();
				}
				$dg -> addColumn("Total", array("colspan" => "4", "width" => "85%"));
				$dg -> addColumn(douazecimale($total), array("width" => "15%", "style" => "text-align:right"));
					}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function evidentaIesiri($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Transfer");
		$dg -> addHeadColumn("Data Fatura");
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Pret iesire LEI");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		$transfer_id = 0;
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$comp = $this -> comp;
			if($transfer_id != $comp -> transfer_id) {
				$transfer = $this -> comp -> transfer;
				$transfer_id = $comp -> transfer_id;
			}	
			$dg -> addColumn($transfer -> numar_doc);
			$dg -> addColumn(c_data($transfer -> data_doc));
			$dg -> addColumn($transfer -> gestiune_destinatie -> denumire);
			$dg -> addColumn($this -> cantitate);
			$dg -> addColumn($this -> lot -> pret_intrare_ron);
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