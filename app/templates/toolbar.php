<div style="margin-top:40px;margin-bottom:7px;margin-left:5px;">
	<a href="#" onClick="xajax_frm(0);"><img alt="Adauga" src="<?php echo DOC_ROOT;?>app/img/toolbar/add.png" title="Adauga" border="0"></a> 
	<a href="#" onClick="xajax_frm($('#selected_id').val());"><img alt="Editeaza" src="<?php echo DOC_ROOT;?>app/img/toolbar/edit.png" title="Editeaza" border="0"></a> 
	<a href="#" onClick="xajax_confirm('Stergeti inregistrarea?', 'xajax_sterge($(\'#selected_id\').val(), xajax.getFormValues(\'frmFiltre\'),xajax.getFormValues(\'frmPager\'));');"><img alt="Sterge" src="<?php echo DOC_ROOT;?>app/img/toolbar/delete.png" title="Sterge" border="0"></a> 
	<a href="#" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');"><img alt="Actualizeaza" src="<?php echo DOC_ROOT;?>app/img/toolbar/refresh.png" title="Actualizeaza" border="0"></a> 
</div>