<?php
    class VanzariPosContinut extends Model
	{
    	var $tbl = "vanzari_pos_continut";	
		
		var $_relations = array(
			"pos" => array("type"=>"one", "model"=>"Posuri", "key"=>"pos_id"),
			"produs" => array("type"=>"one", "model"=>"Produse", "key"=>"produs_id"),
		);
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Pret");
		$dg -> addHeadColumn("Valoare");
		
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			if(count($this -> produs)) $dg -> addColumn($this -> produs -> denumire);
			else  $dg -> addColumn("ERR:".$this -> produs_id);
			$dg -> addColumn($this -> cantitate);
			$dg -> addColumn($this -> pret_vanzare);
			$dg -> addColumn(douazecimale($this -> cantitate * $this -> pret_vanzare));
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
	
	function listaPreturiAchizitie($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Pret Vanzare");
		$dg -> addHeadColumn("Valoare Vanzare");
		$dg -> addHeadColumn("Pret Achizitie");
		$dg -> addHeadColumn("Valoare Achizitie");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			if(count($this -> produs)) $dg -> addColumn($this -> produs -> denumire);
			else  $dg -> addColumn("ERR:".$this -> produs_id);
			$dg -> addColumn($this -> cantitate);
			$dg -> addColumn($this -> pret_vanzare);
			$dg -> addColumn(douazecimale($this -> cantitate * $this -> pret_vanzare));
			$pret = $this -> getPretAch();
			$dg -> addColumn($pret);
			$dg -> addColumn($pret * $this -> cantitate);
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
	
	function getPretAch() {
		$iesiri = new VanzariPosContinutIesiri("where comp_id = '". $this -> id ."'");
		$val = 0;
		foreach($iesiri as $iesire) {
			$val += $iesire -> cantitate * $iesire -> lot -> pret_intrare_ron;
		}
		
		$pret = $val / $this -> cantitate;
		return douazecimale($pret);
	}
}
?>