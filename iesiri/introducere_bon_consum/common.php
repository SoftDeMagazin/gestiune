<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("salveazaAntet");
$xajax -> registerFunction("frmComponenta");
$xajax -> registerFunction("saveComponenta");
$xajax -> registerFunction("stergeComponenta");

$xajax -> registerFunction("inchideDocument");
$xajax -> registerFunction("salveazaDocument");
$xajax -> registerFunction("anuleazaDocument");

$xajax -> registerFunction("frmProdus");
$xajax -> registerFunction("salveazaProdus");
$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");
$xajax -> registerFunction("infoLoturi");


$xajax -> registerFunction("selectTipDoc");
$xajax -> registerFunction("calculeazaPretMediu");
?>