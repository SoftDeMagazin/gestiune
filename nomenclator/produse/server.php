<?php
require_once("common.php");
$xajax->processRequest();

function lista($frmFiltre=array(), $frmPager=array(), $action="first", $selected=0)
{
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$model = new ViewProduseGestiuni();
	$sql = " where gestiune_id = ". $_SESSION['user'] -> gestiune_id ." ";
	if($frmFiltre['denumire']) {
		$sql .= " and denumire like '%". $frmFiltre['denumire'] ."%'";
	}

	if(isset($frmFiltre['categorie_id']) && $frmFiltre['categorie_id'][0]) {
		$in = implode(",", $frmFiltre['categorie_id']);
		$sql .= " and categorie_id in (". $in .")";
	}

	if(isset($frmFiltre['tip_produs']) && $frmFiltre['tip_produs'][0]) {
		$in = "'".implode("','", $frmFiltre['tip_produs'])."'";
		$sql .= " and tip_produs in (". $in .")";
	}
		
	$sql .= " order by denumire asc";
	$model -> prepareQuery($sql);

	if($frmPager['pagesize'] == 1) $frmPager['pagesize'] = $model -> expectedResult();
	
	$info = paginated($action, $model, $frmPager['curentpage'], $frmPager['pagesize']);
	$objResponse = new xajaxResponse();
	$objResponse -> assign("pagedisplay", "value", $info['pagedisplay']);
	$objResponse -> assign("curentpage", "value", $info['curentpage']);
	$objResponse -> assign("grid", "innerHTML", $info['page'] -> lista("", "$('#tabs').tabs('enable', 1);xajax_frm('<%produs_id%>')", $selected));
	$objResponse -> script("\$('.tablesorter').tablesorter();");
	return $objResponse;
}


function listaRetete($produs_id) {
	$retetare = new Retetar("where componenta_id = '$produs_id'");
	
	$dialog = new Dialog(800, 600, "", "win_lista_retete");
	$dialog -> append(Html::overflowDiv($retetare -> listaReteteDenumiri(), "400px"));
	return $dialog -> open();
}

function selectProdus($produs_id) {
	$objResponse = new xajaxResponse();
	if($produs_id) {
		$produs = new Produse($produs_id);
		$objResponse -> assign("componenta_id", "value", ''.$produs -> id.'');
		$objResponse -> assign("div_info_produs", "innerHTML", ''.$produs -> denumire.'');
		$objResponse -> assign("info_um", "innerHTML", $produs -> unitate_masura -> denumire);
	} else {
		$objResponse -> assign("componenta_id", "value", '0');
		$objResponse -> assign("div_info_produs", "innerHTML", '&nbsp;');
		$objResponse -> assign("info_um", "innerHTML", '&nbsp;');
	}
		
	$objResponse -> script("$('#cantitate').focus().select();");
	return $objResponse;
}

function filtruProduse($filtru) {
	if($filtru) {
		$produse = new ViewProduseGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and denumire like '$filtru%' order by denumire asc");
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select(20));
		return $objResponse; 
	}
	else {
		$produse = new ViewProduseGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
		$objResponse = new xajaxResponse();
		$objResponse -> assign("div_select_produse", "innerHTML", $produse -> select(20));
		return $objResponse; 
	}		
}


