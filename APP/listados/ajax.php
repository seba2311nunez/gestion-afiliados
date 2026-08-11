<?php  
include("../../Config/Conectar.inc");

$ip = $_SERVER['REMOTE_ADDR'];
$usu = $_SESSION["usu"];
$id_user = $_SESSION['iduser'];
$id_desreguladora = $_SESSION["id_especialidad"];

if($_SESSION['perfil'] == 'consulta_prestador' && $_SESSION['id_especialidad']){}


// Helpers mysql_*:
function yaHayProceso(){
  $sql = "SELECT id, fechador
          FROM log_eventos
          WHERE evento='afil_work' AND fechador_fin IS NULL
          ORDER BY id DESC
          LIMIT 1";
  $rs = mysql_query($sql) or die(json_encode(['status'=>'error','message'=>mysql_error().' '.$sql]));
  if ($rs && mysql_num_rows($rs) > 0){
    return mysql_fetch_assoc($rs);
  }
  return null;
}


switch ($parametro) {
	
	
	case 'lst_padron_filtros':

			//Dejo registro de la descarga
			mysql_query("INSERT INTO $base_padron.log_eventos(evento,ip,id_usuario)
							VALUES ('Descarga padron','$ip',$id_user) ") or die(mysql_error());

						
			$filename = "Padron_$base_".date('Ymd') ."_cap_".$id_desreguladora. ".xls";
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=".$filename." ");
			
			header("Content-Type: text/html;charset=utf-8");
			mysql_query("SET NAMES 'utf8'");
			
			mysql_query("CALL $base_padron.crea_afil_work(CURDATE())") or die(mysql_error()."Primer call");
			
			$where_capita = " WHERE id_desreguladora='$id_desreguladora' ";                             
			
			$sql_final="SELECT cuil_titular,cuil,tbt,nd,ayn,
								sexo,
								DATE_FORMAT(fn,'%d/%m/%Y') AS fn,
								TIMESTAMPDIFF(YEAR,fn,CURDATE()) AS edad,
								parentesco,desreguladora,
								provincia,localidad,calle,numero,piso,depto,
								incapacidad,mn,mx 
							FROM afil_work_actual $where_capita"; //echo "$sql_final";
			
			$result=mysql_query($sql_final) or die(mysql_error()."<br>SQL FINAL <br>".$sql_final);
			
			$tabla="";
			
			$tabla.="<table border=1>
						<tr>
							<th>Tipo beneficiario</th>
							<th>Cuil titular</th>
							<th>Cuil familiar</th>
							<th>DNI</th>
							<th>Sexo</th>
							<th>Apellido y nombre</th>
							<th>Fecha nacimiento</th>
							<th>Edad</th>                           
							<th>Parentesco</th>
							<th>Desreguladora</th>
							<th>Provincia</th>
							<th>Localidad</th>
							<th>Calle</th>
							<th>Numero</th>
							<th>Piso</th>
							<th>Dto</th>
							<th>Incapacidad</th>
							<th>Periodo desde</th>
							<th>Periodo hasta</th>  
						</tr>                   
						";
			
			while($d=mysql_fetch_object($result)){
						
				$tabla.="<tr>
							<td>$d->tbt</td>
							<td>$d->cuil_titular</td>
							<td>$d->cuil</td>
							<td>$d->nd</td>
							<td>$d->sexo</td>
							<td>$d->ayn</td>
							<td>$d->fn</td>
							<td>$d->edad</td>                           
							<td>$d->parentesco</td>
							<td>$d->desreguladora</td>
							<td>$d->provincia</td>
							<td>$d->localidad</td>
							<td>$d->calle</td>
							<td>$d->numero</td>
							<td>$d->piso</td>
							<td>$d->depto</td>
							<td>$d->incapacidad</td>    
							<td>$d->mn</td> 
							<td>$d->mx</td>             
						</tr>                   
						";
				
			}
			
			$tabla.="</table>";                                 
			
			echo "$tabla";
					
		break;
	
	case 'lst_descarga_padron':
		// code...
		$sql="SELECT DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fecha,u.usuario 
				FROM $base_padron.log_eventos l
				JOIN $base_usuarios.users u ON l.id_usuario=u.id 
				WHERE evento='Descarga padron'
					AND id_usuario=$id_user
				ORDER BY l.id DESC ";

		
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		$q = mysql_num_rows($result);

		if($q>0){

			while ($row = mysql_fetch_assoc($result)) {
			
				$json[] = array(
						'fecha' => $row['fecha'],                                       
						'usuario' => $row['usuario']
							   
				  );
			}

		}
		else{

			$json[] = array(
						'fecha' => 'Sin resultados',                                        
						'usuario' => 'Sin resultados'
							   
				  );
		}

		echo json_encode($json);

		break;

	case 'lst_bajas_x_periodo':
		// code...
		mysql_query("CALL $base.tablero_bajas_rg()");

		$sql = "SELECT p.periodo1,COUNT(*) AS total
					FROM prueba.periodos p
					LEFT JOIN $base.tmp_bajas_rg_final b ON p.periodo1=MID(b.fecha_vigencia,1,7)
					WHERE 1=1
						AND id_desreguladora=$id_desreguladora 
						AND primer_dia BETWEEN DATE_ADD(CURDATE(),INTERVAL -12 MONTH) AND ADDDATE(CURDATE(),INTERVAL 1 MONTH)
					GROUP BY 1
					ORDER BY 1 DESC";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		$q = mysql_num_rows($result);

		if($q>0){

			while ($row = mysql_fetch_assoc($result)) {
			
				$json[] = array(
						'periodo' => $row['periodo1'],                                      
						'cantidad' => $row['total']
							   
				  );
			}

		}
		else{

			$json[] = array(
						'fecha' => 'Sin resultados',                                        
						'usuario' => 'Sin resultados'
							   
				  );
		}

		echo json_encode($json);                    

		break;

	case 'lst_bajas_x_periodo_xls': 

		if($rango){

			$parametro = " AND fecha_eleccion BETWEEN ADDDATE(CURDATE(),INTERVAL -$rango DAY) AND CURDATE() ";

			$sql="SELECT ADDDATE(CURDATE(),INTERVAL -$rango DAY) as desde,CURDATE() as hasta";

			$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
			$d = mysql_fetch_object($result);

			$desde= $d->desde;
			$hasta= $d->hasta;

			$parametro2=" Desde $desde hasta $hasta  ";

		}
		else{

			$parametro = " AND MID(fecha_vigencia,1,7)='$periodo' ";

			$parametro2=" Periodo $periodo ";

		}
		// code...

		//Dejo registro de la descarga
		mysql_query("INSERT INTO $base_padron.log_eventos(evento,ip,id_usuario)
						VALUES ('Descarga bajas extranet $periodo','$ip',$id_user) ") or die(mysql_error());

					
		$filename = "Bajas_".strtoupper(INST_NAME)."_".$periodo."_cap_".$id_desreguladora. ".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		mysql_query("CALL $base.tablero_bajas_rg()");
		
		$sql = "SELECT t.*,MID(p.procedencia,1,250) AS os_nom
					FROM $base.tmp_bajas_rg_final t
					LEFT JOIN $base_padron.procedencia p ON t.os_origen=p.codigo
					WHERE id_desreguladora=$id_desreguladora
						$parametro
					ORDER BY fecha_vigencia";
		//echo $sql;exit();
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);


		$sql="SELECT DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y/%m/%d %H:%i') AS fecha";

		$rsdate =mysql_query($sql) or die(mysql_error()."<br>".$sql); $d = mysql_fetch_object($rsdate);$descarga=$d->fecha;
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr>
						<th colspan=8>Listado de Bajas RG $parametro2 | Usuario: $usu | Hora de Descarga: $descarga</th>
					</tr>
					<tr>
						<th>Formulario</th>
						<th>Cuil titular</th>
						<th>Apellido y nombre</th>
						<th>Fecha de Eleccion</th>
						<th>Fecha de Vigencia</th>
						<th>Telefono</th>
						<th>RNOS</th>
						<th>OS nombre</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nro_formulario</td>
						<td>$d->cuil_titular</td>
						<td>$d->ayn</td>
						<td>$d->fecha_eleccion</td>
						<td>$d->fecha_vigencia</td>
						<td>$d->telefono</td>
						<td>$d->os_origen</td>
						<td>$d->os_nom</td>
					</tr>                   
					";
			
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'imagen_cantidades':
		// code...
		//mysql_query("CALL $base_padron.crea_afil_work(CURDATE())") or die(mysql_error()."Primer call");

		$sql="SELECT SUM(IF(id_desreguladora=1 AND cp BETWEEN 1600 AND 1684,1,0)) AS zona_norte,
						SUM(IF(id_desreguladora=1 AND cp BETWEEN 2000 AND 3512 AND cp NOT IN (3490,2930),1,0)) AS resto_pais,
						SUM(IF(id_desreguladora=36 AND cp BETWEEN 1600 AND 1684,1,0)) AS zona_norte_ddjj,
						SUM(IF(id_desreguladora=36 AND cp BETWEEN 2000 AND 3512 AND cp NOT IN (3490,2930),1,0)) AS resto_pais_ddjj,
						SUM(IF(id_desreguladora=1 AND (cp BETWEEN 2000 AND 2799 OR cp BETWEEN 2931 AND 3016) ,1,0)) AS emerger_amb
					FROM $base_padron.afil_work_actual
					WHERE 1=1
						 ";                         

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
			$json[] = array(
					'zn' => $row['zona_norte'],                                     
					'rp' => $row['resto_pais'],
					'zn_ddjj' => $row['zona_norte_ddjj'],                                       
					'rp_ddjj' => $row['resto_pais_ddjj'],
					'emerger_amb' => $row['emerger_amb']
						   
			  );
		}

		echo json_encode($json);

		break;
	case 'abi_totales_x_periodo':
		$sql="
			SELECT MID(fecha_aPartir,1,7) as periodo,SUM(IF(sub.estado = 'ALTA',1,0)) AS altas, SUM(IF(sub.estado = 'BAJA',1,0)) AS bajas
			FROM (
				SELECT DISTINCT id_afiliado,fecha_aPartir,estado 
				FROM $base_historicos._historico_afiliados ha
				JOIN $base_padron.afiliados a ON a.id=ha.id_afiliado
				JOIN $base_padron.desreguladoras d ON d.id=a.id_desreguladora
				WHERE fecha_aPartir BETWEEN ADDDATE(CURDATE(),INTERVAL -70 YEAR) AND ADDDATE(CURDATE(),INTERVAL 3 MONTH)
					AND d.convenio_real = 'PROPIOS'
					AND a.nben IS NOT NULL
			) sub
			GROUP BY MID(fecha_aPartir,1,7)
			ORDER BY 1 DESC
		";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
			$json[] = $row;
		}

		echo json_encode($json);
		break;
	case 'abi_xls':

		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_".$estado."_".$periodo."_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="
			SELECT DISTINCT p.cuil,p.apellido,p.nombre,ha.fecha_aPartir AS fecha_estado,ha.estado#,ha.*
			FROM $base_historicos._historico_afiliados ha
			JOIN $base_padron.afiliados a ON a.id=ha.id_afiliado
			JOIN $base_padron.desreguladoras d ON d.id=a.id_desreguladora
			JOIN $base_padron.persona p ON p.id=a.id_persona
			WHERE MID(fecha_aPartir,1,7) = '$periodo' AND ha.estado='$estado'
				AND d.convenio_real = 'PROPIOS'
				AND a.nben IS NOT NULL;
		";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		

		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>CUIL</th>                       
						<th>Apellido</th>
						<th>Nombre</th>
						<th>F. Estado</th>
						<th>Estado</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="
				<tr>
					<td>$d->cuil</td>
					<td>$d->apellido</td>
					<td>$d->nombre</td>
					<td>$d->fecha_estado</td>
					<td>$d->estado</td>
				</tr>                   
				";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;
	case 'imagen_zona_norte_xls':
		// code...
		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_imagen_zona_norte_".$hoy."_.xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		#mysql_query("CALL $base_padron.crea_afil_work(CURDATE())") or die(mysql_error()."Primer call");
				
		$sql = "SELECT nben,ayn,fn,CONCAT(calle,' ',numero) AS domicilio,localidad,cp,provincia,nd

					FROM $base_padron.afil_work_actual
					WHERE 1=1
						AND id_desreguladora=1 
						AND cp BETWEEN 1600 AND 1684
						
					ORDER BY nben";
		//echo $sql;exit();
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		##IMPORTANTE - Los siguientes updates deben de tener el mismo WHERE que el selector para el listado

		$sql="
			UPDATE $base_padron.afil_work_actual aw
			JOIN $base_padron.afiliados a ON a.id=aw.id_afiliado
			SET a.id_plan_anterior=a.id_plan_medico
			WHERE 1=1
			AND a.id_plan_medico=6
			AND aw.id_desreguladora=1 
			AND aw.cp BETWEEN 1600 AND 1684;
		";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="
			UPDATE $base_padron.afil_work_actual aw
			JOIN $base_padron.afiliados a ON a.id=aw.id_afiliado
			SET a.id_plan_medico=6
			WHERE 1=1
			AND aw.id_desreguladora=1 
			AND aw.cp BETWEEN 1600 AND 1684;
		";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);


		$sql="SELECT DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y/%m/%d %H:%i') AS fecha";

		$rsdate =mysql_query($sql) or die(mysql_error()."<br>".$sql); $d = mysql_fetch_object($rsdate);$descarga=$d->fecha;
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th colspan=8>Imagen zona norte | Usuario: $usu | Hora de Descarga: $descarga</th>
					</tr>
					<tr style='background-color: black; color: white;'>
						<th>Nben</th>                       
						<th>Apellido y nombre</th>
						<th>Fecha de Nacimiento</th>
						<th>Domicilio</th>
						<th>Localidad</th>
						<th>Codigo postal</th>
						<th>Provincia</th>
						<th>Documento</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nben</td>
						<td>$d->ayn</td>
						<td>$d->fn</td>
						<td>$d->domicilio</td>
						<td>$d->localidad</td>
						<td>$d->cp</td>
						<td>$d->provincia</td>
						<td>$d->nd</td>
					</tr>                   
					";
			
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'imagen_resto_pais_xls':
		// code...
		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_imagen_zona_norte_".$hoy."_.xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

				
		$sql = "SELECT nben,ayn,fn,CONCAT(calle,' ',numero) AS domicilio,localidad,cp,provincia,nd

					FROM $base_padron.afil_work_actual
					WHERE 1=1
						AND id_desreguladora=1 
						AND cp BETWEEN 2000 AND 3512 
						AND cp NOT IN (3490,2930)
						
					ORDER BY nben";
		//echo $sql;exit();
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		##IMPORTANTE - Los siguientes updates deben de tener el mismo WHERE que el selector para el listado

		$sql="
			UPDATE $base_padron.afil_work_actual aw
			JOIN $base_padron.afiliados a ON a.id=aw.id_afiliado
			SET a.id_plan_anterior=a.id_plan_medico
			WHERE 1=1
			AND aw.id_desreguladora=1 
			AND aw.cp BETWEEN 2000 AND 3512 
			AND aw.cp NOT IN (3490,2930);
		";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="
			UPDATE $base_padron.afil_work_actual aw
			JOIN $base_padron.afiliados a ON a.id=aw.id_afiliado
			SET a.id_plan_medico=6
			WHERE 1=1
			AND aw.id_desreguladora=1 
			AND aw.cp BETWEEN 2000 AND 3512 
			AND aw.cp NOT IN (3490,2930);
		";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);


		$sql="SELECT DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y/%m/%d %H:%i') AS fecha";

		$rsdate =mysql_query($sql) or die(mysql_error()."<br>".$sql); $d = mysql_fetch_object($rsdate);$descarga=$d->fecha;
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th colspan=8>Imagen Resto del pais | Usuario: $usu | Hora de Descarga: $descarga</th>
					</tr>
					<tr style='background-color: black; color: white;'>
						<th>Nben</th>                       
						<th>Apellido y nombre</th>
						<th>Fecha de Nacimiento</th>
						<th>Domicilio</th>
						<th>Localidad</th>
						<th>Codigo postal</th>
						<th>Provincia</th>
						<th>Documento</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nben</td>
						<td>$d->ayn</td>
						<td>$d->fn</td>
						<td>$d->domicilio</td>
						<td>$d->localidad</td>
						<td>$d->cp</td>
						<td>$d->provincia</td>
						<td>$d->nd</td>
					</tr>                   
					";
			
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'padron_ambulancia_emerger':
		// code...
		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_imagen_zona_norte_".$hoy."_.xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

				
		$sql = "SELECT nben,ayn,fn,CONCAT(calle,' ',numero) AS domicilio,localidad,cp,provincia,nd

					FROM $base_padron.afil_work_actual
					WHERE 1=1
						AND id_desreguladora=1 
						AND cp BETWEEN 2152 AND 3016
						#AND cp NOT IN (3490,2930)
						
					ORDER BY nben";
		//echo $sql;exit();
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);


		$sql="SELECT DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y/%m/%d %H:%i') AS fecha";

		$rsdate =mysql_query($sql) or die(mysql_error()."<br>".$sql); $d = mysql_fetch_object($rsdate);$descarga=$d->fecha;
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th colspan=8>Padron ".strtoupper(INST_NAME)." para EMERGER | Usuario: $usu | Hora de Descarga: $descarga</th>
					</tr>
					<tr style='background-color: black; color: white;'>
						<th>Nben</th>                       
						<th>Apellido y nombre</th>
						<th>Fecha de Nacimiento</th>
						<th>Domicilio</th>
						<th>Localidad</th>
						<th>Codigo postal</th>
						<th>Provincia</th>
						<th>Documento</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nben</td>
						<td>$d->ayn</td>
						<td>$d->fn</td>
						<td>$d->domicilio</td>
						<td>$d->localidad</td>
						<td>$d->cp</td>
						<td>$d->provincia</td>
						<td>$d->nd</td>
					</tr>                   
					";
			
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'ambulancias_xls':
		// code...

		switch ($tipo) {
			case 'amb_semzar':
				// code...
				$condicion_cp = "AND cp BETWEEN 2800 AND 2814 " ;
				$tipo_format = "SEMZAR ( Zarate - Campana ) cp 2800 al 2814 ";
				$tipo_format_redu = "SEMZAR";
				break;

			case 'amb_emergencias_regionales':
				// code...
				$condicion_cp = "AND cp BETWEEN 2126 AND 2128 " ;
				$tipo_format = "Emergencias regionales | Pueblo Esther - Arroyo Seco cp 2126 hasta 2128 ";
				$tipo_format_redu = "Emergencias_regionales";
				break;

			case 'amb_serva':
				// code...
				$condicion_cp = "AND cp BETWEEN 0000 AND 1980 " ;
				$tipo_format = "SERVA y Ayuda medica (Capital y Gba) cp 0000 a 1980 ";
				$tipo_format_redu = "SERVA";
				break;

			case 'amb_cem':
				// code...
				$condicion_cp = "AND cp BETWEEN 2900 AND 2930 " ;
				$tipo_format = "CEM ( san pedro - san nicolas - villa constitucion) cp 2900 a 2930 ";
				$tipo_format_redu = "CEM";
				break;
			
			case 'amb_emerger':
				// code...
				$condicion_cp = "AND (cp BETWEEN 2000 AND 2799 OR cp BETWEEN 2931 AND 3016) " ;
				$tipo_format = "EMERGER cp 2000 a 3016 ";
				$tipo_format_redu = "EMERGER";
				break;

			default:
				// code...
				break;
		}

		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_".$tipo_format_redu."_".$hoy."_.xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

				
			$sql = "SELECT nben,ayn,fn,CONCAT(calle,' ',numero) AS domicilio,localidad,cp,provincia,nd

						FROM $base_padron.afil_work_actual
						WHERE 1=1
							AND id_desreguladora=1 
							$condicion_cp 
							
						ORDER BY nben";
		echo $sql;exit();
		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);


		$sql="SELECT DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y/%m/%d %H:%i') AS fecha";

		$rsdate =mysql_query($sql) or die(mysql_error()."<br>".$sql); $d = mysql_fetch_object($rsdate);$descarga=$d->fecha;
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th colspan=8>$tipo_format | Usuario: $usu | Hora de Descarga: $descarga</th>
					</tr>
					<tr style='background-color: black; color: white;'>
						<th>Nben</th>                       
						<th>Apellido y nombre</th>
						<th>Fecha de Nacimiento</th>
						<th>Domicilio</th>
						<th>Localidad</th>
						<th>Codigo postal</th>
						<th>Provincia</th>
						<th>Documento</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nben</td>
						<td>$d->ayn</td>
						<td>$d->fn</td>
						<td>$d->domicilio</td>
						<td>$d->localidad</td>
						<td>$d->cp</td>
						<td>$d->provincia</td>
						<td>$d->nd</td>
					</tr>                   
					";
			
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'cursantes_xls':

		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_PROPIOS_cursantes_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="CALL $base_padron.INFO_listar_cursantes()";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		

		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>CUIL</th>                       
						<th>Apellido</th>
						<th>Nombre</th>
						<th>F. Nac.</th>
						<th>Edad</th>
						<th>Estado</th>
						<th>Filial</th>
						<th>Ultimo Vto.</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->cuil</td>
						<td>$d->apellido</td>
						<td>$d->nombre</td>
						<td>$d->fn</td>
						<td>$d->edad</td>
						<td>$d->estado</td>
						<td>$d->filial</td>
						<td>$d->ult_vencimiento</td>
					</tr>                   
					";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'credenciales_xls':

		$hoy = date("Y-m-d");
		$filename = "OSEMM_PROPIOS_credenciales_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="CALL $base_padron.INFO_listar_credenciales()";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		

		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>CUIL</th>						
						<th>Apellido</th>
						<th>Nombre</th>
						<th>F. Nac.</th>
						<th>Edad</th>
						<th>Estado</th>
						<th>Filial</th>
						<th>Ultimo Vto.</th>
					</tr>					
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->cuil</td>
						<td>$d->apellido</td>
						<td>$d->nombre</td>
						<td>$d->fn</td>
						<td>$d->edad</td>
						<td>$d->estado</td>
						<td>$d->filial</td>
						<td>$d->ult_vencimiento</td>
					</tr>					
					";
		}
		
		$tabla.="</table>";					        		
		
		echo "$tabla";

		break;

	case 'empresas_sin_dato_xls':

		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_empresas_sin_dato_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="CALL $base_padron.INFO_listar_empresas_sin_dato()";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		
		
		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>CUIT</th>                       
						<th>Nombre</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->cuit</td>
						<td>$d->nombre</td>
					</tr>                   
					";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;
	case 'monot_serdom_propios':

		$hoy = date("Y-m-d");
		$filename = INST_NAME."_monot_serdom_Propios_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="CALL $base_padron.INFO_monot_serdom_propios()";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		
		
		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>Nro Sind</th>   
						<th>Nben</th>                       
						<th>CUIL</th>
						<th>AyN</th>
						<th>Ult Pago</th>                       
						<th>Estado</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nro_sind</td>
						<td>$d->nben</td>
						<td>$d->cuil</td>
						<td>$d->ayn</td>
						<td>$d->ult_pago</td>
						<td>$d->estado</td>
					</tr>                   
					";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;

	case 'jubilados_propios':

		$hoy = date("Y-m-d");
		$filename = INST_NAME."_jubilados_Propios_".$hoy.".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		$sql="CALL $base_padron.INFO_jubilados_propios()";

		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		
		
		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>Nro Sind</th>   
						<th>Nben</th>                       
						<th>CUIL</th>
						<th>AyN</th>                        
						<th>Estado</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->nro_sind</td>
						<td>$d->nben</td>
						<td>$d->cuil</td>
						<td>$d->ayn</td>
						<td>$d->estado</td>
					</tr>                   
					";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;
		
	case 'regenerar_legacy':
		// code...
		mysql_query("CALL $base_padron.crea_afil_work(CURDATE())") or die(mysql_error());
		echo "ok";
		break;

	case 'regenerar':
		// 1) ¿Ya corre?
		$enCurso = yaHayProceso();
		if ($enCurso){
		  echo json_encode(['status'=>'running','started_at'=>$enCurso['fechador']]);
		  exit;
		}

		// 2) Lock rápido para evitar doble lanzamiento simultáneo
		$lockOK = false;
		$rs = mysql_query("SELECT GET_LOCK('afil_work_launcher', 0) AS l");
		if ($rs){
		  $row = mysql_fetch_assoc($rs);
		  $lockOK = ((int)$row['l'] === 1);
		}
		if (!$lockOK){
		  echo json_encode(['status'=>'starting']);
		  exit;
		}

		// 3) Lanzar worker en background
		// Verificar si exec está habilitado
		if (!is_callable('exec')) {
		  // IMPORTANTE: si exec está deshabilitado en php.ini (disable_functions) nunca lanzará
		  @mysql_query("DO RELEASE_LOCK('afil_work_launcher')");
		  echo json_encode(['status'=>'error','message'=>'exec() deshabilitado en php.ini']);
		  exit;
		}

		// Detectar php cli
		$php = trim(@shell_exec('which php'));
		if (!$php || !file_exists($php)) { $php = '/usr/bin/php'; }
		if (!file_exists($php)) {
		  // Último intento
		  $php = '/opt/lampp/bin/php';
		}

		$worker = __DIR__ . '/runner_regenerar_afil_work.php';

		// Log opcional para diagnóstico (quitar si no querés escribir en /tmp)
		$logFile = '/tmp/afil_work_runner.log';

		// Pasamos $base_padron; asumo que viene definido en tu Conectar.inc
		$cmd = sprintf(
		  'nohup %s %s %s >> %s 2>&1 & echo $!',
		  escapeshellarg($php),
		  escapeshellarg($worker),
		  escapeshellarg($base_padron),
		  escapeshellarg($logFile)
		);

		$pid = @exec($cmd);

		// 4) Liberar lock de lanzamiento
		@mysql_query("DO RELEASE_LOCK('afil_work_launcher')");

		// 5) Responder
		echo json_encode([
		  'status'=>'starting',
		  'requested_at'=>date('Y-m-d H:i:s'),
		  'pid'=>$pid ?: null
		]);
		break;

  	case 'status_regenerar':
	    $enCurso = yaHayProceso();
	    if ($enCurso){
	      echo json_encode(['running'=>true, 'started_at'=>$enCurso['fechador']]);
	      exit;
	    } else {
	      $sql = "SELECT fechador, fechador_fin
	              FROM log_eventos
	              WHERE evento='afil_work'
	              ORDER BY id DESC
	              LIMIT 1";
	      $rs = mysql_query($sql);
	      $last = ($rs && mysql_num_rows($rs)>0) ? mysql_fetch_assoc($rs) : null;
	      echo json_encode([
	        'running'=>false,
	        'last_started_at'=>$last ? $last['fechador'] : null,
	        'finished_at'=>$last ? $last['fechador_fin'] : null
	      ]);
	      exit;
	    }
	    break;

	case 'visitar_basico':

	    // --- Config general ---
	    set_time_limit(0);
	    ini_set('memory_limit', '512M');

	    // 1) Traer datos
	    mysql_query("SET NAMES 'utf8'");
	    $sql = "CALL {$base_padron}.lst_padron_visitar_basico()";
	    $rs  = mysql_query($sql) or die(mysql_error() . "<br>" . $sql);

	    // 2) PHPExcel
	    require_once __DIR__ . '/../../Lib/PHPExcel/Classes/PHPExcel.php'; // <-- ajusta la ruta según tu proyecto

	    $objPHPExcel = new PHPExcel();
	    $sheet = $objPHPExcel->getActiveSheet();
	    $sheet->setTitle('Visitar Básico');

	    // 3) Encabezados
	    $headers = [
	        'id_afiliado','id_titular','red','ayn','td','nd','cuil','sexo','edad','plan',
	        'parentesco','localidad','cp','provincia','telefono','telef_celular','estado',
	        'calle','numero','piso','depto','fn','est_civil','nacionalidad','email'
	    ];

	    $col = 0;
	    foreach ($headers as $h) {
	        $sheet->setCellValueByColumnAndRow($col, 1, $h);
	        $col++;
	    }

	    // Estilo encabezados
	    $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
	    #$sheet->getStyle('A1:Y1')->getFill()
	    #      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    #      ->getStartColor()->setRGB('333333');
	    #$sheet->getStyle('A1:Y1')->getFont()->getColor()->setRGB('FFFFFF');

	    // Freeze encabezado
	    $sheet->freezePane('A2');

	    // 4) Cargar filas
	    $row = 2;

	    // Columnas que deben ir como TEXTO para evitar re-formateos (CUIL, doc, CP, teléfonos, etc.)
	    // Índices de columna (0-based): A=0, B=1, ..., Z=25. Ajustamos según headers:
	    $textCols = [
	        2, // red? (si hay códigos alfanuméricos; si es numérica pura, podés quitarla)
	        4, // td
	        5, // nd
	        6, // cuil (MUY importante en texto)
	        12, // cp
	        14, // telefono
	        15, // telef_celular
	        18, // numero (si puede tener ceros a la izquierda o alfanum)
	        19, // piso (puede ser "PB", "1", "S/NUM", etc.)
	        20, // depto (alfanum)
	        24  // email (texto)
	    ];

	    while ($d = mysql_fetch_object($rs)) {

	        $values = [
	            $d->id_afiliado,
	            $d->id_titular,
	            $d->red,
	            $d->ayn,
	            $d->td,
	            $d->nd,
	            $d->cuil,
	            $d->sexo,
	            $d->edad,
	            $d->plan,
	            $d->parentesco,
	            $d->localidad,
	            $d->cp,
	            $d->provincia,
	            $d->telefono,
	            $d->telef_celular,
	            $d->estado,
	            $d->calle,
	            $d->numero,
	            $d->piso,
	            $d->depto,
	            $d->fn,           // luego lo formateamos como fecha
	            $d->est_civil,
	            $d->nacionalidad,
	            $d->email
	        ];

	        foreach ($values as $i => $val) {
	            $colIndex = $i; // 0-based

	            // Fecha (FN) → formato fecha Excel (dd/mm/yyyy)
	            if ($headers[$i] === 'fn' && !empty($val) && $val != '0000-00-00') {
	                $ts = strtotime($val);
	                if ($ts) {
	                    $excelDate = PHPExcel_Shared_Date::PHPToExcel($ts);
	                    $sheet->setCellValueByColumnAndRow($colIndex, $row, $excelDate);
	                    $sheet->getStyleByColumnAndRow($colIndex, $row)
	                          ->getNumberFormat()->setFormatCode('dd/mm/yyyy');
	                    continue;
	                }
	            }

	            // Columnas que deben ir como Texto (evita notación científica / recortes)
	            if (in_array($colIndex, $textCols, true)) {
	                $sheet->setCellValueExplicitByColumnAndRow(
	                    $colIndex, $row, (string)$val, PHPExcel_Cell_DataType::TYPE_STRING
	                );
	            } else {
	                // Numérico si corresponde, sino texto
	                if (is_numeric($val) && $val !== '' && $val !== null &&
	                    $headers[$i] !== 'cp' // por si te quedaste corto en textCols
	                ) {
	                    $sheet->setCellValueByColumnAndRow($colIndex, $row, (0+$val));
	                } else {
	                    $sheet->setCellValueExplicitByColumnAndRow(
	                        $colIndex, $row, (string)$val, PHPExcel_Cell_DataType::TYPE_STRING
	                    );
	                }
	            }
	        }

	        $row++;
	    }

	    // Auto-ajustar ancho
	    $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers)-1); // Y
	    for ($c = 0; $c < count($headers); $c++) {
	        $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setAutoSize(true);
	    }

	    // 5) Descargar .xlsx
	    $hoy = date("Y-m-d");
	    $filename = strtoupper(INST_NAME) . " - Plan Basico.xlsx";

	    // Limpiar buffers para evitar corrupción del archivo
	    if (ob_get_length()) { ob_end_clean(); }

	    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	    header("Content-Disposition: attachment; filename=\"{$filename}\"");
	    header("Cache-Control: max-age=0");

	    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $writer->save('php://output');
	    exit;

	    break;

	case 'visitar_plata':

	    // --- Config general ---
	    set_time_limit(0);
	    ini_set('memory_limit', '512M');

	    // 1) Traer datos
	    mysql_query("SET NAMES 'utf8'");
	    $sql = "CALL {$base_padron}.lst_padron_visitar_plata()";
	    $rs  = mysql_query($sql) or die(mysql_error() . "<br>" . $sql);

	    // 2) PHPExcel
	    require_once __DIR__ . '/../../Lib/PHPExcel/Classes/PHPExcel.php'; // <-- ajusta la ruta según tu proyecto

	    $objPHPExcel = new PHPExcel();
	    $sheet = $objPHPExcel->getActiveSheet();
	    $sheet->setTitle('Visitar Básico');

	    // 3) Encabezados
	    $headers = [
	        'id_afiliado','id_titular','red','ayn','td','nd','cuil','sexo','edad','plan',
	        'parentesco','localidad','cp','provincia','telefono','telef_celular','estado',
	        'calle','numero','piso','depto','fn','est_civil','nacionalidad','email'
	    ];

	    $col = 0;
	    foreach ($headers as $h) {
	        $sheet->setCellValueByColumnAndRow($col, 1, $h);
	        $col++;
	    }

	    // Estilo encabezados
	    $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
	    #$sheet->getStyle('A1:Y1')->getFill()
	    #      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    #      ->getStartColor()->setRGB('333333');
	    #$sheet->getStyle('A1:Y1')->getFont()->getColor()->setRGB('FFFFFF');

	    // Freeze encabezado
	    $sheet->freezePane('A2');

	    // 4) Cargar filas
	    $row = 2;

	    // Columnas que deben ir como TEXTO para evitar re-formateos (CUIL, doc, CP, teléfonos, etc.)
	    // Índices de columna (0-based): A=0, B=1, ..., Z=25. Ajustamos según headers:
	    $textCols = [
	        2, // red? (si hay códigos alfanuméricos; si es numérica pura, podés quitarla)
	        4, // td
	        5, // nd
	        6, // cuil (MUY importante en texto)
	        12, // cp
	        14, // telefono
	        15, // telef_celular
	        18, // numero (si puede tener ceros a la izquierda o alfanum)
	        19, // piso (puede ser "PB", "1", "S/NUM", etc.)
	        20, // depto (alfanum)
	        24  // email (texto)
	    ];

	    while ($d = mysql_fetch_object($rs)) {

	        $values = [
	            $d->id_afiliado,
	            $d->id_titular,
	            $d->red,
	            $d->ayn,
	            $d->td,
	            $d->nd,
	            $d->cuil,
	            $d->sexo,
	            $d->edad,
	            $d->plan,
	            $d->parentesco,
	            $d->localidad,
	            $d->cp,
	            $d->provincia,
	            $d->telefono,
	            $d->telef_celular,
	            $d->estado,
	            $d->calle,
	            $d->numero,
	            $d->piso,
	            $d->depto,
	            $d->fn,           // luego lo formateamos como fecha
	            $d->est_civil,
	            $d->nacionalidad,
	            $d->email
	        ];

	        foreach ($values as $i => $val) {
	            $colIndex = $i; // 0-based

	            // Fecha (FN) → formato fecha Excel (dd/mm/yyyy)
	            if ($headers[$i] === 'fn' && !empty($val) && $val != '0000-00-00') {
	                $ts = strtotime($val);
	                if ($ts) {
	                    $excelDate = PHPExcel_Shared_Date::PHPToExcel($ts);
	                    $sheet->setCellValueByColumnAndRow($colIndex, $row, $excelDate);
	                    $sheet->getStyleByColumnAndRow($colIndex, $row)
	                          ->getNumberFormat()->setFormatCode('dd/mm/yyyy');
	                    continue;
	                }
	            }

	            // Columnas que deben ir como Texto (evita notación científica / recortes)
	            if (in_array($colIndex, $textCols, true)) {
	                $sheet->setCellValueExplicitByColumnAndRow(
	                    $colIndex, $row, (string)$val, PHPExcel_Cell_DataType::TYPE_STRING
	                );
	            } else {
	                // Numérico si corresponde, sino texto
	                if (is_numeric($val) && $val !== '' && $val !== null &&
	                    $headers[$i] !== 'cp' // por si te quedaste corto en textCols
	                ) {
	                    $sheet->setCellValueByColumnAndRow($colIndex, $row, (0+$val));
	                } else {
	                    $sheet->setCellValueExplicitByColumnAndRow(
	                        $colIndex, $row, (string)$val, PHPExcel_Cell_DataType::TYPE_STRING
	                    );
	                }
	            }
	        }

	        $row++;
	    }

	    // Auto-ajustar ancho
	    $lastColLetter = PHPExcel_Cell::stringFromColumnIndex(count($headers)-1); // Y
	    for ($c = 0; $c < count($headers); $c++) {
	        $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setAutoSize(true);
	    }

	    // 5) Descargar .xlsx
	    $hoy = date("Y-m-d");
	    $filename = strtoupper(INST_NAME) . " - Plan Plata.xlsx";

	    // Limpiar buffers para evitar corrupción del archivo
	    if (ob_get_length()) { ob_end_clean(); }

	    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	    header("Content-Disposition: attachment; filename=\"{$filename}\"");
	    header("Cache-Control: max-age=0");

	    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    $writer->save('php://output');
	    exit;

	    break;

	case 'legajos_subidos':

		$hoy = date("Y-m-d");
		$filename = strtoupper(INST_NAME)."_Legajos_Subidos_".$year."_".$hoy.".xls";

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		header("Content-Type: text/html;charset=utf-8");
		mysql_query("SET NAMES 'utf8'");

		if($clausula_usuarios == "solo_ei"){

			$join_um = " JOIN $base_usuarios.users_modulos um ON um.id_user=u.id AND um.perfil='equipo_interdisciplinario'";

		}
		if($clausula_usuarios == "no_ei"){

			$join_um = " JOIN $base_usuarios.users_modulos um ON um.id_user=u.id AND um.perfil!='equipo_interdisciplinario'";

		}

		$sql="
			SELECT DISTINCT ldo.id,ldo.ubicacion,ldo.nombre_archivo,
				ldo.fechador,
				p.cuil,
				CONCAT(p.apellido,', ',p.nombre) AS ayn,
				u.nombrecompleto as usuario
			FROM $base.logs_documentacion ldo
			JOIN $base_padron.afiliados a ON a.id=ldo.id_afiliado
			JOIN $base_padron.persona p ON p.id=a.id_persona
			JOIN $base_usuarios.users u ON u.id=ldo.id_usuario
			$join_um
			WHERE ubicacion LIKE 'padron/Legajos/".$year."%'
			AND (ldo.fechador BETWEEN '$fecha_desde' AND ADDDATE('$fecha_hasta', INTERVAL 1 DAY))
			ORDER BY fechador DESC;
		";
		#echo $sql;exit();
		$result =mysql_query($sql) or die(mysql_error()."<br>".$sql); 
		

		$tabla="";
		$tabla.="<table border=1>
					<tr style='background-color: black; color: white;'>
						<th>ID</th>                       
						<th>Archivo</th>
						<th>Fecha Subida</th>
						<th>CUIL</th>
						<th>AyN</th>
						<th>Usuario</th>
					</tr>                   
					";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="<tr>
						<td>$d->id</td>
						<td>$d->nombre_archivo</td>
						<td>$d->fechador</td>
						<td>$d->cuil</td>
						<td>$d->ayn</td>
						<td>$d->usuario</td>
					</tr>                   
					";
		}
		
		$tabla.="</table>";                                 
		
		echo "$tabla";

		break;
  default:
    echo json_encode(['status'=>'error','message'=>'parametro inválido']);
    break;
}


?>