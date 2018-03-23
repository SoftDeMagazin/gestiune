<?php
class Loturi extends Model
{
	var $tbl="loturi";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);
		
	function lista($click="", $dblClick="", $selected="") {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Data intrare");
		$dg -> addHeadColumn("Cantitate initiala");
		$dg -> addHeadColumn("Cantitate ramasa");
		$dg -> addHeadColumn("Pret intrare lei");
		$dg -> addHeadColumn("Pret intrare val");
		
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn(c_data($this -> data_intrare));
			$dg -> addColumn($this -> cantitate_init);
			$dg -> addColumn($this -> cantitate_ramasa);
			$dg -> addColumn($this -> pret_intrare_ron);
			$dg -> addColumn($this -> pret_intrare_val);
			
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
	
	function infoLoturi() {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0"));
		$nr_r = count($this);
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Cant");
		$dg -> addHeadColumn("Pret lei");
		$dg -> addHeadColumn("Pret val.");
		for($i=0;$i<$nr_r;$i++) {
			$this -> fromDataSource($i);
			$dg -> addColumn(c_data($this -> data_intrare));
			$dg -> addColumn($this -> cantitate_ramasa, array("align" => "right"));
			$dg -> addColumn($this -> pret_intrare_ron, array("align" => "right"));
			$dg -> addColumn($this -> pret_intrare_val, array("align" => "right"));
			$dg -> index();
		}	
		
		return $dg -> getDataGrid();
	}		
	
	/**
	 * afiseaza tabel vezi stocuri/evidenta_loturi/ evidenta loturi
	 * @param object $click [optional]
	 * @param object $dblClick [optional]
	 * @param object $selected [optional]
	 * @return 
	 */
	function evidentaLoturi($click="", $dblClick="", $selected="") {
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Data intrare");
		$dg -> addHeadColumn("Furnizor");
		$dg -> addHeadColumn("Document");
		$dg -> addHeadColumn("Numar Document");
		$dg -> addHeadColumn("Cantitate initiala");
		$dg -> addHeadColumn("Cantitate ramasa");
		$dg -> addHeadColumn("Pret intrare lei");
		$dg -> addHeadColumn("Pret intrare val");
		
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn(c_data($this -> data_intrare));
			if(get_class($this -> doc) != "Inventare")
				$dg -> addColumn($this -> doc -> tert -> denumire);
			else 
				$dg -> addColumn($this -> doc -> gestiune -> denumire);
			$dg -> addColumn($this -> doc_tip);
			$dg -> addColumn($this -> doc -> numar_doc);
			$dg -> addColumn($this -> cantitate_init);
			$dg -> addColumn($this -> cantitate_ramasa);
			$dg -> addColumn($this -> pret_intrare_ron);
			$dg -> addColumn($this -> pret_intrare_val);
			
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
	
	/**
	 * incarca loturile cu cantitate_ramasa > 0
	 * @param object $produs_id produs
	 * @param object $gestiune_id gestiune
	 * @return 
	 */
	function getLoturiActive($produs_id, $gestiune_id) {
		$sql  = " where produs_id = '$produs_id'";
		$sql .= " and gestiune_id = '$gestiune_id'";
		$sql .= " and cantitate_ramasa > 0";
		$sql .= " order by data_intrare asc, lot_id asc";
		$this -> fromString($sql);
	}
	
	/**
	 * incarca loturile negative pentru produs_id din gestiune_id
	 * @param object $produs_id
	 * @param object $gestiune_id
	 * @return 
	 */
	function getLoturiNegative($produs_id, $gestiune_id) {
		$sql  = " where produs_id = '$produs_id'";
		$sql .= " and gestiune_id = '$gestiune_id'";
		$sql .= " and cantitate_ramasa < 0";
		$sql .= " order by data_intrare asc, lot_id asc";
		$this -> fromString($sql);
	}
	
	/**
	 * incarca ultimul lot pentru produs_id din gestiune_id
	 * @param object $produs_id
	 * @param object $gestiune_id
	 * @return 
	 */
	function ultimulLot($produs_id, $gestiune_id) {
		$sql  = " where produs_id = '$produs_id'";
		$sql .= " and gestiune_id = '$gestiune_id'";
		$sql .= " order by data_intrare desc, lot_id desc limit 0, 1";
		$this -> fromString($sql);
	}	
	
	function __get($name) {
		
		if($name == "doc") {
			switch($this -> doc_tip) {
				case "nir": {
					$nir = new Niruri($this -> doc_id);
					return $nir;
				}
				case "factura_retur": {
					$factura = new Facturi($this -> doc_id);
					return $factura;
				} 
				
				case "inventar": {
					$inventar = new Inventare($this -> doc_id);
					return $inventar;
				}
			}
		}
		
		return parent::__get($name);
	}
}
?>