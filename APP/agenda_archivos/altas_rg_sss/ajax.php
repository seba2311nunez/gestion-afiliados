<?php 
include('../../../Config/Conectar.inc');
$id_usuario = $_SESSION["iduser"];
$root = $_SERVER['DOCUMENT_ROOT'];

if (isset($_FILES['archivo'])){
	$parametro = (isset($_POST['parametro'])) ? $_POST['parametro']: '';
	$nombre = (isset($_POST['nombre'])) ? $_POST['nombre']: '';
	$periodo = (isset($_POST['periodo'])) ? $_POST['periodo']: '';
	$archivo = (isset($_FILES['archivo'])) ? $_FILES['archivo']: '';
	$extension = (isset($_POST['extension'])) ? $_POST['extension']: '';
}
switch ($parametro) {
		
	case 'TraerPeriodos':
		
		$sql="SELECT primer_dia,periodo1 
					FROM prueba.periodos 
					WHERE periodo2<=prueba.periodo_actual() 
						OR DATE_ADD(CURDATE(), INTERVAL 1 MONTH) BETWEEN primer_dia AND ultimo_dia
						OR DATE_ADD(CURDATE(), INTERVAL 2 MONTH) BETWEEN primer_dia AND ultimo_dia
					ORDER BY id DESC  
					LIMIT 68";
					
		$rs=mysql_query($sql) or die(mysql_error());
		$json = array();
		if(mysql_num_rows($rs)==0){

			$error = mysql_error()."<br>".$sql;
			$json[] = array(
						'error' => $error
			      );
		}else{
			while($row=mysql_fetch_assoc($rs)){
			
			$json[] = array(
						'primer_dia' => $row['primer_dia'],										        		
						'periodo1' => $row['periodo1']
					);
			}
		}
		echo json_encode($json);
		
	break;
	
	case 'TraerProcesados':
	
		$sql="SELECT id, archivo, descripcion, cant_registros,
						DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fecha_carga
					FROM $base_historicos.lotes 
					WHERE proceso='alta_rg_sss'
					ORDER BY descripcion DESC
					";
					
		$rs=mysql_query($sql) or die(mysql_error());
		$json = array();
		if(mysql_num_rows($rs)==0){

			$error = mysql_error()."<br>".$sql;
			$json[] = array(
						'error' => $error
			      );
		}else{
			while($row=mysql_fetch_assoc($rs)){
			
			$json[] = array(
						'id' => $row['id'],	
						'titulo' => $row['archivo'],	
						'fecha_aPartir' => $row['descripcion'],										        		
						'q_registros' => $row['cant_registros'],
						'fecha_carga' => $row['fecha_carga']
					);
			}
		}
		echo json_encode($json);

		break;
	//Alan
	case 'TraerProcedencia':
		
		$sql="
			SELECT IF(pro.procedencia IS NULL,'Desconocida',REPLACE(pro.procedencia,'OBRA SOCIAL','O.S.')) AS procedencia,
				IF(pro.codigo IS NULL,'-',pro.codigo) AS rnos,
				COUNT(*) AS cantidad
			FROM $base_historicos.`altas_rg_sss` ars
			LEFT JOIN $base_padron.`procedencia` pro ON pro.codigo=ars.os_origen
			JOIN $base_historicos.lotes l ON l.id=ars.id_lote
			WHERE l.`descripcion` BETWEEN '$procedencia_desde' AND '$procedencia_hasta'
			GROUP BY pro.`procedencia`
			ORDER BY COUNT(*) DESC
					";
					
		$rs=mysql_query($sql) or die(mysql_error());
		
		$json = array();

		if(mysql_num_rows($rs)==0){

			$json[] = array(
						'error' => "No hay resultados"
	      	);

		}else{
			while($row=mysql_fetch_assoc($rs)){
			
			$json[] = array(
						'rnos' => $row['rnos'],	
						'procedencia' => $row['procedencia'],	
						'cantidad' => $row['cantidad']
					);
			}
		}
		echo json_encode($json);

		break;
	case 'TraerDetalles':

		if($rnos=="Desconocido"){
			$rnos_where='pro.codigo IS NULL';
		}
		else{
			$rnos_where='pro.codigo='.$rnos;
		}
		
		$sql="SELECT ayn,cuil_titular,localidad,provincia,os_origen
			FROM $base_historicos.`altas_rg_sss` ars
			LEFT JOIN $base_padron.`procedencia` pro ON pro.codigo=ars.os_origen
			JOIN $base_historicos.lotes l ON l.id=ars.id_lote
			WHERE l.`descripcion` BETWEEN '$procedencia_desde' AND '$procedencia_hasta' AND $rnos_where ";

		$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		
		$json = array();

		if(mysql_num_rows($rs)==0){

			$json[] = array(
						'error' => "No hay resultados"
	      	);

		}else{
			while($row=mysql_fetch_assoc($rs)){
			
			$json[] = array(
						'ayn' => $row['ayn'],	
						'cuil_titular' => $row['cuil_titular'],	
						'localidad' => $row['localidad'],
						'provincia' => $row['provincia'],
						'os_origen' => $row['os_origen'],
					);
			}
		}
		echo json_encode($json);	
		break;
	case 'valida_archivo_a_procesar':
	
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='alta_rg_sss'";
		$cant = mysql_num_rows(mysql_query($sql));
		/*
		if($cant=0){//EN ESTE CASO, NO HAY ARCHIVOS CARGADOS POR ESE PERIODO
			$var = 0;
		}
		else{//EN ESTE CASO, SI HAY UN ARCHIVO CARGADO
			$var = 1;
		}
		echo $var;
		*/
		echo $cant;
		break;
	
	case 'trabajar_archivo':
		$directorio_temporal = sys_get_temp_dir(); // Obtiene el directorio temporal del sistema

		if ($directorio_temporal) {
	    $nombre_archivo_temporal = tempnam($directorio_temporal, 'archivo_temporal_');
	    if ($nombre_archivo_temporal) {
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $nombre_archivo_temporal)) {
	        $gestor = fopen($nombre_archivo_temporal, "r");
			$konta=0 ;
			$periodo_formateado=substr(str_replace('-','',$periodo),0,6);
			while ($input = fgets($gestor, 350)) {
				
				$input= ereg_replace( "'", " ", $input );
				//echo $input;exit();
				
				if($input==""){
					
				}
				else{
					$konta++;
					$sq=insertar($konta,$input,$periodo,$periodo_formateado,$base_historicos);
					//$json[]=array('SQL' =>$sq);
					
				}

			}
			$contador="SELECT COUNT(*) as contador FROM $base_historicos.altas_rg_sss WHERE periodo_recibido='$periodo'";
			$result = mysql_query($contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];
			$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario,$base_historicos);

			$sql="CALL $base_historicos.ALTAS_RG_completo($id_lote)";
			mysql_query($sql);
			echo $id_lote;
			fclose($gestor); // No olvides cerrar el archivo después de su uso

	        // Finalmente, puedes eliminar el archivo temporal si ya no lo necesitas
	        unlink($nombre_archivo_temporal);
		} else {
			echo "Error moviendo el archivo al directorio temporal.";
		}
		} else {
			echo "No se pudo crear un archivo temporal.";
		}
		} else {
			echo "El servidor no tiene configurado un directorio temporal.";
		}
		break;

	case 'aai_insertar':
			
			insertar_en_aai($id_lote,$id_usuario);
			echo "<script>
						window.close();
			     	</script>";

			break;	


}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario,$base_historicos){
	$inserta="INSERT INTO $base_historicos.lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','alta_rg_sss',$id_usuario,'altas_rg_sss_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE $base_historicos.altas_rg_sss SET id_lote=$id_lote WHERE id_lote=periodo_recibido") or die(mysql_error());


	
	return $id_lote;
}

