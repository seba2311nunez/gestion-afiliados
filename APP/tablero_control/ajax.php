<?php 
include("../../Config/Conectar.inc");
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set("allow_url_fopen", 1);

switch ($parametro) {
	case 'listar':
		$query = "SELECT tc.*,(SELECT COALESCE(SUM((altas_rg+altas_mt-bajas_rg-bajas_mt-bajas_b15-bajas_fallecio)),0) AS coef
					FROM $base_tmp.tablero_control t
					WHERE t.id<=tc.id-1) AS coef
			FROM $base_tmp.tablero_control tc
		";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'periodo' => $row['periodo'],		
					'altas_rg' => numero($row['altas_rg']),
					'bajas_rg' => numero($row['bajas_rg']),
					'altas_mt' => numero($row['altas_mt']),
					'bajas_mt' => numero($row['bajas_mt']),
					'bajas_b15' => numero($row['bajas_b15']),
					'desempleo' => numero($row['desempleo']),
					'fallecidos' => numero($row['bajas_fallecio']),
					'jubilados' => numero($row['bajas_jubilacion']),
					'ddjj' => numero($row['ddjj']),
					'aportes' => numero($row['aportes']),
					'coef' => numero($row['coef'])
		      );
		}

		echo json_encode($json);
		break;

	case 'descargar':
		//echo $tipo;exit();
		//$grupo = $_GET['grupo'];


		switch ($grupo){
			case '1':
				$json = array();
				if($tipo == "Altas_RG"){$stored = "tablero_altas_rg()";$tabla = "tmp_altas_rg_final";$os_externa_title = "OS origen";}
				if($tipo == "Altas_MT"){$stored = "tablero_altas_mt()";$tabla = "tmp_altas_mt_final";$os_externa_title = "OS origen";}
				if($tipo == "Bajas_RG"){$stored = "tablero_bajas_rg()";$tabla = "tmp_bajas_rg_final";$os_externa_title = "OS destino";}
				if($tipo == "Bajas_MT"){$stored = "tablero_bajas_mt()";$tabla = "tmp_bajas_mt_final";$os_externa_title = "OS destino";}

				$sql_stored = "CALL $base.$stored";
				
				//echo $sql_stored;exit();
				$rs_call = mysql_query($sql_stored) or die(mysql_error()."<br>".$sql_stored);
				switch ($tipo_descarga) {

					case 'cantidad_por_desreguladora':
						
						$sql="SELECT d.convenio AS desreguladora,COUNT(*) AS cantidad
							FROM $base.$tabla arf
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id
							WHERE fecha_vigencia=CONCAT('$periodo','-01')
							GROUP BY 1 ORDER BY 2 DESC
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);
						
						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
						    $json[] = array(
									'Desreguladora' => $row['desreguladora'],
									'Cantidad' => $row['cantidad']
					      	);
						}
					
						break;
					case 'detalle':
						$sql="
							SELECT nro_formulario,cuil_titular AS cuil, ayn, 
										os_origen AS os_rnos, telefono,DATE_FORMAT(fecha_eleccion,'%d/%m/%Y') AS fecha_eleccion, 
										d.convenio AS desreguladora
							FROM $base.$tabla arf 
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id 
							WHERE fecha_vigencia=CONCAT('$periodo','-01') 
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);

						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
								$json[] = array(
									'Nro Formulario' => $row['nro_formulario'],
									'CUIL' => $row['cuil'],
									'Apellido y Nombre' => $row['ayn'],
									$os_externa_title => $row['os_rnos'],
									'Telefono' => $row['telefono'],
									'Fecha Eleccion' => $row['fecha_eleccion'],
									'Desreguladora' => $row['desreguladora']
								);
						}
						break;
				}
    			echo json_encode($json);
				break;
			case '2'://Bajas B15
				$json = array();
				if($tipo == "Bajas_B15"){$stored = "tablero_bajas_b15()";$tabla = "tmp_bajas_b15";$os_externa_title = "OS destino";}
				$sql_stored = "CALL $base.$stored";
				$rs_call = mysql_query($sql_stored) or die(mysql_error()."<br>".$sql_stored);
				switch ($tipo_descarga) {

					case 'cantidad_por_desreguladora':
						
						$sql="SELECT d.convenio AS desreguladora,COUNT(*) AS cantidad
							FROM $base.$tabla arf
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id
							WHERE fecha_vigencia=CONCAT('$periodo','-01')
							GROUP BY 1 ORDER BY 2 DESC
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);
						
						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
						    $json[] = array(
									'Desreguladora' => $row['desreguladora'],
									'Cantidad' => $row['cantidad']
					      	);
						}
					
						break;
					case 'detalle':
						$sql="
							SELECT cuil_titular AS cuil, ayn,DATE_FORMAT(fecha_vigencia,'%d/%m/%Y') AS fecha_vigencia,d.convenio AS desreguladora
							FROM $base.$tabla arf 
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id 
							WHERE fecha_vigencia=CONCAT('$periodo','-01') 
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);

						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
								$json[] = array(
									'CUIL' => $row['cuil'],
									'Apellido y Nombre' => $row['ayn'],
									'Fecha Vigencia' => $row['fecha_vigencia'],
									'Desreguladora' => $row['desreguladora']
								);
						}
						break;
				}
				echo json_encode($json);
				break;
			case '3': //Fallecidos
				$json = array();
				if($tipo == "Fallecidos"){$stored = "tablero_fallecidos()";$tabla = "tmp_fallecidos";}
				if($tipo == "Jubilados"){$stored = "tablero_jubilados()";$tabla = "tmp_jubilados";}
				$sql_stored = "CALL $base.$stored";
				$rs_call = mysql_query($sql_stored) or die(mysql_error()."<br>".$sql_stored);
				switch ($tipo_descarga) {

					case 'cantidad_por_desreguladora':
						
						$sql="SELECT d.convenio AS desreguladora,COUNT(*) AS cantidad
							FROM $base.$tabla arf
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id
							WHERE fecha_vigencia LIKE CONCAT('$periodo','%')
							GROUP BY 1 ORDER BY 2 DESC
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);
						
						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
						    $json[] = array(
									'Desreguladora' => $row['desreguladora'],
									'Cantidad' => $row['cantidad']
					      	);
						}
					
						break;
					case 'detalle':
						$sql="
							SELECT cuil_titular,cuil, ayn,DATE_FORMAT(fecha_vigencia,'%d/%m/%Y') AS fecha_vigencia,d.convenio AS desreguladora
							FROM $base.$tabla arf 
							JOIN $base_padron.desreguladoras d ON arf.id_desreguladora=d.id 
							WHERE fecha_vigencia LIKE CONCAT('$periodo','%') 
						";
						//echo $sql;exit();
						$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

						check_result($result);

						while ($row = mysql_fetch_assoc($result)) {
								#IMPORTANTE - Si los titulos son cambiados en el json enviado, deben ser cambiados tambien en los dos arrays de comparacion de control_xls
								$json[] = array(
									'CUIL Titular' => $row['cuil_titular'],
									'CUIL' => $row['cuil'],
									'Apellido y Nombre' => $row['ayn'],
									'Fecha Vigencia' => $row['fecha_vigencia'],
									'Desreguladora' => $row['desreguladora']
								);
						}
						break;
				}
				echo json_encode($json);
				break;
		}
		break;
		
}

function numero($var){
	if (!$var) {
		return 0;
	}else{
		return $var;
	}
}
function check_result($result){//Cree esta funcion porque hay un problema en la ejeccucion del stored de BAJAS MT

	// echo "<pre>";
	// print_r($result);
	// echo "</pre>";exit();
	$json_error = array();
	$count = mysql_num_rows($result);
	if($count < 1 ){
		$json_error[] = array("Error" => "Comuniquese con administracion"); echo json_encode($json_error);exit();
	}

}
?>