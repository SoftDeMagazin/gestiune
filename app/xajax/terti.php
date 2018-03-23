<?php

/**
 * deschide dialog cu formular cautare tert
 * @return $objResponse
 */
function xWinCautareTert() {
	
	$dialog = new Dialog(400, 400, '', 'x_cautare_tert');
	$dialog -> title = 'Cautare Tert';
	$dialog -> append(Html::text('txt_cautare', "", array("id" => "txt_cautare")));
	$dialog -> append('<input type="button" value="Ok" onClick="xajax_xCautTert($(\'#txt_cautare\').val())" />');
	$dialog -> append(Html::overflowDiv('', "300px", '', array("id" => "div_rez_terti")));
	$objResponse = $dialog -> open();
	$objResponse -> script("
		$('#txt_cautare').focus();
		$('#txt_cautare').keyup(
			function(event) {
				if(event.keyCode == 13) {
					xajax_xCautTert($(this).val());
				}
			}
		)
	");
	return $objResponse;
}

/**
 * populeaza div_rez_terti din dialog cautare terti
 * @param object $str_search
 * @return 
 */
function xCautTert($str_search) {
	$terti = new Terti();
	$terti -> cautare($str_search);
	$objResponse = new xajaxResponse();
	if(count($terti)) {
		$out = $terti -> listaDefault(array("denumire" => "Denumire", 
			"cod_fiscal" => "Cod Fiscal"),
			"xajax_xSelectTert('<%tert_id%>');$('#x_cautare_tert').dialog('close');"
			);
		$objResponse -> assign('div_rez_terti', 'innerHTML', $out);
		return $objResponse;
	} else {		
		$objResponse -> assign('div_rez_terti', 'innerHTML', 'Nici un rezultat');
		return $objResponse;
	}
}

function xSelectTert($tert_id) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("$('#tert_id').val(".$tert_id.")");
	return $objResponse;	
}

$xajax -> registerFunction("xSelectTert");
$xajax -> registerFunction("xWinCautareTert");
$xajax -> registerFunction("xCautTert");
?>