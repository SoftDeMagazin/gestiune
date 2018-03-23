<?php 
require_once ("common.php");
require_once (DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
                    	var OnKeyRequestBuffer = 
                                {
                                    bufferText: false,
                                    bufferTime: 350,
                                    fnc: false,
                                    modified : function(strId, fun, time)
                                    {
                            				this.fnc = fun;
                            				this.bufferTime = time;
                                            setTimeout('OnKeyRequestBuffer.compareBuffer("'+strId+'","'+xajax.$(strId).value+'");', this.bufferTime);
                            				
                                    },
                                    
                                    compareBuffer : function(strId, strText)
                                    {
                                        if (strText == xajax.$(strId).value && strText != this.bufferText)
                                        {
                                            this.bufferText = strText;
                                            OnKeyRequestBuffer.makeRequest(xajax.$(strId).value);
                                        }
                                    },
                                    
                                    makeRequest : function(str, fnc)
                                    {
                                        setTimeout(''+this.fnc+'("'+str+'");', 1);
                                    }
                                }	
                    	
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
											
											
											
                    						$('#cautare_produs').keyup(
                    	                	function(event) {
                    	                		switch(event.keyCode) {
                    	                			case 40: {						
                    	                				$('#sel_produse').attr('selectedIndex', 0);
                    	                				$('#sel_produse').focus();						
                    	                			}break;
                    	                			default: {
                    	                				OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filterProducts', 100);
                    	                			}break;
                    	                		}
                    	                	}    );
                                    	}
                                    );
</script>
<title>Stocuri - Necesar aprovizionare</title>
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
                            Data<input type="text" name="data_doc_filtru" id="from" class="calendar" onchange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');"> Realizat<input type="checkbox" name="realizat_filtru" id="realizat_filtru" onchange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');">
                        </form>
                    </div>
                    <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
                        <?php 
                        include_once (DOC_ROOT."app/templates/pager.php");
                        ?>
                    </div>
                    <?php 
                    //session_start();
                    echo toolbar('necesar_aprovizionare_id', $_SESSION['user']->permissions['47']);
                    ?>
                    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
                        &nbsp;
                    </div>
                </div>
                <div id="frm" class="tab">
                    <div id="necesar_header">
                        <fieldset>
                            <legend>
                                Necesar aprovizionare antet
                            </legend>
                            <form id="frmNecesar" name="frmNecesar">
                                <input id="necesar_aprovizionare_id" name="necesar_aprovizionare_id" type="hidden">Numar doc
                                <br>
                                <input type="text" id="numar_doc" name="numar_doc" readonly="true">
								  <br>
                                    Data
                                    <br>
                                    <input type="text" id="data" name="data" class="calendar">
                                <div style="text-align:right;">
                                    <input type="button" id="btnSaveHeader" name="btnSaveHeader" value="Salveaza" onclick="xajax_save(xajax.getFormValues('frmNecesar'), xajax.getFormValues('frmFiltre'),xajax.getFormValues('frmPager'),xajax.getFormValues('frmFiltreContent'),xajax.getFormValues('frmPagerContent'),'first');"><input type="button" id="btnCancelHeader" name="btnCancelHeader" value="Renunta" onclick="xajax_cancel();">
                                </div>
                            </form>
                        </fieldset>
                    </div>
                    <div id="necesar_continut">
                        <fieldset>
                            <legend>
                                Necesar aprovizionare continut
                            </legend>
                            <table width="100%">
                                <tr>
                                    <td style="width:250px; vertical-align:top;">
                                        <div id="div_filtru">
                                            <input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
                                        </div>
                                        <div id="div_select_produse">
                                            <?php 
                                            $produse = new ViewProduseGestiuni("where tip_produs!='serviciu' and gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
                                            echo $produse->select(25);
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <table width="100%">
                                            <tr>
                                                <td style="padding-left:10px;">
                                                    <form id="frmProduct">
                                                        <fieldset>
                                                            <input type="hidden" id="produs_id" name="produs_id">
															<input type="hidden" id="nac_id" name="nac_id">
                                                                Produs:
                                                                <br>
                                                                <input type="text" id="produs" name="produs" size="35" readonly="true">
																 
                                                                UM:
                                                           
                                                                <input type="text" id="um" name="um" readonly="true" size=5>
                                                       			<br>
                                                                Cantitate:
                                                                <br>
                                                                <input type="text" id="cantitate" name="cantitate">
                                                    	
                                                                <div style="text-align:right;">
                                                                    <input type="button" id="btnSaveProduct" name="btnSaveProduct" value="Salveaza" onclick="xajax_save_product($('#necesar_aprovizionare_id').val(),xajax.getFormValues('frmProduct'))"><input type="button" id="btnCancelProduct" name="btnCancelProduct" value="Renunta" onclick="xajax_cancel_product();">
                                                                </div>
                                                          
                                                        </fieldset>
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left:10px;">
                                                 
                                                    <input type="button" id="btnEditRecipe" name="btnEditRecipe" onclick="xajax_edit_recipe($('#selected_nac_id').val());" value="Editeaza">
													<input type="button" id="btnDeleteRecipe" name="btnDeleteRecipe" onclick="xajax_delete_recipe($('#selected_nac_id').val(),$('#selected_necesar_aprovizionare_id').val());" value="Sterge">
                                                    <div style="float:right;">
                                                        <form id="frmPagerContent">
                                                            <a href="#" onClick="xajax_lista_reteta(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'first'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/first.png" border="0" class="first"/></a><a href="#" onClick="xajax_lista_reteta(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'back'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/prev.png" border="0" class="prev"/></a><input name="pagedisplaycontent" type="text" id="pagedisplaycontent" size="15" readonly/><input type="hidden" name="curentpagecontent" id="curentpagecontent"/><a href="#" onClick="xajax_lista_reteta(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'next'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/next.png" border="0" class="next"/></a><a href="#" onClick="xajax_lista_reteta(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'last'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/last.png" border="0" class="last"/></a>
                                                            <select id="pagesizecontent" onChange="xajax_lista_reteta(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerContent'), 'pagesizecontent');" name="pagesizecontent">
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
                                                    <div id="grid_necesar_retete" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left:10px;">
                                                    <div style="text-align:right;">
                                                        <form id="frmPagerMP">
                                                            <a href="#" onClick="xajax_lista_mp(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerMP'), 'first'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/first.png" border="0" class="first"/></a><a href="#" onClick="xajax_lista_mp(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerMP'), 'back'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/prev.png" border="0" class="prev"/></a><input name="pagedisplay_mp" type="text" id="pagedisplay_mp" size="15" readonly/><input type="hidden" name="curentpage_mp" id="curentpage_mp"/><a href="#" onClick="xajax_lista_mp(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerMP'), 'next'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/next.png" border="0" class="next"/></a><a href="#" onClick="xajax_lista_mp(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerMP'), 'last'); return false;"><img src="<?php echo DOC_ROOT; ?>app/js/jquery.plugins/jquery.tablesorter/addons/pager/icons/last.png" border="0" class="last"/></a>
                                                            <select id="pagesize_mp" onChange="xajax_lista_mp(document.getElementById('necesar_aprovizionare_id').value,xajax.getFormValues('frmFiltreContent'), xajax.getFormValues('frmPagerMP'), 'pagesize_mp');" name="pagesize_mp">
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
                                                    <div id="grid_necesar_mp" style="height:200px; overflow:scroll; overflow-x:hidden; clear:both;">
                                                    </div>
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
                    <div id="print_necesar">
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
