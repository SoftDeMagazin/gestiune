<?php
class Terti extends Model
{
	var $tbl="terti";
	var $_relations = array(
		"delegati" => array("type" => "many", "model" => "Delegati", "key" => "tert_id"),	
		"adrese" => array("type" => "many", "model" => "TertiAdrese", "key" => "tert_id"),
		);
	var $_defaultForm = array(
		"tert_id" => array("type" => "hidden"),
		"denumire" => array("type"=>"text", "label"=>"Denumire", "attributes" => array( "style" => "width:400px;")),
		"tip" => array("type"=>"select", "options" => "SELECT `tip_tert`, `descriere` FROM `tipuri_terti`","label" => "Tip"),
		"reg_com" => array("type"=>"text", "label"=>"Reg com", "attributes" => array( "style" => "width:400px;")),
		"cod_fiscal" => array("type"=>"text", "label"=>"Cod fiscal", "attributes" => array( "style" => "width:400px;")),
		"cod_tara" => array("type" => "select", "label"=>"Tara", "value" => "RO", "options" => "SELECT cod, concat(cod, ' - ', denumire) from tari", "attributes" => array( "style" => "width:400px;")),
		"sediul" => array("type"=>"textarea", "label"=>"Sediul", "attributes" => array( "style" => "width:400px;")),
		"judet" => array("type"=>"text", "label"=>"Judet", "attributes" => array( "style" => "width:400px;")),
		"iban" => array("type"=>"text", "label"=>"Iban", "attributes" => array("style" => "width:400px;", "class" => "iban")),
		"banca" => array("type"=>"text", "label"=>"Banca", "attributes" => array( "style" => "width:400px;")),
		"telefon" => array("type"=>"text", "label"=>"Telefon", "attributes" => array("style" => "width:400px;")),
		"email" => array("type"=>"text", "label"=>"Email", "attributes" => array("style" => "width:400px;")),
		"valuta" => array("type"=>"select", "value" => "LEI", "options" => "SELECT `descriere`, `descriere` FROM `valute`","label" => "Valuta"),
		"gest" => '<div id="div_frm_gest">Gestiune</div>',
		);
		
	
	var $_validator = array(
	);	
	
