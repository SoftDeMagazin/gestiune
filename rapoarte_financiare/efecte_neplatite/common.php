<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");
$xajax -> registerFunction("salveazaEfecte");
$xajax -> registerFunction("loadSocietate");
$xajax -> registerFunction("loadGestiune");
?>