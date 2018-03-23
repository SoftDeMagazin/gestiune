<?php 
class RptVanzariPos extends Rpt
{
    function __construct($filtre)
    {
        $this->filtre = $filtre;
    }
    
    function genereazaRaport()
    {
        $f = $this->filtre;
        global $db;
        
        /*
         * Filtrarea se poate face la nivelul unei societati, punct de lucru, gestiuni sau pos;
         * Pentru a se face filtrarea pe toate societatile, nu se selecteaza nimic
         * Pentru anumite societati, se selecteaza acele societati si se lasa restul deselectat
         * Pentru a se face filtrarea pe anumite puncte de lucru, se bifeaza acele puncte de lucru; in jos nu se completeaza nimic
         * Pentru a se filtra pe anumite gestiuni, se selecteaza gestiunile; in jos nu se selecteaza nimic;
         * Pentr a se filtra pe anumite posuri, se selecteaza acele posuri.
         */
        
        // -------------------- LOCATIA ------------------------
        
        $header_conditions = "";
        $pos_ids = array();
        $pos_in_sql = "";
        
        //daca s-au ales posuri, nu se mai tine cont de filtrele superioare
        if ($f['pos_id'])
        {
            $pos_ids = $f['pos_id'];
            
        }
        //daca s-au ales gestiuni, fara sa se bifeze posuri, se face pe acele gestiuni, indiferent de filtrele superioare
        else if ($f['gestiune_id'])
        {
            //obtinem gestiunile selectate
            $gestiuni_ids = implode(",", $f['gestiune_id']);
            
            //obtinem posurile asociate cu aceste gestiuni
            $posuri = new Posuri("where gestiune_id in ($gestiuni_ids)");
            
            //obtin idurile posurilor, sub forma de array
            foreach ($posuri as $pos)
                $pos_ids[] = $pos->pos_id;
        }
        // dc s-au selectat puncte de lucru, fara sa se selecteze gestiuni sau posuri, filtrarea se face pe acele punct de lucru
        else if ($f['punct_lucru_id'])
        {
            //obtinem punctele de lucru ca array
            $puncte_lucru_ids = implode(",", $f['punct_lucru_id']);
            
            //obtinem gestiunile de pe acele puncte de lucru
            $gestiuni = new Gestiuni("where punct_lucru_id in ($puncte_lucru_ids)");
            
            //obtinem gestiunile ca array de iduri
            $gestiuni_ids = array();
            foreach ($gestiuni as $g)
                $gestiuni_ids[] = $g->gestiune_id;
            $gestiuni_in_sql = implode(",", $gestiuni_ids);
            
            //obtinem posurile de pe gestiunile anterioare
            $posuri = new Posuri("where gestiune_id in ($gestiuni_in_sql)");
            
            //obtinem posurile ca array de iduri
            foreach ($posuri as $pos)
                $pos_ids[] = $pos->pos_id;
        }
        //dc s-au selectat societati, fara sa se selecteze puncte de lucru, gestiuni sau posuri, filtarea se face pe acele societati
        else if ($f['societate_id'])
        {
            //obtinem societatile selectate
            $societati_ids = $f['societate_id'];
            
            $societati_in_sql = implode(",", $societati_ids);
            
            //obtinem gestiunile de pe acele societati
            $gestiuni = new Gestiuni("where societate_id in ($societati_in_sql)");
            
            //obtinem gestiunile ca array de iduri
            $gestiuni_ids = array();
            foreach ($gestiuni as $g)
                $gestiuni_ids[] = $g->gestiune_id;
            $gestiuni_in_sql = implode(",", $gestiuni_ids);
            
            //obtinem posurile de pe gestiunile anterioare
            $posuri = new Posuri("where gestiune_id in ($gestiuni_in_sql)");
            
            //obtinem posurile ca array de iduri
            foreach ($posuri as $pos)
                $pos_ids[] = $pos->pos_id;
        }
        
        /* dc nu s-a intrat in niciun if, inseamna ca nu s-a selectat nimic din filtrele locatie, 
         * deci se face raportul pe toate soc, deci pe toate posurile, deci nu mai punem nicio conditie pe pos_id
         */
        if ( empty($pos_ids) == TRUE)
        {
            $header_conditions = "where 1";
        }
        // altfel, setam aici conditia
        else
        {
            //obtim o conditie de tip "where pos_id in (1,2,3)"
            $pos_in_sql = implode(",", $pos_ids);
            $header_conditions .= " and vanzari_pos.pos_id in ($pos_in_sql)";
        }
        
        // -------------- PERIOADA ------------------------
        
        if ($f['from'])
        {
            $de_la = data_c($f['from']);
            $header_conditions .= " and datediff(vanzari_pos.data_economica,$de_la) >0";
        }
        
        if ($f['end'])
        {
            $pana_la = data_c($f['end']);
            $header_conditions .= " and datediff(vanzari_pos.data_economica,$pana_la) <0";
        }
        
        // ------------- PRODUS -----------------------
        
        //aici pastram conditiile pentru produsele vandute pe pos
        $detail_conditions = "";
        
        // dc este completat ceva la produs, poate fi selectat din filtru, sau completat partial
        if ($f['produs_denumire'])
        {
            $detail_conditions = " and produse.denumire LIKE '%".$f['produs_denumire']."%'";
        }
        
        // dc s-a ales un produs din filtru
        if ($f['produs_id'])
        {
            $detail_conditions .= " and vanzari_pos_continut.produs_id =".$f['produs_id'];
        }
        
        // dc s-au bifat anumite categorii, se verifica ca produsele sa fie in ele; altfel se iau de pe toate categ
        if ($f['categorie_id'])
        {
            $categorii_ids = implode(",", $f['categorie_id']);
            $detail_conditions .= " and produse.categorie_id in ($categorii_ids)";
        }
        
        // ------------- CONSTRUIM SQL -------------------
        
        $sql = "
			select
				vanzari_pos.data_economica as data,
				posuri.cod as cod,  
				produse.denumire as denumire,
				unitati_masura.denumire as um, 
				vanzari_pos_continut.cantitate as cantitate,
				vanzari_pos_continut.pret_vanzare as pret_vanzare
			from vanzari_pos
			inner join vanzari_pos_continut on vanzari_pos.vp_id = vanzari_pos_continut.vp_id
			inner join produse on produse.produs_id = vanzari_pos_continut.produs_id
			inner join posuri on posuri.pos_id = vanzari_pos.pos_id
			inner join unitati_masura on produse.unitate_masura_id = unitati_masura.unitate_masura_id
			$header_conditions $detail_conditions 
			order by vanzari_pos.data_economica
		";

		
        $this->loadData($sql);
    }
    
