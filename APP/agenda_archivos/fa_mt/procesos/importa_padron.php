<?php 
require(__DIR__."/../../../../../Config/Conectar.inc");

/* Datos que pide el formulario de alta 
	 * 
	 * Datos personales
		apellido
		nombre
		cuil 
		fn
		nacionalidad
		estado civil
		sexo
		telef celular 
		email
	 	nben,
	 	gpar,
	Datos afiliacion
		incapacidad
		plan
		observaciones
		fecha de alta manual
		motivo de alta
		tipo beneficiario (tabla ppp1.tipo_beneficiario_titular) 
		situacion revista (tabla ppp1.`situacion_revista`)
		obra social (tabla ppp1.institucion)
		delegacion (tabla ppp1.delegaciones)
		sanatorio (tabla ppp1.sanatorio)
		nro formulario 
	Datos de domicilio
		calle
		numero
		piso (opcional)
		dto (opcional)
		entre calles (opcional)
		localidad (tabla ppp1.localidad)
			- provincia | cp | nombre ;*/
			
if($id_aprobacion==""){
	echo "<center>
				<br>
				<h2>Script de importador general</h2>
			</center>";
	exit();
}

$id_usuario = $_SESSION['id_user'];					

$sql = " SELECT a.* 
			FROM $base_historicos.aprobacion_afiliados_importacion a
			LEFT JOIN $base_historicos.persona p ON a.nd=p.nd 
			WHERE a.estado IN ('inicial','empadronado') 
				AND p.id IS NULL
				AND a.id IN ($id_aprobacion)
				 ";

$result = mysql_query($sql) or die(mysql_error().$sql);

if(mysql_num_rows($result)==0){

	mensaje_persona_existe();
	exit();

}
$n=0;

while($d=mysql_fetch_object($result)){
	// Estableciendo domicilio - Si es titular se inserta, si es familiar se recupera la del titular aunque podria actualizarse pero ahora no.
	if ($d->id_parentesco!=0) {

		$id_domicilio = domicilio_titular($base_historicos, $d);

		if($id_domicilio==0){
			echo "<br><h1>El titular no existe ( $d->nd $d->apellido $d->nombre )"; exit();
		}
	}
	else{

		$id_domicilio = inserta_domicilio($base_historicos, $d);	
	}

	$id_persona = inserta_persona($base_historicos, $d, $id_domicilio, $id_usuario);

	$id_afiliado = inserta_afiliado($base_historicos, $d, $id_persona, $id_usuario) ;

	inserta_historico($base_historicos, $d, $id_usuario, $id_afiliado);

	echo mensaje_ok($base_historicos, $d, $id_aprobacion, $id_usuario);
	$n++;
}
echo"<script>
	alert('Se procesaron $n afiliados');
	window.close();
	window.opener.location.reload();</script>";
exit();// -----------------------

function inserta_domicilio($base_insertar, $d){


	if($d->id_parentesco!=0){

		$sql="SELECT d.*
				FROM $base_insertar.persona p 
				JOIN $base_insertar.afiliados a ON p.id=a.id_persona 
				JOIN $base_insertar.domicilio d ON p.id_domicilio=d.id 
				WHERE a.id_titular=0 
					AND p.cuil=$d->cuil_titular ";

		$rs = mysql_query($sql) or die(mysql_error()."Consulta domicilio titular: ".$sql);
		
		$dd = mysql_fetch_object($rs);

		return $dd->id ;

	}
	else{

		$sql_domicilio= "SELECT * 
							FROM $base_insertar.domicilio
							WHERE id_localidad=$d->id_localidad
								and calle='$d->calle' 
								and numero='$d->numero' 
								and piso='$d->piso' 
								and depto='$d->dto' ";


		//echo "$sql_domicilio<br>";
		$rs_domicilio=mysql_query($sql_domicilio);	
		$filas_domicilio=mysql_num_rows($rs_domicilio);
		// Fin Ver si existe el domicilio para UPDATE, sino INSERT
		if ($filas_domicilio == 0 || $filas_domicilio == null){

			$sql_insert_domicilio="INSERT INTO  $base_insertar.domicilio (id_localidad,calle,numero,piso,depto,telefono)
										VALUES ($d->id_localidad,'$d->calle','$d->numero','$d->piso','$d->dto','$d->telef_celular')";
			//echo "domic: $sql_insert_domicilio <br>";
			if(!mysql_query($sql_insert_domicilio)){echo mysql_error()."<br><br>".$sql_insert_domicilio;}
				
			return mysql_insert_id();

		}
		else{

			return mysql_fetch_object($rs_domicilio)->id ;
		}

	}
	
}

