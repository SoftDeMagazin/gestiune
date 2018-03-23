<?php
class TastaturaNumerica
	{
	function printJavaScript()
		{
		return '
		<script type="text/javascript" language="javascript">
	function tnAddChar(chr)
		{
		who = \'tn_value\';
		if(document.getElementById(who).value == \'0\' && chr != \'.\') document.getElementById(who).value = \'\';
		if(document.getElementById(who).value == \'0\' && chr != \'.\') chr = \'\';
		document.getElementById(who).value = document.getElementById(who).value + chr;
		}
	function tnClearChar()
		{
		who = \'tn_value\';
		chr = document.getElementById(who).value;
		chr = chr.substr(0, chr.length - 1);
		document.getElementById(who).value = chr;
		}

	function tnAddCharDest(chr, who, focus)
		{
		if(document.getElementById(who).value == \'0\' && chr != \'.\') document.getElementById(who).value = \'\';
		if(document.getElementById(who).value == \'0\' && chr != \'.\') chr = \'\';
		document.getElementById(who).value = document.getElementById(who).value + chr;
		document.getElementById(focus).focus();
		}
		
	function tnClearCharDest(who, focus)
		{
		
		chr = document.getElementById(who).value;
		chr = chr.substr(0, chr.length - 1);
		document.getElementById(who).value = chr;
		document.getElementById(focus).focus();
		}


	function tnSave(dest, focus)
		{
		who = \'tn_value\';
		chr = document.getElementById(who).value;
		document.getElementById(dest).value = chr;
		if(focus != \'\') document.getElementById(focus).focus();
		}	
	
	function tn_operation(source, op)
	{
	document.getElementById(\'tn_first\').value = document.getElementById(source).value;
	document.getElementById(source).value = 0;
	document.getElementById(\'tn_operation\').value = op;
	}

function tn_result(dest)
	{
	switch(document.getElementById(\'tn_operation\').value)
		{
		case "add":
			{
			document.getElementById(dest).value = parseFloat(document.getElementById(dest).value) + parseFloat(document.getElementById(\'tn_first\').value);
			}break;
		case "minus":
			{
			document.getElementById(dest).value = parseFloat(document.getElementById(dest).value) - parseFloat(document.getElementById(\'tn_first\').value);
			}break;
		case "multiply":
			{
			document.getElementById(dest).value = parseFloat(document.getElementById(dest).value) * parseFloat(document.getElementById(\'tn_first\').value);
			}break;
		case "divide":
			{
			document.getElementById(dest).value = parseFloat(document.getElementById(dest).value) / parseFloat(document.getElementById(\'tn_first\').value);
			}break;
		}
	document.getElementById(dest).value = parseFloat(document.getElementById(dest).value).toFixed(2);
	}		
		</script>
		';
	
		
		}
	
	function html($dest, $focus)
		{
		return '
		<table id="tn_table"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td ><input name="btn6" type="button" class="tn_button" id="btn65" value="6" onClick="tnAddCharDest(\'6\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'6\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td ><input name="btn7" type="button" class="tn_button" id="btn74" value="7" onClick="tnAddCharDest(\'7\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'7\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td ><input name="btn8" type="button" class="tn_button" id="btn84" value="8" onClick="tnAddCharDest(\'8\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'8\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td ><input name="btn9" type="button" class="tn_button" id="btn94" value="9" onClick="tnAddCharDest(\'9\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'9\',\''. $dest .'\', \''. $focus .'\')"></td>
          </tr>
          <tr>
            <td><input name="btn2" type="button" class="tn_button" id="btn25" value="2" onClick="tnAddCharDest(\'2\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'2\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btn3" type="button" class="tn_button" id="btn36" value="3" onClick="tnAddCharDest(\'3\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'3\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btn4" type="button" class="tn_button" id="btn45" value="4" onClick="tnAddCharDest(\'4\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'4\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btn5" type="button" class="tn_button" id="btn55" value="5" onClick="tnAddCharDest(\'5\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'5\',\''. $dest .'\', \''. $focus .'\')"></td>
          </tr>
          <tr>
            <td><input name="btn0" type="button" class="tn_button" id="btn03" value="0" onClick="tnAddCharDest(\'0\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'0\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btn1" type="button" class="tn_button" id="btn14" value="." onClick="tnAddCharDest(\'.\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'.\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btn1" type="button" class="tn_button" id="btn14" value="1" onClick="tnAddCharDest(\'1\',\''. $dest .'\', \''. $focus .'\')" onDblClick="tnAddCharDest(\'1\',\''. $dest .'\', \''. $focus .'\')"></td>
            <td><input name="btnSterge" type="button" class="tn_button" id="btnSterge3" value="<<" onClick="tnClearCharDest(\''. $dest .'\', \''. $focus .'\')" onDblClick="tnClearCharDest(\''. $dest .'\', \''. $focus .'\')" style="background-color: #CC3300;"></td>
          </tr>
		</table>	
		';
		}	
		
	function printHTML($dest, $focus)
		{
		return '
		<div id="tn_div">
<table id="tn_table"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="4">
              <div align="center">
                <input name="tn_value" type="text" id="tn_value" style="" value="0" size="5">
            </div></td>
          </tr>
          <tr>
            <td ><input name="btn6" type="button" class="tn_button" id="btn65" value="6" onClick="tnAddChar(\'6\')" onDblClick="tnAddChar(\'6\')"></td>
            <td ><input name="btn7" type="button" class="tn_button" id="btn74" value="7" onClick="tnAddChar(\'7\')" onDblClick="tnAddChar(\'7\')"></td>
            <td ><input name="btn8" type="button" class="tn_button" id="btn84" value="8" onClick="tnAddChar(\'8\')" onDblClick="tnAddChar(\'8\')"></td>
            <td ><input name="btn9" type="button" class="tn_button" id="btn94" value="9" onClick="tnAddChar(\'9\')" onDblClick="tnAddChar(\'9\')"></td>
          </tr>
          <tr>
            <td><input name="btn2" type="button" class="tn_button" id="btn25" value="2" onClick="tnAddChar(\'2\')" onDblClick="tnAddChar(\'2\')"></td>
            <td><input name="btn3" type="button" class="tn_button" id="btn36" value="3" onClick="tnAddChar(\'3\')" onDblClick="tnAddChar(\'3\')"></td>
            <td><input name="btn4" type="button" class="tn_button" id="btn45" value="4" onClick="tnAddChar(\'4\')" onDblClick="tnAddChar(\'4\')"></td>
            <td><input name="btn5" type="button" class="tn_button" id="btn55" value="5" onClick="tnAddChar(\'5\')" onDblClick="tnAddChar(\'5\')"></td>
          </tr>
          <tr>
            <td><input name="btn0" type="button" class="tn_button" id="btn03" value="0" onClick="tnAddChar(\'0\')" onDblClick="tnAddChar(\'0\')"></td>
            <td><input name="btn1" type="button" class="tn_button" id="btn14" value="." onClick="tnAddChar(\'.\')" onDblClick="tnAddChar(\'.\')"></td>
            <td><input name="btn1" type="button" class="tn_button" id="btn14" value="1" onClick="tnAddChar(\'1\')" onDblClick="tnAddChar(\'1\')"></td>
            <td><input name="btnSterge" type="button" class="tn_button" id="btnSterge3" value="<<" onClick="tnClearChar()" onDblClick="tnClearChar()"style="background-color: #CC3300;"></td>
          </tr>
          <tr>
            <td colspan="2"><input name="tn_btnOk" type="button" id="tn_btnOk" value="Ok" onClick="tnSave(\''. $dest .'\', \''. $focus .'\');xajax_btnRenuntaDialog();"></td>
            <td colspan="2"><input name="tn_btnRenunta" type="button" id="tn_btnRenunta" value="Renunta" onClick="xajax_btnRenuntaDialog();"></td>
          </tr>
        </table>
</div>
		';
		}
	function printCalculator($dest)
		{
		return '
		<table width="240"  border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td><input name="tn_operation" type="hidden" id="tn_operation"></td>
    <td><input name="tn_first" type="hidden" id="tn_first"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
          <tr>
            <td ><div align="center">
              <input name="btn6" type="button" class="tn_button" id="btn65" value="+" onClick="tn_operation(\''. $dest .'\', \'add\');" onDblClick="tn_operation(\''. $dest .'\', \'add\');">
            </div></td>
            <td ><div align="center">
              <input name="btn7" type="button" class="tn_button" id="btn74" value="-" onClick="tn_operation(\''. $dest .'\', \'minus\');" onDblClick="tn_operation(\''. $dest .'\', \'minus\');">
            </div></td>
            <td ><div align="center">
              <input name="btn8" type="button" class="tn_button" id="btn84" value="x" onClick="tn_operation(\''. $dest .'\', \'multiply\');" onDblClick="tn_operation(\''. $dest .'\', \'multiply\');">
            </div></td>
            <td ><div align="center">
              <input name="btn92" type="button" class="tn_button" id="btn9" value="=" onClick="tn_result(\''. $dest .'\')" onDblClick="tn_result(\''. $dest .'\')">
            </div></td>
          </tr>
</table>

		';
		}		
	}
?>