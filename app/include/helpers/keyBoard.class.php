<?php
class KeyBoard
{
	function printJavaScript()
		{
		return '<script type="text/javascript" language="javascript">
function kb_insert(value, idInput)
{
document.getElementById(idInput).value = document.getElementById(idInput).value + value;
}
function kb_backspace(idInput)
{
document.getElementById(idInput).value = document.getElementById(idInput).value.substr(0, document.getElementById(idInput).value.length - 1);
}
	function kb_save(dest, focus)
		{
		who = \'kb_value\';
		chr = document.getElementById(who).value;
		document.getElementById(dest).value = chr;
		if(focus != \'\') document.getElementById(focus).focus();
		}	
</script>';
		}
	
	function printCss()
		{
		return '
		<style type="text/css">
		/* keyboard */
.kb_button_taste
	{
	width:50px;
	height:50px;
	background-color:#FFFFFF;
	}
.kb_key_num
	{
	width:50px;
	height:50px;
	background-color:#FF9900;
	}	
.kb_button_short
	{
	width:40px;
	height:40px;
	}	
.kb_button_space
	{
	width: 248px; 
	height:25px;
	background-color:#FFFFFF;
	}
.kb_buttonTastatura
	{
	background-image:url(../files/img/keyboard.png);
	background-repeat:no-repeat;
	background-position:center;
	width: 50px;
	}		
	
#kb_btnOk
{
width: 120px;
height: 60px;
background-color:#FFFF66;
}	
#kb_btnRenunta
{
width: 120px;
height: 60px;
background-color:#FF0000;
}	</style>
		';
		}
	
	function printHtml($dest, $focus)
		{
		$txt = 'Value: <input name="kb_value" id="kb_value" size="30" type="text">';
		$txt .= $this -> html('kb_value');
		$txt .= '
		<table width="100%">
		<tr>
            <td align="center"><input name="kb_btnOk" type="button" id="kb_btnOk" value="Ok" onClick="kb_save(\''. $dest .'\', \''. $focus .'\');xajax_btnRenuntaDialog();"></td>
            <td align="center"><input name="kb_btnRenunta" type="button" id="kb_btnRenunta" value="Renunta" onClick="xajax_btnRenuntaDialog();"></td>
          </tr></table>';
		return $txt;
		}
		
	function html($dest)
		{
		return '
<table  align="center" cellpadding="0" cellspacing="0" class="table">
      <tr>
        <td colspan="10"><div align="right">
          <input type="button" value="<----" style="width: 60px; height:30px; background-color:#FF0000;" name="backspace" onClick="kb_backspace(document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_backspace(document.getElementById(\'kb_dest\').value);this.blur()">
        </div></td>
        <td colspan="3">&nbsp;</td>
        <td><div align="center">
          <input name="kb_dest" type="hidden" id="kb_dest" value="'. $dest .'">
        </div></td>
      </tr>
      <tr>
        <td>
          <div align="center">
            <input name="button" type="button" class="kb_button_taste" value="Q" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
            <div align="center">
                <input name="button" type="button" class="kb_button_taste" value="W" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button2" type="button" class="kb_button_taste" value="E" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button3" type="button" class="kb_button_taste" value="R" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button4" type="button" class="kb_button_taste" value="T" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button5" type="button" class="kb_button_taste" value="Y" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button6" type="button" class="kb_button_taste" value="U" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button7" type="button" class="kb_button_taste" value="I" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button8" type="button" class="kb_button_taste" value="O" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button9" type="button" class="kb_button_taste" value="P" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td><td>
              <div align="center">
                <input type="button" value="7" class="kb_key_num" name="kp_home" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
              </div></td>
            <td>
              <div align="center">
                <input type="button" value="8" class="kb_key_num" name="kp_uparrow" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
            <td>
              <div align="center">
                <input type="button" value="9" class="kb_key_num" name="kp_pgup" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td><td rowspan="2" valign="top">
              <div align="center">
                  </div></td>
      </tr>
      <tr>
        <td>
          <div align="center">
            <input name="button10" type="button" class="kb_button_taste" value="A" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
            <div align="center">
                <input name="button11" type="button" class="kb_button_taste" value="S" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button12" type="button" class="kb_button_taste" value="D" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button13" type="button" class="kb_button_taste" value="F" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button14" type="button" class="kb_button_taste" value="G" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button15" type="button" class="kb_button_taste" value="H" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button16" type="button" class="kb_button_taste" value="J" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button17" type="button" class="kb_button_taste" value="K" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button18" type="button" class="kb_button_taste" value="L" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button19" type="button" class="kb_button_taste" value=";" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td><td>
              <div align="center">
                <input type="button" value="4" class="kb_key_num" name="kp_leftarrow" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
              </div></td>
            <td>
              <div align="center">
                <input type="button" value="5" class="kb_key_num" name="kp_5" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
            <td>
              <div align="center">
                <input type="button" value="6" class="kb_key_num" name="kp_rightarrow" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td></tr>
      <tr>
        <td>
          <div align="center"></div></td>
        <td>
            <div align="center">
                <input name="button20" type="button" class="kb_button_taste" value="Z" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button21" type="button" class="kb_button_taste" value="X" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button22" type="button" class="kb_button_taste" value="C" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button23" type="button" class="kb_button_taste" value="V" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button24" type="button" class="kb_button_taste" value="B" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button25" type="button" class="kb_button_taste" value="N" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button26" type="button" class="kb_button_taste" value="M" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button27" type="button" class="kb_button_taste" value="," onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
        <td>
          <div align="center">
            <input name="button28" type="button" class="kb_button_taste" value="." onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td><td>
              <div align="center">
                <input type="button" value="1" class="kb_key_num" name="kp_end" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
              </div></td>
            <td>
              <div align="center">
                <input type="button" value="2" class="kb_key_num" name="kp_downarrow" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
            <td>
              <div align="center">
                <input type="button" value="3" class="kb_key_num" name="kp_pgdn" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td><td rowspan="2" valign="top"> <div align="center"><br>
              </div></td>
      </tr>
      <tr>
        <td colspan="10">
          <div align="center">
            <input name="button" type="button" class="kb_button_space" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" value=" ">
            </div></td>
        <td colspan="1">
          <div align="left">
            <input name="kp_ins" type="button" class="kb_key_num"  value="0" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">

			</div></td>
        <td colspan="1">
          <div align="left">
            <input type="button" value="+" class="kb_key_num"  name="kp_plus" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
			</div></td>
        <td>
          <div align="center">
            <input type="button" value="." class="kb_key_num" name="kp_del" onClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()" onDblClick="kb_insert(this.value, document.getElementById(\'kb_dest\').value);this.blur()">
            </div></td>
  </tr>
    </table>';
		}	
}
?>