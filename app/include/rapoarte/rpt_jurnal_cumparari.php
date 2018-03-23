<?php
class RptJurnalCumparari extends Rpt {
	
	function __construct($filtre) {
		$this -> filtre = $filtre;
		$this -> genereazaRaport();
	}
	
	function getConditions() {
		$sql = "";	
		global $db;
		if($this -> filtre['from'] && $this -> filtre['end']) {
			$sql .= "and data_factura ". $db -> between(data_c($this -> filtre['from']), data_c($this -> filtre['end']));
		}
		
		if($this -> filtre['gestiune_id']) {
			$sql .= "and gestiune_id = '". $this -> filtre['gestiune_id']."'";
		}
		return $sql;
	}
	
	function genereazaRaport() {
		$conditions = $this -> getConditions();
		$sql = "
		SELECT terti.denumire, terti.cod_fiscal, numar_doc, data_factura, total_fara_tva, total_tva 
		FROM facturi_intrari
		INNER JOIN terti USING(tert_id)
		WHERE salvat = 'DA' ". $conditions ."
		ORDER BY data_factura ASC
		";
		$this -> loadData($sql);
	}
	
	function getAntet() {
		$pdf = new PdfHelper();
		$gest = new Gestiuni($this -> filtre['gestiune_id']);
		$out .= $pdf -> antetSocietateHtml($gest -> societate_id);
		$out .= '<h2 align="center">Jurnal Cumparari</h2>';
		$out .= '<div align="center">'. c_data($this -> filtre['from']) .' - '. c_data($this -> filtre['end']) .'</div>';
	
		
		$out .= '<div> Gestiune: '.$gest -> denumire;
		$out .= '</div><br/>';
		
		return $out;
	}
	
	function getHtml() {
		
		$out = $this -> getAntet();
		$dg = new DataGrid(array("border" => "1", "cellpadding" => 0, "cellspacing" => 0, "width" => "99%"));
		$dg -> addHeadColumn("Data");
		$dg -> addHeadColumn("Nr. Doc.");
		$dg -> addHeadColumn("Furnizor");
		$dg -> addHeadColumn("CUI");
		$dg -> addHeadColumn("Total Document<br/>(inclusiv tva)");
		$dg -> addHeadColumn("Baza impozitare");
		$dg -> addHeadColumn("TVA");
		$cat = "";
		$nr_r = count($this -> data);
		$tip = "";
		for($i=0; $i<$nr_r;$i++) {
			$row = $this -> data[$i];
			$dg -> addColumn(c_data($row['data_factura']));
			$dg -> addColumn($row['numar_doc'], array('style' => 'text-align:center'));
			$dg -> addColumn($row['denumire']);
			$dg -> addColumn($row['cod_fiscal']);
			$dg -> addColumn(douazecimale($row['total_fara_tva'] + $row['total_tva']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['total_fara_tva']), array('style' => 'text-align:right'));
			$dg -> addColumn(douazecimale($row['total_tva']), array('style' => 'text-align:right'));
			$dg -> index();
			$total += $row['total_fara_tva'] + $row['total_tva'];
			$total_fara_tva += $row['total_fara_tva'];
			$total_tva += $row['total_tva'];
		}
		
		$dg -> addColumn("Total", array("colspan" => "4"));
		$dg -> addColumn(douazecimale($total), array('style' => 'text-align:right'));
		$dg -> addColumn(douazecimale($total_fara_tva), array('style' => 'text-align:right'));
		$dg -> addColumn(douazecimale($total_tva), array('style' => 'text-align:right'));
		$dg -> index();
		
		$out .= $dg -> getDataGrid();
		return $out;
	}
	
	function getPdf() {
		$pdf = new PdfHelper();
		$pdf -> pdfPropertiesLandscape();
		$pdf -> pdf -> AddPage();
		$pdf -> pdf -> writeHTML($this -> getHtml());
		$pdf -> getPdf("jurnal_cumapari.pdf");
	}
}
?>