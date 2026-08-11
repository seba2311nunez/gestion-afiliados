<?php 
header('Access-Control-Allow-Origin: *');
include ("../../../Config/Conectar.inc");
$id_usuario = $_SESSION["iduser"];
//echo  $id_usuario;exit(); 
?>

<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Procesar traspasos</title>
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
	<div class="container" id='container_form'>
		<div class="x_panel">
			<div class="tituloDiv">
				Procesador de traspasos manual
			</div>
			<hr>
			<div class="row" style="margin: 10px;">
				<hr>
				<div style="width: 500px; display: none;">						
				<form method="post" name="form1" id="form1" >	
					<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
					<input type="hidden" name="parametro" id="parametro">
					<input type="hidden" name="nombre" id="nombre">
					<input type="hidden" name="extension" id="extension">
					<table class="table">
						<tr>
							<td>
								<label>Desreguladora</label>
								<!--<input type="date" name="fdesde" id="fdesde" class="inp-sm" value="<?//=date('Y-m-d');?>" required/>-->
								<select name='desreguladora' id="desreguladora" class="form-control">
									
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label>Seleccionar archivo</label>
								<input type="file" name="file_precarga" id="file_precarga" required class="inp-sm">
							</td>											
						</tr>
					</table> 							
					<hr>
					<input type="submit" name="btnEnviar" value ="Enviar" style="display: none;">
					<a id="btnPrecarga" class="btn btn-success" onchange="ValidateSingleInput(this)" >
						<span id="spanEnviar"></span>Precargar 
					</a>
				</form>
			</div>
			<div class="row">
				<div style="padding: 25px;">

					<table class="table table-striped" id="lista_procesados">
						<thead>
							<th>#</th>
							<th>Opciones</th>
							<th>Estado</th>
							<th>Periodo</th>
							<th>Nombre Archivo</th>
							<th>Cantidad</th>
							<th>Capita</th>
							<th>Fecha carga</th>
						</thead>
						<tbody></tbody>
					</table>
				</div>					
			</div>
		</div>
	</div>
	<div id="modal_precarga" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg">
		  <div class="modal-content">
		    <div class="modal-header">
		      <button type="button" class="close" data-dismiss="modal">X</button>
		      <h4 class="modal-title">Precarga (Capitas)</h4>
		    </div>
		    <div class="modal-body">
		    	<div class="col-md-12">
		    		<div class="row">
		      		<div class="col-md-12" id="precarga_vista_previa">
		      			
		      		</div>
		    		</div>
		    	</div>
		    </div>
	    	<br>
		    <div class="modal-footer">
		    	<button type="button" class='btn btn-primary' id="btnPrecargaInsertar">Grabar</button>
		      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		    </div>
		  </div>
		</div>
	</div>

	<div id="modal_procesado" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg">
		  <div class="modal-content">
		    <div class="modal-header">
		      <button type="button" class="close" data-dismiss="modal">X</button>
		      <h4 class="modal-title">Resultado de proceso</h4>
		    </div>
		    <div class="modal-body">
		    	<div class="col-md-12">
		    		<div class="row">
		      		<div class="col-md-12">
		      			<table id="table-ver-lote-procesado" class="table table-sm">
		      				<thead>
		      					<tr>
		      						<th>CUIL</th>
		      						<th>CUIL Sistema</th>
		      						<th>Fecha Nacimiento</th>
		      						<th>AyN</th>
		      						<th>Sexo</th>
		      						<th>Reclamado por</th>
		      						<th>Gerenciador actual</th>
		      						<th>Provincia</th>
		      						<th>Localidad</th>
		      					</tr>
		      				</thead>
		      				<tbody></tbody>
		      			</table>
		      		</div>
		    		</div>
		    	</div>
		    </div>
	    	<br>
		    <div class="modal-footer">
		    	<!--<button type="button" class='btn btn-primary' id="btnPrecargaInsertar">Grabar</button>-->
		      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		    </div>
		  </div>
		</div>
	</div>
	<div id="modal_ver_archivo" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg" style="width: 90%;">
		  <div class="modal-content">
		    <div class="modal-header">
		      <button type="button" class="close" data-dismiss="modal">X</button>
		      <h4 class="modal-title">Lote pendiente de aprobacion</h4>
		    </div>
		    <div class="modal-body">
		    	<div class="col-md-12">
		    		<div class="row">
		      		<div class="col-md-12">
		      			<table id="table-ver-archivo" class="table table-sm">
		      				<thead>
		      					<tr>
									<th>CUIL</th>
									<th>Apellido y Nombre</th>
									<th>F. Nac.</th>
									<th>Sexo</th>
									<th>Domicilio</th>
									<th>Localidad</th>
									<th>Cod. Pos.</th>
									<th>Provincia</th>
		      					</tr>
		      				</thead>
		      				<tbody></tbody>
		      			</table>
		      		</div>
		    		</div>
		    	</div>
		    </div>
	    	<br>
		    <div class="modal-footer">
		    	<button type="button" class='btn btn-primary' id="btnAprobarLote">Aprobar</button>
		      <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		    </div>
		  </div>
		</div>
	</div>
