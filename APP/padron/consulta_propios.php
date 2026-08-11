<?php 
require("../../Config/init.inc");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Consulta Propios</title>
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
</head>
<body style="background-color: #454d55;">
	<div>
		<div class="col-md-12">
			<div class="col-md-6">
				<div class="row">
					<div class="x_panel">
						<div class="tituloDiv">
							Totales de titulares Y familiares por seccional
						</div>
						<div class="row">
							<hr />
						    <table id="tabT1" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>Seccional</th>
						    			<th>Titulares</th>
						    			<th>Familiares</th>						    			
						    			<th>Total</th>				    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="x_panel">
						<div class="tituloDiv">
							Hijos y/o familiares a cargo entre 21 Y 25 años 
						</div>
						<div class="row">
							<hr />
							<a id="btnConsulta2xls" class="btn btn-success" style="margin: 20px;">Excel</a>
						    <table id="tabT2" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>#Afil titular</th>
						    			<th>#Afil familiar</th>
						    			<th>Cuil Titular</th>
						    			<th>Apellido y Nombre</th>
						    			<th>Cuil</th>						    			
						    			<th>Edad</th>			    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
					<div class="x_panel">
						<div class="tituloDiv">
							Hijos y/o familiares a cargo mayores de 25 años
						</div>
						<div class="row">
							<hr />
						    <table id="Tabt3" class="table" style="width: 90%;">
						    	<thead>
						    		<tr>
						    			<th>#Afil titular</th>
						    			<th>#Afil familiar</th>
						    			<th>Cuil Titular</th>
						    			<th>Apellido y Nombre</th>
						    			<th>Cuil</th>						    			
						    			<th>Edad</th>				    			
						    		</tr>
						    	</thead>
						    	<tbody>						    		
						    	</tbody>
						    </table>
						    <hr />
						</div>
					</div>
			</div>	
			
		</div>
		
	</div>
</body>
<script>
	
	$(function(){
		$.getJSON('ajax_propios.php', {parametro: 'consulta1'}).then(function(data){
			$('#TabT1 tbody').html("");
			for(var i=0; i<=data.length-1 ;i++){
					$('#TabT1 tbody').append("<tr>"
							+"<td>"
								+data[i]['seccional']
							+"</td>"
							+"<td>"
								+data[i]['titulares']
							+"</td>"
							+"<td>"
								+data[i]['familiares']
							+"</td>"
							+"<td>"
								+data[i]['total']
							+"</td>"
						+"</tr>");
			}
		});

		$('#TabT2 tbody').html("<h3>Cargando...</h3>");
		$.getJSON('ajax_propios.php', {parametro: 'consulta2'}).then(function(data){
			$('#TabT2 tbody').html("");
			for(var i=0; i<=data.length-1 ;i++){
				$('#TabT2 tbody').append("<tr>"
							+"<td>"
								+data[i]['nben_tit']
							+"</td>"
							+"<td>"
								+data[i]['nben_fam']
							+"</td>"
							+"<td>"
								+data[i]['cuil_titular']
							+"</td>"
							+"<td>"
								+data[i]['ayn']
							+"</td>"
							+"<td>"
								+data[i]['cuil']
							+"</td>"
							+"<td>"
								+data[i]['edad']
							+"</td>"
						+"</tr>");
			}
		});
		$.getJSON('ajax_propios.php', {parametro: 'consulta3'}).then(function(data){
			$('#TabT3 tbody').html("");
			for(var i=0; i<=data.length-1 ;i++){
				$('#TabT3 tbody').append("<tr>"
					+"<td>"
						+data[i]['cuil_titular']
					+"</td>"
					+"<td>"
						+data[i]['ayn']
					+"</td>"
					+"<td>"
						+data[i]['cuil']
					+"</td>"
					+"<td>"
						+data[i]['edad']
					+"</td>"
				+"</tr>");
			}
		});


		$("#btnConsulta2xls").on('click',function(){

			var url = "ajax_propios.php?parametro=consulta2&param_salida=xls";
			abrirEnPestana(url);

		})

	});

	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}


</script>
</html>