<?php
class PrintDepreciere {
	
	var $depreciere;
	var $continut;
	var $iesiri;
	function __construct($depreciere_id) {
		$this -> depreciere = new Deprecieri($depreciere_id);
		$this -> continut = $this -> depreciere -> continut;
		$this -> iesiri = new DeprecieriIesiri(
		"inner join deprecieri_continut on deprecieri_continut.depreciere_continut_id = deprecieri_iesiri.comp_id
		where deprecieri_continut.depreciere_id = '". $depreciere_id ."'
		order by deprecieri_iesiri.produs_id
		"
		);
	}
	
	/**
	 * returnaza antetul documentului
	 * @return string html antet 
	 */
	function antet() {
		$out = '';
		$serie = $this -> depreciere -> serie;
		$nr = str_pad($this -> depreciere -> numar_doc, $serie -> completare_stanga, $serie -> completez_cu, STR_PAD_LEFT);
		$out .= '<h2 align="center">FISA DEPRECIERE NR. '. $nr .'</h2>';	
		$out .= '<div align="center">'. c_data($this -> depreciere -> data_doc) .'</div>';
		$out .= '<div>Gestiune: '. $this -> depreciere -> gestiune -> denumire .'</div>';
		$out .= '<div>Intocmit de: '. $this -> depreciere -> utilizator -> nume .'</div>';
		return $out;
	}
	
	
	/**
	 * returneaza html
	 * @return string
	 */
	function getHtml() {
		$out = $this -> antet();
		//$out .= '<h3>IESIRI CANTITATIVE</h3>';
		//$out .= $this -> continut -> listaPrint();
		//$out .= '<h3>IESIRI CANTITATIV-VALORICE</h3>';
		$out .= $this -> iesiri -> listaPrint();
		return $out;
	}
}
?>