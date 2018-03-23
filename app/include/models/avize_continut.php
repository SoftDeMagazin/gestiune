<?php
class AvizeContinut extends Model
{
	var $tbl="avize_continut";
	var $_relations = array(
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		"aviz" => array("type" => "one", "model" => "Avize", "key" => "aviz_id"),
		);
	var $_defaultForm = array(
		"continut_id" => array("type" => "hidden"),
		"produs_id" => array("type" => "hidden"),
		"cantitate" => array("type"=>"text", "label"=>"Cantitate", "attributes" => array()),
		"pret_vanzare_ron" => array("type"=>"text", "label"=>"Cantitate", "attributes" => array("style" => "width:100px")),
		"pret_vanzare_val" => array("type"=>"text", "label"=>"Cantitate", "attributes" => array("style" => "width:100px")),
		);
	
	function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire);
				$dg -> addColumn($this -> cantitate);
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
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}	
	
	function listaDocPv($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			$dg -> addHeadColumn("Pret LEI");
			$dg -> addHeadColumn("Pret ".$this -> aviz -> valuta);
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire);
				$dg -> addColumn($this -> cantitate);
				$dg -> addColumn($this -> pret_vanzare_ron);
				$dg -> addColumn($this -> pret_vanzare_val);
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
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	/**
	 * returneaza lista componentelor pentru tiparire
	 * @return 
	 */
	function listaPrint()
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "1", "id" => "tbl_". $this -> tbl ."", "cellpadding" => "0", "cellspacing" => "0"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs");
			$dg -> addHeadColumn("UM");
			$dg -> addHeadColumn("Cant");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
				$dg -> addColumn($this -> produs -> unitate_masura -> denumire);
				$dg -> addColumn($this -> cantitate);
				if($this -> id == $selected) $class="rowclick";
				else $class="";
				$ck = $this -> stringReplace($click);
				$dck = $this -> stringReplace($dblClick);
				$dg -> index();
				}
			$out = '<input type="hidden" id="selected_'. $this -> key .'" name="selected_'. $this -> key .'" value="'. $selected .'">';	
		}
		$out .= $dg -> getDataGrid();
		return $out;	
	}
	
	
	/**
	 * returneaza html cu formularul pentru adaugare componenta
	 * @return 
	 */
	function frmContinut() {
		$out = '';
		$out .= '
		<div id="div_info_produs" style=" font-weight:bold;color:red;">&nbsp;</div>
		'. $this -> continut_id() .'
		'. $this -> produs_id() .'
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Cantitate</td>
			<td align="left" colspan="2">'. $this -> cantitate() .' </td>
		</tr>	  
		<tr>
			<td align="left" >UM:</td>
			<td align="left" colspan="2">
			 <span id="div_frm_unitate_masura"></span></td>
		  </tr>';
		
		if($this -> aviz -> tip_aviz == "doc_pv") {
				$out .= '
		<tr>
			<td align="left" >&nbsp;</td>
			<td align="center">'. $this -> aviz -> valuta .'</td>
			<td align="center">LEI</td>
		  </tr>		
		<tr>
			<td align="left" >Pret</td>
			<td align="center">'. $this -> pret_vanzare_val() .'</td>
			<td align="center">'. $this -> pret_vanzare_ron() .'</td>
		  </tr>';
		} 
		
		$out .= '</table>
	
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveazaComp" value="Salveaza" onClick="xajax_saveComponenta(xajax.getFormValues(\'frm_avize_continut\'), $(\'#aviz_id\').val())">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="xajax_frmComponenta(0, $(\'#aviz_id\').val())">		</div>
		';
		return $this -> frmInnerHtml($out);
	}
}
?>