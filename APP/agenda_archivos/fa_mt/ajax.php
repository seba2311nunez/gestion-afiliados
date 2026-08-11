<?php
include(__DIR__.'/../../../Config/Conectar.inc');
$id_usuario = $_SESSION["iduser"];
$id_usuario = 1 ;
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
					LIMIT 60
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
					WHERE proceso='fa_mt'

					ORDER BY descripcion desc
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

		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='fa_mt'";
		$cant = mysql_num_rows(mysql_query($sql));
		$rs=mysql_query($sql);
		$cant = mysql_num_rows($rs);
		//echo $cant;exit();
		if($cant=0){//EN ESTE CASO, NO HAY ARCHIVOS CARGADOS POR ESE PERIODO
			echo $cant;
			
		}
		else{//EN ESTE CASO, SI HAY UN ARCHIVO CARGADO
			$d = mysql_fetch_object($rs);
			echo $d->id;
			
		}
		exit();
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
			while ($input = fgets($gestor, 194)) {
				
				$input = ereg_replace( "'", " ", $input );
				
				$konta++;
				
				insertar($input,$periodo,$periodo_formateado,$base_historicos);

			}

			
			$contador="SELECT COUNT(*) as contador FROM $base_historicos.familiares_mt WHERE periodo='$periodo_formateado'";
			$result = mysql_query($contador) or die (mysql_error()." ".$contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];
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

	case 'crear_tabla':

		$query = "CALL FA_crear_tabla($id_lote)";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'crear_tabla_2':

		$query = "CALL FA_crear_tabla_2()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'traer_nombres_faltantes':

		$query = "CALL FA_traer_nombres_faltantes()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'cargar_nombres_faltantes':

		$query = "CALL FA_cargar_nombres_faltantes()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_preliminar':

		$query = "CALL FA_carga_preliminar()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_persona':

		$query = "CALL FA_carga_persona()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_afiliados':

		$query = "CALL FA_carga_afiliados()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_historico_afiliados':

		$query = "CALL FA_carga_historico_afiliados()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_altas_manuales':

		$query = "CALL FA_carga_altas_manuales()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

	case 'carga_caspno':

		$query = "CALL FA_carga_caspno()";
		
		mysql_query($query) or die(mysql_error().$query);

		echo "<h3>Termino</h3>";
		echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

	break;

}

function insertar($input,$periodo,$periodo_formateado,$base_historicos){
	//
	list($os,$cuit_titular,$td_fam,$nd_fam,$ayn,$nose1,$parentesco,$fec_incorporacion,$tipo)=explode("|",$input);
	//echo "$a<br>";
	//echo $os.$cuit_titular.$td_fam.$nd_fam.$ayn.$nose1.$parentesco.$fec_incorporacion.$tipo;
	


			$sql="INSERT INTO $base_historicos.familiares_mt (periodo,os,cuit_titular,td_fam,nd_fam,
											ayn,n,parentesco,fec_incorporacion,tipo,id_lote,archivo_recibido)

									VALUES ('$periodo_formateado','$os','$cuit_titular','$td_fam','$nd_fam',
												'$ayn','$nose1','$parentesco','$fec_incorporacion','$tipo','$periodo_formateado','$nombre_archivo')";

			mysql_query($sql) or die(mysql_error().$sql);


	
}
function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','fa_mt',$id_usuario,'fa_mt_$periodo')";
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();

	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";


	mysql_query("UPDATE ".N_BASE_HISTORICOS.".familiares_mt SET id_lote=$id_lote WHERE id_lote=periodo") or die(mysql_error());

	mysql_query("CALL ".N_BASE_HISTORICOS.".FA_proceso_completo($id_lote)") or die(mysql_error());

	return $id_lote;
}

?>