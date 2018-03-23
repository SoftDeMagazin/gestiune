<?php
class RptIncasari extends Rpt{
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function genereazaRaport() {
		switch($this -> filtre['grupare_dupa']) {
			case "client": {
				$this -> genereazaRaportClient();
			}break;
			case "moduri_plata": {
				$this -> genereazaRaportModuri();
			}break;
		}
	}
	
	function getAntet() {
		$out .= '<h2 align="center">RAPORT INCASARI</h2>';
		$out .= '<div align="center">'. $this -> filtre['from'] .' - '. $this -> filtre['end'] .'</div>';
		return $out;
	}
	
	function getConditions() {
		$f = $this -> filtre;
		if($f['from'] && $f['end']) {
			$conditions .= " and data_doc between '". data_c($f['from']) ."' and '". data_c($f['end']) ."'";
		}
		if($f['tert_id']) {
			$in = "'".implode("','", $f['tert_id'])."'";
			$conditions .= " and tert_id in (". $in .")";
		}
		if($f['gestiune_id']) {
			$in = "'".implode("','", $f['gestiune_id'])."'";
			$conditions .= " and incasari.gestiune_id in (". $in .")";
		}
		return $conditions;
	}
	
	function genereazaRaportModuri() {
		$conditions = $this -> getConditions();
		$sql = "
			select
				societati.denumire as societate,
				gestiuni.denumire as gestiune,
				concat(terti.denumire, ' - ', terti.cod_fiscal) as tert,
				modalitati_plata.descriere as mod_plata,
				sum(suma) as suma
			from
				incasari
			inner join modalitati_plata
				using(mod_plata_id)	
			inner join terti
				using(tert_id)	
			inner join gestiuni
				using(gestiune_id)
			inner join societati
				using(societate_id)	
			where 1
				$conditions		
			group by 			
				societate,	
				gestiune,
				tert,
				mod_plata	
			with rollup
		";
		//echo $sql;
		$this -> loadData($sql);
	}
	
	function genereazaRaportClient() {
		$conditions = $this -> getConditions();
		$sql = "
			select
				societati.denumire as societate,
				gestiuni.denumire as gestiune,
				concat(terti.denumire, ' - ', terti.cod_fiscal) as tert,
				sum(suma) as suma
			from
				incasari
			inner join terti
				using(tert_id)	
			inner join gestiuni
				using(gestiune_id)
			inner join societati
				using(societate_id)	
			where 1
				$conditions		
			group by 			
				societate,	
				gestiune,
				tert	
			with rollup
		";
		//echo $sql;
		$this -> loadData($sql);
	}
	
	function getHtml() {
		switch($this -> filtre['grupare_dupa']) {
			case "client": {
				return $this -> getHtmlClient();
			}break;
			case "moduri_plata": {
				return $this -> getHtmlModuri();
			}break;
		}
	}
	
	function getHtmlModuri() {
		$this -> genereazaRaport();
		$nr_r = count($this -> data);
		$out = $this -> getAntet();
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "0", "id" => "rpt_iesiri", "class" => "tablesorter"));
		$dg -> addHeadColumn("Client - Cod Fiscal", array("width" => "50%"));
		$dg -> addHeadColumn("Mod Plata", array("width" => "25%"));
		$dg -> addHeadColumn("Suma Incasata", array("width" => "25%"));
		$gestiune = "";
		$societate = "";
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($societate != $row['societate'] && $row['societate']) {
				$out .= "<h3>". $row['societate'] ."</h3>";
				$societate = $row['societate'];
			}
			if($row['mod_plata'] && $row['tert'] && $row['societate'] && $row['gestiune']) {
				$dg -> addColumn($row['tert']);
				$dg -> addColumn($row['mod_plata']);
				$dg -> addColumn($row['suma'], array("style" => "text-align:right"));
				$dg -> index(); 
			} else {
				if($row['tert'] && $row['societate'] && $row['gestiune']) {
						$dg -> addColumn("&nbsp;");
						$dg -> addColumn("<strong>Total Client</strong>");
						$dg -> addColumn($row['suma'], array("style" => "text-align:right"));
						$dg -> index();
				} else {
					if($row['societate'] && $row['gestiune']) {
						$dg -> addColumn("<strong>Total Gestiune</strong>");
						$dg -> addColumn("&nbsp;");
						$dg -> addColumn($row['suma'], array("style" => "text-align:right"));
						$out .= "<h4>".$row['gestiune']."</h4>";
						$out .= $dg -> getDataGrid();
						$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "0", "id" => "rpt_iesiri", "class" => "tablesorter"));
						$dg -> addHeadColumn("Client - Cod Fiscal", array("width" => "50%"));
						$dg -> addHeadColumn("Mod Plata", array("width" => "25%"));
						$dg -> addHeadColumn("Suma Incasata", array("width" => "25%"));
					}
					else {
						if($row['societate']) {
							$out .= "<br>Total ". $row['societate'] ." ".$row['suma']."</br>";
						}
						else {
							$out .= "<br><br><strong>Total Incasari ".$row['suma']."</strong><br>";
						}
					}
				}	
			}
									
		}
		//$out .= $dg -> getDataGrid();
		return $out;
	}
	
	function getHtmlClient() {
		$this -> genereazaRaport();
		$nr_r = count($this -> data);
		$out = $this -> getAntet();
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "0", "id" => "rpt_iesiri", "class" => "tablesorter"));
		$dg -> addHeadColumn("Client - Cod Fiscal", array("width" => "50%"));
		$dg -> addHeadColumn("Suma Incasata", array("width" => "50%"));
		$gestiune = "";
		$societate = "";
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($societate != $row['societate'] && $row['societate']) {
				$out .= "<h3>". $row['societate'] ."</h3>";
				$societate = $row['societate'];
			}
			if($row['tert'] && $row['societate'] && $row['gestiune']) {
				$dg -> addColumn($row['tert']);
				$dg -> addColumn($row['suma'], array("style" => "text-align:right"));
				$dg -> index(); 
			} else {
				if($row['societate'] && $row['gestiune']) {
					$dg -> addColumn("Total Gestiune");
					$dg -> addColumn($row['suma'], array("style" => "text-align:right"));
					$out .= "<h4>".$row['gestiune']."</h4>";
					$out .= $dg -> getDataGrid();
					$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "0", "id" => "rpt_iesiri", "class" => "tablesorter"));
					$dg -> addHeadColumn("Client - Cod Fiscal", array("width" => "50%"));
					$dg -> addHeadColumn("Suma Incasata", array("width" => "50%"));
				}
				else {
					if($row['societate']) {
						$out .= "<br>Total ". $row['societate'] ." ".$row['suma']."</br>";
					}
					else {
						$out .= "<br><br><strong>Total Incasari ".$row['suma']."</strong><br>";
					}
				}
			}
									
		}
		//$out .= $dg -> getDataGrid();
		return $out;
	}
}
?>