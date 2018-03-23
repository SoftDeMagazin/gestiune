<?php
require_once("common.php");
$xajax->processRequest();

function load($aviz_id=NULL) {
	$objResponse = new xajaxResponse();
	$model = new Avize($aviz_id);
	
	if(!$aviz_id) {
		copyResponse($objResponse, selectTipDoc("la_factura"));
		$model -> tip_aviz = "la_factura";
	} else {
		copyResponse($objResponse, selectTipDoc($model -> tip_aviz, $aviz_id));
	}
	$objResponse -> assign("div_tip_document", "innerHTML", $model -> tip());
	
	$objResponse -> script("$('#tip_aviz').change(
		function() {
			xajax_selectTipDoc($(this).val(), $('#aviz_id').val());
		}
	);");
	copyResponse($objResponse, initControl());
	return $objResponse;
}
/**
 * in functie de tip aviz imi afiseaza formularul
 * @param object $tip_aviz
 * @param object $aviz_id [optional]
 * @return 
 */
function selectTipDoc($tip_aviz, $aviz_id=0) {
	$model = new Avize($aviz_id);
	
	
	if($aviz_id) {
		if($tip_aviz != $model -> tip_aviz) {
			$objResponse = alert('Documentul a fost salvat, nu mai puteti schimba tipul');
			$objResponse -> script("$('#tip_aviz').val('". $model -> tip_aviz ."');");
			return $objResponse;
		}
	}
	
	$model -> tip_aviz = $tip_aviz;
	$objResponse = new xajaxResponse();
	switch($tip_aviz) {
		case "la_factura": {
			if(!$aviz_id) {
				$model -> data_doc = c_data(data());
				$serie = $model -> getSerie($_SESSION['user'] -> gestiune_id);
				$model -> numar_doc = $serie -> curent+1;
			} else {
				$model -> data_doc = c_data($model -> data_doc);
				$model -> anulareScaderi();
				
				copyResponse($objResponse, lista($aviz_id));
				copyResponse($objResponse, frmComponenta(0, $aviz_id));
			}
			$objResponse -> assign("div_frm_antet", "innerHTML", $model -> frmDefault($model -> frmLaFactura));
		
			if($aviz_id) {
				$objResponse -> assign("grid_facturi", "innerHTML", $model -> factura -> sumar());
				$objResponse -> assign("div_perioada", "innerHTML", "");
			} else {
				$objResponse -> assign("from", "value",c_data(date("01.m.Y")));
				$objResponse -> assign("end", "value",c_data(date("d.m.Y")));		
			}		
		}break;
		case "la_transfer": {
			$objResponse -> assign("div_frm_antet", "innerHTML", "
				Nu puteti emite acest tip de aviz! Se va genera automat la emiterea unui transfer!
			");
		}break;
		case "doc_pv": {
			
		}
		case "doc_pa": {
			if(!$aviz_id) {
				$model -> data_doc = c_data(data());
				$curs = new Cursuri();
				$curs -> getLast('EUR');
				$serie = $model -> getSerie($_SESSION['user'] -> gestiune_id);
				$model -> numar_doc = $serie -> curent+1;
				$model -> curs_valutar = $curs -> valoare;
			} else {
				$model -> data_doc = c_data($model -> data_doc);
				$model -> anulareScaderi();
				
				copyResponse($objResponse, lista($aviz_id));
				copyResponse($objResponse, frmComponenta(0, $aviz_id));
			}
			
			
			
			$script = "
					\$('#txtCautareFurnizor').keyup(
						function(event) {
							switch(event.keyCode) {
								case 40: {
									$('#sel_furnizor').attr('selectedIndex', 0);					
									$('#sel_furnizor').focus()						
								}break;
								default: {
									OnKeyRequestBuffer.modified('txtCautareFurnizor', 'xajax_filtruClient', 100);
								}break;
							}
						}
					);
			
			
				var position = \$('#txtCautareFurnizor').offset();	
				\$('#div_filtru_furnizori').hide();	
				\$('#div_filtru_furnizori').css('top', ''+(position.top + 23)+'px');
				\$('#div_filtru_furnizori').css('left', ''+(position.left)+'px');
				\$('#txtCautareFurnizor').focus(
					function() {
						\$('#div_filtru_furnizori').show();
					}
				);
				
				\$('#txtCautareFurnizor').focus();			
			
			";
			$objResponse -> assign("div_frm_antet", "innerHTML", $model -> frmDefault());	
			
			if($aviz_id) {
				$objResponse -> assign("txtCautareFurnizor", "value", $model -> tert -> denumire .' - '.$model -> tert -> cod_fiscal);
			}
			$objResponse -> sleep(1);

			
		}break;
	}
	if($tip_aviz != 'la_transfer') {
	$objResponse -> assign("div_save", "innerHTML", '
			   <div>
		      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		        <tr>
		          <td><div align="center">
		            <label>
		            <div align="center">
		              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_avize\'))">
		            </div>
		            </label>
		          </div>
		          </td>
		          </tr>
		      </table>
		    </div>
		
			');
	}
	$objResponse -> script($script);		
	copyResponse($objResponse, initControl());
	return $objResponse;
}

function afiseazaFacturi($from, $end) {
	$sql = "WHERE 1";
	if($from && $end) {
		$sql .= " and `data_factura` between '". data_c($from) ."' and '". data_c($end) ."'";
	}
	$sql .= " and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
	$sql .= " and `salvat` = 'DA'";
	$sql .= " ORDER BY `data_factura` DESC";
	$facturi = new Facturi($sql);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("grid_facturi", "innerHTML", $facturi -> lista("xajax_selecteazaFactura('<%factura_id%>');"));
	return $objResponse;
}

function selecteazaFactura($factura_id) {
	$objResponse = new xajaxResponse();
	$aviz = new Avize(" where factura_id = '$factura_id'");
	if(count($aviz)) {
		return alert("Pentru aceasta factura a fost deja emis Avizul nr. ".$aviz -> numar_doc);
	}
	$factura = new Facturi($factura_id);
	$objResponse -> assign("factura_id", "value", $factura_id);
	$objResponse -> assign("tert_id", "value", $factura -> tert_id);
	$objResponse -> assign("selected_factura", "innerHTML", $factura -> numar_doc);
	return $objResponse;
}

function salveazaAntet($frm)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$objResponse = new xajaxResponse();
	$model = new Avize($frm);
	$model -> data_doc = data_c($model -> data_doc);
	
	if(!$frm['factura_id'] && $frm['tip_aviz'] == 'la_factura') {
		return alert("Nu ati selectat o factura!");
	}
	
	if(!$frm['aviz_id']) {
		$model -> numar_doc = $model -> getNumar($_SESSION['user']-> gestiune_id);
		$model -> incrementSerie($_SESSION['user']-> gestiune_id);
		$serie = $model -> getSerie($_SESSION['user']-> gestiune_id);
		$model -> serie_id = $serie -> id;
		$model -> gestiune_id = $_SESSION['user'] -> gestiune_id;
		$model -> utilizator_id = $_SESSION['user'] -> user_id;
		$model -> data_inregistrare = dataora();
	}	
	
	
	$model -> save();
	copyResponse($objResponse, selectTipDoc($model -> tip_aviz, $model -> id));
	copyResponse($objResponse, switchTab("frm"));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function frmComponenta($continut_id, $aviz_id) {
	$model = new AvizeContinut($continut_id);
	
	$model -> aviz_id = $aviz_id;
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_frm_continut", "innerHTML", $model -> frmContinut());
	
	if($continut_id) {
		copyResponse($objResponse, selectProdus($model -> produs_id));
	}
	
	$objResponse -> script("
		$('#cantitate').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$('#pret_vanzare_val').focus();
					event.preventDefault();
					
				}	
			}
		);
		$('#pret_vanzare_val').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					$('#btnSalveazaComp').focus();
					event.preventDefault();
					
				}	
			}
		);
		$('#pret_vanzare_ron').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$(this).change();
					$('#btnSalveazaComp').focus();
					event.preventDefault();
					
				}	
			}
		);
	");
	
	$objResponse -> script("
		$('#pret_vanzare_val').change(
			function() {
				var pret_ron = $(this).val() * $('#curs_valutar').val();
				$('#pret_vanzare_ron').val(pret_ron.toFixed(2));
			}
		);
		$('#pret_vanzare_ron').change(
			function() {
				var pret_ron = ($(this).val() / $('#curs_valutar').val());
				$('#pret_vanzare_val').val(pret_ron.toFixed(2));
			}
		);
	");	
	return $objResponse;
}

