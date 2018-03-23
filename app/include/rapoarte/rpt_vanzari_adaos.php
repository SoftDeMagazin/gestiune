<?php
class RptVanzariAdaos extends Rpt {
	
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
		
		if($this -> filtre['categorie_id']) {
			$sql .= "and categorii.categorie_id ". $db -> inArray($this -> filtre['categorie_id']);
		}
		
		if($this -> filtre['tip_produs']) {
			$sql .= "and p.tip_produs ". $db -> inArray($this -> filtre['tip_produs']);
		}
		
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
SELECT
  vpc.produs_id,
  p.denumire as produs,
  categorii.denumire as categorie,
  ROUND(SUM(vpi.cantitate*loturi.pret_intrare_ron) / ROUND(SUM(vpc.cantitate),2),2) AS pret_achizitie,
  vpc.pret_vanzare
FROM vanzari_pos_continut AS vpc
  INNER JOIN vanzari_pos_continut_iesiri vpi
    ON vpi.comp_id = vpc.continut_id
  INNER JOIN vanzari_pos AS vp
    ON vpc.vp_id = vp.vp_id
  INNER JOIN posuri
    ON vp.pos_id = posuri.pos_id
  INNER JOIN loturi
    ON loturi.lot_id = vpi.lot_id
  INNER JOIN produse AS p
    ON p.produs_id = vpc.produs_id
  INNER JOIN categorii
    ON categorii.categorie_id = p.categorie_id
WHERE 1
	". $conditions ."
GROUP BY vpc.produs_id
ORDER BY categorii.denumire ASC, p.denumire ASC
;
		";
		
$sql = "
SELECT
  vpc.produs_id,
  p.denumire as produs,
  categorii.denumire as categorie,
  tbl.cantitate,
  ROUND(SUM(vpi.cantitate*loturi.pret_intrare_ron) / tbl.cantitate,2) AS pret_achizitie,
  vpc.pret_vanzare
FROM vanzari_pos_continut AS vpc
  INNER JOIN vanzari_pos_continut_iesiri vpi
    ON vpi.comp_id = vpc.continut_id
  INNER JOIN vanzari_pos AS vp
    ON vpc.vp_id = vp.vp_id
  INNER JOIN posuri
    ON vp.pos_id = posuri.pos_id
  INNER JOIN loturi
    ON loturi.lot_id = vpi.lot_id
  INNER JOIN produse AS p
    ON p.produs_id = vpc.produs_id
  INNER JOIN categorii
    ON categorii.categorie_id = p.categorie_id
  INNER JOIN (
	SELECT vpc.produs_id,
	sum(vpc.cantitate) as cantitate
	FROM vanzari_pos_continut vpc
	 INNER JOIN vanzari_pos AS vp
	 ON vpc.vp_id = vp.vp_id
	INNER JOIN posuri
	 ON vp.pos_id = posuri.pos_id
	INNER JOIN produse AS p
		ON p.produs_id = vpc.produs_id
	INNER JOIN categorii
	ON categorii.categorie_id = p.categorie_id
	WHERE 1 and vp.validat = 'DA' ". $conditions ."
	GROUP BY vpc.produs_id
	) as tbl
	on vpc.produs_id = tbl.produs_id	
WHERE 1 ". $conditions ."
GROUP BY vpc.produs_id
ORDER BY categorii.denumire ASC, p.denumire ASC
";		
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">VANZARI - ADAOS</h2>';
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
		$dg -> addHeadColumn("Cantitate", array("width"=>"7%"));
		$dg -> addHeadColumn("Pret Achiztitie", array("width"=>"7%"));
		$dg -> addHeadColumn("Valoare Achizitie", array("width"=>"7%"));
		$dg -> addHeadColumn("Pret Vanzare", array("width"=>"7%"));
		$dg -> addHeadColumn("Valoare Vanzare", array("width"=>"7%"));
		$dg -> addHeadColumn("Valoare Vanzare Baza", array("width"=>"7%"));
		$dg -> addHeadColumn("Valoare Vanzare TVA", array("width"=>"7%"));
		$dg -> addHeadColumn("Adaos unitar", array("width"=>"7%"));
		
		$dg -> addHeadColumn("Adaos Total", array("width"=>"7%"));
		$dg -> addHeadColumn("Adaos %", array("width"=>"7%"));
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
			$dg -> addColumn(douazecimale($row['cantitate']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['pret_achizitie']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['pret_achizitie']*$row['cantitate']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['pret_vanzare']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['pret_vanzare']*$row['cantitate']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['cantitate']*($row['pret_vanzare']*100/119)), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['cantitate']*($row['pret_vanzare']*19/119)), array('style' => 'text-align:right'));
			$adaos = $row['pret_vanzare']*100/119 - $row['pret_achizitie'];
			$dg -> addColumn(douazecimale($adaos), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($adaos*$row['cantitate']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($adaos/$row['pret_achizitie']*100), array('style' => 'text-align:right'));
			$dg -> index();
			
			$total_categorie += $row['valoare'];
			$total_general += $row['valoare'];
			if($cat != $this -> data[$i+1]['categorie']) {
				//$dg -> addColumn("Total ".$cat, array("colspan" => "2"));
				//$dg -> addColumn(douazecimale($total_categorie), array('style' => 'text-align:right'));
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}
		}
		
		return $out;
	}
}
?>