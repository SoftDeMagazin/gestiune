<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("salveazaAntet");
$xajax -> registerFunction("inchideFactura");
$xajax -> registerFunction("salveazaComponenta");
$xajax -> registerFunction("frmComponenta");
$xajax -> registerFunction("stergeComponenta");
$xajax -> registerFunction("stornare");
$xajax -> registerFunction("salveazaStornare");

$xajax -> registerFunction("genereazaNir");
$xajax -> registerFunction("salveazaFactura");
$xajax -> registerFunction("anuleazaFactura");


$xajax -> registerFunction("frmProdus");
$xajax -> registerFunction("salveazaProdus");
$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");
$xajax -> registerFunction("infoLoturi");

$xajax -> registerFunction("frmClient");
$xajax -> registerFunction("salveazaClient");
$xajax -> registerFunction("cautareClient");
$xajax -> registerFunction("filtruClient");
$xajax -> registerFunction("selectClient");

$xajax -> registerFunction("selectDelegat");
$xajax -> registerFunction("selectAgent");

$xajax -> registerFunction("lista");

$xajax -> registerFunction("calculator");
$xajax -> registerFunction("adaugaIncasare");

$xajax -> registerFunction("calculeazaScadenta");

$xajax -> registerFunction("discount");
$xajax -> registerFunction("reducerePreturi");

$xajax -> registerFunction("changeNaturaTranzactieiA");
$xajax -> registerFunction("changeCurs");

?>