<?php 
require("../../Config/Conectar.inc");

switch ($parametro) {
	case 'traer_desreguladoras':
		$json = array();
		$sql="SELECT * FROM $base_padron.desreguladoras";
		$rs = mysql_query($sql);

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = array('id'=> $row['id'],
							'convenio' => $row['convenio']
			);
		}
		echo json_encode($json);
		break;
	
	case 'traer_afiliados':

		$sql="SELECT convenio FROM $base_padron.desreguladoras where id=$id_desreguladora"; $rs = mysql_query($sql); $d = mysql_fetch_object($rs);$desreguladora = $d->convenio;

		$json = array();

		$sql="SELECT tpa.*,
			MID(tpa.estado,1,LOCATE('@',tpa.estado)) AS tipo_estado,
			-- MID(tpa.estado,LOCATE('@',tpa.estado,10)) 
			MID(tpa.estado,LOCATE('@',tpa.estado)+1,10) AS fecha_estado,
			$base_padron.intervalos_djap(tpa.cuil_titular,'ddjj','ultimo') AS djul
		FROM $base_padron.`tmp_padron_alta` tpa
		WHERE tpa.parentesco='Titular' AND tpa.estado  LIKE 'BAJA%' AND tpa.capita='$desreguladora'
		HAVING DATEDIFF(MID(tpa.estado,LOCATE('@',tpa.estado)+1,10),djul) < 0";
		
		$rs = mysql_query($sql);

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = array('cuil_titular'=> $row['cuil_titular'],
							'nd'=> $row['nd'],
							'apellido' => $row['apellido'],
							'nombre' => $row['nombre'],
							'fecha_estado' => $row['fecha_estado'],
							'djul' => $row['djul']
			);
		}
		echo json_encode($json);
		break;
	case 'generar_excel':
		
		$sql="SELECT convenio FROM $base_padron.desreguladoras where id=$id_desreguladora"; $rs = mysql_query($sql); $d = mysql_fetch_object($rs);$desreguladora = $d->convenio;
 
		$sql="SELECT 
			tpa.estado,tpa.cuil_titular AS Cuil_Titular,tpa.nd AS DNI,tpa.apellido as Apellido,tpa.nombre as Nombre,
			MID(tpa.estado,1,LOCATE('@',tpa.estado)-1) AS tipo_estado,
			MID(tpa.estado,LOCATE('@',tpa.estado)+1,10) AS Fecha_Estado,
			$base_padron.intervalos_djap(tpa.cuil_titular,'ddjj','ultimo') AS Declaracion_Jurada
		FROM $base_padron.`tmp_padron_alta` tpa
		WHERE tpa.parentesco='Titular' AND tpa.estado  LIKE 'BAJA%' AND tpa.capita='$desreguladora'
		HAVING DATEDIFF(MID(tpa.estado,LOCATE('@',tpa.estado)+1,10),Declaracion_Jurada) < 0";
		
		$rs = mysql_query($sql) or die(mysql_error());//exit();

		//echo json_encode($rs); exit();
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set("max_execution_time", 0);
		ini_set('memory_limit', '-1');
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/** Include PHPExcel */
		//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
		require_once '../../Lib/PHPExcel/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Alan")
								 ->setLastModifiedBy("Alan")
								 ->setTitle("My XLSX Test Document")
								 ->setSubject("My XLSX Test Document")
								 ->setDescription("Ejercicio multiple hojas.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0); 
		$rowCount = 1;  
		$column = 'A';

		$objPHPExcel->setActiveSheetIndex(0); 
		$rowCount = 1;  
		$column = 'A';
		for ($i = 1; $i < mysql_num_fields($rs); $i++){
		    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, mysql_field_name($rs,$i));
		    $column++;

		    $cell_name='';
		    $cell_name=$column.$rowCount;

		    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
		}
		$objPHPExcel->getActiveSheet()->getStyle( 'A1' )->getFont()->setBold( true );

		$rowCount = 2;

		while($row = mysql_fetch_row($rs)){  
   $column = 'A';

	   	for($j=1; $j<mysql_num_fields($rs);$j++){  
	        if(!isset($row[$j]))  

	            $value = NULL;  

	        elseif ($row[$j] != "")  

	            $value = strip_tags($row[$j]);  

	        else  

	            $value = "En Blanco";  


	        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
	        $column++;
	    }  

	    $rowCount++;
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("20");
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("20");
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("40");
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("40");
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("20");
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");

	$sheet = $objPHPExcel->getActiveSheet(); //Seteo mi primer pag
	$sheet->setTitle("$desreguladora");

	header('Content-Type: application/vnd.ms-excel'); 
	header('Content-Disposition: attachment;filename="Afiliados_De_baja_con_DDJJ:'.$desreguladora.'.xls"'); 
	header('Cache-Control: max-age=0'); 
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
	$objWriter->save('php://output'); 
	break;


}

?>