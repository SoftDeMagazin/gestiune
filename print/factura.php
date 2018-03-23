<?php
require_once("cfg.php");
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
font-size:10px;
}

fieldset {
	border: 0px solid #000;
}

.bill-item {
	height: 30px;
	line-height: 30px;
	text-align:center
}
</style>
<div id="buttons">
<button onClick="WebBrowser1.ExecWB(7, 6);">Preview</button>  
<button onClick="window.print();">Print</button>
<button onClick="window.close();">Close</button>
</div>
<?php
$tert = new Terti(5);
$societate = new Societati(2);
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
<span style="font-size:12px;"><?php echo $societate -> banca; ?></span><br />
<strong style="font-size:12px;"><?php echo $societate -> iban; ?></strong><br />
<span style="font-size:11px;">Capital Social: <?php echo $societate -> capital_social; ?></span><br />
            </div>

    </td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="350" valign="top" style="padding-top:6px">
    
<table width="300" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #000">
  <tr>
    <td align="center"  bgcolor="#999999"><strong style="font-size:18px">FACTURA </strong> / <em style="font-size:18px">FATTURA</em></td>
  </tr>
  <tr>
    <td height="120" valign="top">
    	<div style="margin-top:4px">
    	<table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td width="53%">Numar / <em>Numero</em></td>
    <td width="47%"><strong style="font-size:14px;">00000001</strong></td>
  </tr>
  <tr>
    <td>Data / <em>Data</em></td>
    <td ><strong style="font-size:14px;">20.12.2009</strong></td>
  </tr>
  <tr>
    <td>Scadenta  / <em>Scadenza </em></td>
    <td ><em style="font-size:14px;">30.12.2009</em></td>
  </tr>
  <tr>
    <td>Agent / <em>Agente</em></td>
    <td>Georgescu Busuioc Florin Adrian</td>
  </tr>
    <tr>
    <td colspan="2"><div align="center" style="margin-top:1px"> <strong>Pagina</strong> 1 / 1</div></td>
  </tr>
 	 
</table></div>

    </td>
  </tr>
  </table>

    </td>
    <td valign="top">
    	<fieldset style="height:100%; font-size:12px">
        	<legend><strong>Client</strong> / <em>Cliente</em></legend>
            <div style="padding:3px 3px 3px 3px">
            <strong style="font-size:12px;"><?php echo $tert -> denumire; ?></strong><br />
<span ><?php echo $tert -> reg_com; ?></span><br />
<strong style="font-size:12px;"><?php echo $tert -> cod_fiscal; ?></strong><br />

<span style="font-size:11px;"><?php echo $tert -> sediul; ?></span><br />
<span><?php echo $tert -> cod_tara; ?></span><br />
<span style="font-size:12px;"><?php echo $tert -> banca; ?></span><br />
<span style="font-size:12px;"><?php echo $tert -> iban; ?></span><br />
            </div>
        </fieldset>

    </td>
  </tr>
</table>
 
<div style="margin-top:3px">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="24%" style="border-top: 1px solid #000"><strong>Moneda</strong> / <em>Divisa</em> <strong>EUR</strong></td>
 <td width="18%" style="border-top: 1px solid #000"><span><strong>SWIFT</strong>: <?php echo $societate -> swift; ?></span></td>
    <td width="58%" style="border-top: 1px solid #000"><strong>Incoterms</strong> / <em>Incoterms</em> <strong>EX WORKS</strong></td>
  </tr>
</table>


</div>


<div style="margin-top:5px">
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td width="40" style="padding:2px 2px 2px 2px;">
    <div align="left"><strong>NrCrt</strong></div>
    <div align="right"><em>Pos</em></div>    </td>
    <td style="padding:2px 2px 2px 2px;">
    <div align="left"><strong>Descriere Produs</strong></div>
    <div align="right"><em>Descrizione Materiale</em></div>    </td>
    <td width="40" style="padding:2px 2px 2px 2px;">    
    <div align="left"><strong>UM</strong></div>
    <div align="right"><em>UM</em></div>	</td>
    <td width="90" style="padding:2px 2px 2px 2px;">
    <div align="left"><strong>Cantitate</strong></div>
    <div align="right"><em>Qta</em></div>    </td>
    <td width="90" style="padding:2px 2px 2px 2px;">
    <div align="left"><strong>Pret</strong></div>
    <div align="right"><em>Prezzo</em></div>    </td>
    <td width="120" style="padding:2px 2px 2px 2px;">
    <div align="left"><strong>Valoare</strong></div>
    <div align="right"><em>Importo</em></div>    </td>

  </tr>
  <tr height="600">
    <td valign="top">
    	<div class="bill-item">1</div>
        <div class="bill-item">2</div>
        <div class="bill-item">3</div>
    </td valign="top">
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
</div>
</div>


</body>
</html>
