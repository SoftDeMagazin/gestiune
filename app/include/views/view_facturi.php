<?php
class ViewFacturi extends Model
{
	var $tbl="view_facturi";
	var $key="factura_id";
	var $_relations = array(
			"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
			"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
			"continut" => array("type"=>"many", "model"=>"FacturiContinut", "key"=>"factura_id"),
			"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare"),
		);
	var $_defaultForm = array(
		);
	function listaIncasari($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Total");
		$dg -> addHeadColumn("Platit");
		$dg -> addHeadColumn("Sold");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				if($this -> totalFacturaCuTva() > 0) {
					$dg -> addColumn(money($this -> totalFacturaCuTva(), $this -> tert -> valuta), array("style" => "color:blue;"));
					$dg -> addColumn(money($this -> totalIncasari(), $this -> tert -> valuta), array("style" => "color:green"));
					$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));	
				}
				else {
					$dg -> addColumn(money($this -> totalFacturaCuTva(), $this -> tert -> valuta), array("style" => "color:blue;"));
					$dg -> addColumn(money($this -> totalIncasari(), $this -> tert -> valuta), array("style" => "color:green"));
					$dg -> addColumn(money(0, $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));	
				}
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
	
	function listaSituatieActuala()
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;", "cellpadding" => 1, "cellspacing" => 0 , "border" => "1", "id" => "tbl_". $this -> tbl .""));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Data Scadenta");
		$dg -> addHeadColumn("Valoare");
		$dg -> addHeadColumn("Achitat");
		$dg -> addHeadColumn("Sold");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				$dg -> addColumn(c_data($this -> data_factura));
				$dg -> addColumn(c_data($this -> data_scadenta));			
				$dg -> addColumn(money($this -> totalFacturaCuTva(), $this -> tert -> valuta), array("style" => "color:blue;text-align:right;"));
				$dg -> addColumn(money($this -> totalIncasari(), $this -> tert -> valuta), array("style" => "color:green;text-align:right;"));
				$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;text-align:right;"));	
				if($this -> id == $selected) $class="rowclick";
				else $class="";
				$ck = $this -> stringReplace($click);
				$dck = $this -> stringReplace($dblClick);
				$dg -> setRowOptions(array(
				));
				$dg -> index();
			}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	
	function listaSituatieGlobala()
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;", "cellpadding" => 1, "cellspacing" => 0 , "border" => "1", "id" => "tbl_". $this -> tbl .""));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Valoare");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				$dg -> addColumn(c_data($this -> data_factura));
				$dg -> addColumn(money($this -> totalFacturaCuTva(), $this -> tert -> valuta), array("style" => "color:blue;text-align:right;"));
				if($this -> id == $selected) $class="rowclick";
				else $class="";
				$ck = $this -> stringReplace($click);
				$dck = $this -> stringReplace($dblClick);
				$dg -> setRowOptions(array(
				));
				$dg -> index();
			}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function totalFacturaCuTva() {
		switch($this -> tert -> tip) {
			case "intern": {
				return $this -> total_ron_cu_tva;
			}break;
			case "extern_ue": {
				return $this -> total_val_cu_tva;
			}break;
		}
	}
	
	function totalIncasari() {
		return $this -> platit;
	}
	
	function sold() {
		if($this -> tert -> tip == "intern") {
			$totalFactura = $this -> total_ron_cu_tva;
		}
		else {
			$totalFactura = $this -> total_val_cu_tva;
		}
		$totalIncasari = $this -> totalIncasari();
		$sold = $totalFactura - $totalIncasari;
		return $sold;
	}
	
	function getTotalSold() {
		$nr_r = count($this);
		$sold = 0;
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$sold += $this -> sold();
		}
		return $sold;
	}
	
	function areSold() {
		if($this -> sold() != 0) return true;
		return false;
	}
}
?>