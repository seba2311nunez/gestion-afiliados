<?php 
include('../../../Config/Conectar.inc');
$id_usuario = $_SESSION["iduser"];
$id_usuario = 1 ;
//echo  $id_usuario;exit(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Archivos FA</title>
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
	<div class='container' style='margin: 20px;' id='container_error'>
		<div class='alert alert-danger'>
			<p id='mensaje_error'></p>
	 	</div>
	 	<div class="row">
	 		<a href="archivos_fa.php" id="btnRecargar" class="btn btn-primary">
				<span id="spanEnviar"></span>Volver 
			</a>
	 	</div>
	</div>
	<div class="container" id='container_form'>
		<div class="col-md-8" id="div_importador">
			<div class="x_panel">
				<div class="tituloDiv">
					Subir un nuevo archivo de <b>FAMILIARES DE MONOTRIBUTISTAS</b> (fa_<?php echo INST_RNOS;?>.txt)
				</div>
				<!-- Form de carga -->
				<div class="row" style="margin: 10px;">
					<hr>
					<div style="width: 600px;">						
						<form method="post" name="form1" id="form1" >	
							<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
							<input type="hidden" name="parametro" id="parametro">
							<input type="hidden" name="nombre" id="nombre">
							<input type="hidden" name="extension" id="extension">
							<table class="table">
								<tr>
									<td>
										<input type="file" name="archivo" id="archivo" required>
									</td>											
								</tr>
								<tr>
									<td>
										<select id="periodo" name="periodo" required disabled>
											<option value="" id="SelectorPeriodo">Seleccione Periodo</option>
										</select>
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
				<!-- FIN Form de carga -->
			</div>
	
		</div>
		<div class="col-md-4">
			<div class="x_panel" id="divTareas" style="display: none;">
				<div class="tituloDiv">
					Tareas porteriores a la importacion
				</div>
				<div class="row">
					<hr>
					<b style="margin-left: 20px;">Lote</b> 
					<input type="text" id="id_lote" value="" readonly style="width: 90%; margin: auto;">
					<br>
					<div style="padding: 10px;">
						<ul class="list-group list-group-flush">
						  <li class="list-group-item" data-tarea="1" style='cursor: pointer;'>1 - Crear Tabla</li>
						  <li class="list-group-item" data-tarea="2" style='cursor: pointer;'>2 - Traer nombres faltantes</li>
						  <li class="list-group-item" data-tarea="3" style='cursor: pointer;'>3 - Cargar nombres faltantes</li>
						  <li class="list-group-item" data-tarea="4" style='cursor: pointer;'>4 - Recargar Tabla</li>
						  <li class="list-group-item" data-tarea="5" style='cursor: pointer;'>5 - Carga Preliminar</li>
						  <li class="list-group-item" data-tarea="6" style='cursor: pointer;'>6 - Carga Persona</li>
						  <li class="list-group-item" data-tarea="7" style='cursor: pointer;'>7 - Carga Afiliados</li>
						  <li class="list-group-item" data-tarea="8" style='cursor: pointer;'>8 - Carga Historico Afiliados</li>
						  <li class="list-group-item" data-tarea="9" style='cursor: pointer;'>9 - Carga Altas Manuales</li>
						  <li class="list-group-item" data-tarea="10" style='cursor: pointer;'>10 - Carga C.A.S.P.N.O.</li>
						</ul>
					</div>
					
				</div>
			</div>
		</div>

		
