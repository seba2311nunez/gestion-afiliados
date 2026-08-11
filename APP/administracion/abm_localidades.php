<?
include('../../Config/Conectar.inc');
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

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
			<div class="col-md-12">
				<div class="x_panel">
					<div class="tituloDiv">
						Administrar Localidades
					</div>
					<div class="row" style="padding-left: 25px;">
						<table class="table" id="t_localidades">
							<thead>
								<tr>
									<th>ID</th>
									<th></th>
									<th>Provincia</th>
									<th>CP</th>
									<th>Localidad</th>
									<th>Provincia Revisada</th>
									<th>CP Revisada</th>
									<th>Localidad Revisada</th>
									<th>Verif Basica</th>
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

				var table = $('#t_localidades').DataTable({
					"processing": true,
					"serverSide": true,
					"ajax": {
						"url": "ajax_localidades.php",
						"data": function(d){
							d.parametro = 'traer_localidades';
						},
						"type": "POST"
					},
					"columns":[
						{
							"data": "id"
						},
						{
							"data":null,
							"render": function(data, type, row) {
								
								return `
								<div class='btn-group btn-group-default'>
									<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
									<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
									</button>
									<ul class='dropdown-menu'>
										<li>
											<a class='btn-redirigir' data-direccion='editar_localidad.php' data-id='${row.id}'>
												<i class='fas fa-print'></i>&nbsp; Editar Localidad
											</a>
										</li>
									</ul>
								</div>`;
							},
						},
						{
							"data": "provincia"
						},
						{
							"data": "cp"
						},
						{
							"data": "nombreLoca"
						},
						{
							"data": "provincia_revisado",
							"createdCell": function(td, cellData, rowData, row, col) {
								$(td).css("background-color", "#b3e0ff"); // Fondo celeste
							}
						},
						{
							"data": "cp_revisado",
							"createdCell": function(td, cellData, rowData, row, col) {
								$(td).css("background-color", "#b3e0ff"); // Fondo celeste
							}
						},
						{
							"data": "nombreLoca_revisado",
							"createdCell": function(td, cellData, rowData, row, col) {
								$(td).css("background-color", "#b3e0ff"); // Fondo celeste
							}
						},
						{
							"data": null,
							"render": function(data, type, row) {
								if (row.verif_basica === "1") {
									return `<i class="fas fa-exclamation-triangle" style="color: red;"></i>`; // Icono rojo si es 1
								} else {
									return ''; // Si es 0, no mostrar nada
								}
							}
						}
					],
					"language": {
						"decimal": ",",
						"thousands": ".",
						"processing": "Procesando...",
						"lengthMenu": "Mostrar _MENU_ registros por página",
						"zeroRecords": "No se encontraron resultados",
						"info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"infoEmpty": "Mostrando 0 a 0 de 0 registros",
						"infoFiltered": "(filtrado de _MAX_ registros totales)",
						"search": "Buscar:",
						"paginate": {
							"first": "Primero",
							"last": "Último",
							"next": "Siguiente",
							"previous": "Anterior"
						},
						"loadingRecords": "Cargando...",
						"emptyTable": "No hay datos disponibles en la tabla"
					}
				});

			$(document).on('click', '.btn-redirigir', function(e) {
					e.preventDefault();

					var direccion = $(this).data('direccion');
					var url = direccion;
					var params = [];

					// Recorrer todos los atributos data-* del botón
					$.each(this.attributes, function() {
					if (this.name.startsWith('data-') && this.name !== 'data-direccion') {
					var key = this.name.replace('data-', ''); // Obtener la clave sin 'data-'
					var value = $(e.target).attr(this.name); // Obtener el valor del atributo correctamente
					if (value !== null) {
					params.push(`${key}=${encodeURIComponent(value)}`);
					}
					}
					});

					// Si hay parámetros, concatenarlos correctamente
					if (params.length > 0) {
					url += '?' + params.join('&');
					}

					var a = document.createElement('a');
					a.href = url;
					a.target = '_blank';
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
			});


		});
	</script>
</html>