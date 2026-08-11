<?php 
ini_set("allow_url_fopen", 1);
header('Access-Control-Allow-Origin: *');
include ("../../Config/Conectar.inc");
header('Content-Type: application/json');
$id_user = $_SESSION['id_user'];
$usuario = $_SESSION['usuario'];

switch($parametro){
	case 'listar_prestaciones':
		
		$sql = "
			SELECT ld.fecha AS fecha_prestacion,mf.numero_factura,mp.cuit,mp.nombre AS nombre_prestador,l.id AS id_liquidacion, ld.id AS id_detalle, nn.`descripcion`,ld.importe,ld.debito,ld.importe-ld.debito AS a_pagar,ld.usuario AS usuario_carga,ld.timestamp AS fecha_carga 
			FROM $base_padron.persona p 
			JOIN $base_ppdev.liquidacion_detalle ld ON ld.id_persona=p.id
			JOIN $base_ppdev.liquidacion l ON l.id=ld.id_liquidacion
			JOIN $base_ppdev.mesaentradas_factura mf ON mf.id=l.id_factura
			JOIN $base_ppdev.mesaentradas_prestador mp ON mp.id=mf.id_prestador
			JOIN $base_ppdev.nomenclador_nacional nn ON nn.`id`=ld.`id_nomenclador`
			WHERE 1=1
			AND p.cuil='$cuil'
			AND ld.`fecha` BETWEEN '$desde' AND '$hasta'
			ORDER BY 1 DESC
		";

		
		$rs = mysql_query($sql);

		if(!$rs){
			$error = mysql_error()."<br>".$sql;
			$json = array('error' => $error);
			echo json_encode($json);exit();
		}
		if(mysql_num_rows($rs)==0){
			$json = array('sin_registros' => 'sin_registros');
			echo json_encode($json);
		}else{
			while($row = mysql_fetch_assoc($rs)){
				$json['consumos'][] = $row;
			}
			//Si tiene registros busco el resto de los datos
			$sql="
				SELECT d.convenio_real, CONCAT(p.apellido,', ',p.nombre) as ayn 
				FROM $base_padron.persona p
				JOIN $base_padron.afiliados a ON p.id=a.id_persona
				JOIN $base_padron.desreguladoras d ON d.id=a.id_desreguladora
				WHERE p.cuil='$cuil'
			";
			$rs = mysql_query($sql);
			$row = mysql_fetch_assoc($rs);
			$json['datos_personales'] = $row;

			echo json_encode($json);exit();
		}
		break;
	case 'listar_prestaciones_por_practica':
		$sql = "
			SELECT ld.fecha AS fecha_prestacion,mf.numero_factura,mp.cuit,mp.nombre AS nombre_prestador,l.id AS id_liquidacion, ld.id AS id_detalle, nn.`descripcion`,ld.importe,ld.debito,ld.importe-ld.debito AS a_pagar,ld.usuario AS usuario_carga,ld.timestamp AS fecha_carga 
			FROM $base_padron.persona p 
			JOIN $base_ppdev.liquidacion_detalle ld ON ld.id_persona=p.id
			JOIN $base_ppdev.liquidacion l ON l.id=ld.id_liquidacion
			JOIN $base_ppdev.mesaentradas_factura mf ON mf.id=l.id_factura
			JOIN $base_ppdev.mesaentradas_prestador mp ON mp.id=mf.id_prestador
			JOIN $base_ppdev.nomenclador_nacional nn ON nn.`id`=ld.`id_nomenclador`
			WHERE 1=1
			AND nn.id='$prestacion'
			AND ld.`fecha` BETWEEN '$desde' AND '$hasta'
		";

		
		$rs = mysql_query($sql);

		if(!$rs){
			$error = mysql_error()."<br>".$sql;
			$json[] = array('error' => $error);
			echo json_encode($json);exit();
		}
		if(mysql_num_rows($rs)==0){
			$json[] = array('sin_registros' => 'sin_registros');
			echo json_encode($json);
		}else{
			while($row = mysql_fetch_assoc($rs)){
				$json[] = $row;
			}
			echo json_encode($json);exit();
		}
		break;
}
exit();

?>