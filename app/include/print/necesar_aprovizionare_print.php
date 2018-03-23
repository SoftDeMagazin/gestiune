<?php 
class NecesarAprovizionarePrint
{
    var $html;
    var $necesar;
    var $gestiune;
    var $utilizator;
    var $numar_doc;
    var $data;
    var $continut;
     
    function __construct($necesar_id)
    {
        $this->necesar = new NecesarAprovizionare($necesar_id);
        $this->gestiune = $this->necesar->gestiune->denumire;
        $this->utilizator = $this->necesar->utilizator->nume;
        $this->data = c_data($this->necesar->data);
        $this->numar_doc = $this->necesar->numar_doc;
        
        $this->continut = new NecesarAprovizionareContinut(" WHERE doc_id = $necesar_id");
        
        $this->genereazaTransfer();
    }

    
    function antetNecesar()
    {
        $out = '<h2 align="center">NECESAR APROVIZIONARE NR. '.$this->numar_doc.'</h2>';
        
        $out .= '
	<table width="400" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td width="200"><strong>Gestiune: </strong></td>
	    <td width="200">'.$this->gestiune.'</td>
	  </tr>
	  <tr>
	    <td width="200"><strong>Utilizator: </strong></td>
	    <td width="200">'.$this->utilizator.'</td>
	  </tr>
	  <tr>
	    <td width="200"><strong>Data</strong></td>
	    <td width="200">'.$this->data.'</td>
	  </tr>
	</table>
	<br>
	<br>
	';
        return $out;
    }
    
    function componenteNecesar()
    {
        $nr_crt = 0;
        
        $out = "<p><strong>PRODUSE FINITE</strong></p>";
        
        $out .= '<table cellspacing="0" cellpadding="0" width="100%" border=1>
			  <TR>
				<TH scope="col">NR. CRT</TH>
				<TH scope="col">PRODUS</TH>
				<TH scope="col">UM</TH>
				<TH scope="col">CANTITATE DORITA</TH>
			  </TR>
	  		';
	  		
        foreach ($this->continut as $cnt)
        {
            $row = new TableRow();
            $nr_crt++;
            $row->addCell( new TableCell($nr_crt, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt->produs->denumire));
            $row->addCell( new TableCell($cnt->produs->unitate_masura->denumire, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt->cantitate_dorita, array("style"=>"text-align:right")));
            $out .= $row->getRow();
        }
        $out .= '</table>';
        return $out;
    }
    
    function componenteMpNecesar()
    {
        $sql = "select p.produs_id as produs_id,
       					p.denumire as produs,
       					um.denumire as um,
       					s.stoc as stoc,
       					SUM(cantitate_necesara) as cantitate_necesara
					from necesar_aprovizionare_continut_mp nacmp
					inner join produse p on p.produs_id = nacmp.produs_id
					inner join unitati_masura um on um.unitate_masura_id = p.unitate_masura_id
					left join stocuri s on s.produs_id = p.produs_id
					where nacmp.doc_id = ".$this->necesar->id."
					group by produs_id
					";
		global $db;			
					
        $data = $db->getRows($sql);
        
        $nr_crt = 0;
        
        $out = "<p><strong>MATERII PRIME</strong></p>";
        
        $out .= '<table cellspacing="0" cellpadding="0" width="100%" border=1>
			  <TR>
				<TH scope="col">NR. CRT</TH>
				<TH scope="col">PRODUS</TH>
				<TH scope="col">UM</TH>
				<TH scope="col">STOC</TH>
				<TH scope="col">CANTITATE NECESARA</TH>
				<TH scope="col">DIFERENTA</TH>
			  </TR>
	  		';
	  		
        foreach ($data as $cnt)
        {
            $row = new TableRow();
            $nr_crt++;
            $row->addCell( new TableCell($nr_crt, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt['produs']));
            $row->addCell( new TableCell($cnt['um'], array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt['stoc'], array("style"=>"text-align:right")));
			$row->addCell( new TableCell($cnt['cantitate_necesara'], array("style"=>"text-align:right")));
			$row->addCell( new TableCell($cnt['stoc']- $cnt['cantitate_necesara'], array("style"=>"text-align:right")));
            $out .= $row->getRow();
        }
        $out .= '</table>';
        return $out;
    }

    
    function genereazaTransfer()
    {
        $out .= $this->antetNecesar();
        $out .= $this->componenteNecesar();
        $out .= $this->componenteMpNecesar();
        
        $this->html = $out;
    }
    
    function getHtml()
    {
        return $this->html;
    }
}
?>
