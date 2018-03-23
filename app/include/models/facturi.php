<?php
class Facturi extends Model
{
	var $tbl="facturi";
	var $_relations = array(
		"tert" => array("type"=>"one", "model"=>"Terti", "key"=>"tert_id", "value" => "denumire"),
		"gestiune" => array("type"=>"one", "model"=>"Gestiuni", "key"=>"gestiune_id", "value" => "denumire"),
		"agent" => array("type"=>"one", "model"=>"Agenti", "key"=>"agent_id", "value" => "nume"),
		"societate" => array("type"=>"one", "model" =>"Societati", "key"=>"societate_id", "value"=> "denumire"),
 		"continut" => array("type"=>"many", "model"=>"FacturiContinut", "key"=>"factura_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => "where 1 order by `cod_tva` asc"),
		"incasari_asociate" => array("type" => "many", "model"=>"IncasariAsocieri", "key"=>"factura_id"),
		"serie" => array("type" => "one", "model"=>"SeriiNumerice", "key"=>"serie_id"),
		"transfer" => array("type" => "one", "model"=>"Transferuri", "key"=>"transfer_id"),
		);
	var $_defaultForm = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva" => array("label" => "Cota Tva"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"transportator" => array("type" => "text", "label" => "Transportator"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"div_agent" => '<div id="div_frm_agent" >Agent</br></div>',
		"info_doc_end" => array("type" => "fieldend"),
		);
		
