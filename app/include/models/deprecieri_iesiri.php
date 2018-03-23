<?php
class DeprecieriIesiri extends Model
{
	var $tbl="deprecieri_iesiri";
	var $_relations = array(
		"lot" => array("type" => "one", "model" => "Loturi", "key" => "lot_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		);
	var $_defaultForm = array(
		);
	
		function listaPrint()
	{
		$dg = new DataGrid(array("style" => "width:100%;margin:10px auto;" , "border" => "1", "id" => "tbl_". $this -> tbl ."", "cellpadding" => "0", "cellspacing" => "0"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs", array("width" => "50%"));
			$dg -> addHeadColumn("UM", array("width" => "10%"));
			$dg -> addHeadColumn("Cant", array("width" => "10%"));
			$dg -> addHeadColumn("Pret ach", array("width" => "15%"));
			$dg -> addHeadColumn("Valoare ach", array("width" => "15%"));
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire, array("width" => "50%"));
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire, array("width" => "10%", "style" => "text-align:center"));
				$dg -> addColumn(treizecimale($this -> cantitate), array("width" => "10%", "style" => "text-align:right"));
				$pret_intrare = $this -> lot -> pret_intrare_ron;
				$dg -> addColumn(treizecimale($pret_intrare), array("width" => "15%", "style" => "text-align:right"));
				$val = douazecimale($this -> cantitate * $pret_intrare);
				$dg -> addColumn($val, array("width" => "15%", "style" => "text-align:right"));
				$total += $val;
				$dg -> index();
				}
				$dg -> addColumn("Total", array("colspan" => "4", "width" => "85%"));
				$dg -> addColumn(douazecimale($total), array("width" => "15%", "style" => "text-align:right"));
					}
		$out .= $dg -> getDataGrid();
		return $out;	
	}	
}
?>