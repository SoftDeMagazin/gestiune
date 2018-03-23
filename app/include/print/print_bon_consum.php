<?php
class PrintBonConsum {
	
	var $bc;
	var $continut;
	var $iesiri;
	function __construct($bon_consum_id) {
		$this -> bc = new BonuriConsum($bon_consum_id);
		$this -> continut = $this -> bc -> continut;
		$this -> iesiri = new BonuriConsumIesiri(
		"inner join bonuri_consum_continut on bonuri_consum_continut.continut_id = bonuri_consum_iesiri.comp_id
		where bonuri_consum_continut.bon_consum_id = '". $bon_consum_id ."'
		order by bonuri_consum_iesiri.produs_id
		"
		);
	}
	
	/**
	 * returnaza antetul documentului
	 * @return string html antet 
	 */
	function antet() {
		$out = '';
		$serie = $this -> bc -> serie;
		$nr = str_pad($this -> bc -> numar_doc, $serie -> completare_stanga, $serie -> completez_cu, STR_PAD_LEFT);
		$out .= '<h2 align="center">BON CONSUM NR. '. $nr .'</h2>';	
		$out .= '<div align="center">'. c_data($this -> bc -> data_doc) .'</div>';
		$out .= '<div>Gestiune: '. $this -> bc -> gestiune -> denumire .'</div>';
		$out .= '<div>Intocmit de: '. $this -> bc -> utilizator -> nume .'</div><br><br>';
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