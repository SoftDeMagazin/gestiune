<?php
class BonuriConsum extends Model
{
	var $tbl="bonuri_consum";
	var $_relations = array(
		"continut" => array("type" => "many", "model" => "BonuriConsumContinut", "key" => "bon_consum_id"),
		"gestiune" => array("type" => "one", "model" => "Gestiuni", "key" => "gestiune_id"),
		"utilizator" => array("type" => "one", "model" => "Utilizatori", "key" => "utilizator_id"),
		"serie" => array("type" => "one", "model" => "SeriiNumerice", "key" => "serie_id"),
		);
	var $_defaultForm = array(
		"bon_consum_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"data_doc" => array("type"=>"text", "label"=>"Data", "attributes" => array("class" => "calendar")),
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
		$s -> getByGestiuneAndTip($gestiune_id, 'bonuri_consum');
		if(count($s))
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
			$cnt -> produs -> scadStoc($cnt -> cantitate, $this -> gestiune_id, $cnt -> id, 'BonuriConsumIesiri');			
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
			$iesiri = new BonuriConsumIesiri("where comp_id = '".$cnt -> id."'");
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
		$sql = "DELETE FROM bonuri_consum_continut WHERE bon_consum_id = '". $this -> id ."'";
		$db -> query($sql);
		$this -> delete();
	}
	
	function anulare() {
		$this -> anulareScaderi();
		$this -> anulat = 'DA';
		$this -> save();
	}
	
	function sumar() {
		$out = '
		<fieldset>
		<legend>Bon Consum Nr: '. $this -> numar_doc .'</legend>	
		Data: '. c_data($this -> data_doc) .'<br>
		Intocmit: '. $this -> utilizator -> nume .'<br>
		Gestiune: '. $this -> gestiune -> denumire .'
		</fieldset>
		';
		return $out;
	}
}
?>