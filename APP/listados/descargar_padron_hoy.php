<?php 
$date = new DateTime();

#mysql_query("CALL $base_padron.crea_afil_work(CURDATE())") or die(mysql_error()."ERROR Ejecutando el stored");

?>

<!DOCTYPE html>
<html>
<head>
	<!-- Jquery -->
	<script src="../agenda_archivos/jquery.min.js"></script>
	
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

	<title>Descargar Padron Hoy</title>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<div class="tituloDiv">
				Descargar padron de Hoy
			</div>
			<br>
			<div>
				Generar padron actual en excel, tardara unos minutos
			</div>
			<hr>
			<a id="btnDescargar" class="btn btn-success" target="_blank">Descargar</a>
			<hr>
			<div class="logs-container">
				<table id="logs" class="table">
					<thead>
						<tr>
							<th>Usuario</th>
							<th>Fecha y hora</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>

	</div>	
</body>

<script>
	$(function(){

		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'listar_logs'},
		})
		.done(function(data) {
			
			for(var i=0; i<=data.length-1 ;i++){
				$('#logs tbody').append(
					"<tr>"
						+"<td>"
							+data[i]['usuario']
						+"</td>"
						+"<td>"
							+data[i]['fechador']
						+"</td>"
					+"<tr>"
				);
			}
		});
		

		$("#btnDescargar").on('click',function(e){

			e.preventDefault();
			
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			var url = "ajax_padron.php?parametro=lst_padron_filtros" ;
			abrirEnPestana(url);
			setTimeout(function(){ 
				$("#btnDescargar").removeAttr('disabled'); 
				$('#btnDescargar').html('Descargar');	
			}, 30000);
		})

		
	})

	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}

</script>
</html>