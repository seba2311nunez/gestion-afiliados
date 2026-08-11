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
switch ($parametro) {
	//Las consultas para todos los selectores y tambien esta la consulta de CUIL al final.

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
	case 'delegacion':
		
		$query = "SELECT id,deleg_nombre,es_central
						FROM delegaciones
						WHERE codigo_os=".INST_RNOS;

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'delegacion' => $row['deleg_nombre'],		
					'es_central' => $row['es_central']
					       
		      );
		}
		echo json_encode($json);
		break;
	case 'seccional':
		
		$query = "SELECT id,
						f.cod_filial,
						CONCAT(COALESCE(f.cod_filial,''),' - ',COALESCE(f.nombre,'')) as seccional
						FROM filial f
						ORDER BY 2 
						 ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],
					'cod_filial' => $row['cod_filial'],		
					'seccional' => $row['seccional']
					       
		      );
		}

		echo json_encode($json);


		break;

	case 'tbt':
		
		$query = "SELECT * FROM tipo_beneficiario_titular ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'beneficiario' => $row['beneficiario'],		
					'codsss' => $row['codsss']
					       
		      );
		}

		echo json_encode($json);


		break;

	case 'revista':
		
		$query = "SELECT * FROM situacion_revista ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'revista' => $row['revista'],		
					'codsss' => $row['codsss']
					       
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

	case 'patologias':
		# code...
		$query = "SELECT * FROM $base_padron.patologias ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'nombre' => $row['nombre']
					       
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

	case 'tipo_documentacion':
		# code...
		$query = "SELECT * FROM tipo_documentacion";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = array(
					'id' => $row['id'],		
					'documentacion' => $row['documentacion']
					       
		      );
		}

		echo json_encode($json);

		break;

	case 'plan_medico':
		$query = "SELECT * FROM $base_padron.planes";
		$result=mysql_query($query) or die(mysql_error()."<br>".$query);
		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = $row;
		}

		echo json_encode($json);
		break;

	case 'tipo_movimiento':
		# code...
		$query = "SELECT * 
					FROM $base_padron.eventos_afiliados 
						WHERE estado='$me_movimiento'
							AND id NOT IN (22,23,24,25) ";

		$result=mysql_query($query) or die(mysql_error()."<br>".$query);

		$json = array();

		while ($row = mysql_fetch_assoc($result)) {
			
		    $json[] = $row;
		}
		echo json_encode($json);
		break;
	//Consulta de CUIL
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
	//FIN - Consulta de CUIL

	
	default:
		# code...
		break;
}

?>