function domicilio_titular($base_insertar, $d){

	$sql_dom = "SELECT id_domicilio
					FROM persona 
					WHERE cuil=$d->cuil_titular ";

	$rs_dom = mysql_query($sql_dom) or die(mysql_error()."<br>ERROR DOMICILIO: recuperando el domicilio del titular");

	if(mysql_num_rows($rs_dom)==0){
		$id_domicilio = 0;
		return $id_domicilio;
	}

	$d_dom = mysql_fetch_object($rs_dom);
	$id_domicilio = $d_dom->id_domicilio;

	return $id_domicilio;

}

function inserta_persona($base_insertar, $d, $id_domicilio, $id_usuario){
	$numcuil=substr($d->sexo, 0, 2);
	if($numcuil == '20'){
		$sexo='M';
	}
	else{
		$sexo='F';
	}
	// INSERT DE PERSONA
	$sql_insert_persona="INSERT INTO $base_insertar.persona (cuil,apellido,nombre,td,nd,
																telef_celular,fn,sexo,
																id_estado_civil,id_nacionalidad,id_domicilio,
																id_usuario,email) 

										VALUES ('$d->cuil','$d->apellido','$d->nombre','$d->td',$d->nd,
												'$d->telefono','$d->fn','$sexo',
												'$d->estado_civil','$d->nacionalidad',$id_domicilio,
												$id_usuario,'$d->email')";
	//echo "persona: $sql_insert_persona <br>";	
	mysql_query($sql_insert_persona) or die(mysql_error().$sql_insert_persona);
	
	return mysql_insert_id();

}

function inserta_afiliado($base_insertar, $d, $id_persona, $id_usuario){


	if($d->id_parentesco!=0){//Familiares
					
		$sql_tit_nben="SELECT a.id AS id_titular,COALESCE(id_tipo_aporte,0) as id_tipo_aporte,id_desreguladora,nben  
							FROM $base_insertar.persona p
							JOIN $base_insertar.afiliados a ON p.id=a.id_persona  
							WHERE a.id_titular = 0
								AND p.cuil = $d->cuil_titular ";
				
		//echo "<br><br>$sql_tit_nben<br><br>";
		$rs_tit_nben=mysql_query($sql_tit_nben) or die(mysql_error()."<br><br>".$sql_tit_nben);
		
		$d_titular = mysql_fetch_object($rs_tit_nben) ;
		$id_titular = $d_titular->id_titular ;
		$tipobeneficiario = $d_titular->id_tipo_aporte ;
		$desreguladora = $d_titular->id_desreguladora ;
		$nben = $d_titular->nben ;
				
		// INSERT FAMILIAR EN TABLA AFILIADOS
		$sql_insert_afiliados="INSERT INTO $base_insertar.afiliados 
											(id_persona,id_titular,id_parentesco,id_usuario,incapacidad,
												nben,gpar,id_desreguladora,
												id_tipo_aporte,observaciones,estado_dia)
									VALUES ($id_persona,$id_titular,$d->id_parentesco,$id_usuario,'$d->incapacidad',
												'$nben','$d->gpar',$desreguladora,
												$tipobeneficiario,'$d->observaciones','ALTA')";
		//echo "familiar: $sql_insert_afiliados <br>";
		mysql_query($sql_insert_afiliados) or die(mysql_error()."<br><br>".$sql_insert_afiliados);
		$id_afiliado= mysql_insert_id();	
		// FIN INSERT FAMILIAR EN TABLA AFILIADOS
	}
	else{// Titulares
		$id_titular=0;
		
		// INSERT TITULAR EN TABLA AFILIADOS
		$sql_insert_afiliados="INSERT INTO $base_insertar.afiliados 
												(id_persona,id_titular,id_parentesco,id_usuario,incapacidad,
													nben,gpar,
													id_tipo_aporte,observaciones,estado_dia)
									VALUES ($id_persona,$id_titular,'$d->id_parentesco','$id_usuario','$d->incapacidad','$d->nben','$d->gpar','$d->tipo_beneficiario_titular','$d->observaciones','ALTA')";
		//echo "familiar: $sql_insert_afiliados <br>";
		if(!mysql_query($sql_insert_afiliados)){echo mysql_error()."<br><br>".$sql_insert_afiliados;}
		$id_afiliado= mysql_insert_id();	
			
		// FIN INSERT TITULAR EN TABLA AFILIADOS
		// INSERT CAMPOS QUE SON DE PREVENTA/OPCION EN TABALA AUXILIAR LUIS
		
			
		
		$sql_insert_tabla_aux_afiliados="INSERT INTO $base_insertar.campos_afiliados_sin_preventa_ni_opcion 
																(id_afiliado,id_obra_social,id_delegacion,
																	id_tipo_beneficiario,id_revista,id_usuario,
																	id_empresa,nro_formulario)

															VALUES ($id_afiliado,$d->obra_social,$d->n_delegacion,
																	$d->tipo_beneficiario_titular,$d->situacion_revista,$id_usuario,$d->id_empresa,'$d->nro_formulario')";
		//echo "familiar: $sql_insert_tabla_aux_afiliados <br>";

		mysql_query($sql_insert_tabla_aux_afiliados) or die(mysql_error()."<br><br>".$sql_insert_tabla_aux_afiliados);
		// FIN INSERT CAMPOS QUE SON DE PREVENTA/OPCION EN TABALA AUXILIAR LUIS
	}

	return $id_afiliado;

}

