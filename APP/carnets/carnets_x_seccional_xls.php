<?php 

//echo __DIR__;exit();
error_reporting(E_ALL);
ini_set('display_errors', 'On');
//include ("../../Conectar.inc");
//mysql_set_charset('utf8',$conexion);
ini_set("allow_url_fopen", 1);
//$url = __DIR__.'/ajax.php';
$url = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF'])."/ajax.php";//exit();

$fecha = $_GET['fecha'];

$url_final= $url.'?parametro=proceso_vista_previa&fecha='.$fecha;
$data = file_get_contents($url_final);

$json = json_decode($data);

foreach($json as $d){
	$newarr[] = array(
					'ID' => $d->id,
					'ID Titular' => $d->id_titular,
					'Seccional' => $d->seccional,
					'Cuil Titular' => $d->cuil_titular,										        		
					'Nº Beneficiario' => $d->nben,
					'Apellido y Nombre' => $d->ayn,
					'DNI' => $d->nd,										        		
					'Fecha Nacimiento' => $d->fn,
					'Vencimiento' => $d->venc_format,
					'CUIT' => $d->cuit,										        		
					'Empresa' => $d->empresa,
					'Tipo Beneficio Titular' => $d->tbt
					);
}

$json = json_encode($newarr);

$json = json_decode($json);

error_reporting(E_ALL);
ini_set("max_execution_time", 0);
ini_set('memory_limit', '-1');
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once __DIR__.'/../../Lib/PHPExcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Alan")
							 ->setLastModifiedBy("Alan")
							 ->setTitle("My XLSX Test Document")
							 ->setSubject("My XLSX Test Document")
							 ->setDescription("Test.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

//Variables iniciales
$objPHPExcel->setActiveSheetIndex(0); 
$rowCount = 1;  
$column = 'A';

//Cabecera
	$new = json_encode($json[0]);
	//var_dump(json_decode($new));exit();
	$new = json_decode($new);

	//print_r($new);exit();

	foreach ($new as $key => $value) {
		
		$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $key);
	    $column++;

	    $cell_name='';
	    $cell_name=$column.$rowCount;

	    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
	}
	$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getFont()->setBold( true );

	$rowCount = 2; 	
//Fin Cabecera
	
//Body
	foreach($json as $row){ 
	   	$column = 'A';

   		foreach($row as $key => $value){ 

	   		if(!$value){
	   			$value='';
	   		}
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
	        $column++;
	    }  

	    $rowCount++;
	} 
//Fin body

//Ordenamiento Columnas
	$orden_nombres='A'. 2 .':Z' . $rowCount;
	$objPHPExcel->getActiveSheet()
	    ->getStyle($orden_nombres)
	    ->getAlignment()
	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	/*
	$orden_nombres='L'. 2 .':L' . $rowCount;
	$objPHPExcel->getActiveSheet()
	    ->getStyle($orden_nombres)
	    ->getAlignment()
	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$orden_nombres='G'. 2 .':K' . $rowCount;
	$objPHPExcel->getActiveSheet()
	    ->getStyle($orden_nombres)
	    ->getAlignment()
	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	*/
//Tamaño Columnas
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("10");
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("10");
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("30");

	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("15");
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("18");
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("28");

	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("13");
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("17");
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("15");

	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("35");
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("35");
	
	$sheet = $objPHPExcel->getActiveSheet(); //Seteo mi primer pag
	$sheet->setTitle('Control de quirofano');

	//Export
	header('Content-Type: application/vnd.ms-excel'); 
	header('Content-Disposition: attachment;filename="Credenciales_por_seccionales:_FECHA:'.$fecha.''.'.xls"'); 
	header('Cache-Control: max-age=0'); 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
	$objWriter->save('php://output');

function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

?>