<?php
class RptStocuri extends Rpt {
	
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
		} else {
			$sql .= " and tip_produs in ('mp', 'marfa')";
		}
		
		if($this -> filtre['cu_stoc']) {
			$sql .= " and stoc > 0";
		}
		
		return $sql;
	}
	
	function getAntet() {
		$out .= '<h2 align="center">STOCURI</h2>';
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
				unitati_masura.denumire as um,
				categorii.denumire as denumire_categorie,
				ifnull(stocuri.stoc,0) as stoc,
				ifnull(stocuri.valoare_stoc_ron,0) as valoare_stoc_ron
			from 
				view_produse_gestiuni
			inner join 
				unitati_masura
				using(unitate_masura_id)	
			inner join 
				categorii
				using(categorie_id)
			left join 
				stocuri
				on view_produse_gestiuni.produs_id = stocuri.produs_id and view_produse_gestiuni.gestiune_id = stocuri.gestiune_id
			where 1
			". $conditions ."
			order by denumire_categorie asc, denumire asc
		";
		
		$sql = "
		select 
			view_produse_gestiuni.denumire,
			unitati_masura.denumire as um,
			categorii.denumire as denumire_categorie,
			ROUND(stoc_la_data(view_produse_gestiuni.produs_id, view_produse_gestiuni.gestiune_id, CURDATE()),3) as stoc,
			ROUND(stoc_la_data_valoric(view_produse_gestiuni.produs_id, view_produse_gestiuni.gestiune_id, CURDATE()),2) as valoare_stoc_ron
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
		$totalCat = 0;
		$total = 0;
		$nr_r = count($this -> data);

		$dg = new DataGrid(array("border" => "1"));
		for($i=0;$i<$nr_r;$i++) {
			$row = $this -> data[$i];
			
			if($cat != $row['denumire_categorie']) {
				$out .= "<h3>". $row['denumire_categorie'] ."</h3>";
				
				$dg = new DataGrid(array("border" => "1", "width" => "98%", "align" => "center", "cellspacing" => 0));
				$dg -> addHeadColumn("Denumire");
				$dg -> addHeadColumn("UM");
				$dg -> addHeadColumn("Stoc Scriptic");
				$dg -> addHeadColumn("Valoare Pret Achizitie");
				$dg -> addHeadColumn("Stoc Faptic");
				
				
				$cat = $row['denumire_categorie'];
				$totalCat = 0;
			}
			
			$dg -> addColumn($row['denumire']);
			$dg -> addColumn($row['um']);
			$dg -> addColumn($row['stoc'] , array("style" => "text-align:right"));
			$dg -> addColumn($row['valoare_stoc_ron'], array("style" => "text-align:right"));
			$dg -> addColumn("&nbsp;");
			
			
			$totalCat += $row['valoare_stoc_ron'];
			$dg -> index();
			
			if($cat != $this -> data[$i+1]['denumire_categorie']) {
				$dg -> addColumn("Total ".$cat, array("colspan" => 3));
				$dg -> addColumn(treizecimale($totalCat), array("style" => "text-align:right"));
				$dg -> addColumn("&nbsp;");
				$out .= $dg -> getDataGrid();
			}
		}
		return $out;
	}
}
?>