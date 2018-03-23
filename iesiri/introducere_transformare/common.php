<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("salveazaAntet");
$xajax -> registerFunction("salveazaProdusFinit");
$xajax -> registerFunction("frmProdus");
$xajax -> registerFunction("saveComponenta");
$xajax -> registerFunction("frmMateriePrima");
$xajax -> registerFunction("stergeMateriePrima");
$xajax -> registerFunction("salveazaProdus");
$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");
$xajax -> registerFunction("infoLoturi");

?>