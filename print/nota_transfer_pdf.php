<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$transfer_id = $_GET['transfer_id'];

$print = new PrintNotaTransferPdf($transfer_id);
$print -> getPdf();
?>