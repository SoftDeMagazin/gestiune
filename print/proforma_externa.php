<?php
require_once("cfg.php");

define("F_FACTURA", "FACTURA");
define("F_NUMAR", "Numar");
define("F_SCADENTA", "Scadenta");
define("F_AGENT", "Agent");
define("F_FURNIZOR", "Furnizor");
define("F_CLIENT", "Client");
define("F_NRCRT", "NrCrt");
define("F_DENUMIRE", "Descriere Produs");
define("F_UM", "UM");
define("F_CANTITATE", "Cantitate");
define("F_PRET", "Pret");
define("F_VALOARE", "Valoare");
define("F_TVA", "TVA");
define("F_CODVAMAL", "Cod Vamal");

define("F_FACTURA_IT", "FATTURA");
define("F_NUMAR_IT", "Numero");
define("F_SCADENTA_IT", "Scadenze");
define("F_AGENT_IT", "Agente");
define("F_FURNIZOR_IT", "Fornitore");
define("F_CLIENT_IT", "Cliente");
define("F_NRCRT_IT", "Pos");
define("F_DENUMIRE_IT", "Descrizione Materiale");
define("F_UM_IT", "UM");
define("F_CANTITATE_IT", "Qta");
define("F_PRET_IT", "Prezzo");
define("F_VALOARE_IT", "Importo");
define("F_TVA_IT", "IVA");
define("F_CODVAMAL_IT", "Cod Vamal");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PRINT</title>
</head>

