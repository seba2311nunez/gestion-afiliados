<??>	

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

	<title>Tablero Traspasos entrantes y salientes</title>
	<style type="text/css">
		.btnDescargaXlsCapitas{
			cursor: pointer;
		}
		.btnConsultaCapitas{
			cursor: pointer;	
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<div class="tituloDiv">
				Tablero Traspasos entrantes y salientes.
			</div>
			<hr>
  		<div class="row">
				<div class="col-xs-4">
					<select id="gerenciadoras" class='form-control'>
						<option value="">Todas</option>
					</select>
				</div>	
				<!--
				<div class="col-xs-4">
					<a id="btnDescargarTraspasos" class="btn btn-success" target="_blank">
						Descargar
					</a>
				</div>
				-->    
			</div>
			<div class="row">
				<table id="tabEstadisticaTrapasos" class="table table-stripped">
					<thead>
						<tr>
							<th>Periodo</th>
							<th>Altas RG</th>
							<th>Altas MT</th>
							<th>Total altas</th>
							<th>Bajas RG</th>
							<th>Bajas MT</th>
							<th>Total bajas</th>
							<th>Saldo Mes</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Detalle de <b><span id='s_tipo'></span></b>  | Periodo: <b><span id='s_periodo'></span></b>   </h4>
				</div>
				<div class="modal-body">
					<table id="tabDetalleCapitas" class="table" style="width: 70%; margin: auto;">
						<thead>
							<tr >
								<th style='text-align: left;'>Capita</th>
								<th style='text-align: right;'>Total</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</body>

<script>
	$(function(){	
		TraspasosAltasBajas('TOTALES');

		$.ajax({
			url: 'ajax_padron.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'traer_gerenciadora'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {convenio_real} = data[i];
				$('#gerenciadoras').append(`<option value='${convenio_real}'>${convenio_real}</option>`);
			}
		});

		$("#tabEstadisticaTrapasos tbody").on('click','.btnConsultaCapitas',function(){

			$("#tabDetalleCapitas tbody").html("");

			var periodo = $(this).data('periodo');
			var tipo = $(this).data('tipo');

			$('#s_periodo').html(periodo);
			$('#s_tipo').html(tipo);

			//console.log(periodo,tipo);
			$.getJSON('../../Main/ajax.php',
						{ parametro: "abi_capita_x_periodo_tipo", tipo: tipo, periodo: periodo},						       				
						function(data){ 
							
							for(var i=0; i<=data.length-1 ;i++){
							
								$("#tabDetalleCapitas tbody").append("<tr>"																
											+"<td style='text-align: left;'>"+data[i]['capita']+"</td>"
											+"<td style='text-align: right;' >" 
												+"<a class='btnDescargaXlsCapitas'  data-capita='"+data[i]['capita']+"' data-tipo='"+tipo+"' data-periodo='"+periodo+"'  >"
													+data[i]['total']
												+"</a>"
											+"</td>"
										+"</tr>");		
																											      				
							}	
							let sum = data.reduce((accumulator, object) => {
							  return accumulator + Number(object.total);
							}, 0);

							$("#tabDetalleCapitas tbody").append("<tr>"																
									+"<th style='text-align: left;'>TOTAL</th>"
										+"<th style='text-align: right;' >" 
											+"<a class='btnDescargaXlsCapitas'  data-capita='TOTAL' data-tipo='"+tipo+"' data-periodo='"+periodo+"'  >"
												+sum
											+"</a>"
										+"</th>"
									+"</tr>");
						}//fin function data
			);//fin getjson
		});
		$(document).on('change','#gerenciadoras',function(e){
			TraspasosAltasBajas($(this).val());
		});
		$(document).on('click','.btnDescargaXlsCapitas',function(){

			var periodo = $(this).data('periodo');
			var tipo = $(this).data('tipo');
			var capita = $(this).data('capita');

			var url = "../../Main/ajax.php?parametro=abi_xls_detalle_capita&capita="+capita+"&tipo="+tipo+"&fv="+periodo ;

			abrirEnPestana(url);
		});
	})
	function TraspasosAltasBajas(convenio){
		$.getJSON('../../Main/ajax.php',
					{ parametro: "abi_x_periodo_tipo",convenio: convenio},						       				
					function(data){ 
						$("#tabEstadisticaTrapasos tbody").html("");
						for(var i=0; i<=data.length-1 ;i++){
							let {periodo} = data[i];
							$("#tabEstadisticaTrapasos tbody").append("<tr>"																
										+`<td><a class='periodo_traspaso' data-toggle="collapse" data-target="#tablero_traspasos_gerenciadora" data-periodo='${periodo}'><b>${periodo.substring(0,7)}</b></a></td>`
										+"<td class='text-right'>" 
											+"<a class='btnConsultaCapitas' data-tipo='alta_rg' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
												+data[i]['alta_rg']
											+"</a>"
										+"</td>"
										+"<td class='text-right'>" 
											+"<a class='btnConsultaCapitas' data-tipo='alta_mt' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
												+data[i]['alta_mt']
											+"</a>"
										+"</td>"											
										+"<td class='text-right font-weight-bold'>"
											+data[i]['altas_total']
										+"</td>"

										+"<td class='text-right'>" 
											+"<a class='btnConsultaCapitas' data-tipo='baja_rg' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
												+data[i]['baja_rg']
											+"</a>"
										+"</td>"

										+"<td class='text-right'>" 
											+"<a class='btnConsultaCapitas' data-tipo='baja_mt' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
												+data[i]['baja_mt']
											+"</a>"
										+"</td>"											
										+"<td class='text-right font-weight-bold'>" 
											+data[i]['bajas_total']
										+"</td>"	
										+`<td class='text-right font-weight-bold'>
											${parseInt(data[i]['altas_total'])-parseInt(data[i]['bajas_total'])}
										</td>`																      				
									+"</tr>") ;		
						}	
					}//fin function data

		);//fin getjson
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