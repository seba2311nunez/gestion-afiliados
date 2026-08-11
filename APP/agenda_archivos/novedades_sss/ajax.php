<?php 
include(__DIR__.'/../../../Config/Conectar.inc');
$id_usuario = $_SESSION["id_user"];
$root = $_SERVER['DOCUMENT_ROOT'];

$id_usuario = 1 ;

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
					FROM ".N_BASE_HISTORICOS.".lotes 
					WHERE proceso='novedades_sss'
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
	
		$sql="SELECT * FROM ".N_BASE_HISTORICOS.".lotes WHERE descripcion='$periodo' AND proceso='novedades_sss'";
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
			while ($input = fgets($gestor, 350)) {
				
				$input= ereg_replace( "'", " ", $input );
				//echo $input;exit();
				
				if($input==""){
					
				}
				else{
					$konta++;
					$sq=insertar($konta,$input);
					//echo $sq;
					//$json[]=array('SQL' =>$sq)
				}
				
			}
			$id_lote = graba_lote_y_cierra($konta,$periodo,'',$id_usuario);

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

	case 'buscar_periodo':
		$sql="SELECT primer_dia FROM prueba.periodos WHERE periodo3='$periodo' LIMIT 1";
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
						'primer_dia' => $row['primer_dia']
					);
			}
		}
		echo json_encode($json);

		break;



}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','novedades_sss',$id_usuario,'novedades_sss_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".novedades_sss SET id_lote=$id_lote WHERE id_lote=0") or die(mysql_error());
	
	return $id_lote;
}

function insertar($konta,$input){
	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$tipo_documento,$nd,$ayn,$sexo,$estado_civil,$fn,$nacionalidad,$calle,$numero,$piso,$departamento,$localidad,$codigo_postal,$provincia,$tipo_domicilio,$telefono,$revista,$incapacidad,$tbt,$fecha_alta,$fecha_cierre,$codigo_movimiento,$detalle)=explode("|",$input);
	if($rnos=='' || $rnos=='\n'){

	}else{
		
		$sql="INSERT INTO ".N_BASE_HISTORICOS.".novedades_sss (
								id_lote,rnos,cuit,cuil_titular,
								parentesco,cuil,tipo_documento,nd,
								ayn,sexo,estado_civil,fn,
								nacionalidad,calle,numero,piso,
								departamento,localidad,codigo_postal,provincia,
								tipo_domicilio,telefono,revista,incapacidad,
								tbt,fecha_alta,fecha_cierre,codigo_movimiento,detalle
								
				)
						VALUES (0,'$rnos','$cuit','$cuil_titular','$parentesco','$cuil','$tipo_documento','$nd','$ayn','$sexo','$estado_civil','$fn','$nacionalidad','$calle','$numero','$piso','$departamento','$localidad','$codigo_postal','$provincia','$tipo_domicilio','$telefono','$revista','$incapacidad','$tbt','$fecha_alta','$fecha_cierre','$codigo_movimiento','$detalle')";
		
		if($rnos!=null and $rnos!="" and $rnos!=0){
			mysql_query($sql) or die(mysql_error().$sql);	
		}
	}
	return $sql;	
}
function multiexplode ($delimiters,$data) {
	$MakeReady = str_replace($delimiters[1], $delimiters[0], $data);
	$Return    = explode($delimiters[0], $MakeReady);
	$Return    = str_replace("'"," ",$Return);
	return  $Return;
}
?>