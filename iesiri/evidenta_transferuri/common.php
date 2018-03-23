<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("cautare");
$xajax -> registerFunction("afiseaza");

$xajax -> registerFunction("sumar");
$xajax -> registerFunction("anuleazaTransfer");
?>