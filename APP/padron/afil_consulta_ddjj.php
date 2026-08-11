<?php 
require("../../Config/init.inc");

?>

<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
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
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>
	</head>
	<body style="background-color: #454d55;">
		<div>
			
			<div class="col-md-12">
				<div class="col-md-4">
					<div class="x_panel">
						<div class="tituloDiv">
							Ingresar parametros de busqueda
						</div>
						<div class="row" style="padding: 5px;">
							<hr>
							
							<table class="table" style="width: 97%;">	
								<tr>
									<th>Cuil</th>
									<td>
										<input type="text" name="cuil" id="cuil" value="<?=$p_cuil;?>" />									
									</td>
								</tr>
							</table>	
							<a class="btn btn-success" id="btnConsultar" >
								<span id="spanEnviar"></span>Consultar
							</a>

							<a class="btn btn-default"  data-toggle="modal" data-target="#myModal">
								<span id="spanEnviar"></span>Nose el cuil
							</a>	

							<hr>							
							<div id="divMensaje" class="alert"></div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="x_panel">
						<div class="tituloDiv">
							Declaraciones juradas
						</div>
						<div class="row">
							<hr />
						    <table id="tabDDJJ" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>CUIT</th>
						    			<th>Empresa</th>
						    			<th>Periodo</th>						    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
				</div>

				<!-- Aportes -->
				<div class="col-md-4">
					<div class="x_panel">
						<div class="tituloDiv">
							Aportes
						</div>
						<div class="row">
							<hr />
						    <table id="tabAporte" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>CUIT</th>
						    			<th>Tipo</th>
						    			<th>Periodo</th>						    			
						    			<th>Aporte</th>				    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
				</div>
				<!-- FIN Aportes -->

			</div>
			
			<!-- Aportes y Desempleo -->
			<div class="row">
				<!-- Desempleo -->
				<div class="col-md-11">
					<div class="x_panel">
						<div class="tituloDiv">
							Desempleo
						</div>
						<div class="row">
							<hr />
						    <table id="tabDesempleo" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>Periodo</th>
						    			<th>Fecha proceso</th>
						    			<th>CUIL Titular</th>
						    			<th>Vigencia</th>	
						    			<th>parentesco</th>
						    			<th>CUIL</th>						    			
						    			<th>DNI</th>						    			
						    			<th>Apellido y nombre</th>						    			
						    			<th>Fec nacimiento</th>						    			
						    			<th>Sexo</th>						    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
				</div>
				<!-- FIN Desempleo -->
				
			</div>

		</div>

		
		

		<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">×</button>
		        <h4 class="modal-title">Consultar CUIL</h4>
		      </div>
		      <div class="modal-body">
		        <table class="table">
		        	<tr>
						<th>Sexo</th>
						<td>
							<select name="sexo" id="sexo">								
								<option value="M">Masculino</option>
								<option value="F">Femenino</option>
							</select>
						</td>
					</tr>
		        	<tr>
						<th>Dni</th>
						<td>
							<input type="number" name="dni" id="dni" />
						</td>								
					</tr>
					<tr>
						<th>Cuil</th>
						<td>
							<label id="mostrarCuil"></label>
						</td>
					</tr>
		        </table>
		        <a id="btnConsultaCuil" class="btn btn-default">Consultar</a>		        
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Listo</button>
		      </div>
		    </div>

		  </div>
		</div>

		

		<script>
			$(function(){
				
				$('#btnConsultaCuil').on('click',function(){
					
					$('#mostrarCuil').html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');	

					var datos = { "parametro": "consulta_cuil", "dni": $('#dni').val(), "sex": $('#sexo').val() };
		       				
					jQuery.ajax({	
							data: datos,
							url: "ajax.php",
							type:"GET",
							success: function(data){
								
								$('#cuil').val(data);
								$('#mostrarCuil').html('');
								$('#mostrarCuil').html(data);
								
							}
					});
					
				})

				$('#btnConsultar').on('click',function(){

					$(this).attr('disabled','disabled');
					$(this).html('');					
					$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Consultando');

					var valida = $('#cuil').val()
					
					if(valida.length!=11){
						
						mostrarMensajeError("El CUIL debe tener 11 digitos", "error");

						setTimeout(function(){ 
						
						
							$('#btnConsultar').removeAttr('disabled');
							$('#btnConsultar').html('');	
							$('#btnConsultar').html("<span id='spanEnviar'></span>Consultar"); 
							
						}, 2000);
						
						return false;
						//console.log(); false;
					}

					$('#tabDDJJ tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$('#tabAporte tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$('#tabDesempleo tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');

					//DDJJ 
					$.getJSON('ajax.php',
								{ parametro: "consulta_fisca", cuil: $('#cuil').val() },						       				
								function(data1){ 

									console.table(data1);
									
									$('#tabDDJJ tbody').html("");
									
									if(data1.length>0){
										
										$('#item0').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data1.length-1 ;i++){
				
											$("#tabDDJJ tbody").append("<tr>"
																			+"<td>"+data1[i]['cuit']+"</td>"
																			+"<td>"+data1[i]['empresa']+"</td>"			
																			+"<td  style='text-align: right;'>"+data1[i]['periodo']+"</td>"
																			/*
																			+"<td  style='text-align: right;'>"+data1[i]['remuneracion']+"</td>"
																			+"<td  style='text-align: right;'>"+data1[i]['calculado']+"</td>"
																			+"<td  style='text-align: right;'>"+data1[i]['aporte']+"</td>"					*/	      				
																		+"</tr>") ;		
										}	
									}
									else{
										
										$("#tabDDJJ tbody").append("<tr>"
																			+"<td colspan=3>No hay resultados</td>"
																				      				
																		+"</tr>") ;		

									}
																
									
									
								}//fin function data
					
					);//fin getjson


					//Aportes
					$.getJSON('ajax.php',
								{ parametro: "consulta_aportes", cuil: $('#cuil').val() },						       				
								function(data1){ 
									
									$('#tabAporte tbody').html("");
									
									if(data1.length>0){
										
										$('#item0').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data1.length-1 ;i++){
				
											$("#tabAporte tbody").append("<tr>"
																			+"<td>"+data1[i]['cuit']+"</td>"
																			+"<td>"+data1[i]['tbt']+"</td>"
																			+"<td  style='text-align: right;'>"+data1[i]['periodo']+"</td>"
																			+"<td>"+data1[i]['aporte']+"</td>"																				      				
																		+"</tr>") ;		
										}	
									}
									else{
										
										$("#tabAporte tbody").append("<tr>"
																			+"<td colspan=4>No hay resultados</td>"
																				      				
																		+"</tr>") ;		

									}
																
									
									
								}//fin function data
					
					);//fin getjson

					//Desempleo
					$.getJSON('ajax.php',
								{ parametro: "consulta_desempleo_afil", cuil: $('#cuil').val() },						       				
								function(data1){ 
									
									$('#tabDesempleo tbody').html("");
									
									if(data1.length>0){
										
										for(var i=0; i<=data1.length-1 ;i++){
				
											$("#tabDesempleo tbody").append("<tr>"
																				+"<td>"+data1[i]['periodo']+"</td>"
																				+"<td>"+data1[i]['fecha_proceso']+"</td>"
																				+"<td>"+data1[i]['cuil_titular']+"</td>"
																				+"<td>"+data1[i]['f_vigencia']+"</td>"
																				+"<td>"+data1[i]['parentesco']+"</td>"
																				+"<td>"+data1[i]['cuil']+"</td>"
																				+"<td>"+data1[i]['dni']+"</td>"
																				+"<td>"+data1[i]['ayn']+"</td>"
																				+"<td>"+data1[i]['fn']+"</td>"
																				+"<td>"+data1[i]['sexo']+"</td>"
																			+"</tr>") ;		
										}	
									}
									else{
										
										$("#tabDesempleo tbody").append("<tr>"
																			+"<td colspan=4>No hay resultados</td>"
																				      				
																		+"</tr>") ;		

									}
																
									
									
								}//fin function data
					
					);//fin getjson

					setTimeout(function(){ 
						

						$('#btnConsultar').removeAttr('disabled');
						$('#btnConsultar').html('');	
						$('#btnConsultar').html("<span id='spanEnviar'></span>Consultar"); 
						
					}, 2000);


				})


				setTimeout(function(){ 

					$('#btnConsultar').click();

				}, 1000);

				

			})

			function mostrarMensajeError(mensaje, estado){
				
				if(estado=="ok"){
					$('#divMensaje').removeClass('alert-danger')
					$('#divMensaje').addClass('alert-success')					
				}else{
					$('#divMensaje').removeClass('alert-success')
					$('#divMensaje').addClass('alert-danger')
				}
				
				$('#divMensaje').html(mensaje);
				$("#divMensaje").fadeIn("slow");				
				
				setTimeout(function(){ 
					$("#divMensaje").fadeOut("slow");		
					$('#divMensaje').html('');								
				}, 4000);
			}

		</script>
	</body>
</html>