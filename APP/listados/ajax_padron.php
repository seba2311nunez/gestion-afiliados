<?php 
include("../../Config/Conectar.inc");
mysql_query("SET NAMES 'utf8'");

if(!$_SESSION['id_user']){
	echo "ERROR - La sesion caduco"; exit();
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
	$id_user = $_SESSION['id_user'];
	$id_desreguladora = $_SESSION['id_especialidad'];

	if($_SESSION['perfil'] == 'consulta_prestador' && $_SESSION['id_especialidad']){
		
		$id_capita = $_SESSION['id_especialidad'];

		$sql="
			SELECT * 
			FROM ".N_BASE_PADRON.".desreguladoras d WHERE d.id=$id_capita";
		
		$rs = mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$d = mysql_fetch_object($rs);

		$capita_nombre = $d->convenio;

		$where_capita = " WHERE desreguladora='$capita_nombre' ";
		$capita="_$capita_nombre";

		$where_log = " AND id_especialidad=$id_capita";
		//echo "okas";exit();
	}else{
		//echo "No, usuario cargado erroneamente";exit();
		$where_capita = "";
		$capita="";
	}
}

switch ($parametro) {
	case 'guardar_filtros':
		unset($_REQUEST['parametro']);
		unset($_REQUEST['nombre_nuevo_filtro']);

		$sql="INSERT ".N_BASE_PADRON.".listados_padron_filtros (nombre,filtros) VALUES ('$nombre_nuevo_filtro','".json_encode($_REQUEST)."' )";
		mysql_query($sql) or die(mysql_error()." ".$sql);
		echo "ok";
		break;
	case 'guardar_agrupamiento':
		$sql="INSERT ".N_BASE_PADRON.".listados_padron_templates (tipo,nombre,rows,cols) VALUES ('$tipo','$nombre_group','$rows','$cols')";
		mysql_query($sql) or die(mysql_error());
		echo "ok";
		break;
	case 'listar_templates_listados_padron':
		$json = array();
		$sql="SELECT * FROM ".N_BASE_PADRON.".listados_padron_templates WHERE tipo='$tipo'";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)) {
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'lst_padron_filtros':

		$consulta_padron = GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato);
		//echo $consulta_padron['sql']; exit();
		//echo $valida_cantidad; exit();

		OutputPadronCSV($consulta_padron,$formato,$parametro);
		break;
	case 'lst_padron_filtros_csv':

		$consulta_padron = GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato);

		OutputPadronCSV($consulta_padron,$formato,$parametro);
		break;
	case 'lst_padron_apaisado_csv':

		$consulta_padron['sql'] = "SELECT d.convenio_real,pa.parentesco as parentesco_sss,IF(pa.id=0,'Titular','Familiar') AS tipo_parentesco,t.*
			FROM ".N_BASE_DEV.".tmp_padron t
			JOIN ".N_BASE_PADRON.".desreguladoras d on d.id=t.id_desreguladora
			JOIN ".N_BASE_PADRON.".parentesco pa ON t.id_parentesco=pa.id 
			";

		$sql_ins = "INSERT INTO ".N_BASE_PADRON.".log_eventos(evento,id_usuario)
						VALUES ('Descarga Apaisado',$id_user) ";
		mysql_query($sql_ins);

		OutputPadronCSV($consulta_padron,$formato,$parametro);
		break;
	case 'lst_reporte_eventos':
		
		$consulta_padron['sql'] = "
			SELECT d.convenio_real,p.cuil,p.apellido,p.nombre,cm.fecha,cm.fechador
			FROM ".N_BASE_HISTORICOS.".cambios_automaticos cm 
			JOIN ".N_BASE_PADRON.".eventos_afiliados ea ON ea.id=cm.id_evento
			JOIN ".N_BASE_PADRON.".afiliados a ON a.id=cm.id_afiliado
			JOIN ".N_BASE_PADRON.".persona p ON p.id=a.id_persona
			JOIN ".N_BASE_PADRON.".desreguladoras d ON d.id=a.id_desreguladora
			WHERE cm.id_evento IN ($id_evento) 
			AND d.id IN ($id_capita)
			AND cm.fechador BETWEEN '$fechador_desde' AND '$fechador_hasta'
		";

		$sql_ins = "INSERT INTO ".N_BASE_PADRON.".log_eventos(evento,ip,query_where,id_usuario)
						VALUES ('Reporte Altas Bajas','$ip','".addslashes($consulta_padron['sql'])."',$id_user) ";

		mysql_query($sql_ins) or die(mysql_error()."<br>".$sql_ins);

		OutputPadronCSV($consulta_padron,1,$parametro);
		break;
	case 'lst_padron_print_pantalla':
		$consulta_padron = GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato);
		OutputPadronCSV($consulta_padron,$formato,$parametro);
		break;
	case 'guardar_csv':
		$consulta_padron = GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato);
		OutputPadronCSV($consulta_padron,$formato,$parametro);
		break;
	case 'lst_traspasos_filtros':
		$where = "WHERE 1=1 ";
		$title_params = "";

		if($tipo_movimiento){
			$where = $where . "AND tipo LIKE '$tipo_movimiento"."%' ";
			$title_params = $title_params . "_". strtoupper($tipo_movimiento);
		}
		// if($gerenciadora){
		// 	$where = $where . "AND desreguladora='$gerenciadora' ";
		// 	$title_params = $title_params . "_". $gerenciadora;
		// }
		if($gerenciadora){

			$arr_gerenciadoras = explode(",", $gerenciadora);

			$arr_gerenciadoras_sql = array();
			foreach($arr_gerenciadoras as $g){
				$g = trim($g);
				if($g != ""){
					$arr_gerenciadoras_sql[] = "'" . mysql_real_escape_string($g) . "'";
				}
			}

			if(count($arr_gerenciadoras_sql) > 0){
				$where .= " AND desreguladora IN (" . implode(",", $arr_gerenciadoras_sql) . ") ";
			}
		}


		if($obra_social){
			$where = $where . "AND os_origen='$obra_social' ";	
			$title_params = $title_params . "_". $obra_social;
		}
		if($fecha_desde && $fecha_hasta){
			$where = $where . "AND fecha_vigencia BETWEEN '$fecha_desde' AND '$fecha_hasta' ";	
			$title_params = $title_params . "_". $fecha_desde . "_al_" . $fecha_hasta;
		}

		$filename = "Traspasos".$title_params.".xls";
		
		$sql_final="
			SELECT * FROM $base.traspasos_resumen_x_periodo 
			$where
		";
		//echo $sql_final;exit();
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename." ");
		header("Content-Type: text/html;charset=utf-8");
		
		mysql_query("SET NAMES 'utf8'");
		
		$result=mysql_query($sql_final) or die(mysql_error()."<br>SQL FINAL <br>".$sql_final);
		
		$tabla="";
		
		$tabla.="<table border=1>
					<tr>
						<th>CUIL</th>
						<th>Desreguladora</th>
						<th>Fecha Vigencia</th>	
						<th>OS Destino/Origen</th>
						<th>Tipo movimiento</th>
					</tr>					
		";
		
		while($d=mysql_fetch_object($result)){
					
			$tabla.="
				<tr>
					<td>$d->cuil_titular</td>
					<td>$d->desreguladora</td>
					<td>$d->fecha_vigencia</td>
					<td>$d->os_origen</td>		
					<td>$d->tipo</td>		
				</tr>					
			";
			
		}
		$tabla.="</table>";
		echo "$tabla";
		
		mysql_query("INSERT INTO ".N_BASE_PADRON.".log_eventos(evento,ip,id_usuario)
						VALUES ('Descarga traspasos','$ip',$id_user) ") or die();
		break;
	case 'listar_logs':
		$json = array();

		$sql="SELECT le.id,us.usuario,DATE_FORMAT(le.fechador,'%d/%m/%Y %H:%i') as fecha
		FROM ".N_BASE_PADRON.".log_eventos le
		JOIN $base_usuarios.users us ON le.id_usuario=us.id 
		JOIN $base_usuarios.users_modulos um ON um.id_user=us.id AND um.sistema='afiliaciones'
		WHERE evento='$tipo'
		ORDER BY le.fechador DESC
		";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}

		echo json_encode($json);

		break;	
	case 'traer_os':
		$json = array();

		$sql="SELECT * FROM ".N_BASE_PADRON.".procedencia";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)){
			
			$json[] = array(
				"codigo" => $row['codigo'],
				"procedencia" => $row['procedencia'],
			);
		}

		echo json_encode($json);
		break;
	case 'traer_gerenciadora':
		$json = array();
		//$sql="SELECT DISTINCT convenio_asignado FROM $base_usuarios.convenios_descargables cd WHERE cd.id_user=$id_user";

		
		$sql="SELECT id,convenio,convenio_real,capita
				FROM ".N_BASE_PADRON.".desreguladoras 
				WHERE convenio_real IN ( SELECT DISTINCT convenio_real FROM ".N_BASE_PADRON.".desreguladoras ORDER BY convenio_real ) ";

		if($_SESSION['usu']=="rodrigo"){


			$sql=$sql." AND id_convenio_real IN (4,7,22) ";
		}

		if(N_BASE == "ospm" && $id_user!=1){
			$sql="SELECT d.id,d.convenio,d.convenio_real,d.capita
				FROM ".N_BASE_PADRON.".desreguladoras d
				JOIN ".N_BASE_USUARIOS.".desreguladoras_descargables dd ON d.id=dd.id_desreguladora
				WHERE dd.id_user=$id_user
				";
		}
				
		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)){
			
			$json[] = $row;
		}

		echo json_encode($json);
		break;
	case 'traer_tipo_beneficiario':
		$sql="SELECT * FROM ".N_BASE_PADRON.".tipo_beneficiario_titular";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_parentescos':
		$sql="SELECT id,parentesco FROM ".N_BASE_PADRON.".parentesco";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_convenio_medico':
		$sql="SELECT * FROM ".N_BASE_PADRON.".convenios_medicos";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_provincias':
		$sql="SELECT * FROM ".N_BASE_PADRON.".provincia";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_categorias_afiliados':
		$sql="SELECT * FROM ".N_BASE_PADRON.".categoria_afiliado";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_filiales':
		$sql="SELECT * FROM ".N_BASE_PADRON.".filial";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_plan':
		$sql="SELECT * FROM ".N_BASE_PADRON.".planes";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_empresas':
		$sql="SELECT ID,cuit,nombre FROM ".N_BASE.".empresas";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_filtros':
		$json = array();
		$sql="SELECT id,nombre,filtros FROM ".N_BASE_PADRON.".listados_padron_filtros";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_eventos':

		if($id_user == 1){
			$sql="SELECT id,descripcion FROM ".N_BASE_PADRON.".eventos_afiliados";
		}else{
			$sql="
				SELECT ea.id,ea.descripcion FROM ".N_BASE_PADRON.".eventos_afiliados ea
				JOIN ".N_BASE_USUARIOS.".eventos_afiliados_descargables ead on ea.id=ead.id_evento
				WHERE ead.id_user=$id_user
			";
		}
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_periodos':
		$sql="select periodo from ".N_BASE.".`tablero_control` order by id desc";
		$rs = mysql_query($sql) or die(mysql_error());
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'traer_filtros_usados':
		$sql="SELECT filtros_cabecera FROM ".N_BASE_PADRON.".log_eventos ORDER BY id DESC LIMIT 1";
		$rs = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_assoc($rs);
		if($row){
			echo $row['filtros_cabecera'];
		}
		break;
	case 'guardar_csv_filtrado':
		$csvData = $_POST['csvData'];
		$filePath = './archivos/'.INST_NAME.'_padron_filtrado.csv'; // Replace with the desired server-side file path
		file_put_contents($filePath, $csvData);
		echo 'ok';
		break;
	case 'lst_listados_pma':
		$json = array();

		$sql="SELECT * FROM $base.listados_pma";

		$rs = mysql_query($sql) or die(mysql_error());

		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}

		echo json_encode($json);
		break;
	case 'contar_cantidad_afiliados':
		$json = array();

		#$sql="SELECT COUNT(*) AS cantidad FROM ".N_BASE_DEV.".tmp_padron_consulta";
		$consulta_padron = GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato);
		
		$rs = mysql_query($consulta_padron["sql"]) or die(mysql_error());
		$row = mysql_fetch_object($rs);

		$json['cantidad_afiliados'] = $row->cantidad;
		echo json_encode($json);
		break;
	case 'test':
		$request = $_REQUEST;

		$columns = array(
		    0   =>  'nben',
		    1   =>  'ayn',
		    2   =>  'cuil',
		    3   =>  'nd'
		);

		$getquery = GetConsultaPadron('','','',null,null,'','',null,'','','',$id_user,'');

		$sql = $getquery['sql'];
		#echo $sql;exit();
		$query = mysql_query($sql);
		$totalData = mysqli_num_rows($query);

		$totalFilter = $totalData;

		if (!empty($request['search']['value'])) {
		    $sql .= " AND (CONCAT(p.apellido,', ',p.nombre) LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR a.nben LIKE '%" . $request['search']['value'] . "%' ";
		    $sql .= " OR p.cuil LIKE '%" . $request['search']['value'] . "%' )";
		}
		$query = mysql_query($sql);
		$totalData = mysql_num_rows($query);
		$totalFilter = $totalData;

		$sql .= "ORDER BY " . $columns[$request['order'][0]['column']] . " " . $request['order'][0]['dir'] ." LIMIT " . $request['start'] . " ," . $request['length'] . " ";

		$query = mysql_query($sql);

		#echo $sql;exit();
		$data = array();
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
		case 'test_columns':
			// Assuming GetConsultaPadron returns an array with a 'sql' key containing the SQL query
			$getquery = GetConsultaPadron('','','',null,null,'','',null,'','','',$id_user,'');

			// Execute the query
			$sql = $getquery['sql'];
			$result = mysql_query($sql);

			// Dynamically generate columns based on the result set metadata
			$columns = array();
			if ($result) {
				$i = 0;
				while ($i < mysql_num_fields($result)){
					$meta = mysql_fetch_field($result, $i);
					$columns[] = array(
						"data" => $meta->name,  // The name of the column
						"title" => ucfirst($meta->name) // Set the title as the column name capitalized (you can customize this)
					);
					$i++;
				}

				$field_info = mysql_fetch_field($result);	
			}
			// Send the column definitions as JSON
			echo json_encode(array("columns" => $columns));
			break;
	case 'padron_apaisado_ultima_actualizacion':
		$sql = "SELECT DATE_FORMAT(fecha_parametro,'%d/%m/%Y') as fecha FROM $base_padron.`log_eventos` WHERE evento='ctrlPadronCompleto' ORDER BY id DESC LIMIT 1";
		$rs = mysql_query($sql);
		$data = mysql_fetch_object($rs);
		echo json_encode($data);
		break;
	default:
		exit();
		break;
}
mysql_close();


