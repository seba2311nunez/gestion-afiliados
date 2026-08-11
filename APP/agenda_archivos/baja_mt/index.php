<?php 
include('../../../Config/Conectar.inc');
$id_usuario = $_SESSION["iduser"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Archivos Bajas MT</title>
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
	 		<a href="index.php" id="btnRecargar" class="btn btn-primary">
				<span id="spanEnviar"></span>Volver 
			</a>
	 	</div>
	</div>
	<div class="container" id='container_form'>
		<div class="x_panel">
				<div class="tituloDiv">
					Subir un nuevo archivo de <b>BAJA MONOTRIBUTISTA</b>
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
			</div>
	</div>	
	<script>
	var INST_RNOS= <?=INST_RNOS;?>;
	var nombre_validacion = `b${INST_RNOS}`;
	var error_periodo = "Existe un archivo de BAJA MONOTRIBUTISTA con este periodo, por favor chequee la lista para saber si ya esta cargado. ";
	var error_tipo_archivo = "No es un archivo 'BAJA MONOTRIBUTISTA' ";
	var periodo  = '<?php echo $periodo; ?>';
	
	$(function(){
		$('#container_error').css("display", "none");
		$('#container_form').css("display", "block");
		
		$('#periodo').append("<option value='"+periodo+"'>"+periodo.substr(0,7)+"</option>");
		$('#periodo').val(periodo).change();

		//CargarPeriodos();

		$('#btnEnviar').on('click',function(){
			confirm('¿Seguro?');
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
						        	//CargarProcesados();
						        }
						    });
						
						}
						

					}
			}});

		});
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

	function getFile(filePath) {
        return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
    }

	</script>
</body>
</html>