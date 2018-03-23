<?php

/**
 * converteste data din Y-m-d in d.m.Y
 * @param object $str
 * @return 
 */
function c_data($str)
	{
	return date("d.m.Y", strtotime($str));
	}

function c_dataora($str=NULL)
	{
	return date("d.m.Y H:i:s", strtotime($str));
	}

/**
 * converteste data din d.m.Y in Y-m-d
 * @param object $str
 * @return 
 */
function data_c($str)
	{
	$data = explode(".", $str);
	return $data[2]."-".$data[1]."-".$data[0];
	}

function dataora_c($str=NULL)
	{
	return date("Y-m-d H:i:s", strtotime($str));
	}
	

/**
 * returneaza data Y-m-d
 * @return 
 */
function data()
	{
	return date("Y-m-d");
	}

function dataora()
	{
	return date("Y-m-d H:i:s");
	}

function ora()
	{
	return date("H:i:s");
	}

function douazecimale($nr)
	{
	return number_format($nr, 2, '.','');
	}

function treizecimale($nr)
	{
	return number_format($nr, 3, '.','');
	}
	
function money($nr, $valuta="") {
	if(!$valuta) return $nr;
	$nr = douazecimale($nr);
	switch($valuta) {
		case "EUR": {
			return "&euro; ".$nr;
		}break;
		case "USD": {
			return $nr.' $';
		}break;
		default: {
			return $nr." ".$valuta;
		}break;
	}
}	

function copyResponse(&$objResponse, $toCopy)
	{
	foreach($toCopy -> aCommands as $c)
		{
		$objResponse -> aCommands[] = $c;
		}
	}
	
function gotojs($url)
{
	echo '<script>
		window.location.href = \''.$url.'\';
	</script>';
}	

function location($url) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("window.location.href = '". $url ."'");
	return $objResponse;
}
	
function error($m)
{
	$ers = explode(";",$m);
	$out = '<ul class="error">';
	unset($ers[count($ers) -1 ]);
	foreach($ers as $er) {
		$out .= '<li>'. $er .'</li>';
	}
	$out .= '</ul>';
	return $out;
}

function error_alert($m)
{
	$ers = explode(";",$m);
	$out = '';
	unset($ers[count($ers) -1 ]);
	foreach($ers as $er) {
		$out .= ''. $er .'\n\r';
	}
	$out .= '';
	$objResponse = new xajaxResponse();
	$objResponse -> script("alert('$out')");
	return $objResponse;
	
}

function lista_poze($filelist)
{
	$list = explode(";",$filelist);
	unset($list[count($list)-1]);
	return $list;
}		

function stylesheet($link) {
	echo '<link rel="stylesheet" type="text/css" href="'.DOC_ROOT.''.$link.'" />';
}

function jscript($link) {
	echo '<script type="text/javascript" src="'.DOC_ROOT.''.$link.'"></script>';
}


function paginator($action, $paged, $curentpage) 
{
	switch($action) {
		case "first": {
			$page = $paged[0];
			$curentpage = 1;
				
		}break;
		case "back": {
			$curentpage = $curentpage;
			if($curentpage == 1) $curentpage = count($paged);
			else $curentpage--;
			$page = $paged[$curentpage-1];
		}break;		
		case "next": {
			$curentpage = $curentpage;
			if($curentpage == count($paged)) $curentpage = 0;
			$page = $paged[$curentpage];
			$curentpage++;

		}break;
		case "last": {
			$curentpage = count($paged) - 1;
			$page = $paged[$curentpage];
			$curentpage++;

		}break;
		case "pagesize": {
			$page = $paged[0];
			$curentpage = 1;

		}break;
		default : {
			$page = $paged[$curentpage-1];
		}break;
	}
	$pagedisplay = "Pagina $curentpage din ".count($paged)."";
	
	return array("pagedisplay"=>$pagedisplay, "curentpage"=>$curentpage, "page"=>$page);	
}

function paginated($action, $model, $curentpage, $pageLength) 
{
	$count = $model -> nrPages($pageLength);
	switch($action) {
		case "first": {
			$model -> getPagedQuery($pageLength, 1);
			$curentpage = 1;
		}break;
		case "back": {
			$curentpage = $curentpage;
			if($curentpage == 1) $curentpage = $count;
			else $curentpage--;
			$model -> getPagedQuery($pageLength, $curentpage);
		}break;		
		case "next": {
			if($curentpage == $count) $curentpage = 1;
			else $curentpage++;
			$model -> getPagedQuery($pageLength, $curentpage);
		}break;
		case "last": {
			$curentpage = $count;
			$model -> getPagedQuery($pageLength, $curentpage);
		}break;
		case "pagesize": {
			$model -> getPagedQuery($pageLength, 1);
			$curentpage = 1;
		}break;
		case "topage": {
			$model -> getPagedQuery($pageLength, $curentpage);
			$curentpage = $curentpage;
		}
		default : {
			$model -> getPagedQuery($pageLength, $curentpage);
		}break;
	}
	$pagedisplay = "Pagina $curentpage din ".$count."";
	
	return array("pagedisplay"=>$pagedisplay, "curentpage"=>$curentpage, "page"=>$model);	
}


function switchTab($tab) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("\$('#tabs').tabs('select', '$tab')");
	return $objResponse;
}

function disableTab($tab) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("\$('#tabs').tabs('disable', '$tab')");
	return $objResponse;
}

function enableTab($tab) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("\$('#tabs').tabs('enable', '$tab')");
	return $objResponse;
}

function initControl() {
	$objResponse = new xajaxResponse();
	//
	$objResponse -> script("
		\$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		\$('.nc8').mask('99999999');
		\$('.calendar').mask('99.99.9999');
		\$('.iban').mask('****-****-****-****-****-****');
		\$('.tablesorter').tablesorter();
	");
	return $objResponse;
}

function openDialog($dialog) {
	$objResponse = new xajaxResponse();
	$objResponse -> append("windows", "innerHTML", $dialog -> getHtml());
	$objResponse -> script($dialog -> script());
	if(!$dialog -> close) {
		$objResponse -> script("$('.ui-dialog-titlebar-close').hide();");
	}
	return $objResponse;
}

function closeDialog($dialog) {
	$objResponse = new xajaxResponse();
	$objResponse -> script("$('#$dialog').dialog('close');");
	return $objResponse;
}

function alert($msg) {
	$objResponse = new xajaxResponse();
	$dialog = new Dialog(300, 200, $msg, "win_alert");
	$dialog -> resizable = false;
	$dialog -> title = "Info";
	$dialog -> addButton("Ok", "<%close%>");
	return openDialog($dialog);
}

function confirm($msg, $okFunction) {
	$objResponse = new xajaxResponse();
	$dialog = new Dialog(300, 200, $msg, "win_alert");
	$dialog -> resizable = false;
	$dialog -> title = "Info";
	$dialog -> addButton("Ok", "$okFunction<%close%>");
	$dialog -> addButton("Renunta");
	return openDialog($dialog);
}

function camelCaseToUnderline($txt) {
	return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $txt));
}

?>