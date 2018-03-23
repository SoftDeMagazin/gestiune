<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");

$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filtruProduse");

$xajax -> registerFunction("frmComponenta");
$xajax -> registerFunction("salveazaComponenta");
$xajax -> registerFunction("stergeComponenta");

$xajax -> registerFunction("adaugaComisionAgent");
$xajax -> registerFunction("salveazaComisionAgent");
$xajax -> registerFunction("stergeComisionAgent");
$xajax -> registerFunction("salveazaComisioane");

$xajax -> registerFunction("listeaza");

$xajax -> registerFunction("importaProdus");
$xajax -> registerFunction("listaRetete");
?>