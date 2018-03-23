<?php
require_once("common.php");
$xajax->processRequest();

function load($transfer_id=NULL) {
	$objResponse = new xajaxResponse();
	$model = new Transferuri($transfer_id);
	
	$serie = $model -> getSerie($_SESSION['user'] -> gestiune_id);
	
	if(!count($serie)) {
		$dialog = new Dialog(800, 600, "", "win_alert_serie");
		$dialog -> modal = true;
		$dialog -> close = FALSE;
		$dialog -> append("Nu ati configurat o serie numerica pentru transferuri!");
		$dialog -> addButton("Serii Numerice", "xajax_location('".DOC_ROOT."configurari/serii_numerice/');");
		return $dialog -> open();
	}
	
	if(!$transfer_id) {
		copyResponse($objResponse, selectTipDoc(0));
	} else {
		copyResponse($objResponse, selectTipDoc($transfer_id));
	}
	copyResponse($objResponse, initControl());
	return $objResponse;
}
/**
 * in functie de tip aviz imi afiseaza formularul
 * @param object $tip_aviz
 * @param object $aviz_id [optional]
 * @return 
 */
function selectTipDoc($transfer_id=0) {
	$model = new Transferuri($transfer_id);
	$objResponse = new xajaxResponse();
	if(!$transfer_id) {
		$model -> data_doc = c_data(data());
	} else {
		$model -> data_doc = c_data($model -> data_doc);
		copyResponse($objResponse, frmComponenta(0, $transfer_id));
		copyResponse($objResponse, lista($transfer_id));
	}
	$objResponse -> assign("div_frm_antet", "innerHTML", $model -> frmDefault());	
	if(!$transfer_id) {
		if(!PERMIT_GESTIUNI_EXTERNE) {
			$gest = new Gestiuni($_SESSION['user'] -> gestiune_id);
			$gestiuni = new Gestiuni("where societate_id = '". $gest -> societate_id ."'");
			$objResponse -> assign("div_frm_gestiune_destinatie", "innerHTML", $gestiuni -> select("", "gestiune_destinatie_id"));
		}
	} else {
		
		$objResponse -> assign("div_frm_gestiune_destinatie", "innerHTML", 
		'<input type="hidden" id="gestiune_destinatie_id" name="gestiune_destinatie" />
		'. $model -> gestiune_destinatie -> denumire .''
		);
	}
	$objResponse -> append("div_frm_antet", "innerHTML", '
			 <div>
		      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		        <tr>
		          <td><div align="center">
		            <label>
		            <div align="center">
		              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_transferuri\'))">
		            </div>
		            </label>
		          </div>
		          </td>
		          </tr>
		      </table>
		    </div>
		
			');
	copyResponse($objResponse, initControl());
	return $objResponse;
}

function salveazaAntet($frm)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$objResponse = new xajaxResponse();
	$model = new Transferuri($frm);
	$model -> data_doc = data_c($model -> data_doc);
	if(!$frm['transfer_id']) {
		$model -> numar_doc = $model -> getNumar($_SESSION['user']-> gestiune_id);
		
		$model -> incrementSerie($_SESSION['user']-> gestiune_id);
		
		$serie = $model -> getSerie($_SESSION['user']-> gestiune_id);
		$model -> serie_id = $serie -> id;
		
		$model -> gestiune_id = $_SESSION['user'] -> gestiune_id;
		$model -> utilizator_id = $_SESSION['user'] -> user_id;
		$model -> data_inregistrare = dataora();
	}	
	
	if($model -> gestiune_id == $model -> gestiune_destinatie_id) {
		return alert("Nu puteti transfera in aceiasi gestiune!");
	}
	$model -> save();
	copyResponse($objResponse, selectTipDoc($model -> id));
	copyResponse($objResponse, frmComponenta(0,$model -> id));
	copyResponse($objResponse, switchTab("frm"));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	$objResponse -> script("is_saved = false");
	return $objResponse;
}

