<?php
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load("xls/nir.xls");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('C6', $furnizor -> obj -> nume);
$objPHPExcel->getActiveSheet()->setCellValue('C7', $this -> obj -> numar_factura);
$objPHPExcel->getActiveSheet()->setCellValue('C3', $this -> obj -> numar_nir);
$objPHPExcel->getActiveSheet()->setCellValue('C5', date("d/m/Y", strtotime($this -> obj -> data_factura)));

$objPHPExcel->getActiveSheet()->setCellValue('A'.$linie, $i);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$linie,$objComp -> denumire);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$linie,$objComp -> um);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$linie,$objComp -> cant);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$linie,$objComp -> pret_ach);
$objPHPExcel->getActiveSheet()->setCellValue('F'.$linie,$objComp -> val_ach);
$objPHPExcel->getActiveSheet()->setCellValue('G'.$linie,$objComp -> tva_ach);
$objPHPExcel->getActiveSheet()->setCellValue('H'.$linie,$objComp -> total_tva_ach);
$objPHPExcel->getActiveSheet()->setCellValue('I'.$linie,$objComp -> adaos_unit);
$objPHPExcel->getActiveSheet()->setCellValue('J'.$linie,$objComp -> total_adaos);
$objPHPExcel->getActiveSheet()->setCellValue('K'.$linie,$objComp -> tva_vanzare);
$objPHPExcel->getActiveSheet()->setCellValue('L'.$linie,$objComp -> total_tva_vanzare);
$objPHPExcel->getActiveSheet()->setCellValue('M'.$linie,$objComp -> pret_vanzare);
$objPHPExcel->getActiveSheet()->setCellValue('N'.$linie,$objComp -> val_total);	

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter -> save("temp/".$time.".xls");
?>