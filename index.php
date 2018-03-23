<?php
define("DOC_ROOT", '');
$sess=true;
require_once("cfg.php");
require_once("test_login.php");
header("Location:".DOC_ROOT."/home/");
?>