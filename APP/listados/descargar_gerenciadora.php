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

	<title>Descargar P. Convenio</title>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<input type="hidden" id="formato" value="1">
			<div class="tituloDiv">
				Descargar Padron con Filtros.
			</div>
			<hr>
			<div class="row">
				<div class="col-md-4" >
					<label class="input-group-text">Red de Prestacion</label>
					<select id="gerenciadoras" class="selectores-multiples">
						<option value="">Todas</option>
					</select>
				</div>
				
				<div class="col-md-4">
					<label class="input-group-text">Tipo beneficiario</label>
					<select id="tipo_beneficiario" class="selectores-multiples">
						<option value="">Todos</option>
					</select>
				</div>
				<!-- <div class="col-md-4">
					<label class="input-group-text">Convenio</label>
					<select id="convenio_medico">
						<option value="">Todos</option>
					</select>
				</div> -->
			</div>
			<div class="row">
				<div class="col-md-4">
					<label class="input-group-text">Provincia</label>
					<select id="provincias" class="selectores-multiples">
						<option value="">Todas</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="input-group-text">Parentesco</label>
					<select id="parentesco" class="selectores-multiples">
						<option value="" data-deselected="false">Todos</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="input-group-text">Filial</label>
					<select id="filial" class="selectores-multiples">
						<option value="">Todas</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<label class="input-group-text">Sexo</label>
					<select id="sexo">
						<option value="">Todos</option>
						<option value="M">M</option>
						<option value="F">F</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="input-group-text">Incapacidad</label>
					<select id="incapacidad">
						<option value="">Todos</option>
						<option value="00">No</option>
						<option value="01">Si</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label class="input-group-text">Rango Etareo</label>
					<div class="input-group">
						<input type="number" id="edad_desde" name="edad_desde" placeholder="desde Ej 0" class="form-control input-sm inp_personales form-control-sm">
						<div class="input-group-prepend">
							<div class="input-group-text input-group-text">/</div>
						</div>
						<input type="number" id="edad_hasta" name="edad_hasta" placeholder="hasta Ej 25" class="form-control input-sm inp_personales form-control-sm">
					</div>
				</div>
			</div>
			<!-- 
			<div class="row">
				<div class="col-md-12">
					<br>
					<label class="input-group-text">Empresa</label>
					<select id="empresa">
						<option value="">Todas</option>
					</select>
				</div>
			</div> -->
			<div class="row">
				<div class="col-md-4" style="display: none;">
					<div class="radio">
					  	<label style='display: none;'><input type="radio" name="excentricidades" class="exc" value="formato_1">Formato 1</label>
					  	<label><input type="radio" name="excentricidades" class="exc" value="incluir_bajas" >Incluir Bajas</label>
					</div>
					
				</div>
			</div>
			<hr>
		  <div class="row">
		    <div class="col-md-6">
		      <div class="btn-group" role="group">
		        <a id="btnDescargar" class="btn btn-success text-light" target="_blank">Descargar</a>
		        <a id="btnAbrirEstadisticas" class="btn btn-success text-light" target="_blank">Estadisticas</a>
		        <a id="btnMostrarPantalla" class="btn btn-success text-light" target="_blank">Mostrar Pantalla</a>
				<?php
					if(INST_RNOS=="110404"){

						?>
						<a id="btnModeloVisitar" class="btn btn-success text-light" target="_blank">
							Modelo VISITAR 
						</a>
						<?php
					}
				?>
		        <button id="GuardarFiltros" class="btn btn-primary text-light">Guardar Filtros</button>
		      </div>
		    </div>
		    <div class="col-md-4">
		      <div class="form-group">
		        <select id="FiltrosGuardados" class="form-control"></select>
		      </div>
		    </div>
		    <div class="col-md-2">
		      <button id="CargarFiltro" class="btn btn-info text-light">Cargar Filtro</button>
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
							<th>ID</th>
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
			data: {parametro: 'listar_logs', tipo: 'Consulta Padron'},
		})
		.done(function(data) {
			
			for(var i=0; i<=data.length-1 ;i++){
				$('#logs tbody').append(
					"<tr>"
						+"<td>"
							+data[i]['id']
						+"</td>"
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
		//Gerenciadoras
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_gerenciadora'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,convenio_real,convenio} = data[i];
				$('#gerenciadoras').append(`<option value='${id}'>${id} - ${convenio_real} - ${convenio}</option>`);
			}
			$('#gerenciadoras').attr('multiple','true');
			$('#gerenciadoras').addClass("select2");
			$('#gerenciadoras').select2();
		});
		//Tipo afiliado
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_tipo_beneficiario'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,beneficiario} = data[i];
				$('#tipo_beneficiario').append(`<option value='${id}'>${beneficiario}</option>`);
			}
			$('#tipo_beneficiario').attr('multiple','true');
			$('#tipo_beneficiario').addClass("select2");
			$('#tipo_beneficiario').select2();
		});
		//Provincias
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_provincias'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {cod,nom} = data[i];
				$('#provincias').append(`<option value='${cod}'>${nom}</option>`);
			}
			$('#provincias').attr('multiple','true');
			$('#provincias').addClass("select2");
			$('#provincias').select2();

		});
		//Parentescos
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_parentescos'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,parentesco} = data[i];
				$('#parentesco').append(`<option value='${id}'>${parentesco}</option>`);
			}
			$('#parentesco').attr('multiple','true');
			$('#parentesco').addClass("select2");
			$('#parentesco').select2();

		});	
		
		//Filial
		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_filiales'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {id,nombre} = data[i];
				$('#filial').append(`<option value='${id}'>${nombre}</option>`);
			}
			$('#filial').attr('multiple','true');
			$('#filial').addClass("select2");
			$('#filial').select2();
		});
		
		TraerFiltros();
		$("#btnDescargar").on('click',function(e){
			
			e.preventDefault();

			$("#btnDescargar").addClass('disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			url = GetURLAJAX('lst_padron_filtros_csv'); 
			//console.log(url);return false;
			abrirEnPestana(url);
			setTimeout(function(){ 
				
			},2000);
			$("#btnDescargar").removeClass('disabled'); 
			$('#btnDescargar').html('Descargar');	
		});
		$("#btnAbrirEstadisticas").on('click',function(e){

			$("#btnAbrirEstadisticas").addClass('disabled');
			$("#btnAbrirEstadisticas").html('');					
			$("#btnAbrirEstadisticas").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			url = GetURLAJAX('guardar_csv'); //console.log(url); return false;
			$.ajax({url: url, dataType: 'json'}).done(function(response){
				if(!response || response.ok !== true){
					alert('No se pudo generar el archivo para estadísticas.');
					$("#btnAbrirEstadisticas").removeClass('disabled');
					$("#btnAbrirEstadisticas").html('Estadisticas');
					return;
				}
				$("#btnAbrirEstadisticas").removeClass('disabled');
				$("#btnAbrirEstadisticas").html('Estadisticas');					
				abrirEnPestana('estadisticas_padron.php');
			}).fail(function(xhr){
				var mensaje = 'No se pudo generar el archivo para estadísticas.';
				if(xhr.responseJSON && xhr.responseJSON.error) mensaje += '\n' + xhr.responseJSON.error;
				alert(mensaje);
				$("#btnAbrirEstadisticas").removeClass('disabled');
				$("#btnAbrirEstadisticas").html('Estadisticas');
			});

			

		});
		$("#btnMostrarPantalla").on('click',function(e){
			

			$("#btnMostrarPantalla").addClass('disabled');
			$("#btnMostrarPantalla").html('');					
			$("#btnMostrarPantalla").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			

			url = GetURLAJAX('contar_cantidad_afiliados');
			$.ajax({url: url, type: 'GET', dataType: 'json'}).then(function(data){
				if(data['cantidad_afiliados']){
					console.log(Number(data['cantidad_afiliados']));
					if(Number(data['cantidad_afiliados']) < 25000){
						url = GetURLAJAX('guardar_csv');
						$.ajax({url: url}).then(function(data){

							if(data=="0 registros"){
								$("#btnMostrarPantalla").removeClass('disabled'); 
								$('#btnMostrarPantalla').html('Mostrar Pantalla');	
								alert('La consulta no arroja resultados.');
								console.log(data); return false;	
							}else{

								$("#btnMostrarPantalla").removeClass('disabled'); 
								$('#btnMostrarPantalla').html('Mostrar Pantalla');	
								abrirEnPestana('filtros_padron.php');
							}
						});
						console.log('Se puede');
					}else{
						console.log('No se puede');
						$("#btnMostrarPantalla").removeClass('disabled'); 
						$('#btnMostrarPantalla').html('Mostrar Pantalla');
						alert(`La presentacion por pantalla admite hasta 25mil registros.\nCantidad actual ${data['cantidad_afiliados']}.\nDescargue el padron en formato Excel.`);	
					}
				}
			});

			setTimeout(function(){ 
				//$("#btnMostrarPantalla").removeClass('disabled'); 
				//$('#btnMostrarPantalla').html('Mostrar Pantalla');	
			},2000);
		});
		$("#GuardarFiltros").on('click',function(e){
			$("#GuardarFiltros").attr('disabled','disabled');
			$("#GuardarFiltros").html('');					
			$("#GuardarFiltros").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			url = GetURLAJAX('guardar_filtros');

			var nombre_nuevo_filtro = prompt('Nombre el nuevo filtro');

			if(nombre_nuevo_filtro){
				$.ajax({url: url,data: {nombre_nuevo_filtro: nombre_nuevo_filtro}}).then(function(data){
					console.log(data);
					if(data=="ok"){
						alert('Nuevo filtro guardado');
						TraerFiltros();
					}	
				});
			}			
			setTimeout(function(){ 
				$("#GuardarFiltros").removeAttr('disabled'); 
				$('#GuardarFiltros').html('Guardar Filtros');	
			},3000);
		});
		$("#btnModeloVisitarr").on('click',function(e){
			

			$("#btnModeloVisitar").addClass('disabled');
			$("#btnModeloVisitar").html('');					
			$("#btnModeloVisitar").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			$("#formato").val(2);
			
			url = GetURLAJAX('guardar_csv');


			$.ajax({url: url}).then(function(data){

				
				//console.log(data); return false;	
				if(data=="0 registros"){
					alert('La consulta no arroja resultados.');
					console.log(data); return false;	
				}
				else{
					
					abrirEnPestana('filtros_padron.html');
				}	
				

				
			});
			setTimeout(function(){ 
				$("#btnModeloVisitar").removeClass('disabled'); 
				$('#btnModeloVisitar').html('Modelo VISITAR ');	
			},2000);

		});

		$("#btnModeloVisitar").on('click',function(e){
			
			e.preventDefault();

			$("#btnModeloVisitar").addClass('disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			$("#formato").val(2);
			
			url = GetURLAJAX('lst_padron_filtros_csv'); 
			//console.log(url);return false;
			abrirEnPestana(url);
			setTimeout(function(){ 
				$("#btnModeloVisitar").removeClass('disabled');
				$('#btnModeloVisitar').html('Modelo VISITAR');	
			},2000);
		});

		$('.exc').on('click', function() {
			if ($(this).prop('checked')) {
				$('input[name="' + $(this).attr('name') + '"]').not(this).prop('checked', false);
			}
		});

		$('.selectores-multiples').on('change', function() {
	        var firstOption = $('option[value=""]', this);
	        var selectedOptions = $('option:selected', this);

	        if (selectedOptions.length > 1) {
	            // Deselect the first option if it is selected along with others
	            firstOption.prop('selected', false);
	        }
	    });
	    
	    $('#CargarFiltro').on('click',function(e){
	    	e.preventDefault();
	    	var filtros = $('#FiltrosGuardados option:selected').data('filtros');
	    	console.log(filtros);
	    	let {gerenciadora,tipo_afiliado,convenio_medico,filial,plan,provincia,servicios,sexo} = filtros;


				//Convenio Medico
				$('#gerenciadoras').val(null).trigger('change');
				if (typeof gerenciadora !== "undefined" && gerenciadora.trim() !== "") {
					var arr_gerenciadora = convertirCadenaANumeros(gerenciadora);
					$('#gerenciadoras').val(arr_gerenciadora).trigger('change');
				}else{
				  var primeraOpcion = $('#gerenciadoras option:first').val();
				  $('#gerenciadoras').val(primeraOpcion).trigger('change');
				}
				//Convenio Medico
				$('#convenio_medico').val(null).trigger('change');
				if (typeof filtros['convenio_medico'] !== "undefined" && filtros['convenio_medico'].trim() !== "") {
					var arr_convenio_medico = convertirCadenaANumeros(filtros['convenio_medico']);
					$('#convenio_medico').val(arr_convenio_medico).trigger('change');
				}else{
				  var primeraOpcion = $('#convenio_medico option:first').val();
				  $('#convenio_medico').val(primeraOpcion).trigger('change');
				}

				//Categoria Afiliado
				$('#categoria_afiliado').val(null).trigger('change');
				if (typeof filtros['categoria_afiliado'] !== "undefined" && filtros['categoria_afiliado'].trim() !== "") {
					var arr_categoria_afiliado = convertirCadenaANumeros(filtros['categoria_afiliado']);
					$('#categoria_afiliado').val(arr_categoria_afiliado).trigger('change');
				}else{
				  var primeraOpcion = $('#categoria_afiliado option:first').val();
				  $('#categoria_afiliado').val(primeraOpcion).trigger('change');
				}

				//TBT
				$('#tipo_beneficiario').val(null).trigger('change');
				if (typeof filtros['tipo_beneficiario'] !== "undefined" && filtros['tipo_beneficiario'].trim() !== "") {
					var arr_tipo_beneficiario = convertirCadenaANumeros(filtros['tipo_beneficiario']);
					$('#tipo_beneficiario').val(arr_tipo_beneficiario).trigger('change');
				}else{
				  var primeraOpcion = $('#tipo_beneficiario option:first').val();
				  $('#tipo_beneficiario').val(primeraOpcion).trigger('change');
				}

				//Provincia
				$('#provincias').val(null).trigger('change');
				if (typeof filtros['provincia'] !== "undefined" && filtros['provincia'].trim() !== "") {
					var arr_provincia = convertirCadenaANumeros(filtros['provincia']);
					$('#provincias').val(arr_provincia).trigger('change');
				}else{
				  var primeraOpcion = $('#provincia option:first').val();
				  $('#provincias').val(primeraOpcion).trigger('change');
				}

				//Plan
				$('#plan').val(null).trigger('change');
				if (typeof filtros['plan'] !== "undefined" && filtros['plan'].trim() !== "") {
					var arr_plan = convertirCadenaANumeros(filtros['plan']);
					$('#plan').val(arr_plan).trigger('change');
				}else{
				  var primeraOpcion = $('#plan option:first').val();
				  $('#plan').val(primeraOpcion).trigger('change');
				}

				//Filial
				$('#filial').val(null).trigger('change');
				if (typeof filtros['filial'] !== "undefined" && filtros['filial'].trim() !== "") {
					var arr_filial = convertirCadenaANumeros(filtros['filial']);
					$('#filial').val(arr_filial).trigger('change');
				}else{
				  var primeraOpcion = $('#filial option:first').val();
				  $('#filial').val(primeraOpcion).trigger('change');
				}

				//Sexo
				$('#sexo').val(null).trigger('change');
				if (typeof filtros['sexo'] !== "undefined" && filtros['sexo'].trim() !== "") {
					$('#sexo').val(filtros['sexo']).trigger('change');
				}else{
				  var primeraOpcion = $('#sexo option:first').val();
				  $('#sexo').val(primeraOpcion).trigger('change');
				}

				//Incapacidad
				$('#incapacidad').val(null).trigger('change');
				if (typeof filtros['incapacidad'] !== "undefined" && filtros['incapacidad'].trim() !== "") {
					$('#incapacidad').val(filtros['incapacidad']).trigger('change');
				}else{
				  var primeraOpcion = $('#incapacidad option:first').val();
				  $('#incapacidad').val(primeraOpcion).trigger('change');
				}

				//Edad Desde
				$('#edad_desde').val(null).trigger('change');
				if (typeof filtros['edad_desde'] !== "undefined" && filtros['edad_desde'].trim() !== "") {
					$('#edad_desde').val(filtros['edad_desde']).trigger('change');
				}else{
				  var primeraOpcion = $('#edad_desde option:first').val();
				  $('#edad_desde').val(primeraOpcion).trigger('change');
				}

				//Edad Hasta
				$('#edad_hasta').val(null).trigger('change');
				if (typeof filtros['edad_hasta'] !== "undefined" && filtros['edad_hasta'].trim() !== "") {
					$('#edad_hasta').val(filtros['edad_hasta']).trigger('change');
				}else{
				  var primeraOpcion = $('#edad_hasta option:first').val();
				  $('#edad_hasta').val(primeraOpcion).trigger('change');
				}

				//Servicios
				$('#servicios').val(null).trigger('change');
				if (typeof filtros['servicios'] !== "undefined" && filtros['servicios'].trim() !== "") {
					$('#servicios').val(filtros['servicios']).trigger('change');
				}else{
				  var primeraOpcion = $('#servicios option:first').val();
				  $('#servicios').val(primeraOpcion).trigger('change');
				}

				//Parentesco
				$('#parentesco').val(null).trigger('change');
				if (typeof filtros['parentesco'] !== "undefined" && filtros['parentesco'].trim() !== "") {
					var arr_parentesco = convertirCadenaANumeros(filtros['parentesco']);
					$('#parentesco').val(arr_parentesco).trigger('change');
				}else{
				  var primeraOpcion = $('#parentesco option:first').val();
				  $('#parentesco').val(primeraOpcion).trigger('change');
				}
				//Empresa
				$('#empresa').val(null).trigger('change');
				if (typeof filtros['empresa'] !== "undefined" && filtros['empresa'].trim() !== "") {
					var arr_empresa = convertirCadenaANumeros(filtros['empresa']);
					$('#empresa').val(arr_empresa).trigger('change');
				}else{
				  var primeraOpcion = $('#empresa option:first').val();
				  $('#empresa').val(primeraOpcion).trigger('change');
				}


	    })
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

			if($('#gerenciadoras').val()){
				url = url.concat(`&gerenciadora=${$('#gerenciadoras').val()}`);

				var opcionesSeleccionadas = $('#gerenciadoras').find('option:selected');
				var textoOpciones = opcionesSeleccionadas.map(function() {
					return $(this).text();
				}).get().join(', ');
				url = url.concat(`&gerenciadora_nombre=${textoOpciones}`);
			}else {
				// No hay selección: usar todos los valores posibles
				var todosLosValores = $('#gerenciadoras option').map(function() {
					return $(this).val();
				}).get();

				url = url.concat(`&gerenciadora=${todosLosValores.join(',')}`);
				url = url.concat(`&gerenciadora_nombre=Todos`);
			}	

			if($('#tipo_beneficiario').val() != ""){

				if($('#tipo_beneficiario').val()==0){
					valor_tbt ="cero";
				}else{
					valor_tbt = $('#tipo_beneficiario').val();
				}
				url = url.concat(`&tipo_beneficiario=${valor_tbt}`);

		    var opcionesSeleccionadas = $('#tipo_beneficiario').find('option:selected');
		    var textoOpciones = opcionesSeleccionadas.map(function() {
		      return $(this).text();
		    }).get().join(', ');
		    url = url.concat(`&tipo_beneficiario_nombre=${textoOpciones}`);


			}
			if($('#provincias').val()){
				url = url.concat(`&provincia=${$('#provincias').val()}`);

		    var opcionesSeleccionadas = $('#provincias').find('option:selected');
		    var textoOpciones = opcionesSeleccionadas.map(function() {
		      return $(this).text();
		    }).get().join(', ');
		    url = url.concat(`&provincia_nombre=${textoOpciones}`);
			}
			if($('#parentesco').val() != ""){
				
				if($('#parentesco').val()==0){
					valor_parentesco="cero";
				}else{
					valor_parentesco= $('#parentesco').val();
				}
				url = url.concat(`&parentesco=${valor_parentesco}`);

				var opcionesSeleccionadas = $('#parentesco').find('option:selected');
				var textoOpciones = opcionesSeleccionadas.map(function() {
					return $(this).text();
				}).get().join(', ');
				url = url.concat(`&parentesco_nombre=${textoOpciones}`);
			}
			if($('#filial').val()){
				url = url.concat(`&filial=${$('#filial').val()}`);

			    var opcionesSeleccionadas = $('#filial').find('option:selected');
			    var textoOpciones = opcionesSeleccionadas.map(function() {
			      return $(this).text();
			    }).get().join(', ');
			    url = url.concat(`&filial_nombre=${textoOpciones}`);
			}
			if($('#sexo').val()){
				url = url.concat(`&sexo=${$('#sexo').val()}`);
			}
			if($('#incapacidad').val()){
				url = url.concat(`&incapacidad=${$('#incapacidad').val().trim()}`);
			}
			if($('#edad_desde').val() && $('#edad_hasta').val()){
				url = url.concat(`&edad_desde=${$('#edad_desde').val().trim()}&edad_hasta=${$('#edad_hasta').val().trim()}`);
			}
			if($("input[name='excentricidades']:checked").val()){
				url = url.concat(`&excentricidad=${$("input[name='excentricidades']:checked").val().trim()}`);
			}

			numero_formato = 1;

			if(INST_FORMATO_PADRON_EXCEL){
				numero_formato = INST_FORMATO_PADRON_EXCEL;
			}

			if($("#formato").val()==2){
				numero_formato = 2; //Padron Visitar
			}
			if(parametro == 'contar_cantidad_afiliados'){
				numero_formato = "contar";	
			}

			url = url.concat(`&formato=${numero_formato}`);

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
				$('#FiltrosGuardados').append(`<option value='${id}' data-filtros='${filtros}'>${nombre}</option>`);
			}
		});
	}
</script>
</html>
