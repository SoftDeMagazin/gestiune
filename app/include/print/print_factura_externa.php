<?php
class PrintFacturaExterna extends PrintFactura {
	
	function __construct($factura_id) {
		parent::__construct($factura_id);
	}
	
	function infoSocietate() {
		$societate = $this -> societate;
		$out = '
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="200">
					<strong style="font-size:11px;">Furnizor</strong> / <em style="font-size:11px;">Fornitore</em><br />
					'. $this -> getLogo() .'</td>
				<td>
				<div style="padding:3px 3px 3px 3px">
						<strong style="font-size:12px;">'. $societate -> denumire .'</strong><br />
						<span style="font-size:11px;">'. $societate -> reg_com .'</span><br />
						<strong style="font-size:12px;">'. $societate -> cod_fiscal .'</strong><br />
						
						<span style="font-size:11px;">'. $societate -> sediul .'</span>
						<span style="font-size:11px;">'. $societate -> tara .'</span><br />
						<span style="font-size:12px;">'. $societate -> banca_valuta .'</span><br />
						<strong style="font-size:12px;">'. $societate -> iban_valuta.'</strong><br />
						<span style="font-size:11px;">Capital Social: '. $societate -> capital_social .'</span><br />
						<span style="font-size:11px;">'. $societate -> website .'</span><br />
				</div>
			</td>
		  </tr>
		</table>
		';
		return $out;
	}
	
	function infoDoc($page=1) {
	
		$out = '
			<table width="300" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #000">
				<tr>
					<td align="center"  bgcolor="#999999"><strong style="font-size:18px">FACTURA / <em>FATTURA</em></strong></td>
				</tr>
				<tr>
					<td height="120" valign="top">
						<div style="margin-top:4px">
							<table width="100%" border="0" cellspacing="5" cellpadding="0">
							<tr>
								<td width="53%" style="font-size:11px;">Numar / <em>Numero</em></td>
								<td width="47%"><strong style="font-size:14px;">'. str_pad($this -> factura -> numar_doc, 8, "0", STR_PAD_LEFT) .'</strong></td>
							</tr>
							<tr>
								<td style="font-size:11px;">Data / <em>Data</em></td>
								<td ><strong style="font-size:14px;">'. c_data($this -> factura -> data_factura) .'</strong></td>
							</tr>
							<tr>
								<td style="font-size:11px;">Scadenta / <em>Scadenza</em></td>
								<td ><em style="font-size:12px;">'. c_data($this -> factura -> data_scadenta) .'</em></td>
							</tr>
							<tr>
								<td style="font-size:11px;">Agent / <em>Agente</em></td>
								<td>';  
		
		if($factura -> agent_id > 0) {
							$out .= $factura -> agent -> nume; 
		}					
								
		$out .='				</td>	
							</tr>
							<tr>
								<td colspan="2"><div align="center" style="margin-top:1px;font-size:11px;"> <strong>Pagina</strong> '. $page .' / '. $this -> pages .'</div></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
			';
		return $out;	
  	}
	
	function infoClient() {
		$tert = $this -> tert;
		$out = '
				<strong>Client</strong> / <em>Cliente</em>           
				<div style="padding:3px 3px 3px 3px">
				<strong style="font-size:12px;">'. $tert -> denumire .'</strong><br />
				<span >'. $tert -> reg_com.'</span><br />
				<strong style="font-size:12px;">'. $tert -> cod_fiscal .'</strong><br />
				<span style="font-size:11px;">'. $tert -> sediul .'</span><br />
				<span>'. $tert -> cod_tara .'</span><br />
				<span style="font-size:12px;">'. $tert -> banca .'</span><br />
				<span style="font-size:12px;">'. $tert -> iban .'</span><br />
				</div>
			  ';  
		 return $out;	  
	}
	
	function infoFactura($page=1) {
		$out .= '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="350" valign="top" style="padding-top:6px">
				'. $this -> infoDoc($page) .'
				</td>
				<td valign="top">
				'. $this -> infoClient() .'
				</td>
			  </tr>
			</table>
			';
		return $out;			
	}
	
