<?php 
$date = new DateTime();


?>

<!DOCTYPE html>
<html>
<head>
	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	<!-- Estilos propios -->
	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
	<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>

	<!-- Select clase -->
	<link href="http://34.123.90.171/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
	<script src="http://34.123.90.171/framework/bootstrap/select/select2.min.js"></script>

	<title>Descargar Traspasos entrantes y salientes</title>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<div class="tituloDiv">
				Descargar Traspasos entrantes y salientes.
			</div>
			<hr>
			
			<div class="row">
				<div class="col-md-6">
					<b>Fecha vigencia Desde</b>
					<input type="date" id="fecha_desde" class="form-control" />
				</div>	
				<div class="col-md-6">
					<b>Fecha vigencia Hasta</b>
					<input type="date" id="fecha_hasta" class="form-control" />
				</div>	
			</div>
			<hr>
			<div class="row">
				<div class="col-md-4">
					<b>Tipo Movimiento</b>
					<select id="tipo_movimiento" class="selectores-multiples">
						
						<option value="">Todos las movimientos</option>
						
						<option value="baja">Todas las bajas</option>
						<option value="baja_rg">Bajas de Regimen general</option>
						<option value="baja_mt">Bajas de Monotributo</option>

						<option value="alta">Todas las altas</option>
						<option value="alta_rg">Altas de Regimen general</option>
						<option value="alta_mt">Altas de Monotributo</option>
						
					</select>
				</div>
				<div class="col-md-4">
					<b>Redes</b>
					<select id="gerenciadoras" class="selectores-multiples">
						<option value="">Seleccione...</option>
					</select>	
				</div>	
				<div class="col-md-4">
					<b>Obras Sociales</b>
					<select id="obras_sociales" class="selectores-multiples">
						<option value="">Seleccione...</option>
					</select>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<hr>
					<a id="btnDescargar" class="btn btn-success" target="_blank">
						Descargar
					</a>		
				</div>
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
			data: {parametro: 'traer_gerenciadora'},
		})
		.done(function(data){			
		    const vistos = new Set(); // acá guardamos los convenio_real ya agregados

		    data.forEach(function(item) {
		        const { id, convenio_real } = item;

		        // si todavía no lo agregamos, lo agregamos ahora
		        if (!vistos.has(convenio_real)) {
		            vistos.add(convenio_real);

		            // si querés que el value sea el texto del convenio_real:
		            $('#gerenciadoras').append(
		                `<option value="${convenio_real}">${convenio_real}</option>`
		            );

		            // si preferís que el value sea el id, usarías:
		            // $('#gerenciadoras').append(
		            //    `<option value="${id}">${convenio_real}</option>`
		            // );
		        }
		    });

		    $('#gerenciadoras')
		        .attr('multiple', 'true')
		        .addClass('select2')
		        .select2();
		});	
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_os'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {codigo,procedencia} = data[i];
				$('#obras_sociales').append(`<option value='${codigo}'>${codigo} - ${procedencia}</option>`);
			}
		});		

		$("#btnDescargar").on('click',function(e){

			e.preventDefault();
			
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			DescargarExcel();			
					
		})

		$('.selectores-multiples').on('change', function() {
	        var firstOption = $('option[value=""]', this);
	        var selectedOptions = $('option:selected', this);

	        if (selectedOptions.length > 1) {
	            // Deselect the first option if it is selected along with others
	            firstOption.prop('selected', false);
	        }
	    });

	})
	function DescargarExcel(){
		let parametro = "lst_traspasos_filtros";
		let gerenciadora = $('#gerenciadoras option:selected').val();
		let obra_social = $('#obras_sociales option:selected').val();
		let tipo_movimiento = $('#tipo_movimiento option:selected').val();
		let fecha_desde = $('#fecha_desde').val();
		let fecha_hasta = $('#fecha_hasta').val();

		

		let url = `ajax_padron.php?parametro=${parametro}&tipo_movimiento=${tipo_movimiento}&obra_social=${obra_social}&fecha_desde=${fecha_desde}&fecha_hasta=${fecha_hasta}`;

		if($('#gerenciadoras').val()){
			url = url.concat(`&gerenciadora=${$('#gerenciadoras').val()}`);
	    
	    var opcionesSeleccionadas = $('#gerenciadoras').find('option:selected');
	    var textoOpciones = opcionesSeleccionadas.map(function() {
	      return $(this).text();
	    }).get().join(', ');
	    url = url.concat(`&gerenciadora_nombre=${textoOpciones}`);
		}

		console.log(url); //return false;

		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();

		$("#btnDescargar").removeAttr('disabled'); 
		$('#btnDescargar').html('Descargar');	
	}

	function convertirCadenaANumeros(cadena) {
	  // Dividir la cadena en un array de subcadenas
	  var subcadenas = cadena.split(',');
	  
	  // Convertir las subcadenas en números
	  var numeros = subcadenas.map(function(item) {
	    return parseInt(item, 10);
	  });

	  return numeros;
	}
	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}

</script>
</html>