function frmComponenta($continut_id, $transfer_id) {
	$model = new TransferuriContinut($continut_id);
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_frm_continut", "innerHTML", $model -> frmContinut());
	
	if($continut_id) {
		copyResponse($objResponse, selectProdus($model -> produs_id));
	}
	
	$objResponse -> script("
		$('#cantitate').keypress(
			function(event) {
				if(event.keyCode == 13) {
					$('#btnSalveazaComp').focus();
					event.preventDefault();		
				}	
			}
		);
		$('#cantitate').change(
			function() {
				$('#btnSalveazaComp').focus();
			}
		);
	");
	return $objResponse;
}

function calculeazaPretMediu($produs_id, $cantitate) {
	$produs = new Produse($produs_id);
	$pmp = $produs -> getPretMediuAchizitie($_SESSION['user'] -> gestiune_id);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_frm_pret_mediu", "innerHTML", $pmp);
	return $objResponse;
}

function saveComponenta($frm, $transfer_id) {
	$model = new TransferuriContinut($frm);
	$model -> transfer_id = $transfer_id;
	if(!$_SESSION['user']) {
		return xLogin();
	}
	

	
	if(!$frm['produs_id']) {
		return alert("Selectati un produs!");
	}
	
	if(!$frm['continut_id']) {
		$cnt = new TransferuriContinut("where transfer_id = '$transfer_id' and produs_id='". $frm['produs_id'] ."'");
		if(count($cnt)) {
			$objResponse = alert('Produsul exista deja in lista de componente. <br> Editati inregistrarea!');
			copyResponse($objResponse, frmComponenta(0, $transfer_id));
			return $objResponse;
		}
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
	
	$objResponse = lista($transfer_id);
	copyResponse($objResponse, frmComponenta(0, $transfer_id));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function stergeComponenta($continut_id) {
	$model = new TransferuriContinut($continut_id);
	$transfer_id = $model -> transfer_id;
	$model -> delete();
	
	$objResponse = lista($transfer_id);
	return $objResponse;
}

function inchideDocument($frm) {
	$model = new Transferuri($frm['transfer_id']);

	$dialog = new Dialog(800, 600, '', 'win_sumar_document');
	$dialog -> title = "Validare document";
	$dialog -> append($model -> sumar());
	$dialog -> append("<fieldset><legend>Continut Document</legend>");
	$dialog -> append(Html::overflowDiv($model -> continut -> lista(), "300px"));
	$dialog -> append("</fieldset>");
	
	$dialog -> addButton("Valideaza", "xajax_salveazaDocument('". $model -> id ."');<%close%>");
	$dialog -> addButton("Anuleaza", "xajax_anuleazaDocument('". $model -> id ."');<%close%>");
	$dialog -> addButton("Continua Editare", "<%close%>");
	return $dialog -> open();
	
}

function salveazaDocument($transfer_id) {
	$model = new Transferuri($transfer_id);

	$model -> salvat = 'DA';
	$model -> save();
	
	$stoc = $model -> validareDocument(); 
	
	$dialog = new Dialog();
	$dialog -> modal = true;
	$dialog -> close = false;
	
	$dialog -> append("Transferul a fost salvat");

	if($model -> gestiune -> societate_id != $model -> gestiune_destinatie -> societate_id) {
		$dialog -> addButton("Tipareste Factura", "xajax_xPrintFactura(". $model -> doc_id .")");
	} else {
		if(TRANSFER_EMIT_AVIZ) {
			$dialog -> addButton("Tipareste Aviz", "xajax_xPrintAviz(". $model -> doc_id .")");
		} else {
			$dialog -> addButton("Tipareste Transfer", "xajax_xPrintNotaTransfer(". $model -> id .")");
		}
	}
	$dialog -> addButton("Tipareste Nir", "xajax_xPrintNir(". $model -> nir_id .")");
	$dialog -> addButton("Evidenta Transferuri", "xajax_location('". DOC_ROOT ."iesiri/evidenta_transferuri/');");
	$dialog -> addButton("Adauga Transfer Nou", "xajax_location('". DOC_ROOT ."iesiri/introducere_transfer/');");
	$objResponse = $dialog -> open();
	$objResponse -> script("is_saved = true;");
	return $objResponse;
}

function importa_stoc($transfer_id) {
	global $db;
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$transfer = new Transferuri($transfer_id);
	$transfer -> stergeContinut();

	$gestiune_id = $_SESSION['user'] -> gestiune_id;
	$sql = "
	insert into transferuri_continut (transfer_id, produs_id, cantitate) (
	select '$transfer_id' as transfer_id, produs_id, stoc from stocuri where gestiune_id = '$gestiune_id' and stoc > 0
	)
	";
	$db -> query($sql);
	$objResponse = lista($transfer_id);

	return $objResponse;
}

function anuleazaDocument($transfer_id) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	$model = new Transferuri($transfer_id);
	$model -> sterge();
	
	$serie = $model -> getSerie($_SESSION['user'] -> gestiune_id);
	if(count($serie)) $serie -> decrement(); 
	$objResponse = new xajaxResponse();
	$objResponse -> script("is_saved = true;");
	copyResponse($objResponse, location(DOC_ROOT.'iesiri/evidenta_transferuri/'));
	return $objResponse;
}

function lista($transfer_id) {
	$model = new Transferuri($transfer_id);
	
	$objResponse = new xajaxResponse();
	$continut = $model -> continut;		
	$objResponse -> assign("div_preview_continut", "innerHTML", $continut -> lista("", "xajax_frmComponenta('<%continut_id%>', $('#aviz_id').val())"));
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
		$objResponse -> alert('Salut');
		$objResponse -> assign("cantitate", "value", $filtru);
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
		//$pmp = douazecimale($stoc_val/$stoc_cant);
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

?>