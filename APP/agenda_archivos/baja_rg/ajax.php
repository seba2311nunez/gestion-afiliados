<?php 
include(__DIR__.'/../../../Conectar.inc');
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
	
		$sql="SELECT * FROM $base_historicos.lotes WHERE descripcion='$periodo' AND proceso='baja_rg'";
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
			$archivo_send =  $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/baja_rg/archivos/";
		}
		else{
			$archivo_send = $_SERVER['DOCUMENT_ROOT']."/padron/APP/agenda_archivos/baja_rg/archivos/";
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
	list($a, $b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q)=explode("|",$input);
		
	$sql="
	INSERT INTO ".N_BASE_HISTORICOS.".bajas_super (tipo_registro,numero_super,cuil,apeynom,calle,num,piso,dpto,telef,localidad,codpos,provincia,cuit,empresa,nose1,fecha1,numero1,periodo,id_lote)
	VALUES ('$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k','$l','$m','$n','$o','$p','$q','$periodo_formateado','$periodo_formateado')
	";
	
	mysql_query($sql) or die(mysql_error().$sql);
	
	
}
function graba_lote_y_cierra($konta,$periodo,$nombre_archivo,$id_usuario){
	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes(archivo,descripcion,cant_registros,proceso,id_usuario,clave_agenda)
				VALUES('$nombre_archivo','$periodo','$konta','baja_rg',1,'baja_rg_$periodo')"; 
	mysql_query($inserta) or die(mysql_error().$inserta);
	$id_lote = mysql_insert_id();
		
	//echo "Hay $konta grabados en el lote $lote // $descripcion </br>";
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".bajas_super SET id_lote=$id_lote WHERE id_lote=periodo") or die(mysql_error());
	
	return $id_lote;
}
?>