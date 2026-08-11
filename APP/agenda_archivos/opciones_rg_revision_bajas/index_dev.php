<?php 
include(__DIR__.'/../../../Config/Conectar.inc');

if(isset($_SESSION["iduser"])){
	$usu= $_SESSION['usuario'];
	$id_usuario = $_SESSION["iduser"];
}
else{
	echo "<h2>Su sesion caduco vuelva a loguearse</h2>
			<br>
			<ul>
				<li>
					<a href='http://".DOMINIO."'>Sistema ".strtoupper(INST_NAME)." - OBRA SOCIAL</a>					
				</li>
				<li>
					<a href='http://".DOMINIO."/extranet'>Sistema ".strtoupper(INST_NAME)." EXTRANET</a>
				</li>
			</ul>";
	 //header("Location: error.php");
	 exit();
}
//echo  $id_usuario;exit(); 
?>

<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>BAJAS RG | Opc RG Bajas Revision</title>
	<!-- Jquery -->
	<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js">
    
</script>

	
	<!-- Bootstrap -->		
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">		
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	
    <!-- Datatables Buttons (Excel) -->
    <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
    <script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<!-- Estilos propios -->
	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
	<script src='http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js'></script>
</head>
<body>
	<div class='container' style='margin: 20px;' id='container_error'>
		<div class='alert alert-danger'>
			<p id='mensaje_error'></p>
	 	</div>
	 	<div class="row">
	 		<a href="index.php" id="btnRecargar" class="btn btn-primary">
				<span id="spanEnviar"></span>Volver 
			</a>
	 	</div>
	</div>
	<div class="container" id='container_form'>
		
		<div class="row" style="width: 600px;">
			<div class="x_panel">
				<div class="tituloDiv">
					Subir un nuevo archivo de <b>traspasos | Bajas RG </b>
				</div>
				<!-- Form de carga -->
				<div class="row" style="margin: 10px;">
					<hr>
					<div style="width: 500px;">						
						<form method="post" name="form1" id="form1" >	
							<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
							<input type="hidden" name="parametro" id="parametro">
							<input type="hidden" name="nombre" id="nombre">
							<input type="hidden" name="extension" id="extension">
							<table class="table">
								<tr>
									<td>
										<a href="https://www.sssalud.gob.ar/login.php?opc=menuOpciones"  target='_NEW2' class="btn btn-sm btn-info">Ir a SSS</a>
									</td>
								</tr>
								<tr>
									<td>
										<label>Fecha desde</label>
										<input type="date" name="fdesde" id="fdesde" class="inp-sm" value="<?=date('Y-m-d');?>" required />
									</td>
								</tr>
								<tr>
									<td>
										<label>Fecha hasta</label>
										<input type="date" name="fhasta" id="fhasta" class="inp-sm" value="<?=date('Y-m-d');?>" required />
									</td>
								</tr>
								<tr>
									<td>
										<label>Seleccionar archivo</label>
										<input type="file" name="archivo" id="archivo" required class="inp-sm">
									</td>											
								</tr>
								<tr>
									<td>
										<div class="checkbox">
											<label><input id="archivo_vacio" type="checkbox" value="">Archivo vacio</label>
										</div>
									</td>
								</tr>
								
							</table> 							
							<hr>
							<input type="submit" name="btnEnviar" value = "Enviar" style="display: none;">
							<a id="btnEnviar" class="btn btn-success">
								<span id="spanEnviar"></span>Procesar 
							</a>
						</form>
					</div>					
				</div>
			</div>
		</div>

		<!-- Div historicos -->
		<div class="row">
			<div class="x_panel">
				<div class="tituloDiv">
					Historico de archivos procesados
				</div>
				<div class="row">
					<div style="width: 99%; padding: 20px;">
						<h5>Los registros en verde indican que fueron exportados al padron</h5>
						
        <div style="margin: 10px 0;">
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#ModalResumenBajas">
                <i class="fas fa-chart-bar"></i> Resumen Bajas
            </button>
        </div>

        <!-- Modal Resumen Bajas -->
        <div class="modal fade" id="ModalResumenBajas" tabindex="-1" role="dialog" aria-labelledby="ModalResumenBajasLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="ModalResumenBajasLabel">Resumen Bajas RG</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-sm-6">
                                <label>Mes</label>
                                <select id="periodo_resumen_bajas" class="form-control"></select>
                            </div>
                            <div class="col-sm-6" style="padding-top: 25px;">
                                <button type="button" class="btn btn-success" id="btnBuscarResumenBajas">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info" id="alertResumenBajas" style="display:none;"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabResumenBajas" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Bajas mensuales</th>
                                        <th>Bajas diarias</th>
                                        <th>Dif</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
