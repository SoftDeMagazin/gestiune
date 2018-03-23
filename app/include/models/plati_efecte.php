<?php
class PlatiEfecte extends Model
{
	var $tbl="plati_efecte";
	var $_relations = array(
			"mod_plata" => array(
				"type" => "one", 
				"model" => "ModalitatiPlata",
				"key" => "mod_plata_id", 
				"value" => "descriere", 
				"conditions" => " WHERE `efect_comercial` = 'DA' order by `descriere` asc"
				),
			"tert" => array(
				"type" => "one",
				"model" => "Terti", 
				"key" => "tert_id"
				)
		);
	var $_defaultForm = array(
		"plata_efect_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"societate_id" => array("type" => "hidden"),
		"numar_doc" => array("type" => "text", "label" => "Numar Doc"),
		"mod_plata" => array("label" => "Tip Document"),
		"data_emitere" => array("type" => "text", "label" => "Data Emitere", "attributes" => array("class" => "calendar")),
		"data_scadenta" => array("type" => "text", "label" => "Data Scadenta", "attributes" => array("class" => "calendar")),
		"suma" => array("type" => "text", "label" => "Suma"),
		"explicatie" => array("type" => "textarea", "label" => "Explicatie", "attributes" => array("rows" => "3")),			
		);
	
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Tip Doc");
		$dg -> addHeadColumn("Data Emitere");
		$dg -> addHeadColumn("Data Scadenta");
		$dg -> addHeadColumn("Suma");
		$dg -> addHeadColumn("Suma Acceptata");
		$dg -> addHeadColumn("Operat");
		$dg -> addHeadColumn("Raspuns");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn($this -> mod_plata -> descriere);	
			$dg -> addColumn(c_data($this -> data_emitere));
			$dg -> addColumn(c_data($this -> data_scadenta));	
			$dg -> addColumn(money($this -> suma, $this -> tert -> valuta));
			$dg -> addColumn(money($this -> suma_acceptata, $this -> tert -> valuta));
			$dg -> addColumn($this -> operat);
			switch($this -> raspuns) {
				case "OK": {
					$dg -> addColumn("accept");
				}break;	
				case "PA": {
					$dg -> addColumn("accept partial");
				}break;	
				case "NA": {
					$dg -> addColumn("refuz total");
				}break;	
				default: {
					$dg -> addColumn("neoperat");
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
			"onDblClick"=>"$dck",
			"style" => "$style"
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
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Tip Doc");
		$dg -> addHeadColumn("Data Emitere");
		$dg -> addHeadColumn("Data Scadenta");
		$dg -> addHeadColumn("Suma");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> numar_doc);
			$dg -> addColumn($this -> mod_plata -> descriere);	
			$dg -> addColumn(c_data($this -> data_emitere));
			$dg -> addColumn(c_data($this -> data_scadenta));	
			$dg -> addColumn(money($this -> suma, $this -> tert -> valuta));
			
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck",
			"style" => "$style"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	function listaRptNeplatite() {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Client");
		$dg -> addHeadColumn("Numar Doc");
		$dg -> addHeadColumn("Data Emitere / Scadenta");
		$dg -> addHEadColumn("Suma");
		$dg -> addHeadColumn("Depunere"); 
		$dg -> addHeadColumn("Accept");
		$dg -> addHeadColumn("Accept Partial");
		$dg -> addHeadColumn("Refuz");
		$dg -> addHeadColumn("&nbsp;");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> tert -> denumire);
			$dg -> addColumn($this -> mod_plata -> cod.' '.$this -> numar_doc.'<input type="hidden" name="incasare_efect_id['. $this -> id .']" value="'. $this -> id .'" />');	
			$dg -> addColumn(c_data($this -> data_emitere).' / '.c_data($this -> data_scadenta));
			$dg -> addColumn(money($this -> suma, $this -> tert -> valuta));
			if($this -> depus == 'DA') {
				$dg -> addColumn("Depus - ".c_data($this -> data_depunere));
			}
			else {
			$dg -> addColumn('<input type="radio" name="action['. $this -> id .']" value="depunere" ><input type="text" name="data_depunere['. $this -> id .']" value="'. c_data(data()) .'" class="calendar">');
			}
			$dg -> addColumn('<input type="radio" name="action['. $this -> id .']" value="accept_total" >');
			$dg -> addColumn('<input type="radio" name="action['. $this -> id .']" value="accept_partial" ><input type="text" name="suma_acceptata['. $this -> id .']" size=10 value="0.00" onFocus="this.select();">');
			$dg -> addColumn('<input type="radio" name="action['. $this -> id .']" value="refuz" >');
			$dg -> addColumn('<input type="radio" name="action['. $this -> id .']" value="na" checked>');
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck",
			"style" => "$style"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}		
	
	function operareEfect($suma, $stare) {
		switch($stare) {
			case "OK": {
				$plata = new Plati();
				$plata -> mod_plata_id = $this -> mod_plata_id;
				$plata -> suma = $this -> suma;
				$plata -> numar_doc = $this -> numar_doc;
				$plata -> tert_id = $this -> tert_id;
				$plata -> societate_id = $this -> societate_id;
				$plata -> gestiune_id = $this -> gestiune_id;	
				$plata -> explicatie = $this -> explicatie;
				$plata -> data_doc = $this -> data_scadenta;
				$plata -> incasare_efect_id = $this -> id;
				$plata -> save();
				$suma = $this -> suma;
			}break;
			case "NA": {
				$suma = '0.00';
			}break;
			case "PA": {
				$plata = new Plati();
				$plata -> mod_plata_id = $this -> mod_plata_id;
				$plata -> suma = $suma;
				$plata -> numar_doc = $this -> numar_doc;
				$plata -> tert_id = $this -> tert_id;
				$plata -> societate_id = $this -> societate_id;
				$plata -> gestiune_id = $this -> gestiune_id;	
				$plata -> explicatie = $this -> explicatie;
				$plata -> data_doc = $this -> data_scadenta;
				$plata -> incasare_efect_id = $this -> id;
				$plata -> save();
			}break;
		}	
		$this -> operat = 'DA';
		$this -> raspuns = $stare;
		$this -> suma_acceptata = $suma;
		$this -> data_incasare = data();
		$this -> save();
	}	
	
	function depunere($data) {
		$this -> depus = 'DA';
		$this -> data_depunere = $data;
		$this -> save();
	}
}
?>