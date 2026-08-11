<?php
	session_start();

	$tipo_perfil = $_SESSION['perfil'];
	$user=$_SESSION["usu"];
	$id_user=$_SESSION["iduser"];
	if ( $user == "" ){ 
		echo "Sesion Expirada, vuelva a loguear"; 
		exit();
	}
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Buscar Afiliado</title>
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<!-- DataTables CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
	<style>
		body{
			background-color: #454d55;
			font-size:16px;
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
			margin-left:15px;
			margin-right:15px;
		}
		.table-container{
			margin-top: 20px;
			height:360px;
			overflow: scroll;
			overflow-x: hidden;
			width: 100%;
		}
		.mySubmit{
			padding-left: 35px;
			padding-top: 10px;
		}
		.mySubmit a{
			width:150px;
		}
	</style>
	<style>
	/* Estilo personalizado para el campo de búsqueda en DataTables */
	.dataTables_filter label {
	  color: white; /* Texto del label "Buscar" */
	}

	.dataTables_filter input {
	  background-color: white !important; /* Fondo blanco */
	  color: black !important;            /* Texto negro */
	  border: 1px solid #ccc;
	  padding: 4px 6px;
	  border-radius: 4px;
	}
	</style>
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container-fluid">
		    <div class="navbar-header" style="margin-right: 155px;">
		      <a class="navbar-brand" href="#" style="margin-left: 55px;">CONSULTA AFILIADOS</a>
		    </div>
		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
		    	<span class="navbar-toggler-icon"></span>
		  	</button>
	  		<div class="collapse navbar-collapse" id="navbarNavDropdown">
	  			
			</div>
		</div>
	</nav>

	<!-- Body -->
	<div class="container-fluid">

		<!-- Seccion del Formulario de busqueda -->
		<div class="row">
			<div class="col-md-2"></div>

			<!-- Formulario de busqueda -->
			<div class="col-md-8">
				<div style="height: 150px">
					<div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-10">
							

							<div class="input-group mt-3 mb-3">
								  <div class="input-group-prepend">
								    <button id="btnSeleccione" style='color: white;' type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
								      Buscando por beneficiario
								    </button>
								    <div class="dropdown-menu">
								      
								      <a class="dropdown-item list-parametro" data-p_campo="dni" data-p_campo_mostrar="DNI">DNI</a>
								      <a class="dropdown-item list-parametro" data-p_campo="cuil" data-p_campo_mostrar="CUIL">CUIL</a>
								      <a class="dropdown-item list-parametro" data-p_campo="beneficiario" data-p_campo_mostrar="beneficiario">Numero de beneficiario</a>
								      <a class="dropdown-item list-parametro" data-p_campo="ayn" data-p_campo_mostrar="Apellido y nombre">Apellido y nombre</a>
								      <a class="dropdown-item list-parametro" data-p_campo="cuit" data-p_campo_mostrar="CUIT">CUIT (test)</a>
								    </div>
								</div>
							  	<input type="text" class="form-control" id="inp_parametro" placeholder="beneficiario">
							  	<input type="hidden" name="campo_busqueda" id="campo_busqueda" value="beneficiario">
							 	<a id="btnEnviar" class="btn btn-success" style="margin-left: 10px;" >
							 		<span id="s_buscar"><i class="fas fa-search"></i></span> Buscar
							  	</a>
							  	<button type="button" style='margin-left: 10px;' class="btn btn-secondary" data-toggle="modal" data-target="#historialModal">Ver Historial</button>
							</div>

						</div>
						<div class="col-md-1">
							
						</div>
					</div>
				</div>
			</div>
			<!-- FIN formulario de busqueda -->
			<div class="col-md-2"></div>
		</div>

		<!-- Btn nuevo afiliado -->
		<div class="row">
			<a class="btn btn-warning" id="btnAlta" data-toggle='modal' data-target='#modal_add_afi' style="margin: auto;">
				<i class="fas fa-user-plus"></i> Agregar nuevo titular
			</a>
			
		</div>
		<!-- FIN Btn nuevo afiliado -->

		<!-- Historial de buqueda y resultados de la busqueda -->
		<div class="row">
			<!-- Historial de busqueda -->
			<!-- <div class="col-md-2">
				<label style="color: white;font-style: oblique;">
					Historial de busqueda
				</label>
			</div> -->
			<!-- Resultados de la busqueda -->
			<div class="col-md-12">
				<div class="table-container" id="TablaNombres">
					<label style="color: white;font-style: oblique;">Resultados de la busqueda (Haga click sobre la fila para ver el grupo familiar del afiliado)</label>
					<table id="tabListado" class="table table-hover table-dark table-striped" style="font-size: 12px;">
						<thead>
							<tr>
						        <th>ID afiliado</th>
						        <th>Beneficiario</th>
						        <th>CUIL</th>
						        <th>Apellido</th>						        
						        <th>Nombre</th>
						        <th>Tipo Beneficio</th>
						        <th>Gerenciadora</th>
						        <th>Filial</th>
						    </tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- FIN - Historial de buqueda y resultados de la busqueda -->

		<!-- Modal carga afiliado -->
		<div class="modal fade bd-example-modal-lg" id="modal_add_afi" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLongTitle">Ingreso manual de nuevo afiliado</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">   
		     	<span> <b>Todos los campos son requeridos</b> salvo los que indiquen que no.</span>,<b> Ej:</b>  <span style="color: red;">(*) No requerido</span>   
		     	<hr>
		      	<!-- Tab alta afiliado -->
				<table id="tabAltaAfiliado" class="table" style="margin: auto; font-size: 12px;">
					<tr>
						<th>DNI </th>
						<td>						
							<input type="text" name="t_dni" id="t_dni" placeholder="dni">
						</td>
						<th>Sexo</th>
						<td>						
							<select name="t_sexo" id="t_sexo">
								<option value="M">M - Masculino</option>
								<option value="F">F - Femenino</option>
							</select>
						</td>		
					</tr>
					<tr>
						<th>Desreguladora</th>
						<td>
							<select id="t_desreguladora" name="t_desreguladora" class="form-control">
												      
						    </select>
						</td>
						<!--
						<th>Delegacion</th>
						<td>
							<select id="t_delegacion" name="t_delegacion" class="form-control">					      
						    </select>
						</td>
						-->
						<th>
	            			Seccional
	            		</th>
	            		<td>
	            			<select name="t_seccional" id="t_seccional" class="form-control">
	            				<option value="">Seleccione</option>
	            			</select>
	            		</td>	
					</tr>
					<tr>
						<th>Tipo de beneficiario</th>
						<td>
							<select name="t_tbt" id="t_tbt">
								<option value="">Seleccione</option>
							</select>						
						</td>
						<th>Situacion revista</th>
						<td>
							<select name="t_revista" id="t_revista">
								<option value="">Seleccione</option>
							</select>
						</td>	
					</tr>
					<tr>
						<th>Beneficiario</th>
						<td>
							<input id="t_nben" name="t_nben" type="text" placeholder="" class="form-control input-md" >
						</td>
						<th>Gpar</th>
						<td>
							<input id="t_gpar" name="t_gpar" type="text" value="00" class="form-control input-md" readonly>
						</td>	
					</tr>
					<tr>
						<th>CUIL</th>
						<td>
							<input id="t_cuil" name="t_cuil" type="text" placeholder="cuil" class="form-control input-md">
							<span>El cuil es calculado con el dni y sexo</span>
						</td>	
						<th>Fecha nacimiento</th>
						<td>
							<input id="t_fn" name="t_fn" type="date" class="form-control input-md">
						</td>
					</tr>
					<tr>
						<th>Apellido</th>
						<td>
							<input id="t_apellido" name="t_apellido" type="text" placeholder="apellido" class="form-control input-md" required="">
						</td>
						<th>Nombre</th>
						<td>
							<input id="t_nombre" name="t_nombre" type="text" placeholder="nombre" class="form-control input-md" required="">
						</td>		
					</tr>
					<tr>
						<th>Fecha alta</th>
						<td>
							<input id="t_fecha_alta" name="t_fecha_alta" type="date" value="<?=date('Y-m-d');?>" class="form-control input-md">
						</td>
						<th>Incapacidad</th>
						<td>
							<select id="t_incapacidad" name="t_incapacidad" class="form-control">
						      <option value="00">00 - No incapacitado</option>
						      <option value="01">01 - Incapacidad</option>
						    </select>
						</td>							
					</tr>
					<tr>
						<th>Nacionalidad</th>
						<td>
							<select id="t_nacionalidad" name="t_nacionalidad" class="form-control">					      
						    </select>
						</td>
						<th>Estado civil</th>
						<td>
							<select id="t_estado_civil" name="t_estado_civil" class="form-control">					      
						    </select>
						</td>		
					</tr>
					<tr>
						<th>Telefono</th>
						<td>
							<input id="t_telefono" name="t_telefono" type="text" placeholder="telefono" class="form-control input-md">
							<span style="color: red;">(*) No requerido</span>
						</td>
						<th>Email</th>
						<td>
							<input id="t_email" name="t_email" type="email" placeholder="email" class="form-control input-md">
							<span style="color: red;">(*) No requerido</span>
						</td>		
					</tr>
					<!-- Domicilio Familiar -->
					
					<tr >
						<th>Provincia</th>
						<td>						
							<select name="t_provincia" id="t_provincia">							
							</select>
						</td>					
					</tr>
					<tr >
						<th>Localidad</th>
						<td colspan="3">	
							<!--					
							<datalist name="fm_localidad" id="fm_localidad">							
							</datalist> -->
							<input id="t_inp_localidad" list="t_localidad" class="col-sm-12 custom-select custom-select-sm">
							<datalist id="t_localidad" name="t_localidad">						    
							</datalist>
							<input type="hidden" name="t_id_localidad" id="t_id_localidad">
							<span>Al seleccionar la localidad luego presione TAB y pase al siguiente campo</span>
						</td>
					</tr>
					<tr >
						<th>Calle</th>
						<td>
							<input type="text" name="t_calle" id="t_calle">
						</td>
						<th>Numero</th>
						<td>
							<input type="text" name="t_numero" id="t_numero">
						</td>
					</tr>
					<tr >
						<th>Piso</th>
						<td>
							<input type="text" name="t_piso" id="t_piso">
							<span style="color: red;">(*) No requerido</span>
						</td>
						<th>Departamento</th>
						<td>
							<input type="text" name="t_departamento" id="t_departamento">
							<span style="color: red;">(*) No requerido</span>
						</td>
					</tr>
					<tr>
						<th>Nota de alta de afiliado </th>
						<td colspan="3">
							<textarea name="observacion" id="observacion" class="form-control"></textarea>
							<span style="color: red;">(*) No requerido</span>
						</td>
					</tr>
				</table>
				<hr>
				<span style="color: blue;">(*) Controle antes de confirmar que todos los campos requeridos esten completos.</span>
				
				<!-- Fin tabla alta afiliado -->
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		        <button type="button" class="btn btn-warning" id="btnAltaAfiliado">Confirmar alta</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- FIN - Modal carga afiliado -->


	</div>

	<div class="modal fade" id="historialModal" tabindex="-1" role="dialog" aria-labelledby="historialModalLabel" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="historialModalLabel">Historial de Búsquedas</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body">
	            	<input type="text" id="buscarHistorial" class="form-control mb-2" placeholder="Buscar en el historial...">
	                <ul id="historialLista" class="list-group"></ul>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- BOOTSTRAP, Popper.js, and jQuery -->
	<script src="jquery.min.js"></script>
	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	<script type="text/javascript" src="notify.min.js"></script>

	<script>
		var tipo_perfil = "<?php echo $tipo_perfil; ?>";
		var info = [];

		$('#rg_button').click();

		$(function(){	
			if(tipo_perfil !='admin'){
				$('#btnAlta').hide();
			}
			$('select').addClass('form-control');
			$('input[type=text]').addClass('form-control');
			$('input[type=date]').addClass('form-control');

			var id_titular=0 ;
			
			var title_item = "Hacer click para ver el grupo familiar";
			var p_busqueda = localStorage.getItem("p_busqueda");

			if(p_busqueda==null){
				$('#inp_parametro').val("");
				$('#inp_parametro').attr('placeholder','DNI');
				$('#btnSeleccione').html("");
				$('#btnSeleccione').html("Buscando por DNI");
				$('#campo_busqueda').val("dni");
				$('#inp_parametro').focus();
			}
			else{

				
				

				$('#btnSeleccione').html("Buscando por "+p_busqueda);
				$('#inp_parametro').val("");
				var placeholder_cuil = "" ;
				
				if(p_busqueda=="cuil"){
					placeholder_cuil = 'CUIL - Acepto con o sin guiones';
					$('#inp_parametro').attr('placeholder',placeholder_cuil);
				}
				else{
					$('#inp_parametro').attr('placeholder',p_busqueda.toUpperCase());
				}
				
				$('#btnSeleccione').html("");
				$('#btnSeleccione').html("Buscando por "+p_busqueda);
				$('#campo_busqueda').val(p_busqueda);
				$('#inp_parametro').focus();
					
			}

			//var $myGroup = $('#myGroup');
			$('#myGroup').on('show.bs.collapse','.collapse', function() {
			  //console.log('Hola');
			  $('#myGroup').find('.collapse.show').removeClass('show');
			  $(this).addClass('show');
			})
			
			//Busqueda de afiliado
			/*********************************************************/
			$('.list-parametro').on('click',function(){
				//console.log($(this).data('p_campo'))
				var p_campo = $(this).data('p_campo') ;
				var p_campo_mostrar = $(this).data('p_campo_mostrar') ;
				
				var placeholder_cuil = "" ;
				$('#inp_parametro').val("");
				if(p_campo=="cuil"){
					placeholder_cuil = 'CUIL - Acepto con o sin guiones';
					$('#inp_parametro').attr('placeholder',placeholder_cuil);
				}else{
					$('#inp_parametro').attr('placeholder',p_campo.toUpperCase());
				}
				$('#btnSeleccione').html("");
				$('#btnSeleccione').html("Buscando por "+p_campo_mostrar);
				$('#campo_busqueda').val(p_campo);
				$('#inp_parametro').focus();

				localStorage.setItem("p_busqueda", p_campo);

			})

			$('#btnEnviar').on('click',function(e){
				e.preventDefault();
				$(this).attr('disabled','disabled');
				$('#btnEnviar').html('');					
				$('#btnEnviar').html('<i class="fas fa-sync-alt fa-spin"></i> Buscando');
				
				buscar_afiliado(title_item)
				
				
			})
			
			$('#inp_parametro').on('keypress', function (e) {

		         if(e.which === 13){

		         	$('#btnEnviar').attr('disabled','disabled');
					$('#btnEnviar').html('');					
					$('#btnEnviar').html('<i class="fas fa-sync-alt fa-spin"></i> Buscando');
					
					buscar_afiliado(title_item)
					
		         }
   			});

   			$('#tabListado ').on('click','.itemBusqueda',function(){

   				var id_titular = $(this).data('id_titular');
   				var id_afiliado = $(this).data('id_afiliado');
   				var cuil = $(this).data('cuil');
   				//cuil = cuil.replace('-','');
   				//console.log(id_titular);

   				var datos = {
					"parametro": "grabar_busqueda",
					"id_titular": id_titular,
					"cuil": cuil
				};

				$.ajax({

					url: 'ajax.php',
					type: 'get',
					data: datos,
					success: function(data){						
						
						
					}
				})

				window.location.href = "../ver_grupo_familiar/index.php?id_titular="+id_titular+"&id_af_consultado="+id_afiliado ;

   			})

   			$(document).on('click','.modal-itemBusqueda',function(e){
   				var id_titular = $(this).data('id_titular');
   				var id_afiliado = $(this).data('id_afiliado');
   				var cuil = $(this).data('cuil');
   				//cuil = cuil.replace('-','');
   				//console.log(id_titular);

   				var datos = {
					"parametro": "grabar_busqueda",
					"id_titular": id_titular,
					"cuil": cuil
				};

				$.ajax({

					url: 'ajax.php',
					type: 'get',
					data: datos,
					success: function(data){						
						
						
					}
				})

				window.location.href = "../ver_grupo_familiar/index.php?id_titular="+id_titular+"&id_af_consultado="+id_afiliado ;
   			})
   			/*********************************************************/
   			//FIN - Busqueda de afiliado



   			//Alta nuevo afiliado
   			/*********************************************************/
   			
			//provincia
			$.getJSON('ajax_selects.php',
						{ parametro: "provincia" },						       				
						function(datos){ 
							
							$.each(datos, function (key, item) {
				                $("select[name=t_provincia]").append("<option value="+item.id+">"+item.provincia+"</option>");
				            });

							$("#t_provincia").val(2).attr('selected','selected');

						}//fin function data

			);//fin getjson

			//Localidad
			$.getJSON('ajax_selects.php',
						{ parametro: "localidad", provincia: 2 },						       				
						function(datos){ 
							
							$.each(datos, function (key, item) {
				                $("#t_localidad").append("<option label='CP: "+item.localidad+"'  value='"+item.id+"' >");
				            });

						}//fin function data

			);//fin getjson

			//Seccional
			$.getJSON('ajax_selects.php',
						{ parametro: "seccional" },						       				
						function(datos){ 
							
							$.each(datos, function (key, item) {
				                
				                $("select[name=t_seccional]").append("<option value="+item.id+">"+item.seccional+"</option>");
				            });

				            //$("select[name=delegacion]").val(5023).attr('selected','selected');


						}//fin function data

			);//fin getjson

			$('#t_dni').on('blur',function(){

				var datos = {
					"parametro": "dni_existe",
					"dni": $(this).val()
				};

				$.ajax({

					url: 'ajax.php',
					type: 'get',
					data: datos,
					success: function(data){						
						
						//console.log(data);
						var mensaje = "";

						if(data.length>0){

							mensaje = data+" ya existe en el padron."
							/*
							$('#t_dni').notify(
							  mensaje, 
							  { position:"right bottom" }
							);
							*/

							alert(data+" ya existe en el padron.");	

						}
						

					}
				})

			})

			$('#btnAlta').on('click',function(){
				$('#t_desreguladora option:first').prop('selected', true);
				var id_desreguladora=$('#t_desreguladora').val();
				var id_tipo_aporte= $('#t_tbt').val();
				var datos = {
					'parametro':'prox_nben',
					'id_desreguladora':id_desreguladora,
					'id_tipo_aporte':id_tipo_aporte
				};
				$.ajax({
					url:'ajax.php',
					dataType:'json',
					type:'GET',
					data:datos
				}).then(function(data){
					$("#t_nben").val('');
					$("#t_nben").val(data[0]['prox_nben']);
				});
			})
			$('#t_tbt').on('change',function(){
				var id_desreguladora=$('#t_desreguladora').val();
				var id_tipo_aporte= $('#t_tbt').val();
				var datos = {
					'parametro':'prox_nben',
					'id_desreguladora':id_desreguladora,
					'id_tipo_aporte':id_tipo_aporte
				};
				$.ajax({
					url:'ajax.php',
					dataType:'json',
					type:'GET',
					data:datos
				}).then(function(data){
					$("#t_nben").val('');
					$("#t_nben").val(data[0]['prox_nben']);
				});
			})
			$('#tabAltaAfiliado').on('change','#t_provincia',function(){

				$("#t_localidad").empty();
				var provincia = $(this).val();
				
				$.getJSON('ajax_selects.php',
							{ parametro: "localidad", provincia: provincia },						       				
							function(datos){ 
								
								$.each(datos, function (key, item) {
					                $("#t_localidad").append("<option label='CP: "+item.localidad+"'  value='"+item.id+"' >");
					            });

							}//fin function data

				);//fin getjson
			})

			//Seleccion de la localidad
			$("#tabAltaAfiliado").on('blur','#t_inp_localidad',function(){
				
				var id_localidad = document.querySelector('#t_inp_localidad').value;
				var t_localidad = AddValue(document.getElementById('t_inp_localidad'),document.getElementById('t_localidad')) ;
								
				$('#t_id_localidad').val(id_localidad);				
				$('#t_inp_localidad').val(t_localidad);				

			})//FIN - Seleccion de la localidad

			//Consulta de CUIL
			$('#tabAltaAfiliado').on('blur change','#t_sexo',function(){

				var dni = $('#t_dni').val();
				var sexo = $(this).val();

				//Trae cuil
				$.getJSON('ajax_selects.php',
							{ parametro: "consulta_cuil", dni: dni, sexo: sexo },						       				
							function(data){ 
								
								if(data[0]['estado']==0){
									//alert('El afiliado no existe');
									//console.log('No trajo el domicilio');
									$.notify(data[0]['cuil'], "error");
								}
								else{
									
									$('#t_cuil').val(data[0]['cuil']);
									
								}


							}//fin function data

				);//fin getjson Trae datos personales

			})//FIN - Consulta de CUIL


			$('#btnAltaAfiliado').on('click',function(){

				var conf_alta = confirm("Confirma ?");

				if(conf_alta==true){
					
					//Datos principales
					var dni = $('#t_dni').val();
					var sexo = $('#t_sexo').val();
					var desreguladora = $('#t_desreguladora').val();
					var t_seccional = $('#t_seccional').val();
					//var delegacion = $('#t_delegacion').val();
					var tbt = $('#t_tbt').val();
					var revista = $('#t_revista').val();
					var nben = $('#t_nben').val();
					var gpar = $('#t_gpar').val();
					var cuil = $('#t_cuil').val();
					var fn = $('#t_fn').val();
					var apellido = $('#t_apellido').val();
					var nombre = $('#t_nombre').val();
					var fecha_alta = $('#t_fecha_alta').val();
					var incapacidad = $('#t_incapacidad').val();
					var nacionalidad = $('#t_nacionalidad').val();
					var estado_civil = $('#t_estado_civil').val();
					var telefono = $('#t_telefono').val();
					var email = $('#t_email').val();

					//Domicilio			
					var localidad = $('#t_id_localidad').val();
					var calle = $('#t_calle').val();
					var numero = $('#t_numero').val();
					var departamento = $('#t_departamento').val();
					var piso = $('#t_piso').val();

					var observacion = $('#observacion').val();

					//Armo el objeto
					var datos = {
						"parametro": "alta_afiliado",
						
						"dni": dni,
						"sexo": sexo,
						"desreguladora": desreguladora,
						"seccional": t_seccional,
						//"delegacion": delegacion,
						"tbt": tbt,
						"revista": revista,
						"nben": nben,
						"gpar": gpar,
						"cuil": cuil,
						"fn": fn,
						"apellido": apellido,
						"nombre": nombre,
						"fecha_alta": fecha_alta,
						"incapacidad": incapacidad,
						"nacionalidad": nacionalidad,
						"estado_civil": estado_civil,
						"telefono": telefono,
						"email": email,
						
						"localidad": localidad,
						"calle": calle,
						"numero": numero,
						"departamento": departamento,
						"piso": piso,

						"observacion": observacion

					};

					$.getJSON('ajax.php',
								datos,						       				
								function(data){ 
									
									if(data[0]['estado']=="ok"){
										alert("Grabado con exito") ;
										window.location.href = "../ver_grupo_familiar/index.php?id_titular="+data[0]['id_afiliado'] ;
									}
									else{
										console.log(data);
									}
									
								}//fin function data

					);//fin getjson

					
				}//Finaliza condicion en true
				else{
					
					return false;
				}

			})

   			/*********************************************************/
   			//FIN - Alta nuevo afiliado



   			//Historial
   			/*********************************************************/
			$("#historialModal").on("shown.bs.modal", function () {
			    cargarHistorial();
			    $("#buscarHistorial").val(""); // Limpia la barra de búsqueda al abrir el modal

			    // Función para filtrar la lista de historial mientras se escribe
				$("#buscarHistorial").on("keyup", function () {
				    let valor = $(this).val().toLowerCase();
				    console.log("valor "+valor);
				    
				    $(".historial-item").each(function () {
				        let texto = $(this).text().toLowerCase();
				        $(this).toggle(texto.indexOf(valor) > -1);
				    });
				});
			});
   			
			$('#historialLista').on('click','.historial-item',function(){

				var parametro = $(this).data('parametro');
				var inp_parametro = $(this).data('inp_parametro');

				console.log("parametro: "+parametro+" inp_parametro: "+inp_parametro);
				if(parametro=="dni" || parametro=="cuil"){
					//window.location.href = "";
					$("#btnSeleccione").click();
		            $(".list-parametro[data-p_campo='" + parametro + "']").click();
		            $("#inp_parametro").val(inp_parametro);		            
		            setTimeout(function() {
					    $("#btnEnviar").click();
					}, 1000); 
				}
				if(parametro=="beneficiario" || parametro=="ayn"){
					// Cambiar el texto del botón
		            $("#btnSeleccione").click();
		            $(".list-parametro[data-p_campo='" + parametro + "']").click();
		            $("#inp_parametro").val(inp_parametro);
		            setTimeout(function() {
					    $("#btnEnviar").click();
					}, 1000); 
		            
		            //$("#btnSeleccione").text(inp_parametro).attr("data-selected", parametro).click();
				}

				$('#historialModal').modal('hide');

			})
   			/*********************************************************/
   			//FIN - Historial

   			
		})

		function buscar_afiliado(title_item){

			$("#tabListado tbody").html("");

			//console.log()
			var input_parametro = $('#inp_parametro').val().replace('-','').replace('-','');

			guardarBusquedaLocal($('#campo_busqueda').val(), input_parametro);

			$.getJSON('ajax.php', {'parametro': $('#campo_busqueda').val(), 'inp_parametro': input_parametro }, function(data) {

				//console.log(data)
				if(data[0]['estado']=="error"){

					$.notify("La persona que busca no se encuentra en el padron", "error");
				}
				else{

					if ( $.fn.DataTable.isDataTable('#tabListado') ) {
					    $('#tabListado').DataTable().clear().destroy();
					}

					for(var i=0; i<=data.length-1 ;i++){

						var tr_color = "";
						var estado_f = data[i]['estado'].split("@");

						switch(estado_f[0]){
							case 'BAJA':
						   		// code block
						   		tr_color = " style='background-color: red;' ";
						    break;
						    default:
						    	tr_color = "";
						    break;

						}
			
						$("#tabListado tbody").append("<tr class='itemBusqueda' "+tr_color+" data-id_titular="+data[i]['id_titular']+" data-id_afiliado="+data[i]['id_afliliado']+" data-cuil="+data[i]['cuil']+" title='"+title_item+"'>"															
														+"<td>"+data[i]['id_afliliado']+"</td>"
														+"<td>"+data[i]['nben']+"</td>"
														+"<td>"+data[i]['cuil']+"</td>"
														+"<td>"+data[i]['apellido']+"</td>"
														+"<td>"+data[i]['nombre']+"</td>"
														+"<td>"+data[i]['tbt']+"</td>"
														+"<td>"+data[i]['desreguladora']+"</td>"			      				    				
														+"<td>"+data[i]['filial']+"</td>"			  
													+"</tr>") ;		
					}

					$('#tabListado').DataTable({
						    paging: false,
						    info: false,
						    ordering: false,
						    language: {
						      search: "Buscar:",
						      loadingRecords: "<i class='fas fa-spinner fa-spin'></i> Cargando...",
						      zeroRecords: "No se encontraron resultados",
						      emptyTable: "No hay datos disponibles"
						    }
						  });

				}	

				$('#btnEnviar').removeAttr('disabled');
				$('#btnEnviar').html('<span id="s_buscar"><i class="fas fa-search"></i></span> Buscar');
				
				
				
			});

		}
		function cargarHistorial() {
		    let historial = JSON.parse(localStorage.getItem("historial_busquedas")) || [];
		    let lista = $("#historialLista");
		    lista.empty();

		    if (historial.length === 0) {
		        lista.append("<li class='list-group-item text-muted'>No hay búsquedas recientes</li>");
		    } else {
		        // Ordenar historial por fecha (más reciente primero)
		        historial.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

		        historial.forEach(item => {
		            lista.append(`<li class="list-group-item historial-item" data-parametro='${item.parametro}' data-inp_parametro='${item.inp_parametro}'>
		                <strong>${item.parametro}:</strong> ${item.inp_parametro} 
		                <span class="text-muted float-right">${new Date(item.fecha).toLocaleString()}</span>
		            </li>`);
		        });
		    }
		}
		function guardarBusquedaLocal(parametro, inp_parametro) {
		    let historial = JSON.parse(localStorage.getItem("historial_busquedas")) || [];
		    
		    // Agregar nueva búsqueda con timestamp
		    historial.push({
		        parametro: parametro,
		        inp_parametro: inp_parametro,
		        fecha: new Date().toISOString()
		    });

		    // Filtrar búsquedas de más de 7 días
		    let ahora = new Date().getTime();
		    historial = historial.filter(item => {
		        let fechaGuardada = new Date(item.fecha).getTime();
		        return (ahora - fechaGuardada) <= (7 * 24 * 60 * 60 * 1000); // 7 días en milisegundos
		    });

		    // Guardar en LocalStorage
		    localStorage.setItem("historial_busquedas", JSON.stringify(historial));
		}

		function Buscar(element){
			var cuil_b = $(element).closest('tr').children('td:eq(2)').text();
			//alert('mando '+ cuil_b);
			$('#cuilSearch').val(cuil_b);
			var e = jQuery.Event("keypress");
			e.which = 13; // # Some key code value
			$("#cuilSearch").trigger(e);
		}

		function AddValue(el, dl){
		  if(el.value.trim() != ''){
		    var opSelected = dl.querySelector(`[value="${el.value}"]`);
		    return opSelected.getAttribute('label');
		  }
		}

		function alta_afiliado(){	
		}

	</script>
	<script type="text/javascript" src="db_selects.js"></script>
</body>
</html>