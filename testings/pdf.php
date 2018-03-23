<?php
require_once("cfg.php");
$html = file_get_contents("http://127.0.0.1/print/factura_interna.php");

$dompdf = new DOMPDF();

$dompdf->load_html($html);
$dompdf->render();
  $dompdf->stream("dompdf_out.pdf");

?>