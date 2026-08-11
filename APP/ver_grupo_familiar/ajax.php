<?php 
header('Access-Control-Allow-Origin: *');
include("../../Config/Conectar.inc");
$id_usuario = $_SESSION["iduser"] ;
if(!$id_usuario){
	$id_usuario=$id_user; //Por si viene de extranet
}

if(!$id_usuario){
	$json = array('error','No sesion');
	echo json_encode($json);exit();
}
$ce_archivo = (isset($_FILES['ce_archivo'])) ? $_FILES['ce_archivo']: '';
switch ($parametro) {
	case 'buscar_localidad':

		$partes_texto = preg_split("/[\s,]+/", $keyword); // Separo por espacios o coma

		foreach ($partes_texto as $parte) {
			$having_clause .= "AND text LIKE '%$parte%' ";
		}

		$sql="SELECT l.id,CONCAT('<b>Provincia:</b> ',TRIM(pr.nom),' - ','<b>Loc.:</b> ',TRIM(l.nombreLoca),' - ','<b>CP:</b> ',l.cp) AS text
			FROM $base_padron.localidad l 
			JOIN $base_padron.provincia pr ON pr.cod=l.provincia
			WHERE 1=1
			HAVING 1=1 $having_clause
			ORDER BY 2";

		$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json_obj = array();
		while ($row = mysql_fetch_assoc($rs)){
			$json_obj[] = $row;
		}

		$json = array('results' => $json_obj);
		echo json_encode($json);
		break;
	case 'buscar_localidad_especifica':

		$sql="SELECT l.id,CONCAT('<b>Provincia:</b> ',TRIM(pr.nom),' - ','<b>Loc.:</b> ',TRIM(l.nombreLoca),' - ','<b>CP:</b> ',l.cp) AS text
			FROM $base_padron.localidad l 
			JOIN $base_padron.provincia pr ON pr.cod=l.provincia
			WHERE l.id=$id
			ORDER BY 1";

		$rs=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json_obj = array();
		while ($row = mysql_fetch_assoc($rs)){
			$json_obj[] = $row;
		}
		echo json_encode($json_obj);
		break;
	case 'consulta_cuil':
		# code...
		if($dni=="" || $sexo==""){
			$json[] = array(
				'estado' => 0,		
				'cuil' => 'Debe enviar dni y sexo'
				       
	     	);
		}
		else{

			$query = "SELECT prueba.consulta_cuil($dni,'$sexo') AS cuil";		
			$result=mysql_query($query) or die(mysql_error()."<br>".$query);

			$json = array();

			if(mysql_num_rows($result)==0){

				$json[] = array(
					'estado' => 0,		
					'cuil' => 'No encontrado'
					       
		     	);
			}
			else{

				$row = mysql_fetch_assoc($result);

				$json[] = array(
					'estado' => 1,		
					'cuil' => $row['cuil']
					       
		      	);
			}
		}

		echo json_encode($json);

		break;
	
	case 'historico_stored':

		$json = array();
		$sql="CALL $base_padron.historico_movimientos($cuil)";
		
		mysql_query($sql) or die(mysql_error()." ".$sql);

		$sql_historico="SELECT *,DATE_FORMAT(fecha_movimiento,'%d/%m/%Y') AS fec_mov,DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fechador_formateado
							FROM $base_padron.tmp_movimientos_cuil 
							ORDER BY fecha_movimiento DESC,fechador DESC ";

		$result=mysql_query($sql_historico) or die(mysql_error()."<br>".$sql_historico);

		if(mysql_num_rows($result)==0){
			$json = array('estado' => 'No se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {

				$manual=$row['manual'];

				if($manual==0){
					$movimiento_de='Externo';
				}
				else{
					$movimiento_de='Propio';
				}
			
			    $json[] = array(
						'movimiento_de' => $movimiento_de,		
						'movimiento' => $row['movimiento'],	
						'cuil' => $row['cuil'],
						'tipo' => $row['tipo'],						        		
						'fecha_movimiento' => $row['fecha_movimiento'],
						'fec_mov' => $row['fec_mov'],
						'observacion' => $row['observacion'],
						'fechador' => $row['fechador_formateado'],
						'id_usuario' => $row['id_usuario'],
						'usuario' => $row['usuario'],
						'id_cambio_manual' => $row['id_cambio_manual']
						       
			      );
			}
		}


		echo json_encode($json);

		break;

	case 'historico_stored_afil':

			$json = array();
			
			
			$sql_historico="SELECT  manual,
									descripcion AS movimiento,
									estado AS tipo,
									fecha_aPartir AS fecha_movimiento,
									DATE_FORMAT(fecha_aPartir,'%d/%m/%Y') AS fec_mov,
									descripcion AS observacion_ok
								#select *
								FROM $base_historicos.`_historico_afiliados`
								WHERE id_afiliado=$id_afiliado
								ORDER BY fecha_aPartir DESC ";

			$result=mysql_query($sql_historico) or die(mysql_error()."<br>".$sql_historico);

			if(mysql_num_rows($result)==0){
				$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
			}
			else{

				while ($row = mysql_fetch_assoc($result)) {

					$manual=$row['manual'];

					if($manual==0){
						$movimiento_de='Externo';
					}
					else{
						$movimiento_de='Propio';
					}
				
				    $json[] = array(
							'movimiento_de' => $movimiento_de,		
							'movimiento' => $row['movimiento'],	
							'tipo' => $row['tipo'],						        		
							'fecha_movimiento' => $row['fecha_movimiento'],
							'fec_mov' => $row['fec_mov'],
							'observacion' => $row['observacion_ok']
							#,'id_cambio_manual' => $row['id_cambio_manual']
							       
				      );
				}
			}


			echo json_encode($json);

		break;
	

	case 'consulta_cuil_titular':
		# code...

		$query = "SELECT p.cuil AS cuil_titular
					FROM afiliados a
					JOIN persona p ON a.id_persona=p.id 
					WHERE a.id = $id_titular ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		if(mysql_num_rows($result)==0){

			$json[] = array(
				'estado' => 0,		
				'cuil_titular' => 'No encontrado'
				       
	     	);
		}
		else{

			$row = mysql_fetch_assoc($result);

			$json[] = array(
				'estado' => 1,		
				'cuil_titular' => $row['cuil_titular']
				       
	      	);
		}

		echo json_encode($json);

		break;

	

	//Selectores
	case 'desreguladoras':
		
		$query = "SELECT *
					FROM desreguladoras ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'convenio' => $row['convenio']
					       
		      );
		}

		echo json_encode($json);


		break;

	case 'parentescos':

	    $query = "SELECT *
	              FROM parentesco
	              WHERE id NOT IN (11)
	              ORDER BY id";

	    $result=mysql_query($query) or die(mysql_error()."<br>".$query);

	    $json = array();

	    while ($row = mysql_fetch_assoc($result)) {
	        $json[] = array(
	            'id' => $row['id'],
	            'parentesco' => $row['parentesco']
	        );
	    }

	    echo json_encode($json);
	    break;

	case 'parentescos_familiar':

	    $query = "SELECT *
	              FROM parentesco
	              WHERE parentesco != 'Titular'
	              AND id NOT IN (11)
	              ORDER BY id";

	    $result=mysql_query($query) or die(mysql_error()."<br>".$query);

	    $json = array();

	    while ($row = mysql_fetch_assoc($result)) {
	        $json[] = array(
	            'id' => $row['id'],
	            'parentesco' => $row['parentesco']
	        );
	    }

	    echo json_encode($json);
	    break;

	case 'estado_civil':
		
		$query = "SELECT *
					FROM estadocivil ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'estado_civil' => $row['est_civil']
					       
		      );
		}

		echo json_encode($json);

		break;

	case 'nacionalidad':
		# code...
		$query = "SELECT * FROM pais ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'nacionalidad' => $row['lugar_nac']
					       
		      );
		}

		echo json_encode($json);
		break;

	case 'provincia':
		# code...
		$query = "SELECT * FROM provincia";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['cod'],		
					'provincia' => $row['nom']
					       
		      );
		}

		echo json_encode($json);

		break;

	case 'localidad':
		# code...
		$query = "SELECT id,cp,nombreLoca,id_sanatorio,zona_liquidacion,partido,
							CONCAT(cp,' - ',nombreLoca) AS localidad
						FROM localidad 
						WHERE provincia=$provincia
						ORDER BY cp";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],
					'cp' => $row['cp'],
					'nombreLoca' => $row['nombreLoca'],		
					'id_sanatorio' => $row['id_sanatorio'],		
					'zona_liquidacion' => $row['zona_liquidacion'],		
					'partido' => $row['partido'],		
					'localidad' => $row['localidad']
					       
		      );
		}

		echo json_encode($json);

		break;


	case 'lst_datos_aficionales':
		// code...
		# code...
		$query = "SELECT ad.*,
							COALESCE(ad.opciones, '') AS copciones,
						    COALESCE(ad.precio, '') AS cprecio,
						    COALESCE(ad.observaciones, '') AS cobservaciones,
						    COALESCE(ad.documentacion, '') AS cdocumentacion,
							da.id AS id_,da.servicio,
							da.`fecha_inicio_`,da.`opciones_`,da.`fecha_limite_`,da.`precio_`,da.`observaciones_`,da.`documentacion_`,u.usuario,ad.`fechador_carga`
						FROM $base_padron.`afiliado_datos_adicionales` ad
						JOIN $base_usuarios.users u ON ad.id_usuario=u.id 
						JOIN $base_padron.`datos_adicionales` da ON ad.`id_datos_adicionales`=da.id  
						WHERE ad.id_afiliado=$id_afiliado ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = $row;
			}
		}

		echo json_encode($json);
		break;

	case 'mod_dato_adicional':
		// code...
		$update = "UPDATE $base_padron.afiliado_datos_adicionales
			SET fecha_inicio='$fecha_inicio',
				fecha_limite='$fecha_limite',
				precio='$precio',
				observaciones='$observacion'
			WHERE id=$id";

		if(mysql_query($update)){
			echo "ok";
		}
		else{
			echo "ERROR-".mysql_error();
		}

		break;

	case 'dt_adicional_ver_historial':
		// code...
		$query = "SELECT 
					    id,
					    id_propio,
					    DATE_FORMAT(fecha_modificacion, '%d/%m/%Y %H:%i') AS fecha_modificacion,
					    COALESCE(fecha_inicio, '') AS fecha_inicio,
					    COALESCE(fecha_limite, '') AS fecha_limite,
					    COALESCE(precio, '') AS precio,
					    COALESCE(observaciones, '') AS observaciones,
					    documentacion,
					    id_usuario AS usuario
            		FROM ".$base."_trigger.historial_afiliado_datos_adicionales 
            		WHERE id = $id";

        $result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = $row;
			}
		}

		echo json_encode($json);

		break;

	//Consultas
	case 'ver_grupo_familiar':

		$query = "CALL ver_grupo_familiar($id_titular)";
		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {

			$json[] = $row;
			#print_r($json);
		}

		echo json_encode($json);

		break;
	
	case 'info_principal':
		
		$json = array();

		$sql = "SELECT d.convenio,t.beneficiario,d.id as id_desreguladora
					FROM afiliados a
					JOIN desreguladoras d ON a.id_desreguladora=d.id 
					JOIN tipo_beneficiario_titular t ON a.id_tipo_aporte=t.id 
					WHERE a.id = $id_titular ";

		$sql ="DROP TEMPORARY TABLE IF EXISTS $base_padron.tmp_consulta";

		mysql_query($sql) or die (mysql_error()." ".$sql) ;

		$sql="CREATE TEMPORARY TABLE $base_padron.tmp_consulta
						SELECT d.id as id_desreguladora,d.convenio,d.convenio_real,t.beneficiario,t.sigla,
							$base_padron.ult_periodo_y_empresa_rg(t.sigla,p.cuil) AS tt
						FROM afiliados a
						JOIN persona p ON a.id_persona=p.id 
						JOIN desreguladoras d ON a.id_desreguladora=d.id 
						JOIN tipo_beneficiario_titular t ON a.id_tipo_aporte=t.id 
						
						WHERE a.id = $id_titular ";
		mysql_query($sql) or die(mysql_error()." ".$sql);

		$sql = "SELECT c.*,IF(sigla!='RG','No corresponde',e.nombre) AS empresa
					FROM $base_padron.tmp_consulta c
					LEFT JOIN $base.empresas e ON e.cuit COLLATE latin1_swedish_ci=MID(tt,1,11)";

		$result=mysql_query($sql) or die(mysql_error()." ".$sql);

		if(mysql_num_rows($result)==0){
			
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
			    		'id_desreguladora' => $row['id_desreguladora'],	
						'desreguladora' => $row['convenio'],
						'convenio_real' => $row['convenio_real'],			
						'beneficiario' => $row['beneficiario'],
						'sigla_tbt' => $row['sigla'],
						'ultimo_pye' => $row['tt'],
						'empresa' => $row['empresa']
						       
			      );
			}
		}

		echo json_encode($json);

		break;

	case 'es_desempleado':
			
			$json = array();

			$sql = "SELECT DATE_FORMAT(MIN(l.archivo),'%m/%Y') AS per_min,
							DATE_FORMAT(MAX(l.archivo),'%m/%Y') AS per_max
						FROM $base_historicos.desempleo d
						JOIN $base_historicos.lotes l ON d.id_lote=l.id 
						JOIN $base_padron.persona p ON d.cuil COLLATE latin1_general_ci=p.cuil 
						JOIN $base_padron.afiliados a ON p.id=a.id_persona AND id_titular=0
						WHERE a.id = $id_titular ";	
						
			$result = mysql_query($sql) or die(mysql_error());
			
			$row = mysql_fetch_assoc($result);
			
			if($row['per_min']==""){
				$estado="no";
			}
			else{
				$estado="si";
			}

			$json[] = array(						
							'estado' => $estado,
							'per_min' => $row['per_min'],
							'per_max' => $row['per_max']					       
				      );
						
			echo json_encode($json);

	break;

	case 'datos_personales':
		
		$json = array();

		$query = "SELECT a.id_persona,
							nd,sexo,cuil,a.id_desreguladora,d.convenio as desreguladora,
							nben,gpar,
							p.apellido,p.nombre,
							estado_afiliado_nuevo_test(a.id,CURDATE()) AS estado_afiliado,
							a.id_parentesco,pa.parentesco,
							p.fn,
							p.id_estado_civil,e.est_civil,
							a.incapacidad,p.telef_celular,p.email 
						FROM $base_padron.persona p
						JOIN $base_padron.afiliados a ON p.id=a.id_persona
						JOIN $base_padron.desreguladoras d ON a.id_desreguladora=d.id 
						JOIN $base_padron.parentesco pa ON a.id_parentesco=pa.id 
						JOIN $base_padron.estadocivil e ON p.id_estado_civil=e.id 
						WHERE a.id = $id_afiliado ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
						'id_persona' => $row['id_persona'],		
						'nd' => $row['nd'],	
						'sexo' => $row['sexo'],						        		
						'cuil' => $row['cuil'],						        		
						'id_desreguladora' => $row['id_desreguladora'],	    		
						'desreguladora' => $row['desreguladora'],						        		
						'nben' => $row['nben'],						        		
						'gpar' => $row['gpar'],						        		
						'apellido' => $row['apellido'],	
						'nombre' => $row['nombre'],				
						'estado_afiliado' => $row['estado_afiliado'],	
						'id_parentesco' => $row['id_parentesco'],						        		
						'parentesco' => $row['parentesco'],						        		
						'fn' => $row['fn'],						        		
						'id_estado_civil' => $row['id_estado_civil'],							        		
						'est_civil' => $row['est_civil'],						        		
						'incapacidad' => $row['incapacidad'],
						'telefono' => $row['telef_celular'],
						'email' => $row['email']
						       
			      );
			}
		}

		echo json_encode($json);

		break;

	case 'domicilio':
		# code...

		$json = array();

		$query = "SELECT l.id AS id_localidad,l.nombreLoca,
							p.id_domicilio,
							l.cp,pr.nom AS provincia,pr.cod as id_provincia,
							d.calle,d.numero,d.piso,d.depto
						FROM persona p
						JOIN afiliados a ON p.id=a.id_persona 
						JOIN domicilio d ON p.id_domicilio=d.id 
						JOIN localidad l ON d.id_localidad=l.id 
						JOIN provincia pr ON l.provincia=pr.cod
						WHERE a.id = $id_afiliado ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
						'id_localidad' => $row['id_localidad'],
						'id_domicilio' => $row['id_domicilio'],		
						'localidad' => $row['nombreLoca'],	
						'cp' => $row['cp'],						        		
						'provincia' => $row['provincia'],						        		
						'id_provincia' => $row['id_provincia'],	    		
						'calle' => $row['calle'],						        		
						'numero' => $row['numero'],						        		
						'piso' => $row['piso'],						        		
						'depto' => $row['depto'],	
						'nombre' => $row['nombre']
						       
			      );
			}
		}

		echo json_encode($json);

		break;

	case 'datos_afiliacion':
		# code...
		$query = "SELECT tbt.id AS id_tbt,tbt.beneficiario,
							sr.id AS id_revista,sr.revista,
							f.id AS id_filial,f.nombre AS filial,
							a.id_plan_medico
							#,d.id AS id_delegacion,d.`Deleg_nombre` AS delegacion
						FROM afiliados a 
						JOIN tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
						JOIN situacion_revista sr ON sr.id=a.id_revista 
						#JOIN delegaciones d ON c.id_delegacion=d.id 
						LEFT JOIN filial f ON a.filial=f.id 
						WHERE a.id = $id_titular ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = $row;
			}
		}

		echo json_encode($json);

		break;

	case 'fechas_alt_baj':
		# code...
		$query = "SELECT ult_fecha_alta($id_afiliado) AS ult_fecha_alta, ult_fecha_baja($id_afiliado) AS ult_fecha_baja";
		
		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
						'ult_fecha_alta' => $row['ult_fecha_alta'],		
						'ult_fecha_baja' => $row['ult_fecha_baja']
						       
			      );
			}
		}

		echo json_encode($json);

		break;

	case 'historico':
		
		$json = array();

		$sql_historico="SELECT hi.id_evento,e.descripcion,
								IF(b.motivo IS NULL,IF(a.motivo IS NULL,COALESCE(m.motivo_modificacion,'--'),a.motivo),b.motivo) AS motivo_modificacion,
								COALESCE(m.nombre_campo,'--') AS nombre_campo,COALESCE(m.valor_anterior,'--') AS valor_anterior,
								SUBSTR(hi.fechador,1,10)AS fechador,	
								usuario	 
							
							FROM ( 
									( SELECT * FROM historico_afiliados WHERE id_afiliado=$id_afiliado ) hi
									JOIN eventos_afiliados e ON hi.id_evento=e.id
									LEFT JOIN motivo_modificacion_campos m ON hi.id=m.id_historico
									LEFT JOIN bajas_manuales b ON hi.id=b.id_historico
									LEFT JOIN altas_manuales a ON hi.id=a.id_historico
									LEFT JOIN users u ON u.id=hi.id_usuario		
									
								) 

							ORDER BY hi.fechador DESC";
		
		$result=mysql_query($sql_historico) or die(mysql_error()."<br>".$sql_historico);

		if(mysql_num_rows($result)==0){
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
						'descripcion' => $row['descripcion'],		
						'fechador' => $row['fechador'],	
						'motivo_modificacion' => $row['motivo_modificacion'],						        		
						'nombre_campo' => $row['nombre_campo'],						        		
						'valor_anterior' => $row['valor_anterior'],	    		
						'usuario' => $row['usuario']
						       
			      );
			}
		}

		echo json_encode($json);

		break;

	case 'otros_titulares':
		// code...

		$sql = "SELECT a.id AS id_afiliado,
						p.cuil,
						CONCAT(p.apellido,' ',p.nombre) AS ayn,
						d.convenio 
					FROM $base_padron.persona p 
					JOIN $base_padron.afiliados a ON p.id=a.id_persona 
					JOIN $base_padron.desreguladoras d ON a.id_desreguladora=d.id 
					WHERE a.id_persona=p.id
						AND a.id_parentesco=0
						AND a.id_titular!=$id_titular 
						and ( p.cuil LIKE '%$inp_dato%' OR CONCAT(p.apellido,' ',p.nombre) LIKE '%$inp_dato%' )
					LIMIT 10 ";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id_afiliado' => $row['id_afiliado'],						        		
					'cuil' => $row['cuil'],						        		
					'ayn' => $row['ayn'],						        		
					'desreguladora' => $row['convenio']
					       
		      );
		}

		echo json_encode($json);

		break;

	//Modificadores de datos
	case 'graba_datos_personales':
		# code...

		$query = "UPDATE persona p
					JOIN afiliados a ON p.id=a.id_persona

					SET sexo='$sexo',cuil='$cuil',id_desreguladora=$desreguladora,
						nben='$nben',gpar='$gpar',apellido=UPPER('$apellido'),nombre=UPPER('$nombre'),
						id_parentesco=$parentesco,fn='$fn',id_estado_civil=$estado_civil,
						incapacidad='$incapacidad',telef_celular='$telefono',email='$email',
						nd=$nd
					WHERE a.id = $id_afiliado ";

		//echo "$query"; exit();
		mysql_query($query) or die(mysql_error().$query);
		

		$campos = array('desreguladora','nd','sexo','nben','gpar','apellido','nombre','parentesco','fn','estado_civil','incapacidad','telefono','email');



		foreach ($campos as $campo) {

			$_dto_ant = "$campo"."_ant";
			$dto_ant = $$_dto_ant ;
			
			$_dto_act = "$campo";
			$dto_act = $$_dto_act ;

			#echo "Compara datos cambios. dato anterior: ".$dto_ant." actual: ".$dto_act; exit();

			if($dto_act!=$dto_ant){

				motivo_modificacion_campos($id_usuario,$id_afiliado,$campo,$dto_ant,$dto_act,$observacion_desreguladora);

				if($parentesco=="0" ){//Titular 

					$campos_titular = array('desreguladora');



					foreach ($campos_titular as $campo_titular) {

						$sql = "SELECT id FROM ".N_BASE_PADRON.".afiliados WHERE id_titular=$id_afiliado";
						$rs = mysql_query($sql) or die(mysql_error() . " " . $sql );

						while ( $data = mysql_fetch_object($rs) ){
							motivo_modificacion_campos($id_usuario,$data->id,$campo_titular,$dto_ant,$dto_act,$observacion_desreguladora);
						}

						if($dto_ant==$campo_titular){

							$sql = "UPDATE ".N_BASE_PADRON."afiliados SET id_desreguladora=$dto_act WHERE id_titular=$id_afiliado ";
							$rs = mysql_query($sql) or die(mysql_error() . " " . $sql );
						}
						else{
						}
						
						

						
					}

					
				}

			}

			
		}

		

		echo "ok"; exit();

		break;
	
	case 'validar_dni':
		
		$sql="SELECT * FROM $base_padron.persona p JOIN $base_padron.afiliados a on a.id_persona=p.id WHERE p.nd=$nd AND a.id=$id_afiliado";
		if(mysql_num_rows(mysql_query($sql)) > 0){
			$validar = true;
		}else{
			$sql="SELECT * FROM $base_padron.persona p JOIN $base_padron.afiliados a on a.id_persona=p.id WHERE p.nd=$nd AND a.id!=$id_afiliado";
			if(mysql_num_rows(mysql_query($sql)) == 0){
				$validar = true;
			}else{
				$validar = false;
			}
		}
		$json = array('valid' => $validar);
		echo json_encode($json);
		break;

	case 'graba_domicilio':
		# code...
		$query = "UPDATE persona p 
					JOIN afiliados a ON p.id=a.id_persona 
					JOIN domicilio d ON p.id_domicilio=d.id 
					SET d.id_localidad=$id_localidad,
							d.calle='$calle',d.numero='$numero',d.piso='$piso',d.depto='$departamento'
					WHERE a.id = $id_afiliado 
						AND p.id_domicilio = $id_domicilio_afiliado ";

		//echo "$query"; exit();
		mysql_query($query) or die(mysql_error().$query);
			
		$campos = array('id_localidad','calle','numero','piso','departamento');

		foreach ($campos as $campo) {
			$_dto_ant = "$campo"."_ant";
			$dto_ant = $$_dto_ant ;
			
			$_dto_act = "$campo";
			$dto_act = $$_dto_act ;

			if($dto_act!=$dto_ant){
				//echo "<br>comparo $campo y son diferentes : <br>Anterior: $dto_ant <br>Actual: $dto_act";
				motivo_modificacion_campos($id_usuario,$id_afiliado,$campo,$dto_ant,$dto_act,'');
			}
			else{
				//echo "<br>comparo $campo y son iguales : <br>Anterior: $dto_ant <br>Actual: $dto_act";
			}
			
		}

		echo "ok"; 

		break;

	case 'graba_datos_afiliacion':
		# code...
		//$query = "UPDATE campos_afiliados_sin_preventa_ni_opcion
		//			SET id_tipo_beneficiario = $tbt,
		//				id_revista = $revista
		//			WHERE id_afiliado = $id_afiliado ";
						
		//echo "$query"; exit();
		//mysql_query($query) or die(mysql_error().$query);

		$sql = "UPDATE afiliados SET id_tipo_aporte=$tbt,id_revista=$revista WHERE id = $id_afiliado ";
		mysql_query($sql) or die(mysql_error()." ".$sql);
		
		if($seccional){
			$sql ="UPDATE afiliados SET filial=$seccional WHERE id = $id_afiliado";
			mysql_query($sql) or die(mysql_error()." ".$sql);
		}

		if($plan_medico){
			$sql ="UPDATE afiliados SET id_plan_medico=$plan_medico WHERE id = $id_afiliado";
			mysql_query($sql) or die(mysql_error()." ".$sql);
		}
		
		$campos = array('tbt','revista','seccional','plan_medico');

		foreach ($campos as $campo) {
			$_dto_ant = "$campo"."_ant";
			$dto_ant = $$_dto_ant ;
			
			$_dto_act = "$campo";
			$dto_act = $$_dto_act ;

			if($dto_act!=$dto_ant){
				//echo "<br>comparo $campo y son diferentes : <br>Anterior: $dto_ant <br>Actual: $dto_act";
				motivo_modificacion_campos($id_usuario,$id_afiliado,$campo,$dto_ant,$dto_act,'');
			}
			else{
				//echo "<br>comparo $campo y son iguales : <br>Anterior: $dto_ant <br>Actual: $dto_act";
			}			
		}
		echo "ok"; 
		break;

	case 'grabar_movimiento_afiliado':
			
			$insert = "INSERT INTO $base_historicos.`cambios_manuales`(id_afiliado,id_evento,fecha,observacion,id_usuario,manual)
							VALUE ($id_afiliado,$id_evento,'$fecha','$observacion',$id_usuario,1)";
							
			mysql_query($insert) or die(mysql_error().$insert);
			
			echo "ok";
			
		break;

	case 'graba_baja_automatica':
	
		if($_SERVER['DOCUMENT_ROOT']=="/var/www/".DOMINIO){
			$path =  $_SERVER['DOCUMENT_ROOT']."/padron/APP/documentos/archivos/";
		}
		else{
			$path = $_SERVER['DOCUMENT_ROOT']."/".DOMINIO."/padron/APP/documentos/archivos/";
		}

		//echo "$path"; exit();
		
		$path = $path . basename( $_FILES['ce_archivo']['name']);
		$copiado = move_uploaded_file($_FILES['ce_archivo']['tmp_name'], $path);

		if($copiado==false){
						
			echo "Error";
			
		}
		else{
			$image = addslashes(fread(fopen($path, "r"), filesize($path)));
			//echo "Cargo";
			if(strlen($image)!=0){

				$query ="INSERT INTO historico_afiliados (id_afiliado,id_evento,id_row,id_usuario)
							VALUES ($id_afiliado,'35','-1',$id_usuario)";
				mysql_query($query) or die(mysql_error()." ".$query);
				$id_historico= mysql_insert_id();

				$query ="INSERT INTO bajas_manuales (id_afiliado,id_historico,fecha_aPartir,motivo,id_usuario)
							VALUES ($id_afiliado,$id_historico,'$fecha_aPartir','Vencimiento del Certificado de Estudios',$id_usuario)";
				mysql_query($query) or die(mysql_error()." ".$query);
				$id_row= mysql_insert_id();

				$query ="UPDATE historico_afiliados 
						SET id_row=$id_row
						WHERE id_row=-1";
				mysql_query($query) or die(mysql_error()." ".$query);


				$nombre_archivo= basename( $_FILES['ce_archivo']['name']);

				$sql="insert into $base_padron.afiliados_documentacion(id_afiliado,id_historico,tipo,fecha,observacion,id_usuario) 
						VALUES ($id_afiliado,$id_historico,'certificado_escolar','$fecha_aPartir','$ce_observacion',$id_usuario)";		

				mysql_query($sql) or die(mysql_error()." ".$sql);
				$id_documentacion = mysql_insert_id();

				$query_archivo = "INSERT INTO $base_imagenes.documentacion(id_documentacion,nombre,imagen,id_usuario)
										VALUES ($id_documentacion,'$nombre_archivo','$image',$id_usuario)";

				mysql_query($query_archivo) or die(mysql_error().$query_archivo);

				echo "ok";
			}
			else{
				echo "archivo vacio";
			}
			
			
		}
	
		/*
		

		
		*/
		break;

	case 'subir_documentacion':
			# code...
			$json = array();

			if($_SERVER['DOCUMENT_ROOT']=="/var/www/.".DOMINIO){
				$path =  $_SERVER['DOCUMENT_ROOT']."/padron/APP/documentos/archivos/";
			}
			else{
				$path = $_SERVER['DOCUMENT_ROOT']."/".DOMINIO."/padron/APP/documentos/archivos/";
			}

			//echo "$path"; exit();
			
			$path = $path . basename( $_FILES['doc_archivo']['name']);
			$copiado = move_uploaded_file($_FILES['doc_archivo']['tmp_name'], $path);

			if($copiado==false){
							
				$json[] = array('estado' => 'ERROR - Copiando el archivo.');
				
			}
			else{
				$image = addslashes(fread(fopen($path, "r"), filesize($path)));
				//echo "Cargo";
				if(strlen($image)!=0){

					$query ="INSERT INTO historico_afiliados (id_afiliado,id_evento,id_row,id_usuario)
														VALUES ($id_afiliado,'35','-1',$id_usuario)";
					mysql_query($query) or die( $json[] = array('estado' => mysql_error()." ".$query ) );
					$id_historico= mysql_insert_id();
					

					$sql="INSERT INTO afiliados_documentacion(id_afiliado,id_historico,id_tipo_doc,fecha,observacion,id_usuario) 
									VALUES ($id_afiliado,0,$tipo_documentacion,'$doc_fecha','$doc_observacion',$id_usuario)";		

					//mysql_query($sql) or die(mysql_error()." ".$sql);
					mysql_query($sql) or die( $json[] = array('estado' => mysql_error()." ".$sql ) );
					$id_documentacion = mysql_insert_id();


					$nombre_archivo= basename( $_FILES['doc_archivo']['name']);
					
					$nombre_archivo = str_replace("#", "-", $nombre_archivo);

					$query_archivo = "INSERT INTO $base_imagenes.documentacion(id_documentacion,nombre,imagen,id_usuario)
											VALUES ($id_documentacion,'$nombre_archivo','$image',$id_usuario)";

					mysql_query($query_archivo) or die(mysql_error().$query_archivo);

					//echo "ok";
					exec("rm $path");
					
					$json[] = array('estado' => 'ok');
					echo json_encode($json);
				}
				
				
			}
			break;	

	case 'ver_documentacion':
		# code...
		$json = array();

		$query = "SELECT td.documentacion,d.nombre AS archivo,ad.fecha,ad.observacion,
							d.id as id_documentacion,
							u.usuario,
							DATE_FORMAT(ad.fechador,'%d/%m/%Y %H:%i') AS fecha_carga
						FROM $base_padron.afiliados a
						JOIN $base_padron.afiliados_documentacion ad ON a.id=ad.id_afiliado 
						JOIN $base_imagenes.documentacion d ON ad.id=d.id_documentacion 
						JOIN $base_padron.tipo_documentacion td ON ad.id_tipo_doc=td.id 
						JOIN $base_usuarios.users u ON ad.id_usuario=u.id 
						WHERE a.id = $id_afiliado ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		if(mysql_num_rows($result)==0){
			$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
		}
		else{

			while ($row = mysql_fetch_assoc($result)) {
			
			    $json[] = array(
						'id_documentacion' => $row['id_documentacion'],	
						'documentacion' => $row['documentacion'],		
						'archivo' => $row['archivo'],	
						'fecha' => $row['fecha'],						        		
						'observacion' => $row['observacion'],						        		
						'usuario' => $row['usuario'],	    		
						'fecha_carga' => $row['fecha_carga']
						       
			      );
			}
		}

		echo json_encode($json);


		break;			

	case 'prueba_array':
		# code...
		$campos = array('calle','numero','piso','depto');

		foreach ($campos as $campo) {
			$_dto_ant = "$campo"."_ant";
			$dto_ant = $$_dto_ant ;
			
			$_dto_act = "$campo";
			$dto_act = $$_dto_act ;

			if($dto_act!=$dto_ant){
				echo "<br>comparo $campo y son diferentes : <br>Anterior: $dto_ant <br>Actual: $dto_act";
			}
			else{
				echo "<br>comparo $campo y son iguales : <br>Anterior: $dto_ant <br>Actual: $dto_act";
			}
			
		}

		break;


	//Alta familiar

	case 'dni_familiar':
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

	case 'nuevo_gpar':
			# code...
			$query = "SELECT LPAD(MAX(gpar)+1,2,'00') AS nuevo_gpar
						FROM afiliados 
						WHERE id=$id_titular OR id_titular=$id_titular ";
			$result = mysql_query($query);
			$d = mysql_fetch_object($result);

			echo $d->nuevo_gpar;

			break;	

	case 'domicilio_titular':
			# code...
			$json = array();

			$sql="SELECT p.id_domicilio 
					FROM afiliados a
					JOIN persona p ON a.id_persona=p.id 
					WHERE a.id=$id_titular  ";
			
			$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);

			if(mysql_num_rows($result)==0){
				$json[] = array('estado' => 'ERROR - NO se encontraron resultados');
			}
			else{

				while ($row = mysql_fetch_assoc($result)) {
				
				    $json[] = array('estado' => $row['ok'],	
										'id_domicilio_titular' => $row['id_domicilio']
							       
				      );
				}
			}

			echo json_encode($json);
			break;	

	case 'alta_familiar':
		# code...
		
		//Domicilio
		if($select_s_domicilio==0){
			$id_domicilio = $id_domicilio_titular ;
		}
		else{

			$insert_domicilio = "INSERT INTO domicilio(id_localidad,calle,numero,piso,depto,telefono)
									VALUES ($localidad,'$calle','$numero','$piso','$departamento','$telefono')";
			//echo $insert_domicilio."<br>";
			//$id_domicilio = 0;
			mysql_query($insert_domicilio) or die(mysql_error().$insert_domicilio);
			$id_domicilio = mysql_insert_id();
			

		}

		//Persona
		$sql_insert_persona="INSERT INTO persona (cuil,apellido,nombre,td,nd,telef_celular,fn,sexo,
													id_estado_civil,id_nacionalidad,id_domicilio,id_usuario,email) 
										VALUES ('$cuil',UPPER('$apellido'),UPPER('$nombre'),'DNI',$dni,'$telefono','$fn','$sexo',
												$estado_civil,$nacionalidad,$id_domicilio,$id_usuario,'$email')";
		//echo "persona: $sql_insert_persona <br>";
		mysql_query($sql_insert_persona) or die(mysql_error().$sql_insert_persona);
		$id_persona= mysql_insert_id();
		//mysql_query($query) or die(mysql_error().$query);

		// INSERT FAMILIAR EN TABLA AFILIADOS
		$query_titular = "SELECT *
						FROM afiliados 
						WHERE id=$id_titular";
		$rs_titular = mysql_query($query_titular) or die(mysql_error().$query_titular);
		$d_titular = mysql_fetch_object($rs_titular);

		
		$sql_insert_afiliados="INSERT INTO $base_padron.afiliados (id_persona,id_titular,id_parentesco,
										nben,gpar,id_usuario,incapacidad,id_plan_medico,
										id_tipo_aporte,
										id_desreguladora,id_revista,estado_dia)

										VALUES ($id_persona,$id_titular,$parentesco,
										LPAD('$d_titular->nben',8,'00000000'),LPAD('$gpar',2,'00'),$id_usuario,'$incapacidad',
										$d_titular->id_plan_medico,
										$d_titular->id_tipo_aporte,
										$d_titular->id_desreguladora,$d_titular->id_revista,'ALTA')";
		//eho "familiar: $sql_insert_afiliados <br>";
		mysql_query($sql_insert_afiliados) or die(mysql_error().$sql_insert_afiliados);
		$id_afiliado= mysql_insert_id();	
		// FIN INSERT FAMILIAR EN TABLA AFILIADOS
		
		$id_evento_nuevo_familiar = mysql_fetch_object(mysql_query("SELECT id FROM eventos_afiliados WHERE nombre_tabla='cambios_manuales' AND descripcion='Nuevo familiar' AND estado='ALTA'"))->id ;

		// INSERT EN HISTORICO DE ALTA MANUAL
		//Historico
		$historico = "INSERT INTO $base_historicos.cambios_manuales(id_afiliado,id_evento,fecha,observacion,id_usuario)
						VALUES ($id_afiliado,$id_evento_nuevo_familiar,'$fecha_alta','',$id_usuario)";
		mysql_query($historico) or die(mysql_error()."Historico <br>".$historico);
		
		// FIN UPDATE DE ID_ROW DEL HISTORICO DE ALTA MANUAL

		//mysql_query("CALL novedades_persona_a_lote_presentacion('$dni')") or die(mysql_error());

		echo "ok";

		/*$json[] = array('estado' => "ERROR",'id_afiliado' => $id_afiliado);
		$json[] = array(
				'estado' => "ok",						        		
				'id_afiliado' => $id_afiliado
				       
	    );
		echo json_encode($json);
		*/
		
		break;
	
	case 'vincular_otro_grupo_familiar':
		
		$sql="UPDATE $base_padron.afiliados 
				SET id_titular=$id_titular_nuevo
				WHERE id=$id_afiliado";

		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="UPDATE $base_padron.afiliados 
				SET id_parentesco=8
				WHERE id=$id_afiliado AND id_parentesco=0";

		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$insert_historico = "INSERT INTO $base_historicos.`cambios_manuales`(id_afiliado,id_evento,fecha,observacion,id_aai,detalle)
								VALUES ($id_afiliado,99,CURDATE(),'Pasa del titular $id_titular al $id_titular_nuevo',$id_titular,$id_titular_nuevo)";

		mysql_query($insert_historico) or die(mysql_error().$insert_historico);

		echo "ok";
		break;

	case 'desvincular_familiar':
		$sql="UPDATE $base_padron.afiliados SET id_titular=0 WHERE id=$id_afiliado";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="UPDATE $base_padron.afiliados SET id_tipo_aporte=$id_tbt WHERE id=$id_afiliado";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="UPDATE $base_padron.afiliados SET id_parentesco=0 WHERE id=$id_afiliado";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		#$sql="INSERT INTO $base_historicos.cambios_manuales (id_afiliado,id_evento,fecha,id_usuario) VALUES ($id_afiliado,74,'$fecha',$id_usuario)";
		#SEBA: 02/02/2023 | cambiado a 86 ( Familiar Convertido en Titular ) aca y en la base de datos ( eran 17 ) 
		$sql="INSERT INTO $base_historicos.cambios_manuales (id_afiliado,id_evento,fecha,id_usuario) VALUES ($id_afiliado,86,'$fecha',$id_usuario)";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		echo "ok";
		break;

	case 'transferir_titularidad':
			// code...
			$query="CALL $base_padron.`cambiar_titular_grupo`($id_afiliado,8,$id_usuario);";
			mysql_query($query) or die(mysql_error().$query);
			echo "ok";
			break;	
	
	case 'traer_sinc_logs':
		$json = array();
		$sql="SELECT lg.id,lg.`estado`,lg.`tipo_dato`,
				lg.`tipo_lote`,lg.`dato_actual`,lg.`dato_nuevo`,
				DATE_FORMAT(lg.fechador,'%d/%m/%Y %H:%i') AS fechador,us.usuario  
			FROM $base_padron.log_sincronizacion lg 
			JOIN $base_usuarios.`users` us ON us.id=lg.id_usuario
			WHERE id_afiliado=$id_afiliado AND lg.estado='pendiente' ORDER BY lg.fechador DESC";
		$rs=mysql_query($sql) or die (mysql_error()."<br>".$sql);

		while ($row = mysql_fetch_assoc($rs)){

			switch ($row['tipo_dato']) {
				case 'parentesco':
					
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.parentesco WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->parentesco;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.parentesco WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo=$d->parentesco;

					break;
				case 'desreguladora':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.desreguladoras WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->convenio;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.desreguladoras WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo=$d->convenio;
					break;
				case 'estado_civil':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.estadocivil WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->est_civil;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.estadocivil WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->est_civil;
					break;
				case 'situacion_revista':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.situacion_revista WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->revista;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.situacion_revista WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->revista;
					break;
				case 'tipo_beneficio_titular':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.tipo_beneficiario_titular WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->beneficiario;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.tipo_beneficiario_titular WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->beneficiario;
					break;
				default:
					$dato_actual=$row['dato_actual'];
					$dato_nuevo=$row['dato_nuevo'];
					break;
			}
			$json[] = array('id' => $row['id'],
					'estado' => ucfirst($row['estado']),
					'tipo_dato' => ucfirst($row['tipo_dato']),
					'dato_actual' => $dato_actual,
					'dato_nuevo' => $dato_nuevo,
					'tipo_lote' => ucfirst($row['tipo_lote']),
					'fechador' => $row['fechador'],
					'usuario' => $row['usuario'],
					'd_actual' => $row['dato_actual'],
					'd_nuevo' => $row['dato_nuevo']
			);
		}
		echo json_encode($json);
		break;
	
	case 'traer_sinc_logs_no_pend':
		$json = array();
		$sql="SELECT lg.id,lg.`estado`,lg.`tipo_dato`,
				lg.`tipo_lote`,lg.`dato_actual`,lg.`dato_nuevo`,
				DATE_FORMAT(lg.fechador_cambio,'%d/%m/%Y %H:%i') AS fechador,us.usuario,
				lg.observacion  
			FROM $base_padron.log_sincronizacion lg 
			LEFT JOIN $base_usuarios.`users` us ON us.id=lg.id_usuario
			WHERE id_afiliado=$id_afiliado AND lg.estado!='pendiente' ORDER BY lg.fechador DESC";
		$rs=mysql_query($sql) or die (mysql_error()."<br>".$sql);

		while ($row = mysql_fetch_assoc($rs)){

			switch ($row['tipo_dato']) {
				case 'parentesco':
					
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.parentesco WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->parentesco;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.parentesco WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo=$d->parentesco;

					break;
				case 'desreguladora':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.desreguladoras WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->convenio;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.desreguladoras WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo=$d->convenio;
					break;
				case 'estado_civil':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.estadocivil WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->est_civil;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.estadocivil WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->est_civil;
					break;
				case 'situacion_revista':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.situacion_revista WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->revista;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.situacion_revista WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->revista;
					break;	
				case 'tipo_beneficio_titular':
					$d_actual=$row['dato_actual'];
					$sql="SELECT * FROM $base_padron.tipo_beneficiario_titular WHERE id=$d_actual";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_actual=$d->beneficiario;

					$d_nuevo=$row['dato_nuevo'];
					$sql="SELECT * FROM $base_padron.tipo_beneficiario_titular WHERE id=$d_nuevo";
					$par=mysql_query($sql);$d = mysql_fetch_object($par); $dato_nuevo =$d->beneficiario;
					break;
				default:
					$dato_actual=$row['dato_actual'];
					$dato_nuevo=$row['dato_nuevo'];
					break;
			}
			$json[] = array('id' => $row['id'],
				'estado' => ucfirst($row['estado']),
				'tipo_dato' => ucfirst($row['tipo_dato']),
				'dato_actual' => $dato_actual,
				'dato_nuevo' => $dato_nuevo,
				'tipo_lote' => ucfirst($row['tipo_lote']),
				'fechador' => $row['fechador'],
				'usuario' => $row['usuario'],
				'd_actual' => $row['dato_actual'],
				'd_nuevo' => $row['dato_nuevo'],
				'observacion' => $row['observacion']
			);
		}
		echo json_encode($json);
		break;
	
	case 'sinc_aceptar':
		$sql="SELECT tipo_dato,dato_actual,dato_nuevo,id_afiliado FROM $base_padron.log_sincronizacion WHERE id=$id";
		$rs = mysql_query($sql); $row = mysql_fetch_assoc($rs);
		//$dato_actual=$row['dato_actual'];
		$id_afiliado=$row['id_afiliado'];
		//echo $row['tipo_dato'];exit();
		switch ($row['tipo_dato']) {
			case 'email':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.email='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'nombre':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.nombre='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'apellido':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.apellido='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'desreguladora':
				$sql="UPDATE $base_padron.afiliados
					SET id_desreguladora='$d_nuevo' 
					WHERE id=$id_afiliado";
				break;
			case 'fn':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.fn='$d_nuevo' 
					WHERE af.id=$id_afiliado";
				break;
			case 'estado_civil':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.id_estado_civil='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				//echo $sql;exit();
				break;
			case 'situacion_revista':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_revista='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				//echo $sql;exit();
				break;
			case 'tipo_beneficio_titular':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_tipo_aporte='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'parentesco':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_parentesco='$d_nuevo' 
					WHERE af.id=$id_afiliado ";
				break;
		}
		mysql_query($sql) or die(mysql_error($sql)."<br>".$sql);
		$sql_upd="UPDATE $base_padron.log_sincronizacion SET estado='aceptado' WHERE id=$id";
		mysql_query($sql_upd) or die(mysql_error($sql_upd)."<br>".$sql_upd);
		echo "ok";	
		break;
	
	case 'sinc_rechazar':

		$sql="UPDATE $base_padron.log_sincronizacion SET estado='rechazado' WHERE id=$id";

		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		echo "ok";

		break;
	
	case 'revertir_log':
		$sql="SELECT tipo_dato,dato_actual,dato_nuevo,id_afiliado FROM $base_padron.log_sincronizacion WHERE id=$id";
		$rs = mysql_query($sql); $row = mysql_fetch_assoc($rs);
		//$dato_actual=$row['dato_actual'];
		$id_afiliado=$row['id_afiliado'];
		//echo $row['tipo_dato'];exit();
		switch ($row['tipo_dato']) {
			case 'email':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.email='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'nombre':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.nombre='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'apellido':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.apellido='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'desreguladora':
				$sql="UPDATE $base_padron.afiliados
					SET id_desreguladora='$d_actual' 
					WHERE id=$id_afiliado";
				break;
			case 'fn':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.fn='$d_actual' 
					WHERE af.id=$id_afiliado";
				break;
			case 'estado_civil':
				$sql="UPDATE $base_padron.persona p 
					JOIN $base_padron.afiliados af ON af.id_persona=p.id 
					SET p.id_estado_civil='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'situacion_revista':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_revista='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'tipo_beneficio_titular':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_tipo_aporte='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
			case 'parentesco':
				$sql="UPDATE $base_padron.afiliados af
					SET af.id_parentesco='$d_actual' 
					WHERE af.id=$id_afiliado ";
				break;
		}
		mysql_query($sql) or die(mysql_error($sql)."<br>".$sql);
		$sql_upd="UPDATE $base_padron.log_sincronizacion SET estado='revertido' WHERE id=$id";
		mysql_query($sql_upd) or die(mysql_error($sql_upd)."<br>".$sql_upd);
		echo "ok";	
		break;
	
	case 'es_jubilado_ahora':

		$json  = array();

		$sql="SELECT p.cuil, af.id_tipo_aporte
				FROM $base_padron.`persona` p
				JOIN $base_padron.afiliados af ON af.id_persona=p.id
				WHERE af.id=$id_titular
		";
		$rs = mysql_query($sql) or die( mysql_error() ); $d = mysql_fetch_object($rs); 

		$cuil = $d->cuil; $id_tipo_aporte = $d->id_tipo_aporte;
		//Obtengo el cuil del titular

		if($id_tipo_aporte != "10"){ //puede pasar que esta consulta se use con afiliados que no sean jubilados, en todo caso devuelvo que no es jubilado
			$json[] = array('actual'=>'no','causa'=>'no_es');	
		} else {

			$sql2 = "SELECT id,MID(descripcion,1,7) as periodo
			FROM $base_historicos.`lotes`
			WHERE proceso='jubilados'
			ORDER BY descripcion DESC
			LIMIT 1";
			$rs2 = mysql_query($sql2) or die( mysql_error() ); $d2 = mysql_fetch_object($rs2); 

			$ultimo_lote = $d2->id;
			//Luego obtengo el ultimo periodo procesado de jubilados

			$sql3="SELECT * FROM $base_historicos.jubilados WHERE id_lote=$ultimo_lote AND cuil_titular=$cuil";
			$rs3=mysql_query($sql3) or die ( mysql_error()." ".$sql3 );

			$count = mysql_num_rows($rs3);
			//Aca verifico si el afiliado forma parte del ultimo lote

			//Hay 3 tipos de jubilados (por id_tipo_aporte en afiliados) los que estan en el ultimo lote, los que no estan en el ultimo lote, y los que nunca estuvieron en jubilados, voy a informar las 3 condiciones en el programa

			if($count>0){
				$json[] = array('actual'=>'si','periodo'=>$d2->periodo);//En este caso el afiliado SI esta en el lote actual, informo el periodo tambien
			}else{
				//aca voy a manejar los posibles jubilados, para estoy voy a usar la variable "causa"

				$sql="SELECT cuil_titular,MID(MAX(descripcion),1,7) AS periodo
				FROM $base_historicos.jubilados j
				JOIN $base_historicos.lotes l ON l.id=j.id_lote
				WHERE cuil_titular='$cuil';";
				$rs=mysql_query($sql) or die ( mysql_error()." ".$sql );
				$count = mysql_num_rows($rs);$d = mysql_fetch_object($rs); 

				if($d->periodo){
					$json[] = array('actual'=>'no','causa'=>'lo_fue','periodo'=>$d->periodo);
				}else{
					$json[] = array('actual'=>'no','causa'=>'nunca_estuvo');
				}
			}
		}



		echo json_encode($json);

		break;
	
	case 'verificar_credencial':
		$sql="SELECT $base_padron.estado_afiliado_nuevo($nd,ADDDATE(CURDATE(),INTERVAL 1 YEAR)) as estado";
		$rs = mysql_query($sql) or die(mysql_error());
		$d = mysql_fetch_object($rs);
		$estado_full = $d->estado; 
		$estado = explode("@",$estado_full);

		$sql="
			SELECT fi.`cod_filial`,IF(d.convenio_real LIKE 'FILIAL%' OR d.convenio_real ='MOSAISTAS' OR d.convenio_real LIKE '%UTEPLIM%' OR fi.cod_filial = '099',1,0) as propio
			FROM $base_padron.`persona` p
			JOIN $base_padron.`afiliados` af ON af.id_persona=p.`id`
			JOIN $base_padron.desreguladoras d on d.id=af.id_desreguladora
			LEFT JOIN $base_padron.`filial` fi ON fi.`id`=af.`filial`
			WHERE p.`nd`=$nd
		";
		$rs = mysql_query($sql) or die(mysql_error());
		$d = mysql_fetch_object($rs);
		$cod_filial = $d->cod_filial; 
		$propio = $d->propio;

		if(in_array($cod_filial, array("191","192","921","922"))){
			$start_date = date('Y-m-d');
			$fecha_vencimiento = date('Y-m-d', strtotime("+6 months")); 
		}else{
			$start_date = date('Y-m-d');
			$fecha_vencimiento = date('Y-m-d', strtotime("+12 months"));

			if($estado[0] == 'BAJA'){
				$fecha_vencimiento = $estado[1];
			} 
		}

		$json = array('estado' => $estado[0],'fecha_estado' => $estado[1],'cod_filial' => $cod_filial,'fecha_vencimiento' => $fecha_vencimiento,'propio' => $propio);

		echo json_encode($json);
		break;

	case 'novedades_agregar_a_presentacion':

		mysql_query("CALL $base_padron.`NOV_agregar_movimiento_manualmente`('$cuil','$tipo_mov','$fecha_movimiento')") or die(mysql_error());
		echo "ok";

		break;
	case 'info_credenciales':
		$sql="SELECT ce.*,us.`nombrecompleto` as usuario
			FROM $base_historicos.`credenciales_emitidas` ce
			JOIN $base_usuarios.`users` us ON us.id=ce.`id_usuario`
			WHERE id_afiliado=$id_afiliado
			ORDER BY fechador DESC
		";
		$rs = mysql_query($sql) or die(mysql_error());
		$json = array();
		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'lst_cronologia_afiliado':
		mysql_query("CALL $base_padron.`novedades_cronologia`($id_persona)") or die(mysql_error()."ERROR stored");

		$sql = "SELECT DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fechador_mostrar,id_usuario,MID(evento,1,250) AS evento,tipo_mov
					FROM $base_padron.tmp_cronologia_novedades 
					ORDER BY fechador DESC" ;

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
			$json[] = array(
				'fechador' => $row['fechador_mostrar'],
				'id_usuario' => $row['id_usuario'],	
				'movimiento' => $row['evento'],
				'tipo_mov' => $row['tipo_mov']
			);
		}

		echo json_encode($json);
		break;
}
mysql_close();

