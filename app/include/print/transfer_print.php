<?php 
class TransferPrint
{
    var $html;
    var $transfer;
    var $gestiune_sursa;
    var $gestiune_destinatie;
    var $data;
    var $continut;
    
    function __construct($transfer_id)
    {
        $this->transfer = new Transferuri($transfer_id);
        $this->gestiune_sursa = $this->transfer->gestiune_sursa->denumire;
        $this->gestiune_destinatie = $this->transfer->gestiune_destinatie->denumire;
        $this->data = c_data($this->transfer->data);
        
        $this->componente = new TransferuriComponente(" WHERE transfer_id = $transfer_id");
        
        $this->genereazaTransfer();
    }

    
    function antetTransfer()
    {
        $out = '<h2 align="center">TRANSFER NR. '.$this->transfer->id.'</h2>';
        
        $out .= '
	<table width="400" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td width="200"><strong>Gestiune sursa:</strong></td>
	    <td width="200">'.$this->gestiune_sursa.'</td>
	  </tr>
	  <tr>
	    <td width="200"><strong>Gestiune destinatie:</strong></td>
	    <td width="200">'.$this->gestiune_destinatie.'</td>
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
    
    function componenteTransfer()
    {
        $nr_crt = 0;
        
        $out = '<table cellspacing="0" cellpadding="0" width="100%" border=1>
			  <TR>
				<TH scope="col">NR. CRT</TH>
				<TH scope="col">PRODUS</TH>
				<TH scope="col">UM</TH>
				<TH scope="col">CANTITATE</TH>
			  </TR>
	  		';
	  		
        foreach ($this->componente as $cnt)
        {
            $row = new TableRow();
            $nr_crt++;
            $row->addCell( new TableCell($nr_crt, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt->produs->denumire));
            $row->addCell( new TableCell($cnt->produs->unitate_masura->denumire, array("style"=>"text-align:center")));
            $row->addCell( new TableCell($cnt->cantitate, array("style"=>"text-align:right")));
            $out .= $row->getRow();
        }
        $out .= '</table>';
        return $out;
    }
    
    function genereazaTransfer()
    {
        $out .= $this->antetTransfer();
        $out .= $this->componenteTransfer();
        
        $this->html = $out;
    }
    
    function getHtml()
    {
        return $this->html;
    }
}
?>
