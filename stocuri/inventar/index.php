<?php 
require_once ("common.php");
require_once (DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
                    $(document).ready(
                    	function() {
                    		$('#meniu').accordion({header: 'h3', animated: false});
                    		$('#meniu').accordion('activate', <?=STOCURI?>);
                    		$('#tabs').tabs();
    						$('#tabs').tabs('disable', 2);
                			$('#utilizator_id').multiSelect(null, function(el) {
                					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
                				});
                			$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
                			$('.calendar').mask('99.99.9999');
                    		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
                    	}
                    );
</script>
<title>Stocuri - Inventar</title>
</head>
<body>
    <div id="left" style="height:100%;">
        <div id="left-content">
            <?php require_once (DOC_ROOT.'app/templates/header.php'); ?>
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#lista">Lista </a>
                    </li>
                    <li>
                        <a href="#frm">Adaugare/Editare</a>
                    </li>
                    <li>
                        <a href="#print_preview">Previzualizare listare</a>
                    </li>
                </ul>
                <div id="lista" class="tab">
                    <div id="filtre" class="filtre" style=" width:50%; float:left">
                        <form id="frmFiltre" onSubmit="return false;">
                            Nr_doc: <input name="nr_doc_filtru" id="nr_doc_filtru" type="text" size="20" onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">Utilizator: 
                            <?php 
                            $users = new Utilizatori("WHERE 1");
                            echo $users->select_multiple();
                            ?>
                            Data<input type="text" name="data_doc_filtru" id="from" class="calendar" onChange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');"> Inchis<input type="checkbox" name="inchis_filtru" id="inchis_filtru" onChange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');">
                        </form>
                    </div>
                    <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
                        <?php 
                        include_once (DOC_ROOT."app/templates/pager.php");
                        ?>
                    </div>
                    <?php 
                    echo toolbar('inventar_id', $_SESSION['user']->permissions['35']);
                    ?>
                    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
                        &nbsp;
                    </div>
                </div>
                <div id="frm" class="tab">
                    <div id="inventar_header">
                        <fieldset>
                            <legend>
                                Antet inventar
                            </legend>
                            <form id="frmInventar" name="frmInventar">
                                <input id="inventar_id" name="inventar_id" type="hidden">Numar doc
                                <br>
                                <input type="text" id="numar_doc" name="numar_doc" readonly="true">
                                <p>
                                    Data
                                    <br>
                                    <input type="text" id="data_inventar" name="data_inventar" class="calendar">
                                </p>
                                <p style="text-align:right;">
                                    <input type="button" id="btnSaveHeader" name="btnSaveHeader" value="Salveaza" onClick="xajax_save(xajax.getFormValues('frmInventar'), xajax.getFormValues('frmFiltre'),xajax.getFormValues('frmPager'),xajax.getFormValues('frmFiltreContent'),xajax.getFormValues('frmPagerContent'),'first');"><input type="button" id="btnCancelHeader" name="btnCancelHeader" value="Renunta" onClick="xajax_cancel();">
                                </p>
                            </form>
                        </fieldset>
                    </div>
                    <div id="inventar_continut">
                        <fieldset>
                            <legend>
                                Inventar continut
                            </legend>
                            <table width="100%">
                                <tr >
                                    <td >
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    <table width="100%">
                                                        <tr>
                                                            <td>
                                                                <form id="frmFiltreContent" name="frmFiltreContent" onSubmit="return false;">
                                                                    Produs
                                                                    <br>
                                                                    <input type="text" id="produs_filtru" name="produs_filtru" onKeyDown="if(event.keyCode==13) {xajax_lista_content(document.getElementById('inventar_id').value, xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'first');}">
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div style="margin-left:100px;">
                                                                    <form id="frmPagerContent">
                                                                        <a href="#" onClick="xajax_lista_content(document.getElementById('inventar_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'first'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/first.png" border="0" class="first"/></a><a href="#" onClick="xajax_lista_content(document.getElementById('inventar_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'back'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/prev.png" border="0" class="prev"/></a><input name="pagedisplaycontent" type="text" id="pagedisplaycontent" size="15" readonly/><input type="hidden" name="curentpagecontent" id="curentpagecontent"/><a href="#" onClick="xajax_lista_content(document.getElementById('inventar_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'next'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/next.png" border="0" class="next"/></a><a href="#" onClick="xajax_lista_content(document.getElementById('inventar_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'last'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/last.png" border="0" class="last"/></a>
                                                                        <select id="pagesizecontent" onChange="xajax_lista_content(document.getElementById('inventar_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'pagesizecontent');" name="pagesizecontent">
                                                                            <option value="10">10</option>
                                                                            <option value="20">20</option>
                                                                            <option value="30" selected="selected">30</option>
                                                                            <option value="40">40</option>
                                                                            <option value="50">50</option>
                                                                            <option value="60">60</option>
                                                                            <option value="70">70</option>
                                                                            <option value="80">80</option>
                                                                            <option value="90">90</option>
                                                                            <option value="100">100</option>
                                                                            <option value="1">Toate</option>
                                                                        </select>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="button" id="inchideInventar" value="Inchide inventar" style="margin-left:50px;color:red;" onClick="xajax_close_inventar($('#inventar_id').val());">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">
                                                                <div id="grid_inventar_continut" style="height:400px; overflow:scroll; overflow-x:hidden; clear:both;">
                                                                    &nbsp;
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div id="print_preview" class="tab">
                    <div id="print_inventar">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="right" style="height:100%">
        <?php 
        echo menu();
        ?>
    </div>
    <div id="windows">
    </div>
</body>
</html>
