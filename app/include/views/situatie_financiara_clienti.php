<?php
class SituatieFinanciaraClienti extends Model
{
	var $tbl="situatie_financiara_clienti";
	var $key="tip";
	var $_relations = array(
		);
	var $_defaultForm = array(
		);	
		
	function afisare() {
		switch($this -> tip) {
			case 'intern': {
				$total_facturat = $this -> total_ron_cu_tva;
			}break;
			case 'extern_ue': {
				$total_facturat = $this -> total_val_cu_tva;
			}break;
		}
		$sold = $total_facturat - $this -> incasat_total;
		$soldDescoperit = $sold - $this -> total_efecte;
 		$out = '
		<table width="100%" align="center" border="0" >
			<tr>
			<td width="50%">		
				<strong>Total Facturat:</strong> '. douazecimale($total_facturat)  .'<br />
				<strong>Total Incasat:</strong> '. douazecimale($this -> incasat_total) .'<br />
				<strong>Total De Incasat:</strong> '. douazecimale($sold) .'<br />
			</td>
			<td width="50%">
				<strong>Total De Incasat:</strong> '. douazecimale($sold) .'<br />
				<strong>Sold Acoperit:</strong> '. douazecimale($this -> total_efecte) .'<br />
				<strong>Sold Descoperit:</strong> '. douazecimale($soldDescoperit) .'<br />
			</td>	
			</tr>
		</table>
		';
		return $out;
	}		
}
?>