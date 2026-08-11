<?php
$parametro = isset($_REQUEST['parametro']) ? $_REQUEST['parametro'] : '';
include(__DIR__.'/../../../Config/Conectar.inc');

if(isset($_SESSION["iduser"])){
	$usu= $_SESSION['usuario'];
	$id_usuario = $_SESSION["iduser"];
}
else{
	echo "<h2>Su sesion caduco vuelva a loguearse</h2>
			<br>
			<ul>
				<li>
					<a href='http://".DOMINIO."'>Sistema ".strtoupper(INST_NAME)." - OBRA SOCIAL</a>					
				</li>
				<li>
					<a href='http://".DOMINIO."/extranet'>Sistema ".strtoupper(INST_NAME)." EXTRANET</a>
				</li>
			</ul>";
	 exit();
}

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
		
	
case 'TraerResumenAltasBajasRg':
	  $periodo = isset($_REQUEST['periodo']) ? $_REQUEST['periodo'] : '';

	  if($periodo != ''){
	    $where = "WHERE p.primer_dia = '$periodo'";
	  } else {
	    $where = "WHERE p.primer_dia BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 23 MONTH),'%Y-%m-01')
	                                     AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH),'%Y-%m-01')";
	  }

	  $sql = "SELECT
	            p.primer_dia AS periodo,

	            COALESCE(am.altas_mensuales,0) AS altas_mensuales,
	            COALESCE(ad.altas_diarias,0)   AS altas_diarias,
	            COALESCE(am.altas_mensuales,0) - COALESCE(ad.altas_diarias,0) AS dif_altas,

	            COALESCE(bm.bajas_mensuales,0) AS bajas_mensuales,
	            COALESCE(bd.bajas_diarias,0)   AS bajas_diarias,
	            COALESCE(bm.bajas_mensuales,0) - COALESCE(bd.bajas_diarias,0) AS dif_bajas

	          FROM prueba.periodos p

	          /* ALTAS MENSUALES */
	          LEFT JOIN (
	            SELECT periodo_recibido AS periodo, COUNT(*) AS altas_mensuales
	            FROM $base_historicos.altas_rg_sss
	            GROUP BY periodo_recibido
	          ) am ON am.periodo = p.primer_dia

	          /* ALTAS DIARIAS */
	          LEFT JOIN (
	            SELECT DATE_FORMAT(fecha_vigencia,'%Y-%m-01') AS periodo, COUNT(*) AS altas_diarias
	            FROM $base_historicos.opciones_rg_revision_altas
	            GROUP BY DATE_FORMAT(fecha_vigencia,'%Y-%m-01')
	          ) ad ON ad.periodo = p.primer_dia

	          /* BAJAS MENSUALES */
	          LEFT JOIN (
	            SELECT periodo_recibido AS periodo, COUNT(*) AS bajas_mensuales
	            FROM $base_historicos.bajas_rg_sss
	            GROUP BY periodo_recibido
	          ) bm ON bm.periodo = p.primer_dia

	          /* BAJAS DIARIAS */
	          LEFT JOIN (
	            SELECT DATE_FORMAT(fecha_vigencia,'%Y-%m-01') AS periodo, COUNT(*) AS bajas_diarias
	            FROM $base_historicos.opciones_rg_revision_bajas
	            GROUP BY DATE_FORMAT(fecha_vigencia,'%Y-%m-01')
	          ) bd ON bd.periodo = p.primer_dia

	          $where
	          ORDER BY p.primer_dia DESC";

	  $res = mysql_query($sql) or die(mysql_error()."<br>".$sql);
	  $out = [];
	  while($r = mysql_fetch_assoc($res)) $out[] = $r;
	  echo json_encode($out);
	  break;

