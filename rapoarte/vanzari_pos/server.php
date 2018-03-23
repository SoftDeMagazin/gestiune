<?php 
require_once ("common.php");
$xajax->processRequest();


// ******************** GENERARE RAPORT ************************

/**
 * incarca vanzarile pe posuri in functie de filtre
 * @param array $filtre valori din form-ul de filtrare
 * @author cosmin
 * @return html string
 */
function load($filtre)
{
    $rpt = new RptVanzariPos($filtre);
    $objResponse = new xajaxResponse();
    $objResponse->assign("grid", "innerHTML", $rpt->getHtml());
    return $objResponse;
}

// ******************** END GENERARE RAPORT ************************

//****************** LOAD MULTISELECTS *********************

/**
 * incarca societatile
 *
 * se incarca toate societatile pentru ale caror gestiuni utilizatorul are drepturi (tabela gestiuni_utilizatori)
 * daca un utilizator este superadmin, el va avea drepturi pe toate gestiunile, deci va putea crea rapoarte pe toate societatile
 *
 * @author cosmin
 * @return html societati
 */
function load_societati()
{
    //obtinem societatile cu drepturi
    $societati_ids = get_societati_cu_drepturi();
    
    if (count($societati_ids) == 0)
        return;
        
    //transformam array-ul intr-o sintaxa pt instructiunea IN din sql
    $sql = "";
    $sql .= array_2_sql_in($societati_ids);
    
    //adaugam societatile
    $societati = new Societati("where societate_id in $sql order by denumire asc");
    $objResponse = new xajaxResponse();
    $objResponse->assign("societati", "innerHTML", $societati->select_multiple());
    
    //le facem multiselect
    $js = "$('#societate_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
            									xajax_filter_by_societate(xajax.getFormValues('frmFiltre'));
                            				});";
    $objResponse->script($js);
    return $objResponse;
}

/**
 * incarca puncte de lucru
 *
 * se incarca punctele de lucru asociate cu societatile pentru care sunt drepturi
 * @author cosmin
 * @return html puncte de lucru
 */
function load_puncte_lucru($societati_ids = array())
{
    //obtinem societatile cu drepturi
    if ( empty($societati_ids))
        $societati_ids = get_societati_cu_drepturi();
        
    if (count($societati_ids) == 0)
        return;
        
    //transformam array-ul intr-o sintaxa pt instructiunea IN din sql
    $sql = "";
    $sql .= array_2_sql_in($societati_ids);
    
    //adaugam pcte lucur
    $puncte_lucru = new PuncteLucru("where societate_id in $sql order by denumire asc");
    $objResponse = new xajaxResponse();
    $objResponse->assign("puncte_lucru", "innerHTML", $puncte_lucru->select_multiple());
    
    //le facem multiselect
    $js = "$('#punct_lucru_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
                            					xajax_filter_by_puncte_lucru(xajax.getFormValues('frmFiltre'));
                            				});";
    $objResponse->script($js);
    
    return $objResponse;
}

/**
 * incarca gestiuni
 *
 * se incarca gestiunile din gestiuni_utilizatori deci acelea cu drepturi
 * @author cosmin
 * @return html gestiuni
 */
function load_gestiuni($gestiuni_ids = array())
{
    if ( empty($gestiuni_ids))
    {
        $gestiuni = new Gestiuni();
        $gestiuni->getGestiuniCuDrepturi();
    }
    else
    {
        $gestiuni_in_sql = array_2_sql_in($gestiuni_ids);
        $gestiuni = new Gestiuni("where gestiune_id in $gestiuni_in_sql order by denumire asc");
    }
    
    //adaug gestiuni
    $objResponse = new xajaxResponse();
    $objResponse->assign("gestiuni", "innerHTML", $gestiuni->select_multiple());
    
    //le fac multiselect
    $js = "	$('#gestiune_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
                            					xajax_filter_by_gestiuni(xajax.getFormValues('frmFiltre'));
                            				});";
    $objResponse->script($js);
    
    return $objResponse;
}

