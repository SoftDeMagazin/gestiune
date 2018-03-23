<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

//$xajax -> registerFunction("filterProducts");
$xajax -> registerFunction("filterGestiuni");
$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("sterge");

$xajax -> registerFunction("save");
?>