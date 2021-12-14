<?php
require_once(__DIR__."/../cfg.php");
$xajax = new xajax("server.php");
require_once(__DIR__."/../app/xajax/global_functions.php");

$xajax -> registerFunction("login");
?>