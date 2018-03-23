<?php
class VanzariPos extends Model
{
	var $tbl = "vanzari_pos";
	var $_relations = array(
		"pos" => array("type"=>"one", "model"=>"Posuri", "key"=>"pos_id", "value" => "cod"),
		"continut" => array("type" => "many", "model" => "VanzariPosContinut", "key" => "vp_id")
	);

	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Data Economica");
		$dg -> addHeadColumn("Pos");
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Validat");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> data_economica);
			$dg -> addColumn($this -> pos -> cod);
			$dg -> addColumn($this -> pos -> gestiune -> denumire);
			$dg -> addColumn($this -> validat);
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
	
	function scadStoc() {
		$continut = $this -> continut;
		if(!count($continut)) return false;
		
		$pos = $this -> pos;
		
		$errs = array();
		foreach($continut as $cnt) {
			if(count($cnt -> produs)) {
				$cnt -> produs -> scadStoc($cnt -> cantitate, $pos -> gestiune_id, $cnt -> id, 'VanzariPosContinutIesiri');			
			}
			else {
				$errs[] = $cnt -> produs_id;
			}
		}
		
		return $errs;
	}
	
		/**
	 * anuleaza scaderile efectue
	 * @return bool;
	 */
	function anulareScaderi() {
		$continut = $this -> continut;
		if(!count($continut)) return false;
		
		foreach($continut as $cnt) {
			$iesiri = new VanzariPosContinutIesiri("where comp_id = '".$cnt -> id."'");
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
	
	function __get($name) {
		if($name == "numar_doc") {
			return $this -> data_economica;
		}
		
		return parent::__get($name); 
	}
}
?>