<?php
class PrintNirModel {
	var $nir;
	var $factura;
	var $tert;
	var $gestiune;
	var $continut;
	function __construct($nir_id) {
		$this -> nir = new Niruri($nir_id);
		$this -> tert = $this -> nir -> tert;
		$this -> gestiune = $this -> nir -> gestiune;
		$this -> continut = $this -> nir -> continut;
		$this -> factura = $this -> nir -> factura;
	}
	
	function subsolSemnaturi() {
		return '
		<table width="100%" border=0>
			<tr>
				<td width="50%">Intocmit</td>
				<td width="50%">Gestionar</td>
			</tr>
			<tr>
				<td width="50%">'. $this -> factura -> user -> nume .'</td>
				<td width="50%">_________________________________________</td>
			</tr>
		</table>
		';
	}
	
	function getHtml() {
		$out = '';
		return $out;
	}
}
?>