case 'grabar_archivo_vacio':
		
		$id_lote = graba_lote_y_cierra(0,$fdesde,$fhasta,$id_usuario,$usu);

		echo $id_lote;
		break;


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
	
		$sql="SELECT id, 
					lote as fecha_desde,
					archivo as fecha_hasta, 
					cant_registros,
					DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fecha_carga,
					exp_padron,
					usuario
					FROM ".N_BASE_HISTORICOS.".lotes 
					WHERE proceso='opciones_rg_revision_bajas'
					ORDER BY lote DESC
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
						'q_registros' => $row['cant_registros'],
						'fecha_carga' => $row['fecha_carga'],
						'exp_padron' => $row['exp_padron'],
						'usuario' => $row['usuario']
					);
			}
		}
		echo json_encode($json);

		break;
	
	case 'valida_archivo_a_procesar':
	
		
		$sql = "SELECT COUNT(*) AS q
					FROM ".N_BASE_HISTORICOS.".lotes 
					WHERE proceso='opciones_rg_revision_bajas'
						AND ( '$fdesde' BETWEEN lote AND archivo OR '$fhasta' BETWEEN lote AND archivo ) ";
		$cant = mysql_fetch_object(mysql_query($sql))->q ;
		
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
						if($input==""){		
						}else{
							$konta++;
							$sq=insertar($konta,$input);
							//$json[]=array('SQL' =>$sq)
						}
					}
					$id_lote = graba_lote_y_cierra($konta,$fdesde,$fhasta,$id_usuario,$usu);
					mysql_query("CALL $base_historicos.`OPC_RG_RVS_completo`($id_lote)") or die(mysql_error()."<br><h3>ERROR </h3>");

					mysql_query("UPDATE $base_historicos.lotes SET exp_padron=1 WHERE id=$id_lote AND proceso='opciones_rg_revision' ");

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

function graba_lote_y_cierra($konta,$fdesde,$fhasta,$id_usuario,$usu){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(lote,archivo,descripcion,cant_registros,proceso,id_usuario,usuario,exp_padron)
				VALUES('$fdesde','$fhasta',CONCAT('$fdesde',' al ','$fhasta'),'$konta','opciones_rg_revision_bajas',$id_usuario,'$usu',1)"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".opciones_rg_revision_bajas SET id_lote=$id_lote WHERE id_lote IS NULL ") or die(mysql_error());

	mysql_query("UPDATE ".N_BASE_HISTORICOS.".opciones_rg_revision_bajas o
					JOIN ".N_BASE_HISTORICOS.".bajas_rg_sss b ON o.cuil_titular=b.cuil_titular AND o.nro_formulario=b.nro_formulario 
					SET o.fec_eleccion=CONCAT(MID(b.fecha_eleccion,7,4),'-',MID(b.fecha_eleccion,4,2),'-',MID(b.fecha_eleccion,1,2))
					WHERE o.id_lote = $id_lote ") or die(mysql_error());
	
	return $id_lote;
}

function insertar($konta,$input){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($nro_formulario,$cuil_titular,$ayn,$periodo_vigencia,$telefono_contacto,$email,
						$codigo_postal,$localidad,$provincia,$os_elegida)=explode("|",$input);
	if($os_elegida=='' || $os_elegida=='\n'){

	}else{
	
	$pv = explode("/", $periodo_vigencia);
	$pv_ok = $pv[2]."-".$pv[1]."-".$pv[0];
	
	$sql = "INSERT INTO ".N_BASE_HISTORICOS.".opciones_rg_revision_bajas(nro_formulario,cuil_titular,ayn,fecha_vigencia,telefono,email,
						codigo_postal,localidad,provincia,os_origen)
				VALUES ('$nro_formulario','$cuil_titular','$ayn','$pv_ok','$telefono_contacto','$email',
						'$codigo_postal','$localidad','$provincia','$os_elegida')";


	
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
	$query="CALL ".N_BASE_PADRON.".aai_insertar_altas_rg($id_lote, $id_user);";
	mysql_query($query) or die(mysql_error()."Error en el CALL aai_insertar_altas_rg ".$query);

}

?>