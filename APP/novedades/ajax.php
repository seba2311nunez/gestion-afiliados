<?php
ini_set('display_errors', 1); 
ini_set('log_errors',1); 
error_reporting(E_ALL); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include('../../Config/Conectar.inc');

if(!$_SESSION['id_user']){
	echo "Su session caduco, por favor vuelva a loguearse"; exit();
}
else{
	$id_usuario = $_SESSION['id_user'];	
}


switch ($parametro){

	case 'crear_periodo_presentacion_novsss':
		// code...

		$update_cierra = "UPDATE ".N_BASE_HISTORICOS.".lotes
								SET estado='Cerrado'
								WHERE estado='Proceso' 
								AND proceso='novedades_exportables'";

		mysql_query($update_cierra) or die(mysql_error());

		$insert_periodo = "INSERT INTO ".N_BASE_HISTORICOS.".lotes(descripcion,archivo,obrasocial,proceso,id_usuario,estado)											
			SELECT MID(ADDDATE(archivo,INTERVAL 1 MONTH),1,7) AS descripcion,ADDDATE(archivo,INTERVAL 1 MONTH) AS archivo, ADDDATE(obrasocial,INTERVAL 1 MONTH) AS fecha_cierre,'novedades_exportables',1,'Proceso'
			FROM ".N_BASE_HISTORICOS.".lotes WHERE proceso='novedades_exportables'
			ORDER BY archivo DESC
			LIMIT 1";

		mysql_query($insert_periodo) or die(mysql_error()."ERROR en el alta");

		$id_lote = mysql_insert_id();

		mysql_query("CALL ".N_BASE_PADRON.".novedades_crea_nuevo_periodo()");

		echo $id_lote;

		break;
	
	case 'CrearArchivo':
		$call="CALL $base_padron.NOV_presentar_periodo($id_lote);";
		mysql_query($call) or die (mysql_error()."<br>".$call);
		
		$sql="SELECT * from $base_padron.tmp_novedades";
		//$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$result = mysql_query($sql) or die(mysql_error().$sql);

		$filename=strtoupper(INST_NAME)."_novedades_".$periodo.".txt";
		$dir='files/'.$filename;

		// Create a file handle for writing
		$file = fopen($dir, "w");

		// Loop through the rows of the result and write them to the file
		while ($row = mysql_fetch_assoc($result)) {
		    $line = implode("|", $row) . "\n";
		    fwrite($file, utf8_decode($line));
		}

		// Close the file handle and database connection
		fclose($file);
		mysql_close($conexion);



		if($file){
			header("Content-disposition: attachment; filename=". $filename);
			header("Content-type:".mime_content_type($dir));
			readfile($dir);
		}
		else{
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}


		/*
		$filename="novedades_".$periodo."_fec_cierre_".$fecha_cierre.".txt";
		$dir='files/'.$filename;
		$archivo =createArchive($rs,$dir);
		
		if($archivo){
			header("Content-disposition: attachment; filename=". $filename);
			header("Content-type:".mime_content_type($dir));
			readfile($dir);
		}
		else{
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
		*/
		break;	

	case 'lst_afiliados_presentacion':

			$sql = "CALL $base_padron.NOV_mostrar_lote($id_lote) ";

			$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
			$json = array();
			while ($row = mysql_fetch_assoc($result)) {
			    $json[] = $row;
			}
			echo json_encode($json);
		break;

	case 'lst_afiliados_presentacion_ssp':

	$id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
	$start   = isset($_GET['start']) ? intval($_GET['start']) : 0;
	$length  = isset($_GET['length']) ? intval($_GET['length']) : 50;
	$search  = isset($_GET['search']['value']) ? mysql_real_escape_string($_GET['search']['value']) : '';

	$limit  = $length;
	$offset = $start;

	$sql = "CALL $base_padron.NOV_mostrar_lote($id_lote, $limit, $offset)";
	mysql_query($sql) or die(mysql_error());

	$where = '';
	if ($search != '') {
		$where = "WHERE nd LIKE '%$search%' OR cuil LIKE '%$search%' OR ayn LIKE '%$search%' OR tbt LIKE '%$search%' OR errores LIKE '%$search%' OR rechazos LIKE '%$search%'";
	}

	$total_rs = mysql_query("SELECT COUNT(*) AS total FROM $base_padron.tmp_afiliados_novedades_mostrar $where");
	$total_row = mysql_fetch_assoc($total_rs);
	$recordsTotal = intval($total_row['total']);

	$sql = "SELECT * FROM $base_padron.tmp_afiliados_novedades_mostrar $where LIMIT $offset, $limit";
	$result = mysql_query($sql) or die(mysql_error());

	$data = array();
	$index = $start + 1;
	while ($row = mysql_fetch_assoc($result)) {

	    $acciones = "<div class='btn-group btn-group-default'>
	        <button data-toggle='dropdown' class='btn btn-default dropdown-toggle' type='button'>
	            <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
	        </button>
	        <ul class='dropdown-menu'>
	            <li>
	                <a class='btnVerAfiliado' data-id_titular='{$row['id_titular']}' data-id_afiliado='{$row['id_afiliado']}'>
	                    Ver info afiliado
	                </a>
	            </li>
	            <li>
	                <a class='btnQuitarFctPresentacion' data-id_expo='{$row['id_expo']}' data-id_lote='{$id_lote}'>
	                    Quitar presentación
	                </a>
	            </li>
	            <li>
	                <a class='btnCronologia' data-id_persona='{$row['id_persona']}' data-toggle='modal' data-target='#modalCronologia'>
	                    Cronología
	                </a>
	            </li>
	        </ul>
	    </div>";

	    $data[] = array(
	        $index++,
	        $acciones,
	        $row['desreguladora'],
	        $row['cuil_titular'],
	        $row['parentesco'],
	        $row['cuil'],
	        $row['nd'],
	        $row['ayn'],
	        $row['sexo'],
	        $row['edad'],
	        $row['fn'],
	        $row['incapacidad'],
	        $row['tbt'],
	        $row['tipo_mov'],
	        $row['fecha_movimiento'],
	        $row['errores'],
	        $row['rechazos']
	    );
	}

	echo json_encode(array(
		"draw" => intval($_GET['draw']),
		"recordsTotal" => $recordsTotal,
		"recordsFiltered" => $recordsTotal,
		"data" => $data
	));
	break;


		echo json_encode(array(
			"draw" => intval($_GET['draw']),
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsTotal,
			"data" => $data
		));
		break;

	case 'resumen_tbt_y_errores':

		$id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;

		// Ejecutar el procedimiento que llena la tabla temporal
		$sql = "CALL $base_padron.NOV_mostrar_lote($id_lote, 1000000, 0)";
		mysql_query($sql) or die(mysql_error());

		// Asegurar ejecución previa del stored que carga la tabla tmp_afiliados_novedades_mostrar
		$sql_tbt = "SELECT tbt, COUNT(*) AS cantidad 
		            FROM $base_padron.tmp_afiliados_novedades_mostrar 
		            GROUP BY tbt";
		$rs_tbt = mysql_query($sql_tbt) or die(mysql_error());
		$resumen_tbt = array();
		while($row = mysql_fetch_assoc($rs_tbt)){
		    $clave = $row['tbt'] !== '' ? $row['tbt'] : 'Sin TBT';
		    $resumen_tbt[] = array(
		        'tbt' => $clave,
		        'cantidad' => intval($row['cantidad'])
		    );
		}

		$sql_err = "SELECT COALESCE(TRIM(REPLACE(errores, '\n', '')), 'Sin errores') AS errores, COUNT(*) AS cantidad 
		            FROM $base_padron.tmp_afiliados_novedades_mostrar 
		            GROUP BY errores";
		$rs_err = mysql_query($sql_err) or die(mysql_error());
		$resumen_errores = array();
		while($row = mysql_fetch_assoc($rs_err)){
		    $resumen_errores[] = array(
		        'errores' => $row['errores'],
		        'cantidad' => intval($row['cantidad'])
		    );
		}

		echo json_encode(array(
		    'resumen_tbt' => $resumen_tbt,
		    'resumen_errores' => $resumen_errores
		));

		break;


	case 'exportar_afiliados_excel':

	    require_once '../../Lib/PHPExcel/Classes/PHPExcel.php';
	    if (ob_get_length()) ob_end_clean();

	    error_reporting(E_ALL);
	    ini_set('display_errors', 'On');
	    ini_set("allow_url_fopen", 1);
	    ini_set("max_execution_time", 0);
	    ini_set('memory_limit', '-1');

	    $id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
	    $filename = "afiliados_exportados_lote_" . $id_lote . ".xlsx";

	    // Ejecutar el stored
	    mysql_query("CALL $base_padron.NOV_mostrar_lote($id_lote, 1000000, 0)") or die(mysql_error());

	    // Directamente consultar la tabla temporal
	    $result = mysql_query("SELECT * FROM $base_padron.tmp_afiliados_novedades_mostrar") or die(mysql_error());

	    $objPHPExcel = new PHPExcel();
	    $sheet = $objPHPExcel->getActiveSheet();
	    $sheet->setTitle("Afiliados");

	    $headers = ['#', 'Gerenciadora', 'CUIL Titular', 'Parentesco', 'CUIL', 'DNI', 'AyN', 'Sexo', 'Edad', 'Fecha Nac.', 'Incapacidad', 'Tipo Benef.', 'Movimiento', 'Fecha Movimiento', 'Errores' , 'Rechazos'];

	    $col = 0;
	    foreach ($headers as $h) {
	        $sheet->setCellValueByColumnAndRow($col++, 1, $h);
	    }

	    $rowIndex = 2;
	    $i = 1;
	    while ($row = mysql_fetch_assoc($result)) {
	        $col = 0;
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $i++);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['desreguladora']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil_titular']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['parentesco']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['nd']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['ayn']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['sexo']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['edad']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['fn']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['incapacidad']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tbt']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tipo_mov']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['fecha_movimiento']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['errores']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['rechazos']);
	        $rowIndex++;
	    }

	    // Encabezados para la descarga
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header("Content-Disposition: attachment;filename=\"$filename\"");
	    header('Cache-Control: max-age=0');

	    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $writer->save('php://output');
	    exit;

	    break;
	
	case 'exportar_afiliados_excel':

	    require_once 'PHPExcel/Classes/PHPExcel.php';
	    if (ob_get_length()) ob_end_clean();

	    error_reporting(E_ALL);
	    ini_set('display_errors', 'On');
	    ini_set("allow_url_fopen", 1);
	    ini_set("max_execution_time", 0);
	    ini_set('memory_limit', '-1');

	    $id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
	    $filename = "afiliados_exportados_lote_" . $id_lote . ".xlsx";

	    // Ejecutar el stored
	    mysql_query("CALL $base_padron.NOV_mostrar_lote($id_lote, 1000000, 0)") or die(mysql_error());

	    // Directamente consultar la tabla temporal
	    $result = mysql_query("SELECT * FROM $base_padron.tmp_afiliados_novedades_mostrar") or die(mysql_error());

	    $objPHPExcel = new PHPExcel();
	    $sheet = $objPHPExcel->getActiveSheet();
	    $sheet->setTitle("Afiliados");

	    $headers = ['#', 'Gerenciadora', 'CUIL Titular', 'Parentesco', 'CUIL', 'DNI', 'AyN', 'Sexo', 'Edad', 'Fecha Nac.', 'Incapacidad', 'Tipo Benef.', 'Movimiento', 'Fecha Movimiento', 'Errores' , 'Rechazos'];

	    $col = 0;
	    foreach ($headers as $h) {
	        $sheet->setCellValueByColumnAndRow($col++, 1, $h);
	    }

	    $rowIndex = 2;
	    $i = 1;
	    while ($row = mysql_fetch_assoc($result)) {
	        $col = 0;
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $i++);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['desreguladora']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil_titular']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['parentesco']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['nd']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['ayn']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['sexo']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['edad']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['fn']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['incapacidad']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tbt']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tipo_mov']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['fecha_movimiento']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['errores']);
	        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['rechazos']);
	        $rowIndex++;
	    }

	    // Encabezados para la descarga
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header("Content-Disposition: attachment;filename=\"$filename\"");
	    header('Cache-Control: max-age=0');

	    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $writer->save('php://output');
	    exit;

	    break;


	case 'exportar_afiliados_excel1':

	    require_once '../../Lib/PHPExcel/Classes/PHPExcel.php';
	    #header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    #header('Content-Disposition: attachment;filename="afiliados_exportados_lote_'.$id_lote.'.xlsx"');
	    #header('Cache-Control: max-age=0');

	    #$id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;

	    if($id_lote){
	    	// Generamos la tabla temporal con todos los datos
		    $sql_sp = "CALL $base_padron.NOV_mostrar_lote($id_lote, 1000000, 0)";
		    mysql_query($sql_sp) or die(mysql_error());


		    // Consulta para obtener todos los datos cargados en la tabla temporal
		    $sql = "SELECT * FROM $base_padron.tmp_afiliados_novedades_mostrar";
		    $result = mysql_query($sql) or die(mysql_error());
		    #echo mysql_num_rows($result); exit();

		    $objPHPExcel = new PHPExcel();
		    $objPHPExcel->setActiveSheetIndex(0);
		    $sheet = $objPHPExcel->getActiveSheet();
		    $sheet->setTitle('Afiliados');

		    // Encabezados
		    $headers = ['#', 'Gerenciadora', 'CUIL Titular', 'Parentesco', 'CUIL', 'DNI', 'AyN', 'Sexo', 'Edad', 'Fecha Nac.', 'Incapacidad', 'Tipo Benef.', 'Movimiento', 'Errores'];
		    $col = 0;
		    foreach ($headers as $header) {
		        $sheet->setCellValueByColumnAndRow($col++, 1, $header);
		    }

		    // Datos
		    $rowIndex = 2;
		    $i = 1;
		    while ($row = mysql_fetch_assoc($result)) {
		        $col = 0;
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $i++);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['desreguladora']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil_titular']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['parentesco']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['cuil']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['nd']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['ayn']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['sexo']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['edad']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['fn']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['incapacidad']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tbt']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['tipo_mov']);
		        $sheet->setCellValueByColumnAndRow($col++, $rowIndex, $row['errores']);
		        $rowIndex++;
		    }

		    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		    $writer->save('php://output');
		    exit;
	    }

	    

	    break;
	
	case 'lst_afiliados_presentacion_x_gerenciadora':
		$sql="SELECT d.convenio as desreguladora,count(*) as contador

			FROM $base_historicos.novedades_exportables t
			JOIN $base_padron.persona p ON t.id_persona=p.id 
			JOIN $base_padron.afiliados a ON p.id=a.id_persona 
			JOIN $base_padron.desreguladoras d ON a.id_desreguladora=d.id 
			JOIN $base_padron.parentesco pa ON a.id_parentesco=pa.id 
			LEFT JOIN $base_padron.afiliados a2 ON a.id_titular=a2.id 
			LEFT JOIN $base_padron.persona p2 ON a2.id_persona=p2.id 
			LEFT JOIN $base_padron.`tipo_beneficiario_titular` tb ON tb.id=COALESCE(a2.`id_tipo_aporte`,a.`id_tipo_aporte`)
			WHERE t.id_lote = $id_lote
			GROUP BY 1 ORDER BY 2 DESC";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = $row;
		}
		echo json_encode($json);
		break;
	
	case 'quitar_fct_presentacion':
		// code...
		if($id_expo){
			$delete = "DELETE FROM $base_historicos.novedades_exportables WHERE id=$id_expo AND id_lote=$id_lote";

			mysql_query($delete) or die(mysql_error().$delete);

			echo "ok";			
		}

		break;


	case 'procesar_errores':

			//var_dump($_FILES['file_precarga']); exit();
			//$periodo = "2022-08";
			$id_presentacion=$_POST['id_presentacion'];
			//echo $id_presentacion;exit();
			if(isset($_FILES['file_errores']) && $_FILES['file_errores']['error']==0) {
				
				//$nombre_archivo = $_FILES['file_precarga']['tmp_name'];
				$nombre_archivo = $nombre."_".$periodo.".".$extension;
				
				$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/novedades/aceptados/";

				$path = $archivo_send.$nombre_archivo; 


				$copiado = move_uploaded_file($_FILES['file_errores']['tmp_name'], $path);
				if($copiado==false){
								
					echo "Error moviendo el archivo $path";
					
				}
				else{
					
					//echo "Leyo el archivo"; exit();

					$gestor = fopen("$path","r");
					$konta=0 ;
					
					while ($input = fgets($gestor, 350)) {
						
						$input= ereg_replace( "'", " ", $input );
						//echo $input;exit();
						
						if($input==""){
							
						}
						else{
							$konta++;
							insertar_errores($input,$periodo);
							//$sq=insertar($input);
							//$json[]=array('SQL' =>$sq)
						}
						
					}

					$id_lote = graba_lote_errores($konta,$periodo,$id_usuario);

					mysql_query("UPDATE ".N_BASE_HISTORICOS.".`novedades_sss_errores` n
									JOIN ".N_BASE_PADRON.".persona p ON n.nd=p.nd 
									SET n.id_persona=p.id
									WHERE id_lote=$id_lote") or die(mysql_error());
				}
			}

			mysql_query("UPDATE ".N_BASE_HISTORICOS.".`novedades_exportables` ne
							SET ne.`cod_error_`=NULL
							WHERE ne.id_lote='$id_presentacion'");

			$query = "UPDATE ".N_BASE_HISTORICOS.".`novedades_exportables` ne
						JOIN ".N_BASE_HISTORICOS.".`novedades_sss_errores` nr ON ne.id_persona=nr.id_persona 
																				AND nr.id_lote=$id_lote
																				AND ne.`tipo_mov`=nr.`cod_mov`
						SET ne.`cod_error_`=CONCAT(TRIM(nr.cod_error),'-',TRIM(nr.cod_error2))
						WHERE ne.id_lote=$id_presentacion";
			mysql_query($query) or die(mysql_error().$query);

			echo $id_lote;

		break;

	case 'procesar_aceptados':
		if(isset($_FILES['file_aceptados']) && $_FILES['file_aceptados']['error']==0) {
			

			//$nombre_archivo = $_FILES['file_precarga']['tmp_name'];
			$nombre_archivo = $nombre."_".$periodo.".".$extension;
			
			$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/novedades/aceptados/";

			$path = $archivo_send.$nombre_archivo; 


			$copiado = move_uploaded_file($_FILES['file_aceptados']['tmp_name'], $path);
			if($copiado==false){
							
				echo "Error moviendo el archivo $path";
				
			}
			else{
				
				//echo "Leyo el archivo"; exit();

				$gestor = fopen("$path","r");
				$konta=0 ;
				
				while ($input = fgets($gestor, 350)) {
					
					$input= ereg_replace( "'", " ", $input );
					//echo $input;exit();
					
					if($input==""){
						
					}
					else{
						$konta++;
						insertar_aceptados($input,$periodo);
						//$sq=insertar($input);
						//$json[]=array('SQL' =>$sq)
					}
					
				}

				$id_lote = graba_lote_aceptados($konta,$periodo,$id_usuario);

				
			}
		}

		echo $id_lote;
		break;
	case 'procesar_aceptados_dev':
		if(isset($_FILES['file_aceptados']) && $_FILES['file_aceptados']['error']==0) {
			

			//$nombre_archivo = $_FILES['file_precarga']['tmp_name'];
			$nombre_archivo = $nombre."_".$periodo.".".$extension;
			
			$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/novedades/aceptados/";

			$path = $archivo_send.$nombre_archivo; 


			$copiado = move_uploaded_file($_FILES['file_aceptados']['tmp_name'], $path);
			if($copiado==false){
				echo "Error moviendo el archivo $path";
			}
			else{
				$id_lote = graba_lote_aceptados_dev($konta,$periodo,$id_usuario);
				//echo "Leyo el archivo"; exit();

				$scriptDirectory = __DIR__;
				$csvFileName = 'temp_file_data.csv';
				$absolutePath = $scriptDirectory . DIRECTORY_SEPARATOR . $csvFileName;
	      if (file_exists($absolutePath)) {
			    unlink($absolutePath);
				}
	      $tempFile = fopen($absolutePath, 'w');

				$gestor = fopen("$path","r");
				$konta=0 ;
				
				while ($input = fgets($gestor, 350)) {
					
					$input= ereg_replace( "'", " ", $input );
					
					if($input!=""){
						$konta++;
						insertar_aceptados_dev($input,$periodo,$id_lote,$tempFile);
					}
				}
		  	fclose($tempFile);  
				$output = shell_exec("sh load_aceptados.sh " . N_BASE_HISTORICOS);
		    unlink($absolutePath); // Remove temporary file
			}
		}
		echo $id_lote;
		break;
	case 'procesar_rechazados':

			//var_dump($_FILES['file_precarga']); exit();
			//$periodo = "2022-08";

			if(isset($_FILES['file_rechazados']) && $_FILES['file_rechazados']['error']==0) {
				
				//$nombre_archivo = $_FILES['file_precarga']['tmp_name'];
				$nombre_archivo = $nombre."_".$periodo.".".$extension;
				
				$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/novedades/aceptados/";

				$path = $archivo_send.$nombre_archivo; 


				$copiado = move_uploaded_file($_FILES['file_rechazados']['tmp_name'], $path);
				if($copiado==false){
								
					echo "Error moviendo el archivo $path";
					
				}
				else{
					
					//echo "Leyo el archivo"; exit();

					$gestor = fopen("$path","r");
					$konta=0 ;
					
					while ($input = fgets($gestor, 350)) {
						
						$input= ereg_replace( "'", " ", $input );
						//echo $input;exit();
						
						if($input==""){
							
						}
						else{
							$konta++;
							insertar_rechazados($input,$periodo);
							//$sq=insertar($input);
							//$json[]=array('SQL' =>$sq)
						}
						
					}

					$id_lote = graba_lote_rechazados($konta,$periodo,$id_usuario);

					mysql_query("UPDATE ".N_BASE_HISTORICOS.".`novedades_sss_rechazados` n
									JOIN ".N_BASE_PADRON.".persona p ON n.nd=p.nd 
									SET n.id_persona=p.id
									WHERE id_lote=$id_lote") or die(mysql_error());

					mysql_query("CALL ".N_BASE_PADRON.".NOV_agrega_rechazos_periodo_actual();");
					
				}
			}

			echo $id_lote;

		break;
	case 'lst_novedades_presentaciones':
		mysql_query("CALL $base_padron.`novedades_envio_presentaciones`()") or die(mysql_error()."ERROR stored");

		$sql = "SELECT * FROM $base_padron.lst_novedades_presentaciones ";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_errores_presentacion':

		$sql = " SELECT * FROM $base_historicos.`lotes` WHERE id=$id_presentacion ";
		$rs = mysql_query($sql) or die(mysql_error());
		
		$periodo_presentacion = mysql_fetch_object($rs)->descripcion;

		$sql = "SELECT l.id,l.fechador, cant_registros FROM $base_historicos.lotes l WHERE proceso='novedades_errores' AND descripcion='$periodo_presentacion' ORDER BY id DESC";
		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_errores_por_codigo':
		$sql="SELECT TRIM(cod_error) as codigo,COUNT(*) as cantidad FROM $base_historicos.`novedades_sss_errores` WHERE id_lote=$id_lote GROUP BY 1 ORDER BY 2 DESC";
		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_errores_por_gerenciadora':
		$sql="SELECT d.`convenio_real` AS codigo,COUNT(*) AS cantidad
			FROM $base_historicos.`novedades_sss_errores` nsr
			JOIN $base_padron.`persona` p ON p.`nd`=nsr.`nd`
			JOIN $base_padron.`afiliados` a ON a.`id_persona`=p.`id`
			JOIN $base_padron.`desreguladoras` d ON d.`id`=a.`id_desreguladora` 
			WHERE id_lote=$id_lote GROUP BY 1 ORDER BY 2 DESC";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'editar_fecha_vencimiento':
		$sql="UPDATE $base_historicos.lotes SET obrasocial='$fecha_vencimiento' WHERE id=$id_lote";
		mysql_query($sql) or die(mysql_error());
		echo "ok";
		break;

	case 'lst_cronologia_afiliado':
		// code...
			mysql_query("CALL $base_padron.`novedades_cronologia`($id_persona)") or die(mysql_error()."ERROR stored");

			$sql = "SELECT DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fechador_mostrar,id_usuario,MID(evento,1,250) AS evento
						FROM $base_padron.tmp_cronologia_novedades 
						ORDER BY fechador " ;

			$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
					
			$json = array();
			while ($row = mysql_fetch_assoc($result)) {

								
			    $json[] = array(
			        		'fechador' => $row['fechador_mostrar'],
			        		'id_usuario' => $row['id_usuario'],			        					        		
			        		'movimiento' => $row['evento']
			        		       
			      );
			}
			
			echo json_encode($json);


		break;
	case 'listar_comparacion_padrones':
				$sql = "SELECT id,descripcion,usuario,fechador FROM $base_historicos.`lotes` WHERE proceso='comparacion_padrones'" ;

				$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
						
				$json = array();
				while ($row = mysql_fetch_assoc($result)) {
					$json[] = $row;
				}
				
				echo json_encode($json);
		break;
	case 'listar_ctrlPadronCompleto':
				$sql = "SELECT le.id,le.fecha_parametro,le.fechador,le.fechador_fin,u.usuario
					from $base_padron.log_eventos le
					join $base_usuarios.users u on u.id=le.id_usuario
					where evento='ctrlPadronCompleto' 
					order by le.id desc
				";

				$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
						
				$json = array();
				while ($row = mysql_fetch_assoc($result)) {
					$json[] = $row;
				}
				
				echo json_encode($json);
		break;
	case 'listar_novedadesRechazadas':
				$sql = "SELECT l.id,l.descripcion as fecha,l.fechador,u.usuario
					from $base_historicos.lotes l
					join $base_usuarios.users u on u.id=l.id_usuario
					where l.proceso='novedades_rechazados' 
					order by l.descripcion desc
					LIMIT 6
				";

				$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
						
				$json = array();
				while ($row = mysql_fetch_assoc($result)) {
					$json[] = $row;
				}
				
				echo json_encode($json);
		break;
	case 'traer_comparacion_padron':
		$request = $_REQUEST;
		//Dependiendo el renderizado de las columnas en el listado en pantalla. Se debe mapear de la misma manera el siguiente array para el correcto comportamiento del ordenado de filas asc/desc
		$columns = array(
			0 => 'cuil',
			1 => '',
			2 => 'ayn',
			3 => 'desreguladora'
		);
	
		$sql="SELECT cuil,a.id_titular,a.id as id_afiliado,ayn,desreguladora
			FROM $base_historicos.novedades_exportables_comparacion nec
			JOIN $base_padron.`afiliados` a ON a.id_persona=nec.id_persona
			WHERE 1=1
			AND id_lote=$id_lote
		";

		#echo $sql;exit();
		$query = mysql_query($sql);
		$totalData = mysql_num_rows($query);
		
		if (!empty($request['search']['value'])) {
		    $sql .= " AND (ayn LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR cuil LIKE '%" . $request['search']['value'] . "%' )";
		}

		if($request['order'][0]['column'] != 0){
			$sql .= " ORDER BY " . $columns[$request['order'][0]['column']] . " " . $request['order'][0]['dir'];
		}

		$sql .= " LIMIT " . $request['start'] . " ," . $request['length'] . " ";

		#echo $sql;exit();
		$query = mysql_query($sql);
		#$totalFilter = mysql_num_rows($query);
		$totalFilter  = $totalData;
		
		while ($row = mysql_fetch_object($query)) {
		    $data[] = $row;
		}
		
		$json_data = array(
		    "draw" => intval($request['draw']),
		    "recordsTotal" => intval($totalData),
		    "recordsFiltered" => intval($totalFilter),
		    "data" => $data
		);

		echo json_encode($json_data);
		break;
	case 'traer_novedades_rechazadas':
		$request = $_REQUEST;
		//Dependiendo el renderizado de las columnas en el listado en pantalla. Se debe mapear de la misma manera el siguiente array para el correcto comportamiento del ordenado de filas asc/desc
		$columns = array(
			0 => 'cuil',
			1 => '',
			2 => 'ayn',
			3 => 'parentesco',
			4 => 'cod_mov',
			5 => 'fec_mov',
			6 => 'rechazo',
		);
		
		$sql="SELECT $base_historicos.`get_id_presentacion_novedades_activa`() as id_presentacion";

		$id_presentacion = mysql_fetch_object(mysql_query($sql))->id_presentacion;

		$sql="
			SELECT a.id_titular,a.id as id_afiliado,n.`cuil`,n.`ayn`,n.`parentesco`,n.`cod_mov`,n.`fec_alta` AS fec_mov,CONCAT(TRIM(n.`cod_error`),'-',TRIM(n.`cod_error2`)) AS rechazo
			FROM $base_historicos.novedades_sss_rechazados n
			JOIN $base_padron.afiliados a ON a.id_persona=n.id_persona
			WHERE n.id_lote=$id_lote
				AND n.id_persona NOT IN ( SELECT id_persona FROM $base_historicos.`novedades_exportables` WHERE id_lote=$id_presentacion)
				AND TRIM(CONCAT(n.cod_error,'-',n.cod_error2))!='92-100'
		";

		#echo $sql;exit();
		$query = mysql_query($sql);
		$totalData = mysql_num_rows($query);
		
		if (!empty($request['search']['value'])) {
		    $sql .= " AND (ayn LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR cuil LIKE '%" . $request['search']['value'] . "%' )";
		}

		if($request['order'][0]['column'] != 0){
			$sql .= " ORDER BY " . $columns[$request['order'][0]['column']] . " " . $request['order'][0]['dir'];
		}

		$sql .= " LIMIT " . $request['start'] . " ," . $request['length'] . " ";

		#echo $sql;exit();
		$query = mysql_query($sql);
		#$totalFilter = mysql_num_rows($query);
		$totalFilter  = $totalData;
		
		while ($row = mysql_fetch_object($query)) {
		    $data[] = $row;
		}
		
		$json_data = array(
		    "draw" => intval($request['draw']),
		    "recordsTotal" => intval($totalData),
		    "recordsFiltered" => intval($totalFilter),
		    "data" => $data
		);

		echo json_encode($json_data);
		break;

	case 'listado_previo_nov_y_padronsss':
		// code...

		$valida = "SELECT *
					    FROM $base_padron.tmp_afiliados_nov_padronsss_insertar 
					    WHERE id_padron_sss=$id_lote "; #echo $valida; exit();
		$rs = mysql_query($valida);

		if(mysql_num_rows($rs)==0 ){

			#echo "Entro"; exit();
			$query = "CALL $base_padron.Padron_sss_comparativo_lst_control($id_lote)";
			mysql_query($query) or die(mysql_error().$query);

			
		}
		else{
			#echo "El archivo ya fue generado"; exit();
		}

		$query = "SELECT * FROM $base_padron.tmp_afiliados_nov_padronsss_insertar WHERE id_padron_sss=$id_lote ";
		#$rs = mysql_query($query);

		//generar_json_automatico($query);
		
		$result = mysql_query($query) or die(mysql_error().$query);
		$json = array();

		while($row=mysql_fetch_assoc($result)){
			$json[] = $row;
		}

		$response = [
		    "draw" => intval($_GET['draw']), // Necesario para DataTables
		    "recordsTotal" => $totalRegistros, // Total sin filtrar
		    "recordsFiltered" => $totalFiltrados, // Total después de aplicar filtros
		    "data" => $json // Array de registros
		];

		echo json_encode($response);

		break;

	case 'actualizar_exportar':
		// code...
		$query = "UPDATE $base_padron.tmp_afiliados_nov_padronsss_insertar SET exportar='$exportar' WHERE id=$id " ;
		mysql_query($query);


		break;

}

function getNames($rs){
	for ($i = 0; $i < mysql_num_fields($rs); $i++){
		$names = $names.mysql_field_name($rs,$i)."|";	
	}
	$names=$names."\n";
	return $names;
}
function getFields($rs,$fx){
	$content="";
	while($row = mysql_fetch_row($rs)){  
		$reglon ="";
		/*
		echo $row[7];
		echo " -- ";
		echo 30-strlen(trim($row[7]));
		echo " -- ";
		echo strlen(trim($row[7]));
		echo "<br>";
		*/
	   	for($j=0; $j<mysql_num_fields($rs);$j++){  

	   		

	        if(!isset($row[$j])){
	            $value = NULL;  
	        }
	        elseif ($row[$j] != ""){
	            $value = strip_tags($row[$j]);  
	        }
	        else{
	            $value = "";  
			}

			switch ($j) {
	   			case '7':
	   				//$value = str_pad($value, 29);
	   				//$value = substr("                                 ",0,30-strlen(trim($value)));
	   			break;//ayn
	   			case '12':
	   				$value = str_pad($value,20);
	   				//$value = substr("                                ",0,20-strlen(trim($value)));
	   			break;//calle
	   			case '13':
	   				$value = str_pad($value,5);
	   				//$value = substr("                                ",0,5-strlen(trim($value));
	   			break;//nro
	   			case '14':
	   				//$value = str_pad($value,3);
	   				//$value = substr("                                ",0,3-strlen(trim($value)));
	   			break;//piso
	   			case '15':
	   				$value = str_pad($value,3);
	   				//$value = substr("                                ",0,3-strlen(trim($value)));$value = str_pad($value,3);
	   			break;//dto
	   			case '16':
	   				//$value = str_pad($value,20);
	   				//$value = substr("                                ",0,21-strlen(trim($value)));
	   			break;//localidad
	   			case '17':
	   				$value = str_pad($value,8);
	   				//$value = substr("                                ",0,8-strlen(trim($value)));
	   			break;//cp
	   			case '20':
	   				$value = str_pad($value,19);
	   				//$value = substr("                                ",0,19-strlen(trim($value)));
	   			break;//telefono
	   			//default:$value = "";break;//default
	   		}
	        $reglon=$reglon.$value."|";


	    }  
	    $reglon = rtrim($reglon,'| ');
	    $reglon=$reglon."\n";
	    fwrite($fx,$reglon);
		
	} 
	return $content;
}
function createArchive($rs,$dir){
	$n = mysql_num_rows($rs);
	
	if($n == 0){
		//echo "No hay datos para: ".$dir."<br>";
		return false;
	}else{
		//echo $dir."<br>";
		$fx=fopen($dir,"w");
		//$reglon = getNames($rs);
		//fwrite($fx,$reglon);
		$fx = getFields($rs,$fx);
		fclose($fx);
		return true;
	}	
}

function generar_json_automatico($query){


	$result = mysql_query($query) or die(mysql_error().$query);
	$json = array();

	while($row=mysql_fetch_assoc($result)){
		$json[] = $row;
	}

	echo json_encode($json);

}

//Importadores de archivos 

function graba_lote_aceptados($konta,$periodo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$periodo','$konta','novedades_aceptados',$id_usuario,'novedades_aceptados_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();

	$update="UPDATE ".N_BASE_HISTORICOS.".novedades_sss_aceptados SET id_lote='$id_lote' WHERE periodo='$periodo' AND id_lote IS NULL";
	mysql_query($update) or die(mysql_error().$update);

	return $id_lote;
}
function graba_lote_aceptados_dev($konta,$periodo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$periodo','$konta','novedades_aceptados',$id_usuario,'novedades_aceptados_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
	return $id_lote;
}
function insertar_aceptados($input,$periodo){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$td,$nd,$ayn,$sexo,$est_civil,$fn,$nacionalidad,$calle,$numero,$piso,$dto,$localidad,$cp,$provincia,$tipo_dom,$telefono,$revista,$incapacidad,$tbt,$f_alta,$f_cierre_presentacion,$cod_mov,$cod_error,$cod_error2,$cod_error3)=explode("|",$input);
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".novedades_sss_aceptados (periodo,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,fec_alta,fec_cierre,cod_mov,cod_error,cod_error2,cod_error3)
	VALUES	('$periodo','$rnos','$cuit','$cuil_titular','$parentesco','$cuil','$td','$nd','$ayn','$sexo','$est_civil','$fn','$nacionalidad','$calle','$numero','$piso','$dto','$localidad','$cp','$provincia','$tipo_dom','$telefono','$revista','$incapacidad','$tbt','$f_alta','$f_cierre_presentacion','$cod_mov',
		'$cod_error','$cod_error2','$cod_error3')
	";
	mysql_query($sql) or die(mysql_error().$sql);
	
	
	
	return $sql;
	
}
function insertar_aceptados_dev($input,$periodo,$id_lote,$tempFile){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$td,$nd,$ayn,$sexo,$est_civil,$fn,$nacionalidad,$calle,$numero,$piso,$dto,$localidad,$cp,$provincia,$tipo_dom,$telefono,$revista,$incapacidad,$tbt,$f_alta,$f_cierre_presentacion,$cod_mov,$cod_error,$cod_error2,$cod_error3)=explode("|",$input);	

  $dataRow = implode(';', [
      $id_lote,$periodo,$rnos,$cuit,
      $cuil_titular,$parentesco,$cuil,$td,
      $nd,$ayn,$sexo,$est_civil,
      $fn,$nacionalidad,$calle,$numero,
      $piso,$dto,$localidad,$cp,
      $provincia,$tipo_dom,$telefono,$revista,
      $incapacidad,$tbt,$f_alta,$f_cierre_presentacion,
      $cod_mov,$cod_error,$cod_error2,$cod_error3
  ]);
  fwrite($tempFile, $dataRow . chr(13));
}
function graba_lote_rechazados($konta,$periodo,$id_usuario){

	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$periodo','$konta','novedades_rechazados',$id_usuario,'novedades_rechazados_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();

	$update="UPDATE ".N_BASE_HISTORICOS.".novedades_sss_rechazados SET id_lote='$id_lote' WHERE periodo='$periodo' AND id_lote IS NULL";
	mysql_query($update) or die(mysql_error().$update);

	mysql_query("CALL ".N_BASE_PADRON.".NOV_agrega_rechazos_periodo_actual() ") or die(mysql_error()."ERROR importando los rechazados");

	return $id_lote;
}

