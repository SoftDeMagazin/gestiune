<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("cautare");
$xajax -> registerFunction("afiseazaFacturi");

$xajax -> registerFunction("frmPlata");
$xajax -> registerFunction("stergePlata");
$xajax -> registerFunction("salveazaPlata");

$xajax -> registerFunction("frmEfect");
$xajax -> registerFunction("salveazaEfect");
$xajax -> registerFunction("stergeEfect");
$xajax -> registerFunction("operareEfect");
$xajax -> registerFunction("opereazaEfect");

$xajax -> registerFunction("asociazaPlata");
$xajax -> registerFunction("disociazaPlata");

$xajax -> registerFunction("situatieActuala");
?>