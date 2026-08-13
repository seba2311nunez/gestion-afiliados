<?php
ini_set('display_errors', 0); 
ini_set('log_errors',1); 
error_reporting(E_ALL); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include('../../Config/Conectar.inc');
include_once('../../Config/ftp_sss.inc.php');
include_once(__DIR__.'/lib/sss_workflow.php');

if(!$_SESSION['id_user']){
	sss_json(array('status'=>'error','codigo'=>'SESSION_EXPIRED','mensaje'=>'Su sesion caduco. Vuelva a iniciar sesion.'), 401); exit();
}
else{
	$id_usuario = $_SESSION['id_user'];	
}

sss_crear_estructura();

function sss_python_ejecutable(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3';
}

function sss_error_proceso_ftps($salida, $configPath){
	$detalle = trim(strip_tags((string)$salida));
	if($configPath) $detalle = str_replace($configPath, '[configuracion privada]', $detalle);
	$detalle = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/', ' ', $detalle);
	if($detalle === '') return 'El proceso FTPS no produjo salida. Verifique que shell_exec y Python 3 esten habilitados.';
	if(strlen($detalle) > 400) $detalle = substr($detalle, 0, 400).'...';
	return 'El proceso FTPS no devolvio JSON valido: '.$detalle;
}

function sss_ftp_ultimo_error(){
	$error = error_get_last();
	return $error && isset($error['message']) ? preg_replace('/\s+/', ' ', $error['message']) : '';
}

function sss_ftp_subir_php($archivoLocal, $periodo){
	if(!function_exists('ftp_ssl_connect')) throw new Exception('El servidor PHP no tiene soporte FTPS.');
	$credenciales = ftp_sss_credenciales(INST_NAME, INST_RNOS);
	$host = trim((string)$credenciales['host']);
	$puerto = isset($credenciales['puerto']) ? intval($credenciales['puerto']) : 21;
	$timeout = isset($credenciales['timeout']) ? intval($credenciales['timeout']) : 20;
	if($puerto <= 0) $puerto = 21;
	if($timeout <= 0) $timeout = 20;

	$ftp = @ftp_ssl_connect($host, $puerto, $timeout);
	if(!$ftp) throw new Exception('No se pudo establecer la conexion FTPS con la SSS. '.sss_ftp_ultimo_error());
	try{
		if(!@ftp_login($ftp, $credenciales['usuario'], $credenciales['clave'])){
			throw new Exception('La SSS rechazo el usuario o la clave FTPS. '.sss_ftp_ultimo_error());
		}
		if(!@ftp_pasv($ftp, true)) throw new Exception('No se pudo activar el modo pasivo FTPS. '.sss_ftp_ultimo_error());
		$periodoCompacto = str_replace('-', '', trim((string)$periodo));
		$carpetaRemota = '/'.$periodoCompacto;
		if(!@ftp_chdir($ftp, $carpetaRemota)){
			throw new Exception('No existe o no es accesible la carpeta remota '.$carpetaRemota.'. '.sss_ftp_ultimo_error());
		}
		$archivo = @fopen($archivoLocal, 'rb');
		if(!$archivo) throw new Exception('No se pudo abrir el archivo local para enviarlo.');
		$subido = @ftp_fput($ftp, basename($archivoLocal), $archivo, FTP_BINARY);
		fclose($archivo);
		if(!$subido) throw new Exception('La SSS rechazo la escritura del archivo en '.$carpetaRemota.'. '.sss_ftp_ultimo_error());
		return array('status'=>'ok','ruta_remota'=>$carpetaRemota.'/'.basename($archivoLocal));
	} finally {
		@ftp_close($ftp);
	}
}


