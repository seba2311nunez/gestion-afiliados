<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>

		<!-- Databatables -->
		<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
		
		<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
		<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
		<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

		<!-- PNotify -->
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.js"></script>
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.js"></script>
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.js"></script>
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

		<style>
			#dni_resultado{				
		
			    background-color: black;
			    color: gray;
			    padding: 7px;
			    border-radius: 8px;
			    margin: 5px;
			}
			#tabListado{
				font-size: 11px;
			}
			.btn-refresh{
				position: fixed;
				bottom:2%; 
				left:2%;
				z-index: 1;
			}
		</style>
	</head>
	<body>
		<!-- Cuerpo del formulario -->
		<div class="container-fluid">
			<button class="btn btn-info btn-refresh">
				<i class="fas fa-sync-alt fa-2x"></i>
			</button>
			<div class="col-md-12">
				<div class="x_panel">
					<div class="tituloDiv">
						Supervisión de Novedades
					</div>
					<div class="row" style="padding-left: 25px;">
						<label>Comparacion de padrones. Alta en sistema y Padron SSS</label>
						<table class="table" id="comparacion_padrones_t">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>

						<label>Bajas calculadas para RG por falta de DJ/AP por 3 meses.</label>
						<table class="table" id="table-2">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>

						<label>Bajas calculadas para MT/MS/SD por falta de Aporte por 3 meses.</label>
						<table class="table" id="table-3">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>

						<label>Altas calculadas para RG por ingreso de DJ/AP.</label>
						<table class="table" id="table-4">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>

						<label>Altas calculadas para MT/MS/SD por Ingreso de Aportes.</label>
						<table class="table" id="table-5">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						<label>Novedades Rechazadas para ingresar a la presentacion actual.</label>
						<table class="table" id="novedades_rechazadas">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Periodo</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="x_panel">
					<div class="tituloDiv">
						Actualizacion de Controladores
					</div>
					<div class="row col-md-6" style="padding-left: 25px;">
						<label>Padron Global. DDJJ/AP a la fecha</label>
						<table class="table" id="ctrlPadronCompleto">
							<thead>
								<tr>
									<th>ID</th>
									<th>Fecha</th>
									<th>Inicio</th>
									<th>Fin</th>
									<th>Usuario</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="row col-md-6" style="padding-left: 25px;">
						<label>Padron Altas a fecha actual.</label>
						<table class="table" id="padronAlta">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th colspan=4>Descripcion</th>
									<th>Usuario</th>
									<th>Fechador</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		$(function(){
			$(document).on('click','.btnSupervisarComparacion',function(e){
				window.open(`supervisar_compsss.php?id_lote=${$(this).data('id_lote')}`);
			});
			$(document).on('click','.btnResubirRechazos',function(e){
				window.open(`supervisar_rnov.php?id_lote=${$(this).data('id_lote')}`);
			});

			listar_comparacion_padrones();
			listar_ctrlPadronCompleto();
			listar_novedadesRechazadas();
		});

		function listar_comparacion_padrones(){

			$.ajax({
				url: "ajax.php",
				type: "GET",
				dataType: "json",
				data:{parametro: 'listar_comparacion_padrones'}
			}).then(function(data){
				for(var i=0; i<=data.length-1 ;i++){
					let {id,descripcion,usuario,fechador} = data[i];
					$("#comparacion_padrones_t tbody").append(`
						<tr>
							<td>${id}</td>
							<td>
								<div class='btn-group btn-group-default'>                
									<button style='margin-left: 20%; margin-right: auto;'  data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
										<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
									</button>
									<ul class='dropdown-menu'>
										<li>
											<a class='btnSupervisarComparacion' data-id_lote='${id}'>
												Supervisar lote
											</a>						                     	
										</li>         		 
									</ul>
								</div>
							</td>
							<td colspan=4>${descripcion}</td>
							<td>${usuario}</td>
							<td>${fechador}</td>
						</tr>
					`);
				}
			});
		}

		function listar_novedadesRechazadas(){

			$.ajax({
				url: "ajax.php",
				type: "GET",
				dataType: "json",
				data:{parametro: 'listar_novedadesRechazadas'}
			}).then(function(data){
				for(var i=0; i<=data.length-1 ;i++){
					let {id,fecha,usuario,fechador} = data[i];
					$("#novedades_rechazadas tbody").append(`
						<tr>
							<td>${id}</td>
							<td>
								<div class='btn-group btn-group-default'>                
									<button style='margin-left: 20%; margin-right: auto;'  data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
										<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
									</button>
									<ul class='dropdown-menu'>
										<li>
											<a class='btnResubirRechazos' data-id_lote='${id}'>
												Supervisar Lote
											</a>						                     	
										</li>         		 
									</ul>
								</div>
							</td>
							<td colspan=4>${fecha}</td>
							<td>${usuario}</td>
							<td>${fechador}</td>
						</tr>
					`);
				}
			});
		}

		function listar_ctrlPadronCompleto(){

			$.ajax({
				url: "ajax.php",
				type: "GET",
				dataType: "json",
				data:{parametro: 'listar_ctrlPadronCompleto'}
			}).then(function(data){
				for(var i=0; i<=data.length-1 ;i++){
					let {id,fecha_parametro,fechador,fechador_fin,usuario} = data[i];
					$("#ctrlPadronCompleto tbody").append(`
						<tr>
							<td>${id}</td>
							<td>${fecha_parametro}</td>
							<td>${fechador}</td>
							<td>${fechador_fin}</td>
							<td>${usuario}</td>
						</tr>
					`);
				}
			});
		}
	</script>
</html>