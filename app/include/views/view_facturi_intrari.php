<?php
class ViewFacturiIntrari extends Model
{
	var $tbl="view_facturi_intrari";
	var $key="factura_intrare_id";
	var $_relations = array(
			"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
			"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
			"continut" => array("type"=>"many", "model"=>"FacturiIntrariContinut", "key"=>"factura_intrare_id"),
			"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare"),
			"plati" => array("type"=>"many", "model"=>"Plati", "key"=>"factura_intrare_id"),
		);
	var $_defaultForm = array(
		);
	function listaPlati($click="", $dblClick="" , $selected=0)
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
				switch($this -> tert -> tip) {
					case "intern": {
						$dg -> addColumn(money($this -> totalFactura(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalPlati(), $this -> tert -> valuta), array("style" => "color:green"));
						$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));			

					}break;
					case "extern_ue": {
						$dg -> addColumn(money($this -> totalFacturaValuta(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalPlati(), $this -> tert -> valuta), array("style" => "color:green"));
						$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));			
					}break;
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
	
	function listaSituatieActuala($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
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
				$dg -> addColumn(money($this -> totalFactura(), $this -> tert -> valuta), array("style" => "color:blue;"));
				$dg -> addColumn(money($this -> totalPlati(), $this -> tert -> valuta), array("style" => "color:green"));
				$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));	
						
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
	
	
	function totalFaraTva() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ach_ron`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalTva() {
		$totalFactura = $this -> totalFaraTva();
		$totalTva = ($totalFactura * $this -> cota_tva -> valoare) / 100;
		return douazecimale($totalTva);
	}
	
	function totalFactura() {
		return $this -> total_ron_cu_tva;
	}
	
	
	function totalFaraTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ach_val`) as total 
		FROM `facturi_intrari_continut` 
		WHERE `factura_intrare_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalTvaValuta() {
		$totalFactura = $this -> totalFaraTvaValuta();
		$totalTva = ($totalFactura * $this -> cota_tva -> valoare) / 100;
		return douazecimale($totalTva);
	}
	
	function totalFacturaValuta() {
		return $this -> total_val_cu_tva;
	}
	
	function totalPlati() {
		return douazecimale($this -> platit);
	}
		
	function sold() {
		if($this -> tert -> tip == "intern") {
			$totalFactura = $this -> totalFactura();
		}
		else {
			$totalFactura = $this -> totalFacturaValuta();
		}
		$totalPlati = $this -> totalPlati();
		$sold = douazecimale($totalFactura - $totalPlati);
		return $sold;
	}
	
	function areSold() {
		if($this -> sold() != 0) return true;
		return false;
	}
}
?>