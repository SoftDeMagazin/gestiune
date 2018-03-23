<?php
class RptAdaos extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		

		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and s.gestiune_id = '". $this -> filtre['gestiune_id']."'";
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
		$conditions = $this -> getConditions();
		$sql = "
			select 
			c.denumire as categorie,
			p.denumire as produs, 
			round(valoare_stoc_ron / stoc,2) as pa, 
			pg.pret_ron as pv,
			round(((pg.pret_ron * 100/119 - round(valoare_stoc_ron / stoc,2))),2) as adaos_unit,
			round(((pg.pret_ron * 100/119 - round(valoare_stoc_ron / stoc,2)) / round(valoare_stoc_ron / stoc,2))*100,2) as adaos_proc
			from stocuri s
			inner join produse_gestiuni pg on s.produs_id = pg.produs_id and pg.gestiune_id = s.gestiune_id
			inner join produse p on s.produs_id = p.produs_id
			inner join categorii c on p.categorie_id = c.categorie_id
			where 1
			and s.stoc > 0
			and pg.pret_ron > 0
			". $condition ."
			order by c.denumire
		";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">RAPORT ADAOS</h2>';
		
		return $out;
	}
	
	function getHtml() {
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Pret Ach");
		$dg -> addHeadColumn("Pret Vanzare");
		$dg -> addHeadColumn("Adaos Unit");
		$dg -> addHeadColumn("Adaos Proc");
		$cat = "";
		$nr_r = count($this -> data);

		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($cat != $row['categorie']) {
				$out .= Html::h3($row['categorie']);
				$cat = $row['categorie'];
			}
			
			$dg -> addColumn($row['produs']);
			$dg -> addColumn($row['pa'], array('style' => 'text-align:center'));
			$dg -> addColumn($row['pv'], array('style' => 'text-align:center'));
			$dg -> addColumn($row['adaos_unit'], array('style' => 'text-align:center'));
			$dg -> addColumn($row['adaos_proc'], array('style' => 'text-align:center'));
			$dg -> index();
			if($cat != $this -> data[$i+1]['categorie']) {
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}
		}
		return $out;
	}
}
?>