<?php
class PrintNotaTransfer {
	
	var $doc;
	var $continut;
	var $iesiri;
	function __construct($transfer_id) {
		$this -> doc = new Transferuri($transfer_id);
		$this -> continut = $this -> doc -> continut;
		$this -> iesiri = new TransferuriIesiri(
		"inner join transferuri_continut on transferuri_continut.continut_id = transferuri_iesiri.comp_id
		where transferuri_continut.transfer_id = '". $transfer_id ."'
		order by transferuri_iesiri.produs_id
		"
		);
	}
	
	/**
	 * returnaza antetul documentului
	 * @return string html antet 
	 */
	function antet() {
		$out = '';
		$serie = $this -> doc -> serie;
		$nr = str_pad($this -> doc -> numar_doc, $serie -> completare_stanga, $serie -> completez_cu, STR_PAD_LEFT);
		$out .= '<h2 align="center">NOTA TRANSFER NR. '. $nr .'</h2>';	
		$out .= '<div align="center">'. c_data($this -> doc -> data_doc) .'</div>';
		$out .= '<div>Din Gestiunea: '. $this -> doc -> gestiune -> denumire .'</div>';
		$out .= '<div>In Gestiunea: '. $this -> doc -> gestiune_destinatie -> denumire.'</div>';
		$out .= '<div>Intocmit de: '. $this -> doc -> utilizator -> nume .'</div>
		<br><br>
		';
		return $out;
	}
	
	
	/**
	 * returneaza html
	 * @return string
	 */
	function getHtml() {
		$out = $this -> antet();
		$out .= $this -> iesiri -> listaPrint();
		return $out;
	}
}
?>