	var $_frmFacturaInterna = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"tip_factura" => array("type" => "select", "label" => "Tip Factura", "options" => "SELECT `cod`, `descriere` FROM `tipuri_facturi`"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva" => array("label" => "Cota Tva"),
		"valuta" => array("type" => "select", "options" => "SELECT `descriere`, `descriere` FROM valute", "label" => "Valuta"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"scadenta" => array("type" => "text", "label" => "Scadenta"),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"info_adr" => array("type" => "fieldstart", "label" => "Adresa Alternativa De Livrare"),
		"div_adr" => '<div id="div_frm_adresa" ></div>',
		"info_adr_end" => array("type" => "fieldend"),
		"info_intrastat" => array("type" => "fieldstart", "label" => "Info INTRASTAT"),
		"natura_tranzactie_a" => array("type" => "select", "label" => "Natura Tranzactiei A", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 0"),
		"natura_tranzactie_b" => array("type" => "select", "label" => "Natura Tranzactiei B", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 1"),
		"incoterm" => array("type" => "select", "label" => "Termeni Livrare", "options" => "SELECT `cod`, `cod` FROM `incoterms`"),
		"transport" => array("type" => "select", "label" => "Mod Transport", "options" => "SELECT `cod`, `descriere` FROM `transport`"),
		"info_intrastat_end" => array("type" => "fieldend"),
		"info_agent" => array("type" => "fieldstart", "label" => "Agent"),
		"div_agent" => '<div id="div_frm_agent" ></div>',
		"comision" => array("type" => "text", "label" => "Comision"),
		"info_agent_end" => array("type" => "fieldend"),
		"info_delegat" => array("type" => "fieldstart", "label" => "Delegat"),
		"div_delegat" => '<div id="div_frm_delegat" ></div>',
		"auto_numar" => array("type" => "text", "label" => "Numar Auto"),
		"info_delegat_end" => array("type" => "fieldend"),
		"info_doc_end" => array("type" => "fieldend"),
	);	
	
	var $_frmFacturaExterna = array(
		"factura_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"info_doc" => array("type" => "fieldstart", "label" => "Info Document"),
		"tip_factura" => array("type" => "select", "label" => "Tip Factura", "options" => "SELECT `cod`, `descriere` FROM `tipuri_facturi`"),
		"numar_doc" => array("type" => "text", "label" => "Numar Factura", "attributes" => array("style" => "width:400px;", "readonly")),
		"gestiune_id" => array("type" => "hidden"),
		"cota_tva_id" => array("type" => "hidden"),
		"valuta" => array("type" => "select", "options" => "SELECT `descriere`, `descriere` FROM valute", "label" => "Valuta"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"data_factura" => array("type" => "text", "label" => "Data Document (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
		"scadenta" => array("type" => "text", "label" => "Scadenta"),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta (zz.ll.aaaa)", "attributes" => array("style" => "width:200px;", "class" => "calendar")),
				"info_adr" => array("type" => "fieldstart", "label" => "Adresa Alternativa"),
		"div_adr" => '<div id="div_frm_adresa" ></div>',
		"info_adr_end" => array("type" => "fieldend"),
		"info_intrastat" => array("type" => "fieldstart", "label" => "Info INTRASTAT"),
		"natura_tranzactie_a" => array("type" => "select", "label" => "Natura Tranzactiei A", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 0"),
		"natura_tranzactie_b" => array("type" => "select", "label" => "Natura Tranzactiei B", "options" => "SELECT `cod`, `descriere` FROM `natura_tranzactie` WHERE parent_code = 1"),
		"incoterm" => array("type" => "select", "label" => "Termeni Livrare", "options" => "SELECT `cod`, `cod` FROM `incoterms`"),
		"transport" => array("type" => "select", "label" => "Mod Transport", "options" => "SELECT `cod`, `descriere` FROM `transport`"),
		"info_intrastat_end" => array("type" => "fieldend"),
		"info_agent" => array("type" => "fieldstart", "label" => "Agent"),
		"div_agent" => '<div id="div_frm_agent" ></div>',
		"comision" => array("type" => "text", "label" => "Comision"),
		"info_end_agent" => array("type" => "fieldend"),
		"info_delegat" => array("type" => "fieldstart", "label" => "Delegat"),
		"div_delegat" => '<div id="div_frm_delegat" ></div>',
		"auto_numar" => array("type" => "text", "label" => "Numar Auto"),
		"info_delegat_end" => array("type" => "fieldend"),
		"info_doc_end" => array("type" => "fieldend"),
	);	
		
	var $_validator = array(
		"numar_doc" => array(array("required", "Introduceti numar factura")),
		"data_factura" => array(array("required", "Introduceti data facturii")),
	);	
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Factura");
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Client - Cod Fiscal");
		$dg -> addHeadColumn("Curs Valutar");
		$dg -> addHeadColumn("Data Emitere");
		$dg -> addHeadColumn("Data Scadenta");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn(str_pad($this -> numar_doc, $this -> serie -> completare_stanga, "0", STR_PAD_LEFT));
			$dg -> addColumn($this -> gestiune -> denumire);
			$dg -> addColumn($this -> tert -> denumire ." - ". $this -> tert -> cod_fiscal);
			$dg -> addColumn($this -> curs_valutar);
			$dg -> addColumn(c_data($this -> data_factura));
			$dg -> addColumn(c_data($this -> data_scadenta));
			
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
		$this -> db -> query("delete from facturi_continut where factura_id = '". $this -> id ."'");
		$this -> delete();
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Sumar factura: '. $this -> numar_doc .'</legend>
		<strong>Client:</strong> '. $this -> tert -> denumire .'<br />
		<strong>Gestiune:</strong> '. $this -> gestiune -> denumire .'<br />
		<strong>Data:</strong> '. c_data($this -> data_factura) .'<br />
		<strong>Total Fara TVA:</strong> '. $this -> totalFaraTva() .' <strong>Total TVA:</strong> '. $this -> totalTva() .'<br />
		<strong>Total Factura:</strong> '. $this -> totalFactura() .'<br />
		</fieldset>
		';
		return $out;
	}
	
	function totalFaraTva() {
		$sql = "
		SELECT sum(`cantitate`*`pret_vanzare_ron`) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalTva() {
		$sql = "
		SELECT sum(`cantitate`*(`pret_ron_cu_tva` - `pret_vanzare_ron`)) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalFactura() {
		$sql = "
		SELECT sum(`cantitate`*`pret_ron_cu_tva`) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return douazecimale($total['total']);
	}
	
	function totalFaraTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_vanzare_val`) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function totalTvaValuta() {
		$sql = "
		SELECT sum(`cantitate`*(`pret_val_cu_tva` - `pret_vanzare_val`)) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function totalFacturaValuta() {
		$sql = "
		SELECT sum(`cantitate`*`pret_val_cu_tva`) as total 
		FROM `facturi_continut` 
		WHERE `factura_id` = '". $this -> id ."';
		";
		$total = $this -> db -> getRow($sql);
		return $total['total'];
	}
	
	function sterge() {
		$this -> anulareScaderi();
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
		$s_fact = new SeriiDocumente("where gestiune_id = '$gestiune_id' and tip_doc = 'facturi'");
		if(count($s_fact))
		return $s_fact -> serie;
	}
	
	function setSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$this -> serie_id = $serie -> id;
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
	
	function reducerePreturi($procent) {
		$sql = "
		UPDATE facturi_continut
		SET
		pret_vanzare_ron = (pret_vanzare_ron*(100 - $procent))/100,
		pret_vanzare_val = (pret_vanzare_val*(100 - $procent))/100,
		pret_ron_cu_tva = (pret_ron_cu_tva*(100 - $procent))/100,
		pret_val_cu_tva = (pret_val_cu_tva*(100 - $procent))/100,
		val_vanzare_ron = (val_vanzare_ron*(100 - $procent))/100,
		val_vanzare_ron = (val_vanzare_ron*(100 - $procent))/100,
		val_vanzare_val = (val_vanzare_val*(100 - $procent))/100,
		val_ron_cu_tva = (val_ron_cu_tva*(100 - $procent))/100,
		val_val_cu_tva = (val_val_cu_tva*(100 - $procent))/100,
		val_tva_ron = (val_tva_ron*(100 - $procent))/100,
		val_tva_val = (val_tva_val*(100 - $procent))/100
		WHERE
		factura_id = '". $this -> id ."';	
		";
		$this -> db -> query($sql);
	}
	
	function anulareScaderi() {
		$continut = $this -> continut;
		foreach($continut as $cnt) {
			$iesiri = new FacturiIesiri("where comp_id = '". $cnt -> continut_id ."'");
			foreach($iesiri as $iesire) {
				$lot = new Loturi($iesire -> lot_id);
				$lot -> cantitate_ramasa += $iesire -> cantitate;
				$lot -> save();
				$iesire -> delete();
			}
		}
	}
	
	function scadStoc() {
		$continut = $this -> continut;
		foreach($continut as $cnt) {
			if($cnt -> cantitate > 0) $cnt -> produs -> scadStoc($cnt -> cantitate, $this -> gestiune_id, $cnt -> id, "FacturiIesiri");
		}
		$stornari = new FacturiContinut("where `factura_id` = '". $this -> id ."' and `storno` <> 0");
		if(count($stornari)) {
			foreach($stornari as $stornare) {
				$iesiri = new FacturiIesiri("
					inner join loturi using(lot_id)
					where comp_id = '". $stornare -> storno_id ."'
					and cantitate > 0
					and cantitate > cantitate_stornata
					order by loturi.data_intrare desc, loturi.lot_id desc
				"
				);
				$cantitate = $stornare -> cantitate*(-1);
				foreach($iesiri as $iesire) {
				
					if($cantitate == 0) break;
					if($iesire -> cantitate > $cantitate) {
						$cantitate_lot = $cantitate;
					}
					else {
						$cantitate_lot = $iesire -> cantitate;
						$cantitate -= $iesire -> cantitate;
					}
					
					$iesire -> cantitate_stornata = $cantitate_lot;
					$iesire -> save();
					
					$lotIesire = new Loturi($iesire -> lot_id);
					$pret_intrare_ron = $lotIesire -> pret_intrare_ron;
					$pret_intrare_val = $lotIesire -> pret_intrare_val;
					
					$lot = new Loturi();
					$lot -> gestiune_id = $this -> gestiune_id;
					$lot -> societate_id = $this -> societate_id;
					$lot -> doc_id = $this -> id;
					$lot -> doc_comp_id = $stornare -> id;
					$lot -> doc_tip = "factura_retur";
					$lot -> produs_id = $stornare -> produs_id;
					$lot -> cantitate_init = $cantitate_lot;
					$lot -> cantitate_ramasa = $cantitate_lot;
					$lot -> pret_intrare_ron = $pret_intrare_ron;
					$lot -> pret_intrare_val = $pret_intrare_val;
					$lot -> data_intrare = $this -> data_factura;
					$lot -> tip_lot = "factura_retur";
					$lot -> save();
					
				}
			}
		}
	}

	function adaugaContinut($produs_id, $cantitate, $pret_vanzare_ron=0,$pret_vanzare_val=0) {
		$cnt = new FacturiContinut();
		$cnt -> factura_id = $this -> id;
		
		$produs = new Produse($produs_id);
		$cnt -> produs_id = $produs_id;
		$cnt -> denumire = $produs -> denumire;
		$cnt -> cod_produs = $produs -> cod_produs;
		$cnt -> cod_bare = $produs -> cod_bare;
		$cnt -> nc8 = $produs -> nc8;
		$cnt -> unitate_masura_id = $produs -> unitate_masura_id;
		
		$cnt -> cantitate = $cantitate;
		$cnt -> pret_vanzare_ron = $pret_vanzare_ron;
		
		$cnt -> recalculareTotaluriRon();
		
		$cnt -> save();
	}
}
?>