<?php
require_once("common.php");
$xajax->processRequest();

function load($depreciere_id=NULL) {
	$objResponse = new xajaxResponse();
	$depreciere = new Deprecieri($depreciere_id);
	$serie = $depreciere -> getSerie($_SESSION['user'] -> gestiune_id);
	
	if(!count($serie)) {
		$dialog = new Dialog(800, 600, "", "win_alert_serie");
		$dialog -> modal = true;
		$dialog -> close = FALSE;
		$dialog -> append("Nu ati configurat o serie numerica pentru deprecieri!");
		$dialog -> addButton("Serii Numerice", "xajax_location('".DOC_ROOT."configurari/serii_numerice/');");
		return $dialog -> open();
	}
	
	if(!$depreciere_id) {
		$depreciere -> data_doc = c_data(data());		
	} else {
		$depreciere -> data_doc = c_data($depreciere -> data_doc);
		copyResponse($objResponse, lista($depreciere_id));
		copyResponse($objResponse, frmComponenta(0, $depreciere_id));
	}
	
	$objResponse -> assign("div_frm_depreciere", "innerHTML", $depreciere -> frmDefault());
	$objResponse -> append("div_frm_depreciere", "innerHTML", '
	   <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_deprecieri\'))">
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
	$model = new Deprecieri($frm);
	$model -> data_doc = data_c($model -> data_doc);
	if(!$frm['depreciere_id']) {
		$model -> numar_doc = $model -> getNumar($_SESSION['user']-> gestiune_id);
		$model -> incrementSerie($_SESSION['user']-> gestiune_id);
		$serie = $model -> getSerie($_SESSION['user'] -> gestiune_id);
		$model -> serie_id = $serie -> id;
		$model -> gestiune_id = $_SESSION['user'] -> gestiune_id;
		$model -> utilizator_id = $_SESSION['user'] -> user_id;
		$model -> data_inregistrare = dataora();
	}	
	$model -> save();
	copyResponse($objResponse, load($model -> id));
	copyResponse($objResponse, frmComponenta(0, $model -> id));
	copyResponse($objResponse, switchTab("frm"));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function frmComponenta($continut_id, $depreciere_id) {
	$model = new DeprecieriContinut($continut_id);
	
	
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
	");
	return $objResponse;
}

function saveComponenta($frm, $depreciere_id) {
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$model = new DeprecieriContinut($frm);
	$model -> depreciere_id = $depreciere_id;
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
	
	$objResponse = lista($depreciere_id);
	copyResponse($objResponse, frmComponenta(0, $depreciere_id));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function stergeComponenta($continut_id) {
	$model = new DeprecieriContinut($continut_id);
	$depreciere_id = $model -> depreciere_id;
	$model -> delete();
	
	$objResponse = lista($depreciere_id);
	return $objResponse;
}

function inchideDocument($frm) {
	$model = new Deprecieri($frm['depreciere_id']);

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

function salveazaDocument($depreciere_id) {
	$model = new Deprecieri($depreciere_id);
	$model -> salvat = 'DA';
	$model -> save();
	$model -> anulareScaderi();
	$model -> scadStoc();
	
	$dialog = new Dialog();
	$dialog -> modal = true;
	$dialog -> append("Documentul a fost salvat");
	
	$dialog -> addButton("Tipareste", "xajax_xPrintDepreciere('$depreciere_id')");
	$dialog -> addButton("Evidenta Documente", "xajax_location('". DOC_ROOT ."iesiri/evidenta_deprecieri/');");
	$dialog -> addButton("Adauga Document Nou", "xajax_location('".DOC_ROOT."iesiri/introducere_depreciere/');");
	return $dialog -> open();
}

function anuleazaDocument($depreciere_id) {
	$model = new Deprecieri($depreciere_id);
	$model -> sterge();
	
	return location(DOC_ROOT.'iesiri/evidenta_deprecieri/');
}

function lista($depreciere_id) {
	$depreciere = new Deprecieri($depreciere_id);
	$continut = $depreciere -> continut;
	
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_preview_continut", "innerHTML", $continut -> lista("", "xajax_frmComponenta('<%depreciere_continut_id%>', $('#depreciere_id').val())"));
	return $objResponse;
	
}

function filtruProduse($filtru) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
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
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
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

?>