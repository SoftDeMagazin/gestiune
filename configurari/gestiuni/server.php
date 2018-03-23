<?php 
//session_start();
header("Cache-control: private"); // IE 6 Fix
require_once ("common.php");
$xajax->processRequest();

function lista($frmFiltre = array(), $frmPager = array(), $action = "first", $selected = 0)
{
    $sql = "WHERE 1";
    if ($frmFiltre['denumire'])
    {
        $sql .= " and denumire like '%".$frmFiltre['denumire']."%'";
    }
    
    if ($frmFiltre['punct_lucru_id'])
    {
        $in = implode(",", $frmFiltre['punct_lucru_id']);
        $sql .= " and punct_lucru_id in (".$in.")";
    }
    
    $model = new Gestiuni($sql);
    if ($frmPager['pagesize'] == 1)
        $frmPager['pagesize'] = count($model);
    $model->pageLength($frmPager['pagesize']);
    
    $info = paginator($action, $model, $frmPager['curentpage']);
    $objResponse = new xajaxResponse();
    $objResponse->assign("pagedisplay", "value", $info['pagedisplay']);
    $objResponse->assign("curentpage", "value", $info['curentpage']);
    $objResponse->assign("grid", "innerHTML", $info['page']->lista("", "xajax_frm('<%gestiune_id%>')", $selected));
    $objResponse->script("\$('.tablesorter').tablesorter();");
    return $objResponse;
}


