<?php
class ContinutIntrastat extends Model
{
	var $tbl="continut_intrastat";
	var $_relations = array(
		);
	var $_defaultForm = array(
		"continut_id" => array("type" => "hidden")
		);
		
	function genereazaContinut($factura_id) {
		$sql = "
			select produse.nc8, factura_intrare_id, sum(val_ach_ron) as val_facturata_ron, sum(val_ach_val) as val_facturata_val, sum(masa_neta) as masa_neta from 
		    facturi_intrari_continut 
			inner join produse using(produs_id)
			where factura_intrare_id = '". $factura_id ."'
			group by produse.nc8, factura_intrare_id
			;
			";
		$rows = $this -> db -> getRows($sql);
		$tara_tert = $factura -> tert -> cod_tara;
		foreach($rows as $row) {
			$cont = new ContinutIntrastat($row);
			$cont -> tara_origine = $tara_tert;
			$cont -> tara_expediere = $tara_tert;
			$cont -> save();
		}	
		
		$this -> fromString("where `factura_intrare_id` = '$factura_id'");
	}	
	
	function listaEditare() {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("COD NC8");
		$dg -> addHeadColumn("Masa Neta");
		$dg -> addHeadColumn("Val facturata LEI");
		$dg -> addHeadColumn("Cota transp. LEI");
		$dg -> addHeadColumn("Val statistica LEI");
		$dg -> addHeadColumn("Cond. livrare");
		$dg -> addHeadColumn("Tara origine");
		$dg -> addHeadColumn("Tara expediere");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		$factura = new FacturiIntrari($this -> factura_intrare_id);
		$tari = new Tari("where 1 order by cod asc");
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			
			$total_masa_neta += $this -> masa_neta;
			$total_val_facturata += $this -> val_facturata_ron;
			$total_val_transport += $this -> val_transport_ron;
			$total_val_statistica += $this -> val_statistica_ron;
			
			$dg -> addColumn($this -> nc8);
			$dg -> addColumn('<input type="text" size="5" id="masa_neta_'. $this -> id .'" name="masa_neta[]" value="'. $this -> masa_neta .'" />
			<input type="hidden" id="continut_id_'. $this -> id .'" name="continut_id[]" value="'. $this -> id .'" />
			');
			//$dg -> addColumn('<input type="text" name="val_facturata_ron[]" value="'. $this -> val_facturata_ron .'" />');
			$dg -> addColumn(''. $this -> val_facturata_ron .'');
			$dg -> addColumn('<input type="text" id="val_transport_ron_'. $this -> id .'" name="val_transport_ron[]" value="'. $this -> val_transport_ron .'" />');
			$dg -> addColumn('<input type="text" id="val_statistica_ron_'. $this -> id .'" name="val_statistica_ron[]" value="'. $this -> val_statistica_ron .'" />');
			$dg -> addColumn($factura -> incoterm);
			$dg -> addColumn($tari -> select("tara_origine[]", $this -> tara_origine));
			$dg -> addColumn($tari -> select("tara_expediere[]", $this -> tara_expediere));
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
		$dg -> addColumn("<strong>Total:</strong>");
		$dg -> addColumn("".douazecimale($total_masa_neta)."", array("style" => "color:red"));
		$dg -> addColumn("".douazecimale($total_val_facturata)."", array("style" => "color:red"));
		$dg -> addColumn("".douazecimale($total_val_transport)."", array("style" => "color:red"));
		$dg -> addColumn("".douazecimale($total_val_statistica)."", array("style" => "color:red"));
		$dg -> addColumn("&nbsp;");
		$dg -> addColumn("&nbsp;");
		$dg -> addColumn("&nbsp;");
		$dg -> index();	
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}	
	
	
	function totalMasaNeta() {
		$sql = "SELECT sum(masa_neta) as total FROM continut_intrastat WHERE factura_intrare_id = '". $this -> factura_intrare_id ."'";
		$row = $this -> db -> getRow($sql);
		return $row['total'];
	}
	
	function calculeazaCoteTransport($cota_transport_total) {
		$totalMasaNeta = $this -> totalMasaNeta();
		$unitar = $cota_transport_total / $totalMasaNeta; 
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$this -> val_transport_ron = douazecimale($this -> masa_neta * $unitar);
			$this -> val_statistica_ron = douazecimale($this -> val_facturata_ron - $this -> val_transport_ron);
			$this -> save();	
		}
		return true;	
	}	
}
?>