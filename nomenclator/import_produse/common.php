<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");

$xajax -> registerFunction("asociazaProdus");

$xajax -> registerFunction("modificaPret");
$xajax -> registerFunction("salveazaPretGestiuni");

$xajax -> registerFunction("savePret");

$xajax -> registerFunction("lista_produse");
$xajax -> registerFunction("importa_produse");
?>