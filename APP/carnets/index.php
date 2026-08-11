<?php  


?>

<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		
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
	<body>
		<div class="container-fluid">
			
			<div class="row">

				<!-- Div busqueda -->
				<div class="col-md-4">					
					<div class="x_panel">
						<div class="tituloDiv">
							Busqueda por CUIT
						</div>
						<div class="row" style="padding: 20px;">							
							<div class="form-group">
								<label>Ingrese el CUIT</label>
								<input type="text" id="cuit" style="width: 50%;">
								<a id="btnBuscar" class="btn btn-success" style="margin-top: 15px;">
									Buscar
								</a>
							</div>							
						</div>
					</div>
				</div>

				<!-- Div historicos -->
				<div class="col-md-8">
					<div class="x_panel">
						<div class="tituloDiv">
							Lotes generados
						</div>
						<div class="row">
							<div id="divLotes" style="padding: 20px;">
								<table id="tabLotes" class="table">
									<thead>
										<tr>
											<th>#</th>
											<th># Lote</th>
											<th>CUIT</th>
											<th>Empresa</th>
											<th>Registros</th>
											<th>Fecha vencimiento</th>
											<th>Usuario</th>
											<th>Fecha proceso</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="x_panel">
						<div class="tituloDiv">
							<b>VISTA PREVIA</b> | <span id="s_empresa"></span>
						</div>
						<div class="row">
							<input type="hidden" id="HiddenCUIT" />
							<input type="hidden" id="HiddenFV" />
							<div id="divValidacionLote" style="padding: 15px;"></div>
							<div id="divListado" style="padding: 30px;"></div>
						</div>
					</div>
				</div>
			</div>

			
		</div>
		<script>
			$(function(){	
				llena_tabla_lotes();
				
				$("#btnBuscar").on('click',function(){

					$(this).attr('disabled','disabled');
					$('#btnBuscar').html('');					
					$('#btnBuscar').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

					var fv = prompt("Cual sera la fecha de vencimiento? ", "2022-06-30");
					if (fv != null) {
												
						valida_lote();
						genera_tabla_afiliados();
						llena_tabla_afiliados(fv);
					    
					}

					setTimeout(function(){
						$("#btnBuscar").removeAttr('disabled');
						$('#btnBuscar').html('');					
						$('#btnBuscar').html('Buscar');
					},4000);
					
				})


				$("#divListado").on('click','#btnImprimir',function(){


					var imprimir = confirm('Confirma ?');

					var cuit = $('#HiddenCUIT').val();
					var fv = $('#HiddenFV').val();

					if(imprimir){

						console.log('Va a imprimir'+cuit+" "+fv);

						window.location.replace("imprimir_carnets.php?cuit="+cuit+"&fv="+fv+"&sl="+345);
						

					}
					else{
						return false;
					}


				})




			})

			function valida_lote() {
				// body...
				var cuit = $('#cuit').val();

				var datos = {
					"parametro": "valida_lote",
					"cuit": cuit
				};

				$.ajax({

					url: 'ajax.php',
					type: 'get',
					data: datos,
					success: function(data){						
						$("#divValidacionLote").html(data);
					}
				})

				var datos_2 = {
					"parametro": "nombre_empresa",
					"cuit": cuit
				};

				$.ajax({

					url: 'ajax.php',
					type: 'get',
					data: datos_2,
					success: function(data_2){						
						$("#s_empresa").html(data_2);
					}
				})
			
			}



			function llena_tabla_lotes(){


				$.getJSON('ajax.php',
							{ parametro: "lotes_procesados" },						       				
							function(data){ 
								
								$("#tabLotes tbody").html('');

								for(var i=0; i<=data.length-1 ;i++){
								
									$("#tabLotes tbody").append("<tr>"																
																		+"<td>"+(i+1)+"</td>"
																		+"<td>"+data[i]['id']+"</td>"
																		+"<td>"+data[i]['cuit']+"</td>"
																		+"<td>"+data[i]['empresa']+"</td>"
																		+"<td>"+data[i]['q']+"</td>"
																		+"<td>"+data[i]['fecha_vencimiento']+"</td>"
																		+"<td>"+data[i]['usuario']+"</td>"
																		+"<td>"+data[i]['fechador']+"</td>"																				
																	+"</tr>") ;	


								}	

								$('#tabLotes').dataTable({			    	
																	'bPaginate': true,
																	'iDisplayLength': 10000,
																	'bLengthChange': false,
																	'bFilter': true,
																	
																	'bInfo': false,										
																	'bAutoWidth': false,
																	
																	'oLanguage': {
																	    'sSearch': 'Buscar',
																	}
																});	

							}//fin function data

				);//fin getjson

			}

			function genera_tabla_afiliados(){

				$('#divListado').html("<a class='btn btn-danger' id='btnImprimir' style='margin: 20px;'>Imprimir</a>"
											+"<table id='tabAfiliados' class='table' >"
										    	+"<thead>"
										    		+"<tr>"
										    			+"<th>#</th>"
										    			+"<th>CUIL</th>"
										    			+"<th>Estado</th>"
										    			+"<th>Fecha vencimiento</th>"
										    			+"<th>Fecha nacimiento</th>"
										    			+"<th># afil</th>"
										    			+"<th>DNI</th>"	
										    			+"<th>Afiliado</th>"
										    			+"<th>Parentesco</th>"								    			
										    		+"</tr>"
										    	+"</thead>"
										    	+"<tbody>"					    		
										    	+"</tbody>"
										    +"</table>");

				$("#tabAfiliados tbody").html('<h3><i class="fas fa-sync-alt fa-spin"></i> Cargando...</h3>');

			}

			function llena_tabla_afiliados(fv){
				
				var cuit = $('#cuit').val();

				$('#HiddenCUIT').val(cuit);
				$('#HiddenFV').val(fv);
				$.getJSON('ajax.php',
							{ parametro: "listado_x_empresa", cuit: cuit, fv: fv },						       				
							function(data){ 
								
								$("#tabAfiliados tbody").html('');

								for(var i=0; i<=data.length-1 ;i++){
								
									$("#tabAfiliados tbody").append("<tr>"																
																			+"<td>"+(i+1)+"</td>"
																			+"<td>"+data[i]['d']+"</td>"
																			+"<td>"+data[i]['estado']+"</td>"
																			+"<td>"+data[i]['fecha']+"</td>"
																			+"<td>"+data[i]['fn']+"</td>"
																			+"<td>"+data[i]['nb']+"</td>"
																			+"<td>"+data[i]['nd']+"</td>"		
																			+"<td>"+data[i]['ayn']+"</td>"	
																			+"<td>"+data[i]['parentesco']+"</td>"																
																		+"</tr>") ;		
								}	


								$('#tabAfiliados').dataTable({			    	
																	'bPaginate': true,
																	'iDisplayLength': 10000,
																	'bLengthChange': false,
																	'bFilter': true,
																	
																	'bInfo': false,										
																	'bAutoWidth': false,
																	
																	'oLanguage': {
																	    'sSearch': 'Buscar',
																	}
																});	

							}//fin function data

				);//fin getjson

			}

			

		</script>
	</body>
</html>