function frm($id = 0)
{
    $model = new Gestiuni($id);
    $out = $model->frmDefault();
    // putem importa doar dc s-a salvat gestiunea
    $can_import = ($id == 0) ? "disabled=true" : "";
    $objResponse = new xajaxResponse();
    $objResponse->assign("frm", "innerHTML", $out);
    $btn = '
	<p><input type="button" name="btnImport" value="Importa date pentru gestiune" id="btnImport" onclick="xajax_show_import_wizard();" '.$can_import.'></p>
	<br/>
	 <div align="right">
   		<input type="submit" name="btnSave" id="btnSave" value="Salveaza" onClick="xajax_save(xajax.getFormValues(\'frm_gestiuni\'), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'))" tabindex="6">
   		<input type="submit" name="btnCancel" id="btnCancel" value="Anuleaza" onClick="xajax_cancel();">
 	</div>
	';
    $objResponse->append("frm", "innerHTML", $btn);
	
	$objResponse -> script("
		$('#societate_id').change(
		function() {
			xajax_changeSocietate($(this).val());
		}
		)
	");
	copyResponse($objResponse, refreshSeriiNumerice($id));
    copyResponse($objResponse, switchTab('frm'));
    $objResponse->script("\$('#denumire').focus().select();");
    return $objResponse;
}

function changeSocietate($societate_id) {
	$pct = new PuncteLucru("where societate_id = '$societate_id'");
	$objResponse = new xajaxResponse();
	$objResponse -> assign("div_frm_punct_lucru", "innerHTML", $pct -> select_single());
	return $objResponse;
}

function refreshSeriiNumerice($id) {
	$objResponse = new xajaxResponse();
	if($id) {
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'facturi');
		if(count($s)) $objResponse -> assign("div_serii_facturi", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_facturi", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'facturi_proforme');
		if(count($s)) $objResponse -> assign("div_serii_facturi_proforme", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_facturi_proforme", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'niruri');
		if(count($s)) $objResponse -> assign("div_serii_niruri", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_niruri", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'transferuri');
		if(count($s)) $objResponse -> assign("div_serii_transferuri", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_transferuri", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'avize');
		if(count($s)) $objResponse -> assign("div_serii_avize", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_avize", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'deprecieri');
		if(count($s)) $objResponse -> assign("div_serii_deprecieri", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_deprecieri", "innerHTML", "Nu este asociata nici o serie numerica!");
		
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'bonuri_consum');
		if(count($s)) $objResponse -> assign("div_serii_bonuri_consum", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_bonuri_consum", "innerHTML", "Nu este asociata nici o serie numerica!");
				
		$s = new SeriiDocumente();
		$s -> getByGestiuneAndTip($id, 'transformari');
		if(count($s)) $objResponse -> assign("div_serii_transformari", "innerHTML", $s -> serie -> lista());
		else $objResponse -> assign("div_serii_transformari", "innerHTML", "Nu este asociata nici o serie numerica!");
		
	}
	return $objResponse;
}

function setSerie($gestiune_id, $tip) {
	$dialog = new Dialog(800, 600, "", "win_set_serie");
	$dialog -> title = "Selectare serie numerica pentru ".$tip;
	$dialog -> modal = true;
	$dialog -> close = FALSE;
	$serii = new SeriiNumerice("where 1");
	$dialog -> append(
	'Selectati seria numerica prin dubluclick '.
	Html::overflowDiv($serii -> lista("", "xajax_saveSerie('$gestiune_id', '<%serie_id%>', '$tip');"), "400px")
	);
	
	$dialog -> addButton("Renunta");
	return $dialog -> open();
}

function adaugSerie($gestiune_id, $tip) {
	$dialog = new Dialog(800, 600, "", "win_adauga_serie");
	$serie = new SeriiNumerice();
	$serie -> start = 0;
	$serie -> curent = 0;
	$serie -> completare_stanga = 8;
	$serie -> completez_cu = 0;
	$dialog -> append($serie -> frmDefault());
	$dialog -> addButton("Renunta");
	$dialog -> addButton("Salveaza", "xajax_saveAdaugSerie(xajax.getFormValues('frm_serii_numerice'), '$gestiune_id', '$tip')");
	return $dialog -> open();
}

function saveAdaugSerie($frm, $gestiune_id, $tip) {
	$serie = new SeriiNumerice($frm);
	$serie -> curent = $serie -> start - 1;
	$serie -> save();
	$objResponse = saveSerie($gestiune_id, $serie -> id, $tip);
	copyResponse($objResponse, closeDialog('win_adauga_serie'));
	return $objResponse;
}

function saveSerie($gestiune_id,$serie_id , $tip) {
	$objResponse = new xajaxResponse();
	$serie = new SeriiDocumente();
	$serie -> getByGestiuneAndTip($gestiune_id, $tip);
	
	if(count($serie)) {
		$serie -> serie_id = $serie_id;
		$serie -> data_adaugare = data();
		$serie -> save();
	} else {
		$serie = new SeriiDocumente();
		$serie -> gestiune_id = $gestiune_id;
		$serie -> serie_id = $serie_id;
		$serie -> tip_doc = $tip;
		$serie -> data_adaugare = data();
		$serie -> save();
	}
	
	copyResponse($objResponse, refreshSeriiNumerice($gestiune_id));
	copyResponse($objResponse, closeDialog('win_set_serie'));
	if($tip == 'facturi') {
		copyResponse($objResponse, xPrintDecizieS($gestiune_id));
	}
	return $objResponse;
}

function cancel()
{
    $objResponse = switchTab('lista');
    $objResponse->assign("frm", "innerHTML", "");
    return $objResponse;
}

function save($frmValues, $frmFiltre = array(), $frmPager = array())
{
    $model = new Gestiuni($frmValues);
    $objResponse = new xajaxResponse();
    if (!$model->validate($objResponse))
    {
        return $objResponse;
    }
	
	if(!$model -> id) {
		$gest = new Gestiuni("where denumire='". $frmValues['denumire'] ."'
		and societate_id='". $frmValues['societate_id'] ."'
		and punct_lucru_id='". $frmValues['punct_lucru_id'] ."'
		");
		if(count($gest)) {
			return alert('Ati configurat deja o gestiune cu aceiasi denumire!');
		}
	}
    $model->save();
    $objResponse = lista($frmFiltre, $frmPager, "default", $model->id);
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

function sterge($id, $frmFiltre = array(), $frmPager = array())
{

    $model = new Gestiuni($id);
    $model->delete();
    $objResponse = lista($frmFiltre, $frmPager, "default");
    copyResponse($objResponse, switchTab('lista'));
    return $objResponse;
}

// =================== IMPORT WIZARD ==============================


/**
 * afiseaza un wizard pt realizarea importului de categorii/produse/terti
 *
 * @return
 */
function show_import_wizard()
{
    $dialog = new Dialog(600, 400, '', 'import_wizard');
    $dialog->title = "Import wizard";
    
    // selectia gestiunii surse se face din gestiunile cu drepturi
    $gestiuni = new Gestiuni();
    $gestiuni->getGestiuniCuDrepturi();
    $gestiuni_html = $gestiuni->select("", "gestiune_sursa_id");
    
    // chech boxuri pt ce anume sa importe
    $import_options_html = "<br/><br/><fieldset><legend>Se importa</legend>";
    $import_options_html .= "<input type=\"checkbox\" id=\"chkCategories\" name=\"chkCategories\" value=\"Categorii\">Categorii<br/>";
    $import_options_html .= "<input type=\"checkbox\" id=\"chkProducts\" name=\"chkProducts\" value=\"Produse\">Produse<br/>";
    $import_options_html .= "<input type=\"checkbox\" id=\"chkThirds\" name=\"chkThirds\" value=\"Produse\">Terti<br/>";
    $import_options_html .= "</fieldset>";
    
    // formez tot htmlul
    $html_code = "<form id=\"frmImport\">";
    $html_code .= "Gestiune sursa:<br/>";
    $html_code .= $gestiuni_html;
    $html_code .= $import_options_html;
    $html_code .= "</form>";
    
    $js_code = "if(document.getElementById('frmImport') != null)
				 	xajax_wizard_compute_selection(xajax.getFormValues('frmImport'));
				else 
					xajax_wizard_next();";
    // adaug elementele la dialog
    $dialog->append($html_code);
    $dialog->addButton("Continua", $js_code);
    $dialog->addButton("Renunta", "<%close%>");
    $objResponse = openDialog($dialog);
    
    return $objResponse;
}

/**
 * configureaza parametrii transferului stabiliti in primul pas al wizard-ului
 *
 * @param object $frmValues
 * @return
 */
function wizard_compute_selection($frmValues = array())
{
    //------ validarea selectiilor -----------------
    
    // trebuie selectata o gestiune
    if (!$frmValues['gestiune_sursa_id'])
        return alert("Selectati o gestiune.");
        
    // trebui bifata cel putin o optiune de import
    if ($frmValues['chkProducts'] == FALSE && $frmValues['chkCategories'] == FALSE && $frmValues['chkThirds'] == FALSE)
        return alert("Selectati cel putin o optiune de import");
        
    // ----------------------------------------------
    
    // set valori in sesiune
    $_SESSION['import_gestiune'] = array("gestiune_sursa_id"=>$frmValues['gestiune_sursa_id'], "import_products"=>$frmValues['chkProducts'], "import_categories"=>$frmValues['chkCategories'], "import_thirds"=>$frmValues['chkThirds'], "last_step"=>"");
    
    // delegate next step decision
    return wizard_next();
}

/**
 * decide urmatorul pas in wizard in functie de paramtrii acestuia si de istoria lui
 * @return
 */
function wizard_next()
{
    if (isset($_SESSION['import_gestiune']) == FALSE)
        return alert("Sesiunea a expirat; Reluati procesul de import");
        
    // obtin var globala care a memorat pasul anterior al wizard-ului
    $last_step = $_SESSION['import_gestiune']['last_step'];
    $import_products = $_SESSION['import_gestiune']['import_products'];
    $import_categories = $_SESSION['import_gestiune']['import_categories'];
    $import_thirds = $_SESSION['import_gestiune']['import_thirds'];
    
    // dc sunt la primul pas
    if ($last_step == "")
    {
        //dc am selectat sa import categorii, acest import este primul
        if ($import_categories)
        {
            return wizard_categories_step();
        }
        // dc am selectat produse, fara sa selectez categorii
        else if ($import_products)
        {
            return wizard_products_step();
        }
        // am selectat doar terti
        else
        {
            return wizard_thirds_step();
        }
    }
    // am selectat categorii, urmeza sa verific dc am mai selectat si altceva
    else if ($last_step == "categories")
    {
        // dc am selectat si produse
        if ($import_products)
        {
            return wizard_products_step();
        }
        // dc am selectat doar terti
        else if ($import_thirds)
        {
            return wizard_thirds_step();
        }
    }
    // dc am avut inainte produse
    else if ($last_step == "products")
    {
        // in continuare nu pot avea decat terti
        if ($import_thirds)
        {
            return wizard_thirds_step();
        }
    }
    
    // dc am ajuns la ultimul pas din wizard, pe butonul continua vom inchide dialogul
    $objResponse = new xajaxResponse();
    $objResponse->script("$(this).dialog('close');");
    return $objResponse;
}

/**
 * construieste controalele pt import categorii in cadrul wizard-ului
 *
 * am ales sa fac mai intai import categorii, pentru ca produsele, dc vor fi importate, vor avea oricum categorii asociate
 */
function wizard_categories_step()
{
    // aducem variabilele globale de lucru
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // spunem, dupa parasirea functiei, ca ultimul pas al wizard-ului este acesta
    $_SESSION['import_gestiune']['last_step'] = "categories";
    
    // obtin categoriile de pe gestiunea sursa
    $categ_gest_sursa = new Categorii();
    $categ_gest_sursa->getByGestiuneId($gestiune_sursa_id);
    $nr_gest = count($categ_gest_sursa);
    
    // -------------- construiesc htmlul ------------
    
    $html_code = "<form id=\"frmCategories\">
				 <table cellpading=\"0\" cellspacing=\"0\">
					<tr>
						<td>
							<strong>Categorii gestiune sursa</strong>
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<br/>
							<input type=\"button\" id=\"btnMove\" name=\"btnMove\" value=\">>\" title=\"Muta\" onclick=\"xajax_wizard_save_categories($('#gestiune_id').val(),xajax.getFormValues('frmCategories'));\">
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<strong> Categorii gestiune noua </strong>
							<div id = \"categ_importate\"></div>
						</td>
					</tr>";
					
    // buton de select all
    $html_code .= "<tr>
						<td>
							<input type=\"checkbox\" id=\"chkbox_all\" value=\"\" name=\"chkbox_all\" onclick=\"CheckAll(this.form);\"> Selecteaza tot
						</td>
					</tr>
				";
				
    // iterez categoriile de pe gest si le afisez
    foreach ($categ_gest_sursa as $categ)
    {
        $html_code .= "<tr>
							<td bgcolor = \"#ffffff\">
								<input type=\"checkbox\" id=\"chkbox_".$categ->id."\" value=\"".$categ->denumire."\" name=\"chkbox_".$categ->id."\">".$categ->denumire."
							</td>
						</tr>
					";
    }
    
    $html_code .= " </table></form>";
    
    // ----------------------------------------------
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('import_wizard', 'innerHTML', $html_code);
    return $objResponse;
}

function wizard_products_step()
{
    // aducem variabilele globale de lucru
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // spunem, dupa parasirea functiei, ca ultimul pas al wizard-ului este acesta
    $_SESSION['import_gestiune']['last_step'] = "products";
    
    // obtin produsele de pe gestiunea sursa
    $produse_gest_sursa = new Produse();
    $produse_gest_sursa->getByGestiuneId($gestiune_sursa_id);
    $nr_gest = count($produse_gest_sursa);
    
    // -------------- construiesc htmlul ------------
    
    $html_code = "<form id=\"frmProducts\">
				 <table cellpading=\"0\" cellspacing=\"0\">
					<tr>
						<td>
							<strong>Produse gestiune sursa</strong>
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<br/>
							<input type=\"button\" id=\"btnMove\" name=\"btnMove\" value=\">>\" title=\"Muta\" onclick=\"xajax_wizard_save_products($('#gestiune_id').val(),xajax.getFormValues('frmProducts'));\">
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<strong> Produse gestiune noua </strong>
							<div id = \"produse_importate\"></div>
						</td>
					</tr>";
					
    // buton de select all
    $html_code .= "<tr>
						<td>
							<input type=\"checkbox\" id=\"chkbox_all\" value=\"\" name=\"chkbox_all\" onclick=\"CheckAll(this.form);\"> Selecteaza tot
						</td>
					</tr>
				";
				
    // iterez categoriile de pe gest si le afisez
    foreach ($produse_gest_sursa as $produs)
    {
        $html_code .= "<tr>
							<td bgcolor = \"#ffffff\">
								<input type=\"checkbox\" id=\"chkbox_".$produs->id."\" value=\"".$produs->denumire."\" name=\"chkbox_".$produs->id."\">".$produs->denumire."
							</td>
						</tr>
					";
    }
    
    $html_code .= " </table></form>";

    
    // ----------------------------------------------
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('import_wizard', 'innerHTML', $html_code);
    return $objResponse;
}

function wizard_thirds_step()
{
    // aducem variabilele globale de lucru
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // spunem, dupa parasirea functiei, ca ultimul pas al wizard-ului este acesta
    $_SESSION['import_gestiune']['last_step'] = "thirds";
    
    // obtin produsele de pe gestiunea sursa
    $terti_gest_sursa = new Terti();
    $terti_gest_sursa->getByGestiuneId($gestiune_sursa_id);
    $nr_gest = count($terti_gest_sursa);
    
    // -------------- construiesc htmlul ------------
    
    $html_code = "<form id=\"frmThirds\">
				 <table cellpading=\"0\" cellspacing=\"0\">
					<tr>
						<td>
							<strong>Terti gestiune sursa</strong>
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<br/>
							<input type=\"button\" id=\"btnMove\" name=\"btnMove\" value=\">>\" title=\"Muta\" onclick=\"xajax_wizard_save_thirds($('#gestiune_id').val(),xajax.getFormValues('frmThirds'));\">
						</td>
						<td rowspan=\"$nr_gest\" valign=\"top\">
							<strong> Terti gestiune noua </strong>
							<div id = \"terti_importati\"></div>
						</td>
					</tr>";
					
    // buton de select all
    $html_code .= "<tr>
						<td>
							<input type=\"checkbox\" id=\"chkbox_all\" value=\"\" name=\"chkbox_all\" onclick=\"CheckAll(this.form);\"> Selecteaza tot
						</td>
					</tr>
				";
				
    // iterez categoriile de pe gest si le afisez
    foreach ($terti_gest_sursa as $tert)
    {
        $html_code .= "<tr>
							<td bgcolor = \"#ffffff\">
								<input type=\"checkbox\" id=\"chkbox_".$tert->id."\" value=\"".$tert->denumire."\" name=\"chkbox_".$tert->id."\">".$tert->denumire."
							</td>
						</tr>
					";
    }
    
    $html_code .= " </table></form>";
    
    // ----------------------------------------------
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('import_wizard', 'innerHTML', $html_code);
    return $objResponse;
}

function wizard_save_categories($gestiune_id, $frmValues)
{
    // verific dc sesiunea este activa
    if (isset($_SESSION['import_gestiune']) == FALSE)
        return alert("Sesiunea a expirat; Reluati procesul de import");
        
    // ------------------------ PASUL 1 > Salvez categoriile pentru gestiunea noua ------------------------------------
    
    // obtin gestiunea sursa pt import
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // obtin categoriile de pe gest sursa, dintre care unele urmeaza a fi importate
    $categorii_posibile = new Categorii();
    $categorii_posibile->getByGestiuneId($gestiune_sursa_id);
    
    // pastreaza idurile categoriile ce vor fi importate
    $categorii_de_importat = array();
    
    // parcurg categ posibil si vad care a fost bifata in form
    foreach ($categorii_posibile as $categorie)
    {
        // dc a fost bifata aceasta categ o salvam pt gestiunea noua
        if ($frmValues["chkbox_".$categorie->id])
        {
            $categorii_de_importat[] = $categorie->id;
        }
    }
    
    // copiez gestiunile selectata
    $categorii_posibile->copiazaInGestiuneNoua($gestiune_id, array_2_sql_in($categorii_de_importat));
    
    // ---------------------------- END Pasul 1 -------------------------------------------------------------
    
    // ------------------------ PASUL 2 > Afisez categoriile salvate  ------------------------------------
    
    // codul html ce va fi afisat
    $html_code = "<table>";
    
    // adaug categoriile importate
    foreach ($categorii_de_importat as $c_id)
    {
        $html_code .= "<tr>
				<td bgcolor = \"#ffffff\">".$frmValues["chkbox_".$c_id]."</td>
			</tr>
		";
    }
    $html_code .= "</table>";
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('categ_importate', 'innerHTML', $html_code);
    return $objResponse;
}

function wizard_save_products($gestiune_id, $frmValues)
{
    // verific dc sesiunea este activa
    if (isset($_SESSION['import_gestiune']) == FALSE)
        return alert("Sesiunea a expirat; Reluati procesul de import");
        
    // ------------------------ PASUL 1 > Salvez categoriile pentru gestiunea noua ------------------------------------
    
    // obtin gestiunea sursa pt import
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // obtin categoriile de pe gest sursa, dintre care unele urmeaza a fi importate
    $produse_posibile = new Produse();
    $produse_posibile->getByGestiuneId($gestiune_sursa_id);
    
    // pastreaza idurile categoriile ce vor fi importate
    $produse_de_importat = array();
    
    // parcurg categ posibil si vad care a fost bifata in form
    foreach ($produse_posibile as $produs)
    {
        // dc a fost bifata aceasta categ o salvam pt gestiunea noua
        if ($frmValues["chkbox_".$produs->id])
        {
            $produse_de_importat[] = $produs->id;
        }
    }
    
    // copiez gestiunile selectata
    $produse_posibile->copiazaInGestiuneNoua($gestiune_sursa_id, $gestiune_id, array_2_sql_in($produse_de_importat));
    
    // ---------------------------- END Pasul 1 -------------------------------------------------------------
    
    // ------------------------ PASUL 2 > Afisez categoriile salvate  ------------------------------------
    
    // codul html ce va fi afisat
    $html_code = "<table>";
    
    // adaug categoriile importate
    foreach ($produse_de_importat as $c_id)
    {
        $html_code .= "<tr>
				<td bgcolor = \"#ffffff\">".$frmValues["chkbox_".$c_id]."</td>
			</tr>
		";
    }
    $html_code .= "</table>";
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('produse_importate', 'innerHTML', $html_code);
    return $objResponse;
}

function wizard_save_thirds($gestiune_id, $frmValues)
{
    // verific dc sesiunea este activa
    if (isset($_SESSION['import_gestiune']) == FALSE)
        return alert("Sesiunea a expirat; Reluati procesul de import");
        
    // ------------------------ PASUL 1 > Salvez categoriile pentru gestiunea noua ------------------------------------
    
    // obtin gestiunea sursa pt import
    $gestiune_sursa_id = $_SESSION['import_gestiune']['gestiune_sursa_id'];
    
    // obtin categoriile de pe gest sursa, dintre care unele urmeaza a fi importate
    $terti_posibili = new Terti();
    $terti_posibili->getByGestiuneId($gestiune_sursa_id);
    
    // pastreaza idurile categoriile ce vor fi importate
    $terti_de_importat = array();
    
    // parcurg categ posibil si vad care a fost bifata in form
    foreach ($terti_posibili as $tert)
    {
        // dc a fost bifata aceasta categ o salvam pt gestiunea noua
        if ($frmValues["chkbox_".$tert->id])
        {
            $terti_de_importat[] = $tert->id;
        }
    }
    
    // copiez gestiunile selectata
    $terti_posibili->copiazaInGestiuneNoua($gestiune_sursa_id, $gestiune_id, array_2_sql_in($terti_de_importat));
    
    // ---------------------------- END Pasul 1 -------------------------------------------------------------
    
    // ------------------------ PASUL 2 > Afisez categoriile salvate  ------------------------------------
    
    // codul html ce va fi afisat
    $html_code = "<table>";
    
    // adaug categoriile importate
    foreach ($terti_de_importat as $c_id)
    {
        $html_code .= "<tr>
				<td bgcolor = \"#ffffff\">".$frmValues["chkbox_".$c_id]."</td>
			</tr>
		";
    }
    $html_code .= "</table>";
    
    $objResponse = new xajaxResponse();
    $objResponse->assign('terti_importati', 'innerHTML', $html_code);
    return $objResponse;
}

// --------------------- COMMON --------------------------------

function array_2_sql_in($ids)
{
    $c = count($ids);
    if ($c == 0)
        return;
        
    $sql = "('".$ids[0]."'";
    for ($i = 1; $i < $c; $i++)
        $sql .= ",'".$ids[$i]."'";
    $sql .= ")";
    
    return $sql;
}

function get_js_code_checkall()
{
    return "
			function CheckAll(fmobj) 
			{
  				for (var i=0;i<fmobj.elements.length;i++) 
				{
    				var e = fmobj.elements[i];
    				if ( (e.name != 'chkbox_all') && (e.type=='checkbox') && (!e.disabled) ) 
					{
      					e.checked = fmobj.chkbox_all.checked;
		    		}
				}	
  			}
		";
}

// -------------------- END COMMON ------------------------------

// ==================== END IMPORT WIZARD =============================

?>
