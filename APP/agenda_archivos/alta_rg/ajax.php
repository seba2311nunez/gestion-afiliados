<?php 
require("../Conectar.inc");
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
	
	case 'valida_archivo_a_procesar':
	
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='alta_rg'";
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
		
		if($_SERVER['DOCUMENT_ROOT']=="/var/www/".DOMINIO){
			$archivo_send =  $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/alta_rg/archivos";
		}
		else{
			$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/alta_rg/archivos/";
		}
		//echo $archivo_send;exit();
		$nombre_archivo = $nombre.'_'.$periodo.'.'.$extension;
		
		$path = $archivo_send.$nombre_archivo; 

		$copiado = move_uploaded_file($_FILES['archivo']['tmp_name'], $path);
		if($copiado==false){
						
			echo "Error moviendo el archivo ";
			
		}
		else{
			
			$periodo_formateado=substr(str_replace('-','',$periodo),0,6);
			/*
			$sql="LOAD DATA LOCAL INFILE  '$path'
					INTO TABLE $base_historicos.altas_super
					FIELDS TERMINATED BY '|' 
					ENCLOSED BY ''
					LINES TERMINATED BY '\n'
					(tipo_registro,numero_super,cuil,apeynom,
						calle,num,piso,dpto,telef,localidad,codpos,provincia,cuit,
						empresa,nose1,fechapedido,deleg_nombre,cod_localidad,fecha_envio_super,procedencia)
					SET periodo_recibido = '$periodo_formateado', 
						id_lote = '$periodo_formateado',
						fecha_super = '$periodo' ";
					
			mysql_query($sql);
						
			$contador="SELECT COUNT(*) as contador FROM $base_historicos.altas_super WHERE periodo_recibido='$periodo_formateado'";
			$result = mysql_query($contador);
			$row = mysql_fetch_assoc($result);
			$konta = $row['contador'];
			$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);
			*/

			$gestor = fopen("$path","r");
			$konta=0 ;
			
			while ($input = fgets($gestor, 250)) {
				
				$input= ereg_replace( "'", " ", $input );
				
				if($input==""){
					
				}
				else{
					$konta++;
					insertar($konta,$input,$periodo,$periodo_formateado);
					
				}
				
			}

			$id_lote = graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario);
		}
		echo $id_lote;

		break;

	


}

function insertar($konta,$input,$periodo,$periodo_formateado){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	list($a, $b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s,$t)=explode("|",$input);
		
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".altas_super (tipo_registro,numero_super,cuil,apeynom,calle,num,piso,dpto,telef,localidad,codpos,provincia,cuit,
		empresa,nose1,fechapedido,deleg_nombre,cod_localidad,fecha_envio_super,procedencia,periodo_recibido,id_lote,fecha_super)
	VALUES ('$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k','$l','$m','$n','$o','$p','$q','$r','$s','$t','$periodo_formateado','$periodo_formateado','$periodo')
	";
	
	mysql_query($sql) or die(mysql_error().$sql);
	
	
}

function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','alta_rg',1,'alta_rg_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	$sql="UPDATE ".N_BASE_HISTORICOS.".altas_super SET id_lote=$id_lote WHERE id_lote=periodo_recibido";
	mysql_query($sql) or die(mysql_error()." ".$sql);
	

	$sql="UPDATE ".N_BASE_HISTORICOS.".agenda SET procesado=1 WHERE clave='alta_rg_$periodo'";
	mysql_query($sql) or die(mysql_error()." ".$sql);
	
	return $id_lote;
}

//aprobacion_afiliados_importacion
function insertar_en_aai($id_lote,$id_user){

	// -- Este Proceso toma la informacion completa de este lote y verifica si todos existen en el padron (Titulares/Familiares)
	// -- si no existen los pasa a la tabla de aprobacion_afiliados_importacion y les genera el estado inicial en la tabla de estados.
	$query="CALL ".N_BASE_PADRON.".aai_insertar_altas_rg($id_lote, $id_user);";
	mysql_query($query) or die(mysql_error()."Error en el CALL aai_insertar_altas_rg ".$query);

}

?>