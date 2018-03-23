<?php
class divScroll
{
	function printJavaScript()
		{
		return '
		<script type="text/javascript">
		function divScroll(div_id, amount)
		{
		document.getElementById(div_id).scrollTop += amount;
		}
		</script>';
		}

	function printHtml($id, $width, $height, $innerHTML = '')
		{
		$innerWidth = $width - 50;
		$h = $height/2;
		return '
		<table width="'. $width .'" border="0" align="center" cellpadding="0" cellspacing="0">
  		<tr>
    		<td width="'.$innerWidth.'" height="'. $height .'" rowspan="2" scope="col">
			<div id="'. $id .'" style="width:'.$innerWidth.'px;height:'. $height .'px; overflow:hidden;">
	 		'. $innerHTML .'
			</div>
		</td>
    	<td width="50" height="'. $h .'" valign="top" scope="col"><div align="center"><input type="button" value=" " style="width:45px; height: 35px; background-image:url('. DOC_ROOT .'files/img/go-top.png); background-repeat:no-repeat; background-position:center;" onClick="divScroll(\''. $id .'\',-'.$height.')" onDblClick="divScroll(\''. $id .'\',-'.$height.')"></div></td>
	  </tr>
	  <tr>
   	 <td width="50" height="'. $h .'" valign="bottom"><div align="center"><input type="button" value=" " style="width:45px; height: 35px; background-image:url('. DOC_ROOT .'files/img/go-bottom.png); background-repeat:no-repeat; background-position:center;" onClick="divScroll(\''. $id .'\','.$height.')" onDblClick="divScroll(\''. $id .'\','.$height.')"></div></td>
  	</tr>
	</table> 
		';
		}
}

?>