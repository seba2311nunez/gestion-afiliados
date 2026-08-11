<?php 
include('../../../Config/Conectar.inc');
$id_usuario = $_SESSION["id_user"];
$id_usuario = 1;
//echo  $periodo;exit(); 
?>

<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Padron SSS</title>
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
	 		<a href="padron_sss.php" id="btnRecargar" class="btn btn-primary">
				<span id="spanEnviar"></span>Volver 
			</a>
	 	</div>
	</div>
	<div class="container" id='container_form'>
		<div class="col-md-8" id="div_importador">
			<div class="x_panel">
				<div class="tituloDiv">
					Subir un nuevo archivo de <b>Padron SSS</b>
				</div>

				<!-- Form de carga -->
				<div class="row" style="margin: 10px;">
					<hr>
					<?php
						
						if(INST_NAME=="oseam" || INST_NAME=="ospilm" || INST_NAME=="ospm" || INST_NAME=="ospedyb" || INST_NAME=="osetra"){
							echo "<div class='alert alert-danger'> 
									Este archivo puede ser procesado desde el FTP, para que la importacion sea mas rápida.
								</div> 
							";
						}
					?>
					<hr>
					<div style="width: 600px;">						
						<form method="post" name="form1" id="form1" >	
							<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="200000000">
							<input type="hidden" name="parametro" id="parametro">
							<input type="hidden" name="nombre" id="nombre">
							<input type="hidden" name="extension" id="extension">
							<table class="table">
								<tr>
									<td>										
										<input type="file" name="archivo" id="archivo" required>
										<label style="color: red;">Seleccione el archivo ZIP no el txt </label>
									</td>											
								</tr>
								
								<tr>
									<td>
										<select id="periodo" name="periodo" required disabled>
											<option value="" id="SelectorPeriodo">El periodo se seleccionara solo</option>
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
						  <li class="list-group-item" data-tarea="1">1 - Crear Tabla</li>
						  <li class="list-group-item" data-tarea="2">2 - Preliminar insertar</li>
						  <li class="list-group-item" data-tarea="3">3 - Insertar persona</li>
						  <li class="list-group-item" data-tarea="4">4 - Insertar afiliado</li>
						  <li class="list-group-item" data-tarea="5">5 - Insertar historico</li>
						  <li class="list-group-item" data-tarea="6">6 - Insertar altas manuales</li>
						  <li class="list-group-item" data-tarea="7">7 - Insertar campos afiliados sin preventa</li>
						</ul>
					</div>
					
				</div>
			</div>
		</div>
	</div>
		
