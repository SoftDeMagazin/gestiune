<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("evidentaLoturi");
$xajax -> registerFunction("evidentaIesiri");
$xajax -> registerFunction("fisaMagazie");

$xajax -> registerFunction("printDoc");
?>