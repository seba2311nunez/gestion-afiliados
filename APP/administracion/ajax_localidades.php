<?php
ini_set('display_errors', 1); 
ini_set('log_errors',1); 
error_reporting(E_ALL); 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include('../../Config/Conectar.inc');

if(!$_SESSION['id_user']){
	echo "Su session caduco, por favor vuelva a loguearse"; exit();
}
else{
	$id_usuario = $_SESSION['id_user'];	
}


switch ($parametro){

	case 'guardar_edicion_localidad':
		$sql="UPDATE $base_padron.localidad 
		SET provincia=$provincia, cp=$cp, nombreLoca='$nombreLoca', id_provincia_revisado=$provincia_revisado, cp_revisado=$cp_revisado, nombreLoca_revisado='$nombreLoca_revisado' 
		WHERE id=$id_localidad";
		
		mysql_query($sql) or die(mysql_error()."<br>".$sql);
		
		echo "ok";
		break;
	case 'traer_datos_localidades':
		$sql="
			SELECT l.id,l.provincia,l.cp,l.nombreLoca,l.id_provincia_revisado,l.cp_revisado,l.nombreLoca_revisado
			FROM $base_padron.localidad l
			WHERE l.id=$id_localidad
		";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_localidades':
		$request = $_REQUEST;
		//Dependiendo el renderizado de las columnas en el listado en pantalla. Se debe mapear de la misma manera el siguiente array para el correcto comportamiento del ordenado de filas asc/desc
		$columns = array(
			0 => 'id',
			1 => '',
			2 => 'provincia',
			3 => 'cp',
			4 => 'localidad',
			5 => 'provincia_revisado',
			6 => 'cp_revisado',
			7 => 'nombreLoca_revisado',
			8 => 'verif_basica',
		);
		
		$sql="
			SELECT l.id,pr.nom as provincia,l.cp,l.nombreLoca, pr2.nom as provincia_revisado,l.cp_revisado,l.nombreLoca_revisado,
				IF(
					(l.provincia = 1 AND l.cp*1 BETWEEN 1001 AND 1440) 
					OR (l.provincia != 1 AND l.cp*1 BETWEEN 1601 AND 9421),
					0,
					1
				) AS verif_basica
			FROM $base_padron.localidad l
			left join $base_padron.provincia pr on l.provincia=pr.cod
			LEFT JOIN $base_padron.provincia pr2 ON l.id_provincia_revisado=pr2.cod
			WHERE 1=1
		";

		#echo $sql;exit();
		$query = mysql_query($sql);
		$totalData = mysql_num_rows($query);
		
		if (!empty($request['search']['value'])) {
		    $sql .= " AND (pr.nom LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR l.cp LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR l.nombreLoca LIKE '%" . $request['search']['value'] . "%' )";
		}

		if($request['order'][0]['column'] != 0){
		}
		$sql .= " ORDER BY " . $columns[$request['order'][0]['column']] . " " . $request['order'][0]['dir'];

		$sql .= " LIMIT " . $request['start'] . " ," . $request['length'] . " ";

		#echo $sql;exit();
		$query = mysql_query($sql);
		#$totalFilter = mysql_num_rows($query);
		$totalFilter  = $totalData;
		
		while ($row = mysql_fetch_object($query)) {
		    $data[] = $row;
		}
		
		$json_data = array(
		    "draw" => intval($request['draw']),
		    "recordsTotal" => intval($totalData),
		    "recordsFiltered" => intval($totalFilter),
		    "data" => $data
		);

		echo json_encode($json_data);
		break;

	case 'traer_provincias':
		$sql="
			SELECT cod,nom
			FROM $base_padron.provincia l
		";

		$result=mysql_query($sql) or die(mysql_error()."<br>".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = $row;
		}
		echo json_encode($json);
		break;
}
?>