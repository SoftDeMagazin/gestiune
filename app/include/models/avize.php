<?php
class Avize extends Model
{
	var $tbl="avize";
	var $_relations = array(
		"continut" => array("type" => "many", "model" => "AvizeContinut", "key" => "aviz_id"),
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_id"),
		"utilizator" => array("type" => "one", "model" => "Utilizatori", "key" => "utilizator_id"),
		"tert" =>array("type" => "one", "model" => "Terti", "key" => "tert_id"),
		"cota_tva" => array("type"=>"one", "model"=>"CoteTva", "key"=>"cota_tva_id", "value" => "valoare", "conditions" => "where 1 order by `cod_tva` asc"),
		"tip" => array("type" => "one", "model" => "TipuriAvize", "key" => "tip_aviz", "value" => "descriere", "conditions" => "where activ = 'DA'"),
		"factura" => array("type" => "one", "model" => "Facturi", "key" => "factura_id"),
		"transfer" => array("type" => "one", "model" => "Transferuri", "key" => "transfer_id"),  
		);
	var $_defaultForm = array(
		"aviz_id" => array("type" => "hidden"),
		"tip_aviz" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"div_cautare_tert" => '	
	<div>	
	Client: <br>	
	<input name="txtCautareFurnizor" type="text" value="" id="txtCautareFurnizor" size="45">
    <span id="err_frm_tert_id" class="error">&nbsp;</span>
    <div id="div_filtru_furnizori" style="position:absolute;">
    &nbsp;
    </div>
	</div> 
	',
		"tert_id" => array("type" => "hidden"),
		"data_doc" => array("type"=>"text", "label"=>"Data", "attributes" => array( "class" => "calendar")),
		"numar_doc" => array("type"=>"text", "label"=>"Numar Doc", "attributes" => array("readonly")),
		"cota_tva" => array("label" => "Cota Tva"),
		"valuta" => array("type" => "select", "options" => "SELECT `descriere`, `descriere` FROM valute", "label" => "Valuta"),
		"curs_valutar" => array("type" => "text", "label" => "Curs valutar"),
		"div_save" => '<div id="div_save"></div>'
		);
	
	var $frmLaFactura = array(
		"aviz_id" => array("type" => "hidden"),
		"tip_aviz" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"tert_id" => array("type" => "hidden"),
		"data_doc" => array("type"=>"text", "label"=>"Data", "attributes" => array( "class" => "calendar")),
		"numar_doc" => array("type"=>"text", "label"=>"Numar Doc", "attributes" => array("readonly")),
		"factura_id" => array("type" => "hidden"),
		"cautare_facturi" => '
			<div>
			Factura:
			<div id="selected_factura">&nbsp;</div>
			<div id="div_perioada">
			<fieldset>
			<legend>Perioada</legend>
			<input type="text" name="from" id="from" class="calendar"> - 
			<input type="text" name="end" id="end" class="calendar">	
			<input type="button" name="btnAfiseazaFacturi" id="btnAfiseazaFacturi" value="Afiseaza Facturi" onClick="xajax_afiseazaFacturi($(\'#from\').val(), $(\'#end\').val());">
			</fieldset>
			</div>
			<fieldset>
			<legend>Facturi</legend>
			<div id="grid_facturi" style="height:300px; overflow:scroll; overflow-x:hidden;"></div>
			</fieldset>
			</div>
			',
		"div_save" => '<div id="div_save"></div>'
		);	
	
	/**
	 * returneaza numarul curent al documentului
	 * @param int $gestiune_id id-ul gestiunii
	 * @return int
	 */
	function getNumar($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		return $serie -> curent+1;
	}
	
	function getSerie($gestiune_id) {
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($gestiune_id, 'avize');
		return $s -> serie;
	}
	
