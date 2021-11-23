<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<title>Nomenclator - Categorii</title>
<script type="text/javascript" >
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
		$('#meniu').accordion('activate', <?=NOMENCLATOR?>);
		$('#tabs').tabs();
		$('#tabs').tabs('disable', 1);
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		//interface initialisation
		$('#cautare_produs').keyup(
			function(event) {
				switch(event.keyCode) {
					case 40: {						
						$('#sel_view_produse_gestiuni').attr('selectedIndex', 0);
						$('#sel_view_produse_gestiuni').focus();						
					}break;
					default: {
						OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filtruProduse', 100);
					}break;
				}
			}
		);
	}
);
</script>
<link rel="stylesheet" type="text/css" href="../../app/js/jquery/css/south-street/jquery-ui-1.7.1.custom.css" />
</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista" onClick="xajax_cancel();">Lista </a></li>
        <li><a href="#frm">Adaugare/Editare</a></li>
		<li><a href="#terti">Comisioane Terti</a></li>
		<li><a href="#produse">Comisioane Produse</a></li>
    </ul>
<div id="lista" class="tab">
     <div id="filtre" class="filtre" style=" width:50%; float:left">
        <form id="frmFiltre" onSubmit="return false;">
        Filtre: <input name="denumire" type="text" size="25" onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}">
        </form>
    </div>

    <div id="pager" class="pager" style="text-align:right; width:50%; float:left">
    <?php
	include_once(DOC_ROOT."app/templates/pager.php");
	?>
    </div>
	<?php
	echo toolbar('agent_id',$_SESSION['user']->getPermissionsByUrl($module_url));
	?>
    <div id="grid" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    &nbsp;
    </div>
</div>
<div id="frm" class="tab">
</div>

<div id="terti" class="tab">
</div>
<div id="produse" class="tab">
	
	<table width="100%" border="0" cellspacing="10" cellpadding="0">
    <tr>
      <td width="30%" valign="top">
      	<fieldset> 
        <legend>Cautare</legend>
        <div id="div_filtru">
       <input type="text" name="cautare_produs" id="cautare_produs" onKeyUp="" style="width:98%">
        </div>
        <div id="div_select_produse">
  <?php
  		$sql = "where gestiune_id = '". $_SESSION['user'] -> gestiune_id."'
		and tip_produs in ('marfa', 'mp')
		order by denumire asc";
		$produse = new ViewProduseGestiuni($sql);
		echo $produse -> select();
		?>
</div>
</fieldset>
        </td>
      <td width="70%" valign="top">
        <table width="100%" cellpadding="0"  cellspacing="0">
        	<tr>
           	  <td  valign="top">
              <fieldset>
				<legend>Adaugare Comision</legend>
             	 <div id="div_frm_continut" style="margin-top: 0px;"></div>
              </fieldset>   
              </td>
            </tr>
        </table>
      	
           <fieldset>
           	<legend>Comisioane</legend>
               <div id="div_preview_continut" style="height:300px; overflow:scroll; overflow-x:hidden; clear:both;">          </div>
               <?php
				 echo iconRemove("xajax_stergeComisionProdus($('#selected_agent_produs_id').val())");
			   ?>
           </fieldset>    
     
	 </td>
    </tr>
  </table>
</div>

</div>    </div>	
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