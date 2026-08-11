<?
include('../../Config/Conectar.inc');
#echo "os:".INST_NAME;

$id_usuario = $_SESSION['iduser'];


//Ingreso del nuevo pedido
if(isset($_POST['submit'])){
					
	$query = "INSERT INTO ppdev.medicacion_alto_costo(id_afiliado,id_autorizacion,fecha_pedido,tipo,importe,fecha_cotizacion,detalle,id_usuario)
					VALUES ($id_afiliado,'$id_autorizacion','$fecha_pedido','$tipo','$importe','$fecha_cotizacion','$detalle',$id_usuario)";
					
	mysql_query($query) or die(mysql_error().$query);
	
	echo "<script>
			alert('Ingresado con exito');
			window.location.href = 'index.php';
		</script>";
		
	//header("Location: pedido_medicacion.php?id_pedido=$id_pedido"); 
	exit();	
	
}

$fecha_actual = date("Y-m-d");
$primer_dia_del_anio = date("Y-01-01", strtotime($fecha_actual));
$ultimo_dia_del_anio = date("Y-12-01", strtotime($fecha_actual));

?>

<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
		<!-- Jquery -->
		<script src="jquery.min.js"></script>
		
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

		<!-- Select clase -->
		<link href="http://34.123.90.171/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
		<script src="http://34.123.90.171/framework/bootstrap/select/select2.min.js"></script>

		<style>
			#dni_resultado{				
		
			    background-color: black;
			    color: gray;
			    padding: 7px;
			    border-radius: 8px;
			    margin: 5px;
			}
			#tabListado{
				font-size: 11px;
			}
			.btn-refresh{
				position: fixed;
				bottom:2%; 
				left:2%;
				z-index: 1;
			}
		</style>
	</head>
	<body>
		<!-- Cuerpo del formulario -->
		<div class="container-fluid">
			<button class="btn btn-info btn-refresh">
				<i class="fas fa-sync-alt fa-2x"></i>
			</button>
			<div class="col-md-12">
				<div class="col-md-10">
					<div class="x_panel">
						<div class="tituloDiv">
							Agenda de procesamiento de archivos
						</div>
						<div class="row" style="padding: 15px;">
							<!--
							<a class="btn btn-sm btn-danger" data-toggle='modal' data-target='#modalNuevoIngreso'>
								<i class="fas fa-plus-circle fa-sm"></i> Nueva solicitud
							</a>
							-->
							<div class="row">
								<a class="btn btn-sm btn-warning" id="btnFiltros" style="margin-left:20px;" data-toggle='modal' data-target='#modalFiltros'>
								<i class="fas fa-filter fa-sm"></i> Filtros 
								</a>
								<a class="btn btn-sm btn-success" id="btnExcel">
									<i class="fas fa-download fa-sm"></i> Excel
								</a>

								<!-- Revision opciones Regimen general -->
								<a class="btn btn-sm btn-info"  style="margin-left:20px;" href="opciones_rg_revision/" target="_blank" title="Importador/Procesador de archivos">
									Opciones RG <span style="background-color: forestgreen; padding: 3px; margin-left: 5px;">ALTAS</span> 
								</a>				
								<a class="btn btn-sm btn-info" href="opciones_rg_revision_bajas/" target="_blank" title="Importador/Procesador de archivos">
									Opciones RG <span style="background-color: red; padding: 3px; margin-left: 5px;">BAJAS</span>  
								</a>
								<!-- FIN  Revision opciones Regimen general -->

								<!-- Revision opciones Monotributo/Domestico  -->
								<a class="btn btn-sm btn-primary" style="margin-left:20px;" href="opciones_mt_revision_altas/" target="_blank" title="Importador/Procesador de archivos">
									Opciones MT <span style="background-color: forestgreen; padding: 3px; margin-left: 5px;">ALTAS</span> 
								</a>
								<a class="btn btn-sm btn-primary"  href="opciones_mt_revision_bajas/" target="_blank" title="Importador/Procesador de archivos">
									Opciones MT <span style="background-color: red; padding: 3px; margin-left: 5px;">BAJAS</span> 
								</a>
								<!-- FIN - Revision opciones Monotributo/Domestico  -->
							</div>
							<hr>
							<div>
								<a href="../importar-zips" class="btn btn-danger" target="_blank" title="Unica version para importar el PADRON SSS">
									Importar Padron SSS
								</a>
							</div>
							<hr/>
							<div id="divListado">
								
							</div>
							
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="x_panel" style="display: none;">
						<div class="tituloDiv">
							Total pedidos por periodo
						</div>
						<div class="row" style="padding: 10px;">
							<table id="tabTotPeriodo" class="table" style="width: 90%;">
								<thead>
									<tr>
										<th>Periodo</th>
										<th style="text-align: right;">Cantidad</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<div class="x_panel" style="display: none;">
						<div class="tituloDiv">
							Cantidad por <b>tipo</b> del periodo <span id="s_periodo_tipo">actual</span>
						</div>
						<div class="row" style="padding: 10px;">
							<table id="tabTotTipoPeriodo" class="table" style="width: 90%;">
								<thead>
									<tr>
										<th>Periodo</th>
										<th style="text-align: right;">Cantidad</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
		</div>
		
		<!-- Modal Ingreso -->
		<div id="modalNuevoIngreso" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
		
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Nueva solicitud de medicacion</h4>
		      </div>
		      <div class="modal-body">
		   		<form method="post">	
		   			<div class="col-md-12">
		   				<div class="col-md-6">
			   				<div class="form-group">
						   		<label for="date">Buscar afiliado por DNI</label>
						   		<input type="text" class="form-control" name="dni" placeholder="88999777" required>
						   		<br>
						   		<span id="dni_resultado" style="visibility: hidden;"></span>
						   		<input type="hidden" id="id_afiliado" name="id_afiliado" >			   		
						  	</div>
			   			</div>
			   			<div class="col-md-6">
							<div class="form-group">
						 		<label for="date"># de orden</label>
						    	<input type="text" class="form-control" name="id_autorizacion" >				    
						 	</div>
						</div>
		   			</div>
		   			
					<div class="col-md-12">
		   				<div class="col-md-6">
		   					<div class="form-group">
							    <label for="pwd">Tipo</label>
							    <select name="tipo" required>
							    	<option value="">Seleccione</option>
							    	<option value="dbt">DBT</option>
							    	<option value="oncologico">Oncologico</option>
							    	<option value="hiv">HIV</option>
							    	<option value="otros">Otros</option>
							    </select>
							  </div>
		   				</div>
		   				<div class="col-md-6">
			   				<div class="form-group">
						 		<label for="date">Fecha pedido</label>
						    	<input type="date" class="form-control" name="fecha_pedido" value="<?=date("Y-m-d");?>" >				    
						 	</div>
			   			</div>
		   				
		   			</div>
		   			
		   			<div class="col-md-12">
		   				<div class="col-md-6">		   					
	   						<div class="form-group">
							    <label for="email">Importe</label>
							    <input type="number" class="form-control" name="importe" step="0.01" value="0.00" style='text-align: right;'>
							</div>			   				
		   				</div>
		   				<div class="col-md-6">
		   					<div class="form-group">
						 		<label for="date">Fecha cotizacion</label>
						    	<input type="date" class="form-control" name="fecha_cotizacion" value="<?=date("Y-m-d");?>" >				    
						 	</div>
		   				</div>
		   			</div>
		   			
		   			<div class="col-md-12">
		   				<div class="form-group">
						    <label for="email">Detalle:</label>
						    <textarea name="detalle" class="form-control" rows="3"></textarea>
						</div>
		   			</div>
		   			
		   			
				  <hr />
				  <input type="submit" name="submit" value = "Procesar" style="display: none;">
					<a id="btnEnviar" class="btn btn-success"  onclick="javascript:return confirm('Confirma ?')">
						<span id="spanEnviar"></span>  Grabar 
					</a>
				</form>
  
		      </div>
		      
		    </div>
		
		  </div>
		</div>

		
		<!-- Modal Filtros -->
		<div id="modalFiltros" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Filtros</h4>
		      </div>
		      <div class="modal-body">
		        <div>
		        	<div class="form-group">
						<label for="email">Fecha desde</label>
						<input type="date" class="form-control" id="fdesde">
					</div>
					<div class="form-group">
						<label for="email">Fecha hasta</label>
						<input type="date" class="form-control" id="fhasta">
					</div>
					<div class="form-group">
						<label >Archivo</label>
						<select id="tipo_archivo" style="width: 100%;">
							<option value="todas">Todas</option>
						</select>
					</div>
					<div class="form-group">
						<label >Orden de fecha</label>
						<select id="lst_orden" style="width: 100%;">
							<option value="o_asc">Ascendente</option>
							<option value="o_desc" selected>Descendente</option>
						</select>
					</div>
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        <button type="button" class="btn btn-success" data-dismiss="modal" id="btnAplicar">Aplicar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal Notas -->		
		<div id="modalNotas" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Nota</h4>
		      </div>
		      <div class="modal-body">
		        <p id="p_notas"></p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>	

		<script>	

			$(function(){

				setTimeout(function(){ 
					$('select').addClass("select2");	
					$('.select2').select2();
				}, 3000);

				// Selects
				// Tipos de archivo
				$.getJSON('ajax.php',
							{ parametro: "lst_archivos" },						       				
							function(data){ 
								
								$.each(data, function (key, item) {
					                $("#tipo_archivo").append("<option value="+item.proceso+">"+item.proceso+"</option>");
					            });
							}//fin function data

				);//fin getjson

									

				//Info inicial  
				resumen_por_periodos();
				//resumen_tipo_periodo('0');

				var fecha_actual = new Date();

				var mes_pasado = MesPasado(fecha_actual);
				//alert(mes_pasado);
				//console.log(mes_pasado);

				var ultimo_dia = UltimoDia(fecha_actual);
				
				//alert(ultimo_dia);
				//console.log(ultimo_dia);

				//llena_tabla($("#fdesde").val(), $("#fhasta").val(), 'todas','o_desc');

				llena_tabla(mes_pasado,ultimo_dia,'todas','o_desc');



				

				
				
				/********************************** Eventos ************************************************/

				$(".btn-refresh").on('click',function(){
					$("#btnAplicar").click();
				})

				//btnFiltros
				$("#btnFiltros").on('click',function(){
					var fdesde = $("#fdesde").val();
					var fhasta = $("#fhasta").val();

					console.log('<? echo date('Y-01-01');?>')

					if(fdesde=="" && fhasta==""){

						$("#fdesde").val("<?=$primer_dia_del_anio;?>")
						$("#fhasta").val("<?=$ultimo_dia_del_anio;?>")
					}

				})

				$("#btnAplicar").on('click',function(){

					var fdesde = $("#fdesde").val();
					var fhasta = $("#fhasta").val();

					var fecha_actual = new Date();
					if(!fdesde){
						fdesde = MesPasado(fecha_actual);
					}
					if(!fhasta){
						fhasta = UltimoDia(fecha_actual);
					}
					
					var tipo_archivo = $("#tipo_archivo").val();
					var lst_orden = $("#lst_orden").val();

					llena_tabla(fdesde, fhasta, tipo_archivo, lst_orden);

				})
				

				$("#btnExcel").on('click',function(){

					var fdesde = $("#fdesde").val();
					var fhasta = $("#fhasta").val();
					var url = "ajax.php?parametro=lst_excel&fdesde="+fdesde+"&fhasta="+fhasta ;

					abrirEnPestana(url);

				})
				
				
				$('#btnEnviar').on('click',function(){
					
					$(this).attr('disabled','disabled');
					$('#btnEnviar').html('');					
					$('#btnEnviar').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
					$('input[name=submit]').click();
										
				})

				$('#tabTotPeriodo').on('click','.tt_periodo',function(){

					var periodo = $(this).data('p_periodo');

					//console.log(periodo);
					resumen_tipo_periodo(periodo);

				})

				$(document).on('click','.exportar_ftp',function(e){
					e.preventDefault();
					let tipo = $(this).data('tipo');
					let id = $(this).data('id'); 
					if(tipo === "padron_sss"){
						$.ajax({
							url: 'ajax.php',
							type: 'GET',
							dataType: 'json',
							data: {parametro: 'exportar_ftp_padron_sss', id_lote: id},
						})
						.done(function(data) {
							let {status , periodo} = data;
							if(status === "ok"){
								alert('Exportado 1/2');
								$.ajax({
									url: 'ajax.php',
									type: 'GET',
									dataType: 'ok',
									data: {parametro: 'exportar_ftp_padron_sss_2',periodo: periodo},
								})
								.done(function(data) {
									console.log("success");
									alert('Exportado 2/2');
								});
								
							}
							else{
								console.log(data);
							}
						});
					}else{
						alert('No se puede exportar.');
					}
				})
				
				$(document).on('click','.descargar_padron_sss',function(e){

					var id_lote = $(this).data('id');
					var periodo = $(this).data('periodo');
					var url = `ajax.php?parametro=descargar_padron_sss&id_lote=${id_lote}&periodo=${periodo}`;

					abrirEnPestana(url);
				})
				$(document).on('click','.descargar_padron_sss_sin_proximas_altas',function(e){

					var id_lote = $(this).data('id');
					var periodo = $(this).data('periodo');
					var url = `ajax.php?parametro=descargar_padron_sss_sin_proximas_altas&id_lote=${id_lote}&periodo=${periodo}`;

					abrirEnPestana(url);
				})

			})// FIN de Jquery

			function llena_tabla(fdesde, fhasta, tipo_archivo, lst_orden){

				
				$("#divListado").html("");				

				$("#divListado").html("<table id='tabListado' class='table' >"
											+"<thead>"
												+"<tr>"
													+"<th>#</th>"
													+"<th></th>"
													+"<th>Clave</th>"
													+"<th>Periodo</th>"
													+"<th>de donde sale</th>"
													+"<th>fecha inicio</th>"
													+"<th>fecha limite</th>"
													+"<th style='text-align: center;'>Estado</th>"
													+"<th>lote</th>"
													+"<th>cantidad de registros</th>"													
													+"<th>Fecha procesado</th>"
													+"<th>Usuario</th>"													
												+"</tr>"
											+"</thead>"
											+"<tbody>"
											+"</tbody>"
										+"</table>");

				$("#tabListado tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Cargando...');

				var datos = {
					"parametro": "listado", 
					"fdesde": fdesde, 
					"fhasta": fhasta, 
					"tipo_archivo": tipo_archivo, 
					"lst_orden": lst_orden
				};
				console.log(datos)
				$.getJSON('ajax.php',
							datos,									       				
							function(data){ 

								console.log(data);
								$("#tabListado tbody").html("");

								var title_estado = icono_estado = li_anulacion = li_procesar = li_exportar = li_descargar_padron_sss = li_descargar_padron_sss_sin_proximas_altas = "";
								var periodo='';
								for(var i=0; i<=data.length-1 ;i++){

									li_descargar_padron_sss = li_descargar_padron_sss_sin_proximas_altas = "";

									let {tipo} = data[i];
									//console.log(data[i]["estado_hoy"]);
									var periodo = data[i]['clave'].substring(data[i]['clave'].length-10,data[i]['clave'].length);
									switch(data[i]["estado_hoy"]){
										//Importado
										case "1":
												icono_estado = "<i class='fas fa-check-circle' style='color: green; font-size: 20px;'></i>";
												title_estado = " title='Archivo fue importado' ";

												li_anulacion = "";

												li_procesar = "<li><a href='"+data[i]['link']+"?periodo="+periodo+"' target='_blank'>Procesar archivo</a></li>";

												li_exportar = "<li><a class='exportar_ftp' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id']+">Exportar al FTP</a></li>";

												if(tipo == "padron_sss"){

													li_descargar_padron_sss = "<li><a class='descargar_padron_sss' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id_lote']+" data-periodo="+periodo+">Descargar Padron SSS</a></li>";

													li_descargar_padron_sss_sin_proximas_altas = "<li><a class='descargar_padron_sss_sin_proximas_altas' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id_lote']+" data-periodo="+periodo+">Descargar Padron SSS Sin Altas Futuras</a></li>";
												}
											break;
										//Inexistente
										case "2": 
												icono_estado = "<i class='fas fa-exclamation-circle' style='color: orange; font-size: 20px;'></i>";
												title_estado = " title='Marcado como inexistente' ";

												li_anulacion = "";

												li_procesar = "";
											break;
										//Pendiente	
										case "3":
												icono_estado = "";
												title_estado = " title='Archivo pendiente' ";

												li_anulacion = "<li><a class='btnInexistente' data-id_item='"+data[i]['id']+"'>Marcar como archivo inexistente</a></li>";

												li_procesar = "<li><a href='"+data[i]['link']+"?periodo="+periodo+"' target='_blank'>Procesar archivo</a></li>";

												li_exportar = "<li><a class='exportar_ftp' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id']+">Exportar al FTP</a></li>";

											break;

										//Procesado
										case "4":
												icono_estado = "<i class='fas fa-check-circle' style='color:deepskyblue; font-size:20px;' ></i>";
												title_estado = " title='Archivo fue procesado'";

												li_anulacion = "";

												li_procesar = "";

												li_exportar = "<li><a class='exportar_ftp' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id']+">Exportar al FTP</a></li>";

												if(tipo == "padron_sss"){

													li_descargar_padron_sss = "<li><a class='descargar_padron_sss' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id_lote']+" data-periodo="+periodo+">Descargar Padron SSS</a></li>";

													li_descargar_padron_sss_sin_proximas_altas = "<li><a class='descargar_padron_sss_sin_proximas_altas' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id_lote']+" data-periodo="+periodo+">Descargar Padron SSS Sin Altas Futuras</a></li>";
												}
											break;
										default:
												icono_estado = "";
												title_estado = " title='Archivo pendiente' ";

												li_anulacion = "<li><a class='btnInexistente' data-id_item='"+data[i]['id']+"'>Marcar como archivo inexistente</a></li>";

												li_procesar = "<li><a href='"+data[i]['link']+"?periodo="+periodo+"' target='_blank'>Procesar archivo</a></li>";
											break;

									}
									
									
									//console.log(periodo);

									$("#tabListado tbody").append("<tr >"
																		+"<td>"+(i+1)+"</td>"	
																		+"<td>"
																			+"<div class='btn-group btn-group-default' >"						                    
																				+"<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>"
																					+"<i class='fa fa-ellipsis-v' aria-hidden='true'></i>"
																				+"</button>"
																				+"<ul class='dropdown-menu'>"

																					+li_anulacion
																					 
																					 +"<li>"
																						+"<a class='btn-tipo' data-tipo='"+data[i]['tipo']+"' data-toggle='modal' data-target='#modalNotas' >"
																							+"Nota"
																						+"</a>"						                     	
																					 +"</li>"
																					 +"<li>"
																						+"<a href='../genera_novedades/importar_comparativa_padronsss_padronActual.php?id_lote="+data[i]['id_lote']+"' target='_blank' >"
																							+"Comparativa Padrones"
																						+"</a>"						                     	
																					 +"</li>"													

																					 +li_procesar
																					 +li_descargar_padron_sss
																					 +li_descargar_padron_sss_sin_proximas_altas
																					 +"<li><a class='exportar_ftp' data-tipo="+data[i]['tipo']+" data-id="+data[i]['id_lote']+">Exportar al FTP</a></li>"
																					 + (data[i]['tipo'] === 'desempleo' ? "<li><a href='desempleo/listado_desempleo.php?id_lote="+data[i]['id_lote']+"' target='_blank'>Detalle de desempleo</a></li>" : "")



																				+"</ul>"
																			+"</div>"	
																		+"</td>"													
																		+"<td>"+data[i]['clave']+"</td>"
																		+"<td>"+data[i]['fecha_clave']+"</td>"
																		+"<td>"+data[i]['de_donde_sale']+"</td>"
																		+"<td>"+data[i]['fecha_inicio']+"</td>"
																		+"<td>"+data[i]['fecha_limite']+"</td>"
																		+"<td style='text-align: center;' "+title_estado+">"+icono_estado+"</td>"
																		+"<td>"+data[i]['id_lote']+"</td>"
																		+"<td>"+data[i]['cant_registros']+"</td>"
																		+"<td>"+data[i]['fecha_proceso']+"</td>"
																		+"<td>"+data[i]['usuario']+"</td>"						      				
																	+"</tr>") ;		
								}	

								$("#tabListado").dataTable({			    	
										"bPaginate": true,
										"iDisplayLength": 100,
										"bLengthChange": false,
										"bFilter": true,
										
										"bInfo": false,
										
										"bAutoWidth": false,
										"language": {				    
										    "search": "Buscar",
										    "paginate": {
											      "previous": "Anterior",
											      "next": "Proximo"
											}
									    }
								});


								/*********************************************************************/
								//Estos eventos estan aca porque sino al recargar la tabla no toma el evento
								$('#tabListado').on('click','.btnInexistente',function(){

									var r = confirm("Seguro ???"); 

									if (r == true) { 
										
										var id_item = $(this).data('id_item');
										//console.log(id_item);

										var datos = {
											"parametro": "marcar_inexistente",
											"id_item": id_item
										};

										$.ajax({

											url: 'ajax.php',
											type: 'get',
											data: datos,
											success: function(data){						
												
												if(data=="ok"){

													alert('Se modifico correctamente!');

													var fdesde = $("#fdesde").val();
													var fhasta = $("#fhasta").val();
													var tipo_archivo = $("#tipo_archivo").val();
													var lst_orden = $("#lst_orden").val();

													llena_tabla(fdesde, fhasta, tipo_archivo, lst_orden);

												}
												else{
													alert('Ocurrio un ERROR');
													console.log(data);
												}
											}
										})

									}
									else { 
										return false;
									}

									

								})

								$('#tabListado').on('click','.btn-tipo',function(){

									var tipo = $(this).data('tipo');
									console.log(tipo)
									$('#p_notas').html("");
									$('#p_notas').html("Cargando...");
									
									var datos = {
										"parametro": "mostrar_nota",
										"tipo": tipo
									};

									$.ajax({

										url: 'ajax.php',
										type: 'get',
										data: datos,
										success: function(data){						
											
											//console.log(data);
											$('#p_notas').html("");
											$('#p_notas').html(data);
										}
									})

								})

							}//fin function data

				);//fin getjson

				setTimeout(function(){ 

					

				}, 2000);

			}// FIN - Funcion llena_tabla

			function resumen_por_periodos(){

				$('#tabTotPeriodo tbody').html("");
				$('#tabTotPeriodo tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Cargando...');

				$.getJSON('ajax.php',
							{ parametro: "total_periodo" },						       				
							function(data){ 
								
								$('#tabTotPeriodo tbody').html("");

								for(var i=0; i<=data.length-1 ;i++){
								
									$("#tabTotPeriodo tbody").append("<tr>"																
																		+"<td class='tt_periodo' data-p_periodo='"+data[i]['periodo']+"'>"+data[i]['periodo']+"</td>"
																		+"<td style='text-align: right;'>"+data[i]['cantidad']+"</td>"
																		
																	+"</tr>") ;		
								}	
							}//fin function data

				);//fin getjson

			}// FIN resumen_por_periodos

			function resumen_tipo_periodo(periodo){

				$('#tabTotTipoPeriodo tbody').html("");
				$('#tabTotTipoPeriodo tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Cargando...');

				$.getJSON('ajax.php',
							{ parametro: "total_tipo_periodo", p_periodo: periodo },						       				
							function(data){ 
								
								$('#tabTotTipoPeriodo tbody').html("");

								for(var i=0; i<=data.length-1 ;i++){
								
									$("#tabTotTipoPeriodo tbody").append("<tr>"																
																			+"<td>"+data[i]['tipo']+"</td>"
																			+"<td style='text-align: right;'>"+data[i]['cantidad']+"</td>"
																			
																		+"</tr>") ;		
								}	
							}//fin function data

				);//fin getjson

			}// FIN resumen_tipo_periodo

			function abrirEnPestana(url) {
				var a = document.createElement("a");
				a.target = "_blank";
				a.href = url;
				a.click();
			}
			function MesPasado(yourDate) {

				var day = yourDate.getDate();
				var month = yourDate.getMonth();
				var year = yourDate.getFullYear();


				if(month < 10){
					var fecha = year+"-0"+month+"-"+"01";
				  	//console.log(`${day}-0${month}-${year}`)
				}else{
					var fecha = year+"-"+month+"-"+"01";
				  	//console.log(`${day}-${month}-${year}`)
				}

			    //return new Date(yourDate.getYear(), yourDate.getMonth() - 1, 1);
			    return fecha;
			}

			function UltimoDia(yourDate){
				var lastDayOfMonth = new Date(yourDate.getFullYear(), yourDate.getMonth()+1, 0);

				var day = lastDayOfMonth.getDate();
				var month = lastDayOfMonth.getMonth()+1;
				var year = lastDayOfMonth.getFullYear();

				if(month < 10){
					var fecha = year+"-0"+month+"-"+day;
				  	//console.log(`${day}-0${month}-${year}`)
				}else{
					var fecha = year+"-"+month+"-"+day;
				  	//console.log(`${day}-${month}-${year}`)
				}
				return fecha;
			}


		</script>
	</body>
</html>

<?php

function tipo_pedido($tipo_actual){
	
	$tipos = array('dbt','oncologico','hiv','otros');
	
	foreach ($tipos as $tipo) {
		
		if($tipo==$tipo_actual){
			echo "<option value='$tipo' selected>$tipo</option>";	
		}
		else{
			echo "<option value='$tipo'>$tipo</option>";
		}
	}
	
}

?>