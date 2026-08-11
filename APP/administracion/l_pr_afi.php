<?php 
include ("../../Config/Conectar.inc");
$usuario = $_SESSION['usuario'];
$id_institucion = $_SESSION["id_institucion"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Listar Prestaciones</title>
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

	<!-- BOOTSTRAP, Popper.js, and jQuery -->
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<!-- Databatables -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

	<!-- Select clase -->
	<link href="http://34.123.90.171/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
	<script src="http://34.123.90.171/framework/bootstrap/select/select2.min.js"></script>

	<style>
		body{
			background-color: #454d55;
			font-size:11px;
		}
		/* width */
		::-webkit-scrollbar {
		width: 10px;
		}

		/* Track */
		::-webkit-scrollbar-track {
		background: #f1f1f1; 
		}

		/* Handle */
		::-webkit-scrollbar-thumb {
		background: #888; 
		}

		/* Handle on hover */
		::-webkit-scrollbar-thumb:hover {
		background: #555; 
		}
		.myLi{
			margin-left:15px;
			margin-right:15px;
		}
		.table-container{
			height:480px;
			overflow: scroll;
			overflow-x: hidden;
			width: 100%;
		}
		.mySubmit{
			padding-left: 35px;
			padding-top: 10px;
		}
		.mySubmit a{
			width:150px;
		}
		th{
			position: sticky;
  			top: 0;
  			background: #212529;
  			box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
		}
		.prestadores{
			width: 20%;
		}
		.select2{

			width: 97%;
		}

	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container-fluid">
	   	<div class="navbar-header" style="margin-right: 25px;">
	      <a class="navbar-brand" href="#">LISTADO PRESTACIONES POR AFILIADO</a>
	    </div>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
		    	<span class="navbar-toggler-icon"></span>
	  	</button>
  		<div class="collapse navbar-collapse" id="navbarNavDropdown">
			<ul class="navbar-nav mr-auto">
	      		<li class="myLi navbar-brand">
		        	<span class="nav-link">CUIL:</span>
		        	<input type="text" name="cuil" id="cuil" value="" class="form-control input-sm" required />
	      		</li>	
		    	<li class="myLi navbar-brand">
			        <span class="nav-link">Fecha Desde:</span>
		    	    <input type="date" name="fdesde" id="fdesde" value="<?=date('Y-m-01'); ?>" class="form-control input-sm" min="1995-01-01" max="2049-12-31" required />
	      		</li>
	      		<li class="myLi navbar-brand">
		        	<span class="nav-link">Fecha Hasta:</span>
		        	<input type="date" name="fhasta" id="fhasta" value="<?=date('Y-m-d'); ?>" class="form-control input-sm" max="2050-12-31" required />
	      		</li>
		    <ul class="navbar-nav text-left">
		    	<div class="mySubmit">
	    			<table>
	    			</table>
    			</div>  
      	</ul>
			</div>
		</div>
	</nav>
	<center>
		<tr>
			<a class="btn btn-success btn-xs text-light" id='listar' name="listar">Listar</a>
			<a class="btn btn-success btn-xs text-light" id='excel'>
				Excel
			</a>
		</tr>	
	</center>
	<hr>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<span id="ayn" class="text-light"></span>
				<br>
				<span id="capita" class="text-light"></span>
				</span>
				<div class="table-container">
					<table id="listado" class="table table-hover table-dark table-sm">
						<thead>
							<tr>
				        <th>Fecha Prestacion</th>
				        <th>Nº Factura</th>
				        <th class="">CUIT</th>
				        <th class="prestadores">Prestador</th>
				        <th>Prestacion</th>
				        <th>Importe</th>
				        <th>Debito</th>
				      	<th>A Pagar</th>
				      	<th>Usuario</th>
				      	<th>Fecha Carga</th>
			        </tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script>
	$(function(){
		let INST_NAME = "<?echo INST_NAME;?>";
		
		$('#listar').on('click',function(e){
			
			$('#listar').attr('disabled','disabled');
			$('#listar').html('');					
			$('#listar').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

			e.preventDefault();

			var datos = PrepararDatos();

			$('#listado tbody').html("");
			$.getJSON('ajax_prestaciones.php',
			datos,						       				
			function(data){

				$('#ayn').html("");
				$('#capita').html("");
					
				if(data['error']){
								
								$('#listado tbody').html("");
								console.log(data['error'])								
								$('#listado tbody').html("<tr><th>Hubo un error. Comuniquese con Sistemas</th></tr>");
								$('#listar').html('Listar');
								$('#listar').removeAttr('disabled');
								return false;
				}
				if(data['sin_registros']){
								
								$('#listado tbody').html("");
								console.log(data['sin_registros'])								
								$('#listado tbody').html("<tr><th>No se encontraron registros</th></tr>");
								$('#listar').html('Listar');
								$('#listar').removeAttr('disabled');
				}
				else{
					
					var imp = liquidado = debitado = pagado = a_pagar = 0;
					let {consumos,datos_personales} = data;
					console.table(consumos);

					//Consumos
					for(var i=0; i<=consumos.length-1 ;i++){
						let { ...rowData } = consumos[i];

						$('#listado tbody').append("<tr>"
												+"<td>"+formatDate(rowData.fecha_prestacion)+"</td>"
												+"<td>"+rowData.numero_factura+"</td>"												
												+"<td>"+rowData.cuit+"</td>"
												+"<td>"+rowData.nombre_prestador+"</td>"
												+"<td>"+rowData.descripcion+"</td>"	
												+"<td class='text-right'>"+formatNumber(convertToDecimal(rowData.importe))+"</td>"
												+"<td class='text-right'>"+formatNumber(convertToDecimal(rowData.debito))+"</td>"
												+"<td class='text-right'>"+formatNumber(convertToDecimal(rowData.a_pagar))+"</td>"
												+"<td>"+rowData.usuario_carga+"</td>"	
												+"<td>"+formatTimestamp(rowData.fecha_carga)+"</td>"	
											+"</tr>");
					}
					$('#listar').html('Listar');
					$('#listar').removeAttr('disabled');	

					//Datos Personales
					let {convenio_real,ayn} = datos_personales;

					$('#ayn').html(ayn);
					$('#capita').html(convenio_real);

				}
			});

		});

		$('#excel').on('click',function(e){
			e.preventDefault();
			var datos = PrepararDatos();
			
			let parametro= 'descargar_listado_prestaciones';
			window.open(`http://138.99.7.172/lee_xls/${INST_NAME}/ajax_facturacion.php?parametro=${parametro}&hasta=${datos.hasta}&desde=${datos.desde}&cuil=${datos.cuil}`);
		});
	});

	function PrepararDatos(){
		//var datos = new Array(3).fill(0);

		var desde = $('#fdesde').val();
		var hasta = $('#fhasta').val();
		var cuil = $('#cuil').val();
		const date1 = new Date($('#fdesde').val());
		const date2 = new Date($('#fhasta').val());
		if(date2 < date1){
			alert('Ha colocado las fechas de manera erronea');
			return false;
		}
		if(cuil.length !== 11){
			alert('El CUIL debe estar compuesto por 11 caracteres');
			return false;
		}
		let param = 'listar_prestaciones';

		let datos = {
				'desde': desde,
				'hasta': hasta,
				'cuil': cuil,
				'parametro': param 
		};
		return datos;
	}
	function formatDate(dateStr) {
	  const [date, time] = dateStr.split(' ');
	  const [year, month, day] = date.split('-');
	  return `${day}/${month}/${year}`;
	}
	function formatTimestamp(timestampStr) {
	  const [date, time] = timestampStr.split(' ');
	  const [year, month, day] = date.split('-');
	  return `${day}/${month}/${year} ${time}`;
	}
	function convertToDecimal(text) {
	  return Number(parseFloat(text).toFixed(2));
	}
	function formatNumber(number) {
	  return number.toLocaleString('en-US', { style: 'decimal', decimalSeparator: ',', thousandSeparator: '.' });
	}
</script>