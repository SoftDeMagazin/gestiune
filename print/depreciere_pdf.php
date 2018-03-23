<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$depreciere_id = $_GET['depreciere_id'];

$print = new PrintDeprecierePdf($depreciere_id);
$print -> getPdf();
?>