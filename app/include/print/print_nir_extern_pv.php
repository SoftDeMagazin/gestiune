<?php
class PrintNirExternPv extends PrintNirModel {
	
	function __construct($nir_id) {
		parent::__construct($nir_id);
	}
	
	function antet() {
		$out = '<h2 align="center">NOTA RECEPTIE SI CONSTATARE DIFERENTE</h2>';
		$out .= '
		<table width="400" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="148"><strong>GESTIUNE</strong></td>
		    <td width="252">'. $this -> gestiune -> denumire .'</td>
		  </tr>
		  <tr>
		    <td width="148"><strong>DATA (ZZ.LL.AAAA)</strong></td>
		    <td width="252">'. c_data($this -> nir -> data_nir) .'</td>
		  </tr>
		  <tr>
		    <td width="148"><strong>FURNIZOR</strong></td>
		    <td width="252">'. $this -> tert -> denumire .' -  '. $this -> tert -> cod_fiscal .'</td>
		  </tr>
		  <tr>
		    <td><strong>Document Nr. </strong></td>
		    <td>'. $this -> factura -> tip -> descriere .'  '. $this -> nir -> numar_doc .'</td>
		  </tr>
		  <tr>
		    <td><strong>CURS VALUTAR</strong></td>
		    <td>'. $this -> factura -> curs_valutar .'</td>
		  </tr>
		    <tr>
		    <td><strong>VALUTA</strong></td>
		    <td>'. $this -> factura -> valuta .'</td>
		  </tr>
		</table>
		';		
		return $out;
	}
	
	function antetProduse() {
		return '
			<table cellspacing="0" cellpadding="0" width="100%" border=1>
			  <TR>
				<TH scope="col" rowspan=2><DIV align="center">NR.<BR>
				  CRT.</DIV> </TH>
				<TH scope="col" rowspan=2>DENUMIRE PRODUS </TH>
				<TH scope="col" rowspan=2> UM </TH>
				<TH colSpan="5" scope="col">RECEPTIONAT</TH>
				<TH colSpan="2" scope="col">ADAOS</TH>
				<TH colSpan="2" scope="col">TVA</TH>
				<TH colSpan="2" scope="col">PRET VANZARE<br />
CU TVA</TH>
			  </tr> 
			  <TR>
				<TH scope="col"> <DIV align="center">CANT</DIV></TH>
				<TH scope="col">PRET<BR>
				  UNITAR RON</TH>
				<TH scope="col">VALOARE RON<br>
				</TH>
				<TH scope="col">PRET<BR>
				  UNITAR VALUTA</TH>
				<TH scope="col">VALOARE VALUTA<br>
				</TH>
				<TH scope="col">UNITAR
				</TH>
				<TH scope="col">TOTAL
				</TH>
				<TH scope="col">UNITAR
				</TH>
				<TH scope="col">TOTAL
				</TH>
				<TH scope="col">UNITAR
				</TH>
				<TH scope="col">TOTAL
				</TH>
			   </tr>		  
	  		';
	}
	
	function continut(&$totaluri) {
		$nr_crt = 0;
		
		$out = "";
		
		$ttl_adaos = 0;
		$ttl_tva_ach = 0;
		$ttl_pret_vanzare = 0;
		$ttl_pret_ach = 0;
		$ttl_tva_vanzare = 0;
		foreach($this -> continut as $cnt) {
			$row = new TableRow();
			$nr_crt++;
			$produs = $cnt -> produs;
			

			$pret_ach_ron = $cnt -> getPretIntrareRon();
			$val_ach_ron = $pret_ach_ron * $cnt -> cantitate;
			$val_tva_ron = $cnt -> cantitate * (($pret_ach_ron * $cnt -> cota_tva -> valoare) / 100);

			
			$row -> addCell(new TableCell($nr_crt, array("style" => "text-align:center")));
			$row -> addCell(new TableCell($produs -> denumire));
			$row -> addCell(new TableCell($produs -> unitate_masura -> denumire, array("style" => "text-align:center")));
			$row -> addCell(new TableCell($cnt -> cantitate, array("style" => "text-align:right")));
			$row -> addCell(new TableCell(douazecimale($pret_ach_ron), array("style" => "text-align:right")));
			$row -> addCell(new TableCell(douazecimale($val_ach_ron), array("style" => "text-align:right")));
			$row -> addCell(new TableCell(douazecimale($cnt -> pret_ach_val), array("style" => "text-align:right")));
			$row -> addCell(new TableCell(douazecimale($cnt -> val_ach_val), array("style" => "text-align:right")));
			
			$cota_tva = $produs -> cota_tva -> valoare;
			
			$pret_vnz_fara_tva = ($cnt -> pret_vanzare * 100) / (100 + $cota_tva);
			$adaos = $pret_vnz_fara_tva - $pret_ach_ron;
			$adaos_total = $cnt -> cantitate * $adaos;
			
			$tva = $cnt -> pret_vanzare - $pret_vnz_fara_tva;
			$tva_total = $cnt -> cantitate * $tva;
			$val_vanzare = $cnt -> cantitate * $cnt -> pret_vanzare;
			
			if($cnt -> tip_produs == "marfa") {
				$row -> addCell(new TableCell(douazecimale($adaos), array("style" => "text-align:right")));
				$row -> addCell(new TableCell(douazecimale($adaos_total), array("style" => "text-align:right")));
				$row -> addCell(new TableCell(douazecimale($tva), array("style" => "text-align:right")));
				$row -> addCell(new TableCell(douazecimale($tva_total), array("style" => "text-align:right")));
				$row -> addCell(new TableCell(douazecimale($cnt -> pret_vanzare), array("style" => "text-align:right")));
				$row -> addCell(new TableCell(douazecimale($val_vanzare), array("style" => "text-align:right")));
				
				$ttl_adaos += $adaos_total;
			} else {
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
				$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
			}
			
			
			$ttl_pret_vanzare += $val_vanzare;
			$ttl_tva_ach += $val_tva_ron;
			$ttl_pret_ach += $val_ach_ron;
			$ttl_tva_vanzare += $tva_total;
			$ttl_pret_ach_val += $cnt -> val_ach_val;
			
			
			$out .= $row -> getRow();
		}
		
		$totaluri = array($ttl_pret_ach, $ttl_tva_ach);
			$row = new TableRow();
			$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:center")));
			$row -> addCell(new TableCell("Total", array("colspan" => "4")));
			$row -> addCell(new TableCell(money($ttl_pret_ach, "LEI"), array("style" => "text-align:right")));
			$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:center")));
			$row -> addCell(new TableCell(money($ttl_pret_ach_val, $this -> factura -> valuta), array("style" => "text-align:right")));
			$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
			$row -> addCell(new TableCell(money($ttl_adaos, "LEI"), array("style" => "text-align:right")));
			$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
			$row -> addCell(new TableCell(money($ttl_tva_vanzare, "LEI"), array("style" => "text-align:right")));
			$row -> addCell(new TableCell("&nbsp;", array("style" => "text-align:right")));
			$row -> addCell(new TableCell(money($ttl_pret_vanzare, "LEI"), array("style" => "text-align:right")));
			$out .= $row -> getRow();
			
		$out .= '</table>';
		return $out;
	}
	
	function getHtml() {
		$out .= $this -> antet();
		$out .= $this -> antetProduse();
		$totaluri = array();
		$out .= $this -> continut($totaluri);		
		return $out;
	}
}
?>