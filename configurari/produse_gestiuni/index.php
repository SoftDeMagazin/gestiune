<?php 
require_once ("common.php");
require_once (DOC_ROOT."test_login.php");
require_once (DOC_ROOT."test_drept.php");
require_once (DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<title>Configurari - Produse - Gestiuni</title>
<script type="text/javascript">
        /*var OnKeyRequestBuffer = {
            bufferText: false,
            bufferTime: 350,
            fnc: false,
            modified: function(strId, fun, time){
                this.fnc = fun;
                this.bufferTime = time;
                setTimeout('OnKeyRequestBuffer.compareBuffer("' + strId + '","' + xajax.$(strId).value + '");', this.bufferTime);
                
            },
            
            compareBuffer: function(strId, strText){
                if (strText == xajax.$(strId).value && strText != this.bufferText) {
                    this.bufferText = strText;
                    OnKeyRequestBuffer.makeRequest(xajax.$(strId).value);
                }
            },
            
            makeRequest: function(str, fnc){
                setTimeout('' + this.fnc + '("' + str + '");', 1);
            }
        }*/
        
        $(document).ready(function(){
            $('#meniu').accordion({
                header: 'h3',
                animated: false
            });
            $('#meniu').accordion('activate', <?=CONFIGURARI?>);
            $('#tabs').tabs();
            //$('#gestiune_id').multiSelect();
    		$('#filtru_gestiune').multiSelect(null, function(el) {
    	xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
    			});
           /* $('#cautare_produs').keyup(function(event){
                switch (event.keyCode) {
                    case 40:{
                        $('#sel_produse').attr('selectedIndex', 0);
                        $('#sel_produse').focus();
                    }
        break;
                    default:
                        {
                            OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filterProducts', 100);
                        }
                        break;
                }
            });*/
    		<?php echo 'xajax_filterGestiuni(0);';?>
    		 xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
        });
        
        function validate(){
            valid = true;
    		
            if (document.frm_produse_gestiuni.produs_id.value == "" ||
    		document.frm_produse_gestiuni.produs.value == "" ||
            document.frm_produse_gestiuni.pret_ron.value == "") {
                xajax_alert("Selectati un produs si completati pretul.");
                valid = false;
            }
            
            return valid;
        }
    	
    	function cancel(){
    		document.frm_produse_gestiuni.produs_id.value = "";
    		document.frm_produse_gestiuni.produs.value = "";
    		document.frm_produse_gestiuni.pret_ron.value ="";
    		document.frm_produse_gestiuni.pret_val.value="";
			document.frm_produse_gestiuni.gestiune_id.value="";
			document.frm_produse_gestiuni.gestiune.value="";
    		xajax_switchTab('lista');
    	}
</script>
<link rel="stylesheet" type="text/css" href="../../app/js/jquery/css/south-street/jquery-ui-1.7.1.custom.css" />
</head>
<body>
    <div id="left" style="height: 100%;">
        <div id="left-content">
            <?php require_once (DOC_ROOT.'app/templates/header.php'); ?>
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#lista">Lista </a>
                    </li>
                    <li>
                        <a href="#edit">Editare</a>
                    </li>
                </ul>
                <div id="lista" class="tab">
                    <div id="filtre" class="filtre" style="width: 60%; float: left">
                        <form id="frmFiltre" onSubmit="return false;">
                            Produse:<input type="text" id="denumire" name="denumire" value="" onchange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');">Gestiuni:
                            <?php 
                            $query = "INNER JOIN gestiuni_utilizatori using(gestiune_id)"." WHERE utilizator_id=".$_SESSION['user']->user_id." ORDER BY denumire asc";
                            $gestiuni = new Gestiuni($query);
                            echo $gestiuni->select_multiple_with_name("", 'filtru_gestiune');
                            ?>
                        </form>
                    </div>
                    <div id="pager" class="pager" style="text-align: right; width: 40%; float: left">
                        <?php 
                        include_once (DOC_ROOT."app/templates/pager.php");
                        ?>
                    </div>
                    <?php 
                    session_start();
                    echo toolbar('produs_gestiune_id', $_SESSION['user']->permissions['30']);
                    ?>
                    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
                        &nbsp;
                    </div>
                </div>
                <div id="edit" class="tab">
                    <!-- <table>
                    <tr>
                    <td width="30%">
                    Cautare:
                    <div id="div_filtru">
                    <input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
                    </div>
                    </td>
                    <td rowspan="2" width="70%" valign="top" style="padding-left:20px">-->
                    <form id="frm_produse_gestiuni" name="frm_produse_gestiuni" method="post">
                        <fieldset>
                            <legend>
                                Produs/Gestiune
                            </legend>
                            <input id="produs_id" name="produs_id" type="hidden" value="">
                            <p>
                                <div>
                                    <label for="produs">
                                        Produs
                                    </label>
                                </div>
                                <input type="text" id="produs" name="produs" size=25 readonly="true">
                            </p>
                            <p>
                                <div>
                                    <label for="gestiune">
                                        Gestiune
                                    </label>
                                </div>
                                <input type="hidden" id="gestiune_id" name="gestiune_id">
								<input type="text" size="25" id="gestiune" name="gestiune" value="" readonly="true">
                            </p>
                            <p>
                                <div>
                                    <label for="pr">
                                        Pret RON
                                    </label>
                                </div>
                                <div>
                                    <input type="text" id="pret_ron" name="pret_ron" size="23">
                                </div>
                            </p>
                            <p>
                                <div>
                                    <label for="pv">
                                        Pret val
                                    </label>
                                </div>
                                <div>
                                    <input type="text" id="pret_val" name="pret_val" size="23">
                                </div>
                            </p>
                            <p>
                                <div align="right">
                                    <a href="#" id="btnSave" class="ui-state-default" style="padding: .5em 1em; text-decoration: none;" onClick="if(validate()){xajax_save(xajax.getFormValues('frm_produse_gestiuni'), xajax.getFormValues('frmFiltre'),xajax.getFormValues('frmPager'));};">Salveaza</a>
                                    <a href="#" id="btnCancel" class="ui-state-default" style="padding: .5em 1em; text-decoration: none;" onClick="cancel()">Renunta</a>
                                </div>
                            </p>
                            <br>
                        </fieldset>
                    </form>
                    <!--  </td>
                    </tr>
                    <tr>
                    <td width="30%">
                    <div id="div_select_produse">
                    <?php
                    //$produse = new Produse("where 1 order by denumire asc");
                    //echo $produse->select();
                    ?>
                    </div>
                    </td>
                    </tr>
                    </table>-->
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
