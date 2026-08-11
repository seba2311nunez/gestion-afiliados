<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	  
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	<!-- Estilos propios -->
	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
	<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>

	<!-- Select clase -->
	<link href="http://45.132.242.129/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
	<script src="http://45.132.242.129/framework/bootstrap/select/select2.min.js"></script>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<div class="container-fluid">
		<div class="x_panel">
			<div class="tituloDiv">
				Carnets por Seccional
			</div>
			<div class="form-group">
				<label for="proceso_fecha">Fecha: </label>
				<input type="date" class="form-control" id="proceso_fecha" />
			</div>
			<!--
			<div class="form-group">
				<label for="proceso_periodo">Periodo: </label>
				<select class="form-control" id="proceso_periodo"></select>
			</div>
			-->
			<div class="form-group">
				<a class="btn btn-warning" id="proceso_vista_previa">Vista Previa</a>
				<a class="btn btn-success" id="proceso_excel">Excel</a>
			</div>
			<div id="divListado" style="padding: 30px;"></div>
		</div>
	</div>
</body>

<script>
	$(function(){

		TraerPeriodos();

		$('#proceso_vista_previa').on('click',function(e){

			$("#proceso_vista_previa").html('Procesando');
			$("#proceso_vista_previa").attr('disabled','disabled');		
			

			e.preventDefault();

			var fecha = $('#proceso_fecha').val();

			if(!fecha){
				alert('Coloque fecha');

				$("#proceso_vista_previa").html('Vista Previa');
				$('#proceso_vista_previa').removeAttr('disabled');					
				return false;
			}

			var datos = {
				"parametro": "proceso_vista_previa",
				"fecha": fecha
			};

			$.ajax({
				url: 'ajax.php',
				type: 'GET',
				dataType: 'json',
				data: datos,
			})
			.done(function(data) {
				
				$('#divListado').html("<table id='proceso_tabla' class='table table-stripped table-sm'><thead><tr><td>ID</td><td>ID titular</td><td>Seccional</td><td>Cuil Titular</td><td>Nº Ben</td><td>Nombre y Apellido</td><td>DNI</td><td>Fecha Nacimiento</td><td>Vto.</td><td>CUIT</td><td>Empresa</td><td>TBT</td></tr></thead><tbody></tbody></table>");

				for(var i=0; i<=data.length-1 ;i++){

					$('#proceso_tabla tbody').append("<tr>"
							+"<td>"
								+data[i]['id']
							+"</td>"
							+"<td>"
								+data[i]['id_titular']
							+"</td>"
							+"<td>"
								+data[i]['seccional']
							+"</td>"
							+"<td>"
								+data[i]['cuil_titular']
							+"</td>"
							+"<td>"
								+data[i]['nben']
							+"</td>"
							+"<td>"
								+data[i]['ayn']
							+"</td>"
							+"<td>"
								+data[i]['nd']
							+"</td>"
							+"<td>"
								+data[i]['fecha_nacimiento']
							+"</td>"
							+"<td>"
								+data[i]['vencimiento']
							+"</td>"
							+"<td>"
								+data[i]['cuit']
							+"</td>"
							+"<td>"
								+data[i]['empresa']
							+"</td>"
							+"<td>"
								+data[i]['tbt']
							+"</td>"
						+"</tr>");
				}
			})
			.fail(function(data) {
				console.log("error"+data);
			})
			.then(function(){
				$("#proceso_vista_previa").html('Vista Previa');
				$('#proceso_vista_previa').removeAttr('disabled');
			});
			
			
			
		});

		$('#proceso_excel').on('click',function(e){

			$('#proceso_excel').addClass('disabled');
			$("#proceso_excel").html('');					
			$("#proceso_excel").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

			e.preventDefault();

			var fecha = $('#proceso_fecha').val();

			if(!fecha){
				alert('Coloque fecha');

				$('#proceso_excel').removeClass('disabled');					
				$("#proceso_excel").html('Excel');
				return false;
			}

			var datos = {
				"parametro": "proceso_excel",
				"fecha": fecha
			};

			//window.open("carnets_x_seccional_xls.php?fecha="+fecha);
			alert('Descarga deshabilitada actualmente. Comuniquese con sistemas');
			//Esta descarga anda con file_get_contest. Temporalmente inactivo
			$('#proceso_excel').removeClass('disabled');
			$("#proceso_excel").html('Excel');
		});
	});


	function TraerPeriodos(){

			var datos = {"parametro" : "traer_periodos"};

			$.ajax({
				url: 'ajax.php',
				type: 'GET',
				dataType: 'json',
				data: datos,
			})
			.done(function(data) {
				console.log(data);
				for(var i=0; i<=data.length-1 ;i++){
					$('#proceso_periodo').append("<option value='"+data[i]['periodo']+"'>"+data[i]['periodo']+"</option>>");
				}
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
	}
</script>
</html>