/**
 * incarca posuri
 *
 * se incarca posurile asociate cu gestiunile cu drepturi
 * @author cosmin
 * @return html posuri
 */
function load_posuri($gestiuni_ids = array())
{
    //obtinem gestiunile cu drepturi
    if ( empty($gestiuni_ids))
        $gestiuni_ids = $_SESSION['user']->gestiuni_asociate;
        
    if (count($gestiuni_ids) == 0)
        return;
        
    //transformam array-ul in IN sql
    $gestiuni_in_sql = array_2_sql_in($gestiuni_ids);
    
    //adaugam posurile
    $posuri = new Posuri("where gestiune_id in $gestiuni_in_sql order by cod asc");
    $objResponse = new xajaxResponse();
    $objResponse->assign("posuri", "innerHTML", $posuri->select_multiple());
    
    //le fac multiselect
    $js = "$('#pos_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
                            					xajax_load(xajax.getFormValues('frmFiltre'));
                            				});		";
    $objResponse->script($js);
    
    return $objResponse;
}

// *************** END LOAD MULTILSELECT ********************

// ********************** COMMON ***********************

/**
 * transforma un array intr-un IN din sql
 *
 * Ex: {1,2,3} merge in ('1','2','3')
 * @param array $ids
 * @return string
 * @author cosmin
 */
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

/**
 * intoarce societatile cu drepturi in functie de gestiunile cu drepturi asociate acestora
 * @return array int
 * @author cosmin
 */
function get_societati_cu_drepturi()
{
    //acest array va tine id-urile societatilor cu drepturi, indexate printr-un intreg
    $societati_ids = array();
    
    //obtinem gestiunile cu drepturi, pentru a avea acces la societati
    foreach ($_SESSION['user']->gestiuni_asociate as $id)
    {
        $gestiune = new Gestiuni("where gestiune_id = $id");
        
        //o societate are mai multe gestiuni; adaugam societatea,daca nu exista deja
        if (!in_array($gestiune->societate_id, $societati_ids))
            $societati_ids[] = $gestiune->societate_id;
    }
    
    return $societati_ids;
}

// ********************** END COMMON ***********************


//*************************** FILTRE ***********************

/**
 * filtreaza lista de produse in functie de ce se tasteaza in input text
 * @param array $filtru componente form-ului de filtrare
 * @return html produse
 * @author cosmin
 */
function filter_products($filtru)
{
    $objResponse = new xajaxResponse();
    //dc s-a scris in input text-ul de cautare
    if ($filtru)
    {
        $produse = new Produse("where denumire like '$filtru%' order by denumire asc");
    }
    //input text-ul este gol, incarc toata lista
    else
    {
        $produse = new Produse("where 1 order by denumire asc");
    }
    
    $objResponse->assign("div_select_produse", "innerHTML", $produse->select());
    return $objResponse;
}

/**
 * selecteaza un produs din lista prin dublu click sau enter si seteaza in cadrul paginii valorile
 * @param int $produs_id
 * @return html seteaza in cadrul paginii htm
 * @author cosmin
 */
function selectProdus($produs_id)
{
    $produs = new Produse($produs_id);
    $objResponse = new xajaxResponse();
    $objResponse->assign("produs_id", "value", $produs->id);
    $objResponse->assign("produs_denumire", "value", $produs->denumire);
    return $objResponse;
}

/**
 * filtreaza in functie de societatile selectate in multiselect
 *
 * se filtreaza punctele de lucru, gestiunile, posurile de pe societatile respective
 * daca se deselecteaza totul, se aduc datele ca la prima incarcare, situatie similara cu selectare totala
 * se apeleaza ori de cate ori se schimba selectia pe multiselect-ul de gestiuni
 * @param object $frmFiltre
 * @author cosmin
 */
