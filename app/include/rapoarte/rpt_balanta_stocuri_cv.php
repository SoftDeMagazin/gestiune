<?php
class RptBalantaStocuriCv extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and p.gestiune_id = '". $this -> filtre['gestiune_id'] ."'";
		}
		
		if($this -> filtre['categorie_id']) {
			$sql .= "and p.categorie_id ". $db -> inArray($this -> filtre['categorie_id']);
		}
		
		if($this -> filtre['tip_produs']) {
			$sql .= "and p.tip_produs ". $db -> inArray($this -> filtre['tip_produs']);
		} else {
			$sql .= " and p.tip_produs in ('mp', 'marfa') ";
		}
		
		return $sql;
	}
	
	function genereazaRaport() {
	
		$from = data_c($this -> filtre['from']);
		$end = data_c($this -> filtre['end']);
		
		$ieri = strtotime ( '-1 day' , strtotime ($from) ) ;
		$ieri = date ( 'Y-m-d' , $ieri );
		
		$conditions = $this -> getConditions();
		
		$sql = "
		SELECT
		  p.denumire,
		  p.categorie_id,
		  ROUND(stoc_la_data(p.produs_id, '".$this -> filtre['gestiune_id']."', '$ieri'),3) AS stoc_initial,
		  ROUND(intrari_in_perioada(p.produs_id, '".$this -> filtre['gestiune_id']."', '$from', '$end'), 3) AS intrari,
		  ROUND(iesiri_in_perioada(p.produs_id, '".$this -> filtre['gestiune_id']."', '$from', '$end'), 3) AS iesiri,
		  ROUND(stoc_la_data_valoric(p.produs_id, '".$this -> filtre['gestiune_id']."', '$ieri'),3) AS sold_initial,
		  ROUND(intrari_in_perioada_valoric(p.produs_id, '".$this -> filtre['gestiune_id']."', '$from', '$end'), 3) AS val_intrari,
		  ROUND(iesiri_in_perioada_valoric(p.produs_id, '".$this -> filtre['gestiune_id']."', '$from', '$end'), 3) AS val_iesiri
		FROM view_produse_gestiuni p
		WHERE 1
			".$conditions."
		order by categorie_id	
			";	
		
		$this -> loadData($sql);
	}
	function getAntet() {
		$out .= '<h2 align="center">Balanta Stocuri Valorica</h2>';
		$out .= '<div align="center">'. c_data($this -> filtre['from']) .' - '. c_data($this -> filtre['end']) .'</div>';
	
		$out .= '<div> Gestiune: ';
		$gest = new Gestiuni($this -> filtre['gestiune_id']);
		$out .= $gest -> denumire;
		$out .= '</div>';
		
		
		return $out;
	}
	function getHtml() {
		$this -> genereazaRaport();
		$out .= $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$cat = 0;
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Stoc Initial");
		$dg -> addHeadColumn("Sold Initial");
		$dg -> addHeadColumn("Intrari");
		$dg -> addHeadColumn("Val Intrari");
		$dg -> addHeadColumn("Iesiri");
		$dg -> addHeadColumn("Val Iesiri");
		$dg -> addHeadColumn("Stoc Final");
		$dg -> addHeadColumn("Sold Final");
		$nr_r = count($this  -> data);
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($cat != $row['categorie_id']) {
				$categorie = new Categorii($row['categorie_id']);
				$out .= Html::h3($categorie -> denumire);
				$cat = $row['categorie_id'];
			}
			
			$stoc_final = $row['stoc_initial'] + $row['intrari'] - $row['iesiri'];
			$sold_final = $row['sold_initial'] + $row['val_intrari'] - $row['val_iesiri'];
			
			$dg -> addColumn($row['denumire']);
			$dg -> addColumn($row['stoc_initial'], array( "align" => "center"));
			$dg -> addColumn($row['sold_initial'], array("align" => "center"));
			$dg -> addColumn($row['intrari'], array( "align" => "center"));
			$dg -> addColumn($row['val_intrari'], array( "align" => "center"));
			$dg -> addColumn($row['iesiri'], array("align" => "center"));
			$dg -> addColumn($row['val_iesiri'], array( "align" => "center"));
			$dg -> addColumn(treizecimale($stoc_final), array( "align" => "center"));
			$dg -> addColumn(treizecimale($sold_final), array( "align" => "center"));
			$dg -> index();
			if($cat != $this -> data[$i+1]['categorie_id']) {
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}
		}
		return $out;
	}
}
?>