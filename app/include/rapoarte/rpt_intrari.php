<?php
class RptIntrari extends Rpt{
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
			$conditions .= " and loturi.gestiune_id in (". $in .")";
		}
		
		if($f['from'] && $f['end']) {
			$conditions .= " and data_intrare between '". data_c($f['from']) ."' and '". data_c($f['end']) ."'";
		}
		
		if($f['tert_id']) {
			$in = "'".implode("','", $f['tert_id'])."'";
			$conditions .= " and niruri.tert_id in (". $in .")";
		}
		
		$sql = "
			select 
				produse.denumire,
				unitati_masura.denumire as um,
				IFNULL(gestiuni.denumire, 'Total Articol') as denumire_gestiune,
				sum(cantitate_init) as cantitate,
				sum(cantitate_init*pret_intrare_ron) as val_intrare_ron,
				sum(cantitate_init*pret_intrare_val) as val_intrare_val
			from 
				loturi
			inner join produse 
				using(produs_id)
			inner join unitati_masura
				using(unitate_masura_id)	
			inner join niruri 
				on loturi.doc_id = niruri.nir_id
			inner join gestiuni
				on gestiuni.gestiune_id = loturi.gestiune_id		
			where 
				tip_lot = 'nir'
				$conditions
			group by
				produse.produs_id,
				gestiuni.gestiune_id
			WITH ROLLUP
						
		";
		$this -> loadData($sql);
	}
	
	function getHtml() {
		$this -> genereazaRaport();
		$out .= '<h2 align="center">RAPORT INTRARI</h2>';
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
		$dg -> addHeadColumn("Gestiune");
		$dg -> addHeadColumn("Cantitate");
		$dg -> addHeadColumn("UM");
		$dg -> addHeadColumn("Valoare LEI");
		$total_lei = 0;
		$total_val = 0;
		$articol = "";
		$nr_r = count($this -> data);
		for($i=0; $i<$nr_r;$i++){
			$row = $this -> data[$i];
			if($articol != $row['denumire']) {
				$dg -> addColumn($row['denumire']);	
				$dg -> addColumn("&nbsp;");
				$dg -> addColumn("&nbsp;");
				$dg -> addColumn("&nbsp;");
				if(!$row['denumire']) {
					$dg -> addColumn($row['val_intrare_ron'],array("style" => "text-align:right"));
				}
				else {
					$dg -> addColumn("&nbsp;");
				}
				$dg -> index();
				$articol = $row['denumire'];
			}
			
			if($row['denumire']) {
				$dg -> addColumn("&nbsp;");
				$dg -> addColumn($row['denumire_gestiune']);	
				$dg -> addColumn($row['cantitate'], array("style" => "text-align:right"));
				$dg -> addColumn($row['um']);
				$dg -> addColumn($row['val_intrare_ron'], array("style" => "text-align:right"));
				$dg -> index();
			}	
		}
		$out .= $dg -> getDataGrid();
		return $out;
	}
}
?>