<?php
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("lista");
$xajax -> registerFunction("validateTransfer");
$xajax -> registerFunction("show_transfer");

?>