<?php  
include ("../Config/Conectar.inc");
if ( $_SESSION["usu"] == "" ){ 
	echo "<h1>Su sesion caduco, por favor vuelva a loguearse.</h1></br>";  exit();
}else{
	$usuario=$_SESSION["usu"];
	$id_user=$_SESSION["iduser"];	
}

switch ($parametro) {

	case 'listados_padron':
		$json = array();
		$sql="SELECT * FROM $base.listados_padron order by id_orden asc";
		$rs = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = array(
				'id'=>$row['id'],
				'id_orden'=>$row['id_orden'],
				'url'=>$row['url'],
				'nombre'=>$row['nombre']
			);
		}
		echo json_encode($json);
		break;
	case 'abi_x_periodo_tipo':
		if($convenio != "TOTALES"){
			$join_conv = " JOIN $base_padron.`desreguladoras` d ON d.id=t.`id_desreguladora`";
			$where_conv = " AND desreguladora='$convenio' ";
		}
		$sql = "SELECT t.periodo,CONCAT(t.periodo,'-01') AS fecha_vigencia,
							COALESCE(t2.alta_rg,0) AS alta_rg,
							COALESCE(t2.baja_rg,0) AS baja_rg,
							COALESCE(t2.alta_mt,0) AS alta_mt,
							COALESCE(t2.baja_mt,0) AS baja_mt,
							COALESCE(t2.altas_total,0) AS altas_total,
							COALESCE(t2.bajas_total,0) AS bajas_total,
							COALESCE(t2.diff,0) AS diff

						FROM $base.tablero_control t 
						LEFT JOIN (	SELECT MID(fecha_vigencia,1,7) AS periodo,
											fecha_vigencia,
											SUM(IF(tipo='alta_rg',1,0)) AS alta_rg,	
											SUM(IF(tipo='alta_mt',1,0)) AS alta_mt,	
											(SUM(IF(tipo='alta_rg',1,0))+SUM(IF(tipo='alta_mt',1,0))) AS altas_total,
											
											SUM(IF(tipo='baja_rg',1,0)) AS baja_rg,
											SUM(IF(tipo='baja_mt',1,0)) AS baja_mt,
											(SUM(IF(tipo='baja_rg',1,0))+SUM(IF(tipo='baja_mt',1,0))) AS bajas_total,
											(SUM(IF(tipo='alta_rg',1,0))+SUM(IF(tipo='alta_mt',1,0)))-(SUM(IF(tipo='baja_rg',1,0))+SUM(IF(tipo='baja_mt',1,0))) AS diff

										FROM (
											SELECT DISTINCT * 
											FROM $base.traspasos_control_todos t
											$join_conv
											WHERE 1=1 
											AND t.nro_formulario!='0'
											$where_conv
										) sub 
										WHERE 1=1
										GROUP BY 1 ) t2 ON t.periodo=t2.periodo COLLATE utf8_general_ci
						ORDER BY 1 DESC ";

		$sql = "SELECT t.fecha_vigencia AS periodo,t.fecha_vigencia,
						COALESCE(SUM(IF(t.tipo='alta_rg',1,0)),0) AS alta_rg,
						COALESCE(SUM(IF(t.tipo='baja_rg',1,0)),0) AS baja_rg,
						COALESCE(SUM(IF(t.tipo='alta_mt',1,0)),0) AS alta_mt,
						COALESCE(SUM(IF(t.tipo='baja_mt',1,0)),0) AS baja_mt,
						COALESCE(SUM(IF(t.tipo='alta_rg',1,0)),0)+COALESCE(SUM(IF(t.tipo='alta_mt',1,0)),0) AS altas_total,
						COALESCE(SUM(IF(t.tipo='baja_rg',1,0)),0)+COALESCE(SUM(IF(t.tipo='baja_mt',1,0)),0) AS bajas_total,
						COUNT(*) AS q
					FROM $base.traspasos_resumen_x_periodo t 
					WHERE 1=1
						AND MID(t.fecha_vigencia,1,7) <= DATE_FORMAT(ADDDATE(CURDATE(),INTERVAL +1 MONTH),'%Y-%m')
						$where_conv
					GROUP BY 1 
					ORDER BY 1 DESC ";

		$result = mysql_query($sql);
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'abi_x_periodo_tipo_gerenciadora':
		$sql = "SELECT t2.convenio_real,
							COALESCE(t2.alta_rg,0) AS alta_rg,
							COALESCE(t2.baja_rg,0) AS baja_rg,
							COALESCE(t2.alta_mt,0) AS alta_mt,
							COALESCE(t2.baja_mt,0) AS baja_mt,
							COALESCE(t2.altas_total,0) AS altas_total,
							COALESCE(t2.bajas_total,0) AS bajas_total,
							COALESCE(t2.diff,0) AS diff

						FROM (SELECT convenio_real,
											fecha_vigencia,
											SUM(IF(tipo='alta_rg',1,0)) AS alta_rg,	
											SUM(IF(tipo='alta_mt',1,0)) AS alta_mt,	
											(SUM(IF(tipo='alta_rg',1,0))+SUM(IF(tipo='alta_mt',1,0))) AS altas_total,
											
											SUM(IF(tipo='baja_rg',1,0)) AS baja_rg,
											SUM(IF(tipo='baja_mt',1,0)) AS baja_mt,
											(SUM(IF(tipo='baja_rg',1,0))+SUM(IF(tipo='baja_mt',1,0))) AS bajas_total,
											(SUM(IF(tipo='alta_rg',1,0))+SUM(IF(tipo='alta_mt',1,0)))-(SUM(IF(tipo='baja_rg',1,0))+SUM(IF(tipo='baja_mt',1,0))) AS diff

										FROM (
											SELECT DISTINCT * 
											FROM $base_tmp.traspasos_control_todos t
											JOIN $base_padron.desreguladoras d on d.id=t.id_desreguladora
											WHERE 1=1 
											AND t.nro_formulario!='0'
											AND t.fecha_vigencia LIKE CONCAT('$periodo','%')											
										) sub 
										WHERE 1=1
										GROUP BY 1 ) t2
						ORDER BY 1 DESC ";
		$result = mysql_query($sql) or die(mysql_error());
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'abi_xls_detalle_capita':
		// code...
		$filename = $tipo."_".$fv."_".$capita.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);

		if($capita == 'TOTAL'){
			$where_capita = "";
		}else{
			$where_capita = "AND desreguladora='$capita'";
		}
		$sql = "
			SELECT DISTINCT t.* ,CONCAT(p.apellido,' ',p.nombre) AS ayn
			FROM $base.traspasos_resumen_x_periodo t
			JOIN $base_padron.persona p ON t.`cuil_titular`=p.cuil 
			WHERE 1=1 
			AND t.nro_formulario!='0'
			AND t.tipo='$tipo'
			AND t.fecha_vigencia LIKE CONCAT('$fv','%')	
			$where_capita
	 	";
		//echo $sql; exit();
		$table = "<h3>Listado de $tipo - Periodo $fv | Capita $capita</h3>
					<table border=1>
						<tr>
							<th>Formulario</th>
							<th>CUIL</th>
							<th>Ayn</th>
							<th>Os traspaso</th>
						</tr>
		";
		$result = mysql_query($sql) or die(mysql_error()."ERROR | $sql");
		while($d=mysql_fetch_object($result)){
			$table .= "<tr>
							<td>$d->nro_formulario</td>
							<td>$d->cuil_titular</td>
							<td>$d->ayn</td>
							<td>$d->os_origen</td>
						</tr>";
		}
		echo $table;
		exit();
		break;
	case 'abi_capita_x_periodo_tipo':
		// code...
		$sql = "SELECT desreguladora as capita,
											COUNT(*) as total
										FROM (
											SELECT DISTINCT * 
											FROM $base.traspasos_resumen_x_periodo  t
											
											WHERE 1=1 
											AND t.nro_formulario!='0'
											AND t.tipo='$tipo'
											AND t.fecha_vigencia LIKE CONCAT('$periodo','%')											
										) sub
										GROUP BY 1  ";
					
		$result = mysql_query($sql) or die (mysql_error());
		
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = $row;
		}
		echo json_encode($json);
		break;

	//Comentado ya que con la version actual estos 2 case generaban error 500. - Alan
	/*
	// agregado para un listado de traspasos en proceso
	case 'abi_x_periodo_tipo_filtrado':
	    $convenio = $_GET['convenio'] ?? 'TODAS';
	    $anio = $_GET['anio'] ?? 'TODOS';
	    $mes = $_GET['mes'] ?? 'TODOS';
	    
	    // Construir la consulta SQL con filtros
	    $sql = "SELECT periodo, 
	                   SUM(alta_rg) as alta_rg,
	                   SUM(alta_mt) as alta_mt,
	                   SUM(baja_rg) as baja_rg,
	                   SUM(baja_mt) as baja_mt,
	                   SUM(alta_rg + alta_mt) as altas_total,
	                   SUM(baja_rg + baja_mt) as bajas_total
	            FROM $base.traspasos_resumen_x_periodo
	            WHERE 1=1";
	    
	    if($convenio != 'TODAS') {
	        $sql .= " AND convenio_real = '" . mysqli_real_escape_string($conn, $convenio) . "'";
	    }
	    
	    if($anio != 'TODOS') {
	        $sql .= " AND YEAR(periodo) = " . intval($anio);
	    }
	    
	    if($mes != 'TODOS') {
	        $sql .= " AND MONTH(periodo) = " . intval($mes);
	    }
	    
	    $sql .= " GROUP BY periodo ORDER BY periodo DESC";
	    
	    $result = mysqli_query($conn, $sql);
	    $data = array();
	    
	    while($row = mysqli_fetch_assoc($result)) {
	        $data[] = $row;
	    }
	    
	    echo json_encode($data);
	    break;	
	case 'abi_xls_completo':
	    $convenio = $_GET['convenio'] ?? 'TODAS';
	    $anio = $_GET['anio'] ?? 'TODOS';
	    $mes = $_GET['mes'] ?? 'TODOS';
	    
	    // Encabezados para Excel
	    header("Content-Type: application/vnd.ms-excel");
	    header("Content-Disposition: attachment; filename=traspasos_" . date('Y-m-d') . ".xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    
	    // Construir consulta (similar a la anterior)
	    $sql = "SELECT periodo as 'Periodo',
	                   SUM(alta_rg) as 'Altas RG',
	                   SUM(alta_mt) as 'Altas MT',
	                   SUM(baja_rg) as 'Bajas RG',
	                   SUM(baja_mt) as 'Bajas MT',
	                   SUM(alta_rg + alta_mt) as 'Total Altas',
	                   SUM(baja_rg + baja_mt) as 'Total Bajas',
	                   (SUM(alta_rg + alta_mt) - SUM(baja_rg + baja_mt)) as 'Saldo'
	            FROM tu_tabla_principal
	            WHERE 1=1";
	    
	    if($convenio != 'TODAS') {
	        $sql .= " AND convenio_real = '" . mysqli_real_escape_string($conn, $convenio) . "'";
	    }
	    
	    if($anio != 'TODOS') {
	        $sql .= " AND YEAR(periodo) = " . intval($anio);
	    }
	    
	    if($mes != 'TODOS') {
	        $sql .= " AND MONTH(periodo) = " . intval($mes);
	    }
	    
	    $sql .= " GROUP BY periodo ORDER BY periodo DESC";
	    
	    $result = mysqli_query($conn, $sql);
	    
	    // Generar Excel simple
	    echo "<table border='1'>";
	    echo "<tr><th colspan='8'>Reporte de Traspasos</th></tr>";
	    echo "<tr><th colspan='8'>Filtros aplicados: ";
	    
	    $filtros = [];
	    if($convenio != 'TODAS') $filtros[] = "Gerenciadora: $convenio";
	    if($anio != 'TODOS') $filtros[] = "Año: $anio";
	    if($mes != 'TODOS') $filtros[] = "Mes: " . nombreMes($mes);
	    
	    echo implode(" | ", $filtros);
	    echo "</th></tr>";
	    echo "<tr>
	            <th>Periodo</th>
	            <th>Altas RG</th>
	            <th>Altas MT</th>
	            <th>Total Altas</th>
	            <th>Bajas RG</th>
	            <th>Bajas MT</th>
	            <th>Total Bajas</th>
	            <th>Saldo</th>
	          </tr>";
	    
	    while($row = mysqli_fetch_assoc($result)) {
	        echo "<tr>";
	        foreach($row as $cell) {
	            echo "<td>" . $cell . "</td>";
	        }
	        echo "</tr>";
	    }
	    
	    echo "</table>";
	    break;
	*/
}
	
function nombreMes($mes) {
    $meses = array(
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
        '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
        '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
        '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    );

    if (isset($meses[$mes])) {
        return $meses[$mes];
    } else {
        return $mes;
    }
}
?>