<?php
require_once ("common.php");
require_once (__DIR__."/../app/templates/meta-head.php");

$xajax->printJavascript('../app/thirdparty/xajax/');
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#meniu').accordion({
            header: 'h3',
            animated: false
        });
        $('.calendar').datepicker({
            buttonImageOnly: true,
            hideIfNoPrevNext: true,
            duration: '',
            showOn: 'button',
            buttonImage: '/app/files/img/office-calendar.png'
        });
        $('#tabs').tabs();
		xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
		$('#categorie_id').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
		$('#tip_produs').multiSelect({selectAllText: 'Selecteaza tot!', oneOrMoreSelected: '*'}, function(el) {
					xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'));
				});
		
		$('#cautare_produs').keyup(
			function(event) {
				switch(event.keyCode) {
					case 40: {						
						$('#sel_produse').attr('selectedIndex', 0);
						$('#sel_produse').focus();						
					}break;
					default: {
						OnKeyRequestBuffer.modified('cautare_produs', 'xajax_filtruProduse', 100);
					}break;
				}
			}
		);
		
		$('#cautare_produs').focus(
			function() {
				this.select();
			}
		);
		
		$('#valuta').change(
			function () {
				xajax_afiseazaCurs($(this).val());
			}
		);
    });
    
</script>
<style type="text/css">
    
    #button {
        padding: .5em 1em;
        text-decoration: none;
    }
</style>
<title>HOME</title>
</head>
<body>
    <div id="left">
        <div id="left-content">
<?php require_once (__DIR__.'/../app/templates/header.php'); ?>
            <div id="tabs">
                <ul id="tabs-meniu">
                    <li>
                        <a href="#cautare">HOME</a>
                    </li>
                </ul>
                <div id="cautare" class="tab">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                       <tr>
                         <td valign="top">
						 <fieldset>
                    	<legend>Curs Valutar</legend>
						 <?php
						$curs = new Cursuri();
						$curs -> getLast();
						if(!count($curs)) $valoare = '0.00';
						else $valoare = $curs -> valoare;
						echo '<input name="curs_valutar" type="text" id="curs_valutar" value="'. $valoare .'" size="7">';
						$valute = new Valute("where extern = 'DA'");
						echo $valute -> select('', '', 'EUR');
						?>
                           <label>
                           <input type="button" name="btnSalveazaCurs" id="btnSalveazaCurs" value="Actualizeaza Curs" onClick="xajax_actualizareCurs($('#curs_valutar').val(), $('#valuta').val())">
                           </label>
                           <input type="button" name="btnSalveazaCurs" id="btnSalveazaCurs" value="Info Curs BNR" onClick="xajax_infoCursValutar()">
                           </fieldset>
                         </td>
                         <td valign="top"><form id="frmFiltre" onSubmit="return false;">
                         <fieldset>
                    	<legend>Filtre</legend>
                           <table width="100%" border="0" cellspacing="0" cellpadding="0">
                             <tr>
                               <td valign="middle">Cautare:</td>
                               <td valign="middle"><input
	name="denumire" type="text" size="25"
	onKeyDown="if(event.keyCode==13) {xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');}"></td>
                               <td valign="middle">Cat:</td>
                               <td valign="middle"><?php
$categorii = new Categorii("inner join categorii_gestiuni using(categorie_id) where gestiune_id = '". $_SESSION['user'] -> gestiune_id ."' order by denumire asc");
echo $categorii -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?></td>
                             </tr>
                             <tr>
                               <td valign="middle">Tip:</td>
                               <td valign="middle"><?php
$tip = new TipuriProduse("where 1 order by descriere asc");
echo $tip -> select("xajax_lista(xajax.getFormValues('frmFiltre'), xajax.getFormValues('frmPager'), 'first');");
?>
                               </td>
                               <td valign="middle">&nbsp;</td>
                               <td valign="middle">&nbsp;</td>
                             </tr>
                           </table>
                           </fieldset>
                         </form></td> 
                       </tr>
                  </table>
               	       

<fieldset><legend>Produse</legend>                    
                    
<div id="pager" class="pager" style="text-align: right;"><?php
    //var_dump(__DIR__."/../app/templates/pager.php");
	include_once(__DIR__."/../app/templates/pager.php");
	?>
</div>

<div id="grid" style="height: 540px; overflow: scroll; overflow-x: hidden; clear: both; margin-top: 10px;">
&nbsp;</div>  
<strong>*pretul de vanzare evidentiat cu rosu este cel de referinta</strong>              
</fieldset>


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