<body>
<object ID="WebBrowser1" WIDTH="0" HEIGHT="0" CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>
<style>@media print { #buttons { display:none } } </style>
<style>
body {
font-family:'Arial', Helvetica, sans-serif;
font-size:11px;
}

fieldset {
	border: 0px solid #000;
}

.bill-item {
	margin-top:2px;
	height: 35px;
	text-align:center;
}
.bill-item-right {
	margin-top:2px;
	height: 35px;
	text-align:right;
}

.product-item {
margin-top:2px;
	height: 35px;
}

.nc8code {
	height: 20px;
	line-height: 20px;
	text-align:center;
	text-decoration:underline;
	border: 0px solid #000;
}

.nc8item {
	height:20px;
}
</style>
<div id="buttons">
<button onClick="WebBrowser1.ExecWB(7, 6);">Preview</button>  
<button onClick="window.print();">Print</button>
<button onClick="window.close();">Close</button>
</div>
<?php
$factura_id = $_GET['factura_id'];
$factura = new Facturi($factura_id);
$tert = $factura -> tert;
$societate = new Societati($factura -> societate_id);

?>
<div style="width:190mm; margin:0px auto" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="200">
    <strong>Furnizor</strong> / <em>Fornitore</em><br />
    <img src="../app/img/logo.png" width="160" height="100" /></td>
<td>
    	
        	
            <div style="padding:3px 3px 3px 3px">
            <strong style="font-size:12px;"><?php echo $societate -> denumire; ?></strong><br />
<span ><?php echo $societate -> reg_com; ?></span><br />
<strong style="font-size:12px;"><?php echo $societate -> cod_fiscal; ?></strong><br />

<span style="font-size:11px;"><?php echo $societate -> sediul; ?></span>
<span style="font-size:11px;"><?php echo $societate -> tara; ?></span><br />
<span style="font-size:12px;"><?php echo $societate -> banca_valuta; ?></span><br />
<strong style="font-size:12px;"><?php echo $societate -> iban_valuta; ?></strong><br />
<span style="font-size:11px;">Capital Social: <?php echo $societate -> capital_social; ?></span><br />
<span style="font-size:11px;"><?php echo $societate -> website; ?></span><br />
            </div>

    </td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="350" valign="top" style="padding-top:6px">
    
<table width="300" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #000">
  <tr>
    <td align="center"  bgcolor="#999999"><strong style="font-size:18px">FACTURA / <em>FATTURA</em></strong></td>
  </tr>
  <tr>
    <td height="120" valign="top">
    	<div style="margin-top:4px">
    	<table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td width="53%">Numar / <em>Numero</em></td>
    <td width="47%"><strong style="font-size:14px;">PROFORMA</strong></td>
  </tr>
  <tr>
    <td>Data / <em>Data</em></td>
    <td ><strong style="font-size:14px;">20.12.2009</strong></td>
  </tr>
  
    <tr>
    <td colspan="2"><div align="center" style="margin-top:1px"> <strong>Pagina</strong> 1 / 1</div></td>
  </tr>
</table>
    	</div>

    </td>
  </tr>
  </table>

    </td>
    <td valign="top">
    	
        	<strong>Client</strong>
/ <em>Cliente</em>           
<div style="padding:3px 3px 3px 3px">
            <strong style="font-size:12px;"><?php echo $tert -> denumire; ?></strong><br />
<span ><?php echo $tert -> reg_com; ?></span><br />
<strong style="font-size:12px;"><?php echo $tert -> cod_fiscal; ?></strong><br />

<span style="font-size:11px;"><?php echo $tert -> sediul; ?></span><br />
<span><?php echo $tert -> cod_tara; ?></span><br />
<span style="font-size:12px;"><?php echo $tert -> banca; ?></span><br />
<span style="font-size:12px;"><?php echo $tert -> iban; ?></span><br />
            </div>


    </td>
  </tr>
</table>
 
<div style="margin-top:3px; font-size:12px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="24%" style="border-top: 1px solid #000"><strong>Moneda:</strong> <?php echo $factura -> valuta; ?> </td>
 <td width="28%" style="border-top: 1px solid #000"><span><strong>Swift</strong> <?php echo $societate -> swift; ?> </span></td>
    <td width="27%" style="border-top: 1px solid #000">
    <strong>Incoterms: </strong><?php echo $factura -> incoterm; ?>    </td>
    </tr>
</table>


</div>


<div style="margin-top:5px">
<table width="100%" border="1" cellspacing="0" cellpadding="3">
  <tr>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>NrCrt</strong></div>
        <div align="right"><em>Pos</em></div></td>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>Descriere Produs</strong></div>
        <div align="right"><em>Descrizione Materiale</em></div></td>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>UM</strong></div>
        <div align="right"><em>UM</em></div></td>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>Cantitate</strong></div>
        <div align="right"><em>Qta</em></div></td>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>Pret</strong></div>
        <div align="right"><em>Prezzo</em></div></td>
    <td style="padding:2px 2px 2px 2px;"><div align="left"><strong>Valoare</strong></div>
        <div align="right"><em>Importo</em></div></td>
  </tr>
  <tr >
    <td width="40" valign="top" style="height:450px">
		<?php
		$continut = $factura -> continut;
		$nr_r = 0;
		foreach($continut as $cnt) {
			$nr_r++;
			echo '<div class="bill-item">'. $nr_r .'</div>';
		}
		
		?>    </td>
    <td valign="top">
    	<?php
		foreach($continut as $cnt) {
    	echo '
			<div class="product-item">
				<strong>Cod Articol</strong> '. $cnt -> produs -> cod_produs .'<br />
				'. $cnt -> produs -> denumire .'	
			</div>';
		}
		?>    </td>
    <td width="40" valign="top">
		<?php
		$continut = $factura -> continut;
		$nr_r = 0;
		foreach($continut as $cnt) {
			$nr_r++;
			echo '<div class="bill-item">'. $cnt -> produs -> unitate_masura -> denumire .'</div>';
		}
		
		?>    </td>
    <td width="60" valign="top">
		<?php
		$continut = $factura -> continut;
		$nr_r = 0;
		foreach($continut as $cnt) {
			$nr_r++;
			echo '<div class="bill-item-right">'. $cnt -> cantitate .'</div>';
		}
		
		?>    </td>
    <td width="70" valign="top">
		<?php
		$continut = $factura -> continut;
		$nr_r = 0;
		foreach($continut as $cnt) {
			$nr_r++;
			echo '<div class="bill-item-right">'. $cnt -> pret_vanzare_val .'</div>';
		}
		
		?>    </td>
    <td width="90" valign="top">
		<?php
		$continut = $factura -> continut;
		$nr_r = 0;
		foreach($continut as $cnt) {
			$nr_r++;
			echo '<div class="bill-item-right">'. $cnt -> val_vanzare_val .'</div>';
		}
		
		?>    </td>
    </tr>
</table>

<div style="margin-top:5px;" align="center">
	Scutit cu drept de deducere in conformitate cu prevederile Codului Fiscal &nbsp;  / &nbsp; 
	Exempted with right of deduction according to Fiscal Code
</div>
<div style="margin-top:5px;">
<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="32%" valign="top" style="border:1px solid #000; height:60px;">Expediere prin / <em>Spedizione a cura di</em>: 
    <?php
	if($factura -> delegat_id == -2) {
		echo '<strong>CURIER</strong><br />';
		$awb = ($factura -> auto_numar) ? $factura -> auto_numar : "_____________________";
		echo '
		AWB:
		'. $awb .'';

	}
	$auto = ($factura -> auto_numar) ? $factura -> auto_numar : "_____________________";
	if($factura -> delegat_id == -1) {
		echo '
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
		echo '
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
	
	?>    </td>
    <td width="32%" valign="bottom" style="border:1px solid #000; height:60px;">&nbsp;</td>
    <td width="37%" style="border:1px solid #000; height:60px;">
    
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td width="27%">&nbsp;</td>
        </tr>
      <tr>
        <td>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="55%">Total / <em>Totale </em><strong><?php echo $factura -> valuta; ?> </strong></td>
            <td width="45%"><div align="right" style="font-size:14px; font-weight:bold;"><?php echo number_format($factura -> totalFacturaValuta(), 2,',','.'); ?></div></td>
          </tr>
        </table>        </td>
        </tr>
    </table>
    
    </td>
  </tr>
</table>

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

</div>


</div>
</div>


</body>
</html>
