<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("load");

//***** LOAD MULTISELECTS *****

$xajax -> registerFunction("load_societati");
$xajax -> registerFunction("load_puncte_lucru");
$xajax -> registerFunction("load_gestiuni");
$xajax -> registerFunction("load_posuri");
$xajax -> registerFunction("load_categorii");

// ***** FILTRE *******

//produs
$xajax -> registerFunction("browse_products");
$xajax -> registerFunction("filter_products");
$xajax -> registerFunction("selectProdus");

//locatii
$xajax -> registerFunction("filter_by_societate");
$xajax -> registerFunction("filter_by_puncte_lucru");
$xajax -> registerFunction("filter_by_gestiuni");

?>