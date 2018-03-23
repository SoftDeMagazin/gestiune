<?php
class ViewFurnizori extends Model
{
	var $tbl="view_furnizori";
	var $key="tert_id";
	var $_relations = array(
			"societate" => array("type"=>"one", "model" => "Societati", "key"=>"societate_id", "value" => "denumire"),
		);
	var $_defaultForm = array(
		);
		
	function cautare($str) {
		$this -> fromString("WHERE `denumire` LIKE '$str%' or `cod_fiscal` LIKE '$str%'");
		return count($this);
	}
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire");
		$dg -> addHeadColumn("Reg Com");
		$dg -> addHeadColumn("Cod fiscal");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> denumire);
			$dg -> addColumn($this -> reg_com);
			$dg -> addColumn($this -> cod_fiscal);
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
	
	
	function listaSituatiiFinanciarePlati($click="", $dblClick="" , $selected=0, $sold)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Denumire Client");
		$dg -> addHeadColumn("Cod Fiscal");
		$dg -> addHeadColumn("Societate");
		$dg -> addHeadColumn("De Plata");
		$dg -> addHeadColumn("Sold Acoperit");
		$dg -> addHeadColumn("Sold Descoperit");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
		{
			$this -> fromDataSource($i);
			if($sold) {
				if($this -> soldPlati() != 0) {
					$dg -> addColumn($this -> denumire);
					$dg -> addColumn($this -> cod_fiscal);
					if($this -> societate_id) {
						$dg -> addColumn($this -> societate -> denumire);
					}
					else {
						$dg -> addColumn("");
					}
					$dg -> addColumn(money($this -> soldPlati(), $this -> valuta));
					$dg -> addColumn(money($this -> situatieEfecte(), $this -> valuta));
					$dg -> addColumn(money($this -> soldPlati() - $this -> situatieEfecte(), $this -> valuta));
					
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
			}
			else {
				$dg -> addColumn($this -> denumire);
				$dg -> addColumn($this -> cod_fiscal);
				if($this -> societate_id) {
					$dg -> addColumn($this -> societate -> denumire);
				}
				else {
					$dg -> addColumn("");
				}
				$dg -> addColumn(money($this -> soldPlati(), $this -> valuta));
				$dg -> addColumn(money($this -> situatieEfecte(), $this -> valuta));
				$dg -> addColumn(money($this -> soldPlati() - $this -> situatieEfecte(), $this -> valuta));
				
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
		}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	

	function situatiePlati() {
		// se transforma in view
		return douazecimale($this -> platit_total);
	}
	
	function situatieFacturiEmise() {
		switch($this -> tip) {
			case "intern": {
				return douazecimale($this -> total_ron_cu_tva);
			}break;
			case "extern_ue": {
				return douazecimale($this -> total_val_cu_tva);
			}break;
		}
	}
	
	function soldPlati() {
		return douazecimale($this -> situatieFacturiEmise() - $this -> situatiePlati());
	}
	
	function situatieEfecte() {
		return douazecimale($this -> total_efecte);
	}
		
}
?>