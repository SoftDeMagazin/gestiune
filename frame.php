<?php
define('DOC_ROOT', '');
require_once("common.php");
setcookie("gestiune_id", $_SESSION['user'] -> gestiune_id);
setcookie("uid", $_SESSION['user'] -> user_id);
require_once(DOC_ROOT."app/templates/meta-head.php");

$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/');

?>
<script type="text/javascript" >
$(document).ready(
	function() {
	setTimeout( function() {$('#frame').tabs(); $('#frame').tabs('select', 'gest_<?php echo $_SESSION['user'] -> gestiune_id; ?>');} , 1000);
	//xajax_switchGest(<?php // echo $_SESSION['user'] -> gestiune_id; ?>);
	
	}
);

</script>

<title>FRAME</title>
</head>
<body>
	<div id="frame" style="padding:0px 0px 0px 0px">
	<ul id="gest">
    	<?php
			$nr_r = 0;
			foreach($_SESSION['user'] -> gestiuni_asociate as $id) {
				$nr_r++;
				$gest = new Gestiuni($id);
				echo '<li style=""><a href="#gest_'. $gest -> id .'" onClick="xajax_switchGest('.$gest -> id.')">'. $gest -> denumire .' - '. $gest -> punct_lucru -> societate -> denumire .'</a></li>';
				if($nr_r == 3) break;
			}
			if($nr_r == 3) echo '<li style=""><a href="#more" id="more_link" onClick="xajax_more()">... <span id="gest_name"></span></a></li>';
			echo '<li style=""><a href="#out" onClick="xajax_logOut()">Log Out</a></li>';
		?>
    </ul>
    <div align="center"><a href="#" onClick="$('#gest').hide(); return false"><img src="app/files/img/go-top.png" height="10" border="0"></a><a href="#" onClick="$('#gest').show(); return false"><img src="app/files/img/go-bottom.png" height="10" border="0"></a></div>
    
	<?php
			$nr_r = 0;
			foreach($_SESSION['user'] -> gestiuni_asociate as $id) {
			$nr_r ++;
			$gest = new Gestiuni($id);
			echo '
    <div id="gest_'. $gest -> id .'" style="height:800px; padding:0px 0px 0px 0px" >
    	<iframe id="frame_gest_'. $gest -> id .'" frameborder=0 src="home/" width="100%" height="100%" scrolling="no"></iframe>
    </div>
			';
			if($nr_r == 3) break;
		}
			
			if($nr_r == 3) {
					echo '
			<div id="more" style="height:800px; padding:0px 0px 0px 0px" >
				<iframe id="frame_gest_'. $gest -> id .'" frameborder=0 src="home/" width="100%" height="100%" scrolling="no"></iframe>
			</div>
					';
			}

	?>
    	<div id="out" style="height:800px; padding:0px 0px 0px 0px" >
    
    	</div>
    </div>
<div id="windows"></div>    
</body>
</html>