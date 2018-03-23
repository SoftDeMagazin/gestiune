<?php
class RptRegistruCasa extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		if($this -> filtre['from'] && $this -> filtre['end']) {
			$sql .= "and data_doc ". $db -> between(data_c($this -> filtre['from']), data_c($this -> filtre['end']));
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and gestiune_id = '". $this -> filtre['gestiune_id']."'";
		}
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
		SELECT modalitati_plata.descriere as mod_plata, tbl.* FROM (
			SELECT mod_plata_id, numar_doc, data_doc, explicatie, suma, 'plata' AS 'tip' FROM plati 
			where 1 ". $conditions ."
			UNION ALL
			SELECT mod_plata_id, numar_doc, data_doc, explicatie, suma, 'incasare' AS 'tip' FROM incasari 
			where 1 ". $conditions ."
		) AS tbl
		INNER JOIN modalitati_plata USING(mod_plata_id)
		ORDER BY data_doc ASC
		";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$pdf = new PdfHelper();
		$gest = new Gestiuni($this -> filtre['gestiune_id']);
		$out .= $pdf -> antetSocietateHtml($gest -> societate_id);
		$out .= '<h2 align="center">Registru Casa</h2>';
		$out .= '<div align="center">'. c_data($this -> filtre['from']) .' - '. c_data($this -> filtre['end']) .'</div>';
	
		
		$out .= '<div> Gestiune: '.$gest -> denumire;
		$out .= '</div><br/>';
		
		return $out;
	}
	
	function getHtml() {
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$dg -> addHeadColumn("Document");
		$dg -> addHeadColumn("Nr. Doc.");
		$dg -> addHeadColumn("Data Doc.");
		$dg -> addHeadColumn("Explicatie");
		$dg -> addHeadColumn("Incasat");
		$dg -> addHeadColumn("Platit");
		$cat = "";
		$nr_r = count($this -> data);
		$tip = "";
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			$dg -> addColumn($row['mod_plata']);
			$dg -> addColumn($row['numar_doc'], array('style' => 'text-align:center'));
			$dg -> addColumn(c_data($row['data_doc']), array('style' => 'text-align:right'));
			$dg -> addColumn($row['explicatie'], array('style' => 'text-align:left'));
			switch($row['tip']) {
				case "plata": {
					$dg -> addColumn("&nbsp;");
					$dg -> addColumn(douazecimale($row['suma']), array('style' => 'text-align:right'));
					$total_platit += $row['suma'];
				}break;
				case "incasare": {
					$dg -> addColumn(douazecimale($row['suma']), array('style' => 'text-align:right'));
					$dg -> addColumn("&nbsp;");
					$total_incasat += $row['suma'];
				}break;
			}
			$dg -> index();
		}
		
		$dg -> addColumn("Total", array("colspan" => "4"));
		$dg -> addColumn(douazecimale($total_incasat), array('style' => 'text-align:right'));
		$dg -> addColumn(douazecimale($total_platit), array('style' => 'text-align:right'));
		$dg -> index();
		
		$out .= $dg -> getDataGrid();
		return $out;
	}
	
	function getPdf() {
		$pdf = new PdfHelper();
		$pdf -> pdfPropertiesLandscape();
		$pdf -> pdf -> AddPage();
		$pdf -> pdf -> writeHTML($this -> getHtml());
		$pdf -> getPdf("registru_casa.pdf");
	}
}
?>