	function detaliiFactura() {
		$out .= '
			<div style="margin-top:3px; font-size:12px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="24%" style="border-top: 1px solid #000">
				<strong>Moneda:</strong> '. $this -> factura -> valuta .'
				</td>
				<td width="28%" style="border-top: 1px solid #000">
				<span><strong>Swift:</strong> '. $this -> societate -> swift .'</span>
				</td>
				<td width="27%" style="border-top: 1px solid #000">
				<strong>Incoterms: </strong> '. $this -> factura -> incoterm .'
				</td>
			  </tr>
			</table>
			</div>
		';
		return $out;
	}
	
	
	function antetContinutFactura() {
		$out = '
			<div style="margin-top:5px">
			<table width="100%" border="1" cellspacing="0" cellpadding="3" class="continut_factura">
			  <tr>
				<td width="40" style="padding:2px 2px 2px 2px;"><div align="left"><strong>NrCrt</strong></div>
					<div align="right"><em>Pos</em></div></td>
				<td  style="padding:2px 2px 2px 2px;"><div align="left"><strong>Descriere Produs</strong></div>
					<div align="right"><em>Descrizione Materiale</em></div></td>
				<td width="40" style="padding:2px 2px 2px 2px;"><div align="left"><strong>UM</strong></div>
					<div align="right"><em>UM</em></div></td>
				<td width="60" style="padding:2px 2px 2px 2px;"><div align="left"><strong>Cantitate</strong></div>
					<div align="right"><em>Qta</em></div></td>
				<td width="70" style="padding:2px 2px 2px 2px;"><div align="left"><strong>Pret</strong></div>
					<div align="right"><em>Prezzo</em></div></td>
				<td  width="90" style="padding:2px 2px 2px 2px;"><div align="left"><strong>Valoare</strong></div>
					<div align="right"><em>Importo</em></div></td>
			  </tr>
			';
		return $out;	
	}
	
	function continutFacturaModel($inaltime=450) {
		$out .= '
		<tr >
			<td width="40" valign="top" style="height:'. $this -> inaltime_pagina .'px" class="nrcrt">
				<%nrcrt%>
			</td>
			<td valign="top" class="denumire">
				<%denumire%>
			</td>
			<td width="40" valign="top" class="um">
				<%um%>
			</td>
			<td width="60" valign="top" class="cantitate">
				<%cantitate%>
			</td>
			<td width="70" valign="top" class="pret">
				<%pret%>
			</td>
			<td width="90" valign="top" class="valoare">
				<%valoare%>
			</td>
		</tr>
		';
		return $out;
	}
	
	function continutFactura() {
		global $db;
		$pagini = array();
		$sql = "
			SELECT
			cod_produs,	
			denumire,
			nc8,
			unitate_masura_id,
			cantitate,
			pret_vanzare_val,
			val_vanzare_val
			FROM facturi_continut
			WHERE factura_id = ". $this -> factura -> id ."
			order by nc8
		";
		
		$continut = $db -> getRows($sql); 
		
		$nc8 = "";
		$contor = 0;
		$pages = array();
		$contor_inaltime = 0;
		foreach($continut as $cnt) {
			$contor++;	
			if($cnt['nc8'] != $nc8) {
				$nrcrt .= '<div class="nc8item">&nbsp;</div>';
				$denumire .=  '<div class="nc8code"><strong>Cod Vamal</strong> / <em>Trariffa doganale</em> '. $cnt['nc8'] .'</div>';
				$um .= '<div class="nc8item">&nbsp;</div>';
				$cantitate .= '<div class="nc8item">&nbsp;</div>';
				$pret .= '<div class="nc8item">&nbsp;</div>';
				$valoare .= '<div class="nc8item">&nbsp;</div>';
				$contor_inaltime += $this -> inaltime_nc8;
				$nc8 = $cnt['nc8'];
			}
			
			
			$nrcrt .= '<div class="bill-item">'. $contor .'</div>';
			$denumire .=  '<div class="product-item">
					<strong>Cod Articol</strong> '. $cnt['cod_produs'] .'<br />
					'. $cnt['denumire'] .'	
			</div>';
			$unit = new UnitatiMasura($cnt['unitate_masura_id']);
			$um .= '<div class="bill-item">'. $unit -> denumire .'</div>';
			$cantitate .= '<div class="bill-item-right">'. $cnt['cantitate'] .'</div>';
			$pret .= '<div class="bill-item-right">'. $cnt['pret_vanzare_val'] .'</div>';
			$valoare .= '<div class="bill-item-right">'. $cnt['val_vanzare_val'] .'</div>';
			$contor_inaltime += $this -> inaltime_pozitie;
			
			
			if((($this -> inaltime_pagina - $contor_inaltime) < $this -> inaltime_pozitie) || $contor == $this -> numar_pozitii) {
				$out = $this -> continutFacturaModel();
				$out = str_replace('<%nrcrt%>', $nrcrt, $out); 
				$nrcrt = ''; 
				$out = str_replace('<%denumire%>', $denumire, $out);
				$denumire = '';
				$out = str_replace('<%um%>', $um, $out);
				$um = '';
				$out = str_replace('<%cantitate%>', $cantitate, $out);
				$cantitate = '';
				$out = str_replace('<%pret%>', $pret, $out);
				$pret = '';
				$out = str_replace('<%valoare%>', $valoare, $out);
				$valoare = ''; 
				$pages[] = $out;
				$contor_inaltime = 0;
			}
		}
		
		return $pages;
	}
	
