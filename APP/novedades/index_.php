<?php 	
//Recordar
//Falta que la verificacion en ajax si el archivo NO se creo por falta de datos, retorne al index y muestre un mensaje de porque no se creo
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Generar Novedades</title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

		
		<style>
			body{
				background-color: #454d55;
				font-size:11px;
			}
			/* width */
			::-webkit-scrollbar {
			width: 10px;
			}

			/* Track */
			::-webkit-scrollbar-track {
			background: #f1f1f1; 
			}

			/* Handle */
			::-webkit-scrollbar-thumb {
			background: #888; 
			}

			/* Handle on hover */
			::-webkit-scrollbar-thumb:hover {
			background: #555; 
			}
			.myLi{
				margin-left:35px;
				margin-right:15px;
			}
			.table-container{
				height:450px;
				overflow: scroll;
				overflow-x: hidden;
				width: 100%;
			}
			.mySubmit{
				padding-left: 350px;
				padding-top: 35px;
			}
			.mySubmit a{
				align-content: center;
				width:200px;
			}

			#novedades tbody tr td {
			  width: 20% ;
			}

			.big-checkbox {width: 18px; height: 18px;}
		</style>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<div class="container-fluid">
			    <div class="navbar-header" style="margin-right: 25px;">
			      <a class="navbar-brand" href="#">Generar Novedades</a>
			    </div>
			    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			    	<span class="navbar-toggler-icon"></span>
			  	</button>
		  		<div class="collapse navbar-collapse" id="navbarNavDropdown">
		  			<form name="test" method="POST" action="" enctype="multipart/form-data">
						<ul class="navbar-nav ">
					    	
				      		<li class="myLi navbar-brand" style="width:200px;">
				      			<span class="nav-link">Periodo</span>
								<select id="id_lote" class="form-control" required disabled>
									<option value="">Seleccione</option>
								</select>
							</li>
							<li class="myLi navbar-brand" style="margin-top:50px;">
						        <!--<span class="nav-link">Lote final:</span>-->
						        <input type="checkbox" class="form-check-input big-checkbox" value="SI" id="lote_final" />
						        <label class="form-check-label" for="lote_final">
									Lote final
								</label>
					      	</li>
							
							<li class="myLi navbar-brand" style="margin-top:50px;">
						        <label class="text-light" id="fecha_limite"></label>
					      	</li>
					      	<!--
					      	<li class="myLi navbar-brand">
						        <span class="nav-link">Fecha Cierre:</span>
						        <input type="date" name="hfecha" id="fhasta" value="<?//=date('Y-m-d'); ?>" class="form-control input-xs" max="2050-12-31" required />
					      	</li>
					      	-->
					      	<li class="myLi navbar-brand">
					      		<div class="mySubmit">
						    		<table>
											
						    		</table>
			      				</div> 
					      	</li>	
					    </ul>
					</form>
				</div>
			</div>
		</nav>
		<br>
		<center>
			<button id="btnEnviar" class="btn btn-success text-light">Crear</button>
			<button id="btnVer" class="btn btn-secondary text-light">Ver</button>
			<button id="btnCerrar" class="btn btn-danger text-light">Cerrar</button>	
		</center>
		<br>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<div id="Contenedor-wrapper">
						<table class='table table-hover table-dark table-sm' id='Contenedor'>
							<thead>
								<tr class='text-left'>
									<th>ID</th> 
									<th>CUIL</th>
									<th>AyN</th>
									<th>Sexo</th>
									<th>Fecha Nacimiento</th>
									<th></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
						<hr>
						<hr>	
					</div>
					<table id="novedades" class='table table-hover table-dark table-sm'>
					<thead>
						<tr>
							<th>ID</th>
							<th>Periodo</th>
							<th>Usuario</th>
							<th>Opciones</th>
						</tr>
					</thead>
					<tbody>	

					</tbody>
					</table>
				</div>
				<div class="col-md-1"></div>
			</div>
		</div>
	</body>
	<script>
		$(function(){
			CargarPeriodos();
			CargarNovedades();
			$('#Contenedor-wrapper').hide();
			$('#fecha_limite').hide();
			$('#btnCerrar').hide();

			$('#btnCerrar').on('click',function(){//Cerrar liquidacion Abiert (Creada o abierta recientemente, no importa)

				$('#Contenedor-wrapper').hide();

				$('#btnCerrar').hide();
			});
			$('#novedades').on('click','.btnDescargar',function(){

				var periodo = $(this).data('periodo_enviar');
				var fecha_cierre = $(this).data('fecha_cierre');

				window.open("ajax.php?parametro=CrearNovedades&parametro2=CrearArchivo&periodo="+periodo+"&fecha_cierre="+fecha_cierre+"&final=S");
				//parametro=CrearNovedades&parametro2=CrearArchivo&p_id_lote=3702&periodo=2021-08&fecha_cierre=2021-08-31
			});
			$(document).on('click',"#btnEnviar",function(e){//Crear Liquidacion

				e.preventDefault();
				

				if( $('#lote_final').prop('checked') ) {
	    			var final = "S"
				}else{
					var final = "N";
				}

				//return false;

				$('#btnCerrar').click();
				$("#btnEnviar").addClass('disabled');
				$("#btnEnviar").html('');					
				$("#btnEnviar").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
				
				var periodo = $('#id_lote').find(':selected').data('periodo');
				
				var fdesde =$('#id_lote').find(':selected').data('primer_dia');

				var fhasta =$('#id_lote').find(':selected').data('ultimo_dia');

				if(!fdesde){
					alert('Seleccione un periodo');

					$("#btnEnviar").removeClass('disabled');
					$("#btnEnviar").val('');
					$("#btnEnviar").val('Crear');

					return false;
				}
				
				if(!fdesde || !fhasta){
					alert('Hubo un problema');
					$("#btnEnviar").removeClass('disabled');
					$("#btnEnviar").val('');
					$("#btnEnviar").val('Crear');
				}


				const date1 = new Date($('#id_lote').find(':selected').data('primer_dia'));
				const date2 = new Date($('#id_lote').find(':selected').data('ultimo_dia'));
				const diffTime = Math.abs(date1 - date2);
				const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
				console.log(diffDays + " dias");
				

				if(date2 < date1){
					alert('Ha colocado las fechas de manera erronea');
					$("#btnEnviar").removeClass('disabled');
					$("#btnEnviar").val('');
					$("#btnEnviar").val('Crear');
				}
				else if(date2 >= date1){
					$('#descripcion').html('');
					$('#descripcion').append('Novedades: '+periodo+". Fecha de Cierre: "+fhasta);



					//alert('Rediriguiendo: '+ "ajax.php?parametro=CrearNovedades&periodo="+periodo+"&fecha_cierre="+fhasta)
					window.open("ajax.php?parametro=CrearNovedades&parametro2=CrearArchivo&periodo="+periodo+"&fecha_cierre="+fhasta+"&final="+final);
					//parametro=CrearNovedades&parametro2=CrearArchivo&p_id_lote=3702&periodo=2021-08&fecha_cierre=2021-08-31

					$("#btnEnviar").removeClass('disabled');
					$("#btnEnviar").val('');
					$("#btnEnviar").val('Crear');

				}
				CargarNovedades();
			});
			$(document).on('click','#btnVer',function(e){

				e.preventDefault();

				$('#btnCerrar').click();
				$("#btnVer").addClass('disabled');
				$("#btnVer").html('');					
				$("#btnVer").html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
				
				var id =$('#id_lote').find(':selected').data('id');

				if(!id){
					alert('Seleccione un periodo');

					$("#btnVer").removeClass('disabled');
					$("#btnVer").html('');
					$("#btnVer").html('Ver');

					return false;
				}
					
				var datos = {
					"parametro":"CrearNovedades",
					"parametro2":"ListarPersonas",
					"id":id
				};

				console.table(datos);

				$.ajax({
					url: 'ajax.php',
					type: 'GET',
					dataType: 'json',
					data: datos,
				})
				.done(function(data) {
					console.table(data);

					$('#Contenedor-wrapper').show();
					$('#btnCerrar').show();

					for(var i=0; i<=data.length-1 ;i++){
						
						$('#Contenedor tbody').append("<tr>"
								+"<td>"
									+data[i]['id_persona']
								+"</td>"
								+"<td>"
									+data[i]['cuil']
								+"</td>"
								+"<td>"
									+data[i]['ayn']
								+"</td>"
								+"<td>"
									+data[i]['sexo']
								+"</td>"
								+"<td>"
									+data[i]['fn']
								+"</td>"
								+"<td>"
									+"<a class='btn btn-info' target='_NEW' href='../ver_grupo_familiar/index.php?id_titular="+data[i]['id_afiliado']+"&id_af_consultado="+data[i]['id_afiliado']+"'><i class='fas fa-arrow-right'><i></a>"
								+"</td>"
							+"</tr>"
						);

					}
				})
				.fail(function(data) {
					console.log(data);
				})
				.always(function() {
					//console.table(data);
				});
				$("#btnVer").removeClass('disabled');
				$("#btnVer").html('');
				$("#btnVer").html('Ver');
			});
			$("#novedades").on('click','.novedades',function(){//Abrir Liquidacion
				//Descargar Archivo (Cuando tenga la query)
			});
			$('#id_lote').on('change',function(){
				
				var fecha_limite = $('#id_lote').find(':selected').data('fecha_limite');

				if(fecha_limite){
					$('#fecha_limite').html("Fecha Limite: "+fecha_limite);

					$('#fecha_limite').show();	
				}else{
					$('#fecha_limite').hide();
				}
			});
		});

		function CargarPeriodos(){
			$.getJSON('ajax.php', {parametro: 'CargarPeriodos'}, function(data) {

					for(var i=0;i<=data.length-1;i++){
						$('#id_lote').append("<option value='"+data[i]['id']+"' "
								+"data-periodo='"+data[i]['periodo']+"' "
								+"data-fecha_limite='"+data[i]['fecha_limite']+"' "
								+"data-id='"+data[i]['id']+"' "
								+"data-ultimo_dia='"+data[i]['ultimo_dia']+"' "
								+"data-primer_dia='"+data[i]['primer_dia']+"' "
								+">"
								+data[i]['periodo']+" ("+data[i]['cant_registros']+") "

								+"</option>");
					}

					$('#id_lote').removeAttr('disabled');
			});
		}
		function CargarNovedades(){
			$.getJSON('ajax.php',
				{parametro:'CargarNovedades'},
				function(data){
					$('#novedades tbody').html('');
					for(var i=0; i<=data.length-1 ;i++){
				
						$("#novedades tbody").append("<tr>"																
						+"<td  style='text-align: left;width:5%;'>"+data[i]['id']+"</td>"
						+"<td  style='text-align: left;'>"+data[i]['periodo']+"</td>"
						+"<td  style='text-align: left;'>"+data[i]['usuario']+"</td>"	
						+"<td>"
							+"<a type='button' class='btn btn-info btnDescargar' data-periodo='"+data[i]['periodo']+"' data-fecha_cierre='"+data[i]['fecha_cierre']+"'>Descargar</a>"
							//+"<a type='button' class='borrar_liquidacion btn btn-danger text-light' data-id_liquidacion='"+data[i]['id']+"' data-descripcion='"+data[i]['periodo']+"'>Borrar</a>"
						+"</td>"				      				
						+"</tr>") ;		
						}	
				});
		}
	</script>
</html>