	function incrementSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$serie -> increment();
	}  
		
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Numar Doc");
			$dg -> addHeadColumn("Data Doc");
			$dg -> addHeadColumn("Gestiune");
			$dg -> addHeadColumn("Intocmit de");
			$dg -> addHeadColumn("Validat");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> numar_doc);
				$dg -> addColumn(c_data($this -> data_doc));
				$dg -> addColumn($this -> gestiune -> denumire);
				$dg -> addColumn($this -> utilizator -> nume);
				$dg -> addColumn($this -> salvat);
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
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}			
	/**
	 * scade stocul fifo
	 * @return bool
	 */	
	function scadStoc() {
		$continut = $this -> continut;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$cnt -> produs -> scadStoc($cnt -> cantitate, $this -> gestiune_id, $cnt -> id, 'AvizeIesiri');			
		}
		return TRUE;
	}	
	
	/**
	 * anuleaza scaderile efectue
	 * @return bool;
	 */
	function anulareScaderi() {
		$continut = $this -> continut;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$iesiri = new AvizeIesiri("where comp_id = '".$cnt -> id."'");
			foreach($iesiri as $iesire) {
				//refac lotul din care s-a efectuat scaderea
				$lot = new Loturi($iesire -> lot_id);
				$lot -> cantitate_ramasa += $iesire -> cantitate;
				$lot -> save();
				//sterg iesire
				$iesire -> delete();
			}
		}
	}

	/**
	 * sterge documentul
	 * @return 
	 */
	function sterge() {
		global $db;
		$this -> anulareScaderi();
		$serie = $this -> getSerie($this -> gestiune_id);
		$serie -> decrement();
		$sql = "DELETE FROM avize_continut WHERE aviz_id = '". $this -> id ."'";
		$db -> query($sql);
		$this -> delete();
	}
	
	
	function anulare() {
		global $db;
		$this -> anulareScaderi();
		$this -> anulat = 'DA';
		$this -> save();
	}
	
	function adaugaContinut($produs_id, $cantitate, $pret_vanzare_ron=0) {
		$continut = new AvizeContinut();
		$continut -> aviz_id = $this -> id;
		$continut -> produs_id = $produs_id;
		$continut -> cantitate = $cantitate;
		if($pret_vanzare_ron) {
			$continut -> pret_vanzare_ron = $pret_vanzare_ron;
		}
		$continut -> save();
	}
	
	
	function totalAviz() {
				$sql = "
				select
				sum(avize_iesiri.cantitate * loturi.pret_intrare_ron) as val_vanzare_ron
				from avize_iesiri
				inner join avize_continut on avize_continut.continut_id = avize_iesiri.comp_id
				inner join loturi using(lot_id)
				where avize_continut.aviz_id = '". $this -> id ."'
				";
		$row = $this -> db -> getRow($sql);
		return $row['val_vanzare_ron']; 
	}
	
		function totalAvizPvLei() {
				$sql = "
				select
				sum(avize_continut.cantitate * avize_continut.pret_vanzare_ron) as val_vanzare_ron
				from avize_continut
				where avize_continut.aviz_id = '". $this -> id ."'
				";
		$row = $this -> db -> getRow($sql);
		return $row['val_vanzare_ron']; 
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Aviz Nr: '. $this -> numar_doc .'</legend>	
		Numar Document: '. $this -> numar_doc .'<br>
		Data: '. c_data($this -> data_doc) .'<br>
		Intocmit: '. $this -> utilizator -> nume .'<br>
		</fieldset>
		';
		switch($this -> tip_aviz) {
			case "la_factura": {
				$out .= $this -> factura -> sumar();
			}break;
			case "la_transfer": {
				$out .= $this -> transfer -> sumar();
			}break;
			case "doc_pv": {}
			case "doc_pa": {
				$tert = $this -> tert;	
				if(count($tert)) {
					$out .= '<fieldset>
					<legend>Emis catre:</legend>
					'. $tert -> denumire .' - '. $tert -> cod_fiscal .'
					</fieldset>';
				}
			}break;
		}
		return $out;
	}
	
	function emiteFactura() {
		$factura = new Facturi();
		$gest = $this -> gestiune;
		$factura -> tert_id = $this -> tert_id;
		$factura -> gestiune_id = $gest -> id;
		$factura -> societate_id = $gest -> societate_id;
		$factura -> utilizator_id = $_SESSION['user'] -> user_id;
		$factura -> cota_tva_id = 1;
		$factura -> valuta = 'LEI';
		$factura -> curs_valutar = $this -> curs_valutar;
		$factura -> data_factura = data();
		$factura -> data_scadenta = data();
		$factura -> numar_doc = $factura -> getNumar($gest -> id);
		$factura -> setSerie($gestiune_id);
		$factura -> incrementSerie($gest -> id);
		$factura -> tip_factura = 'interna';
		$factura -> salvat = 'DA';
		$factura -> save();
		
		$continut = $this -> continut;
		foreach($continut as $cnt) {
			$factura -> adaugaContinut($cnt -> produs_id, $cnt -> cantitate, $cnt -> pret_vanzare_ron);
		}
		
		$this -> anulareScaderi();
		
		$this -> tip_aviz = 'la_factura';
		$this -> factura_id = $factura -> id;
		$this -> save();
		$factura -> scadStoc();
		
		return $factura;
	}
}
?>