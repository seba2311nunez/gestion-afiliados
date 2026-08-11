<?php 
include(__DIR__.'/../../Config/Conectar.inc');

$id_usuario = $_SESSION['iduser'] ;

if($_SESSION["perfil"] == 'consulta_filial'){
	$filial_where = "AND a.filial=".$_SESSION["id_especialidad"];
}

switch ($parametro) {

	//Busqueda de afiliados por parametros
	case 'cuil':
		// $sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
		// 				IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
		// 				cuil,apellido,nombre, d.convenio as desreguladora 
		// 		FROM persona p
		// 		JOIN afiliados a ON p.id=a.id_persona
		// 		JOIN desreguladoras d on d.id=a.id_desreguladora  
		// 		WHERE cuil = '$inp_parametro'
		// 		ORDER BY CONCAT(nben,' ',gpar) ";

		$sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
						IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
						cuil,apellido,p.nombre, d.convenio as desreguladora,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as filial,
						tbt.sigla as tbt,$base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()) AS estado  	 
				FROM persona p
				JOIN afiliados a ON p.id=a.id_persona
				JOIN desreguladoras d on d.id=a.id_desreguladora  
				JOIN $base_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
				LEFT JOIN $base_padron.filial f ON a.filial=f.id 
				WHERE cuil = '$inp_parametro'
				$filial_where
				ORDER BY CONCAT(nben,' ',gpar) ";

		echo genera_json($sql);
		
		break;

	case 'cuit':
		$sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
						IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
						cuil,apellido,p.nombre, d.convenio as desreguladora,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as filial,
						tbt.sigla as tbt,
						$base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()) AS estado  
				FROM persona p
				JOIN afiliados a ON p.id=a.id_persona
				JOIN desreguladoras d on d.id=a.id_desreguladora  
				JOIN $base_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
				LEFT JOIN $base_padron.filial f ON a.filial=f.id 
				WHERE cuil IN (SELECT DISTINCT d FROM $base_dev.ddjj_final WHERE c='$inp_parametro')
				$filial_where
				ORDER BY CONCAT(nben,' ',gpar) ";

		echo genera_json($sql);
		break;
	case 'dni':
		// $sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
		// 				IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
		// 				cuil,apellido,nombre, d.convenio as desreguladora
		// 		FROM persona p
		// 		JOIN afiliados a ON p.id=a.id_persona  
		// 		JOIN desreguladoras d on d.id=a.id_desreguladora  
		// 		WHERE nd = '$inp_parametro'
		// 		ORDER BY CONCAT(nben,' ',gpar) ";

		$sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
						IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
						cuil,apellido,p.nombre, d.convenio as desreguladora,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as filial,
						tbt.sigla as tbt,$base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()) AS estado 
				FROM persona p
				JOIN afiliados a ON p.id=a.id_persona  
				JOIN desreguladoras d on d.id=a.id_desreguladora  
				JOIN $base_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
				LEFT JOIN $base_padron.filial f ON a.filial=f.id 
				WHERE nd = '$inp_parametro'
				$filial_where
				ORDER BY CONCAT(nben,' ',gpar) ";

		echo genera_json($sql);

		break;

	case 'beneficiario':
	
		// $sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
		// 				IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
		// 				cuil,apellido,nombre, d.convenio as desreguladora
		// 		FROM persona p
		// 		JOIN afiliados a ON p.id=a.id_persona  
		// 		JOIN desreguladoras d on d.id=a.id_desreguladora  
		// 		WHERE CONCAT(nben,' ',gpar) LIKE '%$inp_parametro%'
		// 		ORDER BY CONCAT(nben,' ',gpar) ";

		$sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
						IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
						cuil,apellido,p.nombre, d.convenio as desreguladora,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as filial,
						tbt.sigla as tbt,$base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()) AS estado 
				FROM persona p
				JOIN afiliados a ON p.id=a.id_persona  
				JOIN desreguladoras d on d.id=a.id_desreguladora  
				JOIN $base_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
				LEFT JOIN $base_padron.filial f ON a.filial=f.id 
				WHERE CONCAT(nben,' ',gpar) LIKE '%$inp_parametro%'
				$filial_where
				ORDER BY CONCAT(nben,' ',gpar) ";

		echo genera_json($sql);

	break;	

	case 'ayn':
	
		// $sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
		// 				IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
		// 				cuil,apellido,nombre, d.convenio as desreguladora 
		// 		FROM persona p
		// 		JOIN afiliados a ON p.id=a.id_persona  
		// 		JOIN desreguladoras d on d.id=a.id_desreguladora  
		// 		WHERE CONCAT(apellido,' ',nombre) LIKE '%$inp_parametro%'
		// 		ORDER BY CONCAT(apellido,' ',nombre) ";

		$sql="SELECT a.id AS id_afliliado,CONCAT(a.nben,'/',a.gpar) as nben,
						IF(a.id_titular=0,a.id,a.id_titular) AS id_titular,
						cuil,apellido,p.nombre, d.convenio as desreguladora,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as filial,
						tbt.sigla as tbt,$base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()) AS estado   
				FROM persona p
				JOIN afiliados a ON p.id=a.id_persona  
				JOIN desreguladoras d on d.id=a.id_desreguladora  
				JOIN $base_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
				LEFT JOIN $base_padron.filial f ON a.filial=f.id 
				
				WHERE CONCAT(apellido,' ',p.nombre) LIKE '%$inp_parametro%'
				$filial_where
				ORDER BY CONCAT(apellido,' ',p.nombre) ";

		echo genera_json($sql);
	
	break;

	case 'grabar_busqueda':
		# code...

		$insert_b = "INSERT INTO $base_historicos.`logs_sistemas`(descripcion,detalle,id_usuario)
						VALUES('Busqueda de afiliado','Titular : $id_titular | cuil: $cuil',$id_usuario)";
		mysql_query($insert_b) or die(mysql_error());

		break;


	//FIN - Busqueda de afiliados por parametros	

	//Consultas
	case 'prox_nben':
		# code...

		if($id_tipo_aporte==0){
			$limite= 100000;
		}elseif ($id_tipo_aporte==4) {
			$limite= 5000000;
		}else {
			$limite=10000000;
		}

		//echo $limite;exit();
		$query = "SELECT (nben*1)+1 AS prox_nben
					FROM afiliados
					WHERE nben!='nben'
						AND nben*1<$limite
						AND id_desreguladora=$id_desreguladora
						AND id_tipo_aporte=$id_tipo_aporte
					ORDER BY nben*1 DESC ";
		//echo $query;exit();
		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		$row = mysql_fetch_assoc($result);

		$json[] = array(
				
			'prox_nben' => $row['prox_nben']
			       
      	);

		echo json_encode($json);			

		break;

	//Graba nuevo afiliado

	case 'dni_existe':
			# code...
			$query = "SELECT CONCAT(apellido,' ',nombre) AS ayn
							FROM persona 
							WHERE nd = $dni ";
			$result = mysql_query($query);

			if(mysql_num_rows($result)>0){
				$d = mysql_fetch_object($result);

				echo $d->ayn;
			}
			else{
				echo "";
			}

			

			break;	

	case 'alta_afiliado':
	
		$json = array();

		// Domicilio
		$sql_domicilio= "SELECT * 
							FROM domicilio
								WHERE id_localidad=$localidad  
										and	calle='$calle'  
										and piso='$piso'  
										and numero='$numero' 
										and depto='$departamento' ";

		$rs_domicilio=mysql_query($sql_domicilio) or die(mysql_error()."<br>$sql_domicilio");	
		$filas_domicilio=@mysql_num_rows($rs_domicilio);

		
		if ($filas_domicilio == 0 || $filas_domicilio == null){
			$sql_insert_domicilio="INSERT INTO  domicilio (id_localidad,calle,piso,numero,depto,telefono)
										VALUES ($localidad,'$calle','$piso',$numero,'$departamento','$telefono')";
			//echo "domic: $sql_insert_domicilio <br>";
			if(!mysql_query($sql_insert_domicilio)){echo mysql_error()."<br>$sql_insert_domicilio";}
			$id_domicilio= mysql_insert_id();	

		}else {
			$data_domicilio=mysql_fetch_object($rs_domicilio);
			$id_domicilio=$data_domicilio->id;
			$sql_update_domicilio="UPDATE domicilio SET telefono='$telefono' WHERE id=$id_domicilio";
			//echo "domic: $sql_update_domicilio <br>";
			if(!mysql_query($sql_update_domicilio)){die (mysql_error()."<br>$sql_domicilio");}
		}
		// FIN DOMICILIO

		// Persona
		$sql_insert_persona="INSERT INTO persona (cuil,apellido,nombre,td,nd,telef_celular,fn,sexo,
													id_estado_civil,id_nacionalidad,id_domicilio,id_usuario,email) 
								VALUES ('$cuil',UPPER('$apellido'),UPPER('$nombre'),'DNI',$dni,'$telefono','$fn','$sexo',
										'$estado_civil','$nacionalidad',$id_domicilio,$id_usuario,'$email')";
		//echo "persona: $sql_insert_persona <br>";
		if(!mysql_query($sql_insert_persona)){echo mysql_error();}
		$id_persona= mysql_insert_id();
		// FIN Persona	

		// Afiliado

		if(!$seccional){
			$seccional=1;
		}

		$sql_insert_afiliados="INSERT INTO afiliados (id_persona,id_titular,id_parentesco,id_usuario,
														incapacidad,observaciones,id_tipo_aporte,
														id_desreguladora,nben,gpar,estado_dia,filial,id_revista)
										VALUES ($id_persona,0,0,$id_usuario,
												'$incapacidad','$observacion',$tbt,
												'$desreguladora','$nben','$gpar','ALTA','$seccional',$revista)";
			//echo "familiar: $sql_insert_afiliados <br>";
			if(!mysql_query($sql_insert_afiliados)){echo mysql_error().$sql_insert_afiliados;}
			$id_afiliado= mysql_insert_id();	
		// FIN AFILIADOS


		// INSERT CAMPOS QUE SON DE PREVENTA/OPCION EN TABALA AUXILIAR LUIS
			/*
		$sql_insert_tabla_aux_afiliados="INSERT INTO campos_afiliados_sin_preventa_ni_opcion (id_afiliado,id_obra_social,id_delegacion,
																								id_tipo_beneficiario,id_revista,id_empresa,
																								id_usuario)
																VALUES ($id_afiliado,1,5023,
																		$tbt,$revista,1,
																		$id_usuario)";
		mysql_query($sql_insert_tabla_aux_afiliados) or die ($sql_insert_tabla_aux_afiliados);
		*/

		//Inserto historico 
		$insert_historico = "INSERT INTO $base_historicos.`cambios_manuales`(id_afiliado,id_evento,fecha,observacion,id_usuario)
								VALUES ($id_afiliado,12,'$fecha_alta','$observacion',id_usuario)";

		mysql_query($insert_historico) or die($insert_historico);

		
		$json[] = array(
				'estado' => "ok",						        		
				'id_afiliado' => $id_afiliado
				       
	    );
		echo json_encode($json);

		break;
	//FIN - Graba nuevo afiliado
	case 'buscar_ultimos_nben':


		if($id_tbt==0){
			$limite= 100000;
		}elseif ($id_tbt==4) {
			$limite= 5000000;
		}else {
			$limite=10000000;
		}

		$sql="SELECT af.id,IF(af.id_titular=0,af.id,af.id_titular) as id_titular,p.cuil,CONCAT(af.`nben`,'/',af.`gpar`) AS nben,par.`parentesco` as parentesco,CONCAT(p.apellido,', ',p.`nombre`) AS ayn
		FROM $base_padron.afiliados af
		JOIN $base_padron.persona p ON p.id=af.id_persona
		JOIN $base_padron.parentesco par ON par.id=af.id_parentesco 
		WHERE af.id_tipo_aporte=$id_tbt AND af.id_desreguladora=1 AND nben*1<$limite AND nben REGEXP '^[0-9]+$' AND (af.nben!='nben' or af.nben IS NULL)
		ORDER BY af.nben*1 DESC
		LIMIT 30";

		$rs = mysql_query($sql) or die(mysql_error());

		$json = array();

		while ($row = mysql_fetch_assoc($rs)){
			$json[] = array(
				"nben" => $row['nben'],
				"cuil" => $row['cuil'],
				"ayn" => $row['ayn'],
				"parentesco" => $row['parentesco'],
				"id_afiliado" => $row['id'],
				"id_titular" => $row['id_titular']
			);
		}
		echo json_encode($json);
		break;

}

function genera_json($sql){

	$rs=mysql_query($sql) or die(mysql_error().$sql);

	if(mysql_num_rows($rs)==0){
		$json[] = array(
					'estado'=> 'error'
		);
	}
	else{
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = array(
					'estado' => 'ok',
					'id_afliliado' => $row['id_afliliado'],
					'id_titular' => $row['id_titular'],
					'nben' => $row['nben'],
					'cuil' => $row['cuil'],
					'apellido' => $row['apellido'],
					'nombre' => $row['nombre'],
					'desreguladora' => $row['desreguladora'],
					'filial' => $row['filial'],					
					'tbt' => $row['tbt'],
					'estado' => $row['estado']
			);
		}
		
	}

	return json_encode($json);
}

?>