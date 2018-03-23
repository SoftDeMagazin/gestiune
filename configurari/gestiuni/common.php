<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");

$xajax -> registerFunction("setSerie");
$xajax -> registerFunction("saveSerie");

$xajax -> registerFunction("adaugSerie");
$xajax -> registerFunction("saveAdaugSerie");
$xajax -> registerFunction("changeSocietate");
// ----- import wizard -----

$xajax -> registerFunction("show_import_wizard");
$xajax -> registerFunction("wizard_compute_selection");

$xajax -> registerFunction("wizard_next");

$xajax -> registerFunction("wizard_products_step");
$xajax -> registerFunction("wizard_categories_step");
$xajax -> registerFunction("wizard_thirds_step");

$xajax -> registerFunction("wizard_save_categories");
$xajax -> registerFunction("wizard_save_products");
$xajax -> registerFunction("wizard_save_thirds");


// --------- end -----------

?>