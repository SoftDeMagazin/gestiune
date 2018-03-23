<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	$sql = " INNER JOIN terti_gestiuni using(tert_id)";
	$sql .= " WHERE gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
	if($frmFiltre['denumire']) {
		$sql .= " and terti.denumire like '%". $frmFiltre['denumire'] ."%'";
	}
	$sql .= " order by denumire asc";
	$model = new Terti($sql);
	
	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = count($model);
	$model -> pageLength($frmPager['pagesize']);
	
	$info = paginator($action, $model, $frmPager['curentpage']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "xajax_frm('<%tert_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function frm($id=0)
{
	if($id) {
		$model = new ViewTertiGestiuni("where tert_id = '$id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	} else {
		$model = new ViewTertiGestiuni();
	}
	
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   <input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_view_terti_gestiuni\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\')); this.disabled = true;" tabindex="6">
   <input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 </div>

	';
	$objResponse -> append("frm", "innerHTML", $btn);
	$gest = new Gestiuni("where 1");
	if($id) {
		$selected = $model -> getGestiuniAsociate();
	}
	else {
		$selected = array($_SESSION['user'] -> gestiune_id);
	}
	$objResponse -> script("$('#tabs').tabs('enable', 1);");
	copyResponse($objResponse, switchTab('frm'));
	$objResponse -> assign("div_frm_gest", "innerHTML", "Gestiune<br />
".$gest -> selectMulti($selected));
	$objResponse -> script("$('#gestiune_id').multiSelect()");
	
	$objResponse -> script("\$('#denumire').focus().select();");
	$objResponse -> script("
		$('#tip').change(
			function() {
				switch($(this).val()) {
					case 'intern': {
						$('#cod_tara').attr('value', 'RO');
						$('#valuta').val('LEI');
						$('#err_frm_cod_tara').html('');
					}break;
					case 'extern_ue': {
						$('#cod_tara').val(0);
						$('#valuta').val('EUR');
					}break;
					case 'extern_nonue': {
						$('#cod_tara').val(0);
						$('#valuta').val('USD');
					}break;
				}
			}
		);
	");
	copyResponse($objResponse, initControl());
	return $objResponse;
}


function cancel() 
{
	$objResponse = switchTab('lista');
	$objResponse -> script("$('#tabs').tabs('disable', 1);");
	$objResponse -> assign("frm", "innerHTML", "");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	global $db;
	$model = new Terti($frmValues);
	$objResponse = new xajaxResponse();
	if(!$model -> validate($objResponse)) {
		return $objResponse;
	}
	
	if(!$frmValues['gestiune_id']) {
		return alert('Asociati tert-ul cu cel putin o gestiune');
	}
	$model -> save();
	
	$model -> disociazaGestiuni($frmValues['gestiune_id']);
	$model -> asociazaCuGestiuni($frmValues['gestiune_id'], array("scadenta_default" => $frmValues['scadenta_default'], "categorie_tert_id" => $frmValues['categorie_tert_id'],"limita_credit_intern" => $frmValues['limita_credit_intern'], "limita_credit_asigurat" => $frmValues['limita_credit_asigurat']));
	
	$tg = new TertiGestiuni("where tert_id = '". $model -> tert_id ."' and gestiune_id = ". $_SESSION['user'] -> gestiune_id ."");
	$tg -> scadenta_default = $frmValues['scadenta_default'];
	$tg -> categorie_tert_id = $frmValues['categorie_tert_id'];
	$tg -> limita_credit_intern = $frmValues['limita_credit_intern'];
	$tg -> limita_credit_asigurat = $frmValues['limita_credit_asigurat'];
	$tg -> save();
	
	$objResponse = lista($frmFiltre, $frmPager, "default", $model -> id);
	copyResponse($objResponse, switchTab('lista'));
	$objResponse -> script("$('#tabs').tabs('disable', 1);");
	return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {

	$model = new Terti($id);
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}

function afisareAgenti($tert_id) {
	$tert = new Terti($tert_id);
	$dialog = new Dialog(800, 600, "", "win_delegati");
	$dialog -> title = "Agenti - ". $tert -> denumire;
	$dialog -> modal = true;
	$dialog -> append('<div id="grid-delegati" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px">');
	$ats = new AgentiTerti("where tert_id = '$tert_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$dialog -> append($ats -> listaAgenti("", ""));
	$dialog -> append("</div>");
	$dialog -> addButton("Inchide");
	return $dialog -> open();
	
}

function afisareDelegati($tert_id) {
	$tert = new Terti($tert_id);
	$dialog = new Dialog(800, 600, "", "win_delegati");
	$dialog -> title = "Delegati - ". $tert -> denumire;
	$dialog -> modal = true;
	$dialog -> append(iconAdd("xajax_frmDelegat(0, '". $tert -> id ."')"));
	$dialog -> append(iconEdit("xajax_frmDelegat($('#selected_delegat_id').val(), '". $tert -> id ."')"));
	$dialog -> append(iconRemove("xajax_stergeDelegat($('#selected_delegat_id').val())"));
	$dialog -> append('<div id="grid-delegati" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px">');
	$dialog -> append($tert -> delegati -> lista("", "xajax_frmDelegat('<%delegat_id%>', '<%tert_id%>')"));
	$dialog -> append("</div>");
	$dialog -> addButton("Inchide");
	return $dialog -> open();
	
}

function afisareAdrese($tert_id) {
	$tert = new Terti($tert_id);
	$dialog = new Dialog(800, 600, "", "win_delegati");
	$dialog -> title = "Adrese - ". $tert -> denumire;
	$dialog -> modal = true;
	$dialog -> append(iconAdd("xajax_frmAdresa(0, '". $tert -> id ."')"));
	$dialog -> append(iconEdit("xajax_frmAdresa($('#selected_tert_adresa_id').val(), '". $tert -> id ."')"));
	$dialog -> append(iconRemove("xajax_stergeAdresa($('#selected_tert_adresa_id').val())"));
	$dialog -> append('<div id="grid-adrese" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both; margin-top:10px">');
	$dialog -> append($tert -> adrese -> lista("", "xajax_frmAdresa('<%tert_adresa_id%>', '<%tert_id%>')"));
	$dialog -> append("</div>");
	$dialog -> addButton("Inchide");
	return $dialog -> open();
	
}

function listaDelegati($tert_id) {
	$tert = new Terti($tert_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid-delegati", "innerHTML", $tert -> delegati -> lista("", "xajax_frmDelegat('<%delegat_id%>', '<%tert_id%>')"));
	return  $objResponse;
}

function listaAdrese($tert_id) {
	$tert = new Terti($tert_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid-adrese", "innerHTML", $tert -> adrese -> lista("", "xajax_frmAdresa('<%tert_adresa_id%>', '<%tert_id%>')"));
	return  $objResponse;
}



function frmDelegat($delegat_id, $tert_id) {
	$delegat = new Delegati($delegat_id);
	$delegat -> tert_id = $tert_id;
	$dialog = new Dialog(400, 300, "", "win_frm_delgat");
	$dialog -> title = "Adaugare/Editare Delegat";
	$dialog -> append($delegat -> frmDefault());
	
	$dialog -> addButton("Salveaza", "xajax_salveazaDelegat(xajax.getFormValues('frm_delegati'));<%close%>");
	$dialog -> addButton("Renunta");
	return $dialog -> open();
}

function salveazaDelegat($frmValues) {
	$delegat = new Delegati($frmValues);
	$delegat -> save();
	return listaDelegati($delegat -> tert_id);
}

function frmAdresa($tert_adresa_id, $tert_id) {
	$delegat = new TertiAdrese($tert_adresa_id);
	$delegat -> tert_id = $tert_id;
	$dialog = new Dialog(500, 400, "", "win_frm_delgat");
	$dialog -> title = "Adaugare/Editare Adresa";
	$tert = new Terti($tert_id);
	if(!$tert_adresa_id) $delegat -> cod_tara = $tert -> cod_tara;
	$dialog -> append($delegat -> frmDefault());
	
	$dialog -> addButton("Salveaza", "xajax_salveazaAdresa(xajax.getFormValues('frm_terti_adrese'));<%close%>");
	$dialog -> addButton("Renunta");
	return $dialog -> open();
}

function salveazaAdresa($frmValues) {
	$delegat = new TertiAdrese($frmValues);
	$delegat -> save();
	return listaAdrese($delegat -> tert_id);
}

function stergeAdresa($delegat_id) {
	if(!$delegat_id) {
		return alert('Selectati un delegat');
	}
	$delegat = new TertiAdrese($delegat_id);
	$tert_id = $delegat -> tert_id;
	$delegat -> delete();
	return listaAdrese($tert_id);
}

function stergeDelegat($delegat_id) {
	if(!$delegat_id) {
		return alert('Selectati un delegat');
	}
	$delegat = new Delegati($delegat_id);
	$tert_id = $delegat -> tert_id;
	$delegat -> delete();
	return listaDelegati($tert_id);
}
?>