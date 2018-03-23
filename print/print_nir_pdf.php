<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$nir_id = $_GET['nir_id'];

$print = new PrintNirPdf($nir_id);
$print -> getPdf();
?>