<?php 
//Configs
	require_once './../../Lib/PHPExcel/Classes/PHPExcel.php';
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	ini_set("allow_url_fopen", 1);
	ini_set("max_execution_time", 0);
	ini_set('memory_limit', '-1');
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
//Fin Configs
//Extraccion Data & Declaracion Variables
	$url = 'http://osemm.smadm.com/padron/APP/tablero_control/ajax.php';

	//Variables extraidas de la URL
	$tipo = $_GET['tipo'];
	$periodo = $_GET['periodo'];
	$tipo_descarga = $_GET['tipo_descarga'];
	$grupo = $_GET['grupo'];

	$tipo_descarga_titulo = ucfirst(str_replace('_', ' ', $tipo_descarga));
	$filename=$tipo."_".$periodo." - ".$tipo_descarga_titulo.".xls";


	$url_final= $url.'?parametro=descargar&tipo='.str_replace(' ', '_', $tipo).'&periodo='.$periodo.'&tipo_descarga='.$tipo_descarga."&grupo=".$grupo;
	//echo $url_final;exit();
	//header('Location: '.$url_final);exit();#DEBUG
	//echo $url_final;exit();

	$data = file_get_contents($url_final);
	$json = json_decode($data);
//Fin Extraccion Data & Declaracion Variables
//Constructor
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Alan")
								 ->setLastModifiedBy("Alan")
								 ->setTitle("My XLSX Test Document")
								 ->setSubject("My XLSX Test Document")
								 ->setDescription("Cosa.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
	$objPHPExcel->setActiveSheetIndex(0); 
	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->setTitle($tipo);
//Fin Constructor
//Cabecera
	$rowCount = 1;  
	$column = 'A';
	$titulos = json_encode($json[0]);
	$titulos = json_decode($titulos);

	foreach ($titulos as $key => $value) {
		
		$sheet->setCellValue($column.$rowCount, $key);
	    $column++;

	    $cell_name='';
	    $cell_name=$column.$rowCount;

	    $sheet->getStyle( $cell_name )->getFont()->setBold( true );
	}
	$sheet->getStyle( 'A1' )->getFont()->setBold( true );

	$rowCount = 2; 	
//Fin Cabecera
//Body
	foreach($json as $row){ 
   	$column = 'A';
   	foreach($row as $key => $value){ 
			$sheet->setCellValue($column.$rowCount, $value);
      $column++;
    }  
    $rowCount++;
	} 
//Fin body
//Misc
	$column_izq = array('Telefono','OS origen','OS destino','CUIL','Nro Formulario');
	$column_der = array('Cantidad');//Estos titulos tienen informacion que va a ser colocada a la derecha (numeros generalmente, hola luis)

	#IMPORTANTE - Si los titulos son cambiados en el json recibido por el ajax, deben ser cambiados tambien en los dos arrays de comparacion.

	$column = "A";
	foreach ($titulos as $titulo => $value) {

		if(in_array($titulo, $column_der)){

				$orden_nombres=$column. 2 .':'.$column . $rowCount;
				$sheet
				    ->getStyle($orden_nombres)
				    ->getAlignment()
				    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}else if(in_array($titulo, $column_izq)){

				$orden_nombres=$column. 2 .':'.$column . $rowCount;
				$sheet
				    ->getStyle($orden_nombres)
				    ->getAlignment()
				    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				
		}
		$sheet->getColumnDimension($column)->setAutoSize(true);
		$column++;
	}
//Fin Misc
//Export
	header('Content-Type: application/vnd.ms-excel'); 
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0'); 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
	$objWriter->save('php://output');
//Fin Export

function cellColor($cells,$color){
    global $objPHPExcel;

    $sheet->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

?>