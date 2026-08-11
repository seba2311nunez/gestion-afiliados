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
	case 'TraerProcedencia':

		if($id_desreguladora=='todas'){
			$capita_where = '';
		}else{
			$capita_where = 'AND ars.id_desreguladora='.$id_desreguladora;
		}
		
		$sql="
			SELECT IF(pro.procedencia IS NULL,'Desconocida',REPLACE(pro.procedencia,'OBRA SOCIAL','O.S.')) AS procedencia,
				IF(pro.codigo IS NULL,'-',pro.codigo) AS rnos,
				COUNT(*) AS cantidad
			FROM $base.tmp_bajas_rg_final ars
			JOIN $base_padron.desreguladoras d ON d.id=ars.id_desreguladora
			LEFT JOIN $base_padron.procedencia pro ON pro.codigo=ars.os_origen
			WHERE ars.fecha_vigencia BETWEEN '$procedencia_desde' AND '$procedencia_hasta' $capita_where
			GROUP BY pro.procedencia
			ORDER BY COUNT(*) DESC
					";
					
		$rs=mysql_query($sql) or die($sql);
		
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
			$rnos_where='AND pro.codigo IS NULL';
		}else if ($rnos=="x"){
			$rnos_where="";
		}
		else{
			$rnos_where='AND pro.codigo='.$rnos;
		}

		if($id_desreguladora=='todas'){
			$capita_where = '';
		}else{
			$capita_where = 'AND ars.id_desreguladora='.$id_desreguladora;
		}
		
		$sql="SELECT ayn,cuil_titular,os_origen,d.convenio
			FROM $base.tmp_bajas_rg_final ars
			JOIN $base_padron.desreguladoras d ON d.id=ars.id_desreguladora
			LEFT JOIN $base_padron.procedencia pro ON pro.codigo=ars.os_origen
			
			WHERE ars.fecha_vigencia BETWEEN '$procedencia_desde' AND '$procedencia_hasta' $rnos_where $capita_where ";
		//echo $sql;
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
						'os_origen' => $row['os_origen'],
						'capita' => $row['convenio']
					);
			}
		}
		echo json_encode($json);	
		break;
	case 'valida_archivo_a_procesar':
	
		$sql="SELECT * FROM $bd.lotes WHERE descripcion='$periodo' AND proceso='bajas_rg_sss'";
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
					$sq=insertar($konta,$input,$periodo,$periodo_formateado);
					//$json[]=array('SQL' =>$sq);
					
				}
				
			}
				
			/*
			$contador="SELECT COUNT(*) as contador FROM $bd.bajas_rg_sss WHERE periodo_recibido='$periodo'";
			$result = mysql_query($contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];*/
			$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);
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
	case 'TraerDesreguladoras':
		$sql="
			SELECT DISTINCT af.id_desreguladora,d.convenio
			FROM $bd.bajas_rg_sss ars
			JOIN $base_padron.persona p ON p.cuil=ars.cuil_titular
			JOIN $base_padron.afiliados af ON af.id_persona=p.id
			JOIN $base_padron.desreguladoras d ON d.id=af.id_desreguladora
			LEFT JOIN $base_padron.procedencia pro ON pro.codigo=ars.os_elegida
			JOIN $bd.lotes l ON l.id=ars.id_lote;
		";
		$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		
		$json = array();

		if(mysql_num_rows($rs)==0){

			$json[] = array(
						'error' => "No hay resultados"
	      	);

		}else{
			while($row=mysql_fetch_assoc($rs)){
			
				$json[] = array(
					'id_desreguladora' => $row['id_desreguladora'],	
					'desreguladora' => $row['convenio']
				);
			}
		}
		echo json_encode($json);	
		break;


}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){

	$contador="SELECT COUNT(*) as contador FROM ".N_BASE_HISTORICOS.".bajas_rg_sss WHERE periodo_recibido='$periodo'";
	$result = mysql_query($contador);
	$row = mysql_fetch_assoc($result);
	$konta = $row['contador'];

	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','bajas_rg_sss',$id_usuario,'bajas_rg_sss_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".bajas_rg_sss SET id_lote=$id_lote WHERE id_lote=periodo_recibido") or die(mysql_error());

	mysql_query("DELETE
					FROM ".N_BASE_HISTORICOS.".bajas_rg_sss 
					WHERE id_lote=$id_lote
						AND os_origen=0") or die(mysql_error());


	
	return $id_lote;
}

function insertar($konta,$input,$periodo,$periodo_formateado){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($nro_formulario,$os_elegida,$cuil_titular,$ayn,
						$telefono_contacto,$telefono_laboral,$calle,$numero,$piso,$departamento,$codigo_postal,$localidad,$provincia,
						$cuit_empleador,$razon_social,$os_origen,$fecha_eleccion,$periodo_vigencia,$fecha_confirmacion,$correo_electronico)=explode("|",$input);
	if($os_elegida=='' || $os_elegida=='\n'){

	}else{
	
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".bajas_rg_sss (nro_formulario,os_elegida,cuil_titular,ayn,
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
	mysql_query($sql) or die(mysql_error().$sql);
	
	
	}
	return $sql;
	
}

//aprobacion_afiliados_importacion
function insertar_en_aai($id_lote,$id_user){

	// -- Este Proceso toma la informacion completa de este lote y verifica si todos existen en el padron (Titulares/Familiares)
	// -- si no existen los pasa a la tabla de aprobacion_afiliados_importacion y les genera el estado inicial en la tabla de estados.
	$query="CALL ".N_BASE_PADRON.".aai_insertar_altas_rg($id_lote, $id_user);";
	mysql_query($query) or die(mysql_error()."Error en el CALL aai_insertar_altas_rg ".$query);

}

?>