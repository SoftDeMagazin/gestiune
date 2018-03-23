<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");


$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("lista_retete");
$xajax -> registerFunction("lista_mp");

$xajax -> registerFunction("filterProducts");
$xajax -> registerFunction("selectProdus");

$xajax -> registerFunction("save_product");
$xajax -> registerFunction("cancel_product");

$xajax -> registerFunction("edit_recipe");
$xajax -> registerFunction("delete_recipe");

$xajax -> registerFunction("save_content");
$xajax -> registerFunction("listeaza");
?>