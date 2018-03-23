<?php
class AgentiProduse extends Model
{
	var $tbl="agenti_produse";
	var $_relations = array(
			"agent" => array("type" => "one", "model" => "Agenti", "key" => "agent_id"),
			"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		);
		
	var $_defaultForm = array(
		"agent_produs_id" => array("type" => "hidden"),
		"agent_id" => array("type" => "hidden"),
		"produs_id" => array("type" => "hidden"),
		"gestiune_id" => array("type" => "hidden"),
		"nume" => array("type" => "text", "label" => "Comision"),
		"comision" => array("type" => "text", "label" => "Comision"),
		);
	
	function frmContinut() {
		$out = '';
		$out .= '
		<div id="div_info_produs" style=" font-weight:bold;color:red;">&nbsp;</div>
		'. $this -> agent_produs_id() .'
		'. $this -> produs_id() .'
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Cantitate</td>
			<td align="left" >'. $this -> comision() .' </td>
		</tr>	  
		<tr>
			<td align="left" >UM:</td>
			<td align="left" >
			 <span id="div_frm_unitate_masura"></span></td>
		  </tr>
		</table>
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveazaComp" value="Salveaza" onClick=">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="">		</div>
		';
		return $this -> frmInnerHtml($out);
	}
		
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Agent");
		$dg -> addHeadColumn("Comision");
		$dg -> addHeadColumn("Sterge");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> agent -> nume);
			$dg -> addColumn('<input type="hidden" id="agent_produs_id" name="agent_produs_id[]" value="'. $this -> id .'"><input type="text" id="comision" name="comision[]" value="'. douazecimale($this -> comision) .'">');
			$dg -> addColumn(iconRemove("xajax_stergeComisionAgent('". $this -> id ."')"));
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
		function listaProduse($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$dg -> addHeadColumn("Produs");
		$dg -> addHeadColumn("Comision");
		$dg -> addHeadColumn("Sterge");
		$dg -> setHeadAttributes(array());
		$nr_r = count($this);
		for($i=0;$i<$nr_r;$i++)
			{
			$this -> fromDataSource($i);
			$dg -> addColumn($this -> produs -> denumire);
			$dg -> addColumn('<input type="hidden" id="agent_produs_id" name="agent_produs_id[]" value="'. $this -> id .'"><input type="text" id="comision" name="comision[]" value="'. douazecimale($this -> comision) .'">');
			$dg -> addColumn(iconRemove("xajax_stergeComisionAgent('". $this -> id ."')"));
			if($this -> id == $selected) $class="rowclick";
			else $class="";
			$ck = $this -> stringReplace($click);
			$dck = $this -> stringReplace($dblClick);
			$dg -> setRowOptions(array(
			"class" => $class,
			"onMouseOver"=>"$(this).addClass('rowhover')", 
			"onMouseOut"=>"$(this).removeClass('rowhover')",
			"onClick"=>"". $ck ."$('#selected_". $this -> key ."').val('". $this -> id ."');$('#tbl_". $this -> tbl ." tr.rowclick').removeClass('rowclick');$(this).addClass('rowclick');",
			"onDblClick"=>"$dck"
			));
			$dg -> index();
			}
		$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		$out .= $dg -> getDataGrid();
		return $out;	
	}
}
?>