<?php 
include ("../../../Config/Conectar.inc");

#echo "lugar: ".INST_NAME; exit();

switch ($parametro) {
	case 'TraerProcesados':
		$json = array();

		$sql="SELECT id,descripcion,archivo,cant_registros,usuario,id_usuario,estado,DATE_FORMAT(fechador,'%d/%m/%Y %H:%i') AS fechador
			FROM $base_historicos.lotes
			WHERE proceso='opciones_nuevas'
			AND id_usuario!=1
			ORDER BY id DESC";

		$rs = mysql_query($sql) or die(mysql_error()."<br>".$sql);

		if(mysql_num_rows($rs)>0){
			while ($row = mysql_fetch_assoc($rs)){
				
				$json[] = $row;
			}
		}else{
			$json[] = array('error'=>'error');
		}
		echo json_encode($json);
		break;
	case 'procesar_lote':

		if(INST_NAME=="ospilm"){
			$cond_convenio_real = "";
		}
		else{
			$cond_convenio_real = " AND d.real=0 ";
		}

		$sql="
			INSERT INTO $base_padron.log_sincronizacion(id_afiliado,estado,tipo_dato,dato_actual,dato_nuevo,tipo_lote,id_lote,id_row,accion)
			SELECT af.id,'aceptado','desreguladora',af.id_desreguladora,l.id_usuario as dato_nuevo,'manual',$id_lote,op.id,''
			FROM $base_padron.afiliados af 
			JOIN $base_padron.desreguladoras d ON d.id=af.id_desreguladora
			JOIN $base_padron.persona p ON af.id_persona=p.id 
			JOIN $base_historicos.opciones_nuevas op ON op.cuil=p.cuil 
			JOIN $base_historicos.lotes  l ON l.id=op.id_lote
			WHERE op.id_lote IN ($id_lote)				
				AND l.id_usuario!=af.id_desreguladora
				$cond_convenio_real
				  ;

		";//Guardo el cambio realizado en afiliados que tienen una desreguladora desde antes, ignoro las altas_pendientes y la desreguladora que estoy usando para procesar este lote
		
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="
			UPDATE $base_padron.afiliados af 
			JOIN $base_padron.desreguladoras d ON d.id=af.id_desreguladora
			JOIN $base_padron.persona p ON af.id_persona=p.id 
			JOIN $base_historicos.opciones_nuevas op ON op.cuil=p.cuil 
			JOIN $base_historicos.lotes l ON l.id=op.id_lote
			SET af.id_desreguladora=l.id_usuario
			WHERE op.id_lote IN ($id_lote)
				AND l.id_usuario!=af.id_desreguladora
				$cond_convenio_real

		";//Updateo la id_desreguladora en afiliados

		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="
		UPDATE $base_padron.persona p 
		JOIN $base_historicos.opciones_nuevas op ON op.cuil=p.cuil 
		SET p.fn=op.fn,p.sexo=op.sexo
		WHERE op.id_lote IN ($id_lote)"
		;
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="
			UPDATE $base_padron.persona p 
			JOIN $base_historicos.opciones_nuevas op ON op.cuil=p.cuil 
			SET p.apellido = MID(op.`ayn`,1,LOCATE(' ',op.ayn)), p.`nombre` = MID(op.`ayn`,LOCATE(' ',op.ayn)+1,99) 
			WHERE op.id_lote IN ($id_lote) AND (p.apellido LIKE '%DDJJ%' OR p.apellido LIKE '%APORTES%') AND op.cuil!=0
		";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		mysql_query("CALL $base_padron.actualiza_domicilio_altas_tempranas();") or die(mysql_error());

		$sql="UPDATE $base_historicos.lotes SET estado='procesado' WHERE id=$id_lote";//Updateo en lotes el estado de la cabecera

		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		echo "ok";
		break;
	case 'desreguladoras':
		
		$query = "SELECT *
				FROM $base_padron.desreguladoras 
				ORDER BY convenio ASC ";

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
	case 'carga_previa_solicitudes':
		if(isset($_FILES['file_precarga']) && $_FILES['file_precarga']['error']==0) {
			require_once "../../../Lib/PHPExcel/Classes/PHPExcel.php";
			$tmpfname = $_FILES['file_precarga']['tmp_name'];
			$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
			$excelObj = $excelReader->load($tmpfname);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();

			

			
			echo "<table class=\"table table-sm\">";
			echo "<tr><th>CUIL</th><th>Apellido y Nombre</th><th>F. Nac.</th><th>Sexo</th></tr>";
			for ($row = 3; $row <= $lastRow; $row++) {
				if($worksheet->getCell('B'.$row)->getValue()){
					echo "<tr><td scope=\"row\">";
					echo $worksheet->getCell('B'.$row)->getValue();
					echo "</td><td>";
					echo $worksheet->getCell('G'.$row)->getValue()." ".$worksheet->getCell('H'.$row)->getValue();
					echo "</td><td>";
					echo $worksheet->getCell('I'.$row)->getValue();
					echo "</td><td>";
					echo $worksheet->getCell('J'.$row)->getValue();
					echo "</td><tr>";
				}
			}
			echo "</table>";	
		}
		break;
	case 'grabar_solicitudes':
		if(isset($_FILES['file_precarga']) && $_FILES['file_precarga']['error']==0) {
			require_once "../../../Lib/PHPExcel/Classes/PHPExcel.php";
			$tmpfname = $_FILES['file_precarga']['tmp_name'];
			$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
			$excelObj = $excelReader->load($tmpfname);
			$worksheet = $excelObj->getSheet(0);
			$lastRow = $worksheet->getHighestRow();

			$sql_lote="INSERT INTO $base_historicos.lotes (descripcion,archivo,proceso,usuario,id_usuario) VALUES ('2022-05-15','".$_FILES['file_precarga']['name']."','opciones_nuevas','$desreguladora','$id_desreguladora')";
			mysql_query($sql_lote) or die("error ".mysql_error());
			$id_lote=mysql_insert_id();
			$q = 0;
			//echo "<table class=\"table table-sm\">";
			for ($row = 3; $row <= $lastRow; $row++) {
				if($worksheet->getCell('B'.$row)->getValue()){
					$q++;

					$date = str_replace('/', '-', $worksheet->getCell('I'.$row)->getValue());
					$fn = date('Y-m-d', strtotime($date));

					$sql_ins="INSERT INTO $base_historicos.opciones_nuevas (id_lote,cuil,ayn,fn,sexo) 
						VALUES('".$id_lote."',
								'".$worksheet->getCell('B'.$row)->getValue()."',
								'".$worksheet->getCell('G'.$row)->getValue()." ".$worksheet->getCell('H'.$row)->getValue()."',
								'".$fn."',
								'".$worksheet->getCell('J'.$row)->getValue()."')";
					mysql_query($sql_ins) or die("error ".mysql_error()." ".$sql_ins);
				}
			}
			$sql_upd="UPDATE $base_historicos.lotes l SET cant_registros=$q WHERE id=$id_lote";
			mysql_query($sql_upd) or die("error ".mysql_Error());
			echo $id_lote;
		}
		break;
	case 'ver_lote_procesado':
		$sql="CALL $base_historicos.TR_mostrar_lote_aceptado($id_lote)";
		$rs = mysql_query($sql) or die(mysql_error()." ".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
	case 'ver_archivo_cargado':
		$sql="SELECT o.cuil,DATE_FORMAT(o.fn,'%d/%m/%Y') AS fn,o.ayn,o.sexo,
			o.provincia,o.localidad,o.cp,o.domicilio
		FROM $base_historicos.`opciones_nuevas` o
		WHERE o.id_lote=$id_lote";
		$rs = mysql_query($sql) or die(mysql_error()." ".$sql);
		$json = array();
		while ($row = mysql_fetch_assoc($rs)){
			$json[] = $row;
		}
		echo json_encode($json);
		break;
}
?>