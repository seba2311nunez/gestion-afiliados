<?php
include('../../Config/Conectar.inc');
mysql_select_db('$base_padron', $conexion);
$id_usuario = $_SESSION["id_user"];
$root = $_SERVER['DOCUMENT_ROOT'];


mysql_query("SET NAMES 'utf8'");
header('Content-Type:text/html; charset=UTF-8');
?>

<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

		<!-- Databatables -->
		<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
		
		<!-- DataTables core -->
		<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
		<link  href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">

		<!-- DataTables Buttons extension -->
		<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

		<!-- Buttons CSS -->
		<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">


		<!-- PNotify -->
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.js"></script>
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.js"></script>
		<script src="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.js"></script>
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
		<link href="http://34.123.90.171/dashboard_sistema/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
		<style>
			#divListado{
				margin: 10px;
			}
			#divCabecera{
				background-color: #616161;
				color: white;
				font-size: 20px;
				padding: 10px;
				text-align: center;
			}
			#divComentarios{
				background-color: #31b0d5;
				color: white;
				padding: 10px;
				border-radius: 10px;
			}
			.table tbody tr > td.success {
			  background-color: #dff0d8 !important;
			}
			.p-15px {
				padding: 15px;
			}
			.table-condensed{
				font-size:9px !important;
			}
			.periodo_proceso{
				background: springgreen;
			}
			.movimiento-error{
				background: wheat !important;
			}
			.dataTables_length {
			  clear: both;
			  margin-top: 1rem;
			}
			#divListFctPrestacion {
			    overflow-x: auto;
			    max-width: 100%;
			}
			#modalListaFct  {
			    overflow-x: auto;
			}

		</style>
		<title>Novedades</title>
	</head>
	<body>
		<div id="divCabecera" class="container">
			Novedades | Envio mensual
		</div>
		<br>
		<div id="divListado">
			<a  class="btn btn-success " id="btnNuevoPeriodoProcesar" data-toggle='modal' data-target='#modalNuevoPeriodo'>
				Agregar nuevo periodo de presentacion
			</a>
			<a class="btn btn-info" href="https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/cronograma_ftp.pdf" target="_blank">
				Cronograma FTP
			</a>
			<a class="btn btn-warning" href="https://seguro.sssalud.gob.ar/descargas/rnos/restricto/padron/InstructivoObrasSociales.pdf" target="_blank">
				Instructivo de novedades | Acciones a seguir con cada código de error a partir de Pág. 16 
			</a>
			<br>
			<br><br>
			<table id="tabListado" class="table" style="margin: 50px; width: 90%;">
				<thead>
					<tr class="success">
						<th>Lote</th>
						<th></th>						
						<th>Periodo</th>
						<th>Vencimiento</th>
						<th>Estado</th>
						<th style="text-align: right;">Afiliados</th>
						<th style="text-align: right;">Error FTP</th>
						<th style="text-align: right;">Aceptadas</th>
						<th style='text-align: right;'>Rechazadas</th>					
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<br>
			<div style="text-align: center;">
			</div>
		</div>

		<!-- Button trigger modal -->
		<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modalAceptados' style="display: none;">
		  Launch demo modal
		</button>

		<!-- Modal Nuevo periodo -->
		<div id="modalNuevoPeriodo" class="modal fade " role="dialog">
		  <div class="modal-dialog " >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Nuevo periodo presentacion </h4>
		        
		      </div>
		      <div class="modal-body">
		        
		      	<div class="form-group">
				    <label for="periodo">Periodo:</label>
				    <select name="periodo" id="periodo" class="form-control">	
				    	<?php
				    		$sql = "SELECT * 
				    					FROM prueba.periodos 
										WHERE CURDATE() BETWEEN primer_dia AND ultimo_dia 
											OR DATE_ADD(CURDATE(),INTERVAL -1 MONTH) BETWEEN primer_dia AND ultimo_dia";
							$rs = mysql_query($sql);
							while($d=mysql_fetch_object($rs)){

								echo "<option value='$d->primer_dia'>$d->periodo1</option>";

							}
				    	?>			    	
				    </select>
				</div>
				<div class="form-group">
				    <label for="periodo">Fecha cierre:</label>
				    <input type="date" id="fcierre" value="<?=date('Y-m-d');?>" class="form-control">
				</div>
				<hr>
				<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
				<a id="btnNuevoPeriodoProcesar" class="btn btn-success" onchange="ValidateSingleInput(this)" >
					<span id="spanNuevoPeriodoProcesar"></span>Generar
				</a>

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal Novedades aceptados -->
		<div id="modalAceptados" class="modal fade " role="dialog">
		  <div class="modal-dialog " >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Subir archivo novedades aceptadas</h4>
		        
		      </div>
		      <div class="modal-body">
		        <form method="post" name="form1" id="form1" >	
					<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="200000000">
					<input type="hidden" name="parametro" id="parametro">
					<input type="hidden" name="nombre" id="nombre">
					<input type="hidden" name="extension" id="extension">
					<input type="hidden" name="pa_periodo" id="pa_periodo">
					<table class="table">						
						<tr>
							<td>
								<label>Seleccionar archivo</label>
								<input type="file" name="file_aceptados" id="file_aceptados" required class="inp-sm">
							</td>											
						</tr>						
					</table> 							
					<hr>
					<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
					<a id="btnProcesarAceptados" class="btn btn-success" onchange="ValidateSingleInput(this)" >
						<span id="spanEnviarAceptados"></span>Procesar
					</a>
				</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal Novedades Errores -->
		<div id="modalErrores" class="modal fade " role="dialog">
		  <div class="modal-dialog modal-lg" >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Gestion de Errores | Periodo: <strong id="errores_periodo"></strong> | ID LOTE: <strong id="errores_id_lote"></strong></h4>
		        
		      </div>
		      <div class="modal-body">
		      	<div class="row">
		      		<div class="col-md-12">
				        <div class="row p-15px">
				        	<form method="post" name="form3" id="form3" >	
										<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="200000000">
										<input type="hidden" name="parametro" id="parametro">
										<input type="hidden" name="nombre" id="nombre">
										<input type="hidden" name="extension" id="extension">
										<input type="hidden" name="pe_periodo" id="pe_periodo">

										<label>Seleccionar archivo</label>
										<input type="file" name="file_errores" id="file_errores" required class="inp-sm">
										<br>
										<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
										<a id="btnProcesarErrores" class="btn btn-success" onchange="ValidateSingleInput(this)" >
											<span id="spanEnviarErrores"></span>Procesar
										</a>
									</form>
				        </div>
		      			<div class="row p-15px">
		      				<table id="tabla_errores_presentaciones" class="table table-stripped">
		      					<thead>
		      						<tr>
		      							<th>ID</th>
		      							<th>Fecha Carga</th>
		      							<th>C. Errores</th>
		      							<th></th>
		      						</tr>
		      					</thead>
		      					<tbody></tbody>
		      				</table>
		      			</div>
		      		</div>
		      	</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal Errores / Presentacion -->
		<div id="modalPresentacionErrores" class="modal fade " role="dialog">
		  <div class="modal-dialog modal-lg" >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		      </div>
		      <div class="modal-body">
		      	<div class="row">
		      		<div class="col-md-12">
		      			<div class="row p-15px">
		      				<table id="tabla_codigos" class="table table-stripped">
		      					<thead>
		      						<tr>
		      							<th>Codigo</th>
		      							<th>Cantidad</th>
		      						</tr>
		      					</thead>
		      					<tbody></tbody>
		      				</table>
		      			</div>
		      		</div>
		      	</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>



		<!-- Modal Novedades rechazados -->
		<div id="modalRechazados" class="modal fade " role="dialog">
		  <div class="modal-dialog " >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Subir archivo novedades rechazadas</h4>
		        
		      </div>
		      <div class="modal-body">
		        <form method="post" name="form1" id="form2" >	
					<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="200000000">
					<input type="hidden" name="parametro" id="parametro">
					<input type="hidden" name="nombre" id="nombre">
					<input type="hidden" name="extension" id="extension">
					<input type="hidden" name="pr_periodo" id="pr_periodo">
					<table class="table">						
						<tr>
							<td>
								<label>Seleccionar archivo</label>
								<input type="file" name="file_rechazados" id="file_rechazados" required class="inp-sm">
							</td>											
						</tr>						
					</table> 							
					<hr>
					<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
					<a id="btnProcesarRechazados" class="btn btn-success" onchange="ValidateSingleInput(this)" >
						<span id="spanEnviarRechazados"></span>Procesar
					</a>
				</form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal ListFctPrestacion -->
		<div id="modalListaFct" class="modal fade " role="dialog">
		  <div class="modal-dialog modal-lg" style="width: 1300px;">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <div class="row">
		        	<div class="col-md-6">
				        <h4 class="modal-title">
				        	Afiliados de la presentacion actual 
				        	<button class="btn btn-primary btn-sm" data-toggle="collapse" href="#collapseExample" role="button" aria-controls="collapseExample">
				        		Ver Cantidades
				        	</button>
				        </h4>
				        <p>Periodo: <b><span id="s_periodo"></span></b></p>		        		
		        	</div>
		        	<div class="col-md-6">
								<div class="collapse" id="collapseExample">
						    		<table id="tabTotal_x_desreguladora" class="table" style="width: 300px;">
						    			<thead>
						    				<tr>
													<th>Gerenciadora</th>
													<th>Total</th>
												</tr>	
						    			</thead>
										<tbody></tbody>									
									</table>
					    	</div> 
		        	</div>
		        </div>
		      </div>
		      <div class="modal-body">
		      	<div id="resumen-errores" class="row" style="display: none; margin: 10px 0;"></div>
		      	<div class="row" style="padding-left: 10px; padding-bottom: 10px;">
		      		<input type="hidden" id="id_lote">
		      		<a id="btnExportarExcel" target="_blank" class="btn btn-success">
					    <i class="fa fa-file-excel-o"></i> Exportar todo a Excel
					</a>

		      	</div>
		      	<div class="row" id="resumen-filtros">
				  <div class="col-md-6">
				    <h5><strong>Resumen por Tipo Beneficiario (TBT):</strong></h5>
				    <div id="resumen-tbt" class="btn-group" role="group"></div>
				  </div>
				  <div class="col-md-6">
				    <h5><strong>Resumen por Código de Error:</strong></h5>
				    <div id="resumen-errores" class="btn-group" role="group"></div>
				  </div>
				</div>

		        <div id="divListFctPrestacion" class="table-responsive">
		        	
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal Editar Vencimiento -->
		<div id="modalEditarVencimiento" class="modal fade " role="dialog">
		  <div class="modal-dialog" style="width: 1100px;">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Editar Vencimiento</h4>
	        </div>
		      <div class="modal-body">
		        <div class="row">
		        	<span>Fecha Vencimiento</span>
		        	<input type="date" id="EditarVencimiento-fecha" class="form-control" />
		        	<input type="hidden" id="EditarVencimiento-id-lote">
		        	<br>
		        	<button class="btn btn-success" id="EditarVencimiento-Guardar">Guardar</button>
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<!-- Modal ListFctPrestacion -->
		<div id="modalCronologia" class="modal fade " role="dialog">
		  <div class="modal-dialog " >

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Cronologia </h4>
		        Afiliado: <b><span id="s_afil_crono"></span></b>
		      </div>
		      <div class="modal-body">
		        <div id="divListCronologia">
		        	
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>

		  </div>
		</div>

		<script type="text/javascript">
			let inst_rnos = "<?php echo INST_RNOS; ?>";
			let INST_SUPERVISA_NOVEDADES = "<?php echo INST_SUPERVISA_NOVEDADES; ?>";
			

			$(document).ready(function(){

				let tablaAfiliados; // debe estar al inicio de tu <script>
				ListarLotes();
				
				function genera_tabla(){

					$('#divListFctPrestacion').html("<table id='tabListaAfilPrestacion' class='table table-condensed'>"
										        		+"<thead>"
										        			+"<tr>"
										        				+"<th>#</th>"
										        				+"<th></th>"
										        				//+"<th>CUIT</th>"
										        				+"<th>Gerenciadora</th>"
										        				+"<th>CUIL Titular</th>"
										        				+"<th>Parentesco</th>"
										        				+"<th>CUIL</th>"
										        				+"<th>DNI</th>"
										        				+"<th>Ayn</th>"
										        				+"<th>Sexo</th>"
										        				+"<th>Edad</th>"
										        				+"<th>Fecha nacimiento</th>"
										        				+"<th>Incapacidad</th>"
										        				+"<th>Tipo beneficiario</th>"
										        				+"<th>Tipo Movimiento</th>"
										        				+"<th>Fecha Movimiento</th>"
										        				+"<th>Codigo ERROR</th>"
										        				+"<th>Codigo RECHAZO</th>"
										        			+"</tr>"
										        		+"</thead>"
										        		+"<tbody></tbody>"
										        	+"</table>"
														);

				}
				function llena_tabla(id_lote, inst_rnos) {
				    //const parametro = (inst_rnos == "128706") ? "lst_afiliados_presentacion_ssp" : "lst_afiliados_presentacion";
				    const parametro = "lst_afiliados_presentacion_ssp";
				    if ($.fn.DataTable.isDataTable('#tabListaAfilPrestacion')) {
				        $('#tabListaAfilPrestacion').DataTable().destroy();
				    }

				    //$('#tabListaAfilPrestacion tbody').html(""); // Asegurar tabla vacía

				    tablaAfiliados = $('#tabListaAfilPrestacion').DataTable({
				        serverSide: true,
				        processing: true,
				        ajax: {
				            url: 'ajax.php',
				            type: 'GET',
				            data: {
				                parametro: parametro,
				                id_lote: id_lote
				            }
				        },
				        dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
				        buttons: [
				            {
				                extend: 'excelHtml5',
				                text: '<i class="fa fa-file-excel-o"></i> Exportar visualización a Excel',
				                titleAttr: 'Exportar a Excel lo que se ve en la tabla',
				                className: 'btn btn-success',
				                exportOptions: {
				                    columns: ':visible'
				                }
				            }
				        ],
				        lengthMenu: [[10, 50, 100, 200, 500, 1000, 2000], [10, 50, 100, 200, 500, 1000, 2000]],
				        pageLength: 10,
				        language: {
				            processing: '<i class="fa fa-spinner fa-spin"></i> Cargando...',
				            lengthMenu: "Mostrar _MENU_ registros por página",
				            zeroRecords: "No se encontraron registros",
				            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
				            infoEmpty: "No hay registros disponibles",
				            infoFiltered: "(filtrado de _MAX_ registros totales)",
				            search: "Buscar:",
				            paginate: {
				                first: "Primero",
				                last: "Último",
				                next: "Siguiente",
				                previous: "Anterior"
				            }
				        }
				    });

				    //tablaAfiliados.search('RG').draw();
				    



				    // Filtrado por botones de resumen TBT
				    $(document).on('click', '.filtro-tbt', function () {
				        const filtro = $(this).data('tbt');
				        tablaAfiliados.search(filtro).draw();
				        //tablaAfiliados.column(12).search(filtro).draw();
				    });

				    // Filtrado por botones de resumen Error
				    $(document).on('click', '.filtro-error', function () {
				        const filtro = $(this).data('error');
				        //tablaAfiliados.column(13).search(filtro).draw();
				        tablaAfiliados.search(filtro).draw();
				    });

				    // Evento cronología
				    $("#tabListaAfilPrestacion tbody").on('click', '.btnCronologia', function () {
				        const id_persona = $(this).data('id_persona');
				        genera_tabla_cronologia();
				        llena_tabla_cronologia(id_persona);
				    });
				}

				//Funciones generales
				$('#btnAgregarFctPresentacion').on('click',function(){

					$(this).attr('disabled','disabled');
					$('#btnAgregarFctPresentacion').html('');					
					$('#btnAgregarFctPresentacion').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

					var datos = {
						"tipo": "integracion_agrega_fct_a_presentacion"
					};

					$.ajax({

						url: 'ajax_gr_sd.php',
						type: 'get',
						data: datos,
						success: function(data){

							// $('#btnAgregarFctPresentacion').removeAttr('disabled');
							// $('#btnAgregarFctPresentacion').html('');					
							// $('#btnAgregarFctPresentacion').html('Agregar facturas nuevas a la presentacion en proceso');						
							
							if(data=="ok"){

								alert('Actualizado con exito!!');
								window.location.reload();

							}
							else{

								alert('Ocurrio un error importando las facturas, comuniqueselo a sistemas');
								console.log(data);
							}
						}
					})
				})
				$('#EditarVencimiento-Guardar').on('click',function(e){
					
					let datos = {
						parametro: 'editar_fecha_vencimiento',
						fecha_vencimiento: $('#EditarVencimiento-fecha').val() ,
						id_lote: $('#EditarVencimiento-id-lote').val()
					};
					$.ajax({
						url: 'ajax.php',
						type: 'GET',
						dataType: 'text',
						data: datos,
					})
					.done(function(data) {
						
						$('#modalEditarVencimiento').modal('toggle');
						ListarLotes();
						if(data==="ok"){
							new PNotify({
								title: 'Cambio Guardado',
								styling: 'bootstrap3',
								type: 'success'
							});
						}else{
							new PNotify({
								title: 'Error.',
								styling: 'bootstrap3',
								type: 'error'
							});
						}
						
					});
					
				})
				$("#btnNuevoPeriodoProcesar").on('click',function(){

					var resp = confirm('¿Confirma?');

					if(resp){


						$(this).attr('disabled','disabled');
						$('#btnAgregarFctPresentacion').html('');					
						$('#btnAgregarFctPresentacion').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

						var datos = {
							"parametro": "crear_periodo_presentacion_novsss"
						};

						$.ajax({

							url: 'ajax.php',
							type: 'get',
							data: datos,
							success: function(data){

								// $('#btnAgregarFctPresentacion').removeAttr('disabled');
								// $('#btnAgregarFctPresentacion').html('');					
								// $('#btnAgregarFctPresentacion').html('Agregar facturas nuevas a la presentacion en proceso');						
								
								if(data.substring(0, 4)=="ERROR"){

									
									alert('Ocurrio un error importando las facturas, comuniqueselo a sistemas');
									console.log(data);

								}
								else{

									alert('Actualizado con exito!! El #n de lotes es '+data);
									window.location.reload();
								}
							}
						})
						console.log('Aca el ajax');
						return false;

					}
					else{

						return false;
					}

					

				})
				// Opciones del listado 
				$('#tabListado tbody').on('click','.btnDescargaDevol',function(){

					console.log('Test');

					var periodo = $(this).data('periodo');
					//var periodo_mostrar = $(this).data('p_mostrar');
					var id_lote = $(this).data('id_lote');

					var url = "ajax_gr_sd.php?tipo=ls_devolucion_presentacion&id_lote_presentacion="+id_lote+"&periodo="+periodo;

					abrirEnPestana(url);

				})
				$('#tabListado tbody').on('click','.btnExportar',function(){

					console.log('');

					var periodo = $(this).data('periodo');
					var fecha_cierre = $(this).data('fecierre');
					var id_lote = $(this).data('id_lote');

					var url = "ajax.php?parametro=CrearArchivo&id_lote="+id_lote+"&periodo="+periodo+"&fecha_cierre="+fecha_cierre;

					abrirEnPestana(url);

				})
				$("#tabListado tbody").on('click','.btnVerListaAfils',function(){

					var id_lote = $(this).data('id_lote');
					$("#id_lote").val(id_lote);
					var periodo = $(this).data('periodo');
					$("#s_periodo").html(periodo);

					genera_tabla();
					cargarResumenAgrupado(id_lote);

					llena_tabla(id_lote,inst_rnos);
					AgruparPorGerenciadora(id_lote);

				})
				// Importadores 
				$(document).on('click','.btnImpErrores',function(){

					$('#errores_id_lote').html($(this).data('id_lote'));
					$('#errores_periodo').html($(this).data('periodo'));
					$("#pe_periodo").val($(this).data('periodo'));
					
					let datos = {
						parametro:'traer_errores_presentacion', 
						id_presentacion: $(this).data('id_lote'), 
						periodo_presentacion: $(this).data('periodo')
					};

					$.ajax({
						url:'ajax.php',
						type: 'GET',
						dataType: 'json',
						data: datos
					}).then(function(data){

						console.table(data);
						$('#tabla_errores_presentaciones tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){

							let {id,cant_registros,fechador} = data[i];

							$('#tabla_errores_presentaciones tbody').append(`
								<tr>
									<td>${id}</td>
									<td>${fechador}</td>
									<td>${cant_registros}</td>
									<td>
										<div class='btn-group btn-group-default'>                
											<button style='margin-left: 20%; margin-right: auto;'  data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
												<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
											</button>
											<ul class='dropdown-menu'>
												<li>
													<a class='btnLoteErrores_Gerenciadoras' data-id_lote='${id}' data-target='#modalPresentacionErrores' data-toggle='modal'>
														Contar errores por Gerenciadora
													</a>						                     	
												</li>
												<li>
													<a class='btnLoteErrores_Codigos' data-id_lote='${id}' data-target='#modalPresentacionErrores' data-toggle='modal'>
														Contar errores por Codigo
													</a>						                     	
												</li>												                     		 
											</ul>
										</div>
									</td>
								</tr>
							`);
						}
					});
				})
				$(document).on('click','.btnEditarVencimiento',function(e){
					$('#EditarVencimiento-fecha').val($(this).data('fecha_vencimiento'));
					$('#EditarVencimiento-id-lote').val($(this).data('id_lote'));

				})	
				$(document).on('click','.btnLoteErrores_Codigos',function(e){
					e.preventDefault();

					let datos = {
						parametro: 'traer_errores_por_codigo',
						id_lote: $(this).data('id_lote')
					};

					$.ajax({
						url: 'ajax.php',
						type: 'GET',
						dataType: 'json',
						data: datos,
					})
					.done(function(data){

						$('#tabla_codigos tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){
							let {codigo,cantidad} = data[i];
							$('#tabla_codigos tbody').append(`
								<tr>
									<td>${codigo}</td>
									<td>${cantidad}</td>
								</tr>
							`);
						}
					});
					
				})
				$(document).on('click','.btnLoteErrores_Gerenciadoras',function(e){
					e.preventDefault();

					let datos = {
						parametro: 'traer_errores_por_gerenciadora',
						id_lote: $(this).data('id_lote')
					};

					$.ajax({
						url: 'ajax.php',
						type: 'GET',
						dataType: 'json',
						data: datos,
					})
					.done(function(data){

						$('#tabla_codigos tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){
							let {codigo,cantidad} = data[i];
							$('#tabla_codigos tbody').append(`
								<tr>
									<td>${codigo}</td>
									<td>${cantidad}</td>
								</tr>
							`);
						}
					});
					
				})
				$("#tabListado").on('click','.btnImpAceptados',function(){

					$("#pa_periodo").val($(this).data('periodo'));
				})
				$("#tabListado").on('click','.btnImpRechazados',function(){

					$("#pr_periodo").val($(this).data('periodo'));
				})	
				$("#btnProcesarErrores").on('click',function(){
					
					var conf = confirm('¿Confirmas ?');
					if(conf){
						
						//Declarar variables en form_data
						var archivo_nombre =  getFile(file_errores.value);						
						var archivo_ext =  file_errores.value.split('.')[1];
						
						var nombre = archivo_nombre+"."+archivo_ext;
						var periodo = $("#pe_periodo").val();
						var id_presentacion = $("#errores_id_lote").html();
						//var id_presentacion = $("#id_lote").val();
						var pe = periodo.replace("-", "");
						var nombre_real = inst_rnos+"-"+pe+".err" ;

						//console.log('Nombre archivo: '+nombre); 
						//console.log('Nombre validacion: '+nombre_real); 
						//return false;

						if(nombre!=nombre_real){

							alert('El archivo debe ser '+nombre_real+' \n y usted quiere importar '+nombre);
							return false;

						}
						else{


							$("#btnProcesarErrores").attr('disabled','disabled');
							$("#btnProcesarErrores").html('');											
							$("#btnProcesarErrores").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

							var parametro = 'procesar_errores';
							var frm = document.getElementById("form3");  
						    var form_data = new FormData(frm); 
						    form_data.append('parametro',parametro);
						    form_data.append('nombre', archivo_nombre);
						    form_data.append('extension', archivo_ext);
						    form_data.append('periodo', periodo);
						    form_data.append('id_presentacion', id_presentacion);
						    

						    //for(var pair of form_data.entries()){console.log(pair[0]+ ': ' + pair[1]);}
						    //Fin declarar variables en form_data

							$.ajax({
								url: 'ajax.php',
								dataType: 'text',
						        cache: false,
						        contentType: false,
						        processData: false,
						        data: form_data,                         
						        type: 'post',
							})
							.done(function(data){
								//console.log(data); return false;
								if(data.substr(0,4)!=="error"){
									alert("Procesado con exito,el # lote es "+data+"  ");
									window.location.reload()
								}else{
									alert('Hubo un problema, comuniquese con sistemas');
									console.log(data);
								}
							}); 
						}
					}
				})
				$("#btnProcesarAceptados").on('click',function(){

					//$(this).attr('disabled','disabled');
					
					var conf = confirm('¿Confirma?');
					if(conf){

						//Declarar variables en form_data						
						var periodo = $("#pa_periodo").val();
						var archivo_nombre =  getFile(file_aceptados.value);
						var archivo_ext =  file_aceptados.value.split('.')[1];
						var parametro = 'procesar_aceptados_dev';

						if(archivo_nombre!='Aceptados-'+inst_rnos && archivo_ext!='txt'){
							console.log(archivo_nombre);
							alert(`El archivo indicado no es Aceptados-${inst_rnos}.txt`);
							return false;

						}
						else{

							$("#btnProcesarAceptados").attr('disabled','disabled');
							$("#spanEnviarAceptados").html('');					
							$("#spanEnviarAceptados").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
							
							
						    
							var frm = document.getElementById("form1");  
					    var form_data = new FormData(frm); 
					    form_data.append('parametro',parametro);
					    form_data.append('nombre', archivo_nombre);
					    form_data.append('extension', archivo_ext);
					    form_data.append('periodo', periodo);

							$.ajax({
								url: 'ajax.php',
								dataType: 'text',
						        cache: false,
						        contentType: false,
						        processData: false,
						        data: form_data,                         
						        type: 'post',
							})
							.done(function(data){

								//console.log(data); return false;
								
								if(data.substr(0,4)!=="error"){
									alert("Procesado con exito,el # lote es "+data+"  ");
									window.location.reload()
								}else{
									alert('Hubo un problema, comuniquese con sistemas');
									console.log(data);
								}
							}); 
						}
					}
				})
				$("#btnProcesarRechazados").on('click',function(){
					
					var conf = confirm('¿Confirma?');
					if(conf){
						
						//Declarar variables en form_data
						var archivo_nombre =  getFile(file_rechazados.value);						
						var archivo_ext =  file_rechazados.value.split('.')[1];
						var nombre = archivo_nombre+"."+archivo_ext;
						var periodo = $("#pr_periodo").val();

						if(nombre!=`Rechazados-${inst_rnos}.txt`){

							alert(`El archivo indicado no es Rechazados-${inst_rnos}.txt`);
							return false;

						}else{


							$("#btnProcesarRechazados").attr('disabled','disabled');
							$("#spanEnviar").html('');					
							$("#spanEnviar").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

							var parametro = 'procesar_rechazados';
							var frm = document.getElementById("form2");  
						    var form_data = new FormData(frm); 
						    form_data.append('parametro',parametro);
						    form_data.append('nombre', archivo_nombre);
						    form_data.append('extension', archivo_ext);
						    form_data.append('periodo', periodo);
						    

						    //for(var pair of form_data.entries()){console.log(pair[0]+ ': ' + pair[1]);}
						    //Fin declarar variables en form_data

							$.ajax({
								url: 'ajax.php',
								dataType: 'text',
						        cache: false,
						        contentType: false,
						        processData: false,
						        data: form_data,                         
						        type: 'post',
							})
							.done(function(data){

								if(data.substr(0,4)!=="error"){
									alert("Procesado con exito,el # lote es "+data+"  ");
									window.location.reload()
								}else{
									alert('Hubo un problema, comuniquese con sistemas');
									console.log(data);
								}
							}); 
						}
					}
				})
				$(document).on('click','.btnQuitarFctPresentacion',function(){

					var id_expo = $(this).data('id_expo');
					var id_lote = $(this).data('id_lote');

					var resp = confirm('Confirma ?');

					if(resp){

						var datos = {
							"parametro": "quitar_fct_presentacion",
							"id_expo": id_expo,
							"id_lote": id_lote
						};

						$.ajax({
							url: 'ajax.php',
							type: 'get',
							data: datos,
							success: function(data){	

								if(data=="ok"){

									alert("El afiliado fue quitado de la presentacion, \n recuerde que debe exportar nuevamente.");
									genera_tabla();
									llena_tabla(id_lote);
								}
								else{

									console.log('nada');
									return false;
								}
								
							}
						})

					}
					else{

						return false;
					}

				})
				$(document).on('click','.btnVerAfiliado',function(){



					var id_titular = $(this).data('id_titular');
					var id_afiliado = $(this).data('id_afiliado');

					if(id_titular==0){
						id_titular=id_afiliado;
					}

					var url = "../ver_grupo_familiar/index.php?id_titular="+id_titular+"&id_af_consultado="+id_afiliado;

					abrirEnPestana(url);
				})
				$(document).on('click','.btnSupervisa',function(e){
					e.preventDefault();

					window.open('supervisar_novedades.php','_blank');
				})
			
				$(document).on('click','#btnExportarExcel',function(){
					var url = "ajax.php?parametro=exportar_afiliados_excel&id_lote="+$("#id_lote").val();
					abrirEnPestana(url);
				})

				
				



			})

			function abrirEnPestana(url) {
				var a = document.createElement("a");
				a.target = "_blank";
				a.href = url;
				a.click();
			}
			


			function cargarResumenAgrupado(id_lote) {
			    $.getJSON("ajax.php", { parametro: "resumen_tbt_y_errores", id_lote: id_lote }, function (data) {
				    console.log("JSON recibido:", data);

				    const resumenTBT = data.resumen_tbt || [];
				    const resumenErrores = data.resumen_errores || [];

				    let htmlResumen = "<div class='row'>";

				    htmlResumen += "<div class='col-md-6'><h5><strong>Resumen por Tipo Beneficiario (TBT):</strong></h5><div class='d-flex flex-wrap gap-2'>";
					resumenTBT.forEach(item => {
					    const tbt = item.tbt || "Sin datos";
					    htmlResumen += `<a href='#' class='btn btn-sm btn-info filtro-tbt' data-tbt='${tbt}'>${tbt}: ${item.cantidad}</a>`;
					});
					htmlResumen += "</div></div>";

					htmlResumen += "<div class='col-md-6'><h5><strong>Resumen por Código de Error:</strong></h5><div class='d-flex flex-wrap gap-2'>";
					resumenErrores.forEach(item => {
					    const cod = item.errores || "Sin errores";
					    htmlResumen += `<a href='#' class='btn btn-sm btn-warning filtro-error' data-error='${cod}'>${cod}: ${item.cantidad}</a>`;
					});
					htmlResumen += "</div></div>";				    

				    $("#resumen-errores").html(htmlResumen).show();


				});


			}


			function genera_tabla_cronologia(){

				$('#divListCronologia').html("<table id='tabListaCronologia' class='table' style='font-size: 11px;'>"
									        		+"<thead>"
									        			+"<tr>"
									        				+"<th>#</th>"
									        												        				
									        				+"<th>Fecha movimiento</th>"								        				
									        				+"<th>Movimiento</th>"
									        			+"</tr>"
									        		+"</thead>"
									        		+"<tbody></tbody>"
									        	+"</table>"
													);

			}

			function llena_tabla_cronologia(id_persona){

				$("#tabListaCronologia tbody").html("<h3>Cargando...</h3>");

				var li_modal_crono = "";

				$.getJSON('ajax.php',
							{ parametro: "lst_cronologia_afiliado", id_persona: id_persona },						       				
							function(data){ 
								
								$("#tabListaCronologia tbody").html("");

								console.log("cantidad: "+data.length);

								for(var i=0; i<=data.length-1 ;i++){

									
								
									$("#tabListaCronologia tbody").append("<tr>"																
																				+"<td>"+(i+1)+"</td>"
																				
																				
																				+"<td>"+data[i]['fechador']+"</td>"
																				+"<td>"+data[i]['movimiento']+"</td>"
																				
																																									
																			+"</tr>") ;		
								}	

								$("#tabListaCronologia").dataTable({			    	
										"bPaginate": false,
										//"iDisplayLength": 100,
										"bLengthChange": false,
										"bFilter": true,
										//"bSort": true,
										"bInfo": false,
										//"aaSorting": [[ 2, "desc" ]],
										"bAutoWidth": false,
										"language": {				    
										    "search": "Buscar",
										    "paginate": {
											      "previous": "Anterior",
											      "next": "Proximo"
											}
									    }
								});

								


							}//fin function data

				);//fin getjson

				

			}

			function AgruparPorGerenciadora(id_lote){
				$.ajax({
					url: 'ajax.php',
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'lst_afiliados_presentacion_x_gerenciadora',id_lote: id_lote},
				})
				.done(function(data) {
					$('#tabTotal_x_desreguladora tbody').html("");

					for(var i=0; i<=data.length-1 ;i++){
						let {desreguladora,contador} = data[i];

						$('#tabTotal_x_desreguladora tbody').append(`
							<tr>
								<td>${desreguladora}</td>
								<td>${contador}</td>
							</tr>
						`);						
					}
				});
			}
			function ListarLotes(){

				$('#tabListado tbody').html("Cargando...");

				$.ajax({
					url: 'ajax.php',
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'lst_novedades_presentaciones'},
				})
				.done(function(data) {
					//console.table(data);

					let btn_editar_vencimiento = btn_exportar = ``;
					$('#tabListado tbody').html("");
					for(var i=0; i<=data.length-1 ;i++){

						let {id,descripcion,archivo,estado,q,errores_q,fecha_vencimiento,rechazados_q,aceptados_q} = data[i];
						
						btn_editar_vencimiento = btn_exportar = btn_supervisa = periodo_proceso = ``;

						if(estado=="Proceso"){
							btn_editar_vencimiento = `
								<li>
									<a class='btnEditarVencimiento' data-toggle='modal' data-target='#modalEditarVencimiento' data-fecha_vencimiento='${fecha_vencimiento}' data-id_lote='${id}'>
											Editar Fecha de Vencimiento
									</a>						                     	
								</li>
							`;

							btn_exportar = `
								<li>
									<a class='btnExportar' data-id_lote='${id}' data-periodo='${descripcion}' data-fecierre='${archivo}' >
										Exportar
									</a>
								</li>
							`;

							periodo_proceso = `class='periodo_proceso'`;

							if(INST_SUPERVISA_NOVEDADES){
								btn_supervisa= `
								<li>
									<a class='btnSupervisa' data-id_lote='${id}' data-periodo='${descripcion}' data-fecierre='${archivo}' >
										Supervisar Lote
									</a>
								</li>
							`;
							}

						}

						$('#tabListado tbody').append(`
								<tr ${periodo_proceso}>
									<td>${id}</td>
									<td>
										<div class='btn-group btn-group-default'>                
											<button style='margin-left: 20%; margin-right: auto;'  data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
												<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
											</button>
											<ul class='dropdown-menu'>
												 <li>
													<a class='btnVerListaAfils' data-id_lote='${id}' data-periodo='${descripcion}' data-toggle='modal' data-target='#modalListaFct' >
														Ver afiliados
													</a>						                     	
												</li>
												 <li>
													<a class='btnImpErrores' data-toggle='modal' data-target='#modalErrores' data-periodo='${descripcion}' data-id_lote='${id}'>						                     		
														Gestion de Errores
													</a>						                     	
												 </li>	
												 <li>
													<a class='btnImpAceptados' data-toggle='modal' data-target='#modalAceptados' data-periodo='${descripcion}'>                    		
														Importar aceptados
													</a>						                     	
												 </li>	
												 <li>
													<a class='btnImpRechazados' data-toggle='modal' data-target='#modalRechazados' data-periodo='${descripcion}' >
														Importar rechazados
													</a>						                     	
												 </li>	
												 ${btn_editar_vencimiento}
												 ${btn_exportar}
												 <li>
													<a class='btnDescargaDevol' data-id_lote='${id}' data-periodo='${descripcion}' >						                     		
														Descargar devoluciones
													</a>						                     	
												 </li>
												 ${btn_supervisa}												                     		 
											</ul>
										</div>	
									</td>
									<td>${descripcion}</td>
									<td>${fecha_vencimiento}</td>
									<td>${estado}</td>
									<td style='text-align: right;'>${q}</td>
									<td style='text-align: right;'>${errores_q}</td>
									<td style='text-align: right;'>${aceptados_q}</td>
									<td style='text-align: right;'>${rechazados_q}</td>
								</tr>
						`);
					}
				});
			}

			function abrirEnPestana(url) {
				var a = document.createElement("a");
				a.target = "_blank";
				a.href = url;
				a.click();
			}

			function getFile(filePath){

				return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
			} 

			function ValidateSingleInput(oInput) {
			    if (oInput.type == "file") {
			        var sFileName = oInput.value;
			         if (sFileName.length > 0) {
			            var blnValid = false;
			            for (var j = 0; j < _validFileExtensions.length; j++) {
			                var sCurExtension = _validFileExtensions[j];
			                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
			                    blnValid = true;
			                    break;
			                }
			            }
			             
			            if (!blnValid) {
			                alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
			                oInput.value = "";
			                return false;
			            }
			        }
			    }
			    return true;
			}

		</script>

	</body>
</html>