function saveComponenta($frm, $aviz_id) {
	$model = new AvizeContinut($frm);
	$model -> aviz_id = $aviz_id;
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	if(!$frm['produs_id']) {
		return alert("Selectati un produs!");
	}
	
	if(!is_numeric($frm['cantitate'])) {
		return alert('Cantitatea trebuie sa aiba o valoare numerica');
	}
	
	$produs = new ViewProduseGestiuni("where produs_id = '". $frm['produs_id'] ."' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	
	$stoc_cant = $produs -> getStoc();
	
	if($frm['cantitate'] > $stoc_cant) {
		return alert('Cantitate insuficienta in stoc!');
	} 
		
	$model -> save();
	
	$objResponse = lista($aviz_id);
	copyResponse($objResponse, frmComponenta(0, $aviz_id));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function stergeComponenta($continut_id) {
	$model = new AvizeContinut($continut_id);
	$aviz_id = $model -> aviz_id;
	$model -> delete();
	
	$objResponse = lista($aviz_id);
	return $objResponse;
}

function inchideDocument($frm) {
	$model = new Avize($frm['aviz_id']);

	$dialog = new Dialog(800, 600, '', 'win_sumar_document');
	$dialog -> title = "Validare document";
	$dialog -> append("<fieldset><legend>Info Document</legend>");
	$dialog -> append("Numar Document: ". $model -> numar_doc);
	$dialog -> append("<br>");
	$dialog -> append("Data Document: ". c_data($model -> data_doc));
	$dialog -> append("<br>");
	$dialog -> append("Intocmit de: ". $model -> utilizator -> nume);
	$dialog -> append("</fieldset>");
	$dialog -> append("<fieldset><legend>Continut Document</legend>");
	$dialog -> append(Html::overflowDiv($model -> continut -> lista(), "300px"));
	$dialog -> append("</fieldset>");
	
	$dialog -> addButton("Valideaza", "xajax_salveazaDocument('". $model -> id ."');<%close%>");
	$dialog -> addButton("Anuleaza", "xajax_anuleazaDocument('". $model -> id ."');<%close%>");
	$dialog -> addButton("Continua Editare", "<%close%>");
	return $dialog -> open();
	
}

function salveazaDocument($aviz_id) {
	$model = new Avize($aviz_id);
	if(!count($model -> continut) && ($model -> tip_aviz == "doc_pa" || $model -> tip_aviz == "doc_pv")) {
		return alert('Documentul nu contine nici o inregistrare!');
	}
	
	$model -> salvat = 'DA';
	$model -> save();
	
	$stoc = $model -> scadStoc();
	
	$dialog = new Dialog();
	$dialog -> modal = true;
	$dialog -> append("Documentul a fost salvat");
	
	$dialog -> addButton("Tipareste", "xajax_xPrintAviz(". $aviz_id .")");
	$dialog -> addButton("Evidenta Documente", "xajax_location('". DOC_ROOT ."iesiri/evidenta_avize/');");
	$dialog -> addButton("Adauga Document Nou", "xajax_location('". DOC_ROOT ."iesiri/introducere_aviz/');");
	return $dialog -> open();
}

function anuleazaDocument($aviz_id) {
	$model = new Avize($aviz_id);
	$model -> sterge();
	
	return location(DOC_ROOT.'iesiri/evidenta_avize/');
}

function lista($aviz_id) {
	$model = new Avize($aviz_id);
	
	$objResponse = new xajaxResponse();
	switch($model -> tip_aviz) {
		case "doc_pv": {
			$continut = $model -> continut;		
			$objResponse -> assign("div_preview_continut", "innerHTML", $continut -> listaDocPv("", "xajax_frmComponenta('<%continut_id%>', $('#aviz_id').val())"));
		}break;
		case "doc_pa": {
			$continut = $model -> continut;		
			$objResponse -> assign("div_preview_continut", "innerHTML", $continut -> lista("", "xajax_frmComponenta('<%continut_id%>', $('#aviz_id').val())"));
		}break;
		case "la_factura": {
			$continut =	$model -> factura -> continut;
			$objResponse -> assign("div_continut_factura", "innerHTML", $continut -> lista("", ""));
			$objResponse -> script("$('#div_adaugare_continut').hide();");
		}break;
	}
	return $objResponse;	
}

function filtruProduse($filtru) {
	if($filtru) {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."' 
		and denumire like '$filtru%' 
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}
	else {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."'
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select());
		return $objResponse; 
	}		
}


function selectProdus($produs_id) {
	$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("produs_id", "value", $produs -> id);
	$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
	if($produs -> pret_val) $pret = $produs -> pret_val;
	else $pret = "0";
	
	$stoc = new Stocuri("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	if(count($stoc)) {
		$stoc_cant = $stoc -> stoc;
		$stoc_val = $stoc -> valoare_stoc_ron;
		$pmp = douazecimale($stoc_val/$stoc_cant);
	}
	else {
		$stoc_cant = 0;
		$stoc_val = 0;
		$pmp = 0;
	}
	
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	
	$objResponse -> assign('div_detalii_produs', 'innerHTML', '
	<strong>Ambalare:</strong>'. $produs -> ambalare .'</br> 
	<strong>Stoc:</strong> <a href="#" onClick="xajax_infoLoturi(\''. $produs_id .'\'); return false;">'. $stoc_cant .'</a><br />
	');
	
	$objResponse -> append("div_detalii_produs", "innerHTML", Html::overflowDiv($loturi -> infoLoturi(), "130px"));
	$objResponse -> assign("div_frm_unitate_masura", "innerHTML", $produs -> unitate_masura());
	$objResponse -> script("\$('#cantitate').focus().select();");
	return $objResponse;
}

function infoLoturi($produs_id) {
	$loturi = new Loturi("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and cantitate_ramasa <> 0");
	$produs = new Produse($produs_id);
	if(count($loturi)) {
		$dialog = new Dialog(600, 400, "", "win_info_loturi");
		$dialog -> title = "Loturi: ".$produs -> denumire;
		$dialog -> append($loturi -> lista());
		$objResponse = $dialog -> open();
		$objResponse -> script("$('#win_info_loturi .tablesorter').tablesorter()");
		return $objResponse;
	}
	else return alert('Nu sunt loturi inregistrate pentru acest produs');
}

function filtruClient($filtru) {
	$clienti = new Terti();
	$objResponse = new xajaxResponse();
	if($clienti -> cautare($filtru)) {
		$objResponse -> assign("div_filtru_furnizori", "innerHTML", $clienti -> selectMultiple());
	}
	$objResponse -> script("
		$('#sel_furnizor').keyup(
			function(event) {
				if(event.keyCode == 13) {
					xajax_selectClient(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
					$('#numar_doc').focus().select();
				}
			}
		);
		$('#sel_furnizor').dblclick(
			function(event) {
					xajax_selectClient(this.options[this.selectedIndex].value);
					$('#div_filtru_furnizori').hide();
					$('#numar_doc').focus().select();
			}
		);
	");
	return $objResponse;
}

function selectClient($tert_id) {
	$tert = new Terti($tert_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("tert_id", "value", $tert -> id);
	$objResponse -> assign("txtCautareFurnizor", "value", $tert -> denumire .' - '. $tert -> cod_fiscal);
	return $objResponse;
}

?>