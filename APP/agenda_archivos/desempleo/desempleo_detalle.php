<?php
require(__DIR__."/../../../Conectar.inc");
$periodo = mysql_fetch_object(mysql_query("SELECT archivo FROM $base_historicos.lotes WHERE id=$id_lote"))->archivo;
?>

<html>
	<head>
		<!-- Jquery -->
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		
		<!-- Bootstrap -->
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		
		<!-- Databatables -->
		<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		
		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src='http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js'></script>
	</head>
	<body>
		<div class="container-fluid">
			<div style="margin: 20px;">
				<h3>
					Si no muestra nada, actualizar la tabla desempleo_distribucion
				</h3>
				
			</div>
			<div class="col-md-5">
				<div class="x_panel">
					<div class="tituloDiv">
						Resumen	
					</div>
					<div class="row" style="padding: 5px;">
						<hr>
						<table class="table" style="width: 95%;">
							<thead>
								<tr>
									<th>Convenio</th>
									<th>Rango desde</th>
									<th>Rando hasta</th>
									<th>Masculino</th>
									<th>Femenino</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?
									mysql_query("CALL $base_padron.desempleo_calculo($id_lote)") or die(mysql_error()."<br> Error ejecutando el proceso");
									
									$sql="SELECT COALESCE(d.convenio,'sin capita') AS convenio,
													e_desde,e_hasta,
													ROUND(SUM(IF(d.sexo='M',importe,0)),2) AS masculino,
													ROUND(SUM(IF(d.sexo='F',importe,0)),2) AS femenino,
													ROUND(SUM(importe),2) AS total 
													
												FROM $base_padron.tmp_desempleo d
												JOIN $base_padron.desempleo_distribucion dd ON edad BETWEEN dd.e_desde 
														AND dd.e_hasta AND d.sexo=dd.sexo 
														AND d.fproceso BETWEEN dd.desde AND dd.hasta 
												GROUP BY 1,2,3
												ORDER BY 2,3,1";
												
									$result = mysql_query($sql);
									
									$total = 0;
									
									while($d = mysql_fetch_object($result)){
										
										echo "<tr>
												<td>$d->convenio</td>
												<td style='text-align: right;'>$d->e_desde</td>
												<td style='text-align: right;'>$d->e_hasta</td>
												<td style='text-align: right;'>$d->masculino</td>
												<td style='text-align: right;'>$d->femenino</td>
												<td style='text-align: right;'>$d->total</td>
											  </tr>";
											  
										$total+=$d->total ;
									}
									
								?>
							</tbody>
						</table>
						<hr>
						<label style="margin-left: 20px;">
							Total $ <?php echo number_format($total,2,',','.');?>
						</label>
					</div>
				</div>
				<hr>
				<div class="x_panel">
					<div class="tituloDiv">
						Resumen	como el archivo DY4LC15.SYS7.Mxxxx.P11160
					</div>
					<div class="row" style="padding: 5px;">
						<hr>
						<table class="table" style="width: 95%;">
							<thead>
								<tr>
									<th>Edad desde</th>
									<th>Edad hasta</th>
									<th>Masculinos</th>
									<th>Total M</th>
									<th>Femeninos</th>
									<th>Total F</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<?
																		
									$sql="SELECT e_desde,e_hasta,
												SUM(q_m) AS q_m,
												SUM(masculino) as masculino,
												SUM(q_f) AS q_f,
												SUM(femenino) as femenino,
												SUM(total) AS total
											FROM $base_padron.tmp_desempleo_202007
											GROUP BY e_desde,e_hasta";
												
									$result = mysql_query($sql);
									
									$total = 0;
									
									while($d = mysql_fetch_object($result)){
										
										$m = number_format($d->masculino,2,",",".");
										$f = number_format($d->femenino,2,",",".");

										echo "<tr>
												
												<td style='text-align: right;'>$d->e_desde</td>
												<td style='text-align: right;'>$d->e_hasta</td>
												<td style='text-align: right;'>$d->q_m</td>
												<td style='text-align: right;'>$m</td>
												<td style='text-align: right;'>$d->q_f</td>
												<td style='text-align: right;'>$f</td>
												<td style='text-align: right;'>$d->total</td>
											  </tr>";
											  
										$total+=$d->total ;
									}
									
								?>
							</tbody>
						</table>
						<hr>
						<label style="margin-left: 20px;">
							Total $ <?php echo number_format($total,2,',','.');?>
						</label>
					</div>
				</div>
			</div>
			
			<div class="col-md-7">
				<div class="x_panel">
					<div class="tituloDiv">
						Detalle
					</div>
					<div class="row" style="padding: 5px;">
						<hr>
						<a class="btn btn-success btn-sm" href="ajax.php?parametro=desempleo_detalle_xls&id_lote=<?=$id_lote;?>&periodo=<?=$periodo;?>"> Excel</a>
						<hr>
						<table class="table" style="width: 95%;">
							<thead>
								<tr>
									<th>CUIL familiar</th>
									<th>CUIL Titular</th>
									<th>Afiliado</th>
									<th>Fecha vigencia</th>
									<!--
									<th>Fec inicio rel</th>
									<th>Fecha cese</th>
									-->
									<th>Edad</th>
									<!--
									<th>Fecha proceso</th>
									-->
									<th>Sexo</th>
									<th>Convenio</th>
									<th>Importe</th>									
								</tr>
							</thead>
							<tbody>
								<?
									$result_detalle = mysql_query("SELECT d.cuil,d.cuil_titular,d.ayn,d.fec_vigencia,fec_inicio_rel,d.fec_cese,
																	edad,fproceso,d.sexo,d.convenio,dd.importe
																FROM $base_padron.tmp_desempleo d
																JOIN $base_padron.desempleo_distribucion dd ON edad BETWEEN dd.e_desde 
																									AND dd.e_hasta AND d.sexo=dd.sexo 
																									AND d.fproceso BETWEEN dd.desde AND dd.hasta
																ORDER BY convenio,cuil_titular,cuil") 
													or die(mysql_error()."<br> Error ejecutando consulta detalle");
																		
									while($d_detalle = mysql_fetch_object($result_detalle)){
										
										$importe = number_format($d_detalle->importe,2,",",".");

										echo "<tr>
												<td>$d_detalle->cuil</td>
												<td>$d_detalle->cuil_titular</td>
												<td>$d_detalle->ayn</td>
												<td>$d_detalle->fec_vigencia</td>
																				
												<td style='text-align: right;'>$d_detalle->edad</td>
												
												<td>$d_detalle->sexo</td>
												<td>$d_detalle->convenio</td>
												<td style='text-align: right;'>$importe</td>												
											  </tr>";
											  
										$total+=$d->total ;
									}
									
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>