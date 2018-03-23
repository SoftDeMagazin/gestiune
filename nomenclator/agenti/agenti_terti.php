<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");
jscript("app/js/jquery.plugins/jquery.ui_multiselect/js/ui.multiselect.js");
stylesheet("app/js/jquery.plugins/jquery.ui_multiselect/css/ui.multiselect.css");
$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');
?>
<title>Nomenclator - Categorii</title>
<script type="text/javascript" >
$(document).ready(
	function() {
		$('#meniu').accordion({header: 'h3', animated: false});
		$('#meniu').accordion('activate', <?php echo $modul -> category_index - 1;?>);
		$('#tabs').tabs();
		$('#sel_tert').multiselect();
		//interface initialisation
	}
);
</script>
<link rel="stylesheet" type="text/css" href="../../app/js/jquery/css/south-street/jquery-ui-1.7.1.custom.css" />
</head>
<body>
<div id="left" style="height:100%;">
<div id="left-content"><?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
	<div id="header" >
    	<?php
			
			$soc = new Societati($_SESSION['user'] -> societate_id);
			echo $soc -> denumire;
			
		?>
    </div>
<div id="tabs">
	<ul id="tabs-meniu">
    	<li><a href="#lista">Asociere Agenti-Terti</a></li>
        <li><a href="#comisioane">Comisioane</a></li>
    </ul>
	<div id="lista" class="tab">
    <?php
	$agent_id = $_GET['agent_id'];
	$agent = new Agenti($agent_id);
	echo Html::h3($agent -> nume);
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		$lista_terti = $_POST['sel_tert'];
		$ats = new AgentiTerti("where agent_id = '$agent_id' and gestiune_id='". $_SESSION['user'] -> gestiune_id ."'");
		$terti_asociati = $agent -> getTertiAsociati($_SESSION['user'] -> gestiune_id);
		foreach($ats as $at) {
			if(!in_array($at -> tert_id, $lista_terti)) {
				$at -> delete();
			}
		}
		
		foreach($lista_terti as $tert_id) {
			if(!in_array($tert_id, $terti_asociati)) {
				$at = new AgentiTerti();
				$at -> gestiune_id = $_POST['gestiune_id'];
				$at -> tert_id = $tert_id;
				$at -> agent_id = $agent_id;
				$at -> save();
			}
		}
		
	}
	
	echo '<form action="" method="post">';
	$gestiune = new Gestiuni($_SESSION['user'] -> gestiune_id);
	$terti = new Terti();
	$terti -> getByGestiuneId($_SESSION['user'] -> gestiune_id);
	echo $gestiune -> gestiune_id();
	echo $terti -> selectUIMultiple($agent -> getTertiAsociati($_SESSION['user'] -> gestiune_id));
	echo Html::submit('submit', 'Salveaza');
	echo '</form>';

	?>
	</div>
    <div id="comisioane" class="tab">
    	<form id="frmComisioane" name="frmComisioane" action="" method="post">
        <div id="grid-comisioane" style="height:500px; overflow:scroll; overflow-x:hidden; clear:both;">
    	<?php
			$ats = new AgentiTerti("where agent_id = '$agent_id' and gestiune_id='". $_SESSION['user'] -> gestiune_id ."'");
			echo $ats -> lista();
		?>
        	
        </div>
        
        <input type="button" value="Salveaza" onClick="xajax_salveazaComisioane(xajax.getFormValues('frmComisioane'))" >
        </form>
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