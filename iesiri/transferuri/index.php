<?php 
require_once ("common.php");
require_once (DOC_ROOT."test_login.php");
require_once (DOC_ROOT."test_drept.php");
require_once (DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
                	function dataIsValid(){
                	    valid = true;
                	    
                	    if (document.frm_transferuri.gestiune_sursa_id.value == 0 ||
                	    document.frm_transferuri.gestiune_destinatie_id.value == 0) {
                	        xajax_alert("Alegeti gestiunea sursa.\nAlegeti gestiunea destinatie");
                	        valid = false;
                	    }
                	    
                	    return valid;
                	}                            	
                                       
                	 function cancelAdd(){
                	 	document.frm_transferuri_componente.produs_id.value = "";
                		document.frm_transferuri_componente.transfer_componenta_id.value ="";
                		document.frm_transferuri_componente.cantitate.value="";
						document.frm_transferuri_componente.um.value="";
                		document.getElementById('div_produs').innerHTML = "";
                		document.getElementById('div_detalii_produs').innerHTML = "";
                	 }  
        			 
        			 function clearHeader()
        			 {
        			 	document.frm_transferuri.transfer_id.value = "";
        				document.frm_transferuri.gestiune_sursa_id.value=0;
        				document.frm_transferuri.gestiune_destinatie_id.value=0;
        				document.frm_transferuri.data.value = "";
        			 }
                	 
                	 function setCanValidate(){
                	 	var canValidate = false;
                		var selected_gestiune_destinatie = document.frm_transferuri.gestiune_destinatie_id.value;
                		
                		for(var i=0;i<document.frm_transferuri.gestiune_sursa_id.length;i++)
                		{
                			if(document.frm_transferuri.gestiune_sursa_id.options[i].value == selected_gestiune_destinatie)
                			{
                				canValidate = true;
                			}
                		}
                		
                		if(canValidate)
                		{
                			document.getElementById("Validate").style.visibility = "visible" ;
                		}
                		else
                		{
                			document.getElementById("Validate").style.visibility = "hidden" ;
                		}
                	 }
                					   
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
                    		$('#meniu').accordion('activate', <?=IESIRI?>);
                    		$('#tabs').tabs();
        					$('#tabs').tabs('disable', 1);
                			$('#data').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
                			$('#data').mask('99.99.9999');
                    		<?php
                    		$transfer_id = $_GET['transfer_id'];
                    		if($transfer_id) 
                			{
                				echo 'xajax_load('. $transfer_id .');';
                			}
                    		else echo 'xajax_load();';				
                    		?>		
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
                	}
                );
                }
                );                                              
</script>
</head>
<title>Iesiri - Transferuri</title>
</head>
<body>
    <div id="left">
        <div id="left-content">
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#antet">Antet transfer</a>
                    </li>
                    <li>
                        <a href="#frm">Transfer componente</a>
                    </li>
                </ul>
                <div id="antet" class="tab">
                    <form name="frm_transferuri" method="post" id="frm_transferuri">
                        <input type="hidden" name="transfer_id" id="transfer_id" value=0>
                        <div id="div_gestiune_sursa">
                            <fieldset>
                                <legend>
                                    Gestiune sursa
                                </legend>
                                <p>
                                    <?php 
                                    $query = "INNER JOIN gestiuni_utilizatori using(gestiune_id)"." WHERE utilizator_id=".$_SESSION['user']->user_id." ORDER BY denumire asc";
                                    $gestiuni = new Gestiuni($query);
                                    echo $gestiuni->select("", "gestiune_sursa_id");
                                    ?>
                                </p>
                            </fieldset>
                        </div>
                        <br>
                        <div id="div_gestiune_destinatie">
                            <fieldset>
                                <legend>
                                    Gestiune destinatie
                                </legend>
                                <p>
                                    <input type="checkbox" name="chkOutside" value="Externa" onclick="xajax_showWorkingPoints(this.checked)">Gestiune externa
                                </p>
                                <div id="outsideDestinations">
                                </div>
                                Gestiune
                                <br>
                                <div id="gestiune_dest">
                                    <?php 
                                    $gestiuni = new Gestiuni("where 1 order by denumire asc");
                                    echo $gestiuni->select("", "gestiune_destinatie_id");
                                    ?>
                                </div>
                                <br>
                            </fieldset>
                        </div>
                        <div>
                            <p>
                                Data:
                                <br>
                                <input id="data" name="data" type="text">
                            </p>
                        </div>
                        <div align="center">
                            <p>
                                <input name="btnSalveaza" id="btnSalveaza" value="Salveaza Antet" onclick="if(dataIsValid()) {xajax_saveHeader(xajax.getFormValues('frm_transferuri'));setCanValidate();}" type="button"><input name="btnReset" id="btnReset" value="Reseteaza" onclick="clearHeader();" type="button">
                            </p>
                        </div>
                    </form>
                </div>
                <div id="frm" class="tab">
                    <table width="100%" border="0" cellspacing="10" cellpadding="0">
                        <tr>
                            <td width="30%">
                                Cautare: 
                                <div id="div_filtru">
                                    <input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
                                </div>
                            </td>
                            <td width="70%" rowspan="2" valign="top">
                                <table>
                                    <tr>
                                        <td width="50%">
                                            <form method="post" id="frm_transferuri_componente" name="frm_transferuri_componente">
                                                <fieldset>
                                                    <legend>
                                                        Produs
                                                    </legend>
                                                    <table>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="produs_id" id="produs_id" value=""><input type="hidden" name="transfer_componenta_id" id="transfer_componenta_id" value="">
                                                                <div id="div_produs" style="font-weight:bold; float:left">
                                                                </div>
                                                                &nbsp;
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div id="div_detalii_produs">
                                                                    &nbsp;
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Cantitate &nbsp;<input type="text" name="cantitate" id="cantitate">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                UM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="um" id="um" readonly="true">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right">
                                                                <input type="button" id="btnAddProduct" name="btnAddProduct" value="Salveaza" onclick="xajax_saveComponent(xajax.getFormValues('frm_transferuri_componente'),document.frm_transferuri.transfer_id.value);"><input type="button" id="btnCancel" name="btnCancel" value="Renunta" onclick="cancelAdd();">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </fieldset>
                                            </form>
                                        </td>
                                        <td>
                                            <div id = "Validate" style="visibility:hidden;">
                                                <a href="#" id="btnValidate" class="ui-state-default" style="padding: .5em 1em; text-decoration: none;" onClick="xajax_validateTransfer(xajax.getFormValues('frm_transferuri'));">Valideaza transfer</a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <div id="div_preview_factura" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">
                                </div>
                                <input type="button" id="btnEditComp" value="Editeaza" onClick="xajax_editComponent($('#selected_transfer_componenta_id').val());"><input type="button" id="btnDelComp" value="Sterge" onClick="xajax_deleteComponent($('#selected_transfer_componenta_id').val());">
                                <div style="float:right;">
                                    <a href="#" id="btnSave" class="ui-state-default" style="padding: .5em 1em; text-decoration: none;" onClick="xajax_saveTransfer(xajax.getFormValues('frm_transferuri'));">Salveaza transfer</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div id="div_select_produse">
                                    <?php 
                                    $produse = new Produse("where 1 order by denumire asc");
                                    echo $produse->select();
                                    ?>
                                </div>
                            </td>
                        </tr>
                    </table>
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
