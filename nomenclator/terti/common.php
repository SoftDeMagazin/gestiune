<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");

$xajax -> registerFunction("afisareDelegati");
$xajax -> registerFunction("frmDelegat");
$xajax -> registerFunction("salveazaDelegat");
$xajax -> registerFunction("stergeDelegat");

$xajax -> registerFunction("afisareAdrese");
$xajax -> registerFunction("frmAdresa");
$xajax -> registerFunction("salveazaAdresa");
$xajax -> registerFunction("stergeAdresa");

$xajax -> registerFunction("afisareAgenti");
?>