<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("frm");
$xajax -> registerFunction("save");
$xajax -> registerFunction("cancel");
$xajax -> registerFunction("sterge");
$xajax -> registerFunction("lista_content");
$xajax -> registerFunction("save_content");
$xajax -> registerFunction("save_pret");
$xajax -> registerFunction("close_inventar");
$xajax -> registerFunction("listeaza");
?>