function insertar($konta,$input,$periodo,$periodo_formateado,$base_historicos){

	list($nro_formulario,$os_elegida,$cuil_titular,$ayn,
						$telefono_contacto,$telefono_laboral,$calle,$numero,$piso,$departamento,$codigo_postal,$localidad,$provincia,
						$cuit_empleador,$razon_social,$os_origen,$fecha_eleccion,$periodo_vigencia,$fecha_confirmacion,$correo_electronico)=explode("|",$input);
	if($os_elegida=='' || $os_elegida=='\n'){

	}else{
	
	
	$sql="
	INSERT INTO $base_historicos.altas_rg_sss (nro_formulario,os_elegida,cuil_titular,ayn,
						telefono_contacto,telefono_laboral,calle,numero,piso,departamento,codigo_postal,localidad,provincia,
						cuit_empleador,razon_social,os_origen,fecha_eleccion,periodo_vigencia,fecha_confirmacion,correo_electronico,fecha_super,id_lote,periodo_recibido)
	VALUES	('$nro_formulario','$os_elegida','$cuil_titular','$ayn',
				'$telefono_contacto','$telefono_laboral','$calle','$numero','$piso','$departamento','$codigo_postal','$localidad','$provincia',
				'$cuit_empleador','$razon_social','$os_origen','$fecha_eleccion','$periodo_vigencia','$fecha_confirmacion','$correo_electronico','$periodo_formateado','$periodo','$periodo')
	";
	/*
	$sql="('$nro_formulario','$os_elegida','$cuil_titular','$ayn',
				'$telefono_contacto','$telefono_laboral','$calle','$numero','$piso','$departamento','$codigo_postal','$localidad','$provincia',
				'$cuit_empleador','$razon_social','$os_origen','$fecha_eleccion','$periodo_vigencia','$fecha_confirmacion','$correo_electronico','$periodo_formateado','$periodo_formateado','$periodo')"
	;
	
	*/

	if($nro_formulario!=null and $nro_formulario!="" and $nro_formulario!=0){
		mysql_query($sql) or die(mysql_error().$sql);	
	}

	
	
	
	}
	return $sql;
	
}

//aprobacion_afiliados_importacion
function insertar_en_aai($id_lote,$id_user,$base_padron){

	// -- Este Proceso toma la informacion completa de este lote y verifica si todos existen en el padron (Titulares/Familiares)
	// -- si no existen los pasa a la tabla de aprobacion_afiliados_importacion y les genera el estado inicial en la tabla de estados.
	$query="CALL $base_padron.aai_insertar_altas_rg($id_lote, $id_user);";
	mysql_query($query) or die(mysql_error()."Error en el CALL aai_insertar_altas_rg ".$query);

}

?>