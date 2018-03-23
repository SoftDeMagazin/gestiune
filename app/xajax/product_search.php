<?php
class ProductSearch {
	var $onSelect="xajax_xSelectProdus";
	
	var $searchInputName="x_cautare_produs";
	var $selectName="x_sel_produse";
	
	
	function __construct() {
		
	}
	
	function 
}


function xSelectProdus($produs_id) {
	$produs = new Produse($produs_id);
	
	$objRespone = new xajaxResponse();
	$objResponse -> assign("s")
}

$xajax -> registerFunction("xSelectProdus")
?>