<html>
	<head>
		<meta charset="utf-8">
		<!-- Jquery -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<!-- Bootstrap -->
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		<!-- PNotify -->
	    <link href="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
	    <link href="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
	    <link href="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
		
	</head>
	<body>
		<div class="container-fluid">
		
			<div class="col-md-12">
				<div class="x_panel">
					<div class="tituloDiv">
						 <b>Afiliados a Procesar</b> 
					</div>					
				</div>

			</div>
			<div class="col-md-12">
				<div class="x_panel">
					<div class="row">
						<div>
							<a class="btn btn-default"  style="margin-left: 30px;" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
					    		Filtrar por:
					    	</a>	
						</div>
						<div class="collapse" id="collapseExample">
				    		<table class="table" style="width: 300px;">
				    			<thead>
				    				<tr>
										<th>test</th>
										<th>test</th>
									</tr>	
				    			</thead>
								<tbody></tbody>									
							</table>
							<h1>E</h1>
						</div>
					</div>
					<div class="row">
						<table class="table" id="ProcesoTabla">
							<thead style="background-color:#ccf3ff;">
								<tr>
									<th>#</th>
									<th></th>
									<th>Estado</th>
									<th>Origen</th>
									<th>Tipo</th>
									<th>CUIL</th>
									<th>ND</th>
									<th>Nombre</th>
									<th>Apellido</th>									
									<th>CUIL Titular</th>
									<th>Sexo</th>
									<th>Nacimiento</th>
								</tr> 
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</body>

	<script>
		$(function(){
			CargarLista()

		})

		function DerivarPadron(element) {
			var estado_actual = $(element).closest('tr').children('td:eq(2)').text();
			var id_aprobacion = $(element).closest('tr').children('td:eq(0)').text();
			var Something = $(element).closest('tr').children('td:eq(5)').text();
			//alert(estado_actual);
			var datos = {
					"parametro": "estado_visto",
					"cuil": Something,
					"id_aprobacion": id_aprobacion,
					"estado": "visto"
				};

			if(estado_actual=="visto"){
				new PNotify({
		                                  title: 'ERROR',
		                                  text: 'Insercion Fallida',
		                                  type: 'error',
		                                  styling: 'bootstrap3'
		                            });
			}else{
				$.ajax({
				
					url: 'ajax_listado_maestro.php',
					type: 'get',
					data: datos,
					success: function(data){						
						
						if(data=="ok"){
							
							new PNotify({
		                                  title: 'Insercion de Estado',
		                                  text: 'Estado modificado con exito',
		                                  type: 'success',
		                                  styling: 'bootstrap3'
		                            });
		                     
		                    CargarLista();
		                    window.open("../importadores/importador_general.php?id_aprobacion="+id_aprobacion);
		                            
						}
						else{
							
							new PNotify({
		                                  title: 'ERROR',
		                                  text: 'Insercion Fallida',
		                                  type: 'error',
		                                  styling: 'bootstrap3'
		                            });
						}
						
					}
				})

			}
		}
	function CargarLista(){
	$.getJSON('ajax_listado_maestro.php',{parametro:'listar'},
				function(data){
					if(data[0]['error']){
										console.log(data[0]['error'])
							}
							else{
									$('#ProcesoTabla tbody').html("");
									
									if(data.length>0){
										for(var i=0; i<=data.length-1 ;i++){
				
											$("#ProcesoTabla tbody").append("<tr id="+data[i]['id']+"><form>"
																			+"<td>"+data[i]['id']+"</td>"
																				+"<td><div class='btn-group btn-group-default'>									<button style='margin-left: 20%; margin-right: auto;' 						data-toggle='dropdown' class='btn btn-default dropdown-toggle' 			style='height: 34px;' type='button'>										<i class='fa fa-ellipsis-v' aria-hidden='true'>							</i>																	</button>																<ul class='dropdown-menu'><li>			<a class='btnModificar'  onclick='DerivarPadron(this)' <!--href='FormAprobacion.php?id="+data[i]['id']+"-->' data-toggle='modal'>					<i class='far 					fa-edit'></i>&nbsp; Agregar al Padron</a> 						</li>							</ul>							</div>							</td>"
																			+"<td class='Two'>"+data[i]['estado']+"</td>"
																			+"<td>"+data[i]['archivo_origen']+"</td>"
																			+"<td>"+data[i]['tipo']+"</td>"
																			+"<td>"+data[i]['cuil']+"</td>"
																			+"<td>"+data[i]['nd']+"</td>"	
																			+"<td>"+data[i]['nombre']+"</td>"	
																			+"<td>"+data[i]['apellido']+"</td>"	
																			+"<td>"+data[i]['cuil_titular']+"</td>"	
																			+"<td>"+data[i]['sexo']+"</td>"
																			+"<td>"+data[i]['fn']+"</td>"			
																		+"<form></tr>") ;		
										}	
									}
									else{
										
									}
									
								
							} 

				})


}
	</script>
	<!-- PNotify -->
    <script src="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.js"></script>
    <script src="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="http://93.188.164.97/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.js"></script>
	
</html>