function motivo_modificacion_campos($id_usuario,$id_afiliado,$nombre_campo,$campo_mod_anterior,$campo_mod_nuevo,$observacion_desreguladora){

	$estado = 0;
	//echo $nombre_campo;
	switch ($nombre_campo) {
		case 'desreguladora':
			$sql="INSERT INTO ".N_BASE_PADRON.".log_sincronizacion 
				(estado,id_afiliado,tipo_dato,dato_actual,dato_nuevo,tipo_lote,id_lote,id_row,accion,id_usuario,observacion)
				VALUES
				('aceptado',$id_afiliado,'$nombre_campo','$campo_mod_anterior','$campo_mod_nuevo','manual',0,0,'',$id_usuario,'$observacion_desreguladora')";
			//echo $sql; exit();
			break;
		default:
			$sql="INSERT INTO ".N_BASE_PADRON.".log_sincronizacion 
				(estado,id_afiliado,tipo_dato,dato_actual,dato_nuevo,tipo_lote,id_lote,id_row,accion,id_usuario)
				VALUES
				('aceptado',$id_afiliado,'$nombre_campo','$campo_mod_anterior','$campo_mod_nuevo','manual',0,0,'',$id_usuario)";
			break;
	}

	mysql_query($sql) or die ($estado=1);
	
	return $estado;

}
function cambio_desreguladora_familiares($id_desreguladora,$id_titular){

	$sql="UPDATE $base_padron.afiliados
				SET id_desreguladora=$id_desreguladora
				WHERE id_titular=$id_titular AND id!=$id_titular";

	mysql_query($sql) or die (mysql_error());

}

?>