	function cautare($str) {
		$this -> fromString(" inner join terti_gestiuni using(tert_id) WHERE gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and (`denumire` LIKE '$str%' or `cod_fiscal` LIKE '$str%')");
		return count($this);
	}
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Cod fiscal");
		$dg -> addHeadColumn("Telefon");
		$dg -> addHeadColumn("Delegati");
		$dg -> addHeadColumn("Adrese");
		$dg -> addHeadColumn("Agenti");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn($this -> cod_fiscal);
			$dg -> addColumn($this -> telefon);
			$dg -> addColumn(iconEdit("xajax_afisareDelegati('". $this -> id ."')"));
			$dg -> addColumn(iconEdit("xajax_afisareAdrese('". $this -> id ."')"));
			$dg -> addColumn(iconView("xajax_afisareAgenti('". $this -> id ."')"));
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
	
	
	function listaSituatiiFinanciareIncasari($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Sold");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			switch($this -> tip) {
				case "intern": {
					$dg -> addColumn(money($this -> soldIncasari(), $this -> valuta));
				}break;
				case "extern_ue": {
					$dg -> addColumn(money($this -> soldIncasari(), $this -> valuta));
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
	
	function listaSituatiiFinanciarePlati($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Sold");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn(money($this -> soldPlati(), $this -> valuta));

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
	
	
	function select($selected=0) 
	{
		$nr_r = count($this);
		$out = '<select name="tert_id" id="tert_id" style="width:400px">';
		$out .= '<option value="0">Selectare</option>';	
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if($this -> id == $selected) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> denumire .' - '. $this -> cod_fiscal .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}
	
	function selectMultiple() {
		$nr_r = count($this);
		$out = '<select size="5" name="sel_furnizor" id="sel_furnizor" style="width:400px">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .' - '. $this -> cod_fiscal .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}	
	
	function selectMulti() {
		$nr_r = count($this);
		$out = '<select size="5" name="tert_id[]" id="tert_id" style="width:400px">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$out .= '<option value="'. $this -> id .'">'. $this -> denumire .' - '. $this -> cod_fiscal .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}	


	function selectUIMultiple($selected=array()) {
		$nr_r = count($this);
		$out = '<select size="5" multiple name="sel_tert[]" id="sel_tert" style="margin:0px auto;width:90%; height: 500px;">';
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			if(in_array($this -> id, $selected)) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			$out .= '<option value="'. $this -> id .'" '. $sel .'>'. $this -> denumire .' - '. $this -> cod_fiscal .'</option>';			
		}
		$out .= '</select>';
		return $out;
	}	


	function situatieIncasari() {
		// se transforma in view
		$sql = "
		SELECT sum(suma) as total_incasari FROM incasari
		WHERE tert_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return douazecimale($row['total_incasari']);
	}
	
	function situatieFacturiEmise() {
		$sql = "
		SELECT sum(cantitate*pret_ron_cu_tva) as total_ron, sum(cantitate*pret_val_cu_tva) as total_val FROM facturi_continut
		INNER JOIN facturi USING(factura_id)
		WHERE facturi.tert_id = '". $this -> id ."';	
		";
		$row = $this -> db -> getRow($sql); 
		switch($this -> tip) {
			case "intern": {
				return douazecimale($row['total_ron']);
			}break;
			case "extern_ue": {
				return douazecimale($row['total_val']);
			}break;
		}
	}
	
	function soldIncasari() {
		return douazecimale($this -> situatieFacturiEmise() - $this -> situatieIncasari());
	}
	
	function situatiePlati() {
		// se transforma in view
		$sql = "
		SELECT sum(suma) as total_plati FROM plati
		WHERE tert_id = '". $this -> id ."'
		";
		$row = $this -> db -> getRow($sql);
		return douazecimale($row['total_plati']);
	}
	
	function situatieFacturiIntrari() {
		$sql = "
		SELECT sum(cantitate*pret_ach_ron + val_tva_ron) as total_ron, sum(cantitate*pret_ach_val + val_tva_ron) as total_val FROM facturi_intrari_continut
		INNER JOIN facturi_intrari USING(factura_intrare_id)
		WHERE facturi_intrari.tert_id = '". $this -> id ."';	
		";
		$row = $this -> db -> getRow($sql); 
		switch($this -> tip) {
			case "intern": {
				return douazecimale($row['total_ron']);
			}break;
			case "extern_ue": {
				return douazecimale($row['total_val']);
			}break;
		}
	}
	
	function soldPlati() {
		return douazecimale($this -> situatieFacturiIntrari() - $this -> situatiePlati());
	}
	
	function situatieActualaClient($gestiune_id=0) {
		$out .= '<h2 align="center">SITUATIE  ACTUALA  CLIENT</h2>';
		$out .= '<h2>'. $this -> denumire .' - '. $this -> cod_fiscal .'</h2>';
		$out .= '<h3>Facturi emise neincasate</h3>';	
		$gestiune = new Gestiuni($gestiune_id);
		if(!$gestiune_id) $gestiune -> getGestiuneActiva();
		$facturi = new ViewFacturi("where tert_id = '". $this -> id ."' and societate_id = '". $gestiune -> punct_lucru -> societate_id ."' and achitat = 'NU'");
		$out .= $facturi -> listaSituatieActuala();
		$facturiNeachitate = douazecimale($facturi -> getTotalSold());
		$out .= '<div align="left"> <strong>Total:</strong> '. $facturiNeachitate .'</div>';
		$efecte = new IncasariEfecte("where tert_id = '". $this -> id ."' and societate_id = '". $gestiune -> punct_lucru -> societate_id ."' and operat = 'NU'");
		$out .= '<h3>Efecte de comert neincasate</h3>';
		$out .= $efecte -> listaSituatieActuala();
		$efecteNeincasate = douazecimale($efecte -> getTotalEfecte());
		$out .= '<div align="left"> <strong>Total:</strong> '. $efecteNeincasate .'</div>';
		$incasari = new ViewIncasari("where tert_id = '". $this -> id ."' and societate_id = '". $gestiune -> punct_lucru -> societate_id ."' and round(ramas,2) > 0");
		$out .= '<h3>Incasari neasociate</h3>';
		$out .= $incasari -> listaSituatieActuala();
		$incasariNeasociate = douazecimale($incasari -> getTotalNeasociat());
		$out .= '<div align="left"> <strong>Total:</strong> '. $incasariNeasociate .'</div>';
		
		$soldClient = douazecimale($facturiNeachitate - $incasariNeasociate);
		$soldDescoperit = $soldClient - $efecteNeincasate;
		$out .= '
			<br />
			<br />
			<table border="1" align="center" cellpadding=1 cellspacing=0 style="width:400px; margin:0px auto">
				<tr>
					<td align="left"><strong>Facturi Neachitate</strong></td>
					<td style="text-align:right;">'. money($facturiNeachitate, $this -> valuta) .'</td>
				</tr>
				<tr>
					<td align="left"><strong>Incasari Neasociate</strong></td>
					<td style="text-align:right;">'. money($incasariNeasociate, $this -> valuta) .'</td>
				</tr>
				<tr>
					<td align="left"><strong>Sold Client</strong></td>
					<td style="text-align:right;">'. money($soldClient, $this -> valuta) .'</td>
				</tr>
				<tr>
					<td align="left"><strong>Sold Acoperit</strong></td>
					<td style="text-align:right;">'. money($efecteNeincasate, $this -> valuta) .'</td>
				</tr>
				<tr>
					<td align="left"><strong>Sold Descoperit</strong></td>
					<td style="text-align:right;">'. money($soldDescoperit, $this -> valuta) .'</td>
				</tr>
			</table>	
		';

		return $out;
	}
	
	function situatieActualaFurnizor($gestiune_id=0) {
		$out .= '<h2>Facturi emise neincasate</h2>';	
		$gestiune = new Gestiuni($gestiune_id);
		if(!$gestiune_id) $gestiune -> getGestiuneActiva();
		$facturi = new ViewFacturiIntrari("where tert_id = '". $this -> id ."' and societate_id = '". $gestiune -> punct_lucru -> societate_id ."' and achitat = 'NU'");
		$out .= $facturi -> listaSituatieActuala();
		$efecte = new PlatiEfecte("where tert_id = '". $this -> id ."' and societate_id = '". $gestiune -> punct_lucru -> societate_id ."' and operat = 'NU'");
		$out .= '<h2>Efecte de comert neincasate</h2>';
		$out .= $efecte -> listaSituatieActuala();
		return $out;
	}
	
		/**
	 * disociaza gestiunile care nu sunt in array
	 * @param array $gestiuni vector id-uri gestiuni array($id1, $id2...)
	 */
	 
	 function disociazaGestiuni($gestiuni=array()) {
	 	$gestiuni_asociate = $this -> getGestiuniAsociate();
	 	foreach($gestiuni_asociate as $gest_id) {
			if(!in_array($gest_id, $gestiuni)) {
				$cg = new TertiGestiuni(" where `gestiune_id` = '$gest_id' and tert_id = '". $this -> id ."'");
				$cg -> delete();
			}
		}
	 } 
	
	/**
	 *  asociaza categorie cu gestiunile din array ... 
	 *	@param array $gestiuni vector id-uri gestiuni($id1, $id2, ...)
	 */  
	function asociazaCuGestiuni($gestiuni=array()) {
		$gestiuni_asociate = $this -> getGestiuniAsociate();
		foreach($gestiuni as $gest_id) {
			if(!in_array($gest_id, $gestiuni_asociate)) {
				$cg = new TertiGestiuni();
				$cg -> gestiune_id = $gest_id;
				$cg -> tert_id = $this -> id;
				$cg -> save();
			}
		}
	}
	
	function getGestiuniAsociate() {
		$rows = $this -> db -> getRowsNum("select gestiune_id from terti_gestiuni where tert_id = '". $this -> id ."'");
		$out = array();
		foreach($rows as $row) {
			$out[] = $row[0];
		}
		return $out;
	}
	
	function getByGestiuneId($gestiune_id, $conditions="") {
		$this -> getByGestiune($gestiune_id, $conditions);
	}
	
	function getByGestiune($gestiune="", $conditions="") {
		if(empty($gestiune)) {
			$sql_gest = " gestiune_id = '". $_SESSION['user'] -> gestiune_id."'";
		}
		elseif(is_array($gestiune)) {
			$sql_gest = " gestiune_id in (". implode(",", $gestiune) .")";
		} else {
			$sql_gest = " gestiune_id = '". $gestiune ."' ";
		}
		
		$sql = " inner join `terti_gestiuni` using(`tert_id`) WHERE ". $sql_gest ." ".$conditions;
		$this -> fromString($sql);
	}
	
	function getFurnizori() {
		$sql = "
		where tert_id in (select distinct tert_id from niruri)
		";
		$this -> fromString($sql);
	}
	function getClienti() {
		$sql = "
		where tert_id in (select distinct tert_id from facturi)
		";
		$this -> fromString($sql);
	}
	
	/**
	 * copiaza tertii primite ca parametru in gestiunea primita ca parametru
	 * 
	 * @param object $gestiune_id gestiunea in care sa se copieze categoriile
	 * @param object $categorii_ids id-urile categoriile ce se vor copia
	 * @return 
	 */
	function copiazaInGestiuneNoua($gestiune_sursa_id,$gestiune_id, $terti_ids)
	{
		$sql = "INSERT INTO terti_gestiuni (tert_id,gestiune_id,scadenta_default)
				SELECT t.tert_id, $gestiune_id, t.scadenta_default
				FROM terti_gestiuni t
				WHERE t.tert_id in $terti_ids
					and t.gestiune_id = $gestiune_sursa_id
				";
		$this->db->query($sql);
	}
	
	function blocheazaClient($gestiune_id) {
		$tg = new TertiGestiuni("where gestiune_id = '$gestiune_id' and tert_id = '". $this -> id ."'");
		$tg -> categorie_tert_id = 4;
		$tg -> save();
	}
	
	function esteBlocat($gestiune_id) {
		$tg = new TertiGestiuni("where gestiune_id = '$gestiune_id' and tert_id = '". $this -> id ."'");
		if($tg -> categorie_tert_id) {
			$categ = new CategoriiTerti($tg -> categorie_tert_id);
			if($categ -> blocare_tert == 'DA') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return false;
		}
	}
	
	function getLimitaDeCredit($gestiune_id) {
		$tg = new TertiGestiuni("where gestiune_id = '$gestiune_id' and tert_id = '". $this -> id ."'");
		$limita = $tg -> limita_credit_intern +$tg -> limita_credit_asigurat;
		return douazecimale($limita);
	}
	
	function depasesteLimitaCredit($gestiune_id) {
		$limita = $this -> getLimitaDeCredit($gestiune_id);
		if($limita > 0) {
			$gest = new Gestiuni($gestiune_id);
			$v = new ViewClienti("where tert_id = '". $this -> id ."' and societate_id = '". $gest -> societate_id ."'");
			if($limita < $v -> soldDescoperit() ) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}
?>