function GetConsultaPadron($gerenciadora,$tipo_beneficiario,$parentesco,$edad_desde,$edad_hasta,$excentricidad,$provincia,$incapacidad,$filial,$sexo,$ip,$id_user,$formato){//El objetivo de esta funcion es armar la query  para consultar a la BD y el titulo del archivo en caso de descarga
		//Dejo registro de la descarga
		mysql_query("SET NAMES 'utf8'");	
		mysql_set_charset('utf8');
		

		$where = "WHERE 1=1 ";
		$having = "HAVING estado like 'ALTA%'";
		if($gerenciadora){
			$where .= " AND d.id IN ($gerenciadora)";
			$title .= "_".$gerenciadora;
			$filtros_cabecera .= "Gerenciadora: ".$_REQUEST['gerenciadora_nombre']." <br> ";
		}
		if($tipo_beneficiario){
			if($tipo_beneficiario=="cero"){
				$where  .= " AND tbt.id IN ('0')";
			}else{
				$where  .= " AND tbt.id IN ($tipo_beneficiario)";
			}
			$filtros_cabecera .= "TBT: ".$_REQUEST['tipo_beneficiario_nombre']." <br> ";
		}
		if( $provincia ){
			$where  .= " AND pr.cod IN ($provincia)";
			//$title .= "_".trim($provincia_nombre);
			$filtros_cabecera .= "Provincia: ".$_REQUEST['provincia_nombre']." <br> ";
		}

		if($parentesco){				
			if($parentesco=="cero"){
				$where  .=  " AND a.id_parentesco IN ('0')";  
			}else{
				$where  .=  " AND a.id_parentesco IN ($parentesco)";
			}
			$title .= "_".trim($parentesco_nombre);
			$filtros_cabecera .= "Parentesco: ".$_REQUEST['parentesco_nombre']." <br> ";
		}
		if($filial){
			$where .= " AND f.id IN ($filial)";		
			$filtros_cabecera .= "Filial: ".$_REQUEST['filial_nombre']." <br> ";	
		}	

		if($sexo){
			$where .= " AND p.sexo = '$sexo'";
			$filtros_cabecera .= "Sexo: ". $sexo." <br> ";			
		}	
		if( !is_null($incapacidad) ){
			$where  .= " AND a.incapacidad = '$incapacidad'";
			switch ($incapacidad) {
				case '00':
					$filtros_cabecera .= "Incapacidad: NO <br> ";	
					break;
				case '01':
					$filtros_cabecera .= "Incapacidad: SI <br> ";	
					break;
			}
		}
		if( !is_null($edad_desde) && !is_null($edad_hasta) ){
			$where  .= " AND TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) BETWEEN '$edad_desde' AND '$edad_hasta' ";
			$filtros_cabecera .= "Grupo Etareo: ". $edad_desde." a ". $edad_hasta ." <br> ";
		}

		
		if( !is_null($excentricidad) ){
			switch ($excentricidad) {
				case 'formato_1':
					$where  .= " AND pr.codsss NOT IN ('01','02','04','05','07','10','13','16','18','19','23','24')";
					break;
				case 'incluir_bajas':
					unset($having);

					break;
			}
		}


		if(INST_NAME!="ospm"){
			mysql_query("CALL ".N_BASE_PADRON.".crea_padron_paso_1()") or die(mysql_error());
		}
		if(INST_NAME=="ospedyb"){
			mysql_query("CALL ".N_BASE_PADRON.".actualizaCuitEnAfiliadosDesdeDjFinal();");
		}
		
		switch ($formato) {
			case 'contar':
				$sql_seleccion = "SELECT COUNT(*) as cantidad ";
				break;
			case 2:
				// code... MODELO VISITAR
				$sql_seleccion = "SELECT a.id AS id_afiliado,a.id_titular,d.convenio AS desreguladora,CONCAT(p.apellido,' ',p.nombre) AS ayn,p.td,p.nd,p.cuil,
									p.sexo,TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) AS edad,tbt.sigla AS plan,pa.parentesco,
									l.nombreLoca as localidad,l.cp,pr.nom AS provincia,
									'' AS telefono,p.telef_celular,'ALTA' AS estado,
									dom.calle,dom.numero,dom.piso,dom.depto,
									p.fn,ec.est_civil,ps.lugar_nac AS nacionalidad ";
				break;
			
			default:

				if(INST_NAME=="osemm"){
					$sql_select_sind = "COALESCE(afs.NRO_SIND,'S/D') AS Sind_Num,";
				}

				$sql_seleccion = "SELECT 
									$sql_select_sind
									coalesce(a.nben,'S/D') as Nben,
									a.gpar as Gpar,
									COALESCE(ptt.cuil,p.cuil) AS CUIL_Titular,
									p.cuil as Cuil,
									tbt.beneficiario AS Tbt,
									COALESCE(d.convenio_real,'SIN DATO') AS Gerenciadora,
									parentesco as Parentesco,
									IF(a.id_parentesco=0,'Titular','Familiar') as Tipo_parentesco,
									CONCAT(p.apellido,', ',p.nombre) AS Nombre_y_Apellido,
									p.td as Td,
									p.nd as Nd,
									DATE_FORMAT(p.fn,'%d/%m/%Y') AS Fecha_Nacimiento,
									TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) AS Edad,
									p.sexo as Sexo,
									ge.grupo_etareo as Grupo_etareo,
									a.incapacidad as Discapacidad,
									ec.est_civil as Estado_Civil,
									p.telef_celular as Telef_celular,
									ps.abrv as Pais,				
									pr.nom AS Provincia,
									l.nombreLoca AS Localidad,
									l.cp as Codigo_Postal,calle as Calle,numero as Numero,piso as Piso,depto as Depto,
									p.email as Email,
									MID(REPLACE(tpc.estado,'@','_'),1,4) AS Estado,
									tpc.fecha_alta as Fecha_alta,
									tpc.proxima_baja as Proxima_baja,
									ps.lugar_nac AS Nacionalidad,				
									COALESCE(f.nombre,'SIN DATO') AS Filial,a.cuit__ as Cuit_empresa";
				break;
		}

		if(INST_NAME=="osemm"){
			$sql_from_sind = "LEFT JOIN ".N_BASE_HISTORICOS.".afili_sindicato afs ON tpc.cuil = afs.CUIL";
		}
		$sql_from = "
			FROM ".N_BASE_DEV.".tmp_padron_consulta tpc
			$sql_from_sind
			JOIN ".N_BASE_PADRON.".afiliados a ON tpc.id_afiliado=a.id		
			JOIN ".N_BASE_PADRON.".persona p ON a.id_persona=p.id
			LEFT JOIN ".N_BASE_PADRON.".grupos_etareos ge ON TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) BETWEEN ge.desde AND ge.hasta
			JOIN ".N_BASE_PADRON.".domicilio dom ON p.id_domicilio=dom.id 
			LEFT JOIN ".N_BASE_PADRON.".localidad l ON dom.id_localidad=l.id 
			LEFT JOIN ".N_BASE_PADRON.".pais ps ON ps.id=p.id_nacionalidad
			LEFT JOIN ".N_BASE_PADRON.".provincia pr ON l.provincia=pr.cod	
			LEFT JOIN ".N_BASE_PADRON.".estadocivil ec ON p.id_estado_civil=ec.id			
			LEFT JOIN ".N_BASE_PADRON.".filial f ON f.id=a.filial
			LEFT JOIN ".N_BASE_PADRON.".afiliados att ON att.id=a.id_titular
			LEFT JOIN ".N_BASE_PADRON.".persona ptt ON ptt.id=att.id_persona
			LEFT JOIN ".N_BASE_PADRON.".desreguladoras d ON a.id_desreguladora=d.id 
			LEFT JOIN ".N_BASE_PADRON.".parentesco pa ON pa.id=a.id_parentesco
			LEFT JOIN ".N_BASE_PADRON.".tipo_beneficiario_titular tbt ON tbt.id=IF(a.id_titular=0,a.id_tipo_aporte,att.id_tipo_aporte)
			$where 
			#ORDER BY IF(ptt.cuil IS NULL,p.cuil,ptt.cuil),pa.id
		";


		$sql=$sql_seleccion.$sql_from;
		#echo $sql; exit();
		$where = str_replace("'", "", $where);

		$sql_ins = "INSERT INTO ".N_BASE_PADRON.".log_eventos(evento,ip,query_where,filtros_cabecera,id_usuario)
						VALUES ('Consulta padron','$ip','$sql_favoritos','$filtros_cabecera',$id_user) ";

		mysql_query($sql_ins) or die(mysql_error()."<br>".$sql_ins);

		$id_log = mysql_insert_id();

		return array('sql' => $sql, 'title' => $title, 'id_log' => $id_log);
}
function PreExcelPadron($consulta_padron){
	$title = $consulta_padron['title'];	
	$filename = "Padron_".strtoupper(INST_NAME)."_".date('Ymd') .".xls";
	//echo $filename; exit();
	header("Content-Disposition: attachment; filename=".$filename." ");
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Type: text/html;charset=utf-8");
}
function OutputPadron($consulta_padron,$formato,$parametro){
	$sql = $consulta_padron['sql'];
	$title = $consulta_padron['title'];
	$result=mysql_query($sql) or die(mysql_error()."<br>SQL FINAL <br>".$sql);

	$valida_cantidad = mysql_num_rows($result); 
	if($valida_cantidad==0){
		echo "<!DOCTYPE html>
				<html>
				<head>
				    <title>Mensaje de Error</title>
				    <style>
				        /* Estilos del contenedor del mensaje de error */
				        .error-container {
				            background-color: yellow;
				            color: black;
				            padding: 20px;
				            text-align: center;
				            border: 2px solid #ffc107;
				        }

				        /* Estilos del icono de alerta (puedes usar una fuente de iconos o una imagen SVG) */
				        .error-icon {
				            font-size: 24px;
				            margin-right: 10px;
				        }
				    </style>
				</head>
				<body>
				    <div class='error-container'>
				        <span class='error-icon'>&#9888;</span> <!-- Icono de alerta: ⚠ -->
				        No se encontraron registros con esos parametros.
				    </div>
				</body>
				</html>
				"; exit();
	}else{

		switch ($parametro) {
			case 'lst_padron_filtros':
				UpdatearLog($consulta_padron['id_log'],$valida_cantidad);
				PreExcelPadron($consulta_padron);
				break;
			case 'lst_padron_print_pantalla':
				UpdatearLog($consulta_padron['id_log'],$valida_cantidad);
				break;
		}

		switch ($formato) {
			case 1:
				
				$tabla="";
			
				$tabla.="
					<table border=1>
						<tr>
							<th>Cuil Titular</th>
							<th>Cuil</th>
							<th>tipo_beneficiario</th>
							<th>Gerenciadora</th>	
							<th>Parentesco</th>
							<th>Tipo parentesco</th>
							<th>AyN</th>
							<th>Tipo Documento</th>
							<th>Numero Documento</th>					
							<th>Fn</th>	
							<th>Edad</th>
							<th>Grupo etareo</th>
							<th>Sexo</th>					
							<th>Incapacidad</th>
							<th>Estado</th>	
							<th>Fecha alta</th>	
							<th>Est_civil</th>								
							<th>Tel</th>
							<th>Pais</th>			
							<th>Provincia</th>
							<th>Localidad</th>					
							<th>CP</th>
							<th>Calle</th>
							<th>Numero</th> 
							<th>Piso</th> 
							<th>Depto</th>					
							<th>Filial</th>
						</tr>
						"
				;
				
				while($d=mysql_fetch_object($result)){

					$estado_funcion = explode("@", $d->estado);
					$estado = $estado_funcion[0];
					$fecha_alta = $estado_funcion[1];
					
					$incapacidad = $d->incapacidad. '';

					$tabla.=
						"<tr>
							<td>$d->cuil_titular</td>
							<td>$d->cuil</td>
							<td>$d->tbt</td>
							<td>$d->desreguladora</td>
							<td>".utf8_decode($d->parentesco)."</td>
							<td>$d->tipo_parentesco</td>
							<td>$d->ayn</td>
							<td>$d->td</td>
							<td>$d->nd</td>					
							<td>$d->fn</td>	
							<td>$d->edad</td>
							<td>$d->grupo_etareo</td>
							<td>$d->sexo</td>
							<td>$incapacidad</td>
							<td>$estado</td>
							<td>$d->fecha_alta</td>	
							<td>$d->est_civil</td>					
							<td>$d->telef_celular</td>
							<td>$d->pais</td>
							<td>$d->provincia</td>
							<td>$d->localidad</td>
							<td>$d->cp</td>
							<td>$d->calle</td>
							<td>$d->numero</td> 
							<td>$d->piso</td> 
							<td>$d->depto</td>	
							<td>$d->filial</td>
						</tr>"
					;	
				}
				$tabla.="</table>";					        		
				
				echo "$tabla";
				break;
			
			case 2:
				
				$tabla="";
			
				$tabla.="
					<table border=1>
						<tr>
							<th>id_afiliado</th>
							<th>id_titular</th>
							<th>desreguladora</th>
							<th>ayn</th>	
							<th>td</th>
							<th>nd</th>
							<th>cuil</th>
							<th>sexo</th>
							<th>edad</th>					
							<th>plan</th>	
							<th>parentesco</th>
							<th>localidad</th>
							<th>cp</th>					
							<th>provincia</th>
							<th>telefono</th>	
							<th>telef_celular</th>								
							<th>estado</th>
							<th>fecha alta</th>
							<th>calle</th>			
							<th>numero</th>
							<th>piso</th>					
							<th>depto</th>
							<th>fn</th>
							<th>est_civil</th> 
							<th>nacionalidad</th> 
							
						</tr>
						"
				;
				
				while($d=mysql_fetch_object($result)){

					$estado_funcion = explode("@", $d->estado);
					$estado = $estado_funcion[0];
					$fecha_alta = $estado_funcion[1];
					
					$incapacidad = $d->incapacidad. '';

					$tabla.=
						"<tr>
							<td>$d->id_afiliado</td>
							<td>$d->id_titular</td>
							<td>$d->desreguladora</td>
							<td>$d->ayn</td>
							<td>$d->td</td>
							<td>$d->nd</td>					
							<td>$d->cuil</td>							
							<td>$d->sexo</td>
							<td>$d->edad</td>
							<td>$d->plan</td>
							<td>$d->parentesco</td>
							<td>$d->localidad</td>					
							<td>$d->cp</td>
							<td>$d->provincia</td>
							<td>$d->telefono</td>
							<td>$d->telef_celular</td>
							<td>$d->estado</td>
							<td>$d->fecha_alta</td>
							<td>$d->calle</td>
							<td>$d->numero</td> 
							<td>$d->piso</td> 
							<td>$d->depto</td>	
							<td>$d->fn</td>
							<td>$d->est_civil</td>
							<td>$d->nacionalidad</td>
						</tr>"
					;	
				}
				$tabla.="</table>";					        		
				
				echo "$tabla";
				break;

			default:
				
				$tabla="";
				
				$t_head.="
					<table border=1>
						<tr>
							<th>Cuil Titular</th>
							<th>Cuil</th>
							<th>tipo_beneficiario</th>
							<th>Gerenciadora</th>	
							<th>Parentesco</th>
							<th>Tipo parentesco</th>
							<th>AyN</th>
							<th>Tipo Documento</th>
							<th>Numero Documento</th>					
							<th>Fn</th>	
							<th>Edad</th>
							<th>Grupo etareo</th>
							<th>Sexo</th>					
							<th>Incapacidad</th>
							<th>Estado</th>	
							<th>Fecha alta</th>	
							<th>Est_civil</th>								
							<th>Tel</th>
							<th>Pais</th>			
							<th>Provincia</th>
							<th>Localidad</th>					
							<th>CP</th>
							<th>Calle</th>
							<th>Numero</th> 
							<th>Piso</th> 
							<th>Depto</th>					
							<th>Filial</th>
						</tr>
						"
				;
				echo $t_head;
				while($d=mysql_fetch_object($result)){

					$estado_funcion = explode("@", $d->estado);
					$estado = $estado_funcion[0];
					$fecha_alta = $estado_funcion[1];
					
					$incapacidad = $d->incapacidad. '';

					$t_row=
						"<tr>
							<td>$d->cuil_titular</td>
							<td>$d->cuil</td>
							<td>$d->tbt</td>
							<td>$d->desreguladora</td>
							<td>".utf8_decode($d->parentesco)."</td>
							<td>$d->tipo_parentesco</td>
							<td>$d->ayn</td>
							<td>$d->td</td>
							<td>$d->nd</td>					
							<td>$d->fn</td>	
							<td>$d->edad</td>
							<td>$d->grupo_etareo</td>
							<td>$d->sexo</td>
							<td>$incapacidad</td>
							<td>$estado</td>
							<td>$d->fecha_alta</td>	
							<td>$d->est_civil</td>					
							<td>$d->telef_celular</td>
							<td>$d->pais</td>
							<td>$d->provincia</td>
							<td>$d->localidad</td>
							<td>$d->cp</td>
							<td>$d->calle</td>
							<td>$d->numero</td> 
							<td>$d->piso</td> 
							<td>$d->depto</td>	
							<td>$d->filial</td>
						</tr>"
					;	
					echo "$t_row";
				}
				echo "</table>";					        		
				
				break;
		}
	}
}
function OutputPadronCSV($consulta_padron,$formato,$parametro){
	$sql = $consulta_padron['sql'];
	$title = $consulta_padron['title'];

	$result=mysql_query($sql) or die(mysql_error()."<br>SQL FINAL <br>".$sql);

	$filename = "Padron_".strtoupper(INST_NAME)."_".date('Ymd');

	if($parametro == 'lst_padron_apaisado_csv'){
		$filename = strtoupper(INST_NAME)."_Padron_DJAP";
	}

	if($parametro == 'lst_reporte_eventos'){
		$filename = strtoupper(INST_NAME)."_";
	}

	$valida_cantidad = mysql_num_rows($result); 
	if($valida_cantidad==0){
		switch ($parametro) {
			case 'lst_padron_filtros_csv':
				echo "<!DOCTYPE html>
					<html>
					<head>
					    <title>Mensaje de Error</title>
					    <style>
					        /* Estilos del contenedor del mensaje de error */
					        .error-container {
					            background-color: yellow;
					            color: black;
					            padding: 20px;
					            text-align: center;
					            border: 2px solid #ffc107;
					        }

					        /* Estilos del icono de alerta (puedes usar una fuente de iconos o una imagen SVG) */
					        .error-icon {
					            font-size: 24px;
					            margin-right: 10px;
					        }
					    </style>
					</head>
					<body>
					    <div class='error-container'>
					        <span class='error-icon'>&#9888;</span> <!-- Icono de alerta: ⚠ -->
					        No se encontraron registros con esos parametros.
					    </div>
					</body>
					</html>
					"; exit();
				break;
			case 'lst_padron_apaisado_csv':
				echo "<!DOCTYPE html>
					<html>
					<head>
					    <title>Mensaje de Error</title>
					    <style>
					        /* Estilos del contenedor del mensaje de error */
					        .error-container {
					            background-color: yellow;
					            color: black;
					            padding: 20px;
					            text-align: center;
					            border: 2px solid #ffc107;
					        }

					        /* Estilos del icono de alerta (puedes usar una fuente de iconos o una imagen SVG) */
					        .error-icon {
					            font-size: 24px;
					            margin-right: 10px;
					        }
					    </style>
					</head>
					<body>
					    <div class='error-container'>
					        <span class='error-icon'>&#9888;</span> <!-- Icono de alerta: ⚠ -->
					        No se encontraron registros con esos parametros.
					    </div>
					</body>
					</html>
					"; exit();
				break;
			case 'lst_padron_print_pantalla':
				echo "0 registros";
				break;
			case 'guardar_csv':
				echo "0 registros";
				break;
			case 'lst_reporte_eventos':
				echo "0 registros";
				break;
		}
	}else{
		//echo "sh zip_result.sh \"$sql\" \"$filename\"";exit();
    $result = shell_exec("sh zip_result.sh \"$sql\" \"$filename\"");

		switch ($parametro) {
			case 'lst_padron_filtros_csv':
				UpdatearLog($consulta_padron['id_log'],$valida_cantidad);

				if ($result === null) {
					echo "Error executing the MySQL query: " . shell_exec('echo $?');
				} else {
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=$filename.zip");
					readfile("$filename.zip");
				}
				break;
			case 'lst_padron_apaisado_csv':
				

				if ($result === null) {
					echo "Error executing the MySQL query: " . shell_exec('echo $?');
				} else {
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=$filename.zip");
					readfile("$filename.zip");
				}
				break;
			case 'lst_reporte_eventos':
				

				if ($result === null) {
					echo "Error executing the MySQL query: " . shell_exec('echo $?');
				} else {
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=$filename.zip");
					readfile("$filename.zip");
				}
				break;
			case 'lst_padron_print_pantalla':
				UpdatearLog($consulta_padron['id_log'],$valida_cantidad);
				break;
			case 'guardar_csv':
				UpdatearLog($consulta_padron['id_log'],$valida_cantidad);
				break;
		}

	}
}
function OutputReporte($query){
	$result=mysql_query($query) or die(mysql_error()."<br>SQL FINAL <br>".$query);

	$tabla="";

	$tabla.="
		<table border=1>
			<tr>
				<th>Cuil Titular</th>
				<th>Cuil</th>
				<th>tipo_beneficiario</th>
				<th>Gerenciadora</th>	
				<th>Parentesco</th>
				<th>Tipo parentesco</th>
				<th>AyN</th>
				<th>Tipo Documento</th>
				<th>Numero Documento</th>					
				<th>Fn</th>	
				<th>Edad</th>
				<th>Grupo etareo</th>
				<th>Sexo</th>					
				<th>Incapacidad</th>
				<th>Estado</th>	
				<th>Est_civil</th>								
				<th>Tel</th>
				<th>Pais</th>			
				<th>Provincia</th>
				<th>Localidad</th>					
				<th>CP</th>
				<th>Calle</th>
				<th>Numero</th> 
				<th>Piso</th> 
				<th>Depto</th>					
				<th>Filial</th>
			</tr>
			"
	;
	
	while($d=mysql_fetch_object($result)){

		$estado_funcion = explode("@", $d->estado);
		$estado = $estado_funcion[0];
		$fecha_alta = $estado_funcion[1];
		
		$incapacidad = $d->incapacidad. '';

		$tabla.=
			"<tr>
				<td>$d->cuil_titular</td>
				<td>$d->cuil</td>
				<td>$d->tbt</td>
				<td>$d->desreguladora</td>
				<td>".utf8_decode($d->parentesco)."</td>
				<td>$d->tipo_parentesco</td>
				<td>$d->ayn</td>
				<td>$d->td</td>
				<td>$d->nd</td>					
				<td>$d->fn</td>	
				<td>$d->edad</td>
				<td>$d->grupo_etareo</td>
				<td>$d->sexo</td>
				<td>$incapacidad</td>
				<td>$estado</td>
				<td>$d->est_civil</td>					
				<td>$d->telef_celular</td>
				<td>$d->pais</td>
				<td>$d->provincia</td>
				<td>$d->localidad</td>
				<td>$d->cp</td>
				<td>$d->calle</td>
				<td>$d->numero</td> 
				<td>$d->piso</td> 
				<td>$d->depto</td>	
				<td>$d->filial</td>
			</tr>"
		;	
	}
	$tabla.="</table>";	
}
function CrearCSV($consulta_padron,$formato,$filetype){
	$sql = $consulta_padron['sql'];
	$title = $consulta_padron['title'];
	$result = mysql_query($sql);
	if (!$result) {
	    die("Error executing query: " . mysql_error());
	}else{
		$valida_cantidad = mysql_num_rows($result); 

		if($valida_cantidad==0){
			echo "0 registros"; exit();
			echo "<!DOCTYPE html>
					<html>
					<head>
					    <title>Mensaje de Error</title>
					    <style>
					        /* Estilos del contenedor del mensaje de error */
					        .error-container {
					            background-color: yellow;
					            color: black;
					            padding: 20px;
					            text-align: center;
					            border: 2px solid #ffc107;
					        }

					        /* Estilos del icono de alerta (puedes usar una fuente de iconos o una imagen SVG) */
					        .error-icon {
					            font-size: 24px;
					            margin-right: 10px;
					        }
					    </style>
					</head>
					<body>
					    <div class='error-container'>
					        <span class='error-icon'>&#9888;</span> <!-- Icono de alerta: ⚠ -->
					        No se encontraron registros con esos parametros.
					    </div>
					</body>
					</html>
					"; exit();
		}else{

			UpdatearLog($consulta_padron['id_log'],$valida_cantidad);

			$fieldMapping = array(
		    'filial' => 'Filial',
		    'desreguladora' => 'Gerenciadora',
		    'ayn' => 'Nombre_y_Apellido',
		    'fn' => 'Fecha Nacimiento',
		    'cuil_titular' => 'CUIL_Titular',
		    'cp' => 'Codigo Postal',
		    'incapacidad' => 'Discapacidad',
		    'nd' => 'DNI',
		    'est_civil' => 'Estado_Civil',
		    'td' => 'Tipo_Documento',
		    'cuil' => 'CUIL'
			);

			// Generate CSV file
			$outputFile = __DIR__."/archivos/".INST_NAME."_".$filetype.".csv";
			//echo $outputFile;exit();
			$output = fopen($outputFile, "w");
			// Get column names and use them as CSV header
			$header = array();
			while ($fieldInfo = mysql_fetch_field($result)) {
				$fieldName = $fieldInfo->name;
				if (isset($fieldMapping[$fieldName])) {
			    $header[] = $fieldMapping[$fieldName];
				} else {
				  $header[] = ucfirst($fieldName);
				}
			}
			fputcsv($output, $header,",",'"');
			// Write CSV data
			
			while ($row = mysql_fetch_assoc($result)) {
			    fputcsv($output, $row,",",'"');
			}

			fclose($output);
			// Close MySQL connection
			echo "CSV file generated successfully.";
			mysql_close($conn);
		}
	}
}
function UpdatearLog($id_log,$cantidad){
	$sql="UPDATE ".N_BASE_PADRON.".log_eventos SET filtros_cabecera=CONCAT('Cantidad de Registros: ',$cantidad,' <br> ',filtros_cabecera) WHERE id=$id_log";
	mysql_query($sql) or die(mysql_error()." ".$sql);
}
?>
