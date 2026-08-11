<?php 
include("../../Config/Conectar.inc");
header('Content-Type: application/json');
mysql_query("SET NAMES 'utf8'");

$id_usuario = $_SESSION['iduser'];

switch ($parametro) {	
	case 'listado_principal':
		// code...
		switch ($tipo_fecha) {
			case 'fechador':
				$cond_fecha = " AND $tipo_fecha BETWEEN CONCAT('$fdesde',' 00:00:00') AND CONCAT('$fhasta',' 23:59:00')  ";
				break;
			case 'fecha_aPartir':
				// code...
				$cond_fecha = " AND $tipo_fecha BETWEEN '$fdesde' AND '$fhasta'  ";
				break;
			case 'fechador_aprobacion':
				// code...
				$cond_fecha = " AND $tipo_fecha BETWEEN CONCAT('$fdesde',' 00:00:00') AND CONCAT('$fhasta',' 23:59:00')  ";
				break;
			default:
				// code...
				break;
		}

		$cond_aprobados = "";
		switch ($aprobados) {
			case 'aprobados':
				$cond_aprobados = " AND h.aprobacion_historico=1 ";
				break;
			case 'no_aprobados':
				$cond_aprobados = " AND h.aprobacion_historico=0 ";
				break;
			default:
				$cond_aprobados = "";
				break;
		}

		switch ($tipo_movimiento) {
			case '':
				$cond_tipo_movimiento = "";
				break;			
			case 'alta':
				$cond_tipo_movimiento = " AND h.estado='$tipo_movimiento' ";
				break;
			case 'baja':
				$cond_tipo_movimiento = " AND h.estado='$tipo_movimiento' ";
				break;
		}
		
		if (!empty($_GET['motivo_descripcion']) && is_array($_GET['motivo_descripcion'])) {
	        $ids = array_map('intval', $_GET['motivo_descripcion']);
	        $cond_evento_afiliado = " AND h.id_evento_afiliado IN (" . implode(',', $ids) . ") ";
	    }
	    else{
	    	$cond_evento_afiliado = "";
	    }

		

		$query = "SELECT h.*,v.*,ea.*,u.usuario as usuario,h.estado AS estado_mov,
							coalesce(date_format(h.fecha_aPartir,'%d/%m/%Y'),'') as fecha_aPartir_f,
							coalesce(date_format(h.fechador,'%d/%m/%Y %H:%i'),'') as fechador_f,
							coalesce(date_format(h.fechador_aprobacion,'%d/%m/%Y %H:%i'),'') as fechador_aprobacion_f,
							CASE
    								when procedencia='baja_rg' then 'BAJAS Traspasos RG'
    								when procedencia='alta_rg' then 'ALTAS Traspasos RG'
    								when procedencia='baja_mt' then 'BAJAS Traspasos MT'
    								when procedencia='alta_mt' then 'ALTAS Traspasos MT'
    								when procedencia='C_MAN' then ea.descripcion
    								when procedencia='C_AUT' then ea.descripcion
    							END as evento_descripcion,
    							uu.usuario AS usu_aprobacion
					FROM $base_historicos.`_historico_afiliados` h 
					JOIN $base_padron.v_padron_general v ON h.id_afiliado=v.id_afiliado 
					LEFT JOIN $base_padron.eventos_afiliados ea ON h.descripcion=ea.descripcion 
					LEFT JOIN $base_usuarios.users u ON h.id_usuario=u.id 
					LEFT JOIN $base_usuarios.users uu ON h.id_usu_aprobacion=uu.id 
					WHERE 1=1
						$cond_fecha
						$cond_tipo_movimiento
						$cond_evento_afiliado
						$cond_aprobados
					
					";
		#echo "$query"; exit();

		generar_json_automatico($query);

		break;

	case 'reporte_excel':
    	// code...
    	
        require_once '../../Lib/PHPExcel/Classes/PHPExcel.php';

        switch ($tipo_fecha) {
			case 'fechador':
				$cond_fecha = " AND $tipo_fecha BETWEEN CONCAT('$fdesde',' 00:00:00') AND CONCAT('$fhasta',' 23:59:00')  ";
				break;
			case 'fecha_aPartir':
				// code...
				$cond_fecha = " AND $tipo_fecha BETWEEN '$fdesde' AND '$fhasta'  ";
				break;
			default:
				// code...
				break;
		}

		$cond_aprobados = "";
		switch ($aprobados) {
			case 'aprobados':
				$cond_aprobados = " AND h.aprobacion_historico=1 ";
				break;
			case 'no_aprobados':
				$cond_aprobados = " AND h.aprobacion_historico=0 ";
				break;
			default:
				$cond_aprobados = "";
				break;
		}

		switch ($tipo_movimiento) {
			case '':
				$cond_tipo_movimiento = "";
				break;			
			case 'alta':
				$cond_tipo_movimiento = " AND h.estado='$tipo_movimiento' ";
				break;
			case 'baja':
				$cond_tipo_movimiento = " AND h.estado='$tipo_movimiento' ";
				break;
		}

		
		if (!empty($_GET['motivo_descripcion']) && is_array($_GET['motivo_descripcion'])) {
	        $ids = array_map('intval', $_GET['motivo_descripcion']);
	        $cond_evento_afiliado = " AND h.id_evento_afiliado IN (" . implode(',', $ids) . ") ";
	    }
	    else{
	    	$cond_evento_afiliado = "";
	    }

	    

	    $sql = "
            SELECT v.cuil,v.apellido,v.nombre,v.parentesco,v.nben,v.gpar,
                h.estado,
                h.fecha_aPartir AS fecha_ab,
                h.fechador AS fecha_incorporacion,u.usuario AS usuario_incorporacion,h.id_usuario,
                h.fechador_aprobacion,uu.usuario AS usu_aprobacion,h.id_usu_aprobacion,
                ea.descripcion 
            FROM osetra_historicos._historico_afiliados h 
            JOIN osetra_padron.v_padron_general v ON h.id_afiliado=v.id_afiliado 
            LEFT JOIN osetra_padron.eventos_afiliados ea ON h.descripcion=ea.descripcion 
            LEFT JOIN osetra_usuarios.users u ON h.id_usuario=u.id 
            LEFT JOIN osetra_usuarios.users uu ON h.id_usu_aprobacion=uu.id 
            WHERE 1=1
            	$cond_fecha
				$cond_tipo_movimiento
				$cond_evento_afiliado
				$cond_aprobados
        ";
        #echo $sql ; exit();

        $fecha_hoy = date('Y-m-d');
        $titulo = "Reporte general de altas y bajas - $fecha_hoy";
        $subtitulo = "Rango de fechas: $fecha_desde al $fecha_hasta";

        // Crear nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Sistema")->setTitle($titulo);

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Títulos
        $sheet->setCellValue("A1", $titulo);
        $sheet->mergeCells("A1:N1");
        $sheet->setCellValue("A2", $subtitulo);
        $sheet->mergeCells("A2:N2");

        // Encabezados
        $sheet->fromArray([
            'CUIL', 'Apellido', 'Nombre', 'Parentesco', 'Nro Beneficiario', 'Grupo Familiar',
            'Estado', 'Fecha Alta/Baja', 'Fecha Incorporación', 'Usuario Incorporación', 'ID Usuario',
            'Fecha Aprobación', 'Usuario Aprobación', 'Evento'
        ], NULL, 'A4');

        // Consulta a la base de datos
        // 

        $res = mysql_query($sql);
        $fila = 5; // comienzo de datos

        while ($row = mysql_fetch_assoc($res)) {
            $sheet->setCellValue("A$fila", $row['cuil']);
            $sheet->setCellValue("B$fila", $row['apellido']);
            $sheet->setCellValue("C$fila", $row['nombre']);
            $sheet->setCellValue("D$fila", $row['parentesco']);
            $sheet->setCellValue("E$fila", $row['nben']);
            $sheet->setCellValue("F$fila", $row['gpar']);
            $sheet->setCellValue("G$fila", $row['estado']);
            $sheet->setCellValue("H$fila", $row['fecha_ab']);
            $sheet->setCellValue("I$fila", $row['fecha_incorporacion']);
            $sheet->setCellValue("J$fila", $row['usuario_incorporacion']);
            $sheet->setCellValue("K$fila", $row['id_usuario']);
            $sheet->setCellValue("L$fila", $row['fechador_aprobacion']);
            $sheet->setCellValue("M$fila", $row['usu_aprobacion']);
            $sheet->setCellValue("N$fila", $row['descripcion']);
            $fila++;
        }

        // Nombre del archivo
        $nombreArchivo = "reporte_altas_bajas_$fecha_hoy.xlsx";

        // Headers para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$nombreArchivo\"");
        header('Cache-Control: max-age=0');

        // Generar archivo
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output');
        exit;

    	break;
	
	case 'altas_bajas_diarias':
   

	    switch ($tipo_fecha) {
	    	case 'fecha_aPartir':
	    		// code...
	    		$campo_fecha = "h.fecha_aPartir";
	    		break;
	    	case 'fechador':
	    		// code...
	    		$campo_fecha = "h.fechador";
	    		break;
	    	case 'fecha_aprobacion':
	    		// code...
	    		$campo_fecha = "h.fecha_aprobacion";
	    		break;
	    	
	    	default:
	    		// code...
	    		break;
	    }

	    $query = "
	        SELECT DATE($campo_fecha) AS fecha,
	        		date_format($campo_fecha,'%d/%m/%Y') as fecha_format,
	            	SUM(IF(ea.estado='BAJA',1,0)) AS bajas, 
	            	SUM(IF(ea.estado='ALTA',1,0)) AS altas
	        FROM osetra_historicos._historico_afiliados h 
	        JOIN osetra_padron.v_padron_general v ON h.id_afiliado=v.id_afiliado 
	        JOIN osetra_padron.eventos_afiliados ea ON h.descripcion=ea.descripcion 
	        JOIN osetra_usuarios.users u ON h.id_usuario=u.id 
	        WHERE $campo_fecha BETWEEN '$desde' AND '$hasta 23:59:59'
	        GROUP BY 1
	        ORDER BY 1
	    ";

	    $res = mysql_query($query);
	    $datos = [];
	    while ($row = mysql_fetch_assoc($res)) {
	        $datos[] = [
	            'fecha' => $row['fecha'],
	            'fecha_format' => $row['fecha_format'],
	            'altas' => $row['altas'],
	            'bajas' => $row['bajas']
	        ];
	    }

	    echo json_encode($datos);
    	break;

    case 'altas_bajas_por_periodo':
    	// code...
    	switch ($tipo_fecha) {
	    	case 'fecha_aPartir':
	    		// code...
	    		$campo_fecha = "h.fecha_aPartir";
	    		break;
	    	case 'fechador':
	    		// code...
	    		$campo_fecha = "h.fechador";
	    		break;
	    	case 'fecha_aprobacion':
	    		// code...
	    		$campo_fecha = "h.fecha_aprobacion";
	    		break;
	    	
	    	default:
	    		// code...
	    		break;
	    }

    	$query = "SELECT MID($campo_fecha,1,7) AS periodo,	
							SUM(IF(ea.estado='BAJA',1,0)) AS bajas, 
							SUM(IF(ea.estado='ALTA',1,0)) AS altas
						FROM osetra_historicos._historico_afiliados h 
						JOIN osetra_padron.v_padron_general v ON h.id_afiliado=v.id_afiliado 
						JOIN osetra_padron.eventos_afiliados ea ON h.descripcion=ea.descripcion 
						JOIN osetra_usuarios.users u ON h.id_usuario=u.id 
						WHERE $campo_fecha BETWEEN '$desde' AND '$hasta'
						GROUP BY 1
						ORDER BY 1";
		generar_json_automatico($query);
    	break;

    case 'motivos_descripcion':
    	// code...
    	if(strlen($me_movimiento)==0 && $me_movimiento==""){
    		$cond_tipo_movimiento = "";
    	}
    	else{
    		$cond_tipo_movimiento = " AND estado='$me_movimiento' ";
    	}

    	/*
    	$query = "SELECT distinct CASE
    								when procedencia='baja_rg' then 'BAJAS Traspasos RG'
    								when procedencia='alta_rg' then 'ALTAS Traspasos RG'
    								when procedencia='baja_mt' then 'BAJAS Traspasos MT'
    								when procedencia='alta_mt' then 'ALTAS Traspasos MT'
    								when procedencia='C_MAN' then descripcion
    								when procedencia='C_AUT' then descripcion
    							END as motivo_descripcion 
    				from $base_historicos._historico_afiliados 
    				WHERE estado='$me_movimiento' having motivo_descripcion is not null limit 1000";*/

    	$query = "SELECT id,estado,CONCAT(UPPER(coalesce(estado,'')),' - ',coalesce(descripcion,'')) as motivo_descripcion 
    				FROM osetra_padron.`eventos_afiliados` 
    				WHERE 1=1
    					AND descripcion is not null 
    					AND estado IN ('ALTA','BAJA')
    					$cond_tipo_movimiento 
    				order by 2 ";

    	//echo $query ; exit();

    	generar_json_automatico($query);

    	break;

    case 'aprobar_seleccionados':
    	// code...
    	$registros = json_decode($_GET['registros'], true);

	    foreach ($registros as $r) {
	        $id_tabla = $r['id_tabla'];
	        $tabla_nombre = $r['tabla_nombre'];
	        // Lógica para marcar como aprobado
	        $update = "UPDATE osetra_historicos.$tabla_nombre 
	        			SET aprobado_aplicar_padron_individual=1,
	        				id_usu_aprobacion=$id_usuario,
							fechador_aprobacion=NOW()
	        			WHERE id=$id_tabla ";
	        //echo $update."<br>";
	        mysql_query($update) or die(mysql_error().$update);

	        $update_historico = "UPDATE osetra_historicos.`_historico_afiliados` 
									SET aprobacion_historico=1,
										id_usu_aprobacion=$id_usuario,
										fechador_aprobacion=NOW()
									WHERE tabla_nombre='$tabla_nombre'
										AND id_tabla='$id_tabla' ";

			mysql_query($update_historico) or die(mysql_error().$update_historico);
	    }

	    echo json_encode(['status' => 'ok']);
	    exit;
    	break;


	default:
		// code...
		break;
}


function generar_json_automatico($query){


	$result = mysql_query($query) or die(mysql_error().$query);
	$json = array();

	while($row=mysql_fetch_assoc($result)){
		$json[] = $row;
	}

	echo json_encode($json);

}

?>