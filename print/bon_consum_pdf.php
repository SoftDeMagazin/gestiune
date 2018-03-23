<?php
require_once("cfg.php");
require_once(DOC_ROOT.'app/thirdparty/tcpdf/config/lang/eng.php');
require_once(DOC_ROOT.'app/thirdparty/tcpdf/tcpdf.php');

$bon_consum_id = $_GET['bon_consum_id'];

$print = new PrintBonConsumPdf($bon_consum_id);
$print -> getPdf();
?>