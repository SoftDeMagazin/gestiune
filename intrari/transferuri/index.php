<?php 
require_once ("common.php");
require_once (DOC_ROOT."test_login.php");
require_once (DOC_ROOT."test_drept.php");
require_once (DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#meniu').accordion({
            header: 'h3',
            animated: false
        });
        $('#meniu').accordion('activate', <?=INTRARI?>);
        $('#tabs').tabs();
        $('#data').datepicker({
            buttonImageOnly: true,
            hideIfNoPrevNext: true,
            duration: '',
            showOn: 'button',
            buttonImage: '/app/files/img/office-calendar.png'
        });
        $('#data').mask('99.99.9999');
        $('#gestiune_id').multiSelect(null, function(el){
            xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
        });
        xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
    });
</script>
<title>Evidenta transferuri intrari</title>
</head>
<body>
    <div id="left" style="height:100%;">
        <div id="left-content">
            <?php require_once (DOC_ROOT.'app/templates/header.php'); ?>
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#lista">Transferuri</a>
                    </li>
                </ul>
                <div id="#lista" class="tab">
                    <br>
                    <br>
                    <div id="filtre" class="filtre" style="width: 60%; float: left">
                        <form id="frmFiltre" onSubmit="return false;">
                            Ges. sursa:
                            <?php 
                            $gestiuni = new Gestiuni("where 1 order by denumire asc");
                            echo $gestiuni->select_multiple("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
                            ?>
                            Data: <input id="data" name="data" type="text" onchange="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));">
							<input id="iValid" name="isValid" type="checkbox" onclick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));">Validate
                        </form>
                    </div>
                    <div id="pager" class="pager" style="text-align: right; width: 40%; float: left">
                        <?php 
                        include_once (DOC_ROOT."app/templates/pager.php");
                        ?>
                    </div>
					<br><br>

                    <p>
                        &nbsp;<a href="#" id="btnValidate" class="ui-state-default" style="padding: .5em 1em; text-decoration: none;clear:both;" onClick="xajax_validateTransfer($('#selected_transfer_id').val(),xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));return false;">Valideaza transfer</a>
                    </p>
					<br>
                    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
                        &nbsp;
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
