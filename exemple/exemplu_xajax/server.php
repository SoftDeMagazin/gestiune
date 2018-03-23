<?php
require_once("common.php");
$xajax -> processRequest();
/*
 * in server definesc functiile in php care trebuie sa returneze in 
 */
function test() {
	//trebuie sa generezi si sa returnezi un $objResponse
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_id", "innerHTML", "hello");
	return $objResponse;
}

function helloWorld($nume) {
	//
	
	$objResponse = new xajaxResponse();
	
	$objResponse -> alert($nume);
	
	//va copia ce returneaza test() in actualul objResponse
	copyResponse($objResponse, test());
	return $objResponse;
}


?>