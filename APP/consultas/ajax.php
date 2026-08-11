<?
include('../../Config/Conectar.inc');

switch ($parametro) {
	
	case 'log_utilizacion':
			
			$insert = "INSERT INTO $base_padron.log_eventos (evento,ip)
							VALUES ('Consulta $cuil','$ip')";
			mysql_query($insert) or die(mysql_error().$insert);	
			
		break;
	
	case 'consulta_cuil':
		
			$sql="SELECT prueba.consulta_cuil('$dni','$sex') AS cuil";
			$cuil=mysql_fetch_object(mysql_query($sql))->cuil ; 
			
			echo $cuil;
		
		break;
	
	case 'consulta_capita':
			
			$sql = "SELECT convenio, CONCAT(p.apellido, ' ', p.nombre) AS ayn
							FROM persona p 
							JOIN afiliados a ON p.id=a.id_persona 
							JOIN desreguladoras d ON a.`id_desreguladora`=d.`id`
							WHERE p.cuil=$cuil";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'capita' => $row['convenio'],
						'ayn' => $row['ayn']											       
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
							
						FROM padron p
						JOIN parentesco pa ON p.id_parentesco=pa.id 
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
						FROM $base_historicos.padrones_sss 
						WHERE cuil_titular=$cuil
							OR cuil=$cuil  ";
						
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'ayn' => $row['ayn'],
						'parentesco' => $row['parentesco'],
						'sexo' => $row['sexo'],
						'fn' => $row['fn'],
						'nd' => $row['nd'],
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
			
			$sql = "SELECT b as periodo,e/100 AS remuneracion,'' as empresa#e.nombre as empresa  
						FROM $base_historicos.declaraciones_juradas dj
						#LEFT JOIN prepaga.empresas e ON dj.c=e.cuit 
						WHERE d='$cuil' 
						ORDER BY b DESC 
						LIMIT 18";
						
						
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
		/*
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
		*/
		break;	
	
	case 'consulta_aportes':
			
			$sql = "SELECT b AS tbt,h AS periodo,e as f_acred,(c/100) AS aporte,g AS cuit 
						FROM $base.aportes 
						WHERE a='".INST_RNOS_MIN."'	
							AND k='$cuil'
						ORDER BY periodo DESC  ";
			
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = $row;
			}
			
			echo json_encode($json);			
			
		break;	
	
	case 'consulta_fisca':
			
			mysql_select_db('$base_historicos', $conexion);
			// DDJJ
			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ult_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ult_ddjj
							SELECT b,d,c,MAX(id_lote) AS id_dj
							FROM $base_historicos.`declaraciones_juradas` dj
							WHERE d='$cuil' 
							GROUP BY b,d,c") or die(mysql_error());
							
			mysql_query("ALTER TABLE tmp_ult_ddjj ADD INDEX (id_dj), ADD INDEX (c), ADD INDEX (b)");


			mysql_query("DROP TEMPORARY TABLE  IF EXISTS tmp_ddjj");
			mysql_query("CREATE TEMPORARY TABLE tmp_ddjj
								SELECT dj.c,dj.b AS periodo,dj.e/100 AS remuneracion,
									'' as empresa#e.nombre AS empresa  
								FROM $base_historicos.`declaraciones_juradas` dj
								JOIN tmp_ult_ddjj udj ON dj.id_lote=udj.id_dj AND dj.c=udj.c AND dj.b=udj.b
								#LEFT JOIN prepaga.empresas e ON dj.c=e.cuit 
								WHERE dj.d='$cuil' 
								ORDER BY dj.b DESC 
								LIMIT 24 ") or die(mysql_error());
			#De momento no muestro las empresas ya que falta la tabla 12/05/2022
						
			$sql = "SELECT dj.c AS cuit,empresa,dj.periodo,remuneracion,
							#COALESCE(ROUND(d931,2),'--') AS d931,
							'--' AS d931,
							#ROUND(COALESCE(aporte,0),2) AS aporte
							0 as aporte  
						FROM tmp_ddjj dj
						#LEFT JOIN $base_fiscalizacion.fiscalizacion f ON dj.c=f.cuit COLLATE latin1_swedish_ci AND dj.periodo=f.periodo COLLATE latin1_swedish_ci
						ORDER BY dj.periodo DESC ";
						
						
			$result = mysql_query($sql) or die(mysql_error());
			
			$json = array();

			$remuneracion_no_mostrar = array('30606588182');
			//exit();

				while ($row = mysql_fetch_assoc($result)) {

					if(in_array($row['cuit'],$remuneracion_no_mostrar)){
						$remu = 'S/D';
					}else{
						$remu = number_format($row['remuneracion'],2,",",".") ;
					}
					#$remu = number_format($row['remuneracion'],2,",",".") ;
					

					
				    $json[] = array(
							'cuit' => $row['cuit'],
							'empresa' => $row['empresa'],
							'periodo' => $row['periodo'],
							'remuneracion' => $remu,
							'calculado' => $row['d931'],
							'aporte' => $row['aporte']				       
				      );
				}	

			
			echo json_encode($json);			
			
			
		break;	
	
	case 'consulta_ddjj_contra_aportes':
			
			$sql="CALL $base_padron.consulta_ddjj_aportes('$cuil')";

			$result = mysql_query($sql);
			
			$json = array();

			$remuneracion_no_mostrar = array(INST_CUIT);

			while ($row = mysql_fetch_assoc($result)) {
					if(in_array($row['cuit'],$remuneracion_no_mostrar)){
						$row['remuneracion'] = 'S/D';
					}

			    $json[] = $row;
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
			

			$sql = "SELECT cuil_titular,
							cuil,gp,nd,ayn,sexo,
							CONCAT(MID(fn,7,2),'/',MID(fn,5,2),'/',MID(fn,1,4)) AS fn,
							REPLACE(fec_vigencia,'.','/') AS fec_vigencia, 
							fec_inicio_rel,fec_cese,
							CONCAT(MID(fecha_proceso,7,2),'/',MID(fecha_proceso,5,2),'/',MID(fecha_proceso,1,4)) AS fec_proceso,
							fecha_proceso,
							MID(DATE_ADD(fecha_proceso,INTERVAL 1 MONTH),1,7) AS mes_vigencia	
							
						FROM $base_historicos.desempleo d
						JOIN $base_historicos.lotes l ON d.id_lote=l.id 
						WHERE cuil_titular='$cuil' OR cuil='$cuil'
						ORDER BY fecha_proceso DESC";			
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								
								'cuil_titular' => $row['cuil_titular'],
								'cuil' => $row['cuil'],
								'parentesco' => $row['gp'],
								'dni' => $row['nd'],
								'ayn' => $row['ayn'],
								'sexo' => $row['sexo'],
								'fn' => $row['fn'],								
								'f_vigencia' => $row['fec_vigencia'],							
								'fec_ini' => $row['fec_inicio_rel'],
								'fec_fin' => $row['fec_cese'],
								'fecha_proceso' => $row['fec_proceso'],
								'mes_vigencia' => $row['mes_vigencia']								
														       
					      );
			}
			
			echo json_encode($json);		
			
		break;	
	
	case 'consulta_jubilados':
			

			$sql = "SELECT MID(l.descripcion,1,7) AS periodo
						FROM $base_historicos.jubilados j
						JOIN $base_historicos.lotes l ON j.id_lote=l.id 
						WHERE cuil_titular=$cuil
							OR cuil=$cuil 
					ORDER BY 1 DESC ";			
						
			$result = mysql_query($sql);
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								
								'periodo' => $row['periodo']
														       
					      );
			}
			
			echo json_encode($json);		
			
		break;

	case 'consulta_liquidaciones'://Liquidaciones efectuadas donde el cuil si forma parte
			
			$json = array();

			$sql="CALL $base_padron.consulta_liquidacion_x_afiliado('$cuil')";

			$rs = mysql_query($sql) or die (mysql_error()."<br>".$sql);

			while ($row = mysql_fetch_assoc($rs)){	
				$json[] = $row;
			}

			echo json_encode($json);
			
		break;

	case 'fa_info':
			
			$nd = substr($cuil, 2,8);
						
			$sql = "SELECT periodo,MID(cuit_titular,1,11) AS cuil_tit,nd_fam,ayn,
							IF(parentesco=1,'Conyuge','Hijo') AS parentesco,
							fec_incorporacion
						FROM $base_fa.fa_historico
						WHERE cuit_titular IN ( SELECT DISTINCT cuit_titular FROM $base_fa.fa_historico WHERE nd_fam=$nd )
						ORDER BY periodo DESC, nd_fam ";
					//13464133	
						
			$result = mysql_query($sql) or die(mysql_error());
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								'periodo' => $row['periodo'],
								'cuil_tit' => $row['cuil_tit'],								
								'nd_fam' => $row['nd_fam'],
								'ayn' => $row['ayn'],								
								'parentesco' => $row['parentesco'],																
								'fec_incorporacion' => $row['fec_incorporacion']						       
					      );
			}
			
			echo json_encode($json);	
			
		break;
	
	case 'consulta_opciones':
		
			$sql = "SELECT * FROM $base_historicos.opciones_nuevas WHERE cuil=$cuil";
			
			$result = mysql_query($sql) or die(mysql_error());
			
			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(						
								'deleg_nombre' => $row['deleg_nombre'],
								'nro_formulario' => $row['nro_formulario'],								
								'regimen' => $row['regimen'],
								'ayn' => $row['ayn'],								
								'sexo' => $row['sexo'],
								'fn' => $row['fn'],
								'os_procedencia' => $row['os_procedencia'],
								'fec_eleccion' => $row['fec_eleccion'],																					
								'fec_entrega' => $row['fec_entrega'],
								'desreguladora' => $row['usu_importacion']						       
					      );
			}
			
			echo json_encode($json);	
			
		break;
	
	default:
		
		break;
}


?>