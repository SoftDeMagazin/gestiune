<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");


$xajax -> registerFunction("afiseazaComisioane");
$xajax -> registerFunction("salveazaComisioane");

$xajax -> registerFunction("frmProdus");
$xajax -> registerFunction("salveazaProdus");
$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");
$xajax -> registerFunction("infoLoturi");
?>