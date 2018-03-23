<?php
class RptPlati extends Rpt{
	
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
			default: {
				$this -> genereazaRaportDefault();
			}break;
		}
	}
	
	function getAntet() {
		$out .= '<h2 align="center">RAPORT PLATI</h2>';
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
			$conditions .= " and plati.gestiune_id in (". $in .")";
		}
		if($f['mod_plata_id']) {
			$in = "'".implode("','", $f['mod_plata_id'])."'";
			$conditions .= " and plati.mod_plata_id in (". $in .")";
		}
		
		return $conditions;
	}
	
	function genereazaRaportDefault() {
		$conditions = $this -> getConditions();
		$sql = "
			select
				societati.denumire as societate,
				gestiuni.denumire as gestiune,
				concat(terti.denumire, ' - ', terti.cod_fiscal) as tert,
				modalitati_plata.descriere as mod_plata,
				suma,
				data_doc,
				numar_doc
			from
				plati
			inner join modalitati_plata
				using(mod_plata_id)	
			inner join terti
				using(tert_id)	
			inner join gestiuni
				using(gestiune_id)
			inner join societati on
				plati.societate_id = societati.societate_id
			where 1
				$conditions	
				and modalitati_plata.descriere not in ('FACTURA RETUR', 'FACTURA DISCOUNT')	
			order by societate, gestiune, tert, data_doc asc	
		";
		$this -> loadData($sql);
	}
	
	function getHtmlDefault() {
		$this -> genereazaRaport();
		$nr_r = count($this -> data);
		$out = $this -> getAntet();
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => ""));
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Suma");
		$dg -> addHeadColumn("Numar Document");
		$dg -> addHeadColumn("Mod Plata");
		$dg -> addHeadColumn("Client");
		$gestiune = "";
		$societate = "";
		$total_gest = 0;
		$total_soc = 0;
		$total_incasari = 0;
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($societate != $row['societate'] && $row['societate']) {
				$out .= "<h2>". $row['societate'] ."</h2>";
				$societate = $row['societate'];
			}
			if($gestiune != $row['gestiune'] && $row['gestiune']) {
				$out .= "<h3>". $row['gestiune'] ."</h3>";
				$gestiune = $row['gestiune'];
				$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => ""));
				$dg -> addHeadColumn("Data", array("width"=> "10%", "style"=>"text-align:left"));
				$dg -> addHeadColumn("Suma", array("width"=> "20%", "style"=>"text-align:left"));
				$dg -> addHeadColumn("Nr. Doc.", array("width"=> "15%", "style"=>"text-align:left"));
				$dg -> addHeadColumn("Mod Plata", array("width"=> "20%", "style"=>"text-align:left"));
				$dg -> addHeadColumn("Client", array("width"=> "45%", "style"=>"text-align:left"));
			}
			
			$dg -> addColumn(c_data($row['data_doc']));
			$dg -> addColumn($row['suma'], array("style"=>"text-align:right"));
			$dg -> addColumn($row['numar_doc'], array("style"=>"text-align:right"));
			$dg -> addColumn($row['mod_plata']);
			$dg -> addColumn($row['tert']);
			$dg -> index();
			$total_incasari += $row['suma'];
			if($row['gestiune'] != $this -> data[$i+1]['gestiune']) {
				$dg -> addColumn("Total");
				$dg -> addColumn(douazecimale($total_incasari), array("style"=>"text-align:right"));
				$dg -> addColumn("&nbsp;", array("style"=>"text-align:right"));
				$dg -> addColumn("&nbsp;");
				$dg -> addColumn("&nbsp;");
				$out .= $dg -> getDataGrid();
				$total_incasari = 0;
			}
		}	
			return $out;
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
			default : {
				return $this -> getHtmlDefault();
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