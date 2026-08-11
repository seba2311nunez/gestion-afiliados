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

	<title>Descargar Padron DJ/AP</title>
	<style type="text/css">
	    .btn-with-legend {
	      display: flex;
	      flex-direction: column;
	      align-items: left;
	    }
	    .btn-legend {
	      margin-top: 5px;
	      font-size: 0.9rem;
	      color: #666;
	    }
	</style>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<input type="hidden" id="formato" value="1">
			<div class="tituloDiv">
				Descargar Padron DJ/AP.
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="btn-group btn-with-legend" role="group">
						<a id="btnDescargar" class="btn btn-success text-light" target="_blank" style=' width:100px;'>Descargar</a>
						<span class="btn-legend">El padron de esta consulta esta armado a <b id="ultima_actualizacion"></b></span>
					</div>
				</div>
			</div>			
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
			data: {parametro: 'listar_logs', tipo: 'Descarga Apaisado'},
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

		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'padron_apaisado_ultima_actualizacion'},
		}).done(function(data){
			let {fecha} = data;
			$('#ultima_actualizacion').html(fecha);

		});
		
		$("#btnDescargar").on('click',function(e){
			
			e.preventDefault();

			$("#btnDescargar").addClass('disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			url = GetURLAJAX('lst_padron_apaisado_csv'); 
			//console.log(url);return false;
			abrirEnPestana(url);
			setTimeout(function(){ 
				
			},2000);
			$("#btnDescargar").removeClass('disabled'); 
			$('#btnDescargar').html('Descargar');	
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

			if($('#gerenciadoras').val()){
				url = url.concat(`&gerenciadora=${$('#gerenciadoras').val()}`);
		    
		    var opcionesSeleccionadas = $('#gerenciadoras').find('option:selected');
		    var textoOpciones = opcionesSeleccionadas.map(function() {
		      return $(this).text();
		    }).get().join(', ');
		    url = url.concat(`&gerenciadora_nombre=${textoOpciones}`);
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
</script>
</html>