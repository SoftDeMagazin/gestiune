<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("cautare");
$xajax -> registerFunction("afiseazaFacturi");

$xajax -> registerFunction("frmIncasare");
$xajax -> registerFunction("stergeIncasare");
$xajax -> registerFunction("salveazaIncasare");

$xajax -> registerFunction("frmEfect");
$xajax -> registerFunction("salveazaEfect");
$xajax -> registerFunction("stergeEfect");
$xajax -> registerFunction("operareEfect");
$xajax -> registerFunction("opereazaEfect");

$xajax -> registerFunction("asociazaIncasare");
$xajax -> registerFunction("disociazaIncasare");

$xajax -> registerFunction("sumarFactura");

$xajax -> registerFunction("situatieActuala");
$xajax -> registerFunction("situatieGlobala");
?>