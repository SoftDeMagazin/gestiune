<?php
class RptIesiri extends Rpt {
	function __construct($filtre) {
		$this -> filtre = $filtre;
	}
	
	function genereazaRaport() {
		$f = $this -> filtre;
		global $db;
		if($f['denumire']) {
			$conditions .= " and produse.denumire like '%". $f['denumire'] ."%'";
		}
		
		if($f['categorie_id']) {
			$in = implode(",", $f['categorie_id']);
			$conditions .= " and produse.categorie_id in (". $in .")";
		}
		
		if($f['tip_produs']) {
			$in = "'".implode("','", $f['tip_produs'])."'";
			$conditions .= " and produse.tip_produs in (". $in .")";
		}
		
		if($f['gestiune_id']) {
			$in = "'".implode("','", $f['gestiune_id'])."'";
			$conditions .= " and facturi.gestiune_id in (". $in .")";
		}
		
		if($f['from'] && $f['end']) {
			$conditions .= " and facturi.data_factura between '". data_c($f['from']) ."' and '". data_c($f['end']) ."'";
		}
		
		if($f['tert_id']) {
			$in = "'".implode("','", $f['tert_id'])."'";
			$conditions .= " and facturi.tert_id in (". $in .")";
		}
		
		$sql = "
			select 
				facturi_iesiri.produs_id,
				produse.denumire,
				sum(facturi_iesiri.cantitate) as cantitate,
				round(sum(((facturi_iesiri.cantitate * loturi.pret_intrare_ron) * (facturi_continut.pret_vanzare_ron*facturi_continut.cantitate))/pret_intrare_componenta(comp_id)),2)
				as val_vanzare_ron,
				sum(facturi_iesiri.cantitate*loturi.pret_intrare_ron) as val_intrare_ron
			from 	
				facturi_iesiri
			inner join loturi 
				using(lot_id)
			inner join facturi_continut
				on facturi_iesiri.comp_id = facturi_continut.continut_id 
			inner join facturi
				on facturi.factura_id = facturi_continut.factura_id	
			inner join produse
				on facturi_iesiri.produs_id = produse.produs_id
			where 
				1
				$conditions
			group by facturi_iesiri.produs_id,
				produse.denumire	
		";
		$this -> loadData($sql);
	}
	
	function getHtml() {
		$this -> genereazaRaport();
		$out .= '<h2 align="center">RAPORT IESIRI</h2>';
		$out .= '<div align="center">'. $this -> filtre['from'] .' - '. $this -> filtre['end'] .'</div>';
		$out .= '<div> Gestiuni: ';
		if($this -> filtre['gestiune_id']) {
			foreach($this -> filtre['gestiune_id'] as $id) {
				$gestiune = new Gestiuni($id);
				$out .= $gestiune -> denumire.", "; 
			}
		}
		else {
			$out .= " Toate";
		}	
		$out .= '</div>';
		$dg = new DataGrid(array("style" => "width:98%;margin:10px auto;" , "border" => "1", "cellpadding" => 0, "cellspacing" => 0, "id" => "rpt_iesiri", "class" => ""));
		$dg -> addHeadColumn("Articol");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("Valoare Pret Vanzare LEI");
		$dg -> addHeadColumn("Valoare Pret Ach LEI");
		foreach($this -> data as $row) {
			$dg -> addColumn($row['denumire']);
			$dg -> addColumn($row['cantitate'], array("style" => "text-align:right"));
			$dg -> addColumn($row['val_vanzare_ron'], array("style" => "text-align:right"));
			$dg -> addColumn($row['val_intrare_ron'], array("style" => "text-align:right"));
			$total_vanzare_lei += $row['val_vanzare_ron'];
			$total_ach_lei += $row['val_intrare_ron'];
			$dg -> index();
		}
		$dg -> addColumn("Total");
		$dg -> addColumn("&nbsp;");
		$dg -> addColumn(douazecimale($total_vanzare_lei), array("style" => "text-align:right"));
		$dg -> addColumn(douazecimale($total_ach_lei), array("style" => "text-align:right"));
		$out .= $dg -> getDataGrid();
		return $out;
	}
}
?>