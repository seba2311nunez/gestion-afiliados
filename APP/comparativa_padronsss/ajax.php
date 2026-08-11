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
	case 'listado_previo_nov_y_padronsss':

		// Construir consulta base
	    $base_query = "FROM $base_padron.tmp_afiliados_nov_padronsss_insertar WHERE id_padron_sss = $id_lote AND presentado='NO' ";
	    
	    // Aplicar filtros adicionales
	    $where = " AND 1=1";

		// Verificar si solo se piden los agrupamientos
	    $soloAgrupamientos = isset($_GET['solo_agrupamientos']) && $_GET['solo_agrupamientos'] == 'true';
	    
	    if ($soloAgrupamientos) {
	        // Consulta solo para agrupamientos
	        $sqlParentesco = "SELECT parentesco, COUNT(*) as total 
	                         FROM $base_padron.tmp_afiliados_nov_padronsss_insertar 
	                         WHERE id_padron_sss = $id_lote
	                         GROUP BY parentesco
	                         ORDER BY total DESC";
	        
	        $resParentesco = mysql_query($sqlParentesco);
	        $agrupamientos = array('parentesco' => array());
	        
	        while ($row = mysql_fetch_assoc($resParentesco)) {
	            $agrupamientos['parentesco'][] = $row;
	        }

	        // Total sin filtros
	        // Construir consulta base
	    	$base_query = "FROM $base_padron.tmp_afiliados_nov_padronsss_insertar WHERE id_padron_sss = $id_lote";
	    	// Filtro específico por parentesco (si existe)
		    if (isset($_GET['columns'][5]['search']['value']) && !empty($_GET['columns'][5]['search']['value'])) {
			    $parentesco = mysql_real_escape_string($_GET['columns'][5]['search']['value']);
			    $where .= " AND parentesco = '$parentesco'";
			}
		    $sqlTotal = "SELECT COUNT(*) $base_query";
		    $resTotal = mysql_query($sqlTotal);
		    $rowTotal = mysql_fetch_row($resTotal);
		    $recordsTotal = $rowTotal[0];
	        
	        echo json_encode(array(
	        						'agrupamientos' => $agrupamientos,
	        						"recordsTotal" => $recordsTotal));
	        
	        exit();
	    }
	    // Parámetros DataTable
	    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
	    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
	    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
	    $search = isset($_GET['search']['value']) ? mysql_real_escape_string($_GET['search']['value']) : '';
	    
	    // Parámetros de filtros adicionales
	    $tipo_fecha = isset($_GET['tipo_fecha']) ? mysql_real_escape_string($_GET['tipo_fecha']) : '';
	    $fecha_desde = isset($_GET['fdesde']) ? mysql_real_escape_string($_GET['fdesde']) : '';
	    $fecha_hasta = isset($_GET['fhasta']) ? mysql_real_escape_string($_GET['fhasta']) : '';
	    $tipo_movimiento = isset($_GET['tipo_movimiento']) ? mysql_real_escape_string($_GET['tipo_movimiento']) : '';
	    $motivo_descripcion = isset($_GET['motivo_descripcion']) ? mysql_real_escape_string($_GET['motivo_descripcion']) : '';
	    $aprobados = isset($_GET['aprobados']) ? mysql_real_escape_string($_GET['aprobados']) : '';
	    // Nuevo parámetro para filtro específico por parentesco
    	$filtroParentesco = isset($_GET['filtro_parentesco']) ? mysql_real_escape_string($_GET['filtro_parentesco']) : '';

	    // Verificar si ya se generó el padron
	    $valida = "SELECT COUNT(*) FROM ospedyb_padron.tmp_afiliados_nov_padronsss_insertar WHERE id_padron_sss = $id_lote";
	    
	    $rs = mysql_query($valida);
	    $row = mysql_fetch_row($rs);
	    
	    if($row[0] == 0) {
	        // Generar los datos si no existen
	        $query = "CALL $base_padron.Padron_sss_comparativo_lst_control($id_lote)";
	        mysql_query($query);
	    }

	    

	    // Filtro específico por parentesco (si existe)
	    if ($filtro_parentesco) {
		    $parentesco = mysql_real_escape_string($filtro_parentesco);
		    $where .= " AND parentesco = '$parentesco'";
		}
		if ($filtro_aprobado) {
		    $aprobado = mysql_real_escape_string($filtro_aprobado);
		    $where .= " AND exportar = '$aprobado'";
		}
	    
	    #echo $base_query; exit();
	    
	    if(!empty($aprobados)) {
	        if($aprobados == 'aprobados') {
	            // $where .= " AND exportar = 'SI'";
	        } else if($aprobados == 'no_aprobados') {
	            // $where .= " AND exportar = 'NO'";
	        }
	    }
	    
	    // Búsqueda general (search box de DataTables)
	    if(!empty($search)) {
	        $where .= " AND (cuil LIKE '%$search%' 
	                      OR ayn LIKE '%$search%' 
	                      OR nd LIKE '%$search%'
	                      OR parentesco LIKE '%$search%'
	                      OR desreguladora LIKE '%$search%')";
	    }

	    // Total sin filtros
	    $sqlTotal = "SELECT COUNT(*) $base_query";
	    $resTotal = mysql_query($sqlTotal);
	    $rowTotal = mysql_fetch_row($resTotal);
	    $recordsTotal = $rowTotal[0];
	    #echo $recordsTotal; exit();

	    // Total con filtros
	    $sqlFiltered = "SELECT COUNT(*) $base_query $where"; #echo $sqlFiltered; exit();
	    $resFiltered = mysql_query($sqlFiltered);
	    $rowFiltered = mysql_fetch_row($resFiltered);
	    $recordsFiltered = $rowFiltered[0];	
	    #echo $recordsFiltered; exit();
	    
	    // Consulta principal con paginación (igual que antes)
	    $sql = "SELECT * $base_query $where 
	            ORDER BY id DESC 
	            LIMIT $start, $length";

	    $res = mysql_query($sql);
	    $data = array();

	    while ($row = mysql_fetch_assoc($res)) {
	        $row['exportar'] = ($row['exportar'] == "SI") ? "SI" : "NO";
	        $data[] = $row;
	    }

	    // Nueva consulta para obtener agrupamientos por parentesco
	    $sqlAgrupamientos = "SELECT parentesco, COUNT(*) as total 
	                         $base_query $where
	                         GROUP BY parentesco
	                         ORDER BY total DESC";
	    
	    $resAgrupamientos = mysql_query($sqlAgrupamientos);
	    $agrupamientos = array();
	    
	    while ($row = mysql_fetch_assoc($resAgrupamientos)) {
	        $agrupamientos[] = $row;
	    }

	    // Respuesta JSON ampliada
	    echo json_encode(array(
	        "draw" => $draw,
	        "recordsTotal" => $recordsTotal,
	        "recordsFiltered" => $recordsFiltered,
	        "data" => $data,
	        "agrupamientos" => array(
	            "parentesco" => $agrupamientos
	            // Puedes añadir más agrupamientos aquí
	        )
	    ));
	    break;
	
	case 'actualizar_exportar':
		// code...
		$query = "UPDATE $base_padron.tmp_afiliados_nov_padronsss_insertar SET exportar='$exportar' WHERE id=$id " ;
		mysql_query($query);


		break;

	case 'aprobar_seleccionados':
	    // Usar MySQLi para prepared statements
	    
	    
	    $registros = json_decode($_GET['registros'], true);
	    
	    
	    foreach ($registros as $registro) {
	        $exportar = $registro['exportar'];
	        $id = $registro['id'];
	        
	        $query = "UPDATE ospedyb_padron.tmp_afiliados_nov_padronsss_insertar 
	                             SET exportar = '$exportar'
	                                 
	                             WHERE id = $id ";
	        mysql_query($query) or die(mysql_error());
	        #echo $query;
	    }
	    #exit();
	    echo json_encode(['success' => true]);
	    break;

	case 'borrar_seleccionados':
	    // Usar MySQLi para prepared statements
	    
	    
	    $registros = json_decode($_GET['registros'], true);
	    
	    
	    foreach ($registros as $registro) {
	        $exportar = $registro['exportar'];
	        $id = $registro['id'];
	        
	        $query = "DELETE FROM ospedyb_padron.tmp_afiliados_nov_padronsss_insertar WHERE id = $id ";
	        mysql_query($query) or die(mysql_error());
	        #echo $query;
	    }
	    #exit();
	    echo json_encode(['success' => true]);
	    break;

	case 'insertarPresentacion':
		// code...
		$query = "UPDATE $base_padron.tmp_afiliados_nov_padronsss_insertar 
					SET presentado='SI'
					WHERE id_padron_sss=$id_lote 
						AND exportar='SI'";

		$query = "INSERT INTO $base_historicos.`novedades_exportables`(id_lote,id_persona,id_motivo,tipo_mov,id_usuario)
					SELECT $base_historicos.`get_id_presentacion_novedades_activa`(),id_persona,1,'A',$id_usuario
					FROM $base_padron.tmp_afiliados_nov_padronsss_insertar 
					WHERE id_padron_sss=$id_lote 
						AND exportar='SI'
						AND id_persona NOT IN ( SELECT id_persona FROM $base_historicos.`novedades_exportables` WHERE id_lote=ospedyb_historicos.`get_id_presentacion_novedades_activa`() )";

		mysql_query($query) or die(mysql_error().$query);
		echo json_encode(['success' => true]);
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