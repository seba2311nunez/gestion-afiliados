<?
include (__DIR__."/../Config/Conectar.inc");
include (__DIR__."/../Config/funciones.inc");

$id_institucion = $_SESSION["id_institucion"];
$id_pplan = $_SESSION["id_pplan"];

if ( $_SESSION["usu"] == "" )
{ echo "<h1>Problemas con el ingreso al sistema </h1></br>"; 
exit();
}
$usuario=$_SESSION["usu"];
$id_user=$_SESSION["iduser"];
?>
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
		<link href="../Lib/estilos_propios/estilo_estandar.css" rel="stylesheet">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<link rel="stylesheet" href="principal.css" />
		<script src="principal.js"></script>
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
			#tabEstadisticaTrapasos tbody tr td {
				text-align: right;
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
  <body>    	
    <div class="container-fluid main">
    	
	  		<div class="x_panel">
		    				<h4>Ultimos accesos &nbsp;&nbsp;</h4>
		    				<hr>
		    				<!-- <b> Su ultimo acceso: <label id="ultimo_acceso"></label>	 </b> -->
		    				<hr>
		    				Accesos de hoy <br><br>
		    				<?php echo ultimos_accesos($base_usuarios);?>	    				
		    			</div>			
		    <div class="row">
    		<div class="x_panel" id="bottomDiv">		        			
	        Este sistema fue desarrollado <b>SMADM S.A.</b> |  Contacto: <b>sebastian@smadm.com</b>, <b>alan@smadm.com</b>      
	        <br>
	        Estas conectado como el usuario: <b><?php echo $_SESSION['usuario']; ?></b>
	        	<!---->					        
    		</div>
			</div>
		</div>
		<!-- Modal Listados -->
		<div id="modalListados" class="modal fade" role="dialog">
		  <div class="modal-dialog " style="width: 1100px;">
		
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title" id="divListadosTitulo">Modal Header</h4>
		      </div>
		      <div class="modal-body">
		        <div id="divListados">
		        	
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
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
	<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
	<script src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
	<script>
		const DOMINIO = "<?echo DOMINIO;?>";
		const INST_NAME = "<?echo INST_NAME;?>";
		$(function(){
		  var url = 'http://'+DOMINIO+'/ws/ws_padron.php';

		  UltimoAcceso(url);
		  ListadosPadron();
		  TraspasosAltasBajas('TOTALES');

	   	$('#myGroup').on('show.bs.collapse','.collapse', function(){

		    $('#myGroup').find('.collapse.in').collapse('hide');
			})
			$('.btnListados').on('click',function(){
					
					var tipo = $(this).data("tipo");
					var titulo = $(this).html().trim();
					
					//console.log(titulo);
					switch(tipo){
						//code
						case 'bajas_rg_sss':
							
							genera_tabla_bajas_rg(titulo);
							llena_tabla_bajas_rg();

							break;
							
						case 'opciones_rg_altas':
							
							genera_tabla_traspasos_rg_alta(titulo);
							llena_tabla_traspasos_rg_alta();

							break;

					}
			})
			$("#tabEstadisticaTrapasos tbody").on('click','.btnConsultaCapitas',function(){

				$("#tabDetalleCapitas tbody").html("");

				var periodo = $(this).data('periodo');
				var tipo = $(this).data('tipo');

				$('#s_periodo').html(periodo);
				$('#s_tipo').html(tipo);

				//console.log(periodo,tipo);
				$.getJSON('ajax.php',
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
			})
			$(document).on('click','.btnDescargaXlsCapitas',function(){

				var periodo = $(this).data('periodo');
				var tipo = $(this).data('tipo');
				var capita = $(this).data('capita');

				var url = "ajax.php?parametro=abi_xls_detalle_capita&capita="+capita+"&tipo="+tipo+"&fv="+periodo ;

				abrirEnPestana(url) ;
			})
			$.ajax({
				url: '../APP/listados/ajax_padron.php',
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
		});
		$(document).on('change','#gerenciadoras',function(e){
			TraspasosAltasBajas($(this).val());
		});
		$("#btnDescargarTraspasos").on('click',function(e){
			
			e.preventDefault();

			let parametro = "listar_traspasos_agrupados_periodo";
			var url = `http://138.99.7.172/lee_xls/${INST_NAME}/ajax.php?parametro=${parametro}`;
			if($('#gerenciadoras').val()){
				url = url.concat(`&convenio=${$('#gerenciadoras').val()}`);
			}else{
				url = url.concat(`&convenio=TOTALES`);
			}
			window.open(url);
		});
		$(document).on('click',".periodo_traspaso",function(e){
			TraspasosAltasBajasGerenciadora($(this).data('periodo'));
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
		function TraspasosAltasBajas(convenio){
			$.getJSON('ajax.php',
						{ parametro: "abi_x_periodo_tipo",convenio: convenio},						       				
						function(data){ 
							$("#tabEstadisticaTrapasos tbody").html("");
							for(var i=0; i<=data.length-1 ;i++){
								let {periodo} = data[i];
								$("#tabEstadisticaTrapasos tbody").append("<tr>"																
											+`<td><a class='periodo_traspaso' data-toggle="collapse" data-target="#tablero_traspasos_gerenciadora" data-periodo='${periodo}'>${data[i]['periodo']}</a></td>`
											+"<td >" 
												+"<a class='btnConsultaCapitas' data-tipo='alta_rg' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
													+data[i]['alta_rg']
												+"</a>"
											+"</td>"
											+"<td >" 
												+"<a class='btnConsultaCapitas' data-tipo='alta_mt' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
													+data[i]['alta_mt']
												+"</a>"
											+"</td>"											
											+"<td>"+data[i]['altas_total']+"</td>"

											+"<td >" 
												+"<a class='btnConsultaCapitas' data-tipo='baja_rg' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
													+data[i]['baja_rg']
												+"</a>"
											+"</td>"

											+"<td >" 
												+"<a class='btnConsultaCapitas' data-tipo='baja_mt' data-periodo="+periodo+" data-toggle='modal' data-target='#myModal'>"
													+data[i]['baja_mt']
												+"</a>"
											+"</td>"											
																						
											+"<td>"+data[i]['bajas_total']+"</td>"	
											+`<td>${parseInt(data[i]['altas_total'])-parseInt(data[i]['bajas_total'])}</td>`																      				
										+"</tr>") ;		
							}	
						}//fin function data

			);//fin getjson
		}
		function TraspasosAltasBajasGerenciadora(periodo){
			$.getJSON('ajax.php',{ parametro: "abi_x_periodo_tipo_gerenciadora",periodo: periodo},function(data){ 
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
							<td>${convenio_real}</td>
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

<?

function ultimos_accesos($base_usuarios){ 
	$sql="SELECT usuario,nombrecompleto,perfil,DATE_FORMAT(fecha_ult_acceso,'%d/%m/%Y %H:%i') AS fecha_ult_acceso
					FROM $base_usuarios._users 
					WHERE sistema='afiliaciones' 
						AND fecha_ult_acceso LIKE CONCAT(CURDATE(),' %') ";
						
	$rs=mysql_query($sql) or die(mysql_error().$sql);
	 
	$tabla="<table class='table' style='font-size: 12px; width: 300px;'>
	<thead>
		<tr class='warning'>
			<th>Usuario</th>
			<th>Fecha</th>
		</tr>
	</thead>
	<tbody>";
				
	while($d= mysql_fetch_object($rs) ){
		$tabla.="<tr>
					<td>$d->usuario</td>
					<td>$d->fecha_ult_acceso</td>
				 </tr>";
	}
	
	$tabla.="</tbody></table>";
	
	return $tabla;
}


?>
