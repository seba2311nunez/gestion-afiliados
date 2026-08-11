<?php 
include ("../../Config/Conectar.inc");

header("Content-Type: text/html;charset=utf-8");
mysql_query("SET NAMES 'utf8'"); 

switch ($parametro) {

	case 'listado':
		# code...

		if($fdesde==null and $fhasta==null){

			$cond_fechas = " AND a.fecha_clave >= CONCAT(MID(DATE_ADD(CURDATE(),INTERVAL -3 MONTH),1,7),'-01') " ;
		}
		else{

			$cond_fechas = " AND a.fecha_clave BETWEEN '$fdesde' AND '$fhasta' ";
		}


		if($tipo_archivo=="todas"){

			$cond_tipo_archivo = "  " ;
		}

		else{
			$cond_tipo_archivo = " AND a.clave LIKE CONCAT('$tipo_archivo','%') ";
			
			if($tipo_archivo=="n_xxxxxx"){
				$cond_tipo_archivo = " AND a.clave LIKE CONCAT('adhesiones','%') ";
			}
			if($tipo_archivo=="alta_rg"){
				$cond_tipo_archivo = " AND ( a.clave LIKE CONCAT('$tipo_archivo','%') OR a.clave LIKE CONCAT('altas_rg_sss','%')) ";
			}
			if($tipo_archivo=="alta_rg_sss"){
				$cond_tipo_archivo = " AND ( a.clave LIKE CONCAT('$tipo_archivo','%') OR a.clave LIKE CONCAT('altas_rg_sss','%')) ";
			}
			if($tipo_archivo=="baja_rg"){
				$cond_tipo_archivo = " AND ( a.clave LIKE CONCAT('$tipo_archivo','%') OR a.clave LIKE CONCAT('bajas_rg_sss','%')) ";
			}
			if($tipo_archivo=="alta_mt"){
				$cond_tipo_archivo = " AND ( a.clave LIKE CONCAT('$tipo_archivo','%') OR a.clave LIKE CONCAT('altas_mt_sss','%')) ";
			}
			if($tipo_archivo=="baja_mt"){
				$cond_tipo_archivo = " AND ( a.clave LIKE CONCAT('$tipo_archivo','%') OR a.clave LIKE CONCAT('bajas_mt_sss','%')) ";
			}
			
			
		}		

		$cond_orden = substr($lst_orden, 2,4);

		$query = "SELECT a.id,
						IF(a.clave LIKE 'adhesio%',REPLACE(a.clave,'adhesiones_sss','n_xxxxxx'),a.clave) AS clave,
						a.fecha_clave,
						a.de_donde_sale,
						a.fecha_inicio,
						a.fecha_limite,
						a.quien_tiene_permiso_para_bajarlo,
						CASE
							WHEN a.procesado=1 THEN 4 -- procesado
							WHEN l.cant_registros IS NOT NULL THEN 1 -- importado
							WHEN l.cant_registros IS NULL AND a.inexistente=1 THEN 2 -- inexistente
							WHEN l.cant_registros IS NULL AND a.inexistente!=1 THEN 3 -- no procesado
							
						END AS estado_hoy,
						IF(DATEDIFF(a.fecha_clave,NOW())>0,
								CONCAT('Revisar en ', DATEDIFF(a.fecha_clave,NOW() ) , ' dias.' ) ,
								IF(l.id IS NULL,
								'Nunca se Proceso',
								DATE_FORMAT(l.fechador,'%d/%m/%Y %H:%i')
								)
								
							) AS fecha_proceso,
						DATEDIFF(a.fecha_clave,NOW()),
						l.id AS id_lote,
						l.cant_registros,
						an.tipo,
						an.link,
						
						l.id_usuario,
						u.usuario   

						FROM $base_historicos.agenda a
						LEFT JOIN $base_historicos.agenda_notas an ON MID(a.clave,1,LENGTH(clave)-11)=an.tipo
						LEFT JOIN $base_historicos.lotes l ON a.clave=l.clave_agenda 
						LEFT JOIN $base_usuarios.users u ON l.id_usuario=u.id
						WHERE 1=1
							$cond_fechas
							$cond_tipo_archivo

						ORDER BY a.fecha_clave $cond_orden  ";
		//echo $query; exit();
		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {

			$importe = number_format($row['importe'],2,",","");
			
		    $json[] = array(
					'id' => $row['id'],
					'clave' => $row['clave'],	
					'fecha_clave' => $row['fecha_clave'],
					'de_donde_sale' => $row['de_donde_sale'],
					'fecha_inicio' => $row['fecha_inicio'],
					'fecha_limite' => $row['fecha_limite'],
					'permiso' => $row['quien_tiene_permiso_de_bajarlo'],
					'estado_hoy' => $row['estado_hoy'],
					'id_lote' => $row['id_lote'],
					'fechador' => $row['fechador'],
					'fecha_proceso' => $row['fecha_proceso'],
					'cant_registros' => $row['cant_registros'],	
					'tipo' => $row['tipo'],				
					'link' => $row['link'],			
					'id_usuario' => $row['id_usuario'],					        		
					'usuario' => $row['usuario']
					       
		      );
		}

		echo json_encode($json);

		break;

	

	case 'lst_archivos':
			
			$query = "SELECT DISTINCT proceso 
						FROM $base_historicos.lotes
						WHERE proceso NOT LIKE '**%'
							AND proceso NOT IN ('altas_mt','altas_rg_sss','bajas_rg_sss','altas_mt_sss','bajas_mt_sss') ";

			$query = "SELECT DISTINCT MID(clave,1,LOCATE('2',clave)-2) AS proceso
							FROM $base_historicos.agenda 
							WHERE MID(clave,1,LOCATE('2',clave)-2) NOT IN ('alta_mt','alta_rg','baja_mt','baja_rg','adhesiones_ftp','n_1')";

			$query = "SELECT DISTINCT IF(MID(clave,1,LOCATE('2',clave)-2)='adhesiones_sss','n_xxxxxx',MID(clave,1,LOCATE('2',clave)-2)) AS proceso
							FROM $base_historicos.agenda 
							WHERE MID(clave,1,LOCATE('2',clave)-2) NOT IN ('alta_mt','alta_rg','baja_mt','baja_rg','adhesiones_ftp','n_1')";
							
			$result = mysql_query($query) or die(mysql_error().$query);
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
									        		
						'proceso' => $row['proceso']
						       
			      );
			}
			
			echo json_encode($json);
			
		break;

	case 'marcar_inexistente':
		# code...

		$query = "UPDATE $base_historicos.agenda SET inexistente=1 WHERE id = $id_item ";
		mysql_query($query) or die(mysql_error().$query);

		echo "ok";

		break;

	

	
	
	case 'mostrar_nota':
		# code...

			$sql_notas = "SELECT nota FROM $base_historicos.agenda_notas WHERE tipo='$tipo' ";
			//echo "$sql_notas";
			$rs_notas = mysql_query($sql_notas) or die(mysql_error().$sql_notas);
			$d_notas = mysql_fetch_object($rs_notas);

			echo $d_notas->nota ;

		break;
	case 'exportar_ftp_padron_sss':
		$sql="CALL $base_dev.exp_padron_sss($id_lote)";
		$rs = mysql_query($sql) or die(mysql_error());

		if($rs){
			$sql="SELECT DATE_FORMAT(descripcion,'%Y%m') AS periodo FROM $base_historicos.`lotes` l WHERE l.`id`=$id_lote";
			$rs = mysql_query($sql) or die(mysql_error());
			$periodo = mysql_fetch_object($rs)->periodo;

			$json = array('periodo' => $periodo,'status' => "ok");
		}
		echo json_encode($json);
		break;
	case 'exportar_ftp_padron_sss_2':
		header("Location: http://190.17.78.252/ftp/fps.php?periodo=".$periodo);
		break;
	case 'descargar_padron_sss':

		$sql ="SELECT ps.rnos,ps.cuit,ps.cuil_titular,ps.parentesco,ps.cuil,ps.td,ps.nd,ps.ayn,ps.sexo,ps.est_civil,ps.fn,ps.nacionalidad,ps.calle,ps.numero,ps.piso,ps.dto,ps.localidad,ps.cp,ps.provincia,ps.tipo_dom,ps.telefono,ps.revista,ps.incapacidad,ps.tbt,ps.f_alta,ps.f_cierre_presentacion,ps.verifica_cuil,ps.cuil_informado,ps.tbt_sijp,ps.cuit_sijp,ps.os_sijp,ps.ult_per_sijp,ps.os_opcion_vigente,ps.periodo_desde_opcion, ROUND(DATEDIFF(CONCAT(MID(ps.periodo,1,4),'-',MID(ps.periodo,5,2),'-01') , CONCAT(MID(ps.fn,5,4),'-',MID(ps.fn,3,2),'-',MID(ps.fn,1,2)))/365) AS edad 
		FROM ".N_BASE_HISTORICOS.".padrones_sss ps 
		JOIN ".N_BASE_PADRON.".persona p ON p.cuil=ps.cuil
		JOIN ".N_BASE_PADRON.".afiliados a ON a.id_persona=p.id
		JOIN ".N_BASE_PADRON.".desreguladoras d ON d.id=a.id_desreguladora
		
		WHERE id_lote='$id_lote' and d.convenio_real IN ('MOSAISTAS','OSPM CORDOBA')";
		#echo $sql;exit();
		$filename = strtoupper(INST_NAME)."_Padron_SSS_".$periodo;

		$readfile = getcwd()."/$filename.zip";

		#echo $readfile;exit();

		$result = shell_exec("sh zip_result.sh \"$sql\" \"$filename\"");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename.zip");
		readfile($readfile);
		break;
	case 'descargar_padron_sss_sin_proximas_altas':

		$sql ="SELECT rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,dto,localidad,cp,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,f_cierre_presentacion,verifica_cuil,cuil_informado,tbt_sijp,cuit_sijp,os_sijp,ult_per_sijp,os_opcion_vigente,periodo_desde_opcion, ROUND(DATEDIFF(CONCAT(MID(periodo,1,4),'-',MID(periodo,5,2),'-01') , CONCAT(MID(fn,5,4),'-',MID(fn,3,2),'-',MID(fn,1,2)))/365) AS edad FROM ".N_BASE_HISTORICOS.".padrones_sss WHERE id_lote='$id_lote' AND CONCAT(MID(f_alta,5,4),'-',MID(f_alta,3,2),'-',MID(f_alta,1,2)) <= CONCAT(MID(periodo,1,4),'-',MID(periodo,5,2),'-01')";
		#echo $sql;exit();
		$filename = strtoupper(INST_NAME)."_Padron_SSS_".$periodo."_Sin_altas_futuras";

		$readfile = getcwd()."/$filename.zip";

		#echo $readfile;exit();

		$result = shell_exec("sh zip_result.sh \"$sql\" \"$filename\"");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename.zip");
		readfile($readfile);
		break;

	default:
		
		break;
}

?>