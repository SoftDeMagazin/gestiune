<?php
class PrintFactura extends PdfHelper {
	var $factura;
	var $societate;
	var $gestiune;
	var $continut;
	var $tert;
	var $numar_pozitii;
	var $pozitii_pe_pagina = 12;
	var $pages;
	
	var $inaltime_pozitie = 37;
	var $inaltime_nc8 = 20;
	var $inaltime_pagina = 480;

	function __construct($factura_id) {
		$this -> factura = new Facturi($factura_id);
		$this -> societate = $this -> factura -> societate;
		$this -> gestiune = $this -> factura -> gestiune;
		$this -> continut = $this -> factura -> continut;
		$this -> tert = $this -> factura -> tert;
		
		$this -> numar_pozitii = count($this -> continut);
		$this -> pages = (int) $this -> numar_pozitii / $this -> pozitii_pe_pagina;
	}
	
	function getLogo() {
		return Html::img(DOC_ROOT.PATH_LOGO_SOCIETATI.$this -> societate -> logo);
	}
	
		function expediere() {
		$factura = $this -> factura;
		if($factura -> delegat_id == -2) {
			$out = '<strong>CURIER</strong><br />';
			$awb = ($factura -> auto_numar) ? $factura -> auto_numar : "_____________________";
			$out .= '
			AWB:
			'. $awb .'';
		}
		
		$auto = ($factura -> auto_numar) ? $factura -> auto_numar : "_____________________";
		if($factura -> delegat_id == -1) {
			$out = '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td>Nume</td>
				<td>_____________________</td>
			  </tr>
			  <tr>
				<td>Cnp</td>
				<td>_____________________</td>
			  </tr>
			  <tr>
				<td>Act</td>
				<td>_____________________</td>
			  </tr>
			  <tr>
				<td>Auto</td>
				<td>'. $auto .'</td>
			  </tr>
			</table>
			';	
		}
		
		if($factura -> delegat_id > 0) {
			$delegat = new Delegati($factura -> delegat_id);
			$out = '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td>Nume</td>
				<td>'. $delegat -> nume .'</td>
			  </tr>
			  <tr>
				<td>Cnp</td>
				<td>'. $delegat -> cnp .'</td>
			  </tr>
			  <tr>
				<td>Act</td>
				<td>'. $delegat -> act_identitate .'</td>
			  </tr>
			  <tr>
				<td>Auto</td>
				<td>'. $auto .'</td>
			  </tr>
			</table>
			';
		}
	
		return $out;
	}
	
	function total() {
	
		$out = '
			<table width="100%" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td width="27%">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="55%">Total / <em>Totale </em><strong>'. $this -> factura -> valuta .'</strong></td>
							<td width="45%"><div align="right">'. number_format($this -> factura -> totalFacturaValuta(), 2,',','.') .'</div></td>
						</tr>
						</table>        
					</td>
				</tr>
			</table>
		';
	
		return $out;
	}
}
?>