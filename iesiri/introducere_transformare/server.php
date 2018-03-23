<?php
require_once("common.php");
$xajax->processRequest();

function load($transformare_id=NULL) {
	$objResponse = new xajaxResponse();
	$transformare = new Transformari($transformare_id);
	$serie = $transformare -> getSerie($_SESSION['user'] -> gestiune_id);
	
	if(!count($serie)) {
		$dialog = new Dialog(800, 600, "", "win_alert_serie");
		$dialog -> modal = true;
		$dialog -> close = FALSE;
		$dialog -> append("Nu ati configurat o serie numerica pentru transformari!");
		$dialog -> addButton("Serii Numerice", "xajax_location('".DOC_ROOT."configurari/serii_numerice/');");
		return $dialog -> open();
	}
	
	if(!$transformare_id) {
		$transformare -> data_doc = c_data(data());		
	} else {
		$transformare -> data_doc = c_data($transformare -> data_doc);
	}
	
	$objResponse -> assign("div_frm_transformare", "innerHTML", $transformare -> frmDefault());
	$objResponse -> append("div_frm_transformare", "innerHTML", '
	   <div>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div align="center">
            <label>
            <div align="center">
              <input type="button" name="btnSalveaza" id="btnSalveaza" value="Salveaza" onClick="xajax_salveazaAntet(xajax.getFormValues(\'frm_transformari\'))">
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
	$model = new Transformari($frm);
	$model -> data_doc = data_c($model -> data_doc);
	if(!$frm['transformare_id']) {
		$model -> numar_doc = $model -> getNumar($_SESSION['user']-> gestiune_id);
		$model -> setSerieId($_SESSION['user']-> gestiune_id);
		
		$model -> incrementSerie($_SESSION['user']-> gestiune_id);
	
		$model -> gestiune_id = $_SESSION['user'] -> gestiune_id;
		$model -> utilizator_id = $_SESSION['user'] -> user_id;
		$model -> data_inregistrare = dataora();
	}	
	$model -> save();
	
	$pf = $model -> adaugaProdusFinit(0, 0);
	copyResponse($objResponse, load($model -> id));
	copyResponse($objResponse, switchTab("frm"));
	$objResponse -> script("$('#cautare_produs').focus().select()");
		$objResponse -> assign("trans_pf_id", "value", $pf -> id);
	$objResponse -> script("$('#btnSalveazaPf').attr('disabled', true);");
	$objResponse -> script("$('#continut_materiale').css('display', 'block');");
	copyResponse($objResponse, frmMateriePrima(0, $pf -> id));
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
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> selectTransformari());
		return $objResponse; 
	}
	else {
		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."'
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> selectTransformari());
		return $objResponse; 
	}		
}

function salveazaProdusFinit($transformare_id, $frm) {
	$transformare = new Transformari($transformare_id);
	$pf = $transformare -> adaugaProdusFinit($frm['pf_produs_id'], $frm['cantitate_pf']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("trans_pf_id", "value", $pf -> id);
	$objResponse -> script("$('#btnSalveazaPf').attr('disabled', true);");
	$objResponse -> script("$('#continut_materiale').css('display', 'block');");
	copyResponse($objResponse, frmMateriePrima(0, $pf -> id));
	return $objResponse;
}

function saveComponenta($frm, $trans_pf_id) {
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$mp = new TransformariMp($frm);
	$pf = new TransformariPf($trans_pf_id);
	$mp -> trans_pf_id = $trans_pf_id;
	$mp -> transformare_id = $pf -> transformare_id;
	
	$produs = new ViewProduseGestiuni("where produs_id = '". $frm['produs_id'] ."' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	
	$stoc_cant = $produs -> getStoc();
	
	if($frm['cantitate'] > $stoc_cant) {
		return alert('Cantitate insuficienta in stoc!');
	} 
	
	$mp -> save();
	$objResponse = lista($mp -> transformare_id);
	copyResponse($objResponse, frmMateriePrima(0, $trans_pf_id));
	$objResponse -> script("$('#cautare_produs').focus().select()");
	return $objResponse;
}

function stergeMateriePrima($trans_mp_id) {
	if(!$trans_mp_id) {
		return alert("Nu ati selectat o componenta!");
	}
	
	$produs = new Produse();
	$mp = new TransformariMp($trans_mp_id);
	$transformare_id = $mp -> transformare_id;
	$mp -> delete();
	$objResponse = lista($transformare_id);
	return $objResponse;
}

function lista($transformare_id) {
	$objResponse = new xajaxResponse();
	$transformare = new Transformari($transformare_id);
	$objResponse -> assign("div_preview_continut", "innerHTML", $transformare -> continut_mp -> lista("", "xajax_frmMateriePrima('<%trans_mp_id%>', $('#trans_pf_id').val())"));
	$objResponse -> assign("total_materiale", "value", douazecimale($transformare -> getTotalMateriale()));
	return $objResponse;
}

function frmMateriePrima($trans_mp_id, $trans_pf_id) {
	$mp = new TransformariMp($trans_mp_id);

	$objResponse = new xajaxResponse();

	$objResponse -> assign("div_frm_continut", "innerHTML", $mp -> frmContinut());
	if($trans_mp_id) {
		copyResponse($objResponse, selectProdus($mp -> produs_id, $trans_pf_id));
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

function selectProdus($produs_id, $trans_pf_id=0) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$produs = new ViewProduseGestiuni("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	if($trans_pf_id) {
		$objResponse = new xajaxResponse();
		$objResponse -> assign("produs_id", "value", $produs -> id);
		$objResponse -> assign("div_info_produs", "innerHTML", $produs -> denumire);
		if($produs -> pret_val) $pret = $produs -> pret_val;
		else $pret = "0";
	} else {
		$objResponse = new xajaxResponse();
		$objResponse -> assign("pf_produs_id", "value", $produs -> id);
		$objResponse -> assign("produs_finit", "value", $produs -> denumire);
		if($produs -> pret_val) $pret = $produs -> pret_val;
		else $pret = "0";
	}
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
	if(!$trans_pf_id) 
		$objResponse -> script("\$('#cantitate_pf').focus().select();");
	else 
		$objResponse -> script("\$('#cantitate').focus().select();");
	return $objResponse;
}

function infoLoturi($produs_id) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
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