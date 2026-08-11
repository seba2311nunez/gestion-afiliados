<?php 
include(__DIR__.'/../../../Config/Conectar.inc');
$id_usuario = $_SESSION["id_user"];
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
					WHERE proceso='padron_sss'
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
						'q_registros' => $row['cant_registros']
					);
			}
		}
		echo json_encode($json);
		break;
	case 'valida_archivo_a_procesar':
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='padron_sss'";
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
		//echo "Ok";exit();
		$periodo_formateado=substr(str_replace('-','',$periodo),0,6);
		if($_SERVER['DOCUMENT_ROOT']=="/var/www/".DOMINIO){
			$archivo_send =  $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/padron_sss/archivos/";
		}
		else{
			$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/padron_sss/archivos/";
		}
		
		$nombre_archivo = $nombre.".".$extension;
		$nombre_txt= $nombre.".txt";
		$path = $archivo_send.$nombre_archivo; 
		$path_txt = $archivo_send.$nombre_txt; 
		//echo $path; exit();
		$copiado = move_uploaded_file($_FILES['archivo']['tmp_name'], $path);
		//echo $_FILES['archivo']['tmp_name']; exit();
		
		if($copiado==false){
						
			$id_lote='Error'.$path;
			
		}else{
			$zip = new ZipArchive;
			
			$res = $zip->open('./archivos/'.$nombre_archivo);
			if ($res === TRUE) {
			  $zip->extractTo('./archivos/');
			  $zip->close();
			} else {
			  echo  $txt.' no se pudo cargar :(';exit();
			} 
			/*
			$sql="LOAD DATA LOCAL INFILE  '$path_txt'
					INTO TABLE $base_historicos.padrones_sss
					FIELDS TERMINATED BY '|' 
					ENCLOSED BY ''
					LINES TERMINATED BY '\n'
					(rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,f_cierre_presentacion,verifica_cuil,cuil_informado,tbt_sijp,cuit_sijp,os_sijp,ult_per_sijp,os_opcion_vigente,periodo_desde_opcion)
					SET periodo = '$periodo_formateado' , id_lote = '$periodo_formateado'
					";
			mysql_query($sql) or die (mysql_error()." ".$sql);
			*/
			$gestor = fopen("$path_txt","r");
			$konta=0 ;

			/*Test*/
			//$json = array();

			while ($input = fgets($gestor, 287)) {
				
				$input= ereg_replace( "'", " ", $input );
				//echo $input;exit();
				
				if($input==""){
					
				}
				else{
					$konta++;
					$sq=insertar($input,$periodo_formateado);
					//$json[]=array('SQL' =>$sq);
					
				}
				
			}
			$contador="SELECT COUNT(*) as contador FROM $base_historicos.padrones_sss WHERE periodo='$periodo_formateado'";
			$result = mysql_query($contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];
			$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);
		}
		echo $id_lote;
		
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

	case 'comprobar_periodo':
		$sql="SELECT * FROM $base_historicos.lotes WHERE proceso='padron_sss' AND descripcion='$periodo'";

		$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$num_rows=mysql_num_rows($rs);

		$data=$num_rows;

		echo $num_rows;
		wp_die();
		break;

}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','padron_sss',$id_usuario,'padron_sss_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();

	$update="UPDATE ".N_BASE_HISTORICOS.".padrones_sss SET id_lote='$id_lote' WHERE id_lote=periodo";
	mysql_query($update);

	mysql_query("CALL ".N_BASE_HISTORICOS.".PDS_procesar($id_lote)") or die(mysql_error()."ERROR Importando al padron");

	return $id_lote;
}

function insertar($input,$periodo_formateado){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($rnos,$cuit,$cuil_titular,$parentesco,$cuil,$td,$nd,$ayn,$sexo,$est_civil,$fn,$nacionalidad,$calle,$numero,$piso,$dto,$localidad,$cp,$provincia,$tipo_dom,$telefono,$revista,$incapacidad,$tbt,$f_alta,$f_cierre_presentacion,$verifica_cuil,$cuil_informado,$tbt_sijp,$cuit_sijp,$os_sijp,$ult_per_sijp,$os_opcion_vigente,$periodo_desde_opcion)=explode("|",$input);
	
	
	
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".padrones_sss (rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,f_cierre_presentacion,verifica_cuil,cuil_informado,tbt_sijp,cuit_sijp,os_sijp,ult_per_sijp,os_opcion_vigente,periodo_desde_opcion,periodo,id_lote)
	VALUES	('$rnos','$cuit','$cuil_titular','$parentesco','$cuil','$td','$nd','$ayn','$sexo','$est_civil','$fn','$nacionalidad','$calle','$numero','$piso','$dto','$localidad','$cp','$provincia','$tipo_dom','$telefono','$revista','$incapacidad','$tbt','$f_alta','$f_cierre_presentacion','$verifica_cuil','$cuil_informado','$tbt_sijp','$cuit_sijp','$os_sijp','$ult_per_sijp','$os_opcion_vigente','$periodo_desde_opcion','$periodo_formateado','$periodo_formateado')
	";
	mysql_query($sql) or die(mysql_error().$sql);
	
	
	
	return $sql;
	
}
?>