function inserta_historico($base_insertar, $d, $id_usuario, $id_afiliado){

	switch ($d->archivo_origen) {

		case 'desempleo':
				$id_evento=33 ; //se da de alta por la SSS
			break;

		case 'ddjj':
				$id_evento=8 ; //se da de alta por la SSS
			break;

		case 'aportes':
				$id_evento=8 ; //se da de alta por la SSS
			break;

		case 'altas_rg':
				$id_evento=8 ; //se da de alta por la SSS
			break;
		
		case 'altas_mt':
				$id_evento=8 ; //se da de alta por la SSS
			break;

		default:			
				$id_evento=12 ; //Alta Manual 
			break;
	}

	$sql_inserta_historico="INSERT INTO $base_insertar.historico_afiliados (id_afiliado,id_evento,id_usuario)
																	VALUES ($id_afiliado,$id_evento,$id_usuario)"; //Evento=12: ALTA MANUAL	
	mysql_query($sql_inserta_historico) or die(mysql_error()."<br><br>".$sql_inserta_historico);
	$id_historico = mysql_insert_id();
				
				
	// INSERT DE ALTAS MANUALES
	$sql_insert_alta_manual="INSERT INTO $base_insertar.altas_manuales (id_afiliado,id_historico,fecha_aPartir,
																		motivo,id_usuario) 
										 						VALUES ($id_afiliado,$id_historico,'$d->f_alta',
										 								'$d->motivo_alta',$id_usuario)";			
	//echo "alta manual: $sql_insert_alta_manual <br>";
	if(!mysql_query($sql_insert_alta_manual)){echo mysql_error()."<br><br>".$sql_insert_alta_manual;}
	$id_row=mysql_insert_id();
	// FIN INSERT DE ALTAS MANUALES
				
	// UPDATE DE ID_ROW DEL HISTORICO DE ALTA MANUAL
		$sql_update_id_row="UPDATE $base_insertar.historico_afiliados SET id_row=$id_row WHERE id=$id_historico";
		//echo "update historico: $sql_update_id_row <br>";
		if(!mysql_query($sql_update_id_row)){echo mysql_error()."<br><br>".$sql_update_id_row;}
	// FIN UPDATE DE ID_ROW DEL HISTORICO DE ALTA MANUAL

}

function mensaje_ok($base_insertar, $d, $id_aprobacion, $id_usuario){

	mysql_query("UPDATE $base_insertar.aprobacion_afiliados_importacion 
					SET estado='empadronado'
					WHERE id IN ($id_aprobacion)") or die(mysql_error()."<br>Error al modificar el estado de Aprobacion.");

	mysql_query("INSERT INTO aprobacion_afiliados_estados(id_aprobacion,estado,id_usuario)
					VALUES ($d->id,'empadronado',$id_usuario)") or die(mysql_error()."<br>Error al insertar el estado de Aprobacion.");
	
	$mensaje= "<html>
				<head>							
					<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>		
					<script src='//code.jquery.com/jquery-1.12.4.js'></script>
					<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>					
				</head>
				<body>
					<div class='container'>
						<div class='row'>
							<br>
							<form>
								<div class='alert alert-success'>Terminado con exito!!!</div>
								<table class='table' style='width: 600px; margin: auto;'>
									<tr>
										<th>Afiliado</th>
										<td>
											$d->apellido $d->nombre
										</td>
									</tr>
									<tr>
										<th>DNI</th>
										<td>
											$d->nd 
										</td>
									</tr>
									<tr>
										<th>Estado</th>
										<td>
											Insertado con exito!! 
										</td>
									</tr>
								</table>
								<br />
								<a href='../padron/verafiliado.php?dni=$d->nd'>Ver afiliado </a>
							</form>
						</div>
					</div>
				</body>
			</html>";

	return $mensaje;
	
}
				
function mensaje_persona_existe(){

	echo "<html>
				<head>							
					<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>		
					<script src='//code.jquery.com/jquery-1.12.4.js'></script>
					<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>					
				</head>
				<body>
					<div class='container'>
						<div class='row'>
							<br>							
							<div class='alert alert-danger'>
								<b>ERROR</b> La persona existe en el padron <br>
								<b>Vefifique!!</b> 
							</div>
						</div>
					</div>
				</body>
			</html>";
	
}


?>