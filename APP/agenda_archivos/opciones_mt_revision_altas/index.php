<?php 
include('../../../Config/Conectar.inc');

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
	 exit();
}
//echo  $id_usuario;exit(); 
?>

<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Altas MT | Opc MT revision SSS</title>
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
		
		<div class="row" style="width: 600px;">
			<div class="x_panel">
				<div class="tituloDiv">
					Subir un nuevo archivo de <b>traspasos | Altas MT </b>
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
	var error_tipo_archivo = "No es un archivo 'Opciones MT revision ALTAS' ";
	
	var INST_RNOS= <?=INST_RNOS;?>;

	(function(){
		//console.log('Hola');
		$('#container_error').css("display", "none");
		$('#container_form').css("display", "block");

		CargarPeriodos();
		CargarProcesados();

		$('#btnEnviar').on('click',function(){
			confirm('¿Seguro?');
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			var fdesde = $('#fdesde').val();
			var fhasta = $('#fhasta').val();
			var validar_periodo = {"parametro":"valida_archivo_a_procesar","fdesde": fdesde,"fhasta": fhasta};
			//console.log(var_periodo);

			var archivo_vacio = $('#archivo_vacio').val();

			$.ajax({url: 'ajax.php',
				type:'get', 
				data: validar_periodo, 
			}).then(function(data){
				//console.log(data);
				console.log(data);
				if(data > 0){//PANTALLA DE ERROR POR INTENTAR CARGAR UN ARCHIVO A UN PERIODO YA USADO

					$('#container_error').css("display", "block");
					$('#container_form').css("display", "none");
					$('#mensaje_error').text(error_periodo);
				}else{//EN CASO DE QUE EL ARCHIVO NO SE HAYA CARGADO ANTES...

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
		            	});
		            }else{

						var var_archivo = document.getElementById('archivo');
						var archivo_nombre =  getFile(archivo.value);
						var archivo_ext =  archivo.value.split('.')[1];
						//console.log('Nombre: '+ archivo_nombre);
						//console.log('Extension: '+ archivo_ext);
						if( archivo_nombre.substr(0,8) != 'a'+INST_RNOS+'-'){//En caso que el nombre del archivo no sea el indicado. Esta validacion deberia estar mas arriba...
							$('#container_error').css("display", "block");

						 	$('#container_form').css("display", "none");

						 	$('#mensaje_error').text(error_tipo_archivo + " " + archivo_nombre); //console.log('a'+INST_RNOS+'-'); return false;
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
						        type: 'post'
						    }).then(function(data){
						    	//console.table(data);
					        	alert("Su numero de lote es: "+data);
					        	$('#btnEnviar').removeAttr('disabled');
					        	$('#btnEnviar').html('');
					        	$('#btnEnviar').html('Procesar');
					        	//window.location.reload();
					        	CargarProcesados();
						    });
						}
					}

				}
			});
		});
		$(document).on('click',".btnExportar",function(){

			var id_lote = $(this).data("id_lote");

			var datos1 = {
				"parametro": "exportacion_padron",
				"id_lote": id_lote
			};

			$.ajax({
				url: 'ajax.php',
				type: 'get',
				data: datos1
			}).then(function(data1){
				if(data1=="ok"){
					alert("Termino");
					//return false();
				}
				else{
					alert('ERROR !');
					console.log(data1);
				}
				CargarProcesados();
			});
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
	
	function getFile(filePath) {
        return filePath.substr(filePath.lastIndexOf('\\') + 1).split('.')[0];
    }
    
    function CargarProcesados(){
    	
    	$('#tabHistorico tbody').html('');
    	$.getJSON('ajax.php',
    		{parametro:'TraerProcesados'},
    		function(data){

    			console.table(data);
    			if(data[0]['error']){
    				$('#tabHistorico tbody').append("<tr><td>No hay Resultados</td></tr>");
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
												+"<i class='fas fa-wrench'></i>&nbsp; Exportar a padron "
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
    															+"<td>"+data[i]['cant_registros']+"</td>"
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