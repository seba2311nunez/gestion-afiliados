<?php

require("../../Config/Conectar.inc");

switch ($parametro) {
	
	case 'consulta_cuil':
		
			$sql="SELECT prueba.consulta_cuil('$dni','$sex') AS cuil";
			$cuil=mysql_fetch_object(mysql_query($sql))->cuil ; 
			
			echo $cuil;
		
		break;
	
	case 'consulta_capita':
			
			$sql = "SELECT convenio 
							FROM persona p 
							JOIN afiliados a ON p.id=a.id_persona 
							JOIN desreguladoras d ON a.`id_desreguladora`=d.`id`
							WHERE p.cuil=$cuil";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'capita' => $row['convenio']											       
			      );
			}
			
			echo json_encode($json);			
			
		break;
	
	case 'consulta_grupoFamiliar':
			
			$sql = "SELECT CONCAT(apellido,' ',nombre) AS ayn,
							pa.parentesco,
							sexo,
							fn AS fechanac,
							TIMESTAMPDIFF(YEAR,fn,CURDATE()) AS edad,
							prox_fecha_baja(id_afiliado) AS fecha_baja,
							p.incapacidad
							
						FROM $base_padron.padron p
						JOIN $base_padron.parentesco pa ON p.id_parentesco=pa.id 
						WHERE cuil_titular=$cuil ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'ayn' => $row['ayn'],
						'parentesco' => $row['parentesco'],
						'sexo' => $row['sexo'],
						'fn' => $row['fechanac'],
						'edad' => $row['edad'],
						'incapacidad' => $row['incapacidad'],
						'fecha_baja' => $row['fecha_baja']												       
			      );
			}
			
			echo json_encode($json);			
			
		break;
		
	case 'consulta_grupoFamiliar_padron_sss':
			
			$sql = "SELECT cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,fn,localidad,cp,incapacidad,
							CONCAT(MID(f_alta,1,2),'/',MID(f_alta,3,2),'/',MID(f_alta,5,4)) AS f_alta, 
							periodo_desde_opcion 
							
						FROM ladrillo_dev.`padron_sss_201907` 
						WHERE cuil_titular=$cuil ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'ayn' => $row['ayn'],
						'parentesco' => $row['parentesco'],
						'sexo' => $row['sexo'],
						'fn' => $row['fn'],
						'localidad' => $row['localidad'],
						'cp' => $row['cp'],
						'incapacidad' => $row['incapacidad'],
						'f_alta' => $row['f_alta'],
						'periodo_desde_opcion' => $row['periodo_desde_opcion']												       
			      );
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_ddjj':
			
			$sql = "SELECT b as periodo,e/100 AS remuneracion,e.nombre as empresa  
						FROM $base_historicos.`declaraciones_juradas` dj
						LEFT JOIN $base.empresas e ON dj.c=e.cuit 
						WHERE d=$cuil 
						ORDER BY b DESC 
						LIMIT 12";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'periodo' => $row['periodo'],
						'empresa' => $row['empresa'],
						'remuneracion' => $row['remuneracion']						       
			      );
			}
			
			echo json_encode($json);			
			
		break;
		
	case 'consulta_altas_bajas':
			
			mysql_query("CALL cuil_altas_bajas('$cuil') ");
			
			$sql = "SELECT * FROM tmp_altas_vs_bajas 
						WHERE cuil=$cuil 
						ORDER BY fecha_aPartir DESC ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'movimiento' => $row['movimiento'],
						'nro_formulario' => $row['nro_formulario'],
						'fecha_aPartir' => $row['fecha_aPartir'],
						'fec_eleccion' => $row['fec_eleccion']						       
			      );
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_aportes':
			
			$sql = "SELECT b AS tbt,h AS periodo,(c/100) AS aporte,g AS cuit 
						FROM $base.aportes 
						WHERE a=".INST_RNOS_MIN."	
							AND k='$cuil'
						ORDER BY periodo DESC  ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'tbt' => $row['tbt'],
						'periodo' => $row['periodo'],
						'aporte' => $row['aporte'],
						'cuit' => $row['cuit']						       
			      );
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_fisca':
			
			// DDJJ
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ult_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ult_ddjj
							SELECT b,d,c,MAX(id_lote) AS id_dj
							FROM $base_historicos.`declaraciones_juradas` dj
							WHERE d='$cuil' 
							GROUP BY b,d,c");
							
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ddjj
								SELECT dj.c,dj.b AS periodo,dj.e/100 AS remuneracion,
									e.nombre AS empresa  
								FROM $base_historicos.`declaraciones_juradas` dj
								JOIN tmp_ult_ddjj udj ON dj.id_lote=udj.id_dj
								LEFT JOIN $base.empresas e ON dj.c COLLATE latin1_general_ci=e.cuit =e.cuit 
								WHERE dj.d='$cuil' 
									AND dj.a=".INST_RNOS."
								ORDER BY dj.b DESC 
								LIMIT 24 ");
					
						
			$sql = "SELECT dj.c AS cuit,empresa,dj.periodo,remuneracion,
							COALESCE(ROUND(d931,2),'--') AS d931,
							ROUND(COALESCE(aporte,0),2) AS aporte
							  
						FROM tmp_ddjj dj
						LEFT JOIN $base_fiscalizacion.fiscalizacion f ON dj.c=f.cuit COLLATE latin1_swedish_ci AND dj.periodo=f.periodo COLLATE latin1_swedish_ci
						ORDER BY dj.periodo DESC ";
						
			//echo $sql;	

			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'cuit' => $row['cuit'],
						'empresa' => $row['empresa'],
						'periodo' => $row['periodo'],
						'remuneracion' => $row['remuneracion'],
						'calculado' => $row['d931'],
						'aporte' => $row['aporte']				       
			      );
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_ddjj_contra_aportes':
			
			// DDJJ
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ult_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ult_ddjj
							SELECT b,d,c,MAX(id) AS id_dj
							FROM $base_historicos.`declaraciones_juradas` dj
							WHERE d=$cuil 
							GROUP BY b,d,c");
							
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ddjj
								SELECT dj.c,dj.b AS periodo,dj.e/100 AS remuneracion,
									e.nombre AS empresa  
								FROM $base_historicos.`declaraciones_juradas` dj
								JOIN tmp_ult_ddjj udj ON dj.id=udj.id_dj
								LEFT JOIN $base.empresas e ON dj.c=e.cuit 
								WHERE dj.d=$cuil 
								ORDER BY dj.b DESC 
								LIMIT 24 ");
			
			// Aportes
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_aportes");
			mysql_query("CREATE TEMPORARY TABLE tmp_aportes
							SELECT b AS tbt,h AS periodo,SUM((c/100)) AS aporte,g AS cuit 
							FROM $base.aportes 
							WHERE a=".INST_RNOS_MIN."	
								AND k=$cuil 
								AND b IN ('381','881','401','471')
							GROUP BY periodo
							ORDER BY periodo DESC");
			
			$sql = "SELECT dj.c AS cuit,empresa,dj.periodo,remuneracion,ROUND((remuneracion*0.0765),2) AS calculado,COALESCE(aporte,0) AS aporte 
						FROM tmp_ddjj dj
						LEFT JOIN tmp_aportes a ON dj.periodo=a.periodo
						ORDER BY dj.periodo DESC  ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'cuit' => $row['cuit'],
						'empresa' => $row['empresa'],
						'periodo' => $row['periodo'],
						'remuneracion' => $row['remuneracion'],
						'calculado' => $row['calculado'],
						'aporte' => $row['aporte']				       
			      );
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_fisca_empresa':
			
			$sql = "SELECT periodo,
							ROUND(d931,2) AS d931,
							ROUND(dadic,2) AS dadic,	
							ROUND(dconv,2) AS dconv,
							ROUND(aporte,2) AS aporte,
							f_ult_apo,
							ROUND((d931+dadic+dconv-aporte),2) AS saldo 
							
						FROM $base_fiscalizacion.fiscalizacion 
						WHERE cuit = $cuit
						ORDER BY periodo DESC ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'periodo' => $row['periodo'],
						'd931' => $row['d931'],
						'dadic' => $row['dadic'],
						'dconv' => $row['dconv'],
						'aporte' => $row['aporte'],
						'f_ult_apo' => $row['f_ult_apo'],						
						'saldo' => $row['saldo']				       
			      );
			}
			
			echo json_encode($json);
			
		break;
	
	case 'consulta_desempleo':
			
			$sql = "SELECT periodo,cuil_titular,f_vigencia, fec_ini,fec_fin,fecha_proceso,
							parentesco,cuil,dni,ayn,fn,sexo
						FROM `desempleo_sss` 
						WHERE cuil_titular='$cuil' 
						ORDER BY periodo DESC,parentesco";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								'periodo' => $row['periodo'],
								'cuil_titular' => $row['cuil_titular'],
								'f_vigencia' => $row['f_vigencia'],
								'fec_ini' => $row['fec_ini'],
								'fec_fin' => $row['fec_fin'],
								'fecha_proceso' => $row['fecha_proceso'],
								'parentesco' => $row['parentesco'],
								'cuil' => $row['cuil'],
								'dni' => $row['dni'],
								'ayn' => $row['ayn'],
								'fn' => $row['fn'],								
								'sexo' => $row['sexo']						       
					      );
			}
			
			echo json_encode($json);			
			
		break;	

	case 'consulta_desempleo_afil':
			
			$sql = "SELECT MID(l.lote,11,10) AS periodo,cuil_titular,fec_vigencia,fec_inicio_rel,fec_cese,fecha_proceso,
							gp,cuil,nd,ayn,fn,sexo
						FROM $base_historicos.desempleo d
						JOIN $base_historicos.lotes l ON d.id_lote=l.id 
						WHERE cuil_titular='$cuil' 
						ORDER BY periodo DESC,gp ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								'periodo' => $row['periodo'],
								'cuil_titular' => $row['cuil_titular'],
								'f_vigencia' => $row['fec_vigencia'],
								'fec_ini' => $row['fec_inicio_rel'],
								'fec_fin' => $row['fec_cese'],
								'fecha_proceso' => $row['fecha_proceso'],
								'parentesco' => $row['gp'],
								'cuil' => $row['cuil'],
								'dni' => $row['nd'],
								'ayn' => $row['ayn'],
								'fn' => $row['fn'],								
								'sexo' => $row['sexo']						       
					      );
			}
			
			echo json_encode($json);			
			
		break;	

	case 'es_desempleado':
			
			$json = array();

			$sql = "SELECT MIN(MID(l.lote,11,10)) AS per_min,MAX(MID(l.lote,11,10)) per_max
						FROM desempleo d
						JOIN $base_historicos.lotes l ON d.id_lote=l.id 
						WHERE cuil_titular='$cuil' ";	
						
			$result = mysql_query($sql) or die(mysql_error());

			$row = mysql_fetch_assoc($result);
						
			if($row['per_min']){
								
				$json[] = array(						
								'estado' => 'si',
								'per_min' => $row['per_min'],
								'per_max' => $row['per_max']					       
					      );
				
				
			}
			else{

				$json[] = array(			
								'estado' => 'no',			
								'per_min' => '',
								'per_max' => ''
					      );


			}
			
			echo json_encode($json);

		break;

}


?>