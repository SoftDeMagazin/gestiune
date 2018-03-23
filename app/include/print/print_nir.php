<?php
class PrintNir {
	var $cls;
	function __construct($nir_id) {
		$nir = new Niruri($nir_id);
		$tert = $nir -> tert;
		$gestiune = $nir -> gestiune;
		switch($gestiune -> tip_gestiune) {
			case "pret_vanzare": {
				switch($tert -> tip) {
					case "intern": {
						$this -> cls = new PrintNirInternPv($nir_id);
					}break;
					case "extern_ue": {
						$this -> cls = new PrintNirExternPv($nir_id);
					}break;
				}
			}break;
			case "pret_achizitie": {
					switch($tert -> tip) {
					case "intern": {
						$this -> cls = new PrintNirInternPa($nir_id);
					}break;
					case "extern_ue": {
						$this -> cls = new PrintNirExternPa($nir_id);
					}break;
				}
			}break;
		}
	
	}
	
	function getHtml() {
		return $this -> cls -> getHtml();
	}
}
?>