</body>
<script>
	var INST_RNOS= <?=INST_RNOS;?>;
	var periodo = "<?php echo $periodo ?>";
	var error_periodo = "Existe un archivo de Padron con este periodo, por favor chequee la lista para saber si ya esta cargado. ";
	var error_tipo_archivo = "No es un archivo de Padron informado por la SSS ";
	var primer_dia = '' ;
	(function(){
		//console.log('Hola');
		$('#container_error').css("display", "none");
		$('#container_form').css("display", "block");
		$("#div_importador").hide();

		$("#container_error").show();
		$("#btnRecargar").hide();
		$("#mensaje_error").html('<center><i class="fas fa-sync-alt fa-spin fa-2x" style="200px"></i> Cargando...</center>');
		/*
		CargarPeriodos();
		*/
		console.log(periodo);
		ComprobarPeriodo(periodo);

		CargarProcesados();
		$('#archivo').on('change',function(){
			var archivo_nombre =  getFile(archivo.value);
			BuscarPeriodo();
		});

		$('#btnEnviar').on('click',function(){
			confirm('¿Seguro?');
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			var archivo_nombre =  getFile(archivo.value);
			
			//console.log(archivo_nombre);
			var var_periodo = $('#periodo').val();
			console.log(var_periodo);
			var validar_periodo = {"parametro":"valida_archivo_a_procesar","periodo":var_periodo};
			
			$.ajax({url: 'ajax.php',
				type:'get', 
				data: validar_periodo, 
				success: function(data){
					
					if(data != 0){//PANTALLA DE ERROR POR INTENTAR CARGAR UN ARCHIVO A UN PERIODO YA USADO
						$('#container_error').css("display", "block");
						$('#container_form').css("display", "none");
						$('#mensaje_error').text(error_periodo);
					}
					if(data == 0){//EN CASO DE QUE EL ARCHIVO NO SE HAYA CARGADO ANTES...
						var var_archivo = document.getElementById('archivo');
						var archivo_nombre =  getFile(archivo.value);
						var archivo_ext =  archivo.value.split('.')[1];
						//console.log('Nombre: '+ archivo_nombre);
						//console.log('Extension: '+ archivo_ext);
						var comprobacion = archivo_nombre.substr(1, 7);

						var comprobacion2 = archivo_nombre.substr(8, 6);

						if(comprobacion != 'Padron-' && comprobacion2 === INST_RNOS){//En caso que el nombre del archivo no sea el indicado. Esta validacion deberia estar mas arriba...

							$('#container_error').css("display", "block");

						 	$('#container_form').css("display", "none");

						 	$('#mensaje_error').text(error_tipo_archivo + "-> " + archivo_nombre);
						}else{
							return false;
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

						        	$('#id_lote').val(data);
						        	$('#divTareas').css('display','block');

						        }
						    });
						}
					}
			}});
		
		});

		$('.list-group-item').on('click',function(){

			var tarea = $(this).data('tarea');
			var id_lote = $("#id_lote").val();
			var url = "procesos/";
			//console.log(id_lote+' '+tarea);
			

			switch(tarea){

				case 1:
					url = url+"1_crear_tabla.php?id_lote="+id_lote ;
				break

				case 2:
					url = url+"2_preliminar_insertar.php?id_lote="+id_lote ;
				break

				case 3:
					url = url+"3_insertar_personas.php?id_lote="+id_lote ;
				break

				case 4:
					url = url+"4_insertar_afiliados.php?id_lote="+id_lote ;
				break

				case 5:
					url = url+"5_insertar_historico_afiliados.php?id_lote="+id_lote ;
				break

				case 6:
					url = url+"6_insertar_altas_manuales.php?id_lote="+id_lote ;
				break

				case 7:
					url = url+"7_insertar_campos_afiliados.php?id_lote="+id_lote ;
				break

				default :
					url = "ninguno";
					break


			}

			//console.log(url)
			//return false;
			abrirEnPestana(url);
			$(this).addClass('list-group-item-success');
		
		})


	 }());

	function getFile(filePath) {
        return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
    }

    function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
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
																				+"<a href='procesos/1_crear_tabla.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Crear Tabla"
																				+"</a>"
																			+"</li>"

																			+"<li>"
																				+"<a href='procesos/2_preliminar_insertar.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Proceso preliminar"
																				+"</a>"
																			+"</li>"

																			+"<li>"
																				+"<a href='procesos/3_insertar_personas.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Insertar Personas"
																				+"</a>"
																			+"</li>"

																			+"<li>"
																				+"<a href='procesos/4_insertar_afiliados.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Insertar Afiliados"
																				+"</a>"
																			+"</li>"

																			+"<li>"
																				+"<a href='procesos/5_insertar_historico_afiliados.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Inserta historico Afiliados"
																				+"</a>"
																			+"</li>"
																			
																			+"<li>"
																				+"<a href='procesos/6_insertar_altas_manuales.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Inserta Altas Manuales"
																				+"</a>"
																			+"</li>"

																			+"<li>"
																				+"<a href='procesos/7_insertar_campos_afiliados.php?id_lote="+data[i]['id']+"' target='_NEW'  title='Crear tabla Temporal, y asignar sexo'> "
																					+"<i class='fas fa-wrench'></i>&nbsp;Inserta C.A.S.P.N.O"
																				+"</a>"
																			+"</li>"
																		+"</ul>"
																	+"</div>"
    															+"</td>"
    															+"</tr>");
    					}
    				}else{
			    		$('#lista_procesados tbody').append("<tr><td>No hay Resultados</td></tr>");
    				}
    			}
    		})
    }
    function BuscarPeriodo(){
    	$('#periodo').append('<option value="'+periodo+'" id="SelectorPeriodo" selected>'+periodo+'</option>');
   
    }

    function BuscarPeriodoBK(nombre_archivo){
    	var bp_periodo = nombre_archivo.substr(7, 6);
    	
    	$.getJSON('ajax.php',
			{parametro: 'buscar_periodo', periodo: bp_periodo},
			function(data){
				$('#periodo').append('<option value="'+data[0]["primer_dia"]+'" id="SelectorPeriodo" selected>'+data[0]["primer_dia"]+'</option>');
			});

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