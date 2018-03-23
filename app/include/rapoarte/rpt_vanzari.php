<?php
class RptVanzari extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		if($this -> filtre['from'] && $this -> filtre['end']) {
			$sql .= "and data_economica ". $db -> between(data_c($this -> filtre['from']), data_c($this -> filtre['end']));
		}
		
		if($this -> filtre['pos_id']) {
			$sql .= " and pos_id ". $db -> inArray($this -> filtre['pos_id']);
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and gestiune_id ". $db -> inArray($this -> filtre['gestiune_id']);
		}
		
		if($this -> filtre['categorie_id']) {
			$sql .= "and categorii.categorie_id ". $db -> inArray($this -> filtre['categorie_id']);
		}
		
		if($this -> filtre['tip_produs']) {
			$sql .= "and produse.tip_produs ". $db -> inArray($this -> filtre['tip_produs']);
		}
		
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
		select 
			produse.denumire as produs,
			categorii.denumire as categorie,
			sum(cantitate) as cantitate, 
			round(sum(cantitate*pret_vanzare),2) as valoare 
		from 
			vanzari_pos_continut
		inner join 
			vanzari_pos 
			using(vp_id)
		inner join 
			posuri
			using(pos_id)
		inner join 
			produse
			using(produs_id)
		inner join
			categorii
			using(categorie_id)
		where 1
			". $conditions ."
		group by
			produse.denumire,
			categorii.denumire
		order by 
			categorii.denumire asc, produse.denumire asc
		";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">VANZARI POS</h2>';
		$out .= '<div align="center">'. c_data($this -> filtre['from']) .' - '. c_data($this -> filtre['end']) .'</div>';
	
		$out .= '<div> Gestiuni: ';
		if($this -> filtre['gestiune_id']) {
			foreach($this -> filtre['gestiune_id'] as $gestiune_id) {
				$gest = new Gestiuni($gestiune_id);
				$out .= $gest -> denumire.", ";
			}
		} else {
			$out .= 'Toate';
		}
		$out .= '</div>';
		
		$out .= '<div> Posuri: ';
		if($this -> filtre['pos_id']) {
			foreach($this -> filtre['pos_id'] as $id) {
				$pos = new Posuri("where `pos_id` = '$id'");
				$out .= $pos -> cod.", ";
			}
		} else {
			$out .= 'Toate';
		}
		$out .= '</div>';
		
		$out .= '<div> Tipuri Produse: ';
		if($this -> filtre['tip_produs']) {
			foreach($this -> filtre['tip_produs'] as $tip) {
				$tip_produs = new TipuriProduse("where `tip` = '$tip'");
				$out .= $tip_produs -> descriere.", ";
			}
		} else {
			$out .= 'Toate';
		}
		$out .= '</div>';
		
		return $out;
	}
	
	function getHtml() {
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Valoare Vanzare Cu TVA");
		$cat = "";
		$nr_r = count($this -> data);
		$total_categorie = 0;
		$total_general = 0;
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($cat != $row['categorie']) {
				$out .= Html::h3($row['categorie']);
				$cat = $row['categorie'];
				$total_categorie = 0;
			}
			
			$dg -> addColumn($row['produs']);
			$dg -> addColumn($row['cantitate'], array('style' => 'text-align:right'));
			$dg -> addColumn($row['valoare'], array('style' => 'text-align:right'));
			$dg -> index();
			$total_categorie += $row['valoare'];
			$total_general += $row['valoare'];
			if($cat != $this -> data[$i+1]['categorie']) {
				$dg -> addColumn("Total ".$cat, array("colspan" => "2"));
				$dg -> addColumn(douazecimale($total_categorie), array('style' => 'text-align:right'));
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}
		}
		$out .= Html::h3("TOTAL GENERAL: ".douazecimale($total_general));
		return $out;
	}
}
?>