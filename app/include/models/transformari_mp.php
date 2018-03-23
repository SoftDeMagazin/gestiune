<?php
class TransformariMp extends Model
{
	var $tbl="transformari_mp";
	var $_relations = array(
		"transformare" => array("type" => "one", "model" => "Transformari", "key" => "transformare_id"),
		"produs" => array("type" => "one", "model" => "Produse", "key" => "produs_id"),
		);
	var $_defaultForm = array(
		"trans_mp_id" => array("type" => "hidden"),
		"cantitate" => array("type" => "text"),
		"produs_id" => array("type" => "hidden"),
		);
		

		function lista($click="", $dblClick="" , $selected=0)
	{
		$dg = new DataGrid(array("style" => "width:98%;margin:0px auto;" , "border" => "0", "id" => "tbl_". $this -> tbl ."", "class" => "tablesorter"));
		$nr_r = count($this);
		if($nr_r) {
			$dg -> addHeadColumn("Produs");
			$dg -> addHeadColumn("Cantitate");
			$dg -> setHeadAttributes(array());
			
			for($i=0;$i<$nr_r;$i++)
				{
				$this -> fromDataSource($i);
				$dg -> addColumn($this -> produs -> denumire);
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

	
	function frmContinut() {
		$out = '';
		$out .= '
		<div id="div_info_produs" style=" font-weight:bold;color:red;">&nbsp;</div>
		'. $this -> trans_mp_id() .'
		'. $this -> produs_id() .'
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Cantitate</td>
			<td align="left" >'. $this -> cantitate() .' </td>
		</tr>	  
		<tr>
			<td align="left" >UM:</td>
			<td align="left" >
			 <span id="div_frm_unitate_masura"></span></td>
		  </tr>
		</table>
		<div align="right">
<input type="button" name="btnSalveaza" id="btnSalveazaComp" value="Salveaza" onClick="xajax_saveComponenta(xajax.getFormValues(\'frm_transformari_mp\'), $(\'#trans_pf_id\').val())">
		<input type="button" name="btnAnuleaza" id="btnAnuleaza" value="Anuleaza" onClick="xajax_frmMateriePrima(0, $(\'#trans_pf_id\').val())">		</div>
		';
		return $this -> frmInnerHtml($out);
	}
	
	function getValoare() {
        $produs = new Produse($this -> produs_id);
		$loturi = $produs->getLoturiFifo($this ->cantitate, $this -> transformare -> gestiune_id);
		$val_loturi = 0;
		foreach ($loturi as $lot) {
		    $val_loturi += $lot[0]->pret_intrare_ron * $lot[1];
		}
		return $val_loturi;
	}	
}
?>