<hr>
						<table class="table" id="tabHistorico">
							<thead>
								<tr>
									<th>#</th>
									<th></th>
									<th>Lote</th>
									<th>Desde</th>
									<th>Hasta</th>
									<th>Registros</th>
									<th>Fechador</th>
									<th>Usuario</th>
								</tr>
							</thead>
							<tbody>
								<?php
									
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script>
	var error_periodo = "Existe uno o mas archivos de Opciones RG revision con este rango de fechas, por favor chequee la lista para saber si ya esta cargado. ";
	var error_tipo_archivo = "No es un archivo 'Opciones RG revision BAJAS' ";
	const DOMINIO = "<?echo DOMINIO;?>";
	const INST_RNOS = "<?echo INST_RNOS;?>";
	(function(){
		//console.log('Hola');
		$('#container_error').css("display", "none");
		$('#container_form').css("display", "block");

		CargarPeriodos();
		CargarProcesados();
		CargarResumenBajas('');

		$('#btnEnviar').on('click',function(){
			confirm('¿Seguro?');
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			var fdesde = $('#fdesde').val();
			var fhasta = $('#fhasta').val();
			var validar_periodo = {"parametro":"valida_archivo_a_procesar","fdesde": fdesde,"fhasta": fhasta};
			//console.log(var_periodo);

			$.ajax({url: 'ajax.php',
				type:'get', 
				data: validar_periodo, 
				success: function(data){

					
					//console.log(data);
					console.log(data);
					if(data > 0){//PANTALLA DE ERROR POR INTENTAR CARGAR UN ARCHIVO A UN PERIODO YA USADO
						$('#container_error').css("display", "block");
						$('#container_form').css("display", "none");
						$('#mensaje_error').text(error_periodo);
					}
					else{//EN CASO DE QUE EL ARCHIVO NO SE HAYA CARGADO ANTES...
						if ($("#archivo_vacio").is(":checked")) {
			                //console.log("Check box in Checked");

			            	var datos = {"parametro":"grabar_archivo_vacio","fdesde": fdesde,"fhasta": fhasta};

			            	$.ajax({
			            		url: 'ajax.php',
			            		type: 'GET',
			            		dataType: 'text',
			            		data: datos,
			            	})
			            	.done(function(data) {
			            		console.log("success");
			            		alert("Su numero de lote VACIO es: " + data);
			            		window.location.reload();
			            	});
			            }else{
							var var_archivo = document.getElementById('archivo');
							var archivo_nombre =  getFile(archivo.value);
							var archivo_ext =  archivo.value.split('.')[1];
							//console.log('Nombre: '+ archivo_nombre);
							//console.log('Extension: '+ archivo_ext);
							if( archivo_nombre.substr(0,8) != 'b'+INST_RNOS+'-'){//En caso que el nombre del archivo no sea el indicado. Esta validacion deberia estar mas arriba...
								$('#container_error').css("display", "block");

							 	$('#container_form').css("display", "none");

							 	$('#mensaje_error').text(error_tipo_archivo + " " + archivo_nombre);
							}else{
								//CARGA DE DATOS NECESARIOS PARA IMPORTAR EL ARCHIVO CON PERIODO INDICADO. NOMBRE Y EXTENSION PODRIAN EXTRAERSE DESDE LA PARTE PHP

								var file_data = $('#archivo').prop('files')[0];
								var parametro = 'trabajar_archivo';
								var frm = document.getElementById("form1");  
								//console.log(frm);
							    var form_data = new FormData(frm); 
							    form_data.append('parametro',parametro);                 
							    form_data.append('archivo', file_data);
							    form_data.append('nombre', archivo_nombre);
							    form_data.append('fdesde', fdesde);
							    form_data.append('fhasta', fhasta);
							    form_data.append('extension', archivo_ext);
							    $.ajax({
							        url: 'ajax.php',
							        dataType: 'text',
							        cache: false,
							        contentType: false,
							        processData: false,
							        data: form_data,                         
							        type: 'post',
							        success: function(data){
							        	//console.table(data);
							        	alert("Su numero de lote es: "+data);
							        	$('#btnEnviar').removeAttr('disabled');
							        	$('#btnEnviar').html('');
							        	$('#btnEnviar').html('Procesar');
							        	//window.location.reload();
							        	CargarProcesados();
							        }
							    });
							
							}
						}

					}
			}});

		});

		$("#tabHistorico").on('click',".btnExportar",function(){

			var id_lote = $(this).data("id_lote");

			var datos1 = {
				"parametro": "exportacion_padron",
				"id_lote": id_lote
			};

			$.ajax({

				url: 'ajax.php',
				type: 'get',
				data: datos1,
				success: function(data1){						
					if(data1=="ok"){
						alert("Termino");
						//return false();
					}
					else{
						alert('ERROR !');
						console.log(data1);
					}
				}
			})

			

		})

	 }());
	 
	// --- Resumen Bajas RG (últimos 24 meses por defecto) ---
    var dtResumenBajas = null;

    function InicializarTablaResumenBajas(){
        if(dtResumenBajas){ return; }
        dtResumenBajas = $('#tabResumenBajas').DataTable({
            dom: 'Blrtip',
            buttons: [{ extend: 'excel', text: 'Excel' }],
            paging: true,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            searching: false,
            ordering: true,
            info: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json' }
        });
    }

    function CargarResumenBajas(periodo){
		    //InicializarTablaResumenBajas();
		    $('#alertResumenBajas').hide().text('');

		    $.ajax({
		        url: 'ajax.php',
		        method: 'GET',
		        dataType: 'json',
		        data: {
		            parametro: 'TraerResumenBajasRg',
		            periodo: (periodo || '')
		        },
		        success: function(data){
		            dtResumenBajas.clear();

		            if(!data || !data.length || data[0]['error']){
		                var msg = (periodo && periodo!='')
		                    ? 'No hay resultados para el periodo seleccionado.'
		                    : 'No hay resultados en los últimos 24 meses.';
		                $('#alertResumenBajas').show().text(msg);

		                console.log('Respuesta JSON (sin datos / error):', data);
		                dtResumenBajas.draw();
		                return;
		            }

		            for(var i=0; i<=data.length-1; i++){
		                dtResumenBajas.row.add([
		                    (data[i]['periodo'] || '').toString().substring(0,7),
		                    data[i]['bajas_mensuales'],
		                    data[i]['bajas_diarias'],
		                    data[i]['dif']
		                ]);
		            }

		            dtResumenBajas.draw(false);
		        },
		        error: function(xhr){
		            dtResumenBajas.clear().draw();

		            // Esto te va a decir EXACTO qué pasa:
		            $('#alertResumenBajas').show().text(
		                'Error AJAX (' + xhr.status + '): ' + (xhr.responseText ? xhr.responseText.substring(0,200) : 'sin respuesta')
		            );

		            console.log('AJAX ERROR status:', xhr.status);
		            console.log('AJAX ERROR responseText:', xhr.responseText);
		        }
		    });
		}


    $(document).on('click', '#btnBuscarResumenBajas', function(){
        var per = $('#periodo_resumen_bajas').val();
        if(!per){
            $('#alertResumenBajas').show().text('Seleccione un mes.');
            return;
        }
        CargarResumenBajas(per);
    });

    $('#ModalResumenBajas').on('shown.bs.modal', function () {
        if(typeof CargarPeriodos === 'function'){ CargarPeriodos(); }
        InicializarTablaResumenBajas();
        dtResumenBajas.columns.adjust();
        CargarResumenBajas('');
    });

    $('#ModalResumenBajas').on('hidden.bs.modal', function(){
        $('#alertResumenBajas').hide().text('');
        if(dtResumenBajas){ dtResumenBajas.clear().draw(); }
    });

	function CargarPeriodos(){
		$.getJSON('ajax.php',
			{parametro: 'TraerPeriodos'},
			function(data){
				//console.log(data);
				if(data[0]['error']){
						
					//console.log(data[0]['error'])								
					$('#periodo').append("<option>Error</option>");

				
                            
}
				else{
					//console.log('Hola');
					if($('#periodo_resumen_bajas').length){
                                
                            }
					if(data.length>0){
						for(var i=0; i<=data.length-1 ;i++){
							$('#periodo').append("<option value='"+data[i]['primer_dia']+"'>"+data[i]['periodo1']+"</option>");
							var periodoVisualB = (data[i]['periodo1'] || '').toString().substring(0,7);
                                $('#periodo_resumen_bajas').append("<option value='"+data[i]['primer_dia']+"'>"+periodoVisualB+"</option>");
						}
					}
				}
		});
	}
	
	function getFile(filePath) {
        return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
    }
    
    function CargarProcesados(){
    	
    	$('#tabHistorico tbody').html('');

    	$.getJSON('ajax.php',
    		{parametro:'TraerProcesados'},
    		function(data){

    			if(data[0]['error']){
    				$('#lista_procesados tbody').append("<tr><td>No hay Resultados</td></tr>");
    			}else{
    				if(data.length>0){

    					for(var i=0; i<=data.length-1; i++){
    						var style = importable = '';
    						if(data[i]['exp_padron']==="0"){

    							importable = "<div class='btn btn-group btn-group-default'>"
									+"<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>"
											+"<i class='fa fa-ellipsis-v' aria-hidden='true'></i>"
									+"</button>"
									+"<ul class='dropdown-menu'>"
										+"<li>"
											+"<a data-id_lote='"+data[i]['id']+"' class='btnExportar' title='Nothing'> "
												+"<i class='fas fa-wrench'></i>&nbsp; Exportar a padron"+DOMINIO+
											+"</a>"
										+"</li>"
										
									+"</ul>"
								+"</div>";
    							style="";
    						}else{
    							importable = "";
    							style='style="background-color: darkseagreen;"';
    						}

    						$('#tabHistorico tbody').append("<tr "+style+">"
    															+"<td>"+(i+1)+"</td>"
    															+"<td>"
    																//+importable
    															+"</td>"
    															+"<td>"+data[i]['id']+"</td>"
    															+"<td>"+data[i]['fecha_desde']+"</td>"
    															+"<td>"+data[i]['fecha_hasta']+"</td>"
    															+"<td>"+data[i]['q_registros']+"</td>"
    															+"<td>"+data[i]['fecha_carga']+"</td>"
    															+"<td>"+data[i]['usuario']+"</td>"
    															+"</tr>");
    					}
    				}
    			}
    		})
    }
</script>
</html>