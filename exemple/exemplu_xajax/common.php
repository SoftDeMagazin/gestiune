<?php
require_once("cfg.php");

$xajax = new xajax("server.php");
//definesc functiile
$xajax -> registerFunction("test");

$xajax -> registerFunction("helloWorld");
?>