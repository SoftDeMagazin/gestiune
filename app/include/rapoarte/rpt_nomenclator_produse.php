<?php
class RptNomenclatorProduse extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function getConditions() {
		if($this -> filtre['gestiune_id']) {
			$sql .= "and view_produse_gestiuni.gestiune_id = '".$this -> filtre['gestiune_id']."'";
		}
		if($this -> filtre['categorie_id']) {
			$in = implode(",", $this -> filtre['categorie_id']);
			$sql .= " and categorie_id in (". $in .")";
		}
		
		if($this -> filtre['tip_produs']) {
			$in = "'".implode("','", $this -> filtre['tip_produs'])."'";
			$sql .= " and tip_produs in (". $in .")";
		}
		return $sql;
	}
	
	function getAntet() {
		$out .= '<h2 align="center">NOMENCLATOR PRODUSE</h2>';
		$out .= '<div align="center">'. date("d.m.Y H:i:s") .'</div>';
		$gestiune = new Gestiuni($this -> filtre['gestiune_id']);
		$out .= '<div align="left">Gestiune: '. $gestiune -> denumire .'</div>';
		
		return $out;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
			
		$sql = "
			select 
				view_produse_gestiuni.denumire,
				view_produse_gestiuni.produs_id,
				view_produse_gestiuni.tip_produs,
				unitati_masura.denumire as um,
				categorii.denumire as denumire_categorie,
				view_produse_gestiuni.pret_ron
			from 
				view_produse_gestiuni
			inner join 
				unitati_masura
				using(unitate_masura_id)	
			inner join 
				categorii
				using(categorie_id)
			where 1
			". $conditions ."
			order by denumire_categorie asc, denumire asc
		";
		$this -> loadData($sql);
	}
	
	function getHtml() {
		$out = $this -> getAntet();
		$this -> genereazaRaport();
		$cat = "";
		$nr_r = count($this -> data);

		$dg = new DataGrid(array("border" => "1"));
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			
			if($cat != $row['denumire_categorie']) {
				$out .= "<h3>". $row['denumire_categorie'] ."</h3>";
				
				$dg = new DataGrid(array("border" => "1", "width" => "98%", "align" => "center", "cellspacing" => 0));
				$dg -> addHeadColumn("Denumire");
				$dg -> addHeadColumn("UM");
				$dg -> addHeadColumn("Pret Vanzare cu TVA");
				
				$cat = $row['denumire_categorie'];
				$totalCat = 0;
			}
			
			$dg -> addColumn($row['denumire']);
			$dg -> addColumn($row['um']);
			$dg -> addColumn($row['pret_ron'], array("style" => "text-align:right"));
			$dg -> index();
			
			if($row['tip_produs'] ==  "reteta") {
				$retetar = new Retetar("where produs_id = '". $row['produs_id'] ."'");
				$dg -> addColumn("Retetar:");
				$dg -> addColumn($retetar -> listaPrintNomenclator(), array("colspan" => 2));
				$dg -> index();
			}
	
			
			
			if($cat != $this -> data[$i+1]['denumire_categorie']) {
				$out .= $dg -> getDataGrid();
			}
		}
		return $out;
	}
}
?>