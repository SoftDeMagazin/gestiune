<?php
function toolbar($key, $drepturi) {
	$doc_root = DOC_ROOT;	
	$add_function = 'xajax_frm(0); return false;';
	$edit_function = "xajax_frm($('#selected_{$key}').val()); return false;";
	$remove_function = "xajax_confirm('Stergeti?', 'xajax_sterge($(\'#selected_{$key}\').val(), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'));'); return false;";
	$print_function = "xajax_listeaza($('#selected_{$key}').val()); return false;";
	
	if($drepturi->getAdd() == false) {
		$add_function = "xajax_alert('Nu aveti drept de adaugare');return false;";
	}
	
	if($drepturi->getEdit() == false) {
		$edit_function = "xajax_alert('Nu aveti drept de editare');return false;";
	}
	
	if($drepturi->getDelete() == false) {
		$remove_function = "xajax_alert('Nu aveti drept de stergere');return false;";
	}
	
	if($drepturi->getPrint() == false) {
		$print_function = "xajax_alert('Nu aveti drept de listare');return false;";
	}
	
	$add = iconAdd($add_function);
	
	$edit = iconEdit($edit_function);
	
	$remove = iconRemove($remove_function);
	
	$refresh_function = 'xajax_lista(xajax.getFormValues(\'frmFiltre\'), xajax.getFormValues(\'frmPager\'), \'first\'); return false;';
	
	$refresh = iconRefresh($refresh_function);
	
    $print = iconPrint($print_function);
	
	$out = '
	<div style="margin-top:5px;margin-bottom:7px;margin-left:5px;clear:both;">
	'.$add.'
	'.$edit.'
	'.$remove.'
	'.$refresh.'
	'.$print.'
	</div>
	';
	return $out;
}

function iconAdd($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Adauga" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/add.png" title="Adauga" border="0"></a>	
		';
	}	
	return $out;
}

function iconEdit($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Editare" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/edit.png" title="Editeaza" border="0"></a>	
		';
	}
	return $out;
}

function iconView($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Vizualizare" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/edit.png" title="Editeaza" border="0"></a>	
		';
	}
	return $out;
}

function iconRemove($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Sterge" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/delete.png" title="Sterge" border="0"></a>	
		';
	}	
	return $out;
}

function iconRefresh($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Refresh" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/refresh.png" title="Refresh" border="0"></a>	
		';
	}
	return $out;
}

function iconPrint($fn) {
	$doc_root = DOC_ROOT;
	if(FOLOSESC_BUTOANE_TOOLBAR) {
		$out = '
			<input type="button" value="Listeaza" onClick="'. $fn.'" >
		';
	}
	else {
		$out = '
			<a href="#" onClick="'.$fn.'"><img alt="Editeaza" src="'.$doc_root.'app/img/toolbar/refresh.png" title="Refresh" border="0"></a>	
		';
	}
	return $out;
}

?>