<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Bajas RG Procedencia</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		
		<!-- Bootstrap -->		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">		
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
		
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		
		<!-- Databatables -->
		<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		
		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src='http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js'></script>
	</head>
	<body>
		<div class="col-sm-12">
			<div class="tituloDiv">
				Procedencia de las Bajas RG recibidas
			</div>
			<div class="row" style="margin: 10px;">
				<div class='col-md-12'>	
					<div class="row">
						<div class="col-md-2">
							<input type="date" class="form-control" name="procedencia_desde" id="procedencia_desde" value="<?php echo "$date";?>" />
						</div>
						<div class="col-md-2">
							<input type="date" class="form-control" name="procedencia_hasta" id="procedencia_hasta" value="<?php echo "$date";?>" />
						</div>
						<div class="col-md-2">
							<select id="SeleccionarDesreguladora" class="form-control">
								<option value="todas">Todas</option>
							</select>
						</div>
						<div class="col-md-6">
							
							<button id="VerTodos" class="btn btn-success">
								<span id="spanVerTodos">Descargar bajas</span> 
							</button>
							<button id="VerProcedencia" class="btn btn-info">
								<span id="spanVerProcedencia">Agrupar por procedencia</span> 
							</button>
							<button id="ExcelProcedencia" class="btn btn-info">
								<span id="spanExcelProcedencia">Excel por procedencia</span>
							</button>
						</div>	
					</div>
				</div>					
			</div>
			<hr>
			<div class="row">
				<div style="padding: 25px;">
					<table class="table table-striped table-sm" id="lista_procedencia">
						<thead>
							<th>#</th>
							<th></th>
							<th>Rnos</th>
							<th>Procedencia</th>
							<th>Cantidad</th>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>					
			</div>
		</div>
	</body>
</html>
<script>
	var fuente = 'bajas_rg_sss';
	$(function(){
		TraerDesreguladoras();

		$('#VerProcedencia').on('click',function(){
			CargarProcedencia();
		});

		$('#ExcelProcedencia').on('click',function(){

			var fdesde = $('#procedencia_desde').val();
    		var fhasta = $('#procedencia_hasta').val();
    		var id_desreguladora = $('#SeleccionarDesreguladora').val();
    		var desreguladora = $( "#SeleccionarDesreguladora option:selected" ).text();

    		window.open('../../agenda_archivos/excel_procedencia/totales.php?fuente='+fuente+'&fdesde='+fdesde+'&fhasta='+fhasta+'&id_desreguladora='+id_desreguladora+'&desreguladora='+desreguladora+'&parametro=totales');

		});

		$("#VerTodos").on('click',function(){
			var fdesde = $('#procedencia_desde').val();
    	var fhasta = $('#procedencia_hasta').val();
    	var id_desreguladora = $('#SeleccionarDesreguladora').val();
    	var desreguladora = $( "#SeleccionarDesreguladora option:selected" ).text();

    	window.open('../../agenda_archivos/excel_procedencia/totales.php?fuente='+fuente+'&fdesde='+fdesde+'&fhasta='+fhasta+'&rnos='+"x"+'&id_desreguladora='+id_desreguladora+'&desreguladora='+desreguladora+'&parametro=detalles');
		});

		$(document).on('click','.descargar_detalles',function(){
			var fdesde = $('#procedencia_desde').val();
    	var fhasta = $('#procedencia_hasta').val();
    	var rnos = $(this).data('rnos');
    	var id_desreguladora = $(this).data('id_desreguladora');
    	var desreguladora = $(this).data('desreguladora');

    	window.open('../../agenda_archivos/excel_procedencia/totales.php?fuente='+fuente+'&fdesde='+fdesde+'&fhasta='+fhasta+'&rnos='+rnos+'&id_desreguladora='+id_desreguladora+'&desreguladora='+desreguladora+'&parametro=detalles');

		});
	});
	function CargarProcedencia(){

  	var datos = {
  		'parametro':'TraerProcedencia',
  		'procedencia_desde': $('#procedencia_desde').val(),
  		'procedencia_hasta': $('#procedencia_hasta').val(),
  		'id_desreguladora': $('#SeleccionarDesreguladora').val(),
    	'desreguladora': $( "#SeleccionarDesreguladora option:selected" ).text()
  	};
  	$('#lista_procedencia tbody').html('');
  	
		$.ajax({
			url: '../../agenda_archivos/bajas_rg_sss/ajax.php',
			type: 'GET',
			dataType: 'json',
			data: datos,
		})
		.done(function(data) {
			console.table(data);

			if(data[0]['error']){

				$('#lista_procedencia tbody').append("<tr><td>No hay Resultados</td></tr>");
			}else{
				if(data.length>0){

					var id_desreguladora =  $('#SeleccionarDesreguladora').val();
    			var desreguladora = $("#SeleccionarDesreguladora option:selected").text();

					for(var i=0; i<=data.length-1; i++){

						$('#lista_procedencia tbody').append("<tr>"
															+"<td>"+(i+1)+"</td>"
															+"<td>"
													 			+"<div class='btn btn-group btn-group-default'>"
																+"<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>"
																		+"<i class='fa fa-ellipsis-v' aria-hidden='true'></i>"
																+"</button>"
																+"<ul class='dropdown-menu'>"
																	+"<li>"
																		+"<a class='descargar_detalles' data-rnos="+data[i]['rnos']+" data-id_desreguladora="+id_desreguladora+" data-desreguladora="+desreguladora+"> "
																			+"<i class='fas fa-wrench'></i>&nbsp; Excel"
																		+"</a>"
																	+"</li>"
																	
																+"</ul>"
															+"</div>"
															+"</td>"
															+"<td>"+data[i]['rnos']+"</td>"
															+"<td>"+data[i]['procedencia']+"</td>"
															+"<td>"+data[i]['cantidad']+"</td>"
															
															+"</tr>");
					}
				}
			}
		});	
  }
  function TraerDesreguladoras(){
  	$.ajax({
  		url: '../../agenda_archivos/bajas_rg_sss/ajax.php',
  		type: 'GET',
  		dataType: 'json',
  		data: {parametro: 'TraerDesreguladoras'},
  	})
  	.done(function(data) {
  		for(var i=0; i<=data.length-1; i++){
  			$('#SeleccionarDesreguladora').append(
  					"<option value='"+data[i]['id_desreguladora']+"'>"
  							+data[i]['desreguladora']
  					+"</option>"
  			);
  		}
  	});	
  }
</script>