<?php 
include(__DIR__.'/../../../Config/Conectar.inc');
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
					WHERE proceso='adhesiones_sss'
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
	
	case 'valida_archivo_a_procesar':
	
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='adhesiones_sss'";
		$cant = mysql_num_rows(mysql_query($sql));
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
					while ($input = fgets($gestor, 250)) {
						//echo "ok";
						$input= ereg_replace( "'", " ", $input );
						//echo $input;exit();
						
						if($input==""){
							exit();
						}
						else{
							$konta++;
							$sq=insertar($konta,$input,$periodo,$periodo_formateado);
							//$json[]=array('SQL' =>$sq);					
						}
					}
	
					$contador="SELECT COUNT(*) as contador FROM $base_historicos.adhesiones_sss WHERE id_lote=0";
					$result = mysql_query($contador);
					$row = mysql_fetch_assoc($result);
					$konta = $row['contador'];
					$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);

					echo $id_lote;

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
function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes (archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','adhesiones_sss',$id_usuario,'adhesiones_sss_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".adhesiones_sss SET id_lote=$id_lote WHERE id_lote=0") or die(mysql_error());

	mysql_query("CALL ".N_BASE_HISTORICOS.".`ADH_proceso_completo`($id_lote)");
	
	return $id_lote;
}
function insertar($konta,$input,$periodo,$periodo_formateado){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	
	list($cuil,$rnos,$periodo,$cuil_nuevo,
						$ayn,$calle,$numero,$piso,$departamento,$localidad,$codigo_postal,$provincia,$categoria_mtr,$categoria_aut)=explode("|",$input);
	
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".adhesiones_sss (id_lote,cuil_titular,rnos,periodo,cuil_nuevo,
						ayn,calle,numero,piso,departamento,localidad,codigo_postal,provincia,categoria_mtr,categoria_aut)
	VALUES	(0,MID('$cuil',1,11),'$rnos','$periodo','$cuil_nuevo',
						'$ayn','$calle','$numero','$piso','$departamento','$localidad','$codigo_postal','$provincia',LTRIM('$categoria_mtr'),LTRIM('$categoria_aut'))
	";
	mysql_query($sql) or die(mysql_error().$sql);
}
?>