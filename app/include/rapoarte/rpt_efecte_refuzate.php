<?php
class RptEfecteRefuzate extends Rpt {

	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function getConditions() {
		$f = $this -> filtre;
		if($f['from'] && $f['end']) {
			$conditions .= " and data_emitere between '". data_c($f['from']) ."' and '". data_c($f['end']) ."'";
		}
		if($f['gestiune_id']) {
			$in = "'".implode("','", $f['gestiune_id'])."'";
			$conditions .= " and incasari_efecte.gestiune_id in (". $in .")";
		}		
		
		if($f['tert_id']) {
			$in = "'".implode("','", $f['tert_id'])."'";
			$conditions .= " and incasari_efecte.tert_id in (". $in .")";
		}		
		return $conditions;
	}
	function getAntet() {
		$out .= '<h2 align="center">EFECTE DE COMERT REFUZATE</h2>';
		$out .= '<div align="center">'. $this -> filtre['from'] .' - '. $this -> filtre['end'] .'</div>';
		return $out;
	}
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "	
			select 
				societati.denumire as societate,
				gestiuni.denumire as gestiune,
				concat(terti.denumire, ' - ', terti.cod_fiscal) as tert, 
				numar_doc,
				data_emitere,
				data_scadenta,
				suma
			from
				incasari_efecte
			inner join terti
				using(tert_id)
			inner join societati	
				using(societate_id)
			inner join gestiuni
				using(gestiune_id)	
			where 1
				$conditions	
				and operat = 'DA'
				and raspuns in ('NA')
			order by
				societate, gestiune, data_emitere asc	

	

		";
		$this -> loadData($sql);
	}
	
	function getHtml() {
		$this -> genereazaRaport();
		$nr_r = count($this -> data);		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => ""));
		$dg -> addHeadColumn("Client");
		$dg -> addHeadColumn("Nr. Doc");
		$dg -> addHeadColumn("Data Emitere");
		$dg -> addHeadColumn("Data Scadenta");
		$dg -> addHeadColumn("Suma");
		$societate = "";
		$gestiune = "";
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			
			if($societate != $row['societate'] && $row['societate']) {
				$out .= "<h2>". $row['societate'] ."</h2>";
				$societate = $row['societate'];
			}
			if($gestiune != $row['gestiune'] && $row['gestiune']) {
				$out .= "<h3>". $row['gestiune'] ."</h3>";
				$gestiune = $row['gestiune'];
				$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => "tablesorter"));
				$dg -> addHeadColumn("Client");
				$dg -> addHeadColumn("Nr. Doc");
				$dg -> addHeadColumn("Data Emitere");
				$dg -> addHeadColumn("Data Scadenta");
				$dg -> addHeadColumn("Suma");
			}
			
			$dg -> addColumn($row['tert']);
			$dg -> addColumn($row['numar_doc'],array("style" => "text-align:left;"));
			$dg -> addColumn(c_data($row['data_emitere']));
			$dg -> addColumn(c_data($row['data_scadenta']));
			$dg -> addColumn($row['suma']);
			switch($row['raspuns']) {
				case "OK": {
					$dg -> addColumn('accept total');
				}break;	
				case "PA": {
					$dg -> addColumn('acceptat partial');
				}break;	
			}
			$dg -> index();
			$total_emis += $row['suma'];
			
			if($row['gestiune'] != $this -> data[$i+1]['gestiune']) {
				$dg -> addColumn("Total");
				$dg -> addColumn("");
				$dg -> addColumn("");
				$dg -> addColumn("");
				$dg -> addColumn($total_emis);
				$out .= $dg -> getDataGrid();
				$total_emis = 0;
				$total_acceptat = 0;
			}
		}
		return $out;
	}
}
?>