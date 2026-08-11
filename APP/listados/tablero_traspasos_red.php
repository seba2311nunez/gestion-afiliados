
<HTML>
  <head>
  	<title>Principal Dev</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>		
		<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<link rel="stylesheet" href="../../Main/principal.css" />
		<script src="../../Main/principal.js"></script>
		<style type="text/css">
			.nderecha{
				text-align: right;
			}
			#tab_factIngresadasMensual tbody tr td{
				background-color: #b3b3b3;
				color: black;
			}
			#tab_factIngresadasMensual thead tr th{
				background-color: #cccccc;
			}
			#tabEstadisticaTrapasosGerenciadora tbody tr td {
				text-align: right;
			}
			#tabEstadisticaTrapasosGerenciadora tbody tr td.a-left {
				text-align: left !important;
			}
			.btn-disconnect{
				position: fixed;
				top:1%; 
				right:1%;
				z-index: 1;
			}
			.btnDescargaXlsCapitas{
				cursor: pointer;
			}
			#bottomDiv {
		    position: fixed;
		    left: 0;
		    bottom: 0;
		    width: 100%;
		    background-color: #333;
		    color: #fff;
		    padding: 10px;
		    text-align: center;
		}
		</style>
    <script>
      $(document).ready(function(){
	      		// $('#actividad_sistema').DataTable( {
				    // fixedHeader: true
				// });
  		});
    	function cerrar_sesion(id){
				window.parent.location=id;
			}
			function cerrar_sesion_volver(id){
				window.parent.location=id;								
			}
    </script>
  </head>
	<div class="container">
		<div class="x_panel">
			<div class="tituloDiv">
				Tablero Traspasos entrantes y salientes.
			</div>
			<hr>
			<div class="row">
				<div class="col-md-3">
					<select id="periodo_traspaso" class="form-control">
						<option selected>Seleccione...</option>
					</select>			
				</div>
			</div>
			<div class="row">
				<table id="tabEstadisticaTrapasosGerenciadora" class="table table-stripped">
					<thead>
						<tr>
							<th>Red</th>
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
</body>
	<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
	<script src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
	<script>
		const DOMINIO = "<?echo DOMINIO;?>";
		const INST_NAME = "<?echo INST_NAME;?>";
		$(function(){

			$.ajax({
				url: 'ajax_padron.php',
				type: 'GET',
				dataType: 'json',
				data: {parametro: 'traer_periodos'},
			})
			.done(function(data){			
				for(var i=0; i<=data.length-1 ;i++){
					let {periodo} = data[i];
					$('#periodo_traspaso').append(`<option value='${periodo}'>${periodo}</option>`);
				}
			});
		});

		$(document).on('change',"#periodo_traspaso",function(e){
			TraspasosAltasBajasGerenciadora($(this).val());
		});

		$(document).on('click','.btnDescargaXlsCapitas',function(){

			var periodo = $(this).data('periodo');
			var tipo = $(this).data('tipo');
			var capita = $(this).data('capita');

			var url = "../../Main/ajax.php?parametro=abi_xls_detalle_capita&capita="+capita+"&tipo="+tipo+"&fv="+periodo ;

			abrirEnPestana(url);
		});
		
		var percentColors = [
		    { pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
		    { pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
		    { pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } } ];

		var getColorForPercentage = function(pct) {
		    for (var i = 1; i < percentColors.length - 1; i++) {
		        if (pct < percentColors[i].pct) {
		            break;
		        }
		    }
		    var lower = percentColors[i - 1];
		    var upper = percentColors[i];
		    var range = upper.pct - lower.pct;
		    var rangePct = (pct - lower.pct) / range;
		    var pctLower = 1 - rangePct;
		    var pctUpper = rangePct;
		    var color = {
		        r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
		        g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
		        b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
		    };
		    return 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
		    // or output as hex if preferred
		} 

		function UltimoAcceso(url){
			var datos = {
				"parametro": "ultimo_acceso",
				"id_user": "<?=$id_user;?>",
				"sistema": "afiliaciones"
			};
				
			$.ajax({
			
				url: url,
				type: 'get',
				data: datos,
				success: function(data){						
					
					$('#ultimo_acceso').html(data);
				}
			})
		}
		function ListadosPadron() {
		    $('#listados_padron').html("");

		    $.ajax({
		        url: 'ajax.php',
		        type: 'GET',
		        dataType: 'json',
		        data: { parametro: 'listados_padron' },
		    })
		    .done(function(data) {
		        console.table(data);

		        $('#listados_padron').append('<ul class="list-group">');
		        for (var i = 0; i < data.length; i++) {
		            // Create a link wrapping the list item content
		            var listItem = '<a href="../' + data[i]['url'] + '" class="list-group-item">' +
		                               data[i]['nombre'] +
		                               '<span class="badge">Ir</span>' +
		                           '</a>';
		            // Append the list item to the list
		            $('#listados_padron').append(listItem);
		        }
		        $('#listados_padron').append("</ul>");
		    });
		}
		//Bajas RG
		function genera_tabla_bajas_rg(titulo){

			$("#divListadosTitulo").html(titulo);

			$('#divListados').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Procesando');
			
			$('#divListados').html("<table id='tab_Bajas_rg' class='table'>"
											+"<thead>"
												+"<tr>"
													+"<th>#</th>"
													+"<th></th>"
													+"<th>Periodo</th>"														
													+"<th>Total bajas</th>"
													+"<th>Empadronadas</th>"
													+"<th>Assistencial</th>"
													+"<th>Scientis</th>"
													+"<th>RSM</th>"
													+"<th>PPLAN</th>"
													+"<th>PROPIOS</th>"
													+"<th>Otros</th>"
												+"</tr>"
											+"</thead>"
											+"<tbody>"					    		
											+"</tbody>"
										    +"</table>");
		}
		function llena_tabla_bajas_rg(){


			$.getJSON('ajax.php',
						{ parametro: "lst_bajas_rg_x_periodo_desreguladora" },						       				
						function(data){ 
							
							$("#tab_Bajas_rg tbody").html("");
							
							for(var i=0; i<=data.length-1 ;i++){
							
								$("#tab_Bajas_rg tbody").append("<tr>"																
																	+"<td>"+(i+1)+"</td>"
																	+"<td></td>"
																	// +"<td>"+data[i]['nombre_periodo']+"</td>"
																	+"<td>"+data[i]['periodo1']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['cant_registros']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['personas']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['assistencial']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['scientis']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['rsm']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['pplan']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['propios']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['otros']+"</td>"					      				
																+"</tr>") ;		
							}	
						}//fin function data
			);//fin getjson
		}
		//Traaspasos de alta
		function genera_tabla_traspasos_rg_alta(titulo){

			$("#divListadosTitulo").html(titulo);

			$('#divListados').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Procesando');
			
			$('#divListados').html("<table id='tab_traspasos_rg_alta' class='table'>"
											+"<thead>"
												+"<tr>"
													+"<th>#</th>"
													+"<th></th>"
													+"<th>Periodo</th>"														
													+"<th>Propios</th>"
													+"<th>Assistencial</th>"
													+"<th>Scientis</th>"														
													+"<th>Sin asignar</th>"
													+"<th>RSM</th>"
													+"<th>RSM Sin asignar</th>"
													+"<th>Todos</th>"
												+"</tr>"
											+"</thead>"
											+"<tbody>"					    		
											+"</tbody>"
										    +"</table>");

		
			$("#tab_traspasos_rg_alta tbody").html("Cargando...");
		}
		function llena_tabla_traspasos_rg_alta(){


			$.getJSON('ajax.php',
						{ parametro: "traspasos_rg_alta" },						       				
						function(data){ 
							
							$("#tab_traspasos_rg_alta tbody").html("");
							
							for(var i=0; i<=data.length-1 ;i++){
								
								$("#tab_traspasos_rg_alta tbody").append("<tr>"																
																	+"<td>"+(i+1)+"</td>"
																	+"<td></td>"
																	// +"<td>"+data[i]['nombre_periodo']+"</td>"
																	+"<td>"+data[i]['fec_eleccion']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['propios']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['assistencial']+"</td>"	
																	+"<td class='dto_numero'>"+data[i]['scientis']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['sin_asignar']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['rsm']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['rsm_sin_asignar']+"</td>"
																	+"<td class='dto_numero'>"+data[i]['total']+"</td>"
																						      				
																+"</tr>") ;		
							}	
						}//fin function data
			);//fin getjson
		}

		function TraspasosAltasBajasGerenciadora(periodo){
			$.getJSON('../../Main/ajax.php',{ parametro: "abi_x_periodo_tipo_gerenciadora",periodo: periodo},function(data){ 
				$("#tabEstadisticaTrapasosGerenciadora tbody").html("");
				let totalAltaRg = 0;
				let totalAltaMt = 0;
				let totalBajaRg = 0;
				let totalBajaMt = 0;
				let totalAltasTotal = 0;
				let totalBajasTotal = 0;
				for(var i=0; i<=data.length-1 ;i++){
					let {fecha_vigencia,convenio_real} = data[i];

					$("#tabEstadisticaTrapasosGerenciadora tbody").append(`
						<tr>																
							<td class='a-left'>${convenio_real}</td>
							<td > 
								<a class='btnDescargaXlsCapitas' data-tipo='alta_rg' data-capita='${convenio_real}' data-periodo=${periodo} >
									${data[i]['alta_rg']}
								</a>
							</td>
							<td > 
								<a class='btnDescargaXlsCapitas' data-tipo='alta_mt' data-capita='${convenio_real}' data-periodo=${periodo} >
									${data[i]['alta_mt']}
								</a>
							</td>											
							<td>${data[i]['altas_total']}</td>

							<td > 
								<a class='btnDescargaXlsCapitas' data-tipo='baja_rg' data-capita='${convenio_real}' data-periodo=${periodo} >
									${data[i]['baja_rg']}
								</a>
							</td>

							<td > 
								<a class='btnDescargaXlsCapitas' data-tipo='baja_mt' data-capita='${convenio_real}' data-periodo=${periodo} >
									${data[i]['baja_mt']}
								</a>
							</td>									
							<td>${data[i]['bajas_total']}</td>	
							<td>${parseInt(data[i]['altas_total'])-parseInt(data[i]['bajas_total'])}</td>															      				
						</tr>

						`);		
			    totalAltaRg += parseInt(data[i]['alta_rg']);
			    totalAltaMt += parseInt(data[i]['alta_mt']);
			    totalBajaRg += parseInt(data[i]['baja_rg']);
			    totalBajaMt += parseInt(data[i]['baja_mt']);
			    totalAltasTotal += parseInt(data[i]['altas_total']);
			    totalBajasTotal += parseInt(data[i]['bajas_total']);
				}	
				$("#tabEstadisticaTrapasosGerenciadora tbody").append(
				    "<tr>" +
				    "<td>Total:</td>" +
				    "<td>" + totalAltaRg + "</td>" +
				    "<td>" + totalAltaMt + "</td>" +
				    "<td>" + totalAltasTotal + "</td>" +
				    "<td>" + totalBajaRg + "</td>" +
				    "<td>" + totalBajaMt + "</td>" +
				    "<td>" + totalBajasTotal + "</td>" +
				    "<td>" + (totalAltasTotal - totalBajasTotal) + "</td>" +
				    "</tr>"
				);
			});//fin getjson
		}
		function abrirEnPestana(url) {
			var a = document.createElement("a");
			a.target = "_blank";
			a.href = url;
			a.click();
		}
	</script>
</HTML>
