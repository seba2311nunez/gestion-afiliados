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
					LIMIT 18";
					
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
					WHERE proceso='efectores'
					ORDER BY id DESC
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
						'q_registros' => $row['cant_registros']
					);
			}

		}
		echo json_encode($json);
		break;
	case 'valida_archivo_a_procesar':
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='efectores'";
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

	case 'id_lote_para_quincena_2':
		$sql="SELECT id as id_lote FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='efectores'";
		#echo $sql;
		$result=mysql_query($sql);
		$d=mysql_fetch_object($result);
		
		echo $d->id_lote;
		break;
	case 'trabajar_archivo':
		$directorio_temporal = sys_get_temp_dir(); // Obtiene el directorio temporal del sistema

		if ($directorio_temporal) {
	    $nombre_archivo_temporal = tempnam($directorio_temporal, 'archivo_temporal_');
	    if ($nombre_archivo_temporal) {
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $nombre_archivo_temporal)) {
	        $gestor = fopen($nombre_archivo_temporal, "r");
			$konta=0 ;
			while ($input = fgets($gestor, 180)) {
				$input= ereg_replace( "'", " ", $input );
				if($input==""){
					exit();
				}
				else{
					$konta++;

					if($periodo >= '2021-03-01'){
						//echo "new";exit();
						$sq=insertar_new($konta,$input,$periodo,$periodo_formateado,$base_historicos);
					}else{
						//echo "old";exit();
						$sq=insertar_old($konta,$input,$periodo,$periodo_formateado,$base_historicos);
					}
					
				}
			}
			$contador="SELECT COUNT(*) as contador FROM $base_historicos.efectores_sociales WHERE id_lote=0";
			$result = mysql_query($contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];

			if($id_lote){
				mysql_query("UPDATE ".N_BASE_HISTORICOS.".efectores_sociales SET id_lote=$id_lote WHERE id_lote=0 or id_lote is null ") or die(mysql_error());
			}
			else{
				$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);		
			}
				
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

}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','efectores',$id_usuario,'efectores_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".efectores_sociales SET id_lote=$id_lote WHERE id_lote=0") or die(mysql_error());

	mysql_query("UPDATE ".N_BASE_HISTORICOS.".lotes l
					JOIN ( SELECT id_lote,COUNT(*) AS q_efec FROM ".N_BASE_HISTORICOS.".`efectores_sociales` GROUP BY 1 ) e ON l.id=e.id_lote 
					SET l.`cant_registros`=q_efec
					WHERE l.id=$id_lote ");
	
	mysql_query("CALL ".N_BASE_HISTORICOS.".EFECT_proceso_completo($id_lote)");
	return $id_lote;
}
function insertar_new($contador,$input,$periodo,$periodo_formateado,$base_historicos){
	list($cuil,$rnos,$ayn,$calle,$numero,$piso,$departamento,$localidad,$codigo_postal,$provincia)=explode("|",$input);
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".efectores_sociales (id_lote,cuil_titular,rnos,ayn,calle,numero,piso,departamento,localidad,codigo_postal,provincia)
	VALUES	(0,
		LTRIM('$cuil'),
		LTRIM('$rnos'),
		LTRIM('$ayn'),
		LTRIM('$calle'),
			LTRIM('$numero'),
			LTRIM('$piso'),
			LTRIM('$departamento'),
			LTRIM('$localidad'),
			LTRIM('$codigo_postal'),
			LTRIM('$provincia')
	)
	";
	
	mysql_query($sql) or die(mysql_error().$sql);
}
function insertar_old($contador,$input,$periodo,$periodo_formateado,$base_historicos){
	list($cuil,$rnos,$ayn,$calle,$numero,$piso,$departamento,$localidad,$codigo_postal,$codigo_provincia)=explode("|",$input);
	
	$sql="
	INSERT INTO $base_historicos.efectores_sociales (id_lote,cuil_titular,rnos,ayn,calle,numero,piso,departamento,localidad,codigo_postal,codigo_provincia)
	VALUES	(0,MID('$cuil',1,11),'$rnos','$ayn','$calle','$numero','$piso','$departamento','$localidad','$codigo_postal','$codigo_provincia')
	";
	
	mysql_query($sql) or die(mysql_error().$sql);
}