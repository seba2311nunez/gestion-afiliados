<?php 
$date = new DateTime();
include("../../Config/Conectar.inc");
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

	<title>Descargar Movimientos de Padron</title>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<input type="hidden" id="formato" value="1">
			<div class="tituloDiv">
				Descargar movimientos de padron
			</div>
			<hr>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-4" >
					<label class="input-group-text">Eventos</label>
					<select id="eventos" class="selectores-multiples">
						<option value="">Todas</option>
					</select>
				</div>
				
				<div class="col-md-4" >
					<label class="input-group-text">Red de Prestacion</label>
					<select id="gerenciadoras" class="selectores-multiples">
						<option value="">Todas</option>
					</select>
				</div>
				<div class="col-md-2"></div>
			</div>
			<div class="row">
				<div class="col-md-2"></div>
				<div class="form-group col-md-4">
					<label class="input-group-text">F. Creacion</label>
					<div class="input-group">
						<input type="date" id="fechador_desde" name="fechador_desde" class="form-control input-sm inp_personales form-control-sm">
						<div class="input-group-prepend">
							<div class="input-group-text input-group-text">/</div>
						</div>
						<input type="date" id="fechador_hasta" name="fechador_hasta" class="form-control input-sm inp_personales form-control-sm">
					</div>
				</div>
				<div class="col-md-2"></div>
			</div>
			<hr>
		  <div class="row">
		    <div class="col-md-6">
		      <div class="btn-group" role="group">
		        <a id="btnDescargar" class="btn btn-success text-light" target="_blank">Descargar</a>

		      </div>
		    </div>
		  </div>			
		</div>
		<div class="x_panel">
			<div class="logs-container">
				<div class="tituloDiv">Historial de descargas</div>
				<hr>
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
	const INST_FORMATO_PADRON_EXCEL = "<? echo INST_FORMATO_PADRON_EXCEL;?>";
	$(function(){

		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'listar_logs', tipo: 'Descarga preventa'},
		})
		.done(function(data) {
			
			for(var i=0; i<=data.length-1 ;i++){
				$('#logs tbody').append(
					"<tr>"
						+"<td>"
							+data[i]['usuario']
						+"</td>"
						+"<td>"
							+data[i]['fecha']
						+"</td>"
					+"<tr>"
				);
			}
		});		

		//Eventos
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_eventos'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,descripcion} = data[i];
				$('#eventos').append(`<option value='${id}'>${id} - ${descripcion}</option>`);
			}
			$('#eventos').attr('multiple','true');
			$('#eventos').addClass("select2");
			$('#eventos').select2();
		});
		
		//Gerenciadoras
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_gerenciadora'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,convenio,convenio_real} = data[i];
				$('#gerenciadoras').append(`<option value='${id}'>${id} - ${convenio} - ${convenio_real}</option>`);
			}
			$('#gerenciadoras').attr('multiple','true');
			$('#gerenciadoras').addClass("select2");
			$('#gerenciadoras').select2();
		});
		$("#btnDescargar").on('click',function(e){
			
			e.preventDefault();

			$("#btnDescargar").addClass('disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			url = GetURLAJAX('lst_reporte_eventos'); 
			//console.log(url);return false;
			abrirEnPestana(url);
			setTimeout(function(){ 
				
			},2000);
			$("#btnDescargar").removeClass('disabled'); 
			$('#btnDescargar').html('Descargar');	
		});
		$('.selectores-multiples').on('change', function() {
	        var firstOption = $('option[value=""]', this);
	        var selectedOptions = $('option:selected', this);

	        if (selectedOptions.length > 1) {
	            // Deselect the first option if it is selected along with others
	            firstOption.prop('selected', false);
	        }
	    });
	})
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
	function GetURLAJAX(parametro){
		var url = "ajax_padron.php";
		if(!parametro){
			return false;
		}
		url = url.concat(`?parametro=${parametro}`);

		if($('#eventos').val()){
			url = url.concat(`&id_evento=${$('#eventos').val()}`);
		}	
		if($('#gerenciadoras').val()){
			url = url.concat(`&id_capita=${$('#gerenciadoras').val()}`);
		}	

		if($('#fechador_desde').val() && $('#fechador_hasta').val()){
			url = url.concat(`&fechador_desde=${$('#fechador_desde').val()}&fechador_hasta=${$('#fechador_hasta').val()}`);
		}

		return url;
	}
	function TraerFiltros(){
		$('#FiltrosGuardados').html("");
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_filtros'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,nombre,filtros} = data[i];
				$('#FiltrosGuardados').append(`<option value='${id}' data-filtros=${filtros}>${nombre}</option>`);
			}
		});
	}
</script>
</html>