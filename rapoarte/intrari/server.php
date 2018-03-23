<?php
require_once("common.php");
$xajax->processRequest();

function load($filtre) {
	$rpt = new RptIntrari($filtre);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid", "innerHTML", $rpt -> getHtml());
	return $objResponse;
}
?>