function filter_by_societate($frmFiltre)
{
    $societati_ids = array();
    $gestiuni_ids = array();
    
    //daca s-a selectat ceva
    if ($frmFiltre['societate_id'])
    {
        $societati_ids = $frmFiltre['societate_id'];
        
        //------- obtinem gestiunile cu drepturi, care au asociate societatile selectate --------
        
        $gestiuni = new Gestiuni();
        
        //obtinem gestiunile cu drepturi
        $gestiuni->getGestiuniCuDrepturi();
        
        //construim arrrayul cu iduri de gestiuni cu drepturi
        foreach ($gestiuni as $g)
            if (in_array($g->gestiune_id, $gestiuni_ids) == FALSE && in_array($g->societate_id, $societati_ids) == TRUE)
                $gestiuni_ids[] = $g->gestiune_id;
    }
    
    /* dupa ce am construit sql query pt societati si gestiuni, aducem datele pentru
     * puncte de lucru, gestiuni si posuri
     */
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, load_puncte_lucru($societati_ids));
    copyResponse($objResponse, load_gestiuni($gestiuni_ids));
    copyResponse($objResponse, load_posuri($gestiuni_ids));
    return $objResponse;
}

/**
 * filtreaza in functie de punctele de lucru selectate in multiselect
 *
 * acest filtru afecteaza gestiunile si implicit posurile
 * @param object $frmFiltre
 * @author cosmin
 * @return
 */
function filter_by_puncte_lucru($frmFiltre)
{
    $puncte_lucru_ids = array();
    $gestiuni_ids = array();
    
    if ($frmFiltre['punct_lucru_id'])
    {
        $puncte_lucru_ids = $frmFiltre['punct_lucru_id'];
        
        //------- obtinem gestiunile cu drepturi, care au asociate societatile selectate --------
        
        $gestiuni = new Gestiuni();
        
        //obtinem gestiunile cu drepturi
        $gestiuni->getGestiuniCuDrepturi();
        
        //construim arrrayul cu iduri de gestiuni cu drepturi
        foreach ($gestiuni as $g)
            if (in_array($g->gestiune_id, $gestiuni_ids) == FALSE && in_array($g->punct_lucru_id, $puncte_lucru_ids) == TRUE)
                $gestiuni_ids[] = $g->gestiune_id;
    }
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, load_gestiuni($gestiuni_ids));
    copyResponse($objResponse, load_posuri($gestiuni_ids));
    return $objResponse;
    
}

/**
 * filtreaza in functie de gestiunile selectate in multiselect
 *
 * doar posurile sunt afectate de aceasta filtrare
 * @author cosmin
 */
function filter_by_gestiuni($frmFiltre)
{
    $gestiuni_ids = array();
    
    if ($frmFiltre['gestiune_id'])
    {
        $gestiuni_ids = $frmFiltre['gestiune_id'];
    }
    
    $objResponse = new xajaxResponse();
    copyResponse($objResponse, load_posuri($gestiuni_ids));
    return $objResponse;
    
}

/**
 * afiseaza dialogul pentru selectia unui produs pentru filtru
 * @author cosmin
 * @return jquery products dialog
 */
function browse_products()
{
    $objResponse = new xajaxResponse();
    
    //instantiez obiectul jquery dialog
    $dialog = new Dialog(400, 500, '', 'win_select_product');
    $dialog->title = "Selecteaza produs";
    
    //--------- construiesc elementele html componente ale dialog-ului --------------
    
    //codul js pentru onkeyup, in input text-ul de cautare a produsului
    $js_code = "switch(event.keyCode) 
				{
            			case 40:					
            				$('#sel_produse').attr('selectedIndex', 0);
            				$('#sel_produse').focus();						
    				        break;
    				     default: 
    				        OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filter_products', 100);
						break;
				}";
				
    // input text cautare produs
    $html_code = "<p>
					Produs: <input type=\"text\" name=\"cautare_produs\" id=\"cautare_produs\" onkeyup=\"$js_code\"\>
				</p>";
				
    // lista produse
    $produse = new Produse("where 1 order by denumire asc");
    $html_code .= "<div id=\"div_select_produse\">".$produse->select()."</div>";
    
    //adaug codul html la dialog
    $dialog->append($html_code);
    //adaug buton de inchidere
    $dialog->addButton("Renunta");
    
    $objResponse = openDialog($dialog);
    
    return $objResponse;
}

//*************************** END FILTRE ***********************
?>
