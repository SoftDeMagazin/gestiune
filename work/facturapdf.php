<?php
require_once("cfg.php");
require_once('../app/thirdparty/tcpdf/config/lang/eng.php');
require_once('../app/thirdparty/tcpdf/tcpdf.php');

define("PDF_FONT", "helvetica");
$pdf = new PrintFacturaInternaPdf(9);
$pdf->getPdf();
?>