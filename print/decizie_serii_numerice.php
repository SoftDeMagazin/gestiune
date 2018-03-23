<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$gestiune_id = $_GET['gestiune_id'];

$print = new PrintDecizieSeriiNumerice($gestiune_id);
$print -> getPdf();
?>