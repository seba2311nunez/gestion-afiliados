<?php 
include('../../../Config/Conectar.inc');

if(isset($_SESSION["iduser"])){
	$usu= $_SESSION['usuario'];
	$id_usuario = $_SESSION["iduser"];
}
else{
	echo "<h2>Su sesion caduco vuelva a loguearse</h2>
			<br>
			<ul>
				<li>
					<a href='http://".DOMINIO."'>Sistema ".INST_NAME." - OBRA SOCIAL</a>					
				</li>
				<li>
					<a href='http://".DOMINIO."/extranet'>Sistema ".INST_NAME." EXTRANET</a>
				</li>
			</ul>";
	 //header("Location: error.php");
	 exit();
}

//echo "$usu";

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
	
		$sql="SELECT l.id, 
					DATE_FORMAT(lote,'%d/%m/%Y') as fecha_desde, 
					DATE_FORMAT(archivo,'%d/%m/%Y') as fecha_hasta, 
					cant_registros,
					DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fecha_carga,
					u.usuario,
					exp_padron
			FROM $base_historicos.lotes l
			LEFT JOIN $base_usuarios.users u ON l.id_usuario=u.id 
			WHERE proceso='opciones_mt_revision_altas'
			GROUP BY 1 
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
						'fecha_desde' => $row['fecha_desde'],	
						'fecha_hasta' => $row['fecha_hasta'],										        		
						'cant_registros' => $row['cant_registros'],
						'fecha_carga' => $row['fecha_carga'],
						'usuario' => $row['usuario'],
						'exp_padron' => $row['exp_padron']
					);
			}
		}
		echo json_encode($json);

		break;
	
	case 'valida_archivo_a_procesar':
	
		
		$sql = "SELECT COUNT(*) AS q
					FROM $base_historicos.lotes 
					WHERE proceso='opciones_mt_revision_altas'
						AND ( '$fdesde' BETWEEN lote AND archivo OR '$fhasta' BETWEEN lote AND archivo ) ";
		$cant = mysql_fetch_object(mysql_query($sql))->q ;
		
		echo $cant;

		break;
	case 'grabar_archivo_vacio':
		
		$id_lote = graba_lote_y_cierra(0,$fdesde,$fhasta,$id_usuario);

		echo $id_lote;
		break;
	case 'trabajar_archivo':
		$directorio_temporal = sys_get_temp_dir(); // Obtiene el directorio temporal del sistema

		if ($directorio_temporal) {
	    $nombre_archivo_temporal = tempnam($directorio_temporal, 'archivo_temporal_');
	    if ($nombre_archivo_temporal) {
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $nombre_archivo_temporal)) {
	        $gestor = fopen($nombre_archivo_temporal, "r");
			$konta=0 ;
			while ($input = fgets($gestor, 350)) {
				$input= ereg_replace( "'", " ", $input );
				if($input==""){		
				}else{
					$konta++;
					$sq=insertar($konta,$input);
					//$json[]=array('SQL' =>$sq)
				}
			}
			$id_lote = graba_lote_y_cierra($konta,$fdesde,$fhasta,$id_usuario,$usu);
			mysql_query("CALL $base_historicos.`OPC_MT_RVS_completo`($id_lote)") or die("<br><h3>ERROR".mysql_error()."</h3>");

			mysql_query("UPDATE $base_historicos.lotes SET exp_padron=1 WHERE id=$id_lote AND proceso='opciones_mt_revision_altas' ");

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

	case 'exportacion_padron':
		#code
		mysql_query("CALL $base_historicos.`OPC_MT_RVS_completo`($id_lote)") or die("<br><h3>ERROR".mysql_error()."</h3>");

		mysql_query("UPDATE $base_historicos.lotes SET exp_padron=1 WHERE id=$id_lote AND proceso='opciones_mt_revision_altas' ");

		echo "ok";

		break;

	
	
}

function graba_lote_y_cierra($konta,$fdesde,$fhasta,$id_usuario){

	if($konta==0){

	}
	
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(lote,archivo,descripcion,cant_registros,proceso,id_usuario)
				VALUES('$fdesde','$fhasta',CONCAT('$fdesde',' al ','$fhasta'),'$konta','opciones_mt_revision_altas',$id_usuario)"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".opciones_mt_revision_altas SET id_lote=$id_lote WHERE id_lote IS NULL ") or die(mysql_error());
		
	return $id_lote;
}

function insertar($konta,$input){
	list($tipo_mt,$nro_formulario,$cuil_titular,$ayn,$periodo_vigencia,$tel_contacto,$email,
						$codigo_postal,$localidad,$provincia,$os_origen)=explode("|",$input);
	if($os_origen=='' || $os_origen=='\n'){

	}else{
	
	//$pv = explode("/", $periodo_vigencia);
	//$pv_ok = $pv[2]."-".$pv[1]."-".$pv[0];
	
	$sql = "INSERT INTO ".N_BASE_HISTORICOS.".opciones_mt_revision_altas(tipo_mt,nro_formulario,cuil_titular,ayn,fecha_vigencia,tel_contacto,email,
						codigo_postal,localidad,provincia,os_origen)
				VALUES ('$tipo_mt','$nro_formulario','$cuil_titular','$ayn',CONCAT(MID('$periodo_vigencia',1,4),'-',MID('$periodo_vigencia',5,2),'-01'),'$tel_contacto','$email',
						'$codigo_postal','$localidad','$provincia','$os_origen')";

	#Formateo aca la fecha_vigencia asi no tengo que hacerlo para la funcion,stored,proceso de insertacion

	
	if($nro_formulario!=null and $nro_formulario!="" and $nro_formulario!=0){
		mysql_query($sql) or die(mysql_error().$sql);	
	}
	
	
	}
	return $sql;
	
}

//aprobacion_afiliados_importacion
function insertar_en_aai($id_lote,$id_user){

	// -- Este Proceso toma la informacion completa de este lote y verifica si todos existen en el padron (Titulares/Familiares)
	// -- si no existen los pasa a la tabla de aprobacion_afiliados_importacion y les genera el estado inicial en la tabla de estados.
	$query="CALL ".N_BASE_HISTORICOS.".aai_insertar_altas_mt($id_lote, $id_user);";
	mysql_query($query) or die(mysql_error()."Error en el CALL aai_insertar_altas_mt ".$query);

}

?>