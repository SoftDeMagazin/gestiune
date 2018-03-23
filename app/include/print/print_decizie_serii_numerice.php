<?php
class PrintDecizieSeriiNumerice extends PdfHelper {
	var $gestiune_id;
	function __construct($gestiune_id) {
		$this -> gestiune_id = $gestiune_id;
		$this -> pdfProperties();
		$this -> pdf -> AddPage();
		$this -> pdf -> writeHTML($this -> getHtml());
	}	
	
	function getHtml() {
		$gestiune = new Gestiuni($this -> gestiune_id);
		$societate = $gestiune -> societate;
		
		$sd = new SeriiDocumente();
		$sd -> getByGestiuneAndTip($this -> gestiune_id, 'facturi');
		$serie = $sd -> serie;
		$nr = $serie -> start;
		$out = '
'. $this -> antetSocietateHtml($societate) .'
<br />
<br />
<br />
<br />
<br />
<br />
<h1 align="center">DECIZIE</h1>
<br />
<br />
<br />
<div align="justify">
  <p>Subsemnatul '.$societate -> administrator.' in calitate de administrator al '. $societate -> denumire .', COD FISCAL: '. $societate -> cod_fiscal .', REGISTRUL COMERTULUI: '. $societate -> reg_com .', decid:</p>
  <p>Incepand cu data de '. c_data($sd -> data_adaugare) .', facturile emise de societate din gestiunea "'. $gestiune -> denumire .'" poarta seria '. $serie -> serie .', primul numar fiind '. str_pad($nr, $serie -> completare_stanga, "0", STR_PAD_LEFT) .'
  </p>
  <p>Prezenta decizie este valabila pana la emiterea unei alte decizii</p>
</div>
<div align="right" style="padding:0px 300px 0px 0px; margin-right:100px;">Administrator,                             </div>
		';
		return $out;
	}
	function getPdf() {
		parent::getPdf('bon_consum.pdf');
	}
}
?>