<?php
class AvizeIesiri extends Model
{
	var $tbl="avize_iesiri";
	var $_relations = array(
		"lot" => array("type" => "one", "model" => "Loturi", "key" => "lot_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		"comp" => array("type" => "one", "model" => "AvizeContinut", "key" => "comp_id", "model_key" => "continut_id"),
		);
	var $_defaultForm = array(
		);
	
	function listaPrint()
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "1", "id" => "tbl_". $this -> tbl ."", "cellpadding" => "0", "cellspacing" => "0"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			$dg -> addHeadColumn("Pret ach");
			$dg -> addHeadColumn("Valoare ach");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire);
				$dg -> addColumn(treizecimale($this -> cantitate));
				$pret_intrare = $this -> lot -> pret_intrare_ron;
				$dg -> addColumn(treizecimale($pret_intrare), array("style"=>"text-align:right"));
				$val = douazecimale($this -> cantitate * $pret_intrare);
				$dg -> addColumn($val, array("style"=>"text-align:right"));
				$total += $val;
				$dg -> index();
				}
				$dg -> addColumn("Total", array("colspan" => "4"));
				$dg -> addColumn(douazecimale($total), array("style"=>"text-align:right"));
			$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}	
	
	function evidentaIesiri($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Aviz");
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
			if($transfer_id != $comp -> aviz_id) {
				$aviz = $this -> comp -> aviz;
				$transfer_id = $comp -> aviz_id;
			}	
			$dg -> addColumn($aviz -> numar_doc);
			$dg -> addColumn(c_data($aviz -> data_doc));
			if($aviz -> tip_aviz == 'doc_pv')
				$dg -> addColumn($aviz -> tert -> denumire);
			$dg -> addColumn($this -> cantitate);
			if($aviz -> tip_aviz == 'doc_pv')
				$dg -> addColumn($comp -> pret_vanzare_ron);
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