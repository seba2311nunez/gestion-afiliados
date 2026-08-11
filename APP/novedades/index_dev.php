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

		<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
		
		<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
		<link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">

		<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

		<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">

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
				font-size:11px !important;
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
			#contenedorEliminarSeleccionados{
				clear: both;
				margin: 10px 0;
			}
			#avisoBuscandoTabla{
				display:none;
				position: fixed;
				top: 20px;
				right: 20px;
				z-index: 99999;
				background: rgba(0,0,0,0.82);
				color: #fff;
				padding: 10px 14px;
				border-radius: 6px;
				font-size: 13px;
				box-shadow: 0 2px 8px rgba(0,0,0,0.25);
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
			<a class="btn btn-info" id="btnNuevoPeriodoProcesar" data-toggle='modal' data-target='#modalNuevoPeriodo'>
				Agregar nuevo periodo de presentacion
			</a>
			<a class="btn btn-info" href="https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/cronograma_ftp.pdf" target="_blank">
				Cronograma FTP
			</a>
			<a class="btn btn-info" href="https://seguro.sssalud.gob.ar/descargas/rnos/restricto/padron/InstructivoObrasSociales.pdf" target="_blank">
				Instructivo de novedades | Acciones a seguir con cada código de error a partir de Pág. 16
			</a>
			<a class="btn btn-info" href="javascript:void(0);">
				Boton de demostracion
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
						<th style="text-align: right;">Movimientos</th>
						<th style="text-align: right;">Error FTP</th>
						<th style="text-align: right;">Aceptadas</th>
						<th style='text-align: right;'>Rechazadas</th>					
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<br>
			<div style="text-align: center;"></div>
		</div>

		<div id="avisoBuscandoTabla">
			<i class="fa fa-spinner fa-spin"></i> Cargando resultados...
		</div>

		<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modalAceptados' style="display: none;">
		  Launch demo modal
		</button>

		<div id="modalNuevoPeriodo" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Nuevo periodo presentacion</h4>
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
				<a id="btnNuevoPeriodoProcesar" class="btn btn-success" onchange="ValidateSingleInput(this)">
					<span id="spanNuevoPeriodoProcesar"></span>Generar
				</a>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default btnCierraModal" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div id="modalAceptados" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Subir archivo novedades aceptadas</h4>
		      </div>
		      <div class="modal-body">
		        <form method="post" name="form1" id="form1">	
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
					<a id="btnProcesarAceptados" class="btn btn-success" onchange="ValidateSingleInput(this)">
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

		<div id="modalErrores" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Gestion de Errores | Periodo: <strong id="errores_periodo"></strong> | ID LOTE: <strong id="errores_id_lote"></strong></h4>
		      </div>
		      <div class="modal-body">
		      	<div class="row">
		      		<div class="col-md-12">
				        <div class="row p-15px">
				        	<form method="post" name="form3" id="form3">	
								<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="200000000">
								<input type="hidden" name="parametro" id="parametro">
								<input type="hidden" name="nombre" id="nombre">
								<input type="hidden" name="extension" id="extension">
								<input type="hidden" name="pe_periodo" id="pe_periodo">

								<label>Seleccionar archivo</label>
								<input type="file" name="file_errores" id="file_errores" required class="inp-sm">
								<br>
								<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
								<a id="btnProcesarErrores" class="btn btn-success" onchange="ValidateSingleInput(this)">
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

		<div id="modalPresentacionErrores" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
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

		<div id="modalRechazados" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Subir archivo novedades rechazadas</h4>
		      </div>
		      <div class="modal-body">
		        <form method="post" name="form1" id="form2">	
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
					<a id="btnProcesarRechazados" class="btn btn-success" onchange="ValidateSingleInput(this)">
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

		<div id="modalListaFct" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg" style="width: 1300px;">
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
		      	<div id="resumen-top" class="row" style="display: none; margin: 10px 0;"></div>

		      	<div class="row" style="padding-left: 10px; padding-bottom: 10px;">
		      		<input type="hidden" id="id_lote">

		      		<a id="btnExportarExcel" target="_blank" class="btn btn-success">
					    <i class="fa fa-file-excel-o"></i> Exportar todo a Excel
					</a>
		      	</div>

			    <div id="topScrollWrapper" style="overflow-x:auto; overflow-y:hidden; height:16px; margin-bottom:8px; display:none;">
					<div id="topScrollContent" style="height:1px;"></div>
				</div>

				<div id="divListFctPrestacion" class="table-responsive"></div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div id="modalEditarVencimiento" class="modal fade" role="dialog">
		  <div class="modal-dialog" style="width: 1100px;">
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

		<div id="modalCronologia" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Cronologia</h4>
		        Afiliado: <b><span id="s_afil_crono"></span></b>
		      </div>
		      <div class="modal-body">
		        <div id="divListCronologia"></div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div id="modalProgresoFTP" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h4 class="modal-title">Envio de novedades a SSS | Periodo: <strong id="progresoFTP_periodo"></strong></h4>
		      </div>
		      <div class="modal-body">
		        <ul class="list-unstyled" id="progresoFTP_pasos" style="font-size: 14px; line-height: 2;">
		          <li data-paso="generar"><span class="icono-paso"><i class="fa fa-circle-o"></i></span> Generar archivo de novedades <span class="texto-extra-paso text-muted"></span></li>
		          <li data-paso="enviar"><span class="icono-paso"><i class="fa fa-circle-o"></i></span> Enviar por FTP a SSS <span class="texto-extra-paso text-muted"></span></li>
		          <li data-paso="esperar"><span class="icono-paso"><i class="fa fa-circle-o"></i></span> Esperar devolucion de SSS (.ok/.err) <span class="texto-extra-paso text-muted"></span></li>
		          <li data-paso="importar"><span class="icono-paso"><i class="fa fa-circle-o"></i></span> Importar errores a la base <span class="texto-extra-paso text-muted"></span></li>
		        </ul>
		        <div id="progresoFTP_mensajeFinal" class="alert" style="display: none; margin-top: 10px;"></div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal" id="progresoFTP_btnCerrar" style="display: none;">Cerrar</button>
		      </div>
		    </div>
		  </div>
		</div>

		<script type="text/javascript">
			let inst_rnos = "<?php echo INST_RNOS; ?>";
			let INST_SUPERVISA_NOVEDADES = "<?php echo INST_SUPERVISA_NOVEDADES; ?>";

			$(document).ready(function(){

				let tablaAfiliados;
				let afiliadosSeleccionados = {};

				ListarLotes();

				function genera_tabla(){
					$('#divListFctPrestacion').html(
						"<table id='tabListaAfilPrestacion' class='table table-condensed'>"
							+"<thead>"
								+"<tr>"
									+"<th style='width:30px; text-align:center;'><input type='checkbox' id='checkTodosAfiliados'></th>"
									+"<th>#</th>"
									+"<th></th>"
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

				function actualizarCheckTodos() {
					let totalVisibles = $('.chkAfiliado').length;
					let totalChequeados = $('.chkAfiliado:checked').length;
					$('#checkTodosAfiliados').prop('checked', totalVisibles > 0 && totalVisibles === totalChequeados);
				}

				function obtenerIdsSeleccionados() {
					let ids = [];
					for (let idExpo in afiliadosSeleccionados) {
						if (afiliadosSeleccionados[idExpo]) {
							ids.push(idExpo);
						}
					}
					return ids;
				}

				function llena_tabla(id_lote, inst_rnos) {
				    const parametro = "lst_afiliados_presentacion_ssp";

				    if ($.fn.DataTable.isDataTable('#tabListaAfilPrestacion')) {
				        $('#tabListaAfilPrestacion').DataTable().destroy();
				    }

				    afiliadosSeleccionados = {};
				    $('#checkTodosAfiliados').prop('checked', false);

				    tablaAfiliados = $('#tabListaAfilPrestacion').DataTable({
				        serverSide: true,
				        processing: true,
				        ajax: {
				            url: 'ajax_dev.php',
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
				        },
				        drawCallback: function () {
				            $('.chkAfiliado').each(function () {
				                const idExpo = $(this).data('id_expo');
				                $(this).prop('checked', afiliadosSeleccionados[idExpo] ? true : false);
				            });

				            actualizarCheckTodos();

				            if ($('#contenedorEliminarSeleccionados').length === 0) {
				                $('<div id="contenedorEliminarSeleccionados">\
				                    <a id="btnEliminarSeleccionados" class="btn btn-danger">\
				                        <i class="fa fa-trash"></i> Eliminar seleccionados\
				                    </a>\
				                </div>').insertBefore($('#tabListaAfilPrestacion_wrapper table'));
				            }

				            inicializarScrollSuperior();
				        }
				    });

				    $('#tabListaAfilPrestacion').off('processing.dt').on('processing.dt', function (e, settings, processing) {
						if (processing) {
							$('#avisoBuscandoTabla').fadeIn(100);
						} else {
							$('#avisoBuscandoTabla').fadeOut(100);
						}
					});
				}

				$(document).on('change', '.chkAfiliado', function () {
					let idExpo = $(this).data('id_expo');

					if ($(this).is(':checked')) {
						afiliadosSeleccionados[idExpo] = true;
					} else {
						delete afiliadosSeleccionados[idExpo];
					}

					actualizarCheckTodos();
				});

				$(document).on('change', '#checkTodosAfiliados', function () {
					let checked = $(this).is(':checked');

					$('.chkAfiliado').each(function () {
						let idExpo = $(this).data('id_expo');
						$(this).prop('checked', checked);

						if (checked) {
							afiliadosSeleccionados[idExpo] = true;
						} else {
							delete afiliadosSeleccionados[idExpo];
						}
					});
				});

				$(document).on('click', '.filtro-tbt', function (e) {
					e.preventDefault();
					const filtro = $(this).data('tbt');
					if (tablaAfiliados) {
						tablaAfiliados.search(filtro).draw();
					}
				});

				$(document).on('click', '.filtro-error', function (e) {
					e.preventDefault();
					const filtro = $(this).data('error');
					if (tablaAfiliados) {
						tablaAfiliados.search(filtro).draw();
					}
				});

				$(document).on('click', '#btnEliminarSeleccionados', function () {
					let ids = obtenerIdsSeleccionados();
					let id_lote = $("#id_lote").val();

					if (ids.length === 0) {
						alert('Seleccioná al menos un afiliado para eliminar.');
						return false;
					}

					let resp = confirm('¿Confirma eliminar ' + ids.length + ' registro(s) seleccionados?');

					if (!resp) {
						return false;
					}

					$(this).attr('disabled', 'disabled');
					$(this).html('<i class="fa fa-spinner fa-spin"></i> Eliminando...');

					$.ajax({
						url: 'ajax_dev.php',
						type: 'POST',
						dataType: 'text',
						data: {
							parametro: 'quitar_fct_presentacion_multiple',
							id_lote: id_lote,
							'ids_expo[]': ids
						},
						success: function (data) {
							$('#btnEliminarSeleccionados').removeAttr('disabled');
							$('#btnEliminarSeleccionados').html('<i class="fa fa-trash"></i> Eliminar seleccionados');

							if ($.trim(data) === 'ok') {
								alert('Los registros seleccionados fueron quitados de la presentación.');
								afiliadosSeleccionados = {};
								$('#checkTodosAfiliados').prop('checked', false);
								tablaAfiliados.ajax.reload(null, false);
							} else {
								alert('Ocurrió un error al eliminar los registros.');
								console.log(data);
							}
						},
						error: function (xhr) {
							$('#btnEliminarSeleccionados').removeAttr('disabled');
							$('#btnEliminarSeleccionados').html('<i class="fa fa-trash"></i> Eliminar seleccionados');
							alert('Ocurrió un error al eliminar los registros.');
							console.log(xhr.responseText);
						}
					});

					return false;
				});

				$('#EditarVencimiento-Guardar').on('click',function(e){
					let datos = {
						parametro: 'editar_fecha_vencimiento',
						fecha_vencimiento: $('#EditarVencimiento-fecha').val(),
						id_lote: $('#EditarVencimiento-id-lote').val()
					};

					$.ajax({
						url: 'ajax_dev.php',
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
				});

				$("#btnNuevoPeriodoProcesar").on('click',function(){
					var resp = confirm('¿Confirma?');

					if(resp){
						$(this).attr('disabled','disabled');

						var datos = {
							"parametro": "crear_periodo_presentacion_novsss"
						};

						$.ajax({
							url: 'ajax_dev.php',
							type: 'get',
							data: datos,
							success: function(data){
								if(data.substring(0, 4)=="ERROR"){
									alert('Ocurrio un error importando las facturas, comuniqueselo a sistemas');
									console.log(data);
								}
								else{
									alert('Actualizado con exito!! El #n de lotes es '+data);
									window.location.reload();
								}
							}
						});

						return false;
					}else{
						return false;
					}
				});

				$('#tabListado tbody').on('click','.btnDescargaDevol',function(){
					var periodo = $(this).data('periodo');
					var id_lote = $(this).data('id_lote');
					var $link = $(this);
					var textoOriginal = $link.text();

					$link.text('Buscando en el FTP...');

					$.ajax({
						url: 'ajax_dev.php',
						type: 'GET',
						dataType: 'json',
						data: {parametro: 'ftp_sss_traer_devolucion', periodo: periodo, id_lote: id_lote},
					})
					.done(function(data){
						if(data.status === 'ok'){
							var msj = 'Periodo '+periodo+':\n';
							msj += data.encontrado_ok ? '.ok encontrado y descargado.\n' : '.ok todavia no esta disponible en el FTP.\n';
							msj += data.encontrado_err ? '.err encontrado y descargado. Importalo desde "Gestion de Errores".' : '.err todavia no esta disponible en el FTP.';
							alert(msj);
						}
						else{
							alert('No se pudo traer la devolucion del FTP:\n'+data.mensaje);
						}
					})
					.fail(function(){
						alert('Error de comunicacion al consultar el FTP.');
					})
					.always(function(){
						$link.text(textoOriginal);
					});
				});

				function progresoFTP_reset(periodo){
					$('#progresoFTP_periodo').text(periodo);
					$('#progresoFTP_pasos li').each(function(){
						$(this).find('.icono-paso').html("<i class='fa fa-circle-o'></i>");
						$(this).find('.texto-extra-paso').text('');
					});
					$('#progresoFTP_mensajeFinal').hide().removeClass('alert-success alert-danger alert-warning').text('');
					$('#progresoFTP_btnCerrar').hide();
					$('#modalProgresoFTP').modal('show');
				}

				function progresoFTP_setEstado(paso, estado, textoExtra){
					var $li = $('#progresoFTP_pasos li[data-paso="'+paso+'"]');
					var icono = "<i class='fa fa-circle-o'></i>";

					if(estado === 'en_progreso'){
						icono = "<i class='fa fa-spinner fa-spin'></i>";
					}
					else if(estado === 'ok'){
						icono = "<i class='fa fa-check' style='color:#3c763d;'></i>";
					}
					else if(estado === 'error'){
						icono = "<i class='fa fa-times' style='color:#a94442;'></i>";
					}
					else if(estado === 'omitido'){
						icono = "<i class='fa fa-minus' style='color:#999;'></i>";
					}

					$li.find('.icono-paso').html(icono);

					if(typeof textoExtra !== 'undefined'){
						$li.find('.texto-extra-paso').text(textoExtra);
					}
				}

				function progresoFTP_finalizar(tipo, mensaje){
					$('#progresoFTP_mensajeFinal').removeClass('alert-success alert-danger alert-warning').addClass('alert-'+tipo).text(mensaje).show();
					$('#progresoFTP_btnCerrar').show();
				}

				$('#tabListado tbody').on('click','.btnExportar',function(){
					var periodo = $(this).data('periodo');
					var id_lote = $(this).data('id_lote');

					if(!confirm('Se va a generar el archivo del periodo '+periodo+', enviarlo por FTP a SSS y, cuando la SSS responda, importar los errores automaticamente. Puede tardar varios minutos y hay que dejar esta pestaña abierta. ¿Confirmar?')){
						return;
					}

					var maxIntentosDevolucion = 40; // 40 * 5s = ~200s de espera maxima
					var intentoActual = 0;

					progresoFTP_reset(periodo);

					function importarErroresDescargados(){
						progresoFTP_setEstado('importar', 'en_progreso');

						$.ajax({
							url: 'ajax_dev.php',
							type: 'GET',
							dataType: 'json',
							data: {parametro: 'ftp_sss_importar_errores_descargados', periodo: periodo, id_lote: id_lote},
						})
						.done(function(imp){
							if(imp.status === 'ok'){
								progresoFTP_setEstado('importar', 'ok', imp.errores_importados+' errores importados');
								progresoFTP_finalizar('success', 'Circuito completo. Errores importados: '+imp.errores_importados+'.');
							}
							else{
								progresoFTP_setEstado('importar', 'error');
								progresoFTP_finalizar('danger', 'La devolucion se descargo, pero fallo la importacion: '+imp.mensaje);
							}
							ListarLotes();
						})
						.fail(function(){
							progresoFTP_setEstado('importar', 'error');
							progresoFTP_finalizar('danger', 'Error de comunicacion importando los errores. La devolucion ya esta descargada: se puede reintentar desde "Gestion de Errores".');
						});
					}

					function intentarTraerDevolucion(){
						intentoActual++;
						progresoFTP_setEstado('esperar', 'en_progreso', 'intento '+intentoActual+'/'+maxIntentosDevolucion);

						$.ajax({
							url: 'ajax_dev.php',
							type: 'GET',
							dataType: 'json',
							data: {parametro: 'ftp_sss_traer_devolucion', periodo: periodo, id_lote: id_lote},
						})
						.done(function(devol){
							if(devol.status !== 'ok'){
								progresoFTP_setEstado('esperar', 'error');
								progresoFTP_finalizar('danger', 'Error consultando la devolucion: '+devol.mensaje);
								return;
							}

							if(!devol.encontrado_ok && !devol.encontrado_err){
								if(intentoActual < maxIntentosDevolucion){
									setTimeout(intentarTraerDevolucion, 5000);
								}
								else{
									progresoFTP_setEstado('esperar', 'error', 'sin respuesta de SSS');
									progresoFTP_finalizar('warning', 'El archivo se envio, pero la SSS todavia no genero la devolucion despues de varios minutos. Probar "Traer devolucion FTP (.ok/.err)" mas tarde.');
								}
								return;
							}

							progresoFTP_setEstado('esperar', 'ok');

							if(!devol.encontrado_err){
								progresoFTP_setEstado('importar', 'omitido', 'sin errores (.ok)');
								progresoFTP_finalizar('success', 'Devolucion recibida sin errores (.ok). No hay nada para importar.');
								ListarLotes();
								return;
							}

							importarErroresDescargados();
						})
						.fail(function(){
							if(intentoActual < maxIntentosDevolucion){
								setTimeout(intentarTraerDevolucion, 5000);
							}
							else{
								progresoFTP_setEstado('esperar', 'error');
								progresoFTP_finalizar('danger', 'Error de comunicacion consultando la devolucion (se agotaron los reintentos).');
							}
						});
					}

					progresoFTP_setEstado('generar', 'en_progreso');

					$.ajax({
						url: 'ajax_dev.php',
						type: 'GET',
						dataType: 'json',
						data: {parametro: 'ftp_sss_generar_archivo', periodo: periodo, id_lote: id_lote},
					})
					.done(function(dataGenerar){
						if(dataGenerar.status !== 'ok'){
							progresoFTP_setEstado('generar', 'error');
							progresoFTP_finalizar('danger', 'No se pudo generar el archivo: '+dataGenerar.mensaje);
							return;
						}

						progresoFTP_setEstado('generar', 'ok', dataGenerar.cantidad_movimientos+' movimientos');
						progresoFTP_setEstado('enviar', 'en_progreso');

						$.ajax({
							url: 'ajax_dev.php',
							type: 'GET',
							dataType: 'json',
							data: {parametro: 'ftp_sss_subir_novedades', periodo: periodo, id_lote: id_lote},
						})
						.done(function(dataEnviar){
							if(dataEnviar.status !== 'ok'){
								progresoFTP_setEstado('enviar', 'error');
								progresoFTP_finalizar('danger', 'No se pudo enviar el archivo por FTP: '+dataEnviar.mensaje);
								return;
							}

							progresoFTP_setEstado('enviar', 'ok');
							setTimeout(intentarTraerDevolucion, 5000);
						})
						.fail(function(){
							progresoFTP_setEstado('enviar', 'error');
							progresoFTP_finalizar('danger', 'Error de comunicacion enviando el archivo por FTP.');
						});
					})
					.fail(function(){
						progresoFTP_setEstado('generar', 'error');
						progresoFTP_finalizar('danger', 'Error de comunicacion generando el archivo.');
					});
				});

				$("#tabListado tbody").on('click','.btnVerListaAfils',function(){
					var id_lote = $(this).data('id_lote');
					$("#id_lote").val(id_lote);

					var periodo = $(this).data('periodo');
					$("#s_periodo").html(periodo);

					genera_tabla();
					inicializarScrollSuperior();
					cargarResumenAgrupado(id_lote);
					llena_tabla(id_lote,inst_rnos);
					AgruparPorGerenciadora(id_lote);
				});

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
						url:'ajax_dev.php',
						type: 'GET',
						dataType: 'json',
						data: datos
					}).then(function(data){
						$('#tabla_errores_presentaciones tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){

							let id = data[i].id;
							let cant_registros = data[i].cant_registros;
							let fechador = data[i].fechador;

							$('#tabla_errores_presentaciones tbody').append(`
								<tr>
									<td>${id}</td>
									<td>${fechador}</td>
									<td>${cant_registros}</td>
									<td>
										<div class='btn-group btn-group-default'>                
											<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' type='button'>
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
				});

				$(document).on('click','.btnEditarVencimiento',function(e){
					$('#EditarVencimiento-fecha').val($(this).data('fecha_vencimiento'));
					$('#EditarVencimiento-id-lote').val($(this).data('id_lote'));
				});

				$(document).on('click','.btnLoteErrores_Codigos',function(e){
					e.preventDefault();

					let datos = {
						parametro: 'traer_errores_por_codigo',
						id_lote: $(this).data('id_lote')
					};

					$.ajax({
						url: 'ajax_dev.php',
						type: 'GET',
						dataType: 'json',
						data: datos,
					})
					.done(function(data){
						$('#tabla_codigos tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){
							let codigo = data[i].codigo;
							let cantidad = data[i].cantidad;
							$('#tabla_codigos tbody').append(`
								<tr>
									<td>${codigo}</td>
									<td>${cantidad}</td>
								</tr>
							`);
						}
					});
				});

				$(document).on('click','.btnLoteErrores_Gerenciadoras',function(e){
					e.preventDefault();

					let datos = {
						parametro: 'traer_errores_por_gerenciadora',
						id_lote: $(this).data('id_lote')
					};

					$.ajax({
						url: 'ajax_dev.php',
						type: 'GET',
						dataType: 'json',
						data: datos,
					})
					.done(function(data){
						$('#tabla_codigos tbody').html("");
						for(var i=0; i<=data.length-1 ;i++){
							let codigo = data[i].codigo;
							let cantidad = data[i].cantidad;
							$('#tabla_codigos tbody').append(`
								<tr>
									<td>${codigo}</td>
									<td>${cantidad}</td>
								</tr>
							`);
						}
					});
				});

				$("#tabListado").on('click','.btnImpAceptados',function(){
					$("#pa_periodo").val($(this).data('periodo'));
				});

				$("#tabListado").on('click','.btnImpRechazados',function(){
					$("#pr_periodo").val($(this).data('periodo'));
				});

				$("#btnProcesarErrores").on('click',function(){
					var conf = confirm('¿Confirmas ?');
					if(conf){
						var archivo_nombre = getFile(file_errores.value);
						var archivo_ext = file_errores.value.split('.')[1];
						var nombre = archivo_nombre+"."+archivo_ext;
						var periodo = $("#pe_periodo").val();
						var id_presentacion = $("#errores_id_lote").html();
						var pe = periodo.replace("-", "");
						var nombre_real = inst_rnos+"-"+pe+".err";

						if(nombre!=nombre_real){
							alert('El archivo debe ser '+nombre_real+' \n y usted quiere importar '+nombre);
							return false;
						}else{
							$("#btnProcesarErrores").attr('disabled','disabled');
							$("#btnProcesarErrores").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

							var parametro = 'procesar_errores';
							var frm = document.getElementById("form3");
						    var form_data = new FormData(frm);
						    form_data.append('parametro',parametro);
						    form_data.append('nombre', archivo_nombre);
						    form_data.append('extension', archivo_ext);
						    form_data.append('periodo', periodo);
						    form_data.append('id_presentacion', id_presentacion);

							$.ajax({
								url: 'ajax_dev.php',
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
				});

				$("#btnProcesarAceptados").on('click',function(){
					var conf = confirm('¿Confirma?');
					if(conf){
						var periodo = $("#pa_periodo").val();
						var archivo_nombre = getFile(file_aceptados.value);
						var archivo_ext = file_aceptados.value.split('.')[1];
						var parametro = 'procesar_aceptados_dev';

						if(archivo_nombre!='Aceptados-'+inst_rnos && archivo_ext!='txt'){
							alert(`El archivo indicado no es Aceptados-${inst_rnos}.txt`);
							return false;
						}else{
							$("#btnProcesarAceptados").attr('disabled','disabled');
							$("#spanEnviarAceptados").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

							var frm = document.getElementById("form1");
						    var form_data = new FormData(frm);
						    form_data.append('parametro',parametro);
						    form_data.append('nombre', archivo_nombre);
						    form_data.append('extension', archivo_ext);
						    form_data.append('periodo', periodo);

							$.ajax({
								url: 'ajax_dev.php',
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
				});

				$("#btnProcesarRechazados").on('click',function(){
					var conf = confirm('¿Confirma?');
					if(conf){
						var archivo_nombre = getFile(file_rechazados.value);
						var archivo_ext = file_rechazados.value.split('.')[1];
						var nombre = archivo_nombre+"."+archivo_ext;
						var periodo = $("#pr_periodo").val();

						if(nombre!=`Rechazados-${inst_rnos}.txt`){
							alert(`El archivo indicado no es Rechazados-${inst_rnos}.txt`);
							return false;
						}else{
							$("#btnProcesarRechazados").attr('disabled','disabled');
							$("#spanEnviar").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

							var parametro = 'procesar_rechazados';
							var frm = document.getElementById("form2");
						    var form_data = new FormData(frm);
						    form_data.append('parametro',parametro);
						    form_data.append('nombre', archivo_nombre);
						    form_data.append('extension', archivo_ext);
						    form_data.append('periodo', periodo);

							$.ajax({
								url: 'ajax_dev.php',
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
				});

				$(document).on('click','.btnQuitarFctPresentacion',function(){
					var id_expo = $(this).data('id_expo');
					var id_lote = $(this).data('id_lote');

					var resp = confirm('¿Confirma quitar este afiliado de la presentación?');

					if(resp){
						var datos = {
							"parametro": "quitar_fct_presentacion",
							"id_expo": id_expo,
							"id_lote": id_lote
						};

						$.ajax({
							url: 'ajax_dev.php',
							type: 'GET',
							data: datos,
							success: function(data){
								if(data=="ok"){
									delete afiliadosSeleccionados[id_expo];
									alert("El afiliado fue quitado de la presentación.");
									if (tablaAfiliados) {
										tablaAfiliados.ajax.reload(null, false);
									}
								}else{
									alert("Ocurrió un error al quitar el afiliado.");
									console.log(data);
								}
							},
							error: function(xhr){
								alert("Ocurrió un error al quitar el afiliado.");
								console.log(xhr.responseText);
							}
						});
					}else{
						return false;
					}
				});

				$(document).on('click','.btnVerAfiliado',function(){
					var id_titular = $(this).data('id_titular');
					var id_afiliado = $(this).data('id_afiliado');

					if(id_titular==0){
						id_titular=id_afiliado;
					}

					var url = "../ver_grupo_familiar/index.php?id_titular="+id_titular+"&id_af_consultado="+id_afiliado;
					abrirEnPestana(url);
				});

				$(document).on('click','.btnSupervisa',function(e){
					e.preventDefault();
					window.open('supervisar_novedades.php','_blank');
				});

				$(document).on('click','#btnExportarExcel',function(){
					var url = "ajax_dev.php?parametro=exportar_afiliados_excel&id_lote="+$("#id_lote").val();
					abrirEnPestana(url);
				});

			});

			function abrirEnPestana(url) {
				var a = document.createElement("a");
				a.target = "_blank";
				a.href = url;
				a.click();
			}

			function inicializarScrollSuperior() {
				const top = document.getElementById('topScrollWrapper');
				const topContent = document.getElementById('topScrollContent');
				const bottom = document.getElementById('divListFctPrestacion');

				if (!top || !topContent || !bottom) return;

				setTimeout(function () {
					let tabla = bottom.querySelector('table');
					if (!tabla) {
						top.style.display = 'none';
						return;
					}

					topContent.style.width = tabla.scrollWidth + 'px';

					if (tabla.scrollWidth > bottom.clientWidth) {
						top.style.display = 'block';
					} else {
						top.style.display = 'none';
					}

					top.scrollLeft = bottom.scrollLeft;
				}, 100);

				top.onscroll = function () {
					bottom.scrollLeft = top.scrollLeft;
				};

				bottom.onscroll = function () {
					top.scrollLeft = bottom.scrollLeft;
				};
			}

			function cargarResumenAgrupado(id_lote) {
			    $.getJSON("ajax_dev.php", { parametro: "resumen_tbt_y_errores", id_lote: id_lote }, function (data) {
				    const resumenTBT = data.resumen_tbt || [];
				    const resumenErrores = data.resumen_errores || [];

				    let htmlResumen = "<div class='row'>";

				    htmlResumen += "<div class='col-md-6'><h5><strong>Resumen por Tipo Beneficiario (TBT):</strong></h5><div class='d-flex flex-wrap gap-2'>";
					resumenTBT.forEach(item => {
					    const tbt = item.tbt || "Sin datos";
					    htmlResumen += `<a href='#' class='btn btn-sm btn-info filtro-tbt' data-tbt='${tbt}'>${tbt}: ${item.cantidad}</a> `;
					});
					htmlResumen += "</div></div>";

					htmlResumen += "<div class='col-md-6'><h5><strong>Resumen por Código de Error:</strong></h5><div class='d-flex flex-wrap gap-2'>";
					resumenErrores.forEach(item => {
					    const cod = item.errores || "Sin errores";
					    htmlResumen += `<a href='#' class='btn btn-sm btn-warning filtro-error' data-error='${cod}'>${cod}: ${item.cantidad}</a> `;
					});
					htmlResumen += "</div></div>";

				    $("#resumen-top").html(htmlResumen).show();
				});
			}

			function genera_tabla_cronologia(){
				$('#divListCronologia').html(
					"<table id='tabListaCronologia' class='table' style='font-size: 11px;'>"
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

				$.getJSON('ajax_dev.php',
					{ parametro: "lst_cronologia_afiliado", id_persona: id_persona },
					function(data){
						$("#tabListaCronologia tbody").html("");

						for(var i=0; i<=data.length-1 ;i++){
							$("#tabListaCronologia tbody").append(
								"<tr>"
									+"<td>"+(i+1)+"</td>"
									+"<td>"+data[i]['fechador']+"</td>"
									+"<td>"+data[i]['movimiento']+"</td>"
								+"</tr>"
							);
						}

						$("#tabListaCronologia").dataTable({
							"bPaginate": false,
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
					}
				);
			}

			function AgruparPorGerenciadora(id_lote){
				$.ajax({
					url: 'ajax_dev.php',
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'lst_afiliados_presentacion_x_gerenciadora',id_lote: id_lote},
				})
				.done(function(data) {
					$('#tabTotal_x_desreguladora tbody').html("");

					for(var i=0; i<=data.length-1 ;i++){
						let desreguladora = data[i].desreguladora;
						let contador = data[i].contador;

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
					url: 'ajax_dev.php',
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'lst_novedades_presentaciones'},
				})
				.done(function(data) {
					let btn_editar_vencimiento = '';
					let btn_exportar = '';
					let btn_supervisa = '';
					let periodo_proceso = '';
					let btn_imp_aceptados = '';
					let btn_imp_rechazados = '';

					$('#tabListado tbody').html("");

					for(var i=0; i<=data.length-1 ;i++){
						let id = data[i].id;
						let descripcion = data[i].descripcion;
						let archivo = data[i].archivo;
						let estado = data[i].estado;
						let q = data[i].q;
						let errores_q = data[i].errores_q;
						let fecha_vencimiento = data[i].fecha_vencimiento;
						let rechazados_q = data[i].rechazados_q;
						let aceptados_q = data[i].aceptados_q;

						btn_editar_vencimiento = '';
						btn_exportar = '';
						btn_supervisa = '';
						periodo_proceso = '';
						btn_imp_aceptados = '';
						btn_imp_rechazados = '';

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
									<a class='btnExportar' data-id_lote='${id}' data-periodo='${descripcion}' data-fecierre='${archivo}'>
										Exportar y enviar por FTP a SSS
									</a>
								</li>
							`;

							periodo_proceso = `class='periodo_proceso'`;

							if(INST_SUPERVISA_NOVEDADES){
								btn_supervisa= `
									<li>
										<a class='btnSupervisa' data-id_lote='${id}' data-periodo='${descripcion}' data-fecierre='${archivo}'>
											Supervisar Lote
										</a>
									</li>
								`;
							}
						}

						if(estado.toLowerCase() == "cerrado"){
							btn_imp_aceptados = `
								<li>
									<a class='btnImpAceptados' data-toggle='modal' data-target='#modalAceptados' data-periodo='${descripcion}'>
										Importar aceptados
									</a>
								</li>
							`;

							btn_imp_rechazados = `
								<li>
									<a class='btnImpRechazados' data-toggle='modal' data-target='#modalRechazados' data-periodo='${descripcion}'>
										Importar rechazados
									</a>
								</li>
							`;
						}

						$('#tabListado tbody').append(`
							<tr ${periodo_proceso}>
								<td>${id}</td>
								<td>
									<div class='btn-group btn-group-default'>
										<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' type='button'>
											<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
										</button>
										<ul class='dropdown-menu'>
											<li>
												<a class='btnVerListaAfils' data-id_lote='${id}' data-periodo='${descripcion}' data-toggle='modal' data-target='#modalListaFct'>
													Ver afiliados
												</a>
											</li>
											<li>
												<a class='btnImpErrores' data-toggle='modal' data-target='#modalErrores' data-periodo='${descripcion}' data-id_lote='${id}'>
													Gestion de Errores
												</a>
											</li>
											${btn_imp_aceptados}
											${btn_imp_rechazados}
											${btn_editar_vencimiento}
											${btn_exportar}
											<li>
												<a class='btnDescargaDevol' data-id_lote='${id}' data-periodo='${descripcion}'>
													Traer devolucion FTP (.ok/.err)
												</a>
											</li>
											${btn_supervisa}
										</ul>
									</div>
								</td>
								<td>${descripcion}</td>
								<td>${formatFechaDDMMYYYY(fecha_vencimiento)}</td>
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

			function formatFechaDDMMYYYY(fecha){
				if(!fecha) return '';
				let partes = fecha.split(/[-T ]/);
				if(partes.length < 3) return fecha;
				let anio = partes[0], mes = partes[1], dia = partes[2];
				if(anio.length !== 4) return fecha;
				return `${dia.padStart(2,'0')}-${mes.padStart(2,'0')}-${anio}`;
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