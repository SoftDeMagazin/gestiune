<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("showWorkingPoints");
$xajax -> registerFunction("filterByWorkingPoint");
$xajax -> registerFunction("saveHeader");

$xajax -> registerFunction("saveComponent");
$xajax -> registerFunction("editComponent");
$xajax -> registerFunction("deleteComponent");

$xajax -> registerFunction("saveTransfer");
$xajax -> registerFunction("validateTransfer");

$xajax -> registerFunction("selectProdus");
$xajax -> registerFunction("filterProducts");
$xajax -> registerFunction("lista");
?>