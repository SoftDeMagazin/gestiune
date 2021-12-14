<?php
require_once(__DIR__."/../cfg.php");
$xajax = new xajax("server.php");
require_once(__DIR__."/../app/xajax/global_functions.php");
$xajax -> registerFunction("lista");
$xajax -> registerFunction("actualizareCurs");
$xajax -> registerFunction("infoCursValutar");
$xajax -> registerFunction("afiseazaCurs");
?>