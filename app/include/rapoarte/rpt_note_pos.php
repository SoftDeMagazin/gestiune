<?php
class RptNotePos extends Rpt {
	
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
			$sql .= " and p.pos_id ". $db -> inArray($this -> filtre['pos_id']);
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and p.gestiune_id ". $db -> inArray($this -> filtre['gestiune_id']);
		}
		
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
		select 
			g.denumire as gestiune,
			p.cod as pos,
			mod_plata as mod_plata,
			sum(suma) as suma
		from note_pos
		inner join posuri as p using(pos_id)
		inner join gestiuni as g on p.gestiune_id = g.gestiune_id
		where 1
			". $conditions ."
		group by pos_id, g.gestiune_id, mod_plata
		order by gestiune, pos, mod_plata
		";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$out .= '<h2 align="center">Raport Incasari Locatii</h2>';
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
		$dg -> addHeadColumn("Pos");
		$dg -> addHeadColumn("Mod Plata");
		$dg -> addHeadColumn("Total");
		$gestiune = "";
		$nr_r = count($this -> data);
		$total_gestiune = 0;
		$total_general = 0;
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			if($gestiune != $row['gestiune']) {
				$out .= Html::h3($row['gestiune']);
				$gestiune = $row['gestiune'];
				$total_gestiune = 0;
			}
			
			$dg -> addColumn($row['pos']);
			$dg -> addColumn($row['mod_plata'], array('style' => 'text-align:left'));
			$dg -> addColumn(douazecimale($row['suma']), array('style' => 'text-align:right'));
			$dg -> index();
			$total_gestiune += $row['suma'];
			$total_general += $row['suma'];
			if($gestiune != $this -> data[$i+1]['gestiune']) {
				$dg -> addColumn("Total ".$cat, array("colspan" => "2"));
				$dg -> addColumn(douazecimale($total_gestiune), array('style' => 'text-align:right'));
				$out .= $dg -> getDataGrid();
				$dg -> renew();
			}
		}
		$out .= Html::h3("TOTAL GENERAL: ".douazecimale($total_general));
		return $out;
	}
}
?>