function insertar_rechazados($input,$periodo){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 

	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$td,$nd,$ayn,$sexo,$est_civil,$fn,$nacionalidad,$calle,$numero,$piso,$dto,$localidad,$cp,$provincia,$tipo_dom,$telefono,$revista,$incapacidad,$tbt,$f_alta,$f_cierre_presentacion,$cod_mov,$cod_error,$cod_error2,$cod_error3)=explode("|",$input);
	
	
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".novedades_sss_rechazados (periodo,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,fec_alta,fec_cierre,cod_mov,cod_error,cod_error2,cod_error3)
	VALUES	('$periodo','$rnos','$cuit','$cuil_titular','$parentesco','$cuil','$td','$nd','$ayn','$sexo','$est_civil','$fn','$nacionalidad','$calle','$numero','$piso','$dto','$localidad','$cp','$provincia','$tipo_dom','$telefono','$revista','$incapacidad','$tbt','$f_alta','$f_cierre_presentacion','$cod_mov',
		'$cod_error','$cod_error2','$cod_error3')
	";
	mysql_query($sql) or die(mysql_error().$sql);
	
	
	
	return $sql;
	
}

function graba_lote_errores($konta,$periodo,$id_usuario){

	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$periodo','$konta','novedades_errores',$id_usuario,'novedades_errores_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();

	$update="UPDATE ".N_BASE_HISTORICOS.".novedades_sss_errores SET id_lote='$id_lote' WHERE periodo='$periodo' AND id_lote IS NULL";
	mysql_query($update) or die(mysql_error().$update);

	return $id_lote;
}

function insertar_errores($input,$periodo){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 

	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$td,$nd,$ayn,$sexo,$est_civil,$fn,$nacionalidad,$calle,$numero,$piso,$dto,$localidad,$cp,$provincia,$tipo_dom,$telefono,$revista,$incapacidad,$tbt,$f_alta,$f_cierre_presentacion,$cod_mov,$cod_error,$cod_error2,$cod_error3)=explode("|",$input);
	
	
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".novedades_sss_errores (periodo,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,fec_alta,fec_cierre,cod_mov,cod_error,cod_error2,cod_error3)
	VALUES	('$periodo','$rnos','$cuit','$cuil_titular','$parentesco','$cuil','$td','$nd','$ayn','$sexo','$est_civil','$fn','$nacionalidad','$calle','$numero','$piso','$dto','$localidad','$cp','$provincia','$tipo_dom','$telefono','$revista','$incapacidad','$tbt','$f_alta','$f_cierre_presentacion','$cod_mov',
		'$cod_error','$cod_error2','$cod_error3')
	";
	mysql_query($sql) or die(mysql_error().$sql);
	
	
	
	return $sql;
	
}


?>