    function getHtml()
    {
        $this->genereazaRaport();
        $out .= '<h2 align="center">RAPORT VANZARI POS</h2>';
        $out .= '<div align="center">'.$this->filtre['from'].' - '.$this->filtre['end'].'</div>';
        
        //adaug societati
        $out .= '<table cellspacing="10">';
        $out .= '<tr><td> Societati:</td><td> ';
        if ($this->filtre['societate_id'])
        {
            foreach ($this->filtre['societate_id'] as $id)
            {
                $societate = new Societati($id);
                $out .= $societate->denumire.", ";
            }
        }
        else
        {
            $out .= " Toate";
        }
        $out .= '</td></tr>';
        
        //adaug puncte de lucru
        $out .= '<tr><td> Puncte de lucru:</td><td> ';
        if ($this->filtre['punct_lucru_id'])
        {
            foreach ($this->filtre['punct_lucru_id'] as $id)
            {
                $pl = new PuncteLucru($id);
                $out .= $pl->denumire.", ";
            }
        }
        else
        {
            $out .= " Toate";
        }
        $out .= '</td></tr>';
        
        //adaug gestiuni
        $out .= '<tr><td> Gestiuni:</td><td> ';
        if ($this->filtre['gestiune_id'])
        {
            foreach ($this->filtre['gestiune_id'] as $id)
            {
                $gestiune = new Gestiuni($id);
                $out .= $gestiune->denumire.", ";
            }
        }
        else
        {
            $out .= " Toate";
        }
        $out .= '</td></tr>';
        
        //adaug posuri
        $out .= '<tr><td> Posuri:</td><td> ';
        if ($this->filtre['pos_id'])
        {
            foreach ($this->filtre['pos_id'] as $id)
            {
                $posuri = new Posuri($id);
                $out .= $posuri->cod.", ";
            }
        }
        else
        {
            $out .= " Toate";
        }
        $out .= '</td></tr>';
        
        //adaug posuri
        $out .= '<tr><td> Categorii:</td><td> ';
        if ($this->filtre['categorie_id'])
        {
            foreach ($this->filtre['categorie_id'] as $id)
            {
                $categorii = new Categorii($id);
                $out .= $categorii->denumire.", ";
            }
        }
        else
        {
            $out .= " Toate";
        }
        $out .= '</td></tr>';
        
        $dg = new DataGrid(array("style"=>"width:98%;margin:10px auto;", "border"=>"0", "id"=>"rpt_vanzari_pos", "class"=>"tablesorter"));
        $dg->addHeadColumn("Data economica");
        $dg->addHeadColumn("Cod");
        $dg->addHeadColumn("Produs");
		$dg->addHeadColumn("UM");
        $dg->addHeadColumn("Cantitate");
		$dg->addHeadColumn("Pret vanzare");
        $total_lei = 0;
        $total_val = 0;
        $articol = "";
        $nr_r = count($this->data);
        for ($i = 0; $i < $nr_r; $i++)
        {
            $row = $this->data[$i];
            
            $dg->addColumn($row['data']);
            $dg->addColumn($row['cod']);
            $dg->addColumn($row['denumire']);
			$dg->addColumn($row['um']);
            $dg->addColumn($row['cantitate']);
			$dg->addColumn($row['pret_vanzare']);
        }
        $out .= $dg->getDataGrid();
        return $out;
    }
}
?>