function frm($id=0)
{
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	if($id) {
	$model = new ViewProduseGestiuni("where produs_id = '". $id ."' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	}
	else {
	$model = new ViewProduseGestiuni();
	}
	$out = $model -> frmDefault();
	$objResponse = new xajaxResponse();
	$objResponse -> script("$('#tabs').tabs('enable', 1);$('#tabs').tabs('enable', 3);");
	copyResponse($objResponse, switchTab('frm'));

	$objResponse -> assign("frm", "innerHTML", $out);
	$btn = '
	 <div align="right">
   		<input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_view_produse_gestiuni\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'));this.disabled = true;" tabindex="6">
   		<input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
		<input type="submit" name="btnRetete" id="btnRetete" value="Retete" onClick="xajax_listaRetete(\''.$model -> id.'\');">
 	 </div>
	';
	$objResponse -> append("frm", "innerHTML", $btn);
	if($id) {
		$ap = new AgentiProduse("where produs_id = '$id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' ");
		$objResponse -> assign("comisioane", "innerHTML", ' 
		<fieldset>
			<legend>Comisioane agenti</legend>
			<input type="button" id="btnAddAgent" value="Adauga Comision Agent" onClick="xajax_adaugaComisionAgent($(\'#produs_id\').val())"> 
			<form id="frm_comisioane" onSubmit="return false;">
			'. Html::overflowDiv($ap -> lista(), "150px", "" , array("id" => "lista_comisioane_agenti")) .'
			</form>
			<div align="right">	
			<input type="button" id="btnSalveazaComisioane" value="Salveaza Comisioane"  onClick="xajax_salveazaComisioane(xajax.getFormValues(\'frm_comisioane\'))"> 
			</div>
		</fieldset>	
		'
	);
	}
	
	$gest = new Gestiuni("where 1");
	if($id) {
		$selected = $model -> getGestiuniAsociate();
	}
	else {
		$selected = array($_SESSION['user'] -> gestiune_id);
	}
	
	$categorii = new Categorii();
	$categorii -> getByGestiuneId($_SESSION['user'] -> gestiune_id);

	

	
	$objResponse -> assign("div_frm_gest", "innerHTML", "Gestiune<br />".$gest -> selectMulti($selected));
	$objResponse -> script("$('#gestiune_id').multiSelect();");
	
	$objResponse -> assign("div_frm_categorie", "innerHTML", $categorii -> select_single($model -> categorie_id));
	
	
	
	$objResponse -> script("\$('#denumire').focus().select();");
	if(($model -> tip_produs == "reteta" || $model -> tip_produs == 'pf') && $model -> id) {
		$objResponse -> script("$('#tabs').tabs('enable', 2);");
		copyResponse($objResponse, listaComponente($model -> id));
		copyResponse($objResponse, frmComponenta(0, $model -> id));
		copyResponse($objResponse, pretAchizitieReteta($model -> id));
	}
	

	copyResponse($objResponse, initControl());
	$curs = new Cursuri();
	$curs -> getLast();
	
	if($id) {
		if($model -> pret_referinta == "LEI") {
			$objResponse -> script("$('#pret_val').attr('readonly', true);");
		} else {
			$objResponse -> script("$('#pret_ron').attr('readonly', true);");
		}
	} else {
		if(PRET_REFERINTA_DEFAULT == "LEI") {
			$objResponse -> script("$('#pret_referinta_LEI').attr('checked', true);");
			$objResponse -> script("$('#pret_val').attr('readonly', true);");
		} else {
			$objResponse -> script("$('#pret_referinta_EUR').attr('checked', true);");
			$objResponse -> script("$('#pret_ron').attr('readonly', true);");
		}
	}
	if(count($curs)) {
		$objResponse -> script("
			$('#pret_ron').change(
				function() {
					var pret = $(this).val()/". $curs -> valoare .";
					$('#pret_val').val(pret.toFixed(2));
				}
			);
			$('#pret_val').change(
				function() {
					var pret = $(this).val()*". $curs -> valoare .";
					$('#pret_ron').val(pret.toFixed(2));
				}
			);
		");
		
		$objResponse -> script("
			$('.pret_referinta').click(
				function() {
					switch($(this).val()) {
						case 'LEI': {
							$('#pret_ron').attr('readonly', false);
							$('#pret_val').attr('readonly', true);
						}break;
						case 'EUR': {
							$('#pret_ron').attr('readonly', true);
							$('#pret_val').attr('readonly', false);
						}break;
					}
				}
			);
		");
	}
	
	$terti = new ViewTertiGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse -> assign("div_furnizor", "innerHTML", $terti -> select($model -> tert_id));
	return $objResponse;
}

function pretAchizitieReteta($produs_id) {
	$objResponse = new xajaxResponse();
	$produs = new Produse($produs_id);
	$objResponse -> assign("pret_achizitie_reteta", "innerHTML", treizecimale($produs -> getPretAchReteta($_SESSION['user'] -> gestiune_id)));
	return $objResponse;
}

function listaComisioaneAgenti($produs_id) {
	$ap = new AgentiProduse("where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("lista_comisioane_agenti", "innerHTML", $ap -> lista());
	return $objResponse;
}

function adaugaComisionAgent($produs_id) {
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$agenti = new AgentiGestiuni("where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' and agent_id not in (select agent_id from agenti_produse where produs_id = '$produs_id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."')");
	
	if(!count($agenti)) {
		return alert('Toti agentii au fost asociati');
	}
	
	$ap = new AgentiProduse();
	$ap -> produs_id = $produs_id;
	$ap -> gestiune_id = $_SESSION['user'] -> gestiune_id;
	
	$dialog = new Dialog(400, 300, "", "win_adauga_agent"); 
	
	$dialog -> append('<form id="frm_agenti_produse" onSubmit="return false;">');
	$dialog -> append('<label> Agent <br> '. $agenti -> select() .' '. $ap -> produs_id() .' '. $ap -> gestiune_id() .'</label><br>');
	$dialog -> append('<label> Comision <br> '. $ap -> comision() .' </label>');
	$dialog -> append('</form>');
	
	$dialog -> addButton('Renunta');
	$dialog -> addButton('Salveaza', "xajax_salveazaComisionAgent(xajax.getFormValues('frm_agenti_produse'));<%close%>");
	
	return $dialog -> open();
}

function salveazaComisionAgent($frmValues) {
	$ap = new AgentiProduse($frmValues);
	$ap -> save();
	return listaComisioaneAgenti($ap -> produs_id);
}

function stergeComisionAgent($id) {
	$ap = new AgentiProduse($id);
	$produs_id = $ap -> produs_id;
	$ap -> delete();
	return listaComisioaneAgenti($produs_id);
}

function salveazaComisioane($frm) {
	$nr_r = count($frm['agent_produs_id']);
	for($i=0;$i<$nr_r;$i++) {
		$ap = new AgentiProduse($frm['agent_produs_id'][$i]);
		$produs_id = $ap -> produs_id;
		$ap -> comision = douazecimale($frm['comision'][$i]);
		$ap -> save();
	}
	return listaComisioaneAgenti($produs_id);
}

function cancel() 
{
	$objResponse = switchTab('lista');
	$objResponse -> assign("frm", "innerHTML", "");
	$objResponse -> script("$('#tabs').tabs('disable', 1);$('#tabs').tabs('disable', 2);$('#tabs').tabs('disable', 3);");
	return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array()) 
{
	global $db;
	$model = new Produse($frmValues);
	$objResponse = new xajaxResponse();
	
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	if(!$frmValues['gestiune_id']) {
		return alert('Asociati produsul cu cel putin o gestiune');
	}
	
	if(!$frmValues['produs_id'] && $model -> tip_produs == 'mp') {
		$model -> denumire .= ' MP';
	}
	
	$model -> save();
	
	$model -> disociazaGestiuni($frmValues['gestiune_id']);
	$model -> asociazaCuGestiuni($frmValues['gestiune_id'], array("pret_ron"=>$frmValues['pret_ron'],"pret_val"=>$frmValues['pret_val'], "stoc_minim" => $frmValues['stoc_minim']));
	
	$pg = new ProduseGestiuni("where produs_id = '". $model -> id ."' and gestiune_id = ". $_SESSION['user'] -> gestiune_id ."");
	$pg -> pret_ron = $frmValues['pret_ron'];
	$pg -> pret_val = $frmValues['pret_val'];
	$pg -> stoc_minim = $frmValues['stoc_minim'];
	$pg -> modificat = 1;
	if($frmValues['tert_id']) {
		$pg -> tert_id = $frmValues['tert_id'];
	}
	$pg -> save();	
	
	//asociere automata a categoriei cu gestiunea 
	$categorie = $model -> categorie;
	$categorie -> asociazaCuGestiuni($frmValues['gestiune_id']);
	
	$objResponse = lista($frmFiltre, $frmPager, "default", $model -> id);
	copyResponse($objResponse, switchTab('lista'));
	$objResponse -> script("$('#tabs').tabs('disable', 1);$('#tabs').tabs('disable', 2);$('#tabs').tabs('disable', 3);");
	return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array()) {
	
	global $db;
	$retetar = new Retetar("where componenta_id = '$id'");
	if(count($retetar)) {
		$out = 'Produsul este configurat ca materie prima pentru retetele:<br>';
		foreach($retetar as $ret) {
			$out .= $ret -> produs -> denumire.'<br/>';
		}
		return alert($out);
	}
	$model = new ProduseGestiuni("where produs_id = '$id' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."';");
	$model -> delete();
	$objResponse = lista($frmFiltre, $frmPager, "default");
	copyResponse($objResponse, switchTab('lista'));
	return $objResponse;
}




function listaComponente($produs_id) {
	$retetar = new Retetar("where produs_id = '$produs_id'");
	$objResponse = new xajaxResponse();
	if(count($retetar)) {
		$objResponse -> assign("div_lista_componente", "innerHTML", $retetar -> lista("", "xajax_frmComponenta('<%retetar_id%>');"));
	}
	else {
		$objResponse -> assign("div_lista_componente", "innerHTML", "Nu sunt componente!");
	}
	return $objResponse;
}

function frmComponenta($retetar_id, $produs_id=0) {
	$retetar = new Retetar($retetar_id);
	if(!$retetar -> id) {
		$retetar -> produs_id = $produs_id;
	}
	$objResponse = selectProdus($retetar -> componenta_id);
	$objResponse -> assign("div_componenta", "innerHTML", $retetar -> frmDefault());
	$objResponse -> script("
	$('#cantitate').keypress(
		function(event) { 
			if(event.keyCode == 13) {
				$('#btnSalveazaComponenta').focus(); 
				event.preventDefault();
			}
	});");
	return $objResponse;
		
}

function salveazaComponenta($frmValues) {
	if(!$frmValues['componenta_id']) {
		return alert('Selectati un produs');
	}
	
	if($frmValues['produs_id'] ==  $frmValues['componenta_id']) {
		return alert('Produsul nu poate fi componenta');
	}
	$retetar = new Retetar($frmValues);
	$retetar -> save();
	$objResponse = listaComponente($retetar -> produs_id);
	copyResponse($objResponse, frmComponenta(0, $retetar -> produs_id));
	copyResponse($objResponse, pretAchizitieReteta($frmValues['produs_id']));
	$objResponse -> script("$('#cautare_produs').focus().select();");
	return $objResponse;
}

function stergeComponenta($retetar_id) {
	$comp = new Retetar($retetar_id);
	$produs_id = $comp -> produs_id;
	$comp -> delete();
	$objResponse = listaComponente($comp -> produs_id);
	copyResponse($objResponse, pretAchizitieReteta($produs_id));
	$objResponse -> script("$('#cautare_produs').focus().select();");
	return $objResponse;
}

function listeaza($produs_id=0) {
	if(!$_SESSION['user']) {
		return xLogin();
	}
	
	$dialog = new Dialog(800, 600, '', 'win_print_stoc');
	$dialog -> title = "Printare produse";
	$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
	
	$dialog -> append(Html::form("frmPrintStoc", array("action" => DOC_ROOT."print/raport.php", "target" => "print", "method" => "post", "onSubmit" => "return popup('', this.target);")));
	$dialog -> append(Html::hidden("rpt_name", "RptNomenclatorProduse"));
	$dialog -> append($gestiune -> gestiune_id());
	
	$categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
	$txt = $categorii -> select("");

	$dialog -> append("Categorie<br>".$txt);
	
	$tip = new TipuriProduse("where 1 order by descriere asc");
	$txt = $tip -> select("");
	
	$dialog -> append("<br>Tip Produs<br>".$txt);
	
	
	$dialog -> append("<br>".Html::submit("print", "Print"));
	
	$dialog -> append(Html::formEnd());
	
	
	$objResponse = $dialog -> open();
	$objResponse -> script("$('#frmPrintStoc #categorie_id').multiSelect()");
	$objResponse -> script("$('#frmPrintStoc #tip_produs').multiSelect()");
	return $objResponse;
}

function importaProdus() {
	$dialog = new Dialog(800, 600, '', 'win_importa_produs');
	$dialog -> title = "Importa Produs";
	$gest = new Gestiuni();
	$gest -> getGestiuniCuDrepturi($_SESSION['user']-> user_id);
	$dialog -> append("Cautare: <input type");
	$dialog -> append("Din gestiunea: ".$gest -> select());
	
	return $dialog -> open();
}
?>