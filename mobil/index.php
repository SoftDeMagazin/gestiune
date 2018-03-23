<?php
require_once("common.php");
require_once("test_login.php");
require_once(DOC_ROOT."app/templates/meta-head-lite.php");
$xajax->printJavascript(DOC_ROOT.'app/thirdparty/xajax/'); 
?>
<script type="text/javascript">
</script>
</head>
<body>
<?php
echo '
<ul>
	<li><a href="raport_vanzari.php">Raport Vanzari</a></li>
	<li><a href="raport_note.php">Raport Incasari Locatii</a></li>
</ul>
';
?>	
<div id="windows"></div>
</body>