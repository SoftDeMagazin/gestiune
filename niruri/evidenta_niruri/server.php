<?php 
require_once ("common.php");
$xajax->processRequest();

function load()
{
    $objResponse = new xajaxResponse();
    $furnizor = new Terti("where 1 order by `denumire` asc");
    $objResponse->assign("div_frm_furnizor", "innerHTML", $furnizor->select());
    return $objResponse;
}


function cautareFurnizor($frm)
{
    if ($frm['tert_id'])
        $sql = "WHERE `tert_id` = '".$frm['tert_id']."'";
    else
        $sql = "WHERE 1";
    if ($frm['from'] && $frm['end'])
    {
        $sql .= " and `data_factura` between '".data_c($frm['from'])."' and '".data_c($frm['end'])."'";
    }
    
    if ($frm['txt_numar'])
    {
        $sql .= " and numar_doc like '%".$frm['txt_numar']."%'";
    }
    
    if (!$frm['gestiune_id'])
    {
        $frm['gestiune_id'] = $_SESSION['user']->gestiuni_asociate;
    }
    
    if ($frm['gestiune_id'])
    {
        $in = "'".implode("','", $frm['gestiune_id'])."'";
        $sql .= " and gestiune_id in (".$in.")";
    }
    
    //$sql .= " and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."'";
    $sql .= " ORDER BY `nir_id` DESC";
    $facturi = new Niruri($sql);
    return afiseazaFacturi($facturi);
}


function afiseazaFacturi($niruri)
{
    $objResponse = new xajaxResponse();
    if (count($niruri))
    {
        $objResponse->assign("grid", "innerHTML", $niruri->lista("", "xajax_sumarNir('<%nir_id%>')"));
        copyResponse($objResponse, switchTab("lista"));
        copyResponse($objResponse, initControl());
        return $objResponse;
    }
    else
    {
        $objResponse = alert('Cautarea nu a returnat nici un rezultat');
        $objResponse->assign("grid", "innerHTML", "");
        return $objResponse;
    }
}

function sumarNir($nir_id)
{
    $nir = new Niruri($nir_id);
    $dialog = new Dialog(800, 600, "", "win_sumar_nir");
    $dialog->title = "Sumar NIR ".$nir->numar_nir;
    $dialog->append($nir->sumar());
    $dialog->append('<fieldset>
	<legend>Continut factura</legend>
	<div style="height:300px;overflow:scroll; overflow-x:hidden;">
	'.$nir->factura->continut->lista().'
	</div>
	</fieldset>
	');
    $dialog->addButton("Renunta");
    $dialog->addButton("Editeaza NIR", "window.location.href = '/intrari/introducere_factura/?factura_id=".$nir->factura_intrare_id."';");
    $dialog->addButton("Tiparire NIR", "xajax_xPrintNir('".$nir->id."');");
    $dialog->addButton("Anuleaza NIR", "xajax_cancel_nir('".$nir->id."');xajax_cautareFurnizor(xajax.getFormValues('frmCautareFurnizor'));<%close%>;");
    return openDialog($dialog);
}


/**
 * anuleaza un nir
 * @param object $nir_id
 * @return
 */
function cancel_nir($nir_id)
{
	global $db;
	// validare
	if(!$nir_id)
		return alert("Va rugam sa selectati un nir.");
	
    // obtin nirul
    $nir = new Niruri($nir_id);
    if (count($nir) == 0)
        return alert("Datele nu sunt consistente");
	if ($nir -> suntLoturiScazute())
        return alert("S-a consumat din loturile de pe acest nir, deci nu se poate anula.");
	$nir -> sterge();
	
	// confirm stergerea
	return alert("Nirul a fost sters!");
}	

?>
