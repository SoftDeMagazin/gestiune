<?php
class RptConsumuri extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		if($this -> filtre['from'] && $this -> filtre['end']) {
			$sql .= " and data_economica ". $db -> between(data_c($this -> filtre['from']), data_c($this -> filtre['end']));
		}
		
		if($this -> filtre['pos_id']) {
			$sql .= " and vp.pos_id ". $db -> inArray($this -> filtre['pos_id']);
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= " and pos.gestiune_id ". $db -> inArray($this -> filtre['gestiune_id']);
		}
		
		
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
select * from (
SELECT 
	p.denumire AS produs,
	ROUND(SUM(vpi.cantitate),3) AS cantitate,
	um.denumire AS um,
	'mp' AS tip
FROM
	vanzari_pos_continut_iesiri vpi
INNER JOIN vanzari_pos_continut vpc
	ON vpi.comp_id = vpc.continut_id AND vpi.produs_id <> vpc.produs_id
INNER JOIN vanzari_pos AS vp 
	ON vpc.vp_id = vp.vp_id
INNER JOIN posuri AS pos
	ON vp.pos_id = pos.pos_id
INNER JOIN produse p		
	ON p.produs_id = vpi.produs_id
INNER JOIN unitati_masura um
	ON p.unitate_masura_id = um.unitate_masura_id
WHERE 1 ". $conditions ."
GROUP BY
	vpi.produs_id
	
union all

select p.denumire as produs, round(sum(vpc.cantitate),3) as cantitate, u.denumire as um, 'marfuri' as tip from vanzari_pos_continut as vpc
inner join vanzari_pos as vp on vpc.vp_id = vp.vp_id
inner join posuri as pos on pos.pos_id = vp.pos_id
inner join produse as p on p.produs_id = vpc.produs_id and p.tip_produs in ('marfa')
inner join unitati_masura as u on p.unitate_masura_id = u.unitate_masura_id
where 1 ". $conditions ."
group by p.produs_id) as tbl2
order by tip, produs
";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">Raport Consumuri</h2>';
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
		
		return $out;
	}
	
	function getHtml() {
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$dg -> addHeadColumn("Produs", array("width" => "40%"));
		$dg -> addHeadColumn("Cantitate", array("width" => "30%"));
		$dg -> addHeadColumn("Um", array("width" => "30%"));
		$cat = "";
		$nr_r = count($this -> data);
		$tip = "";
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($row['tip'] != $tip) {
				$tip = $row['tip'];
				switch($tip) {
					case "mp": { $out .= '<h3>Consum Materii Prime</h3>';}break;
					case "marfuri": { $out .= '<h3>Consum Marfuri</h3>';}break;
				}
			}
			$dg -> addColumn($row['produs']);
			$dg -> addColumn($row['cantitate'], array('style' => 'text-align:right'));
			$dg -> addColumn($row['um'], array('style' => 'text-align:center'));
			$dg -> index();
			if($tip != $this -> data[$i+1]['tip']) {
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}

		}
		return $out;
	}
}
?>