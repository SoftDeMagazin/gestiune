<?php 
class InventarPrint
{
    var $html;
    
    function __construct($inventar_id)
    {
        $this->genereazaRaport($inventar_id);
    }
    
    function genereazaRaport($inventar_id)
    {
        global $db;
        
        $inventar = new Inventare("WHERE inventar_id=$inventar_id");
        
        $sql = "SELECT p.denumire AS produs,
					   um.denumire AS um,
					   ic.pret_achizitie,
					   ic.stoc_scriptic,
					   ic.stoc_faptic
				FROM inventar_continut ic
				INNER JOIN produse p on ic.produs_id = p.produs_id
				INNER JOIN unitati_masura um on p.unitate_masura_id = um.unitate_masura_id
				WHERE ic.inventar_id = $inventar_id";
				
        $out = "";
        $out .= '<h2 align="center">INVENTAR NR. '.$inventar->numar_doc.'</h2>';
        $out .= '<table width="400" border="0" cellspacing="0" cellpadding="0">
		  	<tr>
		    <td width="200"><strong>Gestiune:</strong></td>
		    <td width="200">'.$inventar->gestiune->denumire.'</td>
		  </tr>
		  <tr>
		    <td width="200"><strong>Utilizator:</strong></td>
		    <td width="200">'.$inventar->utilizator->nume.'</td>
		  </tr>
		  <tr>
		    <td width="200"><strong>Data</strong></td>
		    <td width="200">'.c_data($inventar->data_inventar).'</td>
		  </tr>
			</table>
			<br>
		<br>
		';
		
        $data = $db->getRows($sql);
        /*$dg = new DataGrid(array("style"=>"width:98%;margin:10px auto;", "border"=>"1", "id"=>"fisa_mag_cont", "class"=>"tablesorter"));
         $dg->addHeadColumn("Produs");
         $dg->addHeadColumn("UM");
         $dg->addHeadColumn("Stoc scriptic");
         $dg->addHeadColumn("Stoc faptic");
         $dg->addHeadColumn("Diferenta");
         
         $dg->setHeadAttributes(array());
         $nr_r = count($this);
         
         foreach ($data as $row)
         {
         $dg->addColumn($row['produs']);
         $dg->addColumn($row['um']);
         $dg->addColumn($row['stoc_scriptic']);
         $dg->addColumn($row['stoc_faptic']);
         $dg->addColumn($row['stoc_scriptic'] - $row['stoc_faptic']);
         $dg->index();
         }
         $out .= $dg->getDataGrid();*/

        $out.= '<table cellspacing="0" cellpadding="0" width="100%" border=1>
			  <TR>
				<TH scope="col">NR. CRT</TH>
				<TH scope="col">PRODUS</TH>
				<TH scope="col">UM</TH>
				<TH scope="col">PRET ACH.</TH>
				<TH scope="col">STOC SCRIPTIC</TH>
				<TH scope="col">STOC FAPTIC</TH>
				<TH scope="col">DIFERENTA</TH>
				<TH scope="col">DIFERENTA VALORICA</TH>
			  </TR>
	  		';
	  		
        $nr_crt = 0;
        
        foreach ($data as $r)
        {
            $row = new TableRow();
            $nr_crt++;
            $row->addCell( new TableCell($nr_crt, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($r['produs']));
            $row->addCell( new TableCell($r['um'], array("style"=>"text-align:center")));
			$row->addCell( new TableCell(douazecimale($r['pret_achizitie']), array("style"=>"text-align:right")));
            $row->addCell( new TableCell(treizecimale($r['stoc_scriptic']), array("style"=>"text-align:right")));
			$row->addCell( new TableCell(treizecimale($r['stoc_faptic']), array("style"=>"text-align:right")));
			$row->addCell( new TableCell(treizecimale($r['stoc_faptic'] - $r['stoc_scriptic']), array("style"=>"text-align:right")));
            $row->addCell( new TableCell(treizecimale(($r['stoc_faptic'] - $r['stoc_scriptic'])*$r['pret_achizitie']), array("style"=>"text-align:right")));
			$out .= $row->getRow();
        }
        $out .= '</table>';
        
        $this->html = $out;
    }
    
    function getHtml()
    {
        return $this->html;
    }
}
?>