	function subsolContinutFactura() {
		return '</table>';
	}
	
	function intocmit() {
		return '
			<div style="margin-top:5px;" align="center" class="intocmit_de">
				Scutit cu drept de deducere in conformitate cu prevederile Codului Fiscal &nbsp;  / &nbsp; 
				Exempted with right of deduction according to Fiscal Code
			</div>
		';
	}
	
	function conditii() {
		return '
			<div style="margin-top:5px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
			  <tr>
				<td style="border:1px solid #000">
					<div style="text-align:justify; font-size:10px">
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque id lobortis lorem. Nulla magna eros, aliquet at venenatis ac, auctor quis nisl. Maecenas lacinia, sem nec convallis condimentum, ante odio pharetra magna, vel fringilla urna orci vel justo. In consectetur lobortis nisi non interdum. Morbi eleifend, lorem nec pharetra ultricies, libero tortor imperdiet sapien, sit amet volutpat dui mi quis magna. Integer sed metus arcu. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec magna libero, blandit in laoreet in, condimentum eget lectus. Nulla gravida, nisi eu ultrices tincidunt, urna tortor blandit turpis, et vulputate massa purus ac purus. Ut quam est, elementum ut malesuada nec, laoreet eu nisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec tellus mauris, molestie ut molestie ac, eleifend at felis. Sed interdum vulputate odio quis convallis. Duis convallis libero ac risus ullamcorper lobortis. Donec placerat lectus sed sem sodales eu fringilla augue imperdiet. Vivamus ac sapien sed mi sagittis consectetur.        </div>
				</td>
			  </tr>
			</table>
			</div>
		';
	}
	
	function antet($page=1) {
		$out = '';
		$out .= $this -> infoSocietate();
		$out .= $this -> infoFactura($page);
		$out .= $this -> detaliiFactura();
		return $out;
	}
	
	
	
	function getHtml() {
		$pagini = $this -> continutFactura();
		$this -> pages = count($pagini);
		$nr_pagina = 0;
		$out = '';
		foreach($pagini as $pagina) {
			$nr_pagina++;
			$out .=  '<div style="width:190mm; margin:0px auto;" >';
			$out .= $this -> antet($nr_pagina);
			$out .= $this -> antetContinutFactura();
			$out .= $pagina;
			$out .= $this -> subsolContinutFactura();
			$out .= $this -> subsol();
			$out .= '</div><div style="page-break-after:always; height:0px; overflow:hidden;">&nbsp;</div>';
		}
		return $out;	
	}
	
	function subsolTotal() {
		$out = '
			<div style="margin-top:5px;">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
			  <tr>
				<td width="32%" valign="top" style="border:1px solid #000; height:60px;">Expediere prin / <em>Spedizione a cura di</em>: 
				'. $this -> expediere() .'
				</td>
				<td width="32%" valign="bottom" style="border:1px solid #000; height:60px;">
				'. $this -> semnatura() .'
				</td>
				<td width="37%" style="border:1px solid #000; height:60px;"> 
				'. $this -> total() .'
				</td>
			  </tr>
			</table>
			</div>
		';
		return $out;
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
							<td width="45%"><div align="right" style="font-size:14px; font-weight:bold;">'. number_format($this -> factura -> totalFacturaValuta(), 2,',','.') .'</div></td>
						</tr>
						</table>        
					</td>
				</tr>
			</table>
		';
	
		return $out;
	}
	
	function semnatura() {
		$out = '&nbsp;';
		return $out;
	}
	
	function subsol() {
		$out .= $this -> intocmit();
		$out .= $this -> subsolTotal();
		$out .= $this -> conditii();
		return $out;
	}
	
}
?>