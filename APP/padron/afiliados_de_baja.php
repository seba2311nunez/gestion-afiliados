<?php require("../../Config/init.inc"); ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Afiliados de Baja</title>
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
<body>
	<div class="container-fluid">
		<div class="x_panel">
			<div class="tituloDiv">
				Afiliados de baja.
			</div>
			<div class="row">
				<div style="width: 600px;">	
					<table>
						<tr>
							<td style="width:250px;">
								<select id="desreguladoras" class="custom-select">
									<option value="0">Seleccione</option>
								</select>
							</td>
							<td>
								<button type="button" class="btn btn-success" id="seleccionar_desreguladora">Confirmar</button>
							</td>
								
							<td>
								<button type="button" class="btn btn-info" id="generar_excel">Excel</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="row" id="table-container" >
				<div style="padding: 25px;">
					<table class="table table-striped" id="lista">
						<thead>
							<th>#</th>
							<th>Opciones</th>
							<th>CUIL</th>
							<th>DNI</th>
							<th>Apellido</th>
							<th>Nombre</th>
							<th>Fecha Baja</th>
							<th>Ultima DDJJ</th>
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

		$.getJSON('ajax_afiliados.php',{parametro: 'traer_desreguladoras'}).then(function(data){
			
				$.each(data, function (key, item) {
                	$("#desreguladoras").append("<option value="+item.id+">"+item.convenio+"</option>");
	            });
			
		});

		$(document).on('click','#seleccionar_desreguladora',function(e){
			e.preventDefault();
			var id_desreguladora = $('#desreguladoras').val();
			$('#lista tbody').html("<tr><td colspan=8><i class='fas fa-sync-alt fa-spin'></i></td></tr>")
			if(id_desreguladora == 0){
				alert('Seleccione una desreguladora');
			}else{
				$.getJSON('ajax_afiliados.php',{parametro:'traer_afiliados',id_desreguladora:id_desreguladora}).then(function(data){
					console.table(data);
					$('#lista tbody').html("");
					for(var i=0; i<=data.length-1 ;i++){
						$('#lista tbody').append("<tr>"
							+"<td>"
								+i
							+"</td>"
							+"<td>"
								
							+"</td>"
							+"<td>"
								+data[i]['cuil_titular']
							+"</td>"
							+"<td>"
								+data[i]['nd']
							+"</td>"
							+"<td>"
								+data[i]['apellido']
							+"</td>"
							+"<td>"
								+data[i]['nombre']
							+"</td>"
							+"<td>"
								+data[i]['fecha_estado']
							+"</td>"
							+"<td>"
								+data[i]['djul']
							+"</td>"
						+"</tr>");
					}
				}).then(function(){
				});
			}
		});

		$('#generar_excel').on('click',function(e){
			e.preventDefault();
			var id_desreguladora = $('#desreguladoras').val();
			$('#lista tbody').html("<tr><td colspan=8><i class='fas fa-sync-alt fa-spin'></i></td></tr>")
			if(id_desreguladora == 0){
				alert('Seleccione una desreguladora');
			}else{
				window.open('ajax_afiliados.php?parametro=generar_excel&id_desreguladora='+id_desreguladora);
			}
		});
	})
</script>
</html>