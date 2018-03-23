<?php
define('DOC_ROOT', '');
require_once("cfg.php");
$xajax = new xajax("server.php");
require_once(DOC_ROOT."app/xajax/global_functions.php");

$xajax -> registerFunction("switchGest");
$xajax -> registerFunction("logOut");
$xajax -> registerFunction("more");
?>