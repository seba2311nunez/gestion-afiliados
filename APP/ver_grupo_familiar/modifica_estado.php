<?php 
include("../../Config/Conectar.inc");

$id_usuario = $id_usr = $_SESSION['iduser'];

if($eliminar=="eliminar"){
		
	$eliminar="";
	if($tipo_fecha=='Alta'){
		$tabla="altas_manuales";
	}else{
		$tabla="bajas_manuales";
	}
	$sql="delete from $tabla where id=$id_fecha";
	if(!mysql_query($sql)){
		echo "<h2>error al eliminar fecha</h2><br>";
	}
	$sql="insert into historico_afiliados(id_afiliado,id_evento,id_usuario)values($id_afiliado,10,$id_usr)";
	//echo $sql;exit();
	if(!mysql_query($sql)){
		echo "<h2>error insert historico</h2><br>";
	}
	$id_historico=mysql_insert_id();
	$sql="insert into motivo_modificacion_campos(id_historico,nombre_tabla,nombre_campo,motivo_modificacion,valor_anterior)values($id_historico,'$tipo_fecha','$tipo_fecha','Eliminacion fecha de $tipo_fecha','$fechaapartir')";
	if(!mysql_query($sql)){
		echo "<h2>error insert motivo</h2><br>";
	}
}	


