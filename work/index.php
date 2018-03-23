<?php
require_once("common.php");
require_once(DOC_ROOT."test_login.php");
require_once(DOC_ROOT."test_drept.php");
require_once(DOC_ROOT."app/templates/meta-head.php");

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
		$('#meniu').accordion('activate', <?=STOCURI?>);
		$('#tabs').tabs();
		xajax_cancel();
		$('.calendar').datepicker({ buttonImageOnly: true, hideIfNoPrevNext: true, duration: '', showOn: 'button', buttonImage:'/app/files/img/office-calendar.png' });
	xajax_lista(1);
	$('#grid').scroll(
		function() {
			var scrollPos = $(this).attr("scrollTop");
			var scrollSize = $(this).attr("scrollHeight");
			var page = scrollPos/500;
			page = page.toFixed(0);
			$('#data').css("top", scrollPos+"px");
			if(page != $('#curentpage').val()) {
				//$('#data').html('Loading...');
				//xajax_lista(page);
				//alert(page);
				$('#curentpage').val(page);
			}
		}
	);
	}
);
</script>
<title>Layout</title>

</head>
<body>
<div id="left" style="height: 100%;">
<div id="left-content">
<?php require_once(DOC_ROOT.'app/templates/header.php'); ?>
<div id="tabs">
<ul id="tabs-meniu">
	<li><a href="#lista" onClick="xajax_cancel();">Stocuri </a></li>
    </ul>
<div id="lista" class="tab">    
<?php
$produs = new Produse("WHERE denumire like '%COLA PAHAR%'");

$mp = $produs -> getMateriiPrime(8);

echo 'FISA PRODUCTIE: ', $produs -> denumire, '<br>';
foreach($mp as $m) {
	$prod = new Produse($m['produs_id']);
	echo $prod -> denumire, ': ', $m['cantitate'], ' ', $prod -> unitate_masura -> denumire;
	echo '<br>';
}
?>

</div>
</div>
</div>

</div>
<div id="right" style="height: 100%">
<?php
	echo menu();
?></div>
<div id="windows"></div>
</body>
</html>
