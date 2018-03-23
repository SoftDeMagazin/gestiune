<?php
class RptConsumRetete extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		
		if($this -> filtre['from'] && $this -> filtre['end']) {
			$sql .= "and vp.data_economica ". $db -> between(data_c($this -> filtre['from']), data_c($this -> filtre['end']));
		}
		
		if($this -> filtre['pos_id']) {
			$sql .= " and posuri.pos_id ". $db -> inArray($this -> filtre['pos_id']);
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and posuri.gestiune_id ". $db -> inArray($this -> filtre['gestiune_id']);
		}	
		
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		global $db;
		if($this -> filtre['categorie_id']) {
			$c .= "and c.categorie_id ". $db -> inArray($this -> filtre['categorie_id']);
		}
		
		if($this -> filtre['tip_produs']) {
			$c .= "and p.tip_produs ". $db -> inArray($this -> filtre['tip_produs']);
		}		
		$sql = "
SELECT 
	t1.produs_id,
	p.denumire as produs,
	p.tip_produs,
	c.denumire AS categorie,
	t1.cantitate, 
	t1.val_vanzare, 
	t2.prod_comp_id, 
	t2.cantitate_mp,
	t2.val_achizitie
FROM
(

(SELECT 
	vpc.produs_id,
	ROUND(SUM(vpc.cantitate),2) AS cantitate,
	ROUND(SUM(vpc.cantitate*vpc.pret_vanzare),2) AS val_vanzare
FROM 
	vanzari_pos_continut AS vpc
INNER JOIN 
	vanzari_pos vp
	ON vp.vp_id = vpc.vp_id
INNER JOIN posuri
	ON vp.pos_id = posuri.pos_id
WHERE 1 and vp.validat = 'DA' ". $conditions ."			
GROUP BY vpc.produs_id
) AS t1, 
	
(SELECT 
	vpc.produs_id,
	vpi.produs_id AS prod_comp_id,
	ROUND(SUM(vpi.cantitate),2) AS cantitate_mp,
	ROUND(SUM(vpi.cantitate*l.pret_intrare_ron),2) AS val_achizitie
FROM 
	vanzari_pos_continut_iesiri AS vpi
INNER JOIN 
	loturi l
	on l.lot_id = vpi.lot_id	
INNER JOIN 
	vanzari_pos_continut AS vpc
	ON  vpc.continut_id = vpi.comp_id
INNER JOIN 
	vanzari_pos vp
	ON vp.vp_id = vpc.vp_id
INNER JOIN posuri
	ON vp.pos_id = posuri.pos_id	
WHERE 1	". $conditions ."	
GROUP BY vpi.produs_id, vpc.produs_id
ORDER BY vpc.produs_id) AS t2
)

INNER JOIN produse p
	ON t1.produs_id = p.produs_id
INNER JOIN categorii c
	ON p.categorie_id = c.categorie_id
WHERE t1.produs_id = t2.produs_id
". $c ."
ORDER BY c.denumire, p.denumire asc
;
		";
	

		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">RAPORT CONSUM - RETETE</h2>';
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
		$dg -> addHeadColumn("Produs", array("width"=>"25%"));
		$dg -> addHeadColumn("Cantitate", array("width"=>"25%"));
		$dg -> addHeadColumn("Pret Vanzare", array("width"=>"25%"));
		$dg -> addHeadColumn("Valoare Vanzare", array("width"=>"25%"));
		
		$cat = "";
		$nr_r = count($this -> data);
		$total_categorie = 0;
		$total_general = 0;
		$produs_id = 0;
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($cat != $row['categorie']) {
				$out .= Html::h3($row['categorie']);
				$cat = $row['categorie'];
				$total_categorie = 0;
			}
			if($row['tip_produs'] == 'reteta') {
				if($row['produs_id'] != $produs_id) {
					$produs_id = $row['produs_id'];
					$dg -> addColumn($row['produs']);
					$dg -> addColumn($row['cantitate'], array("align" => "right"));
					$dg -> addColumn(douazecimale($row['val_vanzare']/$row['cantitate']), array("align" => "right"));
					$dg -> addColumn(douazecimale($row['val_vanzare']), array("align" => "right"));
					$dg -> index();
					$out .= $dg -> getDataGrid();
					$dg -> renew();
					$out .= 'Consum Materii Prime<br/>';
					
					$comp = new DataGrid(array("border" => "0",  "width" => "80%", "cellpadding" => 0, "cellspacing" => 0, "align" => "center"));
					$comp -> addHeadColumn("Materie Prima");
					$comp -> addHeadColumn("Cantitate");
					$comp -> addHeadColumn("Um");
					$comp -> addHeadColumn("Valoare Pret ach");
					$ttl_val_ach = 0; 
				}
				
				$prod = new Produse($row['prod_comp_id']);
				$comp -> addColumn($prod -> denumire);
				$comp -> addColumn($row['cantitate_mp'], array("align" => "right"));
				$comp -> addColumn($prod -> unitate_masura -> denumire, array("align" => "center"));
				$comp -> addColumn($row['val_achizitie'], array("align" => "right"));
				$comp -> index();
				$ttl_val_ach += $row['val_achizitie'];
				
				if($produs_id != $this -> data[$i+1]['produs_id']) {
					$comp -> addColumn("Total Valoare Ach");
					$comp -> addColumn("&nbsp;", array("align" => "right"));
					$comp -> addColumn("&nbsp;", array("align" => "right"));
					$comp -> addColumn(douazecimale($ttl_val_ach), array("align" => "right"));
					$comp -> index();
					$out .= $comp -> getDataGrid();
					$out .= '<hr><br/>';
				}
			} else {
				$produs_id = $row['produs_id'];
				$dg -> addColumn($row['produs']);
				$dg -> addColumn($row['cantitate'], array("align" => "right"));
				$dg -> addColumn(douazecimale($row['val_vanzare']/$row['cantitate']), array("align" => "right"));
				$dg -> addColumn(douazecimale($row['val_vanzare']), array("align" => "right"));
				$dg -> index();
				$out .= $dg -> getDataGrid();
				$dg -> renew();
				$out .= '<hr><br/>';
			}
						
	
			
			$total_categorie += $row['valoare'];
			$total_general += $row['valoare'];
			if($cat != $this -> data[$i+1]['categorie']) {
			}
		}
		
		return $out;
	}
}
?>