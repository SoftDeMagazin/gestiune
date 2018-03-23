<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?=SITUATII_FINANCIARE?>);
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
		$('.calendar').mask('99.99.9999');
		$('.tablesorter').tablesorter();
		$('#tabs').tabs();
		
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		
		$('#txt_numar').keypress(
			function(event) {
				if(event.keyCode == 13) {
					xajax_cautare(xajax.getFormValues('frmCautareFurnizor'));
				}
			}
		);
		
		$('#societate_id').multiSelect({oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
	}
);

</script>

<title>Situatii Financiare &gt; Clienti</title>
</head>
<body>
<div id="left">
    <div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
        <div id="tabs">
            <ul id="tabs-meniu">
                <li><a href="#interni">CLIENTI</a></li>
            </ul>
            <div id="interni" class="tab">
            <div align="center"> 
            	<a href="#" id="btnIntern" class="ui-state-default ui-state-active" style="padding: .5em 1em; text-decoration: none;" onClick="$(this).addClass('ui-state-active');$('#btnExtern').removeClass('ui-state-active');$('#tip').val('intern');xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager')); return false;">CLIENTI INTERNI</a> 
                <a href="#" id="btnExtern"  class="ui-state-default" style="padding: .5em 1em; text-decoration: none;" onClick="$(this).addClass('ui-state-active');$('#btnIntern').removeClass('ui-state-active');$('#tip').val('extern_ue');xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager')); return false;">CLIENTI EXTERNI</a>
            </div>
            <div style="margin-top:8px;">
             <div id="filtre" class="filtre" style=" width:50%; float:left">
                <form id="frmFiltre" onSubmit="return false;">
                Filtre: <input name="denumire" type="text" size="25" onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">
                <input type="hidden" id="tip" name="tip" value="intern">
        Soc:
		<?php
		$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
		$soc = new Societati("where 1 order by `denumire` asc");
		echo $soc -> select_multiple(array($gestiune -> punct_lucru -> societate_id));
		?>      <br>  
        		Cu sold <input type="checkbox" name="sold" value="DA" onClick="xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');">
                </form>
            </div>
        
            <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
            <?php
            include_once(DOC_ROOT."app/templates/pager.php");
            ?>
            </div>
           </div> 
               
            	<div id="grid" style="height:500px; margin-top: 8px; overflow:scroll; overflow-x:hidden; clear:both;">
                	
            	</div>
                <div id="total"></div>
            </div>
        </div> <!-- end tabs -->
    </div>	 <!-- end left content -->
</div>    <!-- end left -->

<div id="right" style="height:100%">
<?php
	echo menu();
?>
</div> 

<div id="windows">
</div> 
</body>
</html>