switch ($parametro){

	case 'ftp_sss_capacidades':
		sss_json(ftp_sss_estado_configuracion(INST_NAME, INST_RNOS));
		break;

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
		$modoTabulator = isset($_GET['modo']) && $_GET['modo'] === 'tabulator';
		$start   = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$length  = isset($_GET['length']) ? intval($_GET['length']) : 50;
		$search  = isset($_GET['search']['value']) ? mysql_real_escape_string($_GET['search']['value']) : '';
		$filtros = isset($_GET['filtros']) && is_array($_GET['filtros']) ? $_GET['filtros'] : array();

		$sinLimite = $length === -1;
		$limit  = $length > 0 ? $length : 10;
		$offset = $start >= 0 ? $start : 0;
		$clausulaLimit = $sinLimite ? '' : " LIMIT $offset, $limit";

		// Todas las instituciones conservan la firma historica de un parametro.
		// La paginacion se aplica una sola vez sobre la tabla temporal resultante.
		$from = " FROM $base_historicos.novedades_exportables t
			LEFT JOIN $base_padron.persona p ON p.id=t.id_persona
			LEFT JOIN $base_padron.afiliados a ON a.id_persona=p.id
			LEFT JOIN $base_padron.desreguladoras d ON d.id=a.id_desreguladora
			LEFT JOIN $base_padron.parentesco pa ON pa.id=a.id_parentesco
			LEFT JOIN $base_padron.afiliados at ON at.id=IF(a.id_titular IS NULL OR a.id_titular=0,a.id,a.id_titular)
			LEFT JOIN $base_padron.persona pt ON pt.id=at.id_persona
			LEFT JOIN $base_padron.tipo_beneficiario_titular tb ON tb.id=at.id_tipo_aporte";

		// Armar el WHERE si hay búsqueda
		$where = " WHERE t.id_lote=$id_lote";
		$filtrosEspeciales = array(
			'__errores_ftp__' => "TRIM(COALESCE(t.cod_error_,''))<>''",
			'__rechazados__' => "TRIM(COALESCE(t.cod_rechazados,''))<>''",
			'cat:alta_titular' => "UPPER(TRIM(t.tipo_mov))='A' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'cat:baja_titular' => "UPPER(TRIM(t.tipo_mov))='B' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'cat:modificacion_titular' => "UPPER(TRIM(t.tipo_mov))='M' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'cat:alta_familiar' => "UPPER(TRIM(t.tipo_mov))='A' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id",
			'cat:baja_familiar' => "UPPER(TRIM(t.tipo_mov))='B' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id",
			'cat:modificacion_familiar' => "UPPER(TRIM(t.tipo_mov))='M' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id"
		);
		if (isset($filtrosEspeciales[$search])) {
			$where .= " AND (".$filtrosEspeciales[$search].")";
		} elseif ($search != '') {
			$where .= " AND (p.nd LIKE '%$search%' OR p.cuil LIKE '%$search%' OR CONCAT(p.apellido,' ',p.nombre) LIKE '%$search%' OR tb.sigla LIKE '%$search%' OR t.cod_error_ LIKE '%$search%' OR t.cod_rechazados LIKE '%$search%' OR d.convenio LIKE '%$search%' OR d.convenio_real LIKE '%$search%')";
		}

		$categoriasMovimiento = array(
			'alta_titular' => "UPPER(TRIM(t.tipo_mov))='A' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'baja_titular' => "UPPER(TRIM(t.tipo_mov))='B' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'modificacion_titular' => "UPPER(TRIM(t.tipo_mov))='M' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)",
			'alta_familiar' => "UPPER(TRIM(t.tipo_mov))='A' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id",
			'baja_familiar' => "UPPER(TRIM(t.tipo_mov))='B' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id",
			'modificacion_familiar' => "UPPER(TRIM(t.tipo_mov))='M' AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id"
		);
		if(!empty($filtros['movimiento']) && isset($categoriasMovimiento[$filtros['movimiento']])) $where .= " AND (".$categoriasMovimiento[$filtros['movimiento']].")";
		if(isset($filtros['persona']) && $filtros['persona'] === 'titular') $where .= " AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id)";
		if(isset($filtros['persona']) && $filtros['persona'] === 'familiar') $where .= " AND a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id";
		if(isset($filtros['error']) && $filtros['error'] !== ''){
			$where .= $filtros['error'] === '__con_error__' ? " AND TRIM(COALESCE(t.cod_error_,''))<>''" : " AND t.cod_error_ LIKE '%".mysql_real_escape_string($filtros['error'])."%'";
		}
		if(isset($filtros['rechazo']) && $filtros['rechazo'] !== ''){
			$where .= $filtros['rechazo'] === '__con_rechazo__' ? " AND TRIM(COALESCE(t.cod_rechazados,''))<>''" : " AND t.cod_rechazados LIKE '%".mysql_real_escape_string($filtros['rechazo'])."%'";
		}
		if(isset($filtros['gerenciadora']) && $filtros['gerenciadora'] !== ''){
			$gerenciadora = mysql_real_escape_string($filtros['gerenciadora']);
			$where .= " AND (d.convenio='$gerenciadora' OR d.convenio_real='$gerenciadora')";
		}

		// Total general y total filtrado son valores distintos para DataTables.
		$total_general_rs = mysql_query("SELECT COUNT(*) AS total $from WHERE t.id_lote=$id_lote");
		$total_general_row = $total_general_rs ? mysql_fetch_assoc($total_general_rs) : array('total'=>0);
		$recordsTotal = intval($total_general_row['total']);

		$total_rs = mysql_query("SELECT COUNT(*) AS total $from $where");
		if(!$total_rs){
			sss_json(array('status'=>'error','mensaje'=>'No se pudo contar el listado del lote.','detalle'=>mysql_error(),'draw'=>intval($_GET['draw']),'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>array()),500);
			break;
		}
		$total_row = mysql_fetch_assoc($total_rs);
		$recordsFiltered = intval($total_row['total']);

		// Obtener los registros visibles
		$columnasOrden = array(
			0=>'t.id',1=>'t.id',2=>'d.convenio',3=>'cuil_titular',4=>'pa.parentesco',
			5=>'p.cuil',6=>'p.nd',7=>'ayn',8=>'p.sexo',9=>'edad',
			10=>'p.fn',11=>'a.incapacidad',12=>'tb.sigla',13=>'t.tipo_mov',
			14=>'t.fec_mov',15=>'t.cod_error_',16=>'t.cod_rechazados',
			17=>'t.cod_error_',18=>'t.cod_error_',19=>'t.id'
		);
		$indiceOrden = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
		$direccionOrden = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) === 'desc' ? 'DESC' : 'ASC';
		$campoOrden = isset($columnasOrden[$indiceOrden]) ? $columnasOrden[$indiceOrden] : 't.id';

		$sql = "SELECT t.id id_expo,p.id id_persona,a.id id_afiliado,a.id_titular,
			p.cuil,p.nd,CONCAT(p.apellido,' ',p.nombre) ayn,p.fn,p.sexo,
			TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) edad,pa.parentesco,a.incapacidad,
			d.convenio desreguladora,t.tipo_mov,t.fec_mov fecha_movimiento,
			t.cod_error_ errores,t.cod_rechazados rechazos,
			IF(a.id_titular IS NULL OR a.id_titular=0,p.cuil,pt.cuil) cuil_titular,
			 tb.sigla tbt $from $where ORDER BY $campoOrden $direccionOrden$clausulaLimit";
		$result = mysql_query($sql);
		if(!$result){
			sss_json(array('status'=>'error','mensaje'=>'No se pudieron leer los afiliados del lote.','detalle'=>mysql_error(),'draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>0,'data'=>array()),500);
			break;
		}

		$data = array();
		$index = $start + 1;
		while ($row = mysql_fetch_assoc($result)) {

	    $checkbox = "<div style='text-align:center;'>
	        <input type='checkbox' class='chkAfiliado' data-id_expo='{$row['id_expo']}'>
	    </div>";

	    $afiliadoData = htmlspecialchars($row['ayn'], ENT_QUOTES, 'UTF-8');
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
	                <a class='btnCronologia' href='#' data-id_persona='{$row['id_persona']}' data-afiliado='{$afiliadoData}'>
	                    Cronología
	                </a>
	            </li>
	        </ul>
	    </div>";

	    $detalleError = sss_detalle_codigos(trim($row['errores'].' '.$row['rechazos']));
	    $numeroFila = $index++;
	    $filaSalida = array(
	        $checkbox,
	        $numeroFila,
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
	        $row['rechazos'],
	        $detalleError['descripcion'],
	        $detalleError['accion'],
	        $acciones
	    );
	    if($modoTabulator){
	    	$filaSalida = array(
	    		'id_expo'=>intval($row['id_expo']),'id_persona'=>intval($row['id_persona']),
	    		'id_afiliado'=>intval($row['id_afiliado']),'id_titular'=>intval($row['id_titular']),
	    		'numero'=>$numeroFila,'gerenciadora'=>$row['desreguladora'],'cuil_titular'=>$row['cuil_titular'],
	    		'parentesco'=>$row['parentesco'],'cuil'=>$row['cuil'],'dni'=>$row['nd'],'ayn'=>$row['ayn'],
	    		'sexo'=>$row['sexo'],'edad'=>$row['edad'],'fecha_nacimiento'=>$row['fn'],
	    		'incapacidad'=>$row['incapacidad'],'tipo_beneficiario'=>$row['tbt'],
	    		'tipo_movimiento'=>$row['tipo_mov'],'fecha_movimiento'=>$row['fecha_movimiento'],
	    		'errores'=>$row['errores'],'rechazos'=>$row['rechazos'],
	    		'descripcion_sss'=>$detalleError['descripcion'],'accion_sugerida'=>$detalleError['accion'],
	    		'acciones'=>$acciones
	    	);
	    }
	    $data[] = $filaSalida;
	}


		sss_json(array(
			"draw" => intval($_GET['draw']),
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => $data
		));
		break;

	case 'resumen_tbt_y_errores':

		$id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;

		// Firma compatible en todas las bases; el resumen agrupa el lote completo.
		$fromResumen = " FROM $base_historicos.novedades_exportables t
			LEFT JOIN $base_padron.persona p ON p.id=t.id_persona
			LEFT JOIN $base_padron.afiliados a ON a.id_persona=p.id
			LEFT JOIN $base_padron.afiliados at ON at.id=IF(a.id_titular IS NULL OR a.id_titular=0,a.id,a.id_titular)
			LEFT JOIN $base_padron.tipo_beneficiario_titular tb ON tb.id=at.id_tipo_aporte
			WHERE t.id_lote=$id_lote";

		// Asegurar ejecución previa del stored que carga la tabla tmp_afiliados_novedades_mostrar
		$sql_tbt = "SELECT COALESCE(NULLIF(TRIM(tb.sigla),''),'Sin TBT') tbt, COUNT(*) AS cantidad $fromResumen GROUP BY tb.sigla";
		$rs_tbt = mysql_query($sql_tbt) or die(mysql_error());
		$resumen_tbt = array();
		while($row = mysql_fetch_assoc($rs_tbt)){
		    $clave = $row['tbt'] !== '' ? $row['tbt'] : 'Sin TBT';
		    $resumen_tbt[] = array(
		        'tbt' => $clave,
		        'cantidad' => intval($row['cantidad'])
		    );
		}

		$sql_err = "SELECT COALESCE(NULLIF(TRIM(t.cod_error_),''),'Sin errores') AS errores, COUNT(*) AS cantidad $fromResumen GROUP BY t.cod_error_";
		$rs_err = mysql_query($sql_err) or die(mysql_error());
		$resumen_errores = array();
		while($row = mysql_fetch_assoc($rs_err)){
			$detalleCodigo = sss_detalle_codigos($row['errores']);
		    $resumen_errores[] = array(
		        'errores' => $row['errores'],
		        'cantidad' => intval($row['cantidad']),
		        'descripcion' => $detalleCodigo['descripcion'],
		        'accion' => $detalleCodigo['accion']
		    );
		}

		$sql_rechazos = "SELECT COALESCE(NULLIF(TRIM(t.cod_rechazados),''),'Sin rechazos') AS rechazos, COUNT(*) AS cantidad $fromResumen GROUP BY t.cod_rechazados";
		$rs_rechazos = mysql_query($sql_rechazos) or die(mysql_error());
		$resumen_rechazos = array();
		while($row = mysql_fetch_assoc($rs_rechazos)){
			$detalleCodigo = sss_detalle_codigos($row['rechazos']);
		    $resumen_rechazos[] = array(
		        'rechazos' => $row['rechazos'],
		        'cantidad' => intval($row['cantidad']),
		        'descripcion' => $detalleCodigo['descripcion'],
		        'accion' => $detalleCodigo['accion']
		    );
		}

		$sql_mov = "SELECT CASE
			WHEN UPPER(TRIM(t.tipo_mov))='A' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id) THEN 'alta_titular'
			WHEN UPPER(TRIM(t.tipo_mov))='B' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id) THEN 'baja_titular'
			WHEN UPPER(TRIM(t.tipo_mov))='M' AND (a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id) THEN 'modificacion_titular'
			WHEN UPPER(TRIM(t.tipo_mov))='A' THEN 'alta_familiar'
			WHEN UPPER(TRIM(t.tipo_mov))='B' THEN 'baja_familiar'
			WHEN UPPER(TRIM(t.tipo_mov))='M' THEN 'modificacion_familiar' END categoria, COUNT(*) cantidad
			$fromResumen GROUP BY categoria";
		$rs_mov = mysql_query($sql_mov) or die(mysql_error());
		$etiquetas = array('alta_titular'=>'Altas de titulares','baja_titular'=>'Bajas de titulares','alta_familiar'=>'Altas de familiares','baja_familiar'=>'Bajas de familiares','modificacion_titular'=>'Modificaciones de titulares','modificacion_familiar'=>'Modificaciones de familiares');
		$cantidadesMov = array_fill_keys(array_keys($etiquetas),0);
		while($row=mysql_fetch_assoc($rs_mov)) if(isset($cantidadesMov[$row['categoria']])) $cantidadesMov[$row['categoria']] = intval($row['cantidad']);
		$resumen_movimientos = array();
		foreach($etiquetas as $categoria=>$descripcion) $resumen_movimientos[] = array('categoria'=>$categoria,'descripcion'=>$descripcion,'cantidad'=>$cantidadesMov[$categoria]);

		$fechaActualizacionErrores = '';
		$rsControl = mysql_query("SELECT COALESCE(fecha_error_inmediato,actualizado) fecha_actualizacion FROM $base_historicos.sss_presentacion_control WHERE id_lote=$id_lote LIMIT 1");
		if($rsControl && ($filaControl = mysql_fetch_assoc($rsControl))) $fechaActualizacionErrores = $filaControl['fecha_actualizacion'];

		echo json_encode(array(
		    'resumen_tbt' => $resumen_tbt,
		    'resumen_errores' => $resumen_errores,
		    'resumen_rechazos' => $resumen_rechazos,
		    'resumen_movimientos' => $resumen_movimientos,
		    'fecha_actualizacion_errores' => $fechaActualizacionErrores
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

	    // Consulta portable: no depende de la firma variable de NOV_mostrar_lote.
	    $sqlExport = "SELECT d.convenio desreguladora,
	        IF(a.id_titular IS NULL OR a.id_titular=0,p.cuil,pt.cuil) cuil_titular,
	        pa.parentesco,p.cuil,p.nd,CONCAT(p.apellido,' ',p.nombre) ayn,p.sexo,
	        TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) edad,p.fn,a.incapacidad,
	        tb.sigla tbt,t.tipo_mov,t.fec_mov fecha_movimiento,
	        t.cod_error_ errores,t.cod_rechazados rechazos,
	        IF(a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id,1,0) es_titular
	        FROM $base_historicos.novedades_exportables t
	        LEFT JOIN $base_padron.persona p ON p.id=t.id_persona
	        LEFT JOIN $base_padron.afiliados a ON a.id_persona=p.id
	        LEFT JOIN $base_padron.desreguladoras d ON d.id=a.id_desreguladora
	        LEFT JOIN $base_padron.parentesco pa ON pa.id=a.id_parentesco
	        LEFT JOIN $base_padron.afiliados at ON at.id=IF(a.id_titular IS NULL OR a.id_titular=0,a.id,a.id_titular)
	        LEFT JOIN $base_padron.persona pt ON pt.id=at.id_persona
	        LEFT JOIN $base_padron.tipo_beneficiario_titular tb ON tb.id=at.id_tipo_aporte
	        WHERE t.id_lote=$id_lote ORDER BY t.id";
	    $result = mysql_query($sqlExport) or die(mysql_error());

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
	    $resumenMov = array(
	    	'A'=>array('titulares'=>0,'familiares'=>0),
	    	'B'=>array('titulares'=>0,'familiares'=>0),
	    	'M'=>array('titulares'=>0,'familiares'=>0)
	    );
	    $resumenGer = array();
	    $resumenErr = array();
	    $resumenRech = array();
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
	        $mov = strtoupper(trim($row['tipo_mov']));
	        $grupo = intval($row['es_titular']) === 1 ? 'titulares' : 'familiares';
	        if(isset($resumenMov[$mov])) $resumenMov[$mov][$grupo]++;
	        $ger = trim((string)$row['desreguladora']) !== '' ? $row['desreguladora'] : 'Sin gerenciadora';
	        if(!isset($resumenGer[$ger])) $resumenGer[$ger] = array('titulares'=>0,'familiares'=>0);
	        $resumenGer[$ger][$grupo]++;
	        $err = trim((string)$row['errores']);
	        $rech = trim((string)$row['rechazos']);
	        if($err !== '') $resumenErr[$err] = isset($resumenErr[$err]) ? $resumenErr[$err]+1 : 1;
	        if($rech !== '') $resumenRech[$rech] = isset($resumenRech[$rech]) ? $resumenRech[$rech]+1 : 1;
	        $rowIndex++;
	    }

	    $hojaMov = $objPHPExcel->createSheet();
	    $hojaMov->setTitle('Resumen movimientos');
	    $hojaMov->fromArray(array(
	    	array('', 'Titulares', 'Familiares', 'Todos'),
	    	array('Altas', $resumenMov['A']['titulares'], $resumenMov['A']['familiares'], array_sum($resumenMov['A'])),
	    	array('Bajas', $resumenMov['B']['titulares'], $resumenMov['B']['familiares'], array_sum($resumenMov['B'])),
	    	array('Modificaciones', $resumenMov['M']['titulares'], $resumenMov['M']['familiares'], array_sum($resumenMov['M'])),
	    	array('Todos',
	    		$resumenMov['A']['titulares']+$resumenMov['B']['titulares']+$resumenMov['M']['titulares'],
	    		$resumenMov['A']['familiares']+$resumenMov['B']['familiares']+$resumenMov['M']['familiares'],
	    		array_sum($resumenMov['A'])+array_sum($resumenMov['B'])+array_sum($resumenMov['M']))
	    ), null, 'A1');

	    $hojaGer = $objPHPExcel->createSheet();
	    $hojaGer->setTitle('Gerenciadoras');
	    $hojaGer->fromArray(array(array('Gerenciadora','Titulares','Familiares','Todos')), null, 'A1');
	    $filaResumen = 2;
	    ksort($resumenGer);
	    foreach($resumenGer as $nombreGer=>$cantGer){
	    	$hojaGer->fromArray(array($nombreGer,$cantGer['titulares'],$cantGer['familiares'],$cantGer['titulares']+$cantGer['familiares']), null, 'A'.$filaResumen++);
	    }

	    $catalogo = sss_catalogo_en_memoria();
	    foreach(array('Errores FTP'=>$resumenErr,'Rechazos'=>$resumenRech) as $tituloHoja=>$resumenCodigos){
	    	$hojaCod = $objPHPExcel->createSheet();
	    	$hojaCod->setTitle($tituloHoja);
	    	$hojaCod->fromArray(array(array('Código','Campo','Descripción','Acción sugerida','Cantidad')), null, 'A1');
	    	$filaResumen = 2;
	    	ksort($resumenCodigos);
	    	foreach($resumenCodigos as $codigoCompuesto=>$cantidadCodigo){
	    		$detalle = sss_detalle_codigos($codigoCompuesto);
	    		$campo = '';
	    		foreach($detalle['codigos'] as $codigoSimple){
	    			if(isset($catalogo[$codigoSimple]) && $campo === '') $campo = $catalogo[$codigoSimple]['campo'];
	    		}
	    		$hojaCod->fromArray(array($codigoCompuesto,$campo,$detalle['descripcion'],$detalle['accion'],$cantidadCodigo), null, 'A'.$filaResumen++);
	    	}
	    }

	    foreach($objPHPExcel->getWorksheetIterator() as $hoja){
	    	$hoja->freezePane('A2');
	    	$ultimaColumna = $hoja->getHighestColumn();
	    	$ultimaFila = $hoja->getHighestRow();
	    	if($ultimaFila >= 1) $hoja->setAutoFilter('A1:'.$ultimaColumna.$ultimaFila);
	    	for($letra='A'; $letra<=$ultimaColumna; $letra++) $hoja->getColumnDimension($letra)->setAutoSize(true);
	    	$hoja->getStyle('A1:'.$ultimaColumna.'1')->getFont()->setBold(true);
	    }
	    $objPHPExcel->setActiveSheetIndex(0);

	    // Encabezados para la descarga
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header("Content-Disposition: attachment;filename=\"$filename\"");
	    header('Cache-Control: max-age=0');

	    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $writer->save('php://output');
	    exit;

	    break;
	
	case 'exportar_afiliados_excel_legacy':

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
	    mysql_query("CALL $base_padron.NOV_mostrar_lote($id_lote)") or die(mysql_error());

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
		    $sql_sp = "CALL $base_padron.NOV_mostrar_lote($id_lote)";
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
		$sql="SELECT COALESCE(NULLIF(TRIM(d.convenio),''),'Sin gerenciadora') as desreguladora,
			SUM(IF(a.id_titular IS NULL OR a.id_titular=0 OR a.id_titular=a.id,1,0)) AS titulares,
			SUM(IF(a.id_titular IS NOT NULL AND a.id_titular<>0 AND a.id_titular<>a.id,1,0)) AS familiares,
			COUNT(*) as contador

			FROM $base_historicos.novedades_exportables t
			LEFT JOIN $base_padron.persona p ON t.id_persona=p.id 
			LEFT JOIN $base_padron.afiliados a ON p.id=a.id_persona 
			LEFT JOIN $base_padron.desreguladoras d ON a.id_desreguladora=d.id 
			LEFT JOIN $base_padron.parentesco pa ON a.id_parentesco=pa.id 
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

	case 'quitar_fct_presentacion_multiple':

		if(isset($_POST['ids_expo']) && is_array($_POST['ids_expo']) && intval($_POST['id_lote']) > 0){

			$id_lote = intval($_POST['id_lote']);
			$ids_expo = array();

			foreach($_POST['ids_expo'] as $id){
				$ids_expo[] = intval($id);
			}

			$ids_expo = array_filter($ids_expo);

			if(count($ids_expo) > 0){
				$ids = implode(',', $ids_expo);

				$delete = "DELETE FROM $base_historicos.novedades_exportables 
						   WHERE id_lote = $id_lote 
						   AND id IN ($ids)";

				mysql_query($delete) or die(mysql_error().$delete);

				echo "ok";
			}else{
				echo "error";
			}
		}else{
			echo "error";
		}

		break;

	case 'procesar_errores':

			$id_presentacion=$_POST['id_presentacion'];
			$id_lote = null;

			if(isset($_FILES['file_errores']) && $_FILES['file_errores']['error']==0) {

				$nombre_archivo = $nombre."_".$periodo.".".$extension;
				$archivo_send = __DIR__."/aceptados/";
				$path = $archivo_send.$nombre_archivo;

				$copiado = move_uploaded_file($_FILES['file_errores']['tmp_name'], $path);

				if($copiado==false){
					echo "Error moviendo el archivo $path";
					break;
				}

				$id_lote = importar_archivo_errores_novedades($path, $periodo, $id_presentacion, $id_usuario);
			}

			echo $id_lote;

		break;

	case 'procesar_aceptados':
	case 'procesar_aceptados_dev':
		if(isset($_FILES['file_aceptados']) && $_FILES['file_aceptados']['error']==0) {
			$nombre_archivo = $nombre."_".$periodo.".".$extension;
			$archivo_send = __DIR__."/aceptados/";
			$path = $archivo_send.$nombre_archivo;
			$copiado = move_uploaded_file($_FILES['file_aceptados']['tmp_name'], $path);
			if($copiado==false){
				echo "Error moviendo el archivo $path";
				break;
			}
			$rsPres = mysql_query("SELECT id FROM ".N_BASE_HISTORICOS.".lotes WHERE proceso='novedades_exportables' AND descripcion='".sss_escape($periodo)."' ORDER BY id DESC LIMIT 1");
			$pres = $rsPres ? mysql_fetch_object($rsPres) : null;
			$idPresentacionManual = $pres ? intval($pres->id) : 0;
			$cantidad = importar_archivo_resultado_sss($path,$periodo,$idPresentacionManual,$id_usuario,'ACEPTADO');
			echo $cantidad;
		}
		break;
	case 'procesar_rechazados':
			if(isset($_FILES['file_rechazados']) && $_FILES['file_rechazados']['error']==0) {
				$nombre_archivo = $nombre."_".$periodo.".".$extension;
				$archivo_send = __DIR__."/aceptados/";
				$path = $archivo_send.$nombre_archivo;
				$copiado = move_uploaded_file($_FILES['file_rechazados']['tmp_name'], $path);
				if($copiado==false){
					echo "Error moviendo el archivo $path";
					break;
				}
				$rsPres = mysql_query("SELECT id FROM ".N_BASE_HISTORICOS.".lotes WHERE proceso='novedades_exportables' AND descripcion='".sss_escape($periodo)."' ORDER BY id DESC LIMIT 1");
				$pres = $rsPres ? mysql_fetch_object($rsPres) : null;
				$idPresentacionManual = $pres ? intval($pres->id) : 0;
				$cantidad = importar_archivo_resultado_sss($path,$periodo,$idPresentacionManual,$id_usuario,'RECHAZADO');
				sss_propagar_errores_al_periodo_siguiente($idPresentacionManual,$periodo,$id_usuario);
				echo $cantidad;
			}
		break;
	case 'lst_novedades_presentaciones':
		mysql_query("CALL $base_padron.`novedades_envio_presentaciones`()") or die(mysql_error()."ERROR stored");

		$sql = "SELECT p.*,COALESCE(c.estado,IF(LOWER(p.estado)='cerrado','CERRADO','PREPARADO')) AS estado_circuito,
				c.resultados_disponibles_desde,c.fecha_enviado,c.fecha_resultado,c.ultimo_error
				FROM $base_padron.lst_novedades_presentaciones p
				LEFT JOIN ".N_BASE_HISTORICOS.".sss_presentacion_control c ON c.id_lote=p.id";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		sss_json(isset($json)?$json:array());
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

	case 'ftp_sss_subir_novedades':
		// Sube por FTPS nativo de PHP el TXT que ya genero "Exportar".
		header('Content-Type: application/json; charset=utf-8');

		$filename = strtoupper(INST_NAME)."_novedades_".$periodo.".txt";
		$rutaLocal = __DIR__.'/files/'.$filename;

		if(!file_exists($rutaLocal)){
			echo json_encode(array('status' => 'error', 'mensaje' => "No se encontro el archivo a subir: $rutaLocal. Primero hay que generarlo con la opcion 'Exportar'."));
			break;
		}

		$mensajeConfig = '';
		if(!ftp_sss_configuracion_disponible(INST_NAME, INST_RNOS, $mensajeConfig)){
			sss_registrar_estado($id_lote,$periodo,'SIN_CREDENCIALES_FTPS',$id_usuario,array('ultimo_error'=>$mensajeConfig));
			sss_json(array('status'=>'error','mensaje'=>$mensajeConfig));
			break;
		}
		try{
			$respuestaFtp = sss_ftp_subir_php($rutaLocal, $periodo);
			sss_registrar_estado($id_lote,$periodo,'ENVIADO',$id_usuario,array('fecha_enviado'=>date('Y-m-d H:i:s'),'ultimo_error'=>''));
			sss_json($respuestaFtp);
		}catch(Exception $e){
			$mensajeFtp = $e->getMessage();
			sss_registrar_estado($id_lote,$periodo,'ERROR_ENVIO',$id_usuario,array('ultimo_error'=>$mensajeFtp));
			sss_json(array('status'=>'error','mensaje'=>$mensajeFtp));
		}
		break;

	case 'ftp_sss_traer_devolucion':
		// Busca en el FTP de SSS los archivos .ok/.err del periodo y los
		// baja a files/devoluciones/ via Python (mismo motivo que arriba).
		// Todavia NO los importa: solo los deja disponibles localmente,
		// el importado sigue siendo manual desde "Gestion de Errores"
		// como primer paso auditable.
		header('Content-Type: application/json; charset=utf-8');

		$carpetaDestino = __DIR__.'/files/devoluciones';
		$scriptPath = __DIR__.'/scripts/ftp_sss.py';
		$mensajeConfig = '';
		if(!ftp_sss_configuracion_disponible(INST_NAME, INST_RNOS, $mensajeConfig)){ sss_json(array('status'=>'error','mensaje'=>$mensajeConfig)); break; }
		$configPath = ftp_sss_config_path();

		$cmd = sss_python_ejecutable().' '.escapeshellarg($scriptPath)
			.' devolucion'
			.' --inst '.escapeshellarg(INST_NAME)
			.' --periodo '.escapeshellarg($periodo)
			.' --rnos '.escapeshellarg(INST_RNOS)
			.' --config '.escapeshellarg($configPath)
			.' --destino '.escapeshellarg($carpetaDestino)
			.' 2>&1';

		$salida = shell_exec($cmd);
		$decodificado = json_decode(trim((string)$salida), true);

		if($decodificado === null){
			sss_json(array('status' => 'error', 'mensaje' => sss_error_proceso_ftps($salida, $configPath)));
		}
		else{
			if(!empty($decodificado['encontrado_err'])) sss_registrar_estado($id_lote,$periodo,'ERROR_INMEDIATO_DISPONIBLE',$id_usuario,array('fecha_error_inmediato'=>date('Y-m-d H:i:s')));
			elseif(!empty($decodificado['encontrado_ok'])) sss_registrar_estado($id_lote,$periodo,'VALIDADO_SIN_ERRORES',$id_usuario,array('fecha_error_inmediato'=>date('Y-m-d H:i:s')));
			sss_json($decodificado);
		}
		break;

	case 'ftp_sss_generar_archivo':
		// Genera el archivo de novedades (misma consulta que usa "Exportar")
		// sin forzar descarga y sin tocar el FTP. Paso 1 del circuito
		// automatico; el paso 2 (subir) reusa 'ftp_sss_subir_novedades'.
		header('Content-Type: application/json; charset=utf-8');

		$call = "CALL $base_padron.NOV_presentar_periodo($id_lote);";
		mysql_query($call) or die(mysql_error()."<br>".$call);

		$sqlNov = "SELECT * FROM $base_padron.tmp_novedades";
		$resultNov = mysql_query($sqlNov) or die(mysql_error().$sqlNov);

		$filename = strtoupper(INST_NAME)."_novedades_".$periodo.".txt";
		$rutaLocal = __DIR__.'/files/'.$filename;

		$rutaTemporal = $rutaLocal.'.tmp';
		$fileHandle = fopen($rutaTemporal, "wb");
		$cantidadMovimientos = 0;
		while ($row = mysql_fetch_assoc($resultNov)) {
			if(count($row) !== 27){
				fclose($fileHandle); @unlink($rutaTemporal);
				sss_json(array('status'=>'error','mensaje'=>'El procedimiento de exportacion no devolvio los 27 campos exigidos por el Anexo 7.'));
				break 2;
			}
			foreach($row as $valor){
				if(strpos($valor, '|') !== false || strpos($valor, "\n") !== false || strpos($valor, "\r") !== false){
					fclose($fileHandle); @unlink($rutaTemporal);
					sss_json(array('status'=>'error','mensaje'=>'Hay un dato con separadores o saltos de linea que corromperia el archivo SSS.'));
					break 3;
				}
			}
			$line = implode("|", $row)."\r\n";
			fwrite($fileHandle, utf8_decode($line));
			$cantidadMovimientos++;
		}
		fclose($fileHandle);
		rename($rutaTemporal, $rutaLocal);
		$hashArchivo = hash_file('sha256', $rutaLocal);
		$rsCierre = mysql_query("SELECT obrasocial FROM ".N_BASE_HISTORICOS.".lotes WHERE id=".intval($id_lote));
		$fechaCierre = $rsCierre ? mysql_fetch_object($rsCierre)->obrasocial : date('Y-m-d');
		$fechaResultado = sss_fecha_respuesta_cronograma($periodo, $fechaCierre);
		sss_registrar_estado($id_lote,$periodo,'GENERADO',$id_usuario,array('archivo_envio'=>$filename,'hash_archivo'=>$hashArchivo,'cantidad_movimientos'=>$cantidadMovimientos,'fecha_cierre'=>$fechaCierre,'resultados_disponibles_desde'=>$fechaResultado,'fecha_generado'=>date('Y-m-d H:i:s')));

		sss_json(array(
			'status'               => 'ok',
			'archivo'              => $filename,
			'hash_sha256'          => $hashArchivo,
			'cantidad_movimientos' => $cantidadMovimientos,
		));
		break;

	case 'ftp_sss_importar_errores_descargados':
		// Ultimo paso del circuito: importa el .err que YA fue descargado
		// a disco por 'ftp_sss_traer_devolucion' (no vuelve a tocar el FTP).
		// Misma logica que "Gestion de Errores" manual.
		header('Content-Type: application/json; charset=utf-8');

		$periodoCompacto = str_replace('-', '', $periodo);
		$rutaErr = __DIR__.'/files/devoluciones/'.INST_RNOS.'-'.$periodoCompacto.'.err';

		if(!file_exists($rutaErr)){
			echo json_encode(array('status' => 'error', 'mensaje' => "No se encontro el archivo de errores descargado en $rutaErr. Primero hay que traer la devolucion."));
			break;
		}

		$id_lote_errores = importar_archivo_errores_novedades($rutaErr, $periodo, $id_lote, $id_usuario);
		$errores_importados = 0;

		if($id_lote_errores){
			$rsCant = mysql_query("SELECT COUNT(*) AS total FROM ".N_BASE_HISTORICOS.".novedades_sss_errores WHERE id_lote=$id_lote_errores");
			$objCant = $rsCant ? mysql_fetch_object($rsCant) : null;
			$errores_importados = $objCant ? (int)$objCant->total : 0;
		}

		$propagados = sss_propagar_errores_al_periodo_siguiente($id_lote, $periodo, $id_usuario);
		sss_registrar_estado($id_lote,$periodo,'ERRORES_INMEDIATOS_IMPORTADOS',$id_usuario,array('fecha_error_inmediato'=>date('Y-m-d H:i:s')));
		sss_json(array(
			'status'              => 'ok',
			'id_lote_errores'     => $id_lote_errores,
			'errores_importados'  => $errores_importados,
			'errores_propagados'  => $propagados,
		));
		break;

	case 'ftp_sss_procesar_resultados':
		$id_lote = intval($id_lote);
		$rsLote = mysql_query("SELECT l.descripcion,l.estado,l.obrasocial,c.estado estado_circuito FROM ".N_BASE_HISTORICOS.".lotes l LEFT JOIN ".N_BASE_HISTORICOS.".sss_presentacion_control c ON c.id_lote=l.id WHERE l.id={$id_lote} AND l.proceso='novedades_exportables'");
		$lote = $rsLote ? mysql_fetch_assoc($rsLote) : null;
		if(!$lote){ sss_json(array('status'=>'error','mensaje'=>'No existe la presentacion indicada.'),404); break; }
		if($lote['estado_circuito']==='RESULTADOS_IMPORTADOS'){ sss_json(array('status'=>'ok','mensaje'=>'Los resultados de este periodo ya fueron importados.','aceptados_importados'=>0,'rechazados_importados'=>0,'errores_propagados'=>0)); break; }
		if(strtolower($lote['estado']) !== 'cerrado'){ sss_json(array('status'=>'pendiente','mensaje'=>'Los resultados definitivos solo se importan sobre periodos cerrados.')); break; }
		$disponibleDesde = sss_fecha_respuesta_cronograma($lote['descripcion'],$lote['obrasocial']);
		if(date('Y-m-d') < $disponibleDesde){ sss_json(array('status'=>'pendiente','mensaje'=>'Segun el cronograma, controlar resultados desde '.$disponibleDesde,'disponible_desde'=>$disponibleDesde)); break; }

		$destino = __DIR__.'/files/resultados'; if(!is_dir($destino)) mkdir($destino,0770,true);
		$mensajeConfig = '';
		if(!ftp_sss_configuracion_disponible(INST_NAME, INST_RNOS, $mensajeConfig)){ sss_json(array('status'=>'error','mensaje'=>$mensajeConfig)); break; }
		$configPath = ftp_sss_config_path();
		$cmd = sss_python_ejecutable().' '.escapeshellarg(__DIR__.'/scripts/ftp_sss.py').' resultados --inst '.escapeshellarg(INST_NAME).' --rnos '.escapeshellarg(INST_RNOS).' --config '.escapeshellarg($configPath).' --periodo '.escapeshellarg($lote['descripcion']).' --destino '.escapeshellarg($destino).' 2>&1';
		$salidaResultados = shell_exec($cmd);
		$respuesta = json_decode(trim((string)$salidaResultados),true);
		if(!$respuesta || $respuesta['status']!=='ok'){ sss_json($respuesta?$respuesta:array('status'=>'error','mensaje'=>sss_error_proceso_ftps($salidaResultados, $configPath))); break; }
		if(empty($respuesta['disponible'])){ sss_registrar_estado($id_lote,$lote['descripcion'],'ESPERANDO_RESULTADOS',$id_usuario,array('resultados_disponibles_desde'=>$disponibleDesde)); sss_json(array('status'=>'pendiente','mensaje'=>'La SSS aun no publico Devolucion.zip.')); break; }

		$aceptados = !empty($respuesta['aceptados']) ? importar_archivo_resultado_sss($respuesta['aceptados'],$lote['descripcion'],$id_lote,$id_usuario,'ACEPTADO') : 0;
		$rechazados = !empty($respuesta['rechazos']) ? importar_archivo_resultado_sss($respuesta['rechazos'],$lote['descripcion'],$id_lote,$id_usuario,'RECHAZADO') : 0;
		$propagados = sss_propagar_errores_al_periodo_siguiente($id_lote,$lote['descripcion'],$id_usuario);
		sss_registrar_estado($id_lote,$lote['descripcion'],'RESULTADOS_IMPORTADOS',$id_usuario,array('fecha_resultado'=>date('Y-m-d H:i:s'),'resultados_disponibles_desde'=>$disponibleDesde));
		sss_json(array('status'=>'ok','aceptados_importados'=>$aceptados,'rechazados_importados'=>$rechazados,'errores_propagados'=>$propagados));
		break;

	case 'lst_cronologia_afiliado':
			$id_persona = isset($_GET['id_persona']) ? intval($_GET['id_persona']) : 0;
			if(!$id_persona){
				sss_json(array('status'=>'error','mensaje'=>'No se identificó al afiliado.'),400);
				break;
			}
			mysql_query("CALL $base_padron.`novedades_cronologia`($id_persona)") or die(mysql_error()."ERROR stored");

			$sql = "SELECT DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fechador_mostrar,id_usuario,MID(evento,1,500) AS evento,
						'PADRON' AS estado,'' AS periodo,'' AS codigo_error,fechador AS fecha_orden
						FROM $base_padron.tmp_cronologia_novedades
						UNION ALL
						SELECT DATE_FORMAT(fechador,'%d/%m/%Y %H:%i'),id_usuario,detalle,estado,COALESCE(periodo,''),COALESCE(codigo_error,''),fechador
						FROM ".N_BASE_HISTORICOS.".sss_afiliado_cronologia WHERE id_persona=".intval($id_persona)."
						ORDER BY fecha_orden DESC" ;

			$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
					
			$json = array();
			while ($row = mysql_fetch_assoc($result)) {

								
			    $json[] = array(
			        		'fechador' => $row['fechador_mostrar'],
			        		'id_usuario' => $row['id_usuario'],			        			        		
			        		'movimiento' => $row['evento'],
			        		'estado' => $row['estado'],
			        		'periodo' => $row['periodo'],
			        		'codigo_error' => $row['codigo_error']
			        		       
			      );
			}
			
			sss_json($json);


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

/**
 * Logica de importacion del archivo de errores de SSS (.err), extraida
 * tal cual estaba en el case 'procesar_errores' para poder reusarla
 * tanto desde la carga manual (modal "Gestion de Errores") como desde
 * el circuito automatico via FTP. No cambia el comportamiento existente,
 * solo evita duplicar el codigo.
 *
 * @param string $path            ruta local del archivo .err ya disponible en disco
 * @param string $periodo
 * @param int    $id_presentacion id del lote de la presentacion (novedades_exportables)
 * @param int    $id_usuario
 *
 * @return int|null id del lote de errores generado (null si no se pudo leer el archivo)
 */
function importar_archivo_errores_novedades($path, $periodo, $id_presentacion, $id_usuario){
	$id_lote = null;

	if(file_exists($path)){
		$gestor = fopen($path, "r");
		$konta = 0;

		while ($input = fgets($gestor, 4096)) {
			$input = str_replace("'", " ", $input);

			if($input != ""){
				$konta++;
				insertar_errores($input, $periodo);
			}
		}
		fclose($gestor);

		$id_lote = graba_lote_errores($konta, $periodo, $id_usuario);

		mysql_query("UPDATE ".N_BASE_HISTORICOS.".`novedades_sss_errores` n
						JOIN ".N_BASE_PADRON.".persona p ON n.cuil=p.cuil
						SET n.id_persona=p.id
						WHERE id_lote=$id_lote") or die(mysql_error());
	}

	mysql_query("UPDATE ".N_BASE_HISTORICOS.".`novedades_exportables` ne
					SET ne.`cod_error_`=NULL
					WHERE ne.id_lote='$id_presentacion'");

	if($id_lote){
		$query = "UPDATE ".N_BASE_HISTORICOS.".`novedades_exportables` ne
					JOIN ".N_BASE_HISTORICOS.".`novedades_sss_errores` nr ON ne.id_persona=nr.id_persona
															AND nr.id_lote=$id_lote
															AND ne.`tipo_mov`=nr.`cod_mov`
					SET ne.`cod_error_`=TRIM(BOTH '-' FROM CONCAT_WS('-',NULLIF(TRIM(nr.cod_error),''),NULLIF(TRIM(nr.cod_error2),''),NULLIF(TRIM(nr.cod_error3),'')))
					WHERE ne.id_lote=$id_presentacion";
		mysql_query($query) or die(mysql_error().$query);

		$rsCrono = mysql_query("SELECT id_persona,TRIM(BOTH '-' FROM CONCAT_WS('-',NULLIF(TRIM(cod_error),''),NULLIF(TRIM(cod_error2),''),NULLIF(TRIM(cod_error3),''))) codigo FROM ".N_BASE_HISTORICOS.".novedades_sss_errores WHERE id_lote=".intval($id_lote)." AND id_persona IS NOT NULL");
		if($rsCrono) while($fila = mysql_fetch_assoc($rsCrono)){
			$detalle = sss_detalle_codigos($fila['codigo']);
			sss_cronologia($fila['id_persona'],$id_presentacion,$periodo,'ERROR_VALIDACION',$fila['codigo'],$detalle['descripcion'],$id_usuario);
		}
	}

	return $id_lote;
}

function importar_archivo_resultado_sss($path, $periodo, $idPresentacion, $idUsuario, $tipo){
	if(!$path || !file_exists($path)) return 0;
	$gestor = fopen($path,'r'); if(!$gestor) return 0;
	$konta = 0;
	while(($input = fgets($gestor,4096)) !== false){
		$input = trim(str_replace("'",' ',$input));
		if($input==='') continue;
		$campos = explode('|',$input);
		if(count($campos) < 28) continue;
		if($tipo==='ACEPTADO') insertar_aceptados($input,$periodo); else insertar_rechazados($input,$periodo);
		$konta++;
	}
	fclose($gestor);
	if($konta===0) return 0;

	if($tipo==='ACEPTADO'){
		$idResultado = graba_lote_aceptados($konta,$periodo,$idUsuario);
		$tabla = 'novedades_sss_aceptados'; $estado = 'ACEPTADO';
	} else {
		$idResultado = graba_lote_rechazados($konta,$periodo,$idUsuario);
		$tabla = 'novedades_sss_rechazados'; $estado = 'RECHAZADO';
	}

	$base = N_BASE_HISTORICOS;
	mysql_query("UPDATE {$base}.{$tabla} n JOIN ".N_BASE_PADRON.".persona p ON p.cuil=n.cuil SET n.id_persona=p.id WHERE n.id_lote=".intval($idResultado)) or die(mysql_error());
	if($tipo==='ACEPTADO'){
		mysql_query("UPDATE {$base}.novedades_exportables ne JOIN {$base}.{$tabla} r ON r.id_lote=".intval($idResultado)." AND r.id_persona=ne.id_persona AND r.cod_mov=ne.tipo_mov SET ne.cod_error_=NULL WHERE ne.id_lote=".intval($idPresentacion)) or die(mysql_error());
	} else {
		mysql_query("UPDATE {$base}.novedades_exportables ne JOIN {$base}.{$tabla} r ON r.id_lote=".intval($idResultado)." AND r.id_persona=ne.id_persona AND r.cod_mov=ne.tipo_mov SET ne.cod_error_=TRIM(BOTH '-' FROM CONCAT_WS('-',NULLIF(TRIM(r.cod_error),''),NULLIF(TRIM(r.cod_error2),''),NULLIF(TRIM(r.cod_error3),''))) WHERE ne.id_lote=".intval($idPresentacion)) or die(mysql_error());
	}

	$rs = mysql_query("SELECT id_persona,TRIM(BOTH '-' FROM CONCAT_WS('-',NULLIF(TRIM(cod_error),''),NULLIF(TRIM(cod_error2),''),NULLIF(TRIM(cod_error3),''))) codigo FROM {$base}.{$tabla} WHERE id_lote=".intval($idResultado)." AND id_persona IS NOT NULL");
	if($rs) while($fila=mysql_fetch_assoc($rs)){
		$detalle = $tipo==='ACEPTADO' ? 'Novedad aceptada por la SSS.' : sss_detalle_codigos($fila['codigo']);
		$texto = is_array($detalle) ? $detalle['descripcion'] : $detalle;
		sss_cronologia($fila['id_persona'],$idPresentacion,$periodo,$estado,$fila['codigo'],$texto,$idUsuario);
	}
	return $konta;
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