</body>
<script>
	var id_usuario = "<?=$id_usuario;?>";
	var usuario = "<?=$usuario;?>";
	var _validFileExtensions = [".xls", ".xlsx", ".csv"]; 
	$(function(){
		CargarProcesados();
		CargarDesreguladoras();

		$('#btnPrecarga').on('click',function(){

			//var conf = confirm('¿Seguro?');
			var conf = true;
			if(conf){
				$(this).attr('disabled','disabled');
				$(this).html('');					
				$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');

				//Declarar variables en form_data
				var archivo_nombre =  getFile(file_precarga.value);
				var archivo_ext =  file_precarga.value.split('.')[1];
				var parametro = 'carga_previa_solicitudes';
				var id_desreguladora=$('#desreguladora').val();
				var frm = document.getElementById("form1");  
			    var form_data = new FormData(frm); 
			    form_data.append('parametro',parametro);
			    form_data.append('nombre', archivo_nombre);
			    form_data.append('extension', archivo_ext);
			    form_data.append('id_usuario',id_usuario);
			    form_data.append('usuario',usuario);
			    form_data.append('id_desreguladora',id_desreguladora);
			    for(var pair of form_data.entries()){console.log(pair[0]+ ': ' + pair[1]);}
			    //Fin declarar variables en form_data
			    //return false;
			   
		    	$.ajax({
					url: 'ajax.php',
					dataType: 'html',
			        cache: false,
			        contentType: false,
			        processData: false,
			        data: form_data,                         
			        type: 'post',
				})
				.done(function(data){
					console.log(data);
					$('#modal_precarga').modal('show');
					$('#precarga_vista_previa').html(data);
					$('#btnPrecargaInsertar').show();
					
				}); 
			    $(this).removeAttr('disabled');
				$(this).html('');					
				$(this).html('Vista Previa');
			}
		});
		$("#btnPrecargaInsertar").on('click',function(){
			var desreguladora =	$( "#desreguladora option:selected" ).text();
			var conf = confirm('¿Confirma que este lote es para '+desreguladora+'?');
			if(conf){
				$(this).attr('disabled','disabled');
				$(this).html('');					
				$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
				//Declarar variables en form_data
				var archivo_nombre =  getFile(file_precarga.value);
				var archivo_ext =  file_precarga.value.split('.')[1];
				var parametro = 'grabar_solicitudes';
				var id_desreguladora=$('#desreguladora').val();
			    
				var frm = document.getElementById("form1");  
			    var form_data = new FormData(frm); 
			    form_data.append('parametro',parametro);
			    form_data.append('nombre', archivo_nombre);
			    form_data.append('extension', archivo_ext);
			    form_data.append('id_usuario',id_usuario);
			    form_data.append('usuario',usuario);
			    form_data.append('id_desreguladora',id_desreguladora);
			    form_data.append('desreguladora',desreguladora);
			    for(var pair of form_data.entries()){console.log(pair[0]+ ': ' + pair[1]);}
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
					//console.log(data.substr(0,4));

					if(data.substr(0,4)!=="error"){
						alert("Su nuevo lote es "+data+" para la capita "+desreguladora);
					}else{
						alert('Hubo un problema, comuniquese con sistemas');
						console.log(data);
					}
					$('#modal_precarga').modal('hide');
					$('#precarga_vista_previa').html("");
					$('#btnPrecargaInsertar').hide();
				}); 
			}
			$(this).removeAttr('disabled');
			$(this).html('');					
			$(this).html('Grabar');
		});
		$(document).on('click','.btn-ver-procesado',function(e){
			$('#modal_procesado').modal('show');
			$('#table-ver-lote-procesado tbody').html("");
			$('#table-ver-lote-procesado tbody').html("<tr><td align='center' colspan=5><i class='fas fa-sync-alt fa-2x fa-spin'></i></td></tr>");

			var id_lote = $(this).data("id");
			$.ajax({
				url: 'ajax.php',
				type: 'GET',
				dataType: 'json',
				data: {parametro: 'ver_lote_procesado',id_lote: id_lote},
			})
			.done(function(data) {
				console.table(data);
				$('#table-ver-lote-procesado tbody').html("");
				for(var i=0; i<=data.length-1; i++){
					if(data[i]['desreguladora_solicitada'] === data[i]['desreguladora_activa']){
						var bgcolor = "class='success'";
						var estado = "Reclamado";
					}else if(!data[i]['desreguladora_activa']){
						var bgcolor = "class='error'";
						var estado = "Inexistente en Altas";
					}else{
						var bgcolor = "class='warning'";
						var estado = "Reclamado por otra Gerenciadora";
					}
					$('#table-ver-lote-procesado tbody').append(
						"<tr "+bgcolor+">"
							+`
								<td>
									<a target='_NEW' href='../../ver_grupo_familiar/index.php?id_titular=${data[i]['id_afiliado']}&id_af_consultado=${data[i]['id_afiliado']}'>
										${data[i]['cuil']}
									</a>
								</td>`
							+"<td>"+data[i]['cuil']+"</td>"
							+"<td>"+data[i]['fn']+"</td>"
							+"<td>"+data[i]['ayn']+"</td>"
							+"<td>"+data[i]['sexo']+"</td>"
							+"<td>"+data[i]['desreguladora_solicitada']+"</td>"
							+"<td>"+data[i]['desreguladora_activa']+"</td>"
							+"<td>"+data[i]['provincia']+"</td>"
							+"<td>"+data[i]['localidad']+"</td>"
						+"</tr>"
					);
				}
				
			})
			.fail(function(data) {
				console.log(data);
			});
		});
		$(document).on('click','.btn-ver-archivo',function(e){
			$('#modal_ver_archivo').modal('show');
			$('#table-ver-archivo tbody').html("");
			$('#table-ver-archivo tbody').html("<tr><td align='center' colspan=4><i class='fas fa-sync-alt fa-2x fa-spin'></i></td></tr>");

			var id_lote = $(this).data("id");
			$.ajax({
				url: 'ajax.php',
				type: 'GET',
				dataType: 'json',
				data: {parametro: 'ver_archivo_cargado',id_lote: id_lote},
			})
			.done(function(data) {
				console.table(data);
				$('#table-ver-archivo tbody').html("");
				for(var i=0; i<=data.length-1; i++){
					$('#table-ver-archivo tbody').append(
						"<tr>"
							+"<td>"+data[i]['cuil']+"</td>"
							+"<td>"+data[i]['ayn']+"</td>"
							+"<td>"+data[i]['fn']+"</td>"
							+"<td>"+data[i]['sexo']+"</td>"
							+"<td>"+data[i]['domicilio']+"</td>"
							+"<td>"+data[i]['localidad']+"</td>"
							+"<td>"+data[i]['cp']+"</td>"
							+"<td>"+data[i]['provincia']+"</td>"
						+"</tr>"
					);
				}

				$('#btnAprobarLote').data('id_lote',id_lote);
				
			})
			.fail(function(data) {
				console.log(data);
			});
		});
		$('#btnAprobarLote').on('click',function(e){
			
			e.preventDefault();
			var conf = confirm('¿Seguro?');
			if(conf){	
				var id_lote = $(this).data('id_lote');
				var datos = {
					"parametro": "procesar_lote",
					"id_lote":id_lote
				};

				$.ajax({
					url: 'ajax.php',
					type: 'POST',
					dataType: 'text',
					data: datos,
				})
				.done(function(data) {
					if(data === 'ok'){
						alert('Lote procesado.')
					}
					else{
						alert('Ocurrio un problema '+data);
						console.log(data);
					}
					return false;
				})
				.fail(function() {
					alert('Ocurrio un error');
				})
				.always(function() {
					$('#modal_ver_archivo').modal('hide');
					CargarProcesados();
				});
			}
		});
	}());

    function CargarProcesados(){
    	
    	$('#lista_procesados tbody').html('');

    	
    	$.getJSON('ajax.php',
    		{parametro:'TraerProcesados'},
    		function(data){
    			console.table(data);
					if(data[0]['error']){

    				$('#lista_procesados tbody').append("<tr><td colspan=7 align='center'>No hay registros</td></tr>");
    			}else{
    				if(data.length>0){

    					var procesado='';
    					for(var i=0; i<=data.length-1; i++){
    						
    						boton_aprobar = boton_ver_resolucion = ``;

    						let {id} = data[i];

								if(data[i]['estado']==='procesado'){
									estado = 'Aprobado';
									procesado = '';
									boton_ver_resolucion = `
										<li>
											<a class='btn-ver-procesado' data-id='${id}' title='Ver impacto en el padron'>
												<i class='fas fa-eye'></i></i>&nbsp; Ver Resolucion
											</a>
										</li>
									`;
								}else{
									estado = 'En espera';
									procesado = '';
									boton_ver_resolucion = `
										<li>
											<a class='btn-ver-procesado' data-id='${id}' title='Ver impacto en el padron'>
												<i class='fas fa-eye'></i></i>&nbsp; Ver Resolucion
											</a>
										</li>
									`;
									boton_aprobar = "<a class='btn-ver-archivo' data-id='"+data[i]['id']+"' title='Aprobar lote'> "
											+"<i class='fas fa-wrench'></i>&nbsp; Aprobar lote"
										+"</a>";
								}
    						$('#lista_procesados tbody').append(
    							"<tr>"
								+"<td>"+data[i]['id']+"</td>"
								+"<td>"
									+"<div class='btn btn-group btn-group-default'>"
										+"<button style='margin-left: 20%; margin-right: auto;' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button' "+procesado+">"
												+"<i class='fa fa-ellipsis-v' aria-hidden='true'></i>"
										+"</button>"
										+"<ul class='dropdown-menu'>"
											+"<li>"
												
												+`
													${boton_aprobar}
													${boton_ver_resolucion}
												`
											+"</li>"
										+"</ul>"
									+"</div>"
								+"</td>"
								+`<td>${estado}</td>`
								+"<td>"+data[i]['descripcion']+"</td>"
								+"<td>"+data[i]['archivo']+"</td>"			
								+"<td>"+data[i]['cant_registros']+"</td>"
								+"<td>"+data[i]['usuario']+"</td>"
								+"<td>"+data[i]['fechador']+"</td>"						
								+"</tr>"
  							);
    					}
    				}else{
    					$('#lista_procesados tbody').append("<tr><td colspan=7 align='center'>No hay registros</td></tr>");
    				}
    			}
    		});
    }
    function CargarDesreguladoras(){
    	$.getJSON('ajax.php',{ parametro: "desreguladoras" },function(data){
    		$.each(data, function (key, item) {
		     		$("#desreguladora").append("<option value="+item.id+">"+item.convenio+"</option>");
		    });
    	});
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
</html>