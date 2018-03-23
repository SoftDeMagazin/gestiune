<?php 
require_once ("common.php");
require_once (DOC_ROOT."test_login.php");
require_once (DOC_ROOT."test_drept.php");
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
                                    		$('#meniu').accordion('activate', <?=RAPOARTE?>);
                                    		$('#tabs').tabs();
                                    		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });    					
                    						
                    						//load multiselects
                                    		
        									xajax_load_societati();
        									xajax_load_puncte_lucru();
        									xajax_load_gestiuni();
        									xajax_load_posuri();
                    						
                    					
                    						
                    						$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
                                    					xajax_load(xajax.getFormValues('frmFiltre'));
                                    				});    						      	                                                 	                                                            					                                                        					
                                    	}
                                    );
</script>
<title>Layout</title>
</head>
<body>
    <div id="left" style="height: 100%;">
        <div id="left-content">
            <?php require_once (DOC_ROOT.'app/templates/header.php'); ?>
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#intrari" onClick="xajax_cancel();">Raport intrari</a>
                    </li>
                </ul>
                <div id="intrari" class="tab">
                    <div id="filtre" class="filtre" style="width:100%; float: left">
                        <form id="frmFiltre" onSubmit="return false;">
                            <table width="50%" border="0" cellspacing="10" cellpadding="0">
                                <tr>
                                    <td>
                                        <fieldset>
                                            <legend>
                                                Perioada
                                            </legend>
                                            <table cellspacing="10">
                                                <tr>
                                                    <td>
                                                        <label for="de_la">
                                                            De la:
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="from" id="from" class="calendar">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="pana_la">
                                                            Pana la:
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="end" id="end" class="calendar">
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <fieldset>
                                            <legend>
                                                Locatie
                                            </legend>
                                            <table cellspacing="10">
                                                <tr>
                                                    <td>
                                                        <label for="societate">
                                                            Societate:
                                                        </label>
                                                    </td>
                                                    <td id="societati">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="punct_lucru">
                                                            Punct de lucru:
                                                        </label>
                                                    </td>
                                                    <td id="puncte_lucru">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="gestiuni">
                                                            Gestiune:
                                                        </label>
                                                    </td>
                                                    <td id="gestiuni">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="pos">
                                                            Pos:
                                                        </label>
                                                    </td>
                                                    <td id="posuri">
                                                    </td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <fieldset>
                                            <legend>
                                                Produse
                                                <table cellspacing="10">
                                                    <tr>
                                                        <td>
                                                            <label for="produs">
                                                                Produs:
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="produs_denumire" id="produs_denumire"><input type="hidden" id="produs_id" name="produs_id"><input type="button" nume="btnBrowse" id="btnBrowse" value="..." title="Cauta produs" onclick="xajax_browse_products();">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="categorie">
                                                                Categorie:
                                                            </label>
                                                        </td>
                                                        <td id="categorii">
                                                            <?php 
                                                            $categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '".$_SESSION['user']->gestiune_id."' order by denumire asc");
                                                            echo $categorii->select("");
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </legend>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="button" name="btnCauta" id="btnCauta" value="Afiseaza Raport" onClick="xajax_load(xajax.getFormValues('frmFiltre'));">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div id="grid" style="height: 500px; overflow: scroll; overflow-x: hidden; margin-top:10px; clear: both;">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="right" style="height: 100%">
        <?php 
        echo menu();
        ?>
    </div>
    <div id="windows">
    </div>
</body>
</html>
