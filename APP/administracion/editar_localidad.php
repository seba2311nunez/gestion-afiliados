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
		<div class="container">
			<div class="col-md-12">
				<div class="x_panel">
					<div class="tituloDiv">
						Administrar Localidades
					</div>
					<hr>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-4" >
							<label class="input-group-text">Provincia</label>
							<select id="provincia">
								<option value="0">Sin asignar</option>
							</select>
						</div>
						<div class="col-md-4" >
							<label class="input-group-text">Provincia</label>
							<select id="provincia_revisado">
								<option value="0">Sin asignar</option>
							</select>
						</div>
						<div class="col-md-2"></div>
					</div>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-4">
							<label class="input-group-text">CP</label>
							<input type="number" id="cp" class="form-control form-control-sm">
						</div>
						<div class="col-md-4">
							<label class="input-group-text">CP Revisado</label>
							<input type="number" id="cp_revisado" class="form-control form-control-sm">
						</div>
						<div class="col-md-2"></div>
					</div>
					<div class="row">
						<div class="col-md-2"></div>
						<div class="col-md-4">
							<label class="input-group-text">Nombre Localidad</label>
							<input type="text" id="nombreLoca" class="form-control form-control-sm">
						</div>
						<div class="col-md-4">
							<label class="input-group-text">Nombre Localidad Revisado</label>
							<input type="text" id="nombreLoca_revisado" class="form-control form-control-sm">
						</div>
						<div class="col-md-2"></div>
					</div>
					<hr>
				  <div class="row">
				    <div class="col-md-6">
				      <div class="btn-group" role="group">
				        <button id="btnGuardar" class="btn btn-success text-light">Guardar</button>

				      </div>
				    </div>
				  </div>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		const id_localidad = "<?echo $id;?>"; 
		$(function(){
			TraerProvincias();
			/*
			$.ajax({
				'url': 'ajax_localidades.php',
				'type': 'POST',
				'dataType': 'json',
				'data': {'parametro': 'traer_datos_localidades', id_localidad: id_localidad}
			}).then(function(data){
				console.log(data);
				let {provincia,cp,nombreLoca,id_provincia_revisado,cp_revisado,nombreLoca_revisado} = data;

				$('#provincia').val(provincia).change();

			});
			*/
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

			$(document).on('click','#btnGuardar', function(e) {
				let datos = {
					parametro: "guardar_edicion_localidad",
					id_localidad: id_localidad,
					provincia: $('#provincia').val(),
					cp: $('#cp').val(),
					nombreLoca: $('#nombreLoca').val(),

					provincia_revisado: $('#provincia_revisado').val(),
					cp_revisado: $('#cp_revisado').val(),
					nombreLoca_revisado: $('#nombreLoca_revisado').val()
				};
				$.ajax({
					url: 'ajax_localidades.php',
					type: 'GET',
					dataType: 'text',
					data: datos,
				}).then(function(data){
					if(data == "ok"){
						alert('Cambios guardados.');
					}else{
						console.log(data);
						alert('Error');
					}
				})
			});
		});

	function TraerProvincias() {
		$.ajax({
			url: 'ajax_localidades.php',
			type: 'GET',
			dataType: 'json',
			data: { parametro: 'traer_provincias' },
		})
		.done(function(data) {
			for (var i = 0; i <= data.length - 1; i++) {
				let { cod, nom } = data[i];
				$('#provincia').append(`<option value='${cod}'>${nom}</option>`);
				$('#provincia_revisado').append(`<option value='${cod}'>${nom}</option>`);
			}

        	// Una vez que las provincias estén cargadas, entonces asignar el valor.
        	// Esto garantiza que el selector ya tenga las opciones.
			$.ajax({
				'url': 'ajax_localidades.php',
				'type': 'POST',
				'dataType': 'json',
				'data': { 'parametro': 'traer_datos_localidades', id_localidad: id_localidad }
			}).then(function(data) {
				console.log(data);
				let { provincia, cp, nombreLoca, id_provincia_revisado, cp_revisado, nombreLoca_revisado } = data;

            	// Ahora asignamos el valor al campo de provincia
				$('#provincia').val(provincia).change();
				$('#cp').val(cp).change();
				$('#nombreLoca').val(nombreLoca).change();

            	// Ahora asignamos el valor al campo de provincia revisados
				$('#provincia_revisado').val(id_provincia_revisado).change();
				$('#cp_revisado').val(cp_revisado).change();
				$('#nombreLoca_revisado').val(nombreLoca_revisado).change();

			});
		});
	}
	</script>
</html>