if($b15=="Grabar"){
		
		
	// ---------------------------------------------------------Pregunta si se esta realizando ALTA
	if($operacion=="Alta"){
		// Graba en historico_afiliados
			/*
			$sql_inserta_historico="INSERT INTO historico_afiliados (id_afiliado,id_evento,id_usuario)
											VALUES ($id_afiliado,12,$id_usr)"; //Evento=11: bajas manuales
			if(!mysql_query($sql_inserta_historico)){echo "Error al grabar"; exit();}
			$id_historico=mysql_insert_id();
			*/
		// Fin Graba en historico afiliados
		
		// INSERT en tabla altas_manuales
			$sql_insert_alta_baja_manual="INSERT INTO altas_manuales (id_afiliado,fecha_aPartir,motivo,id_usuario) 
									 		VALUES ($id_afiliado,'$fechaeleccion','$motivo',$id_usuario)";
		// Fin INSERT en tabla altas_manuales
	
		if(!mysql_query($sql_insert_alta_baja_manual)){
			echo "Error al grabar"; exit();
		}else{
			$id_row_baja=mysql_insert_id();
			//$sql_update_id_row="UPDATE historico_afiliados SET id_row=$id_row_baja WHERE id=$id_historico";
			//echo $sql_update_id_row;
			//if(!mysql_query($sql_update_id_row)){echo "Error al grabar"; exit();}
		}	

	}		
			
			
	// ---------------------------------------------------------Pregunta si se esta realizando BAJA
			
	else{
		
		// INSERT en tabla bajas_manuales
			$sql_insert_alta_baja_manual="INSERT INTO bajas_manuales (id_afiliado,fecha_aPartir,motivo,id_usuario) 
									 VALUES ($id_afiliado,'$fechaeleccion','$motivo',$id_usuario)";
		// Fin INSERT en tabla bajas_manuales
					
		if(!mysql_query($sql_insert_alta_baja_manual)){
			echo "Error al grabar"; exit();
		}else{ // Si OK
			
			$id_row_baja=mysql_insert_id();
		} //------FIN SI OK

		if($tipo_afil=="Titular"){

			$sql_fam = "SELECT id
							FROM afiliados
							WHERE id_titular=$id_afiliado ";
			$rs_fam = mysql_query($sql_fam) or die(mysql_error().$sql_fam);
			if(mysql_num_rows($rs_fam)>0){

				while ($d_fam = mysql_fetch_object($rs_fam)) {
				
					// INSERT en tabla bajas_manuales
						$sql_insert_baja_manual="INSERT INTO bajas_manuales (id_afiliado,fecha_aPartir,motivo,id_usuario) 
												 VALUES ($d_fam->id,'$fechaeleccion','$motivo',$id_usuario)";
					// Fin INSERT en tabla bajas_manuales
								
					if(!mysql_query($sql_insert_baja_manual)){
						echo "Error al grabar"; exit();
					}else{ // Si OK
						
						$id_row_baja=mysql_insert_id();
					} //------FIN SI OK

				}	

			}
		}
	 
	}// ---------------------------------------------------------FIN BAJA				
		
	
	echo "<script>
			alert('Grabado con exito!!');
			window.opener.location.reload();
			window.close();
		</script>";

}//Fin Grabar	

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		
		<!-- Bootstrap -->
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		
		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>
		
		
		<script>
			function esconderDiv(idElementoAEsconder){
			
				var div = document.getElementById(idElementoAEsconder);
				//Verifica el estatus original del elemento , para decidir si la acción es ocultar o mostrar
				
				if(div.style.display==='block'){
				//si esta visible se modifica a ocultar
				div.style.display = "none";
				}else{
				//Si esta oculto de despliega
				div.style.display = "block";
				}
			}
			
			function confirmar ( mensaje){
				return confirm( mensaje );
			}
		</script>
	</head>
	
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-8">
					<div class="x_panel">
						
						<div class="tituloDiv">Modificar datos del Afiliado</div>
						<br>
						<hr>
						
						<form action="modifica_estado.php" id="form1" name="form1" METHOD="post" >			
							<table class="table" >
								<tr>
									<input type="hidden" id="id_afiliado" name="id_afiliado" value=<?php echo $id_afiliado;?>>
									<input type="hidden" id="operacion" name="operacion" value=<?php echo $operacion;?>>
									<input type="hidden" id="fec_alta" name="fec_alta" value=<?php echo $fec_alta;?>>
									<input type="hidden" id="fec_baja" name="fec_baja" value=<?php echo $fec_baja;?>>
									<input type="hidden" id="tipo_afil" name="tipo_afil" value=<?php echo $tipo_afil;?>>
									
									<th align=left  bgcolor=#336699><font face=verdana size=2  color=white>Ultima Fecha de Alta existente</th>	
									<td><?php if($fec_alta!=="--"){echo $fec_alta;}else{echo "No se le ha dado de alta aun";}?></td>
								</tr>
								<tr>
									<th align=left  bgcolor=#336699><font face=verdana size=2  color=white>Ultima Fecha de Baja existente</th>	
									<td><?php if($fec_baja!=="--"){echo $fec_baja;}else{echo "No se le ha dado de baja aun";}?></td>
								</tr>
								<tr>
									<th align=left  bgcolor=#336699><font face=verdana size=2  color=white>Nueva Fecha de <?php echo $operacion;?></th>	
									<td>
										<input type="date" id="fechaeleccion" name="fechaeleccion" size= 40  maxlength=40 >
									</td>
								</tr>
								<tr>
								<th align=left  bgcolor=#336699><font face=verdana size=2  color=white>Motivo de <?php echo $operacion;?></th>	
									
											<td>
										<?php
										 if ($operacion=='Baja'){
												
										 	$sql_motivo = "SELECT * FROM motivos_bajas WHERE tipo_parentesco='$tipo_afil' ";
											$rs_motivo = mysql_query($sql_motivo);
											
											echo "<select  id='motivo' name='motivo' >";
											
											while($d_motivo = mysql_fetch_object($rs_motivo)){
											 	
												echo "<option value='$d_motivo->motivo'>$d_motivo->motivo</option>";
											}
											
											echo "</select>";
											/*
										 	if($tipo_afil=='titular'){?>
													<select  id="motivo" name="motivo" >
														<option value='Opcion de cambio'>Opcion de cambio</option>
														<option value='Fallecimiento'>Fallecimiento</option>
														<option value='Cese relacion laboral'>Cese relacion laboral</option>
														<option value='Jubilacion'>Jubilacion</option>
													</select>
													<?}else{?>
													<select  id="motivo" name="motivo" >
														<option value='Mayoria de edad'>Mayoria de edad</option>
														<option value='Fallecimiento'>Fallecimiento</option>
														<option value='Falta de documentacion'>Falta de documentacion</option>
														<option value='Vencimiento de documentacion'>Vencimiento de documentacion</option>
														<option value='Inicio de relacion laboral'>Inicio de relacion laboral</option>
														<option value='Baja concubino/a'>Baja concubino/a</option>
														<option value='A cargo de otro titular'>A cargo de otro titular</option>
														<option value='Solicitada por el titular'>Solicitada por el titular</option>
													
													</select>
											<?}
											*/		
										 }else{
											 if($motivo_baja=='Opcion de cambio' && $estado_afil=='BAJA'){
											 	echo "<script>alert('ATENCION: La ultima baja fue por opcion de cambio. Para dar de alta al afiliado debe generar una nueva previa preventa.');</script>";
											 }?>
											<textarea type="text" id="motivo" name="motivo" cols="46" rows="3" maxlength=40 class="form-control"></textarea>		
											<?php 
										 }?>
								</tr>
							</table>
							<br>
							<center>
							<input type="submit" name="b15" id="b15" value="Grabar">
					
							
							
							
							<input type="hidden" name="tipo_afil" id="tipo_afil" value="<?php echo $tipo_afil;?>">	
						</form>
						<br>
					</div>
					
				</div>
			</div>
			
			
			
			<div class="row">				
				<div class="col-md-11">
					<div class="x_panel">
						<div class="tituloDiv">
							Historial altas/bajas
						</div>
						<div>
							<table  class="table">
								<thead>	
									<tr>
										<th class='table-sortable:number'>N</th>
										<th class='table-sortable:date'>Tipo</th>
										<th class='table-sortable:date'>Fecha Vigencia</th>
										<th class='table-sortable:default table-filterable'>Motivo</th>	
										<th class='table-sortable:default table-filterable'>Usuario</th>	
										<th>Eliminar</th>				
									</tr>
								</thead>	
								<tbody>
									<?php 
									
									$n=1;
									
									mysql_query("DROP TEMPORARY TABLE IF EXISTS tmp_mov_estados");
									
									mysql_query("CREATE TEMPORARY TABLE tmp_mov_estados 
													SELECT id AS id_fecha,fecha_aPartir,motivo,'Baja manual' AS tipo,id_historico,id_usuario 
														FROM bajas_manuales 
														WHERE id_afiliado=$id_afiliado");
									
									mysql_query("INSERT INTO tmp_mov_estados (id_fecha,fecha_aPartir,motivo,tipo,id_historico,id_usuario)
													SELECT id AS id_fecha,fecha_aPartir,motivo,'Alta manual' AS tipo,id_historico,id_usuario 
													FROM altas_manuales 
													WHERE id_afiliado=$id_afiliado");
									
									mysql_query("INSERT INTO tmp_mov_estados (id_fecha,fecha_aPartir,motivo,tipo,id_historico,id_usuario)
													SELECT a.id,fecha_aPartir,'Alta por archivo SSS' as motivo,'Alta SSS' as tipo,0,192
														FROM altas_SSS a 
														JOIN cabeceras_SSS c ON a.id_cabecera=c.id 
														WHERE cuil=$cuil ");
									
									mysql_query("INSERT INTO tmp_mov_estados (id_fecha,fecha_aPartir,motivo,tipo,id_historico,id_usuario)
													SELECT a.id,fecha_aPartir,'Baja por archivo SSS' as motivo,'Baja SSS' as tipo,0,192
														FROM bajas_SSS a 
														JOIN cabeceras_SSS c ON a.id_cabecera=c.id 
														WHERE cuil=$cuil ");
									
									$sql_historico="SELECT * FROM(
													SELECT * FROM
													(SELECT id AS id_fecha,fecha_aPartir,motivo,'Baja' AS tipo,id_historico,id_usuario FROM bajas_manuales WHERE id_afiliado=$id_afiliado)a
													UNION
													SELECT id AS id_fecha,fecha_aPartir,motivo,'Alta' AS tipo,id_historico,id_usuario FROM altas_manuales WHERE id_afiliado=$id_afiliado
													)b
													LEFT JOIN $base_usuarios.users su ON su.id=b.id_usuario
													ORDER BY fecha_aPartir DESC";
													
									$sql_historico="SELECT * FROM tmp_mov_estados b
														LEFT JOIN $base_usuarios.users su ON su.id=b.id_usuario
														ORDER BY fecha_aPartir DESC";				
									$rs_historico=mysql_query($sql_historico);
													
									while($d=mysql_fetch_object($rs_historico)){
										
										//$fecha_aPartir=t_fec1($d->fecha_aPartir);
										//$control_user=habilitarLink();
										
										if(substr($d->tipo, 5,6) =="manual"){
												
											echo "<tr>
													<td>$n</td>
													<td>$d->tipo</td>
													<td>$d->fecha_aPartir</td>
													<td>$d->motivo</td>
													<td>$d->nombre</td>
													<td>
													<a $control_user=\"altaBajaManualPopUp.php?operacion=$operacion&id_afiliado=$id_afiliado&fec_alta=$fec_alta&fec_baja=$fec_baja&tipo_afil=$tipo_afil&eliminar=eliminar&tipo_fecha=$d->tipo&id_fecha=$d->id_fecha&fechaapartir=$fecha_aPartir\"
															 Onclick=\"return confirmar('Elimina $d->tipo $fecha_aPartir?');\">Eliminar</a>
													</td>
												</tr>";
												$n++;
										}
										else{
											
											echo "<tr>
													<td>$n</td>
													<td>$d->tipo</td>
													<td>$fecha_aPartir</td>
													<td>$d->motivo</td>
													<td>$d->nombre</td>
													<td>
														Eliminar
													</td>
												</tr>";
												$n++;
											
										}
										
										
									}
									
									?>				
								</tbody>
								
							</table>
						</div>	
					</div>
					
				</div>				
			</div>	
		</div>
	</body>	
	
</html>