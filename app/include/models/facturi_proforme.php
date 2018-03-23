<?php
class FacturiProforme extends Model
{
	var $tbl="facturi_proforme";
	var $_relations = array(
		"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
		"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
		"societate" => array("type"=>"one", "model" =>"Societati", "key"=>"societate_id", "value"=> "denumire"),
 		"continut" => array("type"=>"many", "model"=>"FacturiProformeContinut", "key"=>"factura_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => "where 1 order by `cod_tva` asc"),
		"incasari_asociate" => array("type" => "many", "model"=>"IncasariAsocieri", "key"=>"factura_id"),
		);
	var $_defaultForm = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		
		"cota_tva" => array("label" => "Cota Tva"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"div_agent" => '<div id="div_frm_agent" >Agent</br></div>',
		"info_doc_end" => array("type" => "fieldend"),
		);
		
	var $_frmFacturaInterna = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Proforma", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva" => array("label" => "Cota Tva"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"info_doc_end" => array("type" => "fieldend"),
	);	
	
	var $_frmFacturaExterna = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva_id" => array("type" => "hidden"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"info_doc_end" => array("type" => "fieldend"),
	);	
		
	var $_validator = array(
		"numar_doc" => array(array("required", "Introduceti numar factura")),
		"data_factura" => array(array("required", "Introduceti data facturii")),
	);	
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Client");
		$dg -> addHeadColumn("Numar");
		$dg -> addHeadColumn("Data");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> tert -> denumire);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn(c_data($this -> data_factura));
			
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
				switch($this -> tert -> tip) {
					case "intern": {
						$dg -> addColumn(money($this -> totalFactura(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalIncasari(), $this -> tert -> valuta), array("style" => "color:green"));
						$dg -> addColumn(money($this -> sold(), $this -> tert -> valuta), array("style" => "color:red;font-weight:bold;"));	
					}break;
					case "extern_ue": {
						$dg -> addColumn(money($this -> totalFacturaValuta(), $this -> tert -> valuta), array("style" => "color:blue;"));
						$dg -> addColumn(money($this -> totalIncasari(), $this -> tert -> valuta), array("style" => "color:green"));
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
	
	function listaFacturiIncasari($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Total");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				$dg -> addColumn(c_data($this -> data_factura));
				if($this -> tert -> tip == "intern") {
					$dg -> addColumn($this -> totalFactura(), array("style" => "color:blue;"));
				}
				else {
					$dg -> addColumn($this -> totalFacturaValuta(), array("style" => "color:blue;"));
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
	
	
	function listaIncasariAsociate($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Tip Doc");
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Suma");
		$dg -> setHeadAttributes(array());
		$incasari = $this -> incasari_asociate;
		$nr_r = count($incasari);
		for($i=0;$i<$nr_r;$i++)
			{
				$incasari -> fromDataSource($i);
				$dg -> addColumn($incasari -> incasare -> mod_plata -> descriere);
				$dg -> addColumn($incasari -> incasare -> numar_doc);
				$dg -> addColumn(c_data($incasari -> incasare -> data_doc));
				$dg -> addColumn($incasari -> suma);
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
	
	function frmFactura($tert_id=0) {
		if(!$this -> id) {
			$tert = new Terti($tert_id);
		}
		else {
			$tert = $this -> tert;
		}

		switch($tert -> tip) {
			case "intern": {
				return $this -> frmInnerHtml($this -> frmContent($this -> _frmFacturaInterna));
			}break;
			case "extern_ue": {
				return $this -> frmInnerHtml($this -> frmContent($this -> _frmFacturaExterna));
			}break;		
				
		}
	}
	
	function anuleaza() {
		$serie = $this -> getSerie($this -> gestiune_id);
		if(count($serie)) {
			$serie -> decrement();
		}
		$this -> db -> query("delete from facturi_proforme_continut where factura_id = '". $this -> id ."'");
		$this -> delete();
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Sumar Proforma: '. $this -> numar_doc .'</legend>
		<strong>Client:</strong> '. $this -> tert -> denumire .'<br />
		<strong>Gestiune:</strong> '. $this -> gestiune -> denumire .'<br />
		<strong>Data:</strong> '. c_data($this -> data_factura) .'<br />
		<strong>Total Fara TVA:</strong> '. $this -> totalFaraTva() .' <strong>Total TVA:</strong> '. $this -> totalTva() .'<br />
		<strong>Total Proforma:</strong> '. $this -> totalFactura() .'<br />
		</fieldset>
		';
		return $out;
	}
	
	function totalFaraTva() {
		$sql = "
		SELECT sum(`cantitate`*`pret_vanzare_ron`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalTva() {
		$sql = "
		SELECT sum(`val_tva_ron`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalFactura() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ron_cu_tva`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalFaraTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_vanzare_val`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function totalTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`val_tva_val`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function totalFacturaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_val_cu_tva`) as total 
		FROM `facturi_proforme_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function sterge() {
		$this -> db -> query("delete from facturi_continut where factura_id = '". $this -> id ."'");
		$this -> delete();
	}
	
	function getNumar($gestiune_id=0) {
		if(!$gestiune_id) $gestiune_id = $_SESSION['user'] -> gestiune_id;
		$gestiune = new Gestiuni($gestiune_id);
		$serie = $this -> getSerie($gestiune_id);  
		return $serie -> curent+1;
	}
	
	function getSerie($gestiune_id=0) {
		if(!$gestiune_id) $gestiune_id = $_SESSION['user'] -> gestiune_id;
		$s_fact = new SeriiDocumente("where gestiune_id = '$gestiune_id' and tip_doc = 'facturi_proforme'");
		if(count($s_fact))
		return $s_fact -> serie;
	}
	
	function incrementSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$serie -> increment();
	}  
	
	function infoIncasari() {
		$totalFactura = $this -> totalFactura();
		$totalIncasari = $this -> totalIncasari();
		$sold = douazecimale($this -> sold());
		$out = '
		<fieldset>
		<legend>Sumar factura: '. $this -> numar_doc .'</legend>
		<strong>Client:</strong> '. $this -> tert -> denumire .'<br />
		<strong>Data:</strong> '. c_data($this -> data_factura) .' <br />
		<fieldset><legend>LEI</legend>
		<strong>Total Fara TVA:</strong> '. $this -> totalFaraTva() .' <strong>Total TVA:</strong> '. $this -> totalTva() .'<br />
		<strong>Total Factura:</strong> '. $totalFactura .'<br />
		</fieldset>';
		
		if($this -> tert -> tip == "extern_ue") {
			$out .= '<fieldset><legend>CURS VALUTAR</legend>'. $this -> curs_valutar .'</fieldset>';
			$out .= '<fieldset><legend>VALUTA</legend>
			<strong>Total Fara TVA:</strong> '. $this -> totalFaraTvaValuta() .' <strong>Total TVA:</strong> '. $this -> totalTvaValuta() .'<br />
			<strong>Total Factura:</strong> '. $this -> totalFacturaValuta() .'<br />
			</fieldset>';
		}
		
		$out .= '
			<fieldset><legend>INCASARI</legend>
			<strong>Total Incasari:</strong> '. $totalIncasari .'<br />
			<strong>Sold:</strong> '. $sold .'<br />
			</fieldset>
		</fieldset>
		';
		return $out;
	}
	
	function totalIncasari() {
		$sql = "
		SELECT sum(`suma`) as total_incasari
		FROM incasari_asocieri
		WHERE factura_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return $row['total_incasari'];
	}
	
	function sold() {
		if($this -> tert -> tip == "intern") {
			$totalFactura = $this -> totalFactura();
		}
		else {
			$totalFactura = $this -> totalFacturaValuta();
		}
		$totalIncasari = $this -> totalIncasari();
		$sold = $totalFactura - $totalIncasari;
		return $sold;
	}
	
	function areSold() {
		if($this -> sold() != 0) return true;
		return false;
	}
}
?>