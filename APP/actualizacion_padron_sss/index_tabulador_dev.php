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
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

		<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
		
		<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
		<link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">
		<link href="vendor/tabulator/tabulator.min.css" rel="stylesheet">
		<script src="vendor/tabulator/tabulator.min.js"></script>

		<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

		<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet">

		<style>
			.aviso-local { position:fixed; top:16px; right:16px; z-index:9999; max-width:360px; padding:12px 16px; border-radius:4px; color:#fff; box-shadow:0 2px 8px rgba(0,0,0,.25); }
			.aviso-local-success { background:#2e7d32; }
			.aviso-local-error { background:#c62828; }
		</style>
		<script>
			function mostrarAvisoLocal(titulo, tipo){
				var aviso = document.createElement('div');
				aviso.className = 'aviso-local aviso-local-' + (tipo === 'success' ? 'success' : 'error');
				aviso.textContent = titulo;
				document.body.appendChild(aviso);
				setTimeout(function(){ if(aviso.parentNode) aviso.parentNode.removeChild(aviso); }, 4000);
			}
		</script>

		<style>
			#divListado{
				margin: 0;
			}
			#divCabecera{
				width: 100%;
				background-color: #37474f;
				color: white;
				padding: 18px 0;
				text-align: left;
				box-shadow: 0 1px 3px rgba(0,0,0,.18);
			}
			.cabecera-contenido { width:94%; margin:0 auto; }
			.cabecera-titulo { font-size:21px; font-weight:400; margin:0; }
			.cabecera-subtitulo { margin-top:3px; font-size:12px; color:#cfd8dc; }
			.barra-recursos { width:94%; margin:14px auto 0; }
			.barra-recursos .btn { margin-right:6px; font-size:12px; border-color:#dadce0; background:#fff; color:#3c4043; }
			.barra-recursos .btn:hover { background:#f8f9fa; border-color:#c5c8cc; }
			#avisoFtpManual { width:94%; margin:12px auto 0; font-size:12px; }
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
				background: #fff;
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
			/* Vistas compactas para trabajar con lotes grandes. */
			#tabListado {
				width: 94% !important;
				margin: 16px auto 12px !important;
				font-size: 12.5px;
				border-collapse: collapse;
				background: #fff;
				color: #202124;
			}
			#tabListado > thead > tr > th {
				padding: 10px 12px;
				font-size: 11.5px;
				font-weight: 500;
				color: #5f6368;
				white-space: nowrap;
				background: #fff;
				border-bottom: 1px solid #dadce0;
			}
			#tabListado > tbody > tr > td {
				padding: 10px 12px;
				line-height: 1.3;
				vertical-align: middle;
				border-top: 0;
				border-bottom: 1px solid #e0e0e0;
			}
			#tabListado > tbody > tr:hover > td { background:#f8fafd; }
			#tabListado small {
				font-size: 10px;
				color: #555;
			}
			#tabListado .btn {
				padding: 5px 8px;
				font-size: 16px;
				color: #5f6368;
				background: transparent;
				border-color: transparent;
			}
			#tabListado .btn:hover { color:#1a73e8; background:#eef3fd; border-color:transparent; }
			.periodo-principal { font-size:13px; font-weight:500; white-space:nowrap; }
			.periodo-secundario { display:block; color:#777; font-size:10px; }
			.estado-circuito { display:inline-block; font-size:11px; font-weight:600; color:#3c4043; }
			.estado-punto { display:inline-block; width:7px; height:7px; margin-right:6px; border-radius:50%; background:#0f9d58; }
			.estado-punto.cerrado { background:#9aa0a6; }
			.dato-fecha { display:block; margin-top:3px; font-size:10px; font-weight:normal; color:#777; white-space:nowrap; }
			.acciones-periodo { white-space:nowrap; text-align:right; }
			.barra-acciones-afiliados { display:flex; flex-wrap:wrap; gap:6px; padding:10px 0; margin-bottom:8px; border-bottom:1px solid #e0e0e0; }
			.barra-acciones-afiliados .btn { font-size:11px; }
			.barra-acciones-afiliados .btn-resumen i { color:#7e57c2; }
			.barra-acciones-afiliados #btnExportarExcel i { color:#0f9d58; }
			.barra-acciones-afiliados #btnEliminarSeleccionados i { color:#fff; }
			#filtrosActivosAfiliados { display:none; margin:8px 0; padding:8px 10px; background:#f7f3ff; border:1px solid #e2d8f5; border-radius:4px; font-size:11px; }
			.filtro-activo { display:inline-block; margin:2px 5px 2px 0; padding:4px 7px; border-radius:12px; background:#7e57c2; color:#fff; }
			.filtro-activo a { color:#fff; margin-left:5px; }
			#contadorRegistrosAfiliados { margin:8px 0; font-size:12px; color:#5f6368; }
			#contadorRegistrosAfiliados strong { font-size:15px; color:#202124; }
			#tabListaAfilPrestacion { background:#fff; color:#202124; border-collapse:collapse !important; }
			#tabListaAfilPrestacion > thead > tr > th { background:#fff !important; color:#5f6368; font-weight:500; border-bottom:1px solid #dadce0 !important; }
			#tabListaAfilPrestacion > tbody > tr > td { border-top:0 !important; border-bottom:1px solid #e0e0e0; }
			#tabListaAfilPrestacion > tbody > tr:hover > td { background:#f8fafd; }
			#modalResumenPeriodo .modal-dialog { width:1100px; max-width:96vw; }
			#modalResumenPeriodo .resumen-lista { margin:0; width:100%; }
			#modalResumenPeriodo .resumen-lista td { padding:8px 10px; border-bottom:1px solid #eee; }
			#modalResumenPeriodo .resumen-lista { font-size:11px; }
			#modalResumenPeriodo .resumen-lista th { padding:7px 10px; color:#5f6368; border-bottom:2px solid #ddd; }
			.resumen-codigo-bloque + .resumen-codigo-bloque { border-top:1px dashed #d7d7d7; margin-top:7px; padding-top:7px; }
			.resumen-codigo-numero { display:inline-block; min-width:34px; font-weight:600; color:#1a73e8; }
			.resumen-actualizado { margin:0 0 12px; font-size:11px; color:#777; }
			#wizardPasoAfiliados p,
			#wizardPasoAfiliados .dataTables_info,
			#wizardPasoAfiliados .dataTables_length,
			#wizardPasoAfiliados .dataTables_filter,
			#wizardPasoAfiliados .dataTables_paginate {
				font-size: 11px;
			}
			#tabListaAfilPrestacion {
				font-family: Consolas, "Courier New", monospace;
				font-size: 10px !important;
			}
			#tabListaAfilPrestacion > thead > tr > th {
				padding: 5px 6px !important;
				font-size: 10px;
				line-height: 1.15;
				white-space: nowrap;
				position:relative;
				user-select:none;
			}
			#tabListaAfilPrestacion > tbody > tr > td {
				padding: 4px 6px !important;
				line-height: 1.2;
				vertical-align: middle;
				box-sizing: border-box;
			}
			#tabListaAfilPrestacion > thead > tr > th { box-sizing:border-box; }
			#tabListaAfilPrestacion_wrapper .dataTables_scrollHeadInner,
			#tabListaAfilPrestacion_wrapper .dataTables_scrollHeadInner table,
			#tabListaAfilPrestacion_wrapper .dataTables_scrollBody table { margin:0 !important; }
			#tabListaAfilPrestacion_wrapper .dataTables_scrollHead { position:sticky; top:16px; z-index:20; background:#f5f5f5; }
			#tabListaAfilPrestacion_wrapper .dataTables_scrollBody { overflow-x:scroll !important; scrollbar-width:none; }
			#tabListaAfilPrestacion_wrapper .dataTables_scrollBody::-webkit-scrollbar { height:0; }
			#topScrollWrapper { position:sticky; top:0; z-index:21; background:#fff; }
			.columna-resize { position:absolute; right:-3px; top:0; width:7px; height:100%; cursor:col-resize; z-index:5; }
			.columna-resize:hover { background:rgba(26,115,232,.2); }
			.controles-grilla { display:flex; align-items:center; gap:7px; margin:7px 0; font-size:11px; color:#3c4043; }
			.controles-grilla input[type=number] { width:74px; height:25px; padding:2px 5px; font-size:11px; }
			.controles-grilla .separador { color:#bbb; }
			#tablaAfiliadosTabulator { border:1px solid #c8c8c8; font-family:Consolas,"Courier New",monospace; font-size:10px; }
			#tablaAfiliadosTabulator .tabulator-header { background:#f3f3f3; border-bottom:1px solid #777; }
			#tablaAfiliadosTabulator .tabulator-col { background:#f3f3f3; }
			#tablaAfiliadosTabulator .tabulator-col-title { font-weight:600; }
			#tablaAfiliadosTabulator .tabulator-cell { padding:3px 5px; border-right:1px solid #ddd; }
			#tablaAfiliadosTabulator .tabulator-row { min-height:24px; border-bottom:1px solid #ddd; }
			#tablaAfiliadosTabulator .tabulator-row:nth-child(even) { background:#fafafa; }
			#tablaAfiliadosTabulator .tabulator-header-filter input { height:21px; padding:1px 4px; font-size:9px; }
			#tablaAfiliadosTabulator .tabulator-tableholder::-webkit-scrollbar { height:0; }
			.acciones-afiliado-tabulator { white-space:nowrap; padding:0 2px; }
			.acciones-afiliado-tabulator .btn { padding:2px 6px; font-size:10px; line-height:1.25; }
			#tablaAfiliadosTabulator .fila-error-ftp { background:#fff8df !important; }
			#tablaAfiliadosTabulator .fila-rechazada { background:#fcebea !important; }
			#tablaAfiliadosTabulator .fila-error-ftp:hover,
			#tablaAfiliadosTabulator .fila-rechazada:hover { filter:brightness(.98); }
			.selector-columnas { min-height:280px; max-height:390px; overflow:auto; padding:6px; border:1px solid #ddd; background:#fafafa; }
			.selector-columnas li { display:flex; align-items:center; gap:5px; margin:3px 0; padding:6px 7px; list-style:none; background:#fff; border:1px solid #ddd; border-radius:3px; font-size:11px; }
			.selector-columnas .nombre-columna { flex:1; }
			.selector-columnas .btn { padding:1px 5px; font-size:10px; }
			body.tema-oscuro { background:#202124; color:#e8eaed; }
			body.tema-oscuro #divCabecera { background:#17191b; }
			body.tema-oscuro #wizardPasoAfiliados,
			body.tema-oscuro #wizardPasoPeriodos { color:#e8eaed; }
			body.tema-oscuro .barra-acciones-afiliados { border-color:#4b4d50; }
			body.tema-oscuro #tablaAfiliadosTabulator { border-color:#555; }
			body.tema-oscuro #tablaAfiliadosTabulator .tabulator-header,
			body.tema-oscuro #tablaAfiliadosTabulator .tabulator-col { background:#303134; color:#e8eaed; }
			body.tema-oscuro #tablaAfiliadosTabulator .tabulator-row { background:#252629; color:#e8eaed; border-color:#444; }
			body.tema-oscuro #tablaAfiliadosTabulator .tabulator-row:nth-child(even) { background:#292a2d; }
			body.tema-oscuro #tablaAfiliadosTabulator .tabulator-cell { border-color:#444; }
			body.tema-oscuro #tablaAfiliadosTabulator .fila-error-ftp { background:#494322 !important; }
			body.tema-oscuro #tablaAfiliadosTabulator .fila-rechazada { background:#4b3030 !important; }
			body.tema-oscuro .modal-content { background:#292a2d; color:#e8eaed; }
			body.tema-oscuro .selector-columnas { background:#202124; border-color:#555; }
			body.tema-oscuro .selector-columnas li { background:#303134; border-color:#555; }
			#tabListaAfilPrestacion .btn,
			#wizardPasoAfiliados .dataTables_wrapper .btn {
				padding: 2px 6px;
				font-size: 10px;
			}
			@media (max-width: 767px) {
				#tabListado { width: 100% !important; }
				#wizardPasoAfiliados { width: 100%; padding: 0 6px; }
			}
			#contenedorEliminarSeleccionados{
				clear: both;
				margin: 10px 0;
			}
			#avisoBuscandoTabla{
				display:none;
				position: fixed;
				inset: 0;
				z-index: 99999;
				background: rgba(45,49,53,.48);
				color: #fff;
				font-size: 16px;
				cursor: wait;
			}
			#avisoBuscandoTabla .cargando-centro {
				position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
				min-width:190px; padding:18px 24px; text-align:center;
				background:rgba(32,33,36,.94); border-radius:8px; box-shadow:0 4px 18px rgba(0,0,0,.3);
			}
			.wizard-navegacion {
				width: 94%;
				margin: 14px auto 4px;
				display: flex;
				align-items: center;
				gap: 8px;
				font-size: 12px;
			}
			.wizard-indicador {
				padding: 6px 12px;
				border-radius: 16px;
				background: #e9ecef;
				color: #666;
			}
			.wizard-indicador.activo {
				background: #31b0d5;
				color: #fff;
				font-weight: bold;
			}
			.wizard-separador { color: #aaa; }
			.wizard-paso { display: none; }
			.wizard-paso.activo { display: block; }
			#wizardPasoAfiliados {
				width: 98%;
				margin: 0 auto 20px;
			}
			#wizardPasoAfiliados .wizard-cabecera {
				padding: 10px 4px;
				border-bottom: 1px solid #ddd;
				margin-bottom: 10px;
			}
			#wizardPasoAfiliados .panel-resumen {
				margin-bottom: 10px;
			}
			#wizardPasoAfiliados .panel-resumen .panel-body {
				padding: 8px 12px;
			}
		</style>
		<title>Novedades</title>
	</head>
	<body>
		<div id="divCabecera">
			<div class="cabecera-contenido">
				<h1 class="cabecera-titulo">Presentaciones mensuales a la SSS</h1>
				<div class="cabecera-subtitulo">Control de novedades, envíos y devoluciones del padrón · Prueba de grilla Tabulator</div>
			</div>
		</div>
		<div class="wizard-navegacion">
			<span id="wizardIndicadorPeriodos" class="wizard-indicador activo">1. Períodos</span>
			<span class="wizard-separador"><i class="fa fa-chevron-right"></i></span>
			<span id="wizardIndicadorAfiliados" class="wizard-indicador">2. Afiliados y control</span>
		</div>
		<div id="wizardPasoPeriodos" class="wizard-paso activo">
		<div id="divListado">
			<div class="barra-recursos">
				<a class="btn btn-default btn-sm" href="https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/cronograma_ftp.pdf" target="_blank">
					<i class="fa fa-calendar"></i> Cronograma FTP
				</a>
				<a class="btn btn-default btn-sm" href="https://seguro.sssalud.gob.ar/descargas/rnos/restricto/padron/InstructivoObrasSociales.pdf" target="_blank">
					<i class="fa fa-file-text-o"></i> Instructivo de novedades
				</a>
			</div>
			<div id="avisoFtpManual" class="alert alert-warning" style="display:none;"></div>
			<table id="tabListado" class="table">
				<thead>
					<tr>
						<th>Período</th>
						<th>Vencimiento</th>
						<th>Estado</th>
						<th style="text-align: right;">Movimientos</th>
						<th style="text-align: right;">Error FTP</th>
						<th style="text-align: right;">Aceptadas</th>
						<th style="text-align: right;">Rechazadas</th>
						<th style="text-align: right;">Acciones</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<br>
			<div style="text-align: center;"></div>
		</div>
		</div>

		<div id="avisoBuscandoTabla">
			<div class="cargando-centro"><i class="fa fa-spinner fa-spin"></i><br>Cargando resultados...</div>
		</div>

		<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modalAceptados' style="display: none;">
		  Launch demo modal
		</button>

		<div id="modalNuevoPeriodo" class="modal fade" role="dialog">
		  <div class="modal-dialog" style="width: 1000px;">
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

		<section id="wizardPasoAfiliados" class="wizard-paso">
			<div class="wizard-cabecera clearfix">
				<h4 style="margin:0;">Afiliados de la presentación <small>Período <b><span id="s_periodo"></span></b></small></h4>
			</div>
			<div class="barra-acciones-afiliados">
				<input type="hidden" id="id_lote">
				<button type="button" id="btnVolverPeriodos" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> Volver a períodos</button>
				<button type="button" id="btnEliminarSeleccionados" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Quitar afiliados de la presentación</button>
				<a id="btnExportarExcel" target="_blank" class="btn btn-default btn-sm">
					<i class="fa fa-file-excel-o"></i> Exportar todo a Excel
				</a>
				<button type="button" class="btn btn-default btn-sm btnResumenPeriodo btn-resumen" data-resumen="movimientos"><i class="fa fa-exchange"></i> Resumen por tipo de movimiento</button>
				<button type="button" class="btn btn-default btn-sm btnResumenPeriodo btn-resumen" data-resumen="errores"><i class="fa fa-exclamation-triangle"></i> Resumen de códigos de error FTP</button>
				<button type="button" class="btn btn-default btn-sm btnResumenPeriodo btn-resumen" data-resumen="rechazos"><i class="fa fa-ban"></i> Resumen de códigos de rechazos</button>
				<button type="button" class="btn btn-default btn-sm btnResumenPeriodo btn-resumen" data-resumen="gerenciadoras"><i class="fa fa-building-o"></i> Resumen por gerenciadora</button>
				<button type="button" id="btnConfigurarGrilla" class="btn btn-default btn-sm" title="Configurar tabla y apariencia"><i class="fa fa-cog"></i> Configuración</button>
			</div>
			<div id="filtrosActivosAfiliados"><strong>Filtros aplicados:</strong> <span class="lista-filtros"></span> <a href="#" class="limpiar-filtros-afiliados">Quitar todos</a></div>
			<div id="contadorRegistrosAfiliados"><strong>0</strong> registros encontrados</div>
			<div class="controles-grilla">
				<label style="margin:0;font-weight:normal"><input type="checkbox" id="limitarFilasAfiliados" checked> Limitar filas</label>
				<input type="number" id="cantidadFilasAfiliados" class="form-control input-sm" min="1" max="5000" value="10">
				<span>resultados</span><span class="separador">|</span>
				<input type="search" id="busquedaGlobalTabulator" class="form-control input-sm" style="width:210px" placeholder="Buscar en todas las columnas">
				<span class="separador">|</span><span>Los anchos y el orden de columnas se guardan automáticamente</span>
			</div>
			<div id="resumen-top" style="display:none;"></div>
			<table id="tabTotal_x_desreguladora" style="display:none;"><tbody></tbody></table>
			<div id="topScrollWrapper" style="overflow-x:auto; overflow-y:hidden; height:16px; margin-bottom:8px; display:none;">
				<div id="topScrollContent" style="height:1px;"></div>
			</div>
			<div id="divListFctPrestacion" class="table-responsive"></div>
		</section>

		<div id="modalConfigurarGrilla" class="modal fade" role="dialog">
			<div class="modal-dialog" style="width:850px;max-width:96vw"><div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-cog"></i> Configuración de la tabla</h4></div>
				<div class="modal-body">
					<div class="form-inline" style="margin-bottom:12px"><strong>Apariencia:</strong> <button type="button" class="btn btn-default btn-sm btnTemaGrilla" data-tema="claro"><i class="fa fa-sun-o"></i> Claro</button> <button type="button" class="btn btn-default btn-sm btnTemaGrilla" data-tema="oscuro"><i class="fa fa-moon-o"></i> Oscuro</button></div>
					<div class="row">
						<div class="col-sm-6"><div class="clearfix"><strong>Columnas disponibles</strong><button type="button" id="btnAgregarTodasColumnas" class="btn btn-xs btn-default pull-right">Agregar todas <i class="fa fa-angle-double-right"></i></button></div><ul id="columnasOcultas" class="selector-columnas"></ul></div>
						<div class="col-sm-6"><div class="clearfix"><strong>Columnas visibles</strong><button type="button" id="btnQuitarTodasColumnas" class="btn btn-xs btn-default pull-right"><i class="fa fa-angle-double-left"></i> Quitar todas</button></div><ul id="columnasVisibles" class="selector-columnas"></ul><small class="text-muted">Usá las flechas para cambiar el orden.</small></div>
					</div>
				</div>
				<div class="modal-footer"><button type="button" id="btnRestablecerGrilla" class="btn btn-default pull-left"><i class="fa fa-undo"></i> Restablecer diseño</button><button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button></div>
			</div></div>
		</div>

		<div id="modalResumenPeriodo" class="modal fade" role="dialog">
			<div class="modal-dialog"><div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="tituloResumenPeriodo">Resumen</h4>
				</div>
				<div class="modal-body" id="contenidoResumenPeriodo"></div>
				<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button></div>
			</div></div>
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
			let capacidadFtp = {automatico:false, manual:true, estado:'consultando', mensaje:'Consultando disponibilidad FTPS...'};
			let resumenPeriodoActual = {resumen_movimientos:[],resumen_errores:[],resumen_rechazos:[]};
			let resumenGerenciadorasActual = [];
			let tablaAfiliados = null;
			let afiliadosSeleccionados = {};
			let filtrosAfiliados = {};
			aplicarTemaGrilla(localStorage.getItem('sss-tema-interfaz') || 'claro');

			$(document).ready(function(){

				$.ajax({
					url:'ajax_dev.php',
					type:'GET',
					dataType:'json',
					data:{parametro:'ftp_sss_capacidades'}
				}).done(function(resp){
					capacidadFtp = resp;
				}).fail(function(){
					capacidadFtp = {automatico:false,manual:true,estado:'sin_respuesta',mensaje:'No se pudo validar el acceso FTPS. Se habilitó la contingencia manual.'};
				}).always(function(){
					actualizarAvisoFtp();
					ListarLotes();
				});

				function mostrarPasoWizard(paso) {
					var verAfiliados = paso === 'afiliados';
					$('#wizardPasoPeriodos').toggleClass('activo', !verAfiliados);
					$('#wizardPasoAfiliados').toggleClass('activo', verAfiliados);
					$('#wizardIndicadorPeriodos').toggleClass('activo', !verAfiliados);
					$('#wizardIndicadorAfiliados').toggleClass('activo', verAfiliados);
					window.scrollTo(0, 0);
					if (verAfiliados) {
						setTimeout(function(){
							if (tablaAfiliados && tablaAfiliados.redraw) tablaAfiliados.redraw(true);
							inicializarScrollSuperiorTabulator();
						}, 100);
					}
				}

				$('#btnVolverPeriodos').on('click', function(){
					mostrarPasoWizard('periodos');
					$('#wizardIndicadorAfiliados').text('2. Afiliados y control');
					if (window.history && window.history.replaceState) {
						window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
					}
				});

				function genera_tabla(){
					$('#divListFctPrestacion').html("<div id='tablaAfiliadosTabulator'></div>");
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

				function actualizarFiltrosVisibles(){
					var etiquetas = {
						alta_titular:'Altas de titulares',baja_titular:'Bajas de titulares',
						modificacion_titular:'Modificaciones de titulares',alta_familiar:'Altas de familiares',
						baja_familiar:'Bajas de familiares',modificacion_familiar:'Modificaciones de familiares'
					};
					var nombres = {movimiento:'Movimiento',persona:'Persona',error:'Error FTP',rechazo:'Rechazo',gerenciadora:'Gerenciadora'};
					var html = '';
					$.each(filtrosAfiliados,function(tipo,valor){
						var mostrar = tipo === 'movimiento' && etiquetas[valor] ? etiquetas[valor] : valor;
						if(valor === '__con_error__') mostrar = 'Con errores';
						if(valor === '__con_rechazo__') mostrar = 'Con rechazos';
						if(tipo === 'persona') mostrar = valor === 'titular' ? 'Titulares' : 'Familiares';
						html += "<span class='filtro-activo'>"+nombres[tipo]+": "+$('<div>').text(mostrar).html()+" <a href='#' class='quitar-filtro-afiliados' data-tipo='"+tipo+"'>&times;</a></span>";
					});
					$('#filtrosActivosAfiliados .lista-filtros').html(html);
					$('#filtrosActivosAfiliados').toggle(Object.keys(filtrosAfiliados).length > 0);
				}

				function llena_tabla(id_lote, inst_rnos, filtroInicial) {
					$('#avisoBuscandoTabla').show();
					filtrosAfiliados = {};
					if(filtroInicial === '__errores_ftp__') filtrosAfiliados.error = '__con_error__';
					else if(filtroInicial === '__rechazados__') filtrosAfiliados.rechazo = '__con_rechazo__';
					else if(String(filtroInicial).indexOf('cat:') === 0) filtrosAfiliados.movimiento = String(filtroInicial).substring(4);
					actualizarFiltrosVisibles();
					afiliadosSeleccionados = {};

					$.getJSON('ajax_dev.php', {
						parametro:'lst_afiliados_presentacion_ssp', id_lote:id_lote, modo:'tabulator',
						start:0, length:-1, draw:1, search:{value:''}
					}).done(function(respuesta){
						$('#avisoBuscandoTabla').hide();
						try {
						if(typeof respuesta === 'string') respuesta = JSON.parse(respuesta);
						var filas = respuesta && respuesta.data ? respuesta.data : [];
						if(!Array.isArray(filas) && filas && typeof filas === 'object') filas = Object.keys(filas).map(function(clave){ return filas[clave]; });
						if(!filas.length && parseInt(respuesta.recordsTotal || 0,10) > 0) throw new Error('La respuesta informó '+respuesta.recordsTotal+' registros pero no entregó una colección utilizable.');
						$('#contadorRegistrosAfiliados').html('<strong>'+filas.length+'</strong> registros cargados');
						tablaAfiliados = new Tabulator('#tablaAfiliadosTabulator', {
							data:filas,
							index:'id_expo',
							layout:'fitData',
							height:'62vh',
							pagination:true,
							paginationMode:'local',
							paginationSize:10,
							paginationCounter:function(pageSize,currentRow,currentPage,totalRows){
								var desde=totalRows ? currentRow : 0;
								var hasta=Math.min(totalRows,currentRow+pageSize-1);
								return 'Mostrando '+desde+'-'+hasta+' de '+totalRows+' registros';
							},
							locale:'es-ar',
							langs:{'es-ar':{pagination:{first:'Primera',first_title:'Primera página',last:'Última',last_title:'Última página',prev:'Anterior',prev_title:'Página anterior',next:'Siguiente',next_title:'Página siguiente',page_size:'Filas por página'}}},
							movableColumns:true,
							selectableRows:true,
							selectableRowsPersistence:true,
							persistence:{columns:true,sort:true},
							persistenceID:'sss-afiliados-tabulator-v3',
							rowFormatter:function(row){
								var elemento=row.getElement(), data=row.getData();
								elemento.classList.remove('fila-error-ftp','fila-rechazada');
								if($.trim(data.rechazos || '')) elemento.classList.add('fila-rechazada');
								else if($.trim(data.errores || '')) elemento.classList.add('fila-error-ftp');
							},
							initialSort:[{column:'fecha_movimiento',dir:'desc'}],
							placeholder:'No se encontraron afiliados para los filtros aplicados',
							columns:[
								{title:'Seleccionar',field:'_seleccionar',formatter:'rowSelection',titleFormatter:'rowSelection',hozAlign:'center',headerSort:false,width:34,frozen:true},
								{title:'#',field:'numero',sorter:'number',hozAlign:'right',width:44,frozen:true},
								{title:'Gerenciadora',field:'gerenciadora',headerFilter:'input',width:125},
								{title:'CUIL titular',field:'cuil_titular',headerFilter:'input',width:105},
								{title:'Parentesco',field:'parentesco',headerFilter:'input',width:90},
								{title:'CUIL',field:'cuil',headerFilter:'input',width:105},
								{title:'DNI',field:'dni',headerFilter:'input',sorter:'number',width:82},
								{title:'Apellido y nombre',field:'ayn',headerFilter:'input',width:175},
								{title:'Sexo',field:'sexo',headerFilter:'input',hozAlign:'center',width:55,visible:false},
								{title:'Edad',field:'edad',headerFilter:'input',sorter:'number',hozAlign:'right',width:58,visible:false},
								{title:'Fecha nacimiento',field:'fecha_nacimiento',headerFilter:'input',sorter:ordenarFechaTabulator,width:105,visible:false},
								{title:'Incapacidad',field:'incapacidad',headerFilter:'input',width:82,visible:false},
								{title:'Tipo beneficiario',field:'tipo_beneficiario',headerFilter:'input',width:105,visible:false},
								{title:'Movimiento',field:'tipo_movimiento',headerFilter:'input',width:85},
								{title:'Fecha movimiento',field:'fecha_movimiento',headerFilter:'input',sorter:ordenarFechaTabulator,width:110},
								{title:'Error FTP',field:'errores',headerFilter:'input',width:92},
								{title:'Rechazo',field:'rechazos',headerFilter:'input',width:92},
								{title:'Descripción SSS',field:'descripcion_sss',headerFilter:'input',width:260},
								{title:'Acción sugerida',field:'accion_sugerida',headerFilter:'input',width:280},
								{title:'Acciones',field:'_acciones',formatter:formatearAccionesTabulator,headerSort:false,width:105,hozAlign:'center',frozen:true}
							]
						});
						tablaAfiliados.on('rowSelectionChanged', function(data){
							afiliadosSeleccionados = {};
							data.forEach(function(fila){ afiliadosSeleccionados[fila.id_expo] = true; });
						});
						tablaAfiliados.on('dataLoaded', function(){ actualizarContadorTabulator(); });
						tablaAfiliados.on('dataFiltered', function(){ actualizarContadorTabulator(); });
						tablaAfiliados.on('renderComplete', function(){ actualizarContadorTabulator(); inicializarScrollSuperiorTabulator(); });
						tablaAfiliados.on('tableBuilt', function(){
							actualizarContadorTabulator();
							inicializarScrollSuperiorTabulator();
							if(Object.keys(filtrosAfiliados).length || $.trim($('#busquedaGlobalTabulator').val() || '')) aplicarFiltrosTabulator();
						});
						} catch(errorTabulator) {
							console.error('No se pudo inicializar Tabulator', errorTabulator);
							$('#divListFctPrestacion').prepend("<div class='alert alert-danger'>No se pudo inicializar la grilla Tabulator: "+$('<div>').text(errorTabulator.message || errorTabulator).html()+"</div>");
						}
					}).fail(function(xhr){
						var mensaje='No se pudo cargar la presentación para Tabulator.';
						try { var error=JSON.parse(xhr.responseText); if(error.mensaje) mensaje=error.mensaje; } catch(e) {}
						$('#divListFctPrestacion').html("<div class='alert alert-danger'>"+$('<div>').text(mensaje).html()+"</div>");
					}).always(function(){ $('#avisoBuscandoTabla').stop(true,true).hide(); });

					$('#limitarFilasAfiliados, #cantidadFilasAfiliados').off('.tabulator').on('change.tabulator', function(){
						if(!tablaAfiliados || !tablaAfiliados.setPageSize) return;
						var limitar=$('#limitarFilasAfiliados').is(':checked');
						var cantidad=Math.max(1,parseInt($('#cantidadFilasAfiliados').val()||10,10));
						$('#cantidadFilasAfiliados').prop('disabled',!limitar);
						tablaAfiliados.setPageSize(limitar ? cantidad : Math.max(1,tablaAfiliados.getDataCount('active')));
					});
					$('#busquedaGlobalTabulator').off('.tabulator').on('input.tabulator', function(){ aplicarFiltrosTabulator(); });
					return;
					$('#avisoBuscandoTabla').show();
				    const parametro = "lst_afiliados_presentacion_ssp";
				    filtrosAfiliados = {};
				    if(filtroInicial === '__errores_ftp__') filtrosAfiliados.error = '__con_error__';
				    else if(filtroInicial === '__rechazados__') filtrosAfiliados.rechazo = '__con_rechazo__';
				    else if(String(filtroInicial).indexOf('cat:') === 0) filtrosAfiliados.movimiento = String(filtroInicial).substring(4);
				    actualizarFiltrosVisibles();

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
							data: function(d){
								d.parametro = parametro;
								d.id_lote = id_lote;
								d.filtros = filtrosAfiliados;
							},
							error: function(xhr){
								var mensaje = 'No se pudo cargar la presentacion.';
								try {
									var r = JSON.parse(xhr.responseText);
									if(r.mensaje) mensaje = r.mensaje;
									if(r.detalle) mensaje += ' Detalle tecnico: '+r.detalle;
								} catch(e) {}
								$('#avisoBuscandoTabla').hide();
								$('#divListFctPrestacion').prepend("<div class='alert alert-danger'>"+$('<div>').text(mensaje).html()+"</div>");
							}
						},
				        dom: '<"row mb-2"<"col-sm-12 text-right"f>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
				        scrollX: true,
				        scrollCollapse: true,
				        order: [[14, 'desc']],
						columnDefs: [
							{targets:[0],width:'32px',className:'text-center',orderable:false},
							{targets:[1],width:'38px',className:'text-right'},
							{targets:[2],width:'115px'},
							{targets:[3,5],width:'94px'},
							{targets:[4],width:'78px'},
							{targets:[6],width:'72px'},
							{targets:[7],width:'150px'},
							{targets:[8,9],width:'42px',className:'text-center'},
							{targets:[10,14],width:'88px'},
							{targets:[11,12,13],width:'82px'},
							{targets:[15,16],width:'92px'},
							{targets:[17],width:'240px',orderable:false},
							{targets:[18],width:'260px',orderable:false},
							{targets:[19],width:'58px',className:'text-center',orderable:false}
						],
				        lengthMenu: [[10, 50, 100, 200, 500, 1000, 2000], [10, 50, 100, 200, 500, 1000, 2000]],
				        pageLength: 10,
						search: { search: '' },
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

							if (tablaAfiliados) tablaAfiliados.columns.adjust();
							instalarRedimensionColumnas();
							inicializarScrollSuperior();
				        }
				    });

				    $('#tabListaAfilPrestacion').off('processing.dt').on('processing.dt', function (e, settings, processing) {
						if (processing) {
							$('#avisoBuscandoTabla').stop(true,true).fadeIn(100);
						} else {
							$('#avisoBuscandoTabla').stop(true,true).fadeOut(100);
						}
					});
				    $('#tabListaAfilPrestacion').off('xhr.dt').on('xhr.dt', function(e, settings, json){
				    	if(!json) return;
				    	var filtrados = parseInt(json.recordsFiltered || 0,10);
				    	var totales = parseInt(json.recordsTotal || 0,10);
				    	$('#contadorRegistrosAfiliados').html('<strong>'+filtrados+'</strong> registros encontrados'+(filtrados !== totales ? ' de '+totales+' totales' : ''));
					});

					$('#limitarFilasAfiliados, #cantidadFilasAfiliados').off('.limiteFilas').on('change.limiteFilas', function(){
						if(!tablaAfiliados) return;
						var limitar = $('#limitarFilasAfiliados').is(':checked');
						var cantidad = Math.max(1, parseInt($('#cantidadFilasAfiliados').val() || 10,10));
						$('#cantidadFilasAfiliados').prop('disabled', !limitar);
						tablaAfiliados.page.len(limitar ? cantidad : -1).draw();
					});
				}

				$(document).on('click','#btnConfigurarGrilla',function(){
					renderizarSelectorColumnas();
					$('#modalConfigurarGrilla').modal('show');
				});
				$(document).on('click','.btnTemaGrilla',function(){ aplicarTemaGrilla($(this).data('tema')); });
				$(document).on('click','.accion-mostrar-columna',function(){
					var columna=tablaAfiliados && tablaAfiliados.getColumn($(this).data('campo')); if(columna) columna.show(); renderizarSelectorColumnas();
				});
				$(document).on('click','.accion-ocultar-columna',function(){
					var columna=tablaAfiliados && tablaAfiliados.getColumn($(this).data('campo')); if(columna) columna.hide(); renderizarSelectorColumnas();
				});
				$(document).on('click','.accion-subir-columna, .accion-bajar-columna',function(){
					if(!tablaAfiliados) return;
					var campo=$(this).data('campo'), visibles=tablaAfiliados.getColumns().filter(function(c){return c.isVisible();});
					var indice=visibles.findIndex(function(c){return c.getField()===campo;});
					var subir=$(this).hasClass('accion-subir-columna'), destino=visibles[indice+(subir?-1:1)];
					if(indice>=0 && destino) tablaAfiliados.getColumn(campo).move(destino,!subir);
					renderizarSelectorColumnas();
				});
				$(document).on('click','#btnAgregarTodasColumnas',function(){ if(tablaAfiliados) tablaAfiliados.getColumns().forEach(function(c){c.show();}); renderizarSelectorColumnas(); });
				$(document).on('click','#btnQuitarTodasColumnas',function(){
					if(tablaAfiliados) tablaAfiliados.getColumns().forEach(function(c){if(['_seleccionar','numero','_acciones'].indexOf(c.getField())<0)c.hide();}); renderizarSelectorColumnas();
				});
				$(document).on('click','#btnRestablecerGrilla',function(){
					if(!confirm('¿Restablecer columnas, orden, anchos y apariencia?')) return;
					for(var i=localStorage.length-1;i>=0;i--){var clave=localStorage.key(i);if(clave && clave.indexOf('sss-afiliados-tabulator-v3')>=0)localStorage.removeItem(clave);}
					localStorage.removeItem('sss-tema-interfaz');
					window.location.reload();
				});

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
					filtrosAfiliados.tbt = $(this).data('tbt');
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.filtro-error', function (e) {
					e.preventDefault();
					filtrosAfiliados.error = decodeURIComponent($(this).attr('data-error') || '');
					actualizarFiltrosVisibles();
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.filtro-movimiento', function (e) {
					e.preventDefault();
					filtrosAfiliados.movimiento = decodeURIComponent($(this).attr('data-categoria') || '');
					actualizarFiltrosVisibles();
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.filtro-rechazo, .filtro-gerenciadora', function (e) {
					e.preventDefault();
					var tipo = $(this).hasClass('filtro-rechazo') ? 'rechazo' : 'gerenciadora';
					filtrosAfiliados[tipo] = decodeURIComponent($(this).attr('data-filtro') || '');
					actualizarFiltrosVisibles();
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.filtro-persona', function(e){
					e.preventDefault();
					filtrosAfiliados.persona = decodeURIComponent($(this).attr('data-persona') || '');
					actualizarFiltrosVisibles();
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.btnResumenPeriodo', function(){
					var tipo = $(this).data('resumen');
					var titulo = '';
					var filas = [];
					if(tipo === 'movimientos'){
						titulo = 'Resumen por tipo de movimiento';
						var matrizMovimientos = {
							alta:{titular:0,familiar:0}, baja:{titular:0,familiar:0}, modificacion:{titular:0,familiar:0}
						};
						(resumenPeriodoActual.resumen_movimientos || []).forEach(function(item){
							var partes = String(item.categoria || '').split('_');
							if(matrizMovimientos[partes[0]] && matrizMovimientos[partes[0]][partes[1]] !== undefined){
								matrizMovimientos[partes[0]][partes[1]] = parseInt(item.cantidad || 0,10);
							}
						});
						filas = [
							{etiqueta:'Altas',clave:'alta',datos:matrizMovimientos.alta},
							{etiqueta:'Bajas',clave:'baja',datos:matrizMovimientos.baja},
							{etiqueta:'Modificaciones',clave:'modificacion',datos:matrizMovimientos.modificacion}
						];
					} else if(tipo === 'errores'){
						titulo = 'Resumen de códigos de error FTP';
						filas = (resumenPeriodoActual.resumen_errores || []).map(function(item){
							return {etiqueta:item.errores || 'Sin errores',cantidad:item.cantidad,descripcion:item.descripcion || '',accion:item.accion || '',clase:'filtro-error',atributo:'data-error',filtro:item.errores || ''};
						});
					} else if(tipo === 'rechazos'){
						titulo = 'Resumen de códigos de rechazos';
						filas = (resumenPeriodoActual.resumen_rechazos || []).map(function(item){
							return {etiqueta:item.rechazos || 'Sin rechazos',cantidad:item.cantidad,descripcion:item.descripcion || '',accion:item.accion || '',clase:'filtro-rechazo',atributo:'data-filtro',filtro:item.rechazos || ''};
						});
					} else {
						titulo = 'Resumen por gerenciadora';
						filas = resumenGerenciadorasActual.map(function(item){
							return {etiqueta:item.desreguladora || 'Sin gerenciadora',cantidad:item.contador,titulares:item.titulares,familiares:item.familiares,clase:'filtro-gerenciadora',atributo:'data-filtro',filtro:item.desreguladora || ''};
						});
					}
					var html = '';
					if(tipo === 'errores' || tipo === 'rechazos'){
						var fechaActualizacion = resumenPeriodoActual.fecha_actualizacion_errores || '';
						html += "<p class='resumen-actualizado'><i class='fa fa-clock-o'></i> Última actualización de presentación/errores: <strong>"+(fechaActualizacion ? formatFechaHora(fechaActualizacion) : 'Sin actualización registrada')+"</strong></p>";
					}
					html += "<table class='resumen-lista'>";
					if(tipo === 'movimientos') html += "<thead><tr><th>Movimiento</th><th class='text-right'>Titulares</th><th class='text-right'>Familiares</th><th class='text-right'>Todos</th></tr></thead>";
					if(tipo === 'errores' || tipo === 'rechazos') html += "<thead><tr><th class='text-right' style='width:70px'>Cantidad</th><th>Descripción del instructivo</th><th>Acción sugerida</th></tr></thead>";
					if(tipo === 'gerenciadoras') html += "<thead><tr><th>Gerenciadora</th><th class='text-right'>Titulares</th><th class='text-right'>Familiares</th><th class='text-right'>Todos</th></tr></thead>";
					if(!filas.length) html += "<tr><td class='text-muted'>No hay datos para mostrar.</td></tr>";
					filas.forEach(function(fila){
						if(tipo === 'movimientos'){
							var titulares = parseInt(fila.datos.titular || 0,10), familiares = parseInt(fila.datos.familiar || 0,10);
							var linkTit = "<a href='#' data-dismiss='modal' class='filtro-movimiento' data-categoria='"+fila.clave+"_titular'>"+titulares+"</a>";
							var linkFam = "<a href='#' data-dismiss='modal' class='filtro-movimiento' data-categoria='"+fila.clave+"_familiar'>"+familiares+"</a>";
							html += "<tr><td>"+fila.etiqueta+"</td><td class='text-right'>"+linkTit+"</td><td class='text-right'>"+linkFam+"</td><td class='text-right'><strong>"+(titulares+familiares)+"</strong></td></tr>";
							return;
						}
						var etiqueta = $('<div>').text(fila.etiqueta).html();
						var filtro = encodeURIComponent(fila.filtro || '');
						var enlace = "<a href='#' data-dismiss='modal' class='"+fila.clase+"' "+fila.atributo+"='"+filtro+"'>"+etiqueta+"</a>";
						if(tipo === 'errores' || tipo === 'rechazos'){
							var descripcion = $('<div>').text(fila.descripcion || etiqueta || 'Sin descripción catalogada').html().replace(/\s\|\s/g,"</div><div class='resumen-codigo-bloque'>");
							descripcion = "<div class='resumen-codigo-bloque'>"+descripcion+"</div>";
							html += "<tr><td class='text-right'><strong>"+parseInt(fila.cantidad || 0,10)+"</strong></td><td><a href='#' data-dismiss='modal' class='"+fila.clase+"' "+fila.atributo+"='"+filtro+"'>"+descripcion+"</a></td><td>"+$('<div>').text(fila.accion || 'Revisar según instructivo SSS').html().replace(/\s\|\s/g,'<br>')+"</td></tr>";
						} else if(tipo === 'gerenciadoras'){
							html += "<tr><td>"+enlace+"</td><td class='text-right'>"+parseInt(fila.titulares || 0,10)+"</td><td class='text-right'>"+parseInt(fila.familiares || 0,10)+"</td><td class='text-right'><strong>"+parseInt(fila.cantidad || 0,10)+"</strong></td></tr>";
						} else {
							html += "<tr><td>"+enlace+"</td><td class='text-right'><strong>"+parseInt(fila.cantidad || 0,10)+"</strong></td></tr>";
						}
					});
					if(tipo === 'movimientos'){
						var tt=0, tf=0; filas.forEach(function(f){ tt+=parseInt(f.datos.titular||0,10); tf+=parseInt(f.datos.familiar||0,10); });
						html += "<tfoot><tr><th>Todos</th><th class='text-right'><a href='#' data-dismiss='modal' class='filtro-persona' data-persona='titular'>"+tt+"</a></th><th class='text-right'><a href='#' data-dismiss='modal' class='filtro-persona' data-persona='familiar'>"+tf+"</a></th><th class='text-right'>"+(tt+tf)+"</th></tr></tfoot>";
					}
					html += "</table>";
					$('#tituloResumenPeriodo').text(titulo+' · Período '+$('#s_periodo').text());
					$('#contenidoResumenPeriodo').html(html);
					$('#modalResumenPeriodo').modal('show');
				});

				$(document).on('click', '.limpiar-filtros-afiliados', function (e) {
					e.preventDefault();
					filtrosAfiliados = {};
					actualizarFiltrosVisibles();
					$('#busquedaGlobalTabulator').val('');
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '.quitar-filtro-afiliados', function(e){
					e.preventDefault();
					delete filtrosAfiliados[$(this).data('tipo')];
					actualizarFiltrosVisibles();
					aplicarFiltrosTabulator();
				});

				$(document).on('click', '#btnEliminarSeleccionados', function () {
					let ids = obtenerIdsSeleccionados();
					let id_lote = $("#id_lote").val();

					if (ids.length === 0) {
						alert('Seleccioná al menos un afiliado para eliminar.');
						return false;
					}

					let confirmacion = prompt('Esta acción quitará '+ids.length+' registro(s) de la presentación. Para confirmar escribí "quiero".');

					if ($.trim(confirmacion || '').toLowerCase() !== 'quiero') {
						alert('La eliminación fue cancelada.');
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
							$('#btnEliminarSeleccionados').html('<i class="fa fa-trash"></i> Quitar afiliados de la presentación');

							if ($.trim(data) === 'ok') {
								alert('Los registros seleccionados fueron quitados de la presentación.');
								afiliadosSeleccionados = {};
								$('#checkTodosAfiliados').prop('checked', false);
								if(tablaAfiliados && tablaAfiliados.deleteRow) tablaAfiliados.deleteRow(ids);
							} else {
								alert('Ocurrió un error al eliminar los registros.');
								console.log(data);
							}
						},
						error: function (xhr) {
							$('#btnEliminarSeleccionados').removeAttr('disabled');
							$('#btnEliminarSeleccionados').html('<i class="fa fa-trash"></i> Quitar afiliados de la presentación');
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
							mostrarAvisoLocal('Cambio Guardado', 'success');
						}else{
							mostrarAvisoLocal('Error.', 'error');
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
								progresoFTP_finalizar('danger', 'La devolucion fue descargada, pero no se pudieron importar los errores. Reintentar el circuito de envio.');
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
									progresoFTP_finalizar('warning', 'El archivo se envio, pero la SSS todavia no genero la devolucion inmediata. Reintentar el control mas tarde desde el circuito de envio.');
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
								activarContingenciaFtp(dataEnviar.mensaje);
								return;
							}

							progresoFTP_setEstado('enviar', 'ok');
							setTimeout(intentarTraerDevolucion, 5000);
						})
						.fail(function(){
							progresoFTP_setEstado('enviar', 'error');
							progresoFTP_finalizar('danger', 'Error de comunicacion enviando el archivo por FTP.');
							activarContingenciaFtp('No se pudo establecer conexión con el FTPS de la SSS.');
						});
					})
					.fail(function(){
						progresoFTP_setEstado('generar', 'error');
						progresoFTP_finalizar('danger', 'Error de comunicacion generando el archivo.');
					});
				});

				$("#tabListado tbody").on('click','.btnVerListaAfils',function(event){
					event.preventDefault();
					var id_lote = $(this).data('id_lote');
					$("#id_lote").val(id_lote);
					var filtroInicial = $(this).data('filtro') || '';

					var periodo = $(this).data('periodo');
					$("#s_periodo").html(periodo);
					$('#wizardIndicadorAfiliados').text('2. Afiliados y control · Período '+periodo);

					mostrarPasoWizard('afiliados');
					genera_tabla();
					inicializarScrollSuperiorTabulator();
					cargarResumenAgrupado(id_lote);
					llena_tabla(id_lote,inst_rnos,filtroInicial);
					AgruparPorGerenciadora(id_lote);
					if (window.history && window.history.replaceState) {
						window.history.replaceState(null, document.title, '#periodo-' + id_lote);
					}
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
										tablaAfiliados.deleteRow(id_expo);
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

				$(document).on('click','.btnCronologia',function(e){
					e.preventDefault();
					var idPersona = parseInt($(this).attr('data-id_persona'), 10) || 0;
					var afiliado = $(this).attr('data-afiliado') || '';
					$('#s_afil_crono').text(afiliado);
					genera_tabla_cronologia();
					llena_tabla_cronologia(idPersona);

					$('#modalCronologia').modal('show');
				});

				$('#modalCronologia').on('hidden.bs.modal', function(){
					if ($('.modal.in').length === 0) {
						$('body').removeClass('modal-open');
					}
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

			function ordenarFechaTabulator(a,b){
				function valorFecha(valor){
					if(!valor) return 0;
					var texto=String(valor).trim();
					var partes=texto.substring(0,10).split(/[-\/]/);
					if(partes.length!==3) return 0;
					if(partes[0].length===4) return Date.UTC(parseInt(partes[0],10),parseInt(partes[1],10)-1,parseInt(partes[2],10));
					return Date.UTC(parseInt(partes[2],10),parseInt(partes[1],10)-1,parseInt(partes[0],10));
				}
				return valorFecha(a)-valorFecha(b);
			}

			function aplicarTemaGrilla(tema){
				var oscuro=tema==='oscuro';
				$('body').toggleClass('tema-oscuro',oscuro);
				localStorage.setItem('sss-tema-interfaz',oscuro?'oscuro':'claro');
				$('.btnTemaGrilla').removeClass('btn-primary').addClass('btn-default');
				$('.btnTemaGrilla[data-tema="'+(oscuro?'oscuro':'claro')+'"]').removeClass('btn-default').addClass('btn-primary');
				if(tablaAfiliados && tablaAfiliados.redraw) tablaAfiliados.redraw(true);
			}

			function renderizarSelectorColumnas(){
				if(!tablaAfiliados || !tablaAfiliados.getColumns) return;
				var bloqueadas=['_seleccionar','numero','_acciones'], ocultas='', visibles='';
				tablaAfiliados.getColumns().forEach(function(columna){
					var campo=columna.getField(), titulo=columna.getDefinition().title || campo, bloqueada=bloqueadas.indexOf(campo)>=0;
					var nombre="<span class='nombre-columna'>"+$('<div>').text(titulo).html()+"</span>";
					if(columna.isVisible()){
						visibles+="<li data-campo='"+campo+"'>"+nombre+(bloqueada?"<i class='fa fa-lock text-muted' title='Columna operativa fija'></i>":"<button type='button' class='btn btn-default accion-subir-columna' data-campo='"+campo+"' title='Subir'><i class='fa fa-arrow-up'></i></button><button type='button' class='btn btn-default accion-bajar-columna' data-campo='"+campo+"' title='Bajar'><i class='fa fa-arrow-down'></i></button><button type='button' class='btn btn-default accion-ocultar-columna' data-campo='"+campo+"' title='Ocultar'><i class='fa fa-angle-left'></i></button>")+"</li>";
					}else{
						ocultas+="<li data-campo='"+campo+"'>"+nombre+"<button type='button' class='btn btn-default accion-mostrar-columna' data-campo='"+campo+"' title='Mostrar'><i class='fa fa-angle-right'></i></button></li>";
					}
				});
				$('#columnasOcultas').html(ocultas || "<li class='text-muted'>No hay columnas ocultas</li>");
				$('#columnasVisibles').html(visibles || "<li class='text-muted'>No hay columnas visibles</li>");
				aplicarTemaGrilla(localStorage.getItem('sss-tema-interfaz') || 'claro');
			}

			function formatearAccionesTabulator(cell){
				var data=cell.getRow().getData();
				var contenedor=document.createElement('div');
				contenedor.className='acciones-afiliado-tabulator';
				contenedor.innerHTML=
					"<button type='button' class='btn btn-default btn-xs btnVerAfiliado' title='Ver información del afiliado' data-id_titular='"+data.id_titular+"' data-id_afiliado='"+data.id_afiliado+"'><i class='fa fa-user'></i></button> "+
					"<button type='button' class='btn btn-default btn-xs btnCronologia' title='Ver cronología' data-id_persona='"+data.id_persona+"'><i class='fa fa-history'></i></button> "+
					"<button type='button' class='btn btn-danger btn-xs btnQuitarFctPresentacion' title='Quitar de la presentación' data-id_expo='"+data.id_expo+"' data-id_lote='"+$('#id_lote').val()+"'><i class='fa fa-times'></i></button>";
				return contenedor;
			}

			function aplicarFiltrosTabulator(){
				if(!tablaAfiliados || !tablaAfiliados.setFilter) return;
				var texto = $.trim($('#busquedaGlobalTabulator').val() || '').toLowerCase();
				tablaAfiliados.setFilter(function(data){
					var esTitular = !parseInt(data.id_titular,10) || parseInt(data.id_titular,10) === parseInt(data.id_afiliado,10);
					var tipo = String(data.tipo_movimiento || '').toUpperCase();
					if(filtrosAfiliados.persona === 'titular' && !esTitular) return false;
					if(filtrosAfiliados.persona === 'familiar' && esTitular) return false;
					if(filtrosAfiliados.movimiento){
						var partes = String(filtrosAfiliados.movimiento).split('_');
						var letra = partes[0] === 'alta' ? 'A' : (partes[0] === 'baja' ? 'B' : 'M');
						if(tipo !== letra || (partes[1] === 'titular') !== esTitular) return false;
					}
					if(filtrosAfiliados.error === '__con_error__' && !$.trim(data.errores || '')) return false;
					if(filtrosAfiliados.error && filtrosAfiliados.error !== '__con_error__' && String(data.errores || '').indexOf(filtrosAfiliados.error) < 0) return false;
					if(filtrosAfiliados.rechazo === '__con_rechazo__' && !$.trim(data.rechazos || '')) return false;
					if(filtrosAfiliados.rechazo && filtrosAfiliados.rechazo !== '__con_rechazo__' && String(data.rechazos || '').indexOf(filtrosAfiliados.rechazo) < 0) return false;
					if(filtrosAfiliados.gerenciadora && String(data.gerenciadora || '') !== String(filtrosAfiliados.gerenciadora)) return false;
					if(filtrosAfiliados.tbt && String(data.tipo_beneficiario || '') !== String(filtrosAfiliados.tbt)) return false;
					if(texto){
						var contenido = [data.gerenciadora,data.cuil_titular,data.parentesco,data.cuil,data.dni,data.ayn,data.sexo,data.edad,data.fecha_nacimiento,data.incapacidad,data.tipo_beneficiario,data.tipo_movimiento,data.fecha_movimiento,data.errores,data.rechazos,data.descripcion_sss,data.accion_sugerida].join(' ').toLowerCase();
						if(contenido.indexOf(texto) < 0) return false;
					}
					return true;
				});
				actualizarContadorTabulator();
			}

			function actualizarContadorTabulator(){
				if(!tablaAfiliados || !tablaAfiliados.getDataCount) return;
				var total = tablaAfiliados.getDataCount();
				var filtrados = tablaAfiliados.getDataCount('active');
				$('#contadorRegistrosAfiliados').html('<strong>'+filtrados+'</strong> registros encontrados'+(filtrados !== total ? ' de '+total+' totales' : ''));
			}

			function inicializarScrollSuperiorTabulator(){
				var top=document.getElementById('topScrollWrapper'), contenido=document.getElementById('topScrollContent');
				var holder=document.querySelector('#tablaAfiliadosTabulator .tabulator-tableholder');
				var tabla=document.querySelector('#tablaAfiliadosTabulator .tabulator-table');
				if(!top || !contenido || !holder || !tabla) return;
				contenido.style.width=tabla.scrollWidth+'px';
				top.style.display=tabla.scrollWidth>holder.clientWidth?'block':'none';
				top.onscroll=function(){ holder.scrollLeft=top.scrollLeft; };
				holder.onscroll=function(){ top.scrollLeft=holder.scrollLeft; };
			}

			function instalarRedimensionColumnas(){
				var $cabeceras = $('#tabListaAfilPrestacion_wrapper .dataTables_scrollHead th');
				$cabeceras.each(function(indice){
					var $th = $(this);
					if($th.find('.columna-resize').length) return;
					$('<span class="columna-resize" title="Arrastrar para cambiar ancho"></span>')
						.appendTo($th)
						.on('mousedown', function(evento){
							evento.preventDefault();
							evento.stopPropagation();
							var inicioX = evento.pageX;
							var anchoInicial = $th.outerWidth();
							$(document).on('mousemove.redimensionTabla', function(movimiento){
								var ancho = Math.max(32, anchoInicial + movimiento.pageX - inicioX);
								$('#tabListaAfilPrestacion_wrapper table').each(function(){
									$(this).find('tr > *:nth-child('+(indice+1)+')').css({width:ancho+'px',minWidth:ancho+'px',maxWidth:ancho+'px'});
								});
								inicializarScrollSuperior();
							}).one('mouseup.redimensionTabla', function(){
								$(document).off('.redimensionTabla');
							});
						});
				});
			}

			function inicializarScrollSuperior() {
				const top = document.getElementById('topScrollWrapper');
				const topContent = document.getElementById('topScrollContent');
				const contenedor = document.getElementById('divListFctPrestacion');
				const bottom = document.querySelector('#tabListaAfilPrestacion_wrapper .dataTables_scrollBody') || contenedor;

				if (!top || !topContent || !bottom) return;

				setTimeout(function () {
					let tabla = document.getElementById('tabListaAfilPrestacion');
					if (!tabla) {
						top.style.display = 'none';
						return;
					}

					let anchoContenido = Math.max(tabla.scrollWidth, bottom.scrollWidth);
					topContent.style.width = anchoContenido + 'px';

					if (anchoContenido > bottom.clientWidth) {
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
				    resumenPeriodoActual = data || {resumen_movimientos:[],resumen_errores:[],resumen_rechazos:[]};
				});
			}

			function genera_tabla_cronologia(){
				$('#divListCronologia').html(
					"<table id='tabListaCronologia' class='table' style='font-size: 11px;'>"
						+"<thead>"
							+"<tr>"
								+"<th>#</th>"
								+"<th>Fecha movimiento</th>"
								+"<th>Periodo</th>"
								+"<th>Estado</th>"
								+"<th>Codigo</th>"
								+"<th>Movimiento</th>"
							+"</tr>"
						+"</thead>"
						+"<tbody></tbody>"
					+"</table>"
				);
			}

			function llena_tabla_cronologia(id_persona){
				$("#tabListaCronologia tbody").html("<tr><td colspan='6' class='text-center'><i class='fa fa-spinner fa-spin'></i> Cargando cronología...</td></tr>");

				$.getJSON('ajax_dev.php',
					{ parametro: "lst_cronologia_afiliado", id_persona: id_persona },
					function(data){
						$("#tabListaCronologia tbody").html("");
						if (!Array.isArray(data)) {
							$("#tabListaCronologia tbody").html("<tr><td colspan='6' class='text-center text-danger'>No se pudo cargar la cronología.</td></tr>");
							return;
						}
						if (data.length === 0) {
							$("#tabListaCronologia tbody").html("<tr><td colspan='6' class='text-center text-muted'>Este afiliado todavía no tiene movimientos registrados.</td></tr>");
							return;
						}

						for(var i=0; i<=data.length-1 ;i++){
							var escapeHtml = function(valor){ return $('<div>').text(valor || '').html(); };
							$("#tabListaCronologia tbody").append(
								"<tr>"
									+"<td>"+(i+1)+"</td>"
									+"<td>"+escapeHtml(data[i]['fechador'])+"</td>"
									+"<td>"+escapeHtml(data[i]['periodo'])+"</td>"
									+"<td><span class='label label-info'>"+escapeHtml(data[i]['estado'])+"</span></td>"
									+"<td>"+escapeHtml(data[i]['codigo_error'])+"</td>"
									+"<td>"+escapeHtml(data[i]['movimiento'])+"</td>"
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
				).fail(function(xhr){
					var mensaje = 'No se pudo cargar la cronología.';
					if (xhr.responseJSON && xhr.responseJSON.mensaje) mensaje += ' ' + xhr.responseJSON.mensaje;
					$("#tabListaCronologia tbody").html("<tr><td colspan='6' class='text-center text-danger'>"+$('<div>').text(mensaje).html()+"</td></tr>");
				});
			}

			function AgruparPorGerenciadora(id_lote){
				$.ajax({
					url: 'ajax_dev.php',
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'lst_afiliados_presentacion_x_gerenciadora',id_lote: id_lote},
				})
				.done(function(data) {
					resumenGerenciadorasActual = Array.isArray(data) ? data : [];
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

			$(document).on('click','.btnProcesarResultadosSSS',function(e){
				e.preventDefault();
				var $boton = $(this);
				var idLote = $boton.data('id_lote');
				var periodo = $boton.data('periodo');
				var contenidoBoton = $boton.html();
				$boton.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i>');
				$.ajax({
					url:'ajax_dev.php', type:'GET', dataType:'json',
					data:{parametro:'ftp_sss_procesar_resultados',id_lote:idLote,periodo:periodo}
				}).done(function(resp){
					if(resp.status==='ok'){
						alert('Resultados importados. Aceptados: '+resp.aceptados_importados+'. Rechazados: '+resp.rechazados_importados+'. Errores propagados al periodo siguiente: '+resp.errores_propagados+'.');
					} else {
						alert(resp.mensaje || 'Los resultados todavia no estan disponibles.');
					}
					ListarLotes();
				}).fail(function(xhr){
					var mensaje='No se pudo controlar el resultado de la SSS.';
					try { var r=JSON.parse(xhr.responseText); if(r.mensaje) mensaje=r.mensaje; } catch(ex) {}
					alert(mensaje);
				}).always(function(){ $boton.prop('disabled',false).html(contenidoBoton); });
			});

			function actualizarAvisoFtp(){
				if(capacidadFtp.automatico){
					$('#avisoFtpManual').hide().text('');
					return;
				}
				$('#avisoFtpManual')
					.html('<i class="fa fa-exclamation-triangle"></i> '+$('<div>').text(capacidadFtp.mensaje || 'El FTP automático no está disponible. Utilice las opciones manuales.').html())
					.show();
			}

			function activarContingenciaFtp(mensaje){
				capacidadFtp = {
					automatico:false,
					manual:true,
					estado:'error_conexion',
					mensaje:(mensaje || 'No se pudo acceder al FTP de la SSS.')+' Se habilitaron las opciones manuales.'
				};
				actualizarAvisoFtp();
				ListarLotes();
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
					let btn_exportar = '';
					let periodo_proceso = '';
					let btn_imp_aceptados = '';
					let btn_imp_rechazados = '';
					let btn_imp_errores = '';
					let btn_exportar_manual = '';

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
						let estado_circuito = data[i].estado_circuito || estado;
						let resultados_desde = data[i].resultados_disponibles_desde || '';
						let fecha_enviado = data[i].fecha_enviado || '';
						let fecha_resultado = data[i].fecha_resultado || '';
						let ultimo_error_ftp = data[i].ultimo_error || '';
						let contingenciaFila = !capacidadFtp.automatico || estado_circuito === 'ERROR_ENVIO' || estado_circuito === 'SIN_CREDENCIALES_FTPS';

						btn_exportar = '';
						periodo_proceso = '';
						btn_imp_aceptados = '';
						btn_imp_rechazados = '';
						btn_imp_errores = '';
						btn_exportar_manual = '';

						if(estado.toLowerCase()!="cerrado"){
							if(!contingenciaFila){
								btn_exportar = `
									<button type='button' class='btn btn-default btnExportar' data-id_lote='${id}' data-periodo='${descripcion}' data-fecierre='${archivo}' title='Generar y enviar presentación por FTP'>
										<i class='fa fa-cloud-upload'></i>
									</button>
								`;
							} else {
								btn_exportar_manual = `
									<a class='btn btn-default' target='_blank' href='ajax_dev.php?parametro=CrearArchivo&id_lote=${id}&periodo=${encodeURIComponent(descripcion)}' title='Descargar archivo para presentación manual'>
										<i class='fa fa-download'></i>
									</a>
								`;
							}

							periodo_proceso = `class='periodo_proceso'`;

						}

						if(contingenciaFila){
							btn_imp_errores = `
								<button type='button' class='btn btn-default btnImpErrores' data-toggle='modal' data-target='#modalErrores' data-id_lote='${id}' data-periodo='${descripcion}' title='Importar archivo de errores inmediato (.err)'>
									<i class='fa fa-exclamation-triangle'></i>
								</button>
							`;
						}

						if(estado.toLowerCase() == "cerrado" && contingenciaFila){
							btn_imp_aceptados = `
								<button type='button' class='btn btn-default btnImpAceptados' data-toggle='modal' data-target='#modalAceptados' data-periodo='${descripcion}' title='Carga manual de aceptados (contingencia)'>
									<i class='fa fa-check-circle-o'></i>
								</button>
							`;

							btn_imp_rechazados = `
								<button type='button' class='btn btn-default btnImpRechazados' data-toggle='modal' data-target='#modalRechazados' data-periodo='${descripcion}' title='Carga manual de rechazados (contingencia)'>
									<i class='fa fa-times-circle-o'></i>
								</button>
							`;
						}

						$('#tabListado tbody').append(`
							<tr ${periodo_proceso}>
								<td>
									<span class='periodo-principal'>${descripcion}</span>
									<span class='periodo-secundario'>Lote ${id}</span>
								</td>
								<td>${formatFechaDDMMYYYY(fecha_vencimiento)}</td>
								<td>
									<span class='estado-circuito'><span class='estado-punto ${estado.toLowerCase()==='cerrado' ? 'cerrado' : ''}'></span>${estado_circuito}</span>
									<span class='dato-fecha'>${fecha_enviado ? 'Último envío FTP: '+formatFechaHora(fecha_enviado) : 'Sin envío FTP registrado'}</span>
									${ultimo_error_ftp ? `<span class='dato-fecha text-danger'>FTP: ${$('<div>').text(ultimo_error_ftp).html()}</span>` : ''}
								</td>
								<td style='text-align: right;'>${q}</td>
								<td style='text-align: right;'>${parseInt(errores_q,10) > 0 ? `<a href='#periodo-${id}' class='btnVerListaAfils' data-id_lote='${id}' data-periodo='${descripcion}' data-filtro='__errores_ftp__' title='Ver afiliados con error FTP'>${errores_q}</a>` : errores_q}</td>
								<td style='text-align: right;'>
									${aceptados_q}
									<span class='dato-fecha'>${fecha_resultado ? 'Importados: '+formatFechaHora(fecha_resultado) : (resultados_desde ? 'Disponibles: '+formatFechaDDMMYYYY(resultados_desde) : 'Fecha pendiente')}</span>
								</td>
								<td style='text-align: right;'>
									${parseInt(rechazados_q,10) > 0 ? `<a href='#periodo-${id}' class='btnVerListaAfils' data-id_lote='${id}' data-periodo='${descripcion}' data-filtro='__rechazados__' title='Ver afiliados rechazados'>${rechazados_q}</a>` : rechazados_q}
									<span class='dato-fecha'>${fecha_resultado ? 'Importados: '+formatFechaHora(fecha_resultado) : (resultados_desde ? 'Disponibles: '+formatFechaDDMMYYYY(resultados_desde) : 'Fecha pendiente')}</span>
								</td>
								<td class='acciones-periodo'>
									<a href='#periodo-${id}' class='btn btn-default btnVerListaAfils' data-id_lote='${id}' data-periodo='${descripcion}' title='Abrir detalle de afiliados'>
										<i class='fa fa-list-alt'></i>
									</a>
									${btn_exportar}
									${btn_exportar_manual}
									${btn_imp_errores}
									${btn_imp_aceptados}
									${btn_imp_rechazados}
								</td>
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

			function formatFechaHora(fecha){
				if(!fecha) return '';
				let valor = String(fecha).trim();
				let utc = new Date(valor.replace(' ', 'T') + (valor.indexOf('Z') === -1 ? 'Z' : ''));
				if(isNaN(utc.getTime())){
					let partes = valor.split(' ');
					let fechaFormateada = formatFechaDDMMYYYY(partes[0]);
					return partes.length > 1 ? fechaFormateada+' '+partes[1].substring(0,5) : fechaFormateada;
				}
				let dia = String(utc.getDate()).padStart(2,'0');
				let mes = String(utc.getMonth()+1).padStart(2,'0');
				let anio = utc.getFullYear();
				let hora = String(utc.getHours()).padStart(2,'0');
				let minuto = String(utc.getMinutes()).padStart(2,'0');
				return dia+'-'+mes+'-'+anio+' '+hora+':'+minuto;
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
