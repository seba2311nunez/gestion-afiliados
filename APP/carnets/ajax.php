<?php  
include("../../Config/Conectar.inc");

switch ($parametro) {

	case 'valida_lote':
		// code...

		$query = "SELECT *
					FROM $base_historicos.lotes l 
					WHERE proceso='impresion_carnets'
						AND archivo='$cuit'";

		$result = mysql_query($query);

		$valida = mysql_num_rows($result);

		if($valida==0){
			echo "<div class='alert alert-success'>La empresa aun no fue procesada</div>";
		}
		else{

			$d = mysql_fetch_object($result);
			echo "<div class='alert alert-warning'>La empresa fue procesada $valida antes. </div>";	
		}

		break;

	case 'listado_x_empresa':
		// code...

		if($cuit){

			$query = "CALL $base_padron.`credenciales_x_empresa`('$cuit','$fv')";

			$result=mysql_query($query) or die(mysql_error()."<br>".$query);

			$json = array();

			while ($row = mysql_fetch_assoc($result)) {
				
			    $json[] = array(
						'c' => $row['c'],			
						'd' => $row['d'],			
						'estado' => $row['estado'],			
						'fecha' => $row['fecha'],			
						'id_parentesco' => $row['id_parentesco'],						        		
						'id_afiliado' => $row['id_afiliado'],						        		
						'id_parentesco' => $row['id_parentesco'],
						'parentesco' => $row['parentesco'],						        		
						'id_titular' => $row['id_titular'],	
						'nd' => $row['nd'],
						'fn' => $row['fn'],
						'ayn' => $row['ayn'],
						'nb' => $row['nb']
						       
			      );
			}

			echo json_encode($json);
		}
		else{

			echo "ERROR - No llego la empresa";
		}

		break;

	case 'lotes_procesados':
		// code...
		
		$query = "SELECT l.id,l.archivo AS cuit,COALESCE(e.nombre,'SIN DATO') AS empresa,
							l.`cant_registros`,l.usuario,
							DATE_FORMAT(l.lote,'%d/%m/%Y') AS fecha_vencimiento,
							DATE_FORMAT(l.fechador,'%d/%m/%Y %H:%i') AS fechador,
							COUNT(*) AS q
					FROM $base_historicos.lotes l 
					JOIN $base_historicos.`credenciales_emitidas` ce ON l.id=ce.id_lote 
					LEFT JOIN $base.empresas e ON l.archivo=e.cuit 
					GROUP BY l.id ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],			
					'cuit' => $row['cuit'],			
					'empresa' => $row['empresa'],			
					'q' => $row['q'],	
					'fecha_vencimiento' => $row['fecha_vencimiento'],			
					'usuario' => $row['usuario'],						        		
					'fechador' => $row['fechador']
					       
		      );
		}

		echo json_encode($json);

		
		break;

	case 'nombre_empresa':
		// code...
		$sql = "SELECT id,cuit,nombre FROM $base.empresas WHERE cuit='$cuit' ";
		$d=mysql_fetch_object(mysql_query($sql));

		echo $d->nombre;

		break;
	
	
	case 'traer_periodos':
		
		$sql="SELECT DISTINCT b
			FROM $base.declaraciones_juradas
			WHERE a=".INST_RNOS." AND b<9000
			ORDER BY b DESC";

		$rs = mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$json = array();

		while ($row = mysql_fetch_assoc($rs)){
			
			$json[] = array("periodo" => $row['b']);
		}

		echo json_encode($json);
		break;
	
	case 'proceso_vista_previa':

		
		$call = "CALL $base_padron.padron_propios_x_seccional('$fecha')";

		$call =" SELECT * FROM tmp_credenciales_x_seccinal ORDER BY seccional,nben ";
		$rs = mysql_query($call) or die(mysql_error()."<br>".$call);

		$json = array();
		while ($row = mysql_fetch_assoc($rs)){
			
			$json[] = array(
				"id" => $row['id_afiliado'],
				"id_titular" => $row['id_titular'],
				"seccional" => $row['seccional'],
				"cuil_titular" => $row['cuil_titular'],
				"nben" => $row['nben'],
				"ayn" => $row['ayn'],
				"nd" => $row['nd'],
				"fecha_nacimiento" => $row['fecha_nacimiento'],
				"fn" => $row['fn'], 
				"vencimiento" => $row['vencimiento'],
				"venc_format" => $row['venc_format'],
				"cuit" => $row['cuit'],
				"empresa" => $row['empresa'],
				"tbt" => $row['tbt']
			);

		}

		echo json_encode($json);
		break;
	default:
		// code...
		break;
}


?>