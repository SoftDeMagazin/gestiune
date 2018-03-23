<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("salveazaAntet");
$xajax -> registerFunction("inchideFactura");
$xajax -> registerFunction("salveazaComponenta");
$xajax -> registerFunction("stergeComponenta");
$xajax -> registerFunction("frmComponenta");

$xajax -> registerFunction("genereazaNir");
$xajax -> registerFunction("salveazaFactura");


$xajax -> registerFunction("frmProdus");
$xajax -> registerFunction("salveazaProdus");

$xajax -> registerFunction("frmFurnizor");
$xajax -> registerFunction("salveazaFurnizor");
$xajax -> registerFunction("cautareFurnizor");
$xajax -> registerFunction("filtruFurnizor");
$xajax -> registerFunction("selectFurnizor");

$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");
$xajax -> registerFunction("lista");

$xajax -> registerFunction("calculator");

$xajax -> registerFunction("calculeazaCoteTransport");
$xajax -> registerFunction("salveazaDateIntrastat");

$xajax -> registerFunction("changeNaturaTranzactieiA");
$xajax -> registerFunction("calculeazaScadenta");

$xajax -> registerFunction("calculeaza_cantitate");

$xajax -> registerFunction("changeCurs");
?>