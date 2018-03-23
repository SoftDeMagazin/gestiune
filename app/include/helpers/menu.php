<?php

function menu(){
	$rol_id = $_SESSION['user']->rol_id;
	$modules = new Module("where modul_activ=1 order by category_index,item_index asc");
	//global $db;
	//$rows = $db->getRows($sql);
	//$modules->fromCollection($rows);
	
	if($modules->count() == 0) return;
	
	$out='';
	$out.=Html::divstart("id=\"meniu\"");
	
	$parent = $modules[0]->parinte;
	$count = $modules->count();
	$debug="";
	$i=0;
	while($i<$count) {
		$j=$i;
		$menuItems="";
		$out.= Html::divstart();
		$out.= Html::h3(Html::link('#',$modules[$j]->parinte));
		$out.= Html::divstart("class=\"menu-item\"");		
		
		while($modules[$j]->parinte == $parent){
			if(isset($_SESSION['user'] -> permissions[$modules[$j] -> id])) {
				$hasView = $_SESSION['user'] -> permissions[$modules[$j] -> id] -> getView();
			}
			else {
				$hasView = false;
			}	
			if($hasView) {
				$menuItems.=Html::li(Html::link(DOC_ROOT.$modules[$j]->url,$modules[$j]->denumire));
			}
			else {
				$menuItems.=Html::li($modules[$j]->denumire);
			}	
			$j+=1;
		}
		$parent = $modules[$j]->parinte;
		
		$out.=Html::ul($menuItems);
		$out.=Html::divend();
		$i=$j;
	}
	
	$out.=Html::divend();
	$_SESSION['meniu'] = $out;
	return $out;
}

?>