<?php
class RptRulajFurnizori extends Rpt {

	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function getConditions() {
		$f = $this -> filtre;
		if($f['from'] && $f['end']) {
			$conditions .= " and data_factura between '". data_c($f['from']) ."' and '". data_c($f['end']) ."'";
		}
		if($f['gestiune_id']) {
			$in = "'".implode("','", $f['gestiune_id'])."'";
			$conditions .= " and view_facturi_intrari.gestiune_id in (". $in .")";
		}		
		return $conditions;
	}
	function getAntet() {
		$out .= '<h2 align="center">RAPORT RULAJ FURNIZORI</h2>';
		$out .= '<div align="center">'. $this -> filtre['from'] .' - '. $this -> filtre['end'] .'</div>';
		return $out;
	}
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
			select 
				concat(terti.denumire, ' - ', terti.cod_fiscal) as tert, 
				sum(total_ron_cu_tva) as total_ron,
				sum(total_val_cu_tva) as total_val
			from
				view_facturi_intrari
			inner join terti
				using(tert_id)
			where 1
				$conditions	
			group by
				tert
			with rollup
		";
		$this -> loadData($sql);
	}
	
	function getHtml() {
		$this -> genereazaRaport();
		$nr_r = count($this -> data);
		$total_ron = $this -> data[$nr_r-1]['total_ron'];
		$total_val = $this -> data[$nr_r-1]['total_val'];
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" ,"border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => ""));
		$dg -> addHeadColumn("Client");
		$dg -> addHeadColumn("Total Facturat LEI");
		$dg -> addHeadColumn("Facturat %");
		$dg -> addHeadColumn("Total Facturat EUR");
		$dg -> addHeadColumn("Facturat %");
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			$dg -> addColumn($row['tert']);
			$dg -> addColumn($row['total_ron'],array("style" => "text-align:right;"));
			$dg -> addColumn(douazecimale(($row['total_ron']/$total_ron)*100)."%",array("style" => "text-align:right;"));
			$dg -> addColumn($row['total_val'],array("style" => "text-align:right;"));
			$dg -> addColumn(douazecimale(($row['total_val']/$total_val)*100)."%",array("style" => "text-align:right;"));
			$dg -> index();
		}
		$out .= $dg -> getDataGrid();
		return $out;
	}
}
?>