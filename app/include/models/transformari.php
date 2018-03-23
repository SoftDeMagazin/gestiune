<?php
class Transformari extends Model
{
	var $tbl="transformari";
	var $_relations = array(
		"continut_mp" => array("type" => "many", "model" => "TransformariMp", "key" => "transformare_id"),
		"continut_pf" => array("type" => "many", "model" => "TransformariPf", "key" => "transformare_id"),
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_id"),
		"gestiune_destinatie" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_destinatie_id"),
		"serie" => array("type" => "one", "model" => "SeriiNumerice", "key" => "serie_id"),
		"utilizator" => array("type" => "one", "model" => "Utilizatori", "key" => "utilizator_id"),
		);
	var $_defaultForm = array(
		"transformare_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"data_doc" => array("type"=>"text", "label"=>"Data", "attributes" => array( "class" => "calendar")),
		"numar_doc" => array("type"=>"text", "label"=>"Numar Doc", "attributes" => array("readonly")),
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
		$s -> getByGestiuneAndTip($gestiune_id, 'transformari');
		if(count($s)) return $s -> serie;
		else return NULL;
	}
	
	function incrementSerie($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$serie -> increment();
	}  
	
	function setSerieId($gestiune_id) {
		$serie = $this -> getSerie($gestiune_id);
		$this -> serie_id = $serie -> id;	
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
		$continut = $this -> continut_mp;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$cnt -> produs -> scadStoc($cnt -> cantitate, $this -> gestiune_id, $cnt -> id, 'TransformariIesiri');			
		}
		return TRUE;
	}	
	
	function genereazaIntrari() {
	}
	
	/**
	 * anuleaza scaderile efectue
	 * @return bool;
	 */
	function anulareScaderi() {
		$continut = $this -> continut_mp;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$iesiri = new TransformariIesiri("where comp_id = '".$cnt -> id."'");
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
		
		$sql = "DELETE FROM transformari_mp WHERE transformare_id = '". $this -> id ."'";
		$db -> query($sql);
		$sql = "DELETE FROM transformari_pf WHERE transformare_id = '". $this -> id ."'";
		$db -> query($sql);
		$this -> delete();
	}
	
	
	function anulare() {
		global $db;
		$this -> anulareScaderi();
		$this -> anulat = 'DA';
		$this -> save();
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
		return $out;
	}
	
	
	
	/**
	 * adauga un produs finit in tabla transformari_pf
	 * @param object $produs_id
	 * @param object $cantitate
	 * @return 
	 */
	function adaugaProdusFinit($produs_id, $cantitate) {
		$pf = new TransformariPf();
		$pf -> transformare_id = $this -> id;
		$pf -> produs_id = $produs_id;
		$pf -> cantitate = $cantitate;
		$pf -> save();
		return $pf;
	}
	
	/**
	 * adauga o materie prima
	 * @param object $trans_pf_id
	 * @param object $produs_id
	 * @param object $cantitate
	 * @return 
	 */
	function adaugaMateriePrima($trans_pf_id, $produs_id, $cantitate) {
		$mp = new TransformariMp();
		$this -> transformare_id = $this -> id;
		$this -> trans_pf_id = $trans_pf_id;
		$this -> produs_id = $produs_id;
		$this -> cantitate = $cantitate;
		$this -> save();
	}
	/**
	 * adauga o reteta: automat produs finit si materiile prime aferente
	 * @param object $produs_id
	 * @param object $cantitate
	 * @return 
	 */
	function adaugaReteta($produs_id, $cantitate) {
		$produs = new Produse($produs_id);
		$pf = $this -> adaugaProdusFinit($produs_id, $cantitate);
		$mps = $produs -> getMateriiPrime($cantitate);
		foreach($mps as $mp) {
			$this -> adaugaMateriePrima($pf -> id, $mp['produs_id'], $mp['cantitate']);
		}
	}
	
	function getTotalMateriale() {
		$pfs = $this -> continut_pf;
		$total = 0;
		foreach($pfs as $pf) {
			$total += $pf -> getValoareMateriale();
		}
		return $total;
	}
}
?>