</body>
<script>
	var INST_RNOS= <?=INST_RNOS;?>;
	var nombre_validacion = `fa_${INST_RNOS}`;

	var periodo = "<?php echo $periodo ?>";
	var error_periodo = "Existe un archivo de altas con este periodo, por favor chequee la lista para saber si ya esta cargado. ";
	var error_tipo_archivo = "No es un archivo 'fa' ";
	(function(){
		//console.log('Hola');
		$('#container_error').css("display", "none");
		$('#container_form').css("display", "block");
		$("#div_importador").hide();
		
		$("#container_error").show();
		$("#btnRecargar").hide();
		$("#mensaje_error").html('<center><i class="fas fa-sync-alt fa-spin fa-2x" style="200px"></i> Cargando...</center>');
		console.log(periodo);
		ComprobarPeriodo(periodo);
		$('#periodo').append("<option value='"+periodo+"'>"+periodo.substr(0,7)+"</option>");
		$('#periodo').val(periodo).change();

		//CargarPeriodos();
		CargarProcesados();

		$('#btnEnviar').on('click',function(){
			var conf = confirm('¿Seguro?');
			if(conf){
				$(this).attr('disabled','disabled');
				$(this).html('');					
				$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
				var var_periodo = $('#periodo').val();
				var validar_periodo = {"parametro":"valida_archivo_a_procesar","periodo":var_periodo};
				console.log(var_periodo);
				$.ajax({url: 'ajax.php',
					type:'get', 
					data: validar_periodo, 
					success: function(data){

						$(this).attr('disabled','disabled');
						$(this).html('');					
						$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
						//console.log(data);
						console.log(data);
						if(data == 1){//PANTALLA DE ERROR POR INTENTAR CARGAR UN ARCHIVO A UN PERIODO YA USADO
							$('#container_error').css("display", "block");
							$('#container_form').css("display", "none");
							$('#mensaje_error').text(error_periodo);
						}
						else{//EN CASO DE QUE EL ARCHIVO NO SE HAYA CARGADO ANTES...
							var var_archivo = document.getElementById('archivo');
							var archivo_nombre =  getFile(archivo.value);
							var archivo_ext =  archivo.value.split('.')[1];
							//console.log('Nombre: '+ archivo_nombre);
							//console.log('Extension: '+ archivo_ext);
							if(archivo_nombre != nombre_validacion){//En caso que el nombre del archivo no sea el indicado. Esta validacion deberia estar mas arriba...
								$('#container_error').css("display", "block");

							 	$('#container_form').css("display", "none");

							 	$('#mensaje_error').text(error_tipo_archivo + " " + archivo_nombre);
							}
							else{

								//CARGA DE DATOS NECESARIOS PARA IMPORTAR EL ARCHIVO CON PERIODO INDICADO. NOMBRE Y EXTENSION PODRIAN EXTRAERSE DESDE LA PARTE PHP

								var file_data = $('#archivo').prop('files')[0];
								var parametro = 'trabajar_archivo';
								var frm = document.getElementById("form1");  
								//console.log(frm);
							    var form_data = new FormData(frm); 
							    form_data.append('parametro',parametro);                 
							    form_data.append('archivo', file_data);
							    form_data.append('nombre', archivo_nombre);
							    form_data.append('periodo', var_periodo);
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
							        	console.log(data);
							        	alert("Su numero de lote es: "+data);
							        	$('#btnEnviar').removeAttr('disabled');
							        	$('#btnEnviar').html('');
							        	$('#btnEnviar').html('Procesar');
							        	$('#btnEnviar').hide();
							        	$('#id_lote').val(data);
						        		$('#divTareas').css('display','block');

							        }
							    });
							}
						}
				}});
			}
		});

		$('.list-group-item').on('click',function(){

			var tarea = $(this).data('tarea');
			var id_lote = $("#id_lote").val();
			var url = "procesos/";
			//console.log(id_lote+' '+tarea);
			

			switch(tarea){

				case 1:
					url = url+"1_crear_tabla.php?id_lote="+id_lote ;
				break;

				case 2:
					url = url+"2_traer_nombres_faltantes.php?id_lote="+id_lote ;
				break;

				case 3:
					url = url+"2_cargar_nombres_faltantes.php?id_lote="+id_lote ;
				break;

				case 4:
					url = url+"4_recargar_tabla.php?id_lote="+id_lote ;
				break;

				case 5:
					url = url+"5_carga_preliminar.php?id_lote="+id_lote ;
				break;

				case 6:
					url = url+"6_carga_persona.php?id_lote="+id_lote ;
				break;

				case 7:
					url = url+"7_carga_afiliados.php?id_lote="+id_lote ;
				break;
				case 8:
					url = url+"8_carga_historico_afiliados.php?id_lote="+id_lote ;
				break;
				case 9:
					url = url+"9_carga_altas_manuales.php?id_lote="+id_lote ;
				break;
				case 10:
					url = url+"10_carga_caspno.php?id_lote="+id_lote ;
				break;

				default :
					url = "ninguno";
					break;


			}

			//console.log(url)
			//return false;
			abrirEnPestana(url);
			$(this).addClass('list-group-item-success');
		
		})


	 }());


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
					if(data.length>0){
						for(var i=0; i<=data.length-1 ;i++){
							$('#periodo').append("<option value='"+data[i]['primer_dia']+"'>"+data[i]['periodo1']+"</option>");
						}
					}
				}
		});
	}

	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}
	
	function getFile(filePath) {
        return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
    }

    function CargarProcesados(){
    	$('#lista_procesados tbody').html('');
    	$.getJSON('ajax.php',
    		{parametro:'TraerProcesados'},
    		function(data){
    			if(data[0]['error']){
    				$('#lista_procesados tbody').append("<tr><td>No hay Resultados</td></tr>");
    			}else{
    				if(data.length>0){
    					for(var i=0; i<=data.length-1; i++){
    						$('#lista_procesados tbody').append("<tr>"
    															+"<td>"+data[i]['id']+"</td>"
    															
    															+"<td>"+data[i]['titulo']+"</td>"
    															+"<td>"+data[i]['fecha_aPartir']+"</td>"
    															+"<td>"+data[i]['q_registros']+"</td>"
    															+"<td>"
    																+"<div class='btn btn-group btn-group-default'>"
																		+"<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>"
																				+"<i class='fa fa-ellipsis-v' aria-hidden='true'></i>"
																		+"</button>"
																		+"<ul class='dropdown-menu'>"
																			+"<li>"
																				+"<a href='procesar_fa/poner_sexo.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Crear Tabla"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/procesar_sexo_inexistentes.php' title='Proceso en sql de asignar sexo a los que quedaron con I' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Proceso Manual"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/inserta_nombre_sexo.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Insertar nombre_sexo"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/calcular_cuil.php?fproceso="+data[i]['fecha_aPartir']+"' target='_NEW' title='Fecha de nacimiento, parentesco, estado civil y fecha de alta '>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Completar Datos"
																				+"</a>"
																			+"</li>"
																			/*
																			+"<li>"
																				+"<a href='procesar_fa/actualizar_nombre_bajas.php' target='_NEW' title='Fecha de nacimiento, cuil, y datos de titulares'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 2"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/actualizar_nombre_existente.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 3"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/actualizar_nombre_nuevos.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 4"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/calcular_cuil.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 5"
																				+"</a>"
																			+"</li>"
																			+"<li>"
																				+"<a href='procesar_fa/calcular_fn.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 6"
																				+"</a>"
																			+"</li>"
																			
																			+"<li>"
																				+"<a href='procesar_fa/datos_titulares.php' target='_NEW'>"
																					+"<i class='fas fa-wrench'></i>&nbsp;Coso 8"
																				+"</a>"
																			+"</li>"
																			*/
																		+"</ul>"
																	+"</div>"
    															+"</td>"
    															+"</tr>");
    					}
    				}
    			}
    		})
    }
    function ComprobarPeriodo(periodo){

    	var validar_periodo = {"parametro":"valida_archivo_a_procesar","periodo":periodo};
    	console.log(validar_periodo);

		$.ajax({url: 'ajax.php',
				type:'get', 
				data: validar_periodo, 
				success: function(data){
					$("#mensaje_error").html("");
					$("#btnRecargar").show();
					$("#container_error").hide();
					if(data == 0){//EN CASO DE QUE EL ARCHIVO NO SE HAYA CARGADO ANTES...
						//alert(data);
						$("#div_importador").show();
					}
					else{//PANTALLA DE ERROR POR INTENTAR CARGAR UN ARCHIVO A UN PERIODO YA USADO
						//alert(data);
						$('#id_lote').val(data);
						
						$("#divTareas").show();
					}
				}
			});
    	
    }



</script>
</html>