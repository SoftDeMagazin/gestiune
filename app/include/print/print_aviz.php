<?php
class PrintAviz {
	
	var $aviz;
	var $continut;
	var $iesiri;
	function __construct($aviz_id) {
		$this -> aviz = new Avize($aviz_id);
		$this -> continut = $this -> aviz -> continut;
		$this -> iesiri = new AvizeIesiri(
		"inner join avize_continut on avize_continut.continut_id = avize_iesiri.comp_id
		where avize_continut.aviz_id = '". $aviz_id ."'
		order by avize_continut.produs_id
		"
		);
	}
	
	/**
	 * returnaza antetul documentului
	 * @return string html antet 
	 */
	function antet() {
		$out = '';
		$out .= '<h2 align="center">AVIZ NR. '. $this -> aviz -> numar_doc .'</h2>';	
		$out .= '<div align="center">'. c_data($this -> aviz -> data_doc) .'</div>';
		$out .= '<div>Gestiune: '. $this -> aviz -> gestiune -> denumire .'</div>';
		$out .= '<div>Intocmit de: '. $this -> aviz -> utilizator -> nume .'</div>';
		return $out;
	}
	
	
	/**
	 * returneaza html
	 * @return string
	 */
	function getHtml() {
		$out = $this -> antet();
		$out .= '<h3>IESIRI CANTITATIVE</h3>';
		$out .= $this -> continut -> listaPrint();
		$out .= '<h3>IESIRI CANTITATIV-VALORICE</h3>';
		$out .= $this -> iesiri -> listaPrint();
		return $out;
	}
}
?>