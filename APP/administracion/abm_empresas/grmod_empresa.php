<?php include "../../../Config/init.inc";

	startTransaction();//comienza transaccion

//Busca ID usuario logueado//
$usuario_activo=$_SESSION["usu"];
$id_usr=$_SESSION["id_user"]; 

//Fin Busca ID usuario logueado//
$sql  = "
	update empresa
	set
		id_usuario=$id_usr, cuit='$cuit',nombre='$nombre',direccion='$dir',
		localidad='$localidad',telefono='$telefono',email='$emailpersona', contacto='$contacto',provincia='no se',
		codigopostal='$codpos', estado_aportes='$calificacion', id_rubro=$rubro	
		where id = $idemp
	";
//	echo $sql."<br>";exit();
	mysql_query($sql) or die(breakTransaction($sql));
/*
//SI cambio el estado de la empresa 
	if($clasificacion_anterior!=$calificacion){
	
	//--------------*************AGREGAR: HISTORICO DE AFILIADO CAMBIO DE PLAN QUE REQUIERE MOTIVO; EMPRESA MOROSA O EMPRESA SIN DEUDA
	if($calificacion=="Sin Deuda"){	
		//PASO A PLAN ANTERIOR,EN CASO QUE NO TENGA PORQUE NUNCA FUE MOROSA, MANTIENE EL ACTUAL
		$sql="UPDATE afiliados a,campos_afiliados_sin_preventa_ni_opcion c SET a.id_plan_medico=IF(a.id_plan_anterior IS NULL,a.id_plan_medico,a.id_plan_anterior)
				WHERE c.`id_afiliado`=a.id AND id_empresa=$idemp";
		mysql_query($sql) or die(breakTransaction($sql));	

		$sql="UPDATE afiliados a,persona p,preventa pr SET a.id_plan_medico=IF(a.id_plan_anterior IS NULL,a.id_plan_medico,a.id_plan_anterior)
 				WHERE p.id=a.id_persona AND pr.id_persona=p.id AND pr.id_empresa=$idemp";
		mysql_query($sql) or die(breakTransaction($sql));	
		//Crear eventos
		$afil="SELECT * FROM 
				(
				SELECT a.id,'10' AS evento,COALESCE(pl.nombre,pa.nombre) AS plan FROM afiliados a
				JOIN campos_afiliados_sin_preventa_ni_opcion c ON c.id_afiliado=a.id AND id_empresa=$idemp
				LEFT JOIN planes pl ON pl.id=a.id_plan_anterior
				LEFT JOIN planes pa ON pa.id=a.id_plan_medico)v1
				UNION
				SELECT a.id,'10' AS evento,COALESCE(pl.nombre,pa.nombre) AS plan FROM afiliados a
				JOIN persona p ON p.id=a.id_persona
				JOIN preventa pr ON pr.id_persona=p.id AND pr.id_empresa=$idemp
				LEFT JOIN planes pl ON pl.id=a.id_plan_anterior
				LEFT JOIN planes pa ON pa.id=a.id_plan_medico";
		$rs=mysql_query($afil);
		while($d=mysql_fetch_object($rs)){
			$insert_historico="insert into historico_afiliados (id_afiliado,id_evento,id_usuario)values($d->id,$d->evento,$id_usr)";
			mysql_query($insert_historico) or die(breakTransaction($insert_historico));
			$id_historico= mysql_insert_id();
			$sql_motivo="INSERT INTO motivo_modificacion_campos
			(id_historico,nombre_tabla,motivo_modificacion,nombre_campo,valor_anterior)values
			($id_historico,'afiliados','Empresa Sin Deuda','planmedico','PMO')
			";
			mysql_query($sql_motivo) or die(breakTransaction($sql_motivo));
			
		}
		
	}
	
	if($calificacion=="Morosa"){
		//--------------GUARDO EL PLAN ANTERIOR
		$sql="UPDATE afiliados a,campos_afiliados_sin_preventa_ni_opcion c SET a.`id_plan_anterior`=a.`id_plan_medico` 
				WHERE c.`id_afiliado`=a.id AND id_empresa=$idemp and a.id_plan_medico!=2";
		mysql_query($sql) or die(breakTransaction($sql));
		
		$sql="UPDATE afiliados a,persona p,preventa pr SET a.`id_plan_anterior`=a.`id_plan_medico` 
 				WHERE p.id=a.id_persona AND pr.id_persona=p.id AND pr.id_empresa=$idemp and a.id_plan_medico!=2";
		mysql_query($sql) or die(breakTransaction($sql));
		
		//----------------PASO A PMO
		$sql="UPDATE afiliados a,campos_afiliados_sin_preventa_ni_opcion c SET a.id_plan_medico=2
				WHERE c.`id_afiliado`=a.id AND id_empresa=$idemp";
		mysql_query($sql) or die(breakTransaction($sql));	

		$sql="UPDATE afiliados a,persona p,preventa pr SET a.id_plan_medico=2
 				WHERE p.id=a.id_persona AND pr.id_persona=p.id AND pr.id_empresa=$idemp";
		mysql_query($sql) or die(breakTransaction($sql));
		
	//Crear eventos
		$afil="SELECT * FROM 
				(
				SELECT a.id,'10' as evento,pl.nombre AS plan FROM afiliados a
				JOIN campos_afiliados_sin_preventa_ni_opcion c ON c.id_afiliado=a.id AND id_empresa=$idemp
				left JOIN planes pl ON pl.id=a.`id_plan_anterior`)v1
				UNION
				SELECT a.id,'10' as evento,pl.nombre AS plan FROM afiliados a
				JOIN persona p ON p.id=a.id_persona
				JOIN preventa pr ON pr.id_persona=p.id AND pr.id_empresa=$idemp
				left JOIN planes pl ON pl.id=a.id_plan_anterior";
		$rs=mysql_query($afil);
		while($d=mysql_fetch_object($rs)){
			$insert_historico="insert into historico_afiliados (id_afiliado,id_evento,id_usuario)values($d->id,$d->evento,$id_usr)";
			mysql_query($insert_historico) or die(breakTransaction($insert_historico));
			$id_historico= mysql_insert_id();
			$sql_motivo="INSERT INTO motivo_modificacion_campos
			(id_historico,nombre_tabla,motivo_modificacion,nombre_campo,valor_anterior)values
			($id_historico,'afiliados','Empresa Morosa','planmedico','$d->plan')
			";
			mysql_query($sql_motivo) or die(breakTransaction($sql_motivo));
			
		}
		
	}
}
	commitTransaction();
	mysql_close($conexion);
*/
	?>
	<script type="text/javascript">transaccionEfectuadaOk("<?php echo "ver_empresas.php?c=$cuit&n=$nombre"?>");</script>




