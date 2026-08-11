<?php
include('../../Config/Conectar.inc');

session_start();

$tipo_perfil = $_SESSION["perfil"];
$user=$_SESSION["usu"];
$id_user=$_SESSION["iduser"];
if ( $user == "" ){ echo "Sesion Expirada, vuelva a loguear"; 
exit();
}

if($Subir=="Subir"){

	$target_path = "archivos/";
	$target_path = $target_path . basename( $_FILES['archivo']['name']);
	$nombre_archivo = basename( $_FILES['archivo']['name'] );

	if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)){ 
		echo "El archivo ". basename( $_FILES['archivo']['name']). " ha sido subido";

		$image = addslashes(fread(fopen($target_path, "r"), filesize($target_path)));

		if(strlen($image)!=0){

			$sql = "INSERT INTO $base_imagenes.patologias(id_afiliado,nombre_archivo,imagen,id_usuario)
			VALUES ($pat_id_afiliado,'$nombre_archivo','$image',$id_usuario)";

			mysql_query($sql) or die(mysql_error()."<br>".$sql);

			echo "<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>		
			<meta name='viewport' content='width=device-width, initial-scale=1'>
			<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
			<script defer src='https://use.fontawesome.com/releases/v5.0.6/js/all.js'></script>	

			<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>

			<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
			<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
			<head>
			<body>
			<div class='container'>
			<br><br>
			<div class='alert alert-success'>
			Subido con exito!!
			</div>
			<br>

			</div>
			</body>
			</html>";


			exec("rm -rf archivos/* ");
			header("Location: index.php?id_titular=$pat_id_titular");
			exit();
		}

	}
	else{
		echo "<html>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>		
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
		<script defer src='https://use.fontawesome.com/releases/v5.0.6/js/all.js'></script>	

		<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
		<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
		<head>
		<body>
		<div class='container'>
		<br><br>
		<div class='alert alert-danger'>
		Ha ocurrido un error, trate de nuevo!
		</div>
		<br>
		<center>							
		</div>
		</body>
		</html>";

		exit();
	}	
}

?>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Ver grupo familiar</title>
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" href="style.css">
</head>
<body>	

	<!-- Divs de info principal  -->
	<div class="row" style=" margin: 10px;">
		<div class="col info_principal">
			<a class='btn btn-danger btn-ms btn-error text-light btn-salir' href="../buscar_afiliado/" title="Consultar otro afiliado">
				<i class="fas fa-arrow-left fa-1x"></i> 
				<label style="margin-left: 5px;">
					Salir
				</label>
			</a>
		</div>
		<div class="col info_principal">
			<div class="alert alert-info">
				<span id="gf_desreguladora" style="margin-left: 15px;"><div class="spinner-border text-warning" style="margin: 3px;" ></div>
				</span> 
				<div id="gf_plan_medico_wrap" style="font-size: 13px; margin-top: 10px;">

					<p id="gf_plan_medico" style="margin-left: 15px;">
						
					</p>
					
				</div> 
			</div>		
		</div>
		<div class="col info_principal">
			<div class="alert alert-warning">
				<b><span id="gf_tbt"><div class="spinner-border text-warning" style="margin: 3px;" ></div></span> </b> 
				
				<div style="font-size: 13px; margin-top: 10px;">
					<p id="ult_periodo"></p>
				</div>				
				<div style="font-size: 13px;">
					<p id="ult_empresa"></p>
				</div>
			</div>
		</div>
		<div class="col info_principal" >
			<div class="alert alert-danger" id="divPresentaDesempleo" style="display: none;"></div>
		</div>
	</div>
	<hr>
	<!-- Cuerpo del formulario -->
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<div class="table-container ">
					<label class="labelTitulo">Grupo familiar</label>
					<table id="tabGrupoFamiliar" class="table table-dark tabla-sm " style="font-size: 11px;">
						<thead>
							<tr>
								<th>ID</th>
								<th></th>	
								<th>Estado</th>						        
								<th style='text-align: right;'># Beneficiario</th>
								<th>Afiliado</th>
								<th>Parentesco</th>
								<th>CUIL</th>
								<th>Fecha nacimiento</th>
								<th>Edad</th>
								<?if(INST_VGP_NRO_SIND){?>
									<th>Nro Sind</th>
								<?}?>
								<th class='incapacidad'>Incapacidad</th>		
								<?if(INST_VGP_PATOLOGIA){?>
									<th>Patologias</th>
								<?}?>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- FIN Tabla de grupo famliar  -->

		<!-- Botones -->
		<div class="row" style="padding: 10px;">
			<a class='btn btn-warning btn-ms btn-principal text-light' id="btnVerAportes" title="DDJJ - Aportes - Desempleo ">
				<i class="fas fa-clipboard-list"></i> Ver empresa y aportes
			</a>
			<a class='btn btn-info btn-ms btn-principal text-light' id="btnNuevoFamiliar" data-toggle='modal' data-target='#modal_add_fam' title="Dar de alta a un nuevo familiar en el grupo">
				<i class="fas fa-user-plus"></i> Agregar familiar
			</a>
		</div>
		<!-- FIN Botones -->

		<!-- Todos los datos de un afiliado  -->
		<div class="row" id="divBody">			
			<div class="container-fluid">
				<div class="row" style="padding: 20px;">
					<div class="col-xs-12 " style="width: 100%;">
						<div class="row" id="BodyCabecera">
							<div class="col-md-11">
								<label class="labelTitulo">Datos del afiliado <span id="s_nom_afil" style="font-weight: bold; margin-left: 5px;"></span> </label>
								<p style="font-size: 12px; color: white;">Nota: al modificar algun campo se activa el boton de guardar, si quiere descartar 
									el cambio simplemente no guarde.
								</p>
							</div>
							<div class="col-md-1">
								<a class="btn btn-info text-light" id="BodyCerrar">
									<i class="fas fa-times fa-3x"></i>
								</a>
							</div>
						</div>
						<input type="hidden" name="id_afiliado" id="id_afiliado" value="">
						<input type="hidden" name="id_titular" id="id_titular" value="<?=$_GET['id_titular'];?>" />
						<input type="hidden" name="id_af_consultado" id="id_af_consultado" value="<?=$_GET['id_af_consultado'];?>" />
						<input type="hidden" name="cuil_titular" id="cuil_titular" value="">
						<nav>
							<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
								<a class="nav-item nav-link active" id="nav-personales-tab" data-toggle="tab" href="#nav-personales" role="tab" aria-controls="nav-personales" aria-selected="true">Datos personales</a>
								<a class="nav-item nav-link" id="nav-domicilio-tab" data-toggle="tab" href="#nav-domicilio" role="tab" aria-controls="nav-domicilio" aria-selected="false">Domicilio</a>
								<a class="nav-item nav-link" id="nav-afiliacion-tab" data-toggle="tab" href="#nav-afiliacion" role="tab" aria-controls="nav-afiliacion" aria-selected="false">Datos de afiliacion</a>
							</div>
						</nav>
						<div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
							<!-- Datos personales -->
							<div class="tab-pane fade show active" id="nav-personales" role="tabpanel" aria-labelledby="nav-personales-tab">
								<input type="hidden" name="formDatosPersonales" value="0">
								<input type="hidden" name="DatosCE" value="0">
								<input type="hidden" name="sexo_ant" data-inp_estado="0">
								<input type="hidden" name="desreguladora_ant" data-inp_estado="0">   
								<input type="hidden" name="nben_ant" data-inp_estado="0">          
								<input type="hidden" name="gpar_ant" data-inp_estado="0">
								<input type="hidden" name="apellido_ant" data-inp_estado="0">
								<input type="hidden" name="nombre_ant" data-inp_estado="0">
								<input type="hidden" name="parentesco_ant" data-inp_estado="0">
								<input type="hidden" name="fn_ant" data-inp_estado="0">
								<input type="hidden" name="estado_civil_ant" data-inp_estado="0">
								<input type="hidden" name="incapacidad_ant" data-inp_estado="0">
								<input type="hidden" name="telefono_ant" data-inp_estado="0">
								<input type="hidden" name="email_ant" data-inp_estado="0">
								<input type="hidden" name="nd_ant" data-inp_estado="0">

								<table id="tabDatosPersonales" class="table table-hover table-dark tabla-sm table-striped" style="font-size: 11px;">
									<tr>
										<th>
											Beneficiario
										</th>
										<td>
											<input type="text" name="nben" class="form-control input-sm inp_personales">
										</td>
										<th>
											Cod parentesco
										</th>
										<td>
											<input type="text" name="gpar" class="form-control input-sm inp_personales">
										</td>
									</tr>
									<tr>
										<th class="td_col_1">DNI</th>
										<td class="td_col_1">
											<input type="text" id="nd" name="nd" class="form-control input-sm inp_personales">
										</td>
										<th>Sexo</th>
										<td>
											<select id="sexo" name="sexo" class="form-control input-sm inp_personales">
												<option value="M">M</option>
												<option value="F">F</option>
											</select>
										</td>
										<th>CUIL</th>
										<td>
											<input type="text" id="cuil" name="cuil" class="form-control input-sm inp_personales">
										</td>
									</tr>
									<tr>
										<th class="td_col_1">Apellido</th>
										<td class="td_col_1">
											<input type="text" name="apellido" class="form-control input-sm inp_personales">
										</td>
										<th>Nombre</th>
										<td>
											<input type="text" name="nombre" class="form-control input-sm inp_personales">
										</td>
										<th class="td_col_1">Desreguladora</th>
										<td class="td_col_1">
											<select name="desreguladora" id="p_desreguladora" class="inp_personales">                      				
											</select>
											<input type="hidden" id="observacion_desreguladora" />
										</td>
									</tr>
									<tr>
										<th class="td_col_1">Parentesco</th>
										<td class="td_col_1">
											<select id="parentesco" name="parentesco" class="inp_personales">                      				
											</select>
										</td>
										<th>Fec nacimiento</th>
										<td>
											<input type="date" name="fn" class="form-control input-sm inp_personales">
										</td>
										<th>Estado civil</th>
										<td>
											<select name="estado_civil" class="inp_personales">                      				
											</select>
										</td>
									</tr>
									<tr>
										<th>Incapacidad</th>
										<td>
											<select name="incapacidad" id="incapacidad" class="inp_personales">
												<option value="00">00 - NO</option>
												<option value="01">01 - SI</option>
											</select>
										</td>
										<th>Telefono</th>
										<td>
											<input type="text" name="telefono" class="form-control input-sm inp_personales">
										</td>
										<th>Email</th>
										<td>
											<input type="email" name="email" class="form-control input-sm inp_personales">
										</td>
									</tr>
								</table>
							</div>
							<!-- Datos domicilio -->
							<div class="tab-pane fade" id="nav-domicilio" role="tabpanel" aria-labelledby="nav-domicilio-tab">
								<input type="hidden" name="formDomicilio" value="0">
								<input type="hidden" name="id_domicilio_afiliado"  id="id_domicilio_afiliado">
								<input type="hidden" name="t_provincia_ant" data-inp_estado="0" > 		                    	
								<input type="hidden" name="t_id_localidad_ant" data-inp_estado="0">
								<input type="hidden" id="t_inp_localidad">
								<input type="hidden" name="calle_ant" data-inp_estado="0">
								<input type="hidden" name="numero_ant" data-inp_estado="0">
								<input type="hidden" name="piso_ant" data-inp_estado="0">
								<input type="hidden" name="depto_ant" data-inp_estado="0">

								<table id="tabDomicilio" class="table table-hover table-dark tabla-sm table-striped" style="font-size: 11px;">
									<tr>
										<th>Localidad</th>
										<td colspan=3>
											<select name="t_id_localidad" id="t_id_localidad" class="form-control input-sm inp_domicilio" style="width: 100%;" required>
											</select>
										</td>
									</tr>
									<tr>
										<th>Calle</th>
										<td>
											<input type="text" name="calle" class="form-control input-sm inp_domicilio">
										</td>
										<th>Numero</th>
										<td>
											<input type="text" name="numero" class="form-control input-sm inp_domicilio">
										</td>                      		
									</tr>
									<tr>
										<th>Piso</th>
										<td>
											<input type="text" name="piso" class="form-control input-sm inp_domicilio">
										</td>
										<th>Departamento</th>
										<td>
											<input type="text" name="depto" class="form-control input-sm inp_domicilio">
										</td>                      		
									</tr>

								</table>
							</div>

							<!-- Datos afiliacion -->
							<div class="tab-pane fade" id="nav-afiliacion" role="tabpanel" aria-labelledby="nav-afiliacion-tab">
								<input type="hidden" name="formDatosAfiliacion" value="0">

								<input type="hidden" name="tbt_ant" data-inp_estado="0" > 	
								<input type="hidden" name="revista_ant" data-inp_estado="0" > 	
								<input type="hidden" name="seccional_ant" data-inp_estado="0" > 	
								<input type="hidden" name="plan_medico_ant" id="plan_medico_ant" data-inp_estado="0" >
								<table class="table table-hover table-dark tabla-sm table-striped" style="font-size: 11px; width: 600px; margin: auto;">
									<tr>
										<th>
											Tipo beneficiario
										</th>
										<td>
											<select name="tbt" id="tbt_ll" class="inp_dtos_afiliacion">
												<option value="">Seleccione</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>
											Situacion de revista
										</th>
										<td>
											<select name="revista" id="revista" class="inp_dtos_afiliacion">
												<option value="">Seleccione</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>
											Filial
										</th>
										<td>
											<select name="seccional" class="inp_dtos_afiliacion">
												<option value="">Seleccione</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>
											Plan Medico
										</th>
										<td>
											<select name="plan_medico" id="plan_medico" class="inp_dtos_afiliacion">
												<option value="">Seleccione</option>
											</select>
										</td>
									</tr>
		                    	<!--
		                    	<tr>		                    		
		                    		<th>
		                    			Ultima fecha de alta
		                    		</th>
		                    		<td>
		                    			<input type="date" name="fecha_alta" readonly style="width: 350px; float: left;">
		                    			<a class="btn btn-info btn-sm btn-modificaFechaAB" style="margin-left: 5px;"
		                    				data-operacion="Alta"
		                    				data-tipo_afil="" 
		                    				data-fec_alta="" 
		                    				data-fec_baja="" 
		                    				data-id_afiliado="" 
		                    				data-cuil=""  >
		                    				Modificar
		                    			</a>
		                    		</td>		                    		
		                    	</tr>
		                    	<tr>
		                    		<th>
		                    			Ultima fecha de baja
		                    		</th>
		                    		<td>                    			
		                    			<input type="date" name="fecha_baja" readonly style="width: 350px; float: left; ">
		                    			<a class="btn btn-info btn-sm btn-modificaFechaAB" style="margin-left: 5px;"
		                    				data-operacion="Baja"
		                    				data-tipo_afil="" 
		                    				data-fec_alta="" 
		                    				data-fec_baja="" 
		                    				data-id_afiliado="" 
		                    				data-cuil=""  >
		                    				Modificar
		                    			</a>
		                    		</td>
		                    	</tr>
		                    -->
		                  </table>
		                </div>

		                <!-- Datos historicos -->
		                    <!--
		                    <div class="tab-pane fade" id="nav-historico" role="tabpanel" aria-labelledby="nav-historico-tab">
		                    	
		                    	<a class="btn btn-success" style="margin: 20px;" data-toggle="modal" data-target="#ModificaEstado">
		                    		Modificar estado afiliado
		                    	</a>
		                    	
		                      	<table id='tabHistorico' class='table table-hover table-dark tabla-sm table-striped' style="font-size: 11px;">
									<thead>	
										<tr>
											<th>#</th>
											<th>Origen</th>
											<th>Movimiento</th>
											<th>Tipo</th>
											<th>Fecha</th>			
										</tr>
									</thead>	
									<tbody>													
									</tbody>
								</table>
		                    </div>
		                  -->
		                </div>
		                <div class="row justify-content-center" id="BodyFooter">
		                	<div class="col align-self-center">
		                		<a class='btn btn-link btn-ms btn-principal text-light' id="btnGuardarCambios" title="Guarda los cambios realizados a un afiliado">
		                			<i class="fas fa-save"></i> Guardar cambios
		                		</a>
		                	</div>
		                </div>
		              </div>
		            </div>
		          </div>
		        </div>
		        <!-- FIN Todos los datos de un afiliado  -->

		      </div>
		      <!-- FIN Cuerpo del formulario -->	

		      <hr>

		      <!-- Modales-->

		      <!-- Loading Small modal -->
		      <button type="button" style="display: none;" data-toggle='modal' data-target='.bd-example-modal-sm' id="btnModalLoading">cargando</button>

		      <div class="modal fade bd-example-modal-sm " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		      	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		      		<div class="modal-content">
		      			<button class="btn btn-danger" type="button" disabled>
		      				<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
		      				Cargando...
		      			</button>
		      			<button type="button"  data-dismiss="modal" id="btnCierraSmallModalLoading" style="display: none;">cerrar</button>
		      		</div>
		      	</div>
		      </div>

		      <!-- Links de consulta MODAL -->
		      <div class="modal " id="modalInfoLinks">
		      	<div class="modal-dialog">
		      		<div class="modal-content">

		      			<!-- Modal Header -->
		      			<div class="modal-header">
		      				<h4 class="modal-title">Links de consultas en otras paginas</h4>
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      			</div>

		      			<!-- Modal body -->
		      			<div class="modal-body">
		      				<div class="list-group">
		      					<a href="#" class="list-group-item active" >SSS</a>
		      					<a href="https://www.sssalud.gob.ar/index.php?b_publica=Acceso+P%C3%BAblico&user=GRAL&page=bus650" 
		      					class="list-group-item"
		      					target="_blank"> Regimen general </a>
		      					<a href="#" class="list-group-item"> Monotributo</a>
		      					<a href="#" class="list-group-item"> Opciones regimen general</a>
		      					<a href="#" class="list-group-item"> Opciones monotributo</a>
		      					<a href="#" class="list-group-item active" >Anses</a>
		      					<a href="http://servicioswww.anses.gob.ar/ooss2/" class="list-group-item"> Codem</a>
		      				</div>
		      			</div>

		      			<!-- Modal footer -->
		      			<div class="modal-footer">
		      				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
		      			</div>

		      		</div>
		      	</div>
		      </div>

		      <!--Modal de Rechazo -->
		      <div id="modalDesvincular" class="modal" tabindex="-1" role="dialog">
		      	<div class="modal-dialog modal-lg" role="document">

		      		<!-- Modal content-->
		      		<div class="modal-content">
		      			<div class="modal-header">
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      				<h4 class="modal-title" id='modalDesvincularTitulo'>Desvincular Familiar</h4>
		      			</div>
		      			<div class="modal-body">
		      				<!-- Cosa -->
		      				<input id="modalDesvincularID_titular" type="hidden" />
		      				<input id="modalDesvincularID_afiliado" type="hidden" />
		      				<input id="modalDesvincularCUIL" type="hidden" />
		      				<table id="RechazoModalForm" class="table">
		      					<tr>
		      						<th>
		      							Fecha de Alta
		      						</th>
		      						<td>
		      							<input type="date" id="modalDesvincularFecha">
		      						</td>
		      					</tr>
		      					<tr>
		      						<th>
		      							Tipo beneficiario
		      						</th>
		      						<td>
		      							<select name="modalDesvincularTBT" id="modalDesvincularTBT" class="inp_dtos_afiliacion">
		      								<option value=''>Seleccione</option>
		      							</select>
		      						</td>
		      					</tr>
		      				</table>
		      			</div>
		      			<div class="modal-footer">
		      				<button type="button" class="btn btn-success" data-dismiss="modal" id="modalDesvincularGrabar">Grabar</button>
		      				<button style="display: none;" type="button" class="btn btn-info" data-dismiss="modal" id="modalTransferirTitularGrabar">Grabar</button>
		      				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      			</div>
		      		</div>
		      	</div>
		      </div>
		      <!--Modal de Sincronizacion -->
		      <div id="modalSinc" class="modal" tabindex="-1" role="dialog">
		      	<div class="modal-dialog modal-xl" role="document">
		      		<!-- Modal content-->
		      		<div class="modal-content">
		      			<div class="modal-header">
		      				<h4 class="modal-title" id='modalDesvincularTitulo'>Sincronizar Afiliado</h4>
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      			</div>
		      			<div class="modal-body">
		      				<!-- Cosa -->
		      				<input id="modalSincID_afiliado" type="hidden" />
		      				<table id="modalSinc_table" class="table">
		      					<thead>
		      						<tr>
		      							<th>#</th>
		      							<th>Fuente</th>
		      							<th>Fecha</th>
		      							<th>Dato</th>
		      							<th>Actual</th>
		      							<th>Nuevo</th>
		      							<th>Acciones</th>
		      						</tr>
		      					</thead>
		      					<tbody></tbody>
		      				</table>
		      			</div>
		      			<div class="modal-footer">
		      				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		      			</div>
		      		</div>
		      	</div>
		      </div>

		      <!-- Modal de vincular a otro grupo familiar  -->
		      <div id="modalVincularOtroGrupo" class="modal" tabindex="-1" role="dialog">
		      	<div class="modal-dialog modal-lg" role="document">

		      		<!-- Modal content-->
		      		<div class="modal-content">
		      			<div class="modal-header">
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      				<h4 class="modal-title" id='modalVincularTitulo'>Vincular afiliado a otro grupo familiar</h4>
		      			</div>
		      			<div class="modal-body">
		      				<!-- Cosa -->
		      				<input id="modalVincular_familiar_ID_titular" type="hidden" />
		      				<input id="modalVincular_familiar_ID_afiliado" type="hidden" />

		      				<input type="text" id="inp_busqueda_titular" placeholder="Escriba al menos 10 caracteres"  />

		      				<table id="tabVincularOtroGF" class="table">
		      					<thead>
		      						<tr>
		      							<th>Cuil</th>
		      							<th>Apellido y nombre</th>
		      							<th>Desreguladora</th>
		      						</tr>
		      					</thead>
		      					<tbody></tbody>
		      				</table>
		      			</div>
		      			<div class="modal-footer">
		      				<!--<button type="button" class="btn btn-success" data-dismiss="modal" id="modalDesvincularGrabar">Grabar</button>-->
		      				<button type="button" class="btn btn-default cierra-modal" data-dismiss="modal" >Cerrar</button>
		      			</div>
		      		</div>
		      	</div>
		      </div>

		      <!--Modal de Cambios -->
		      <div id="modalCambios" class="modal" tabindex="-1" role="dialog">
		      	<div class="modal-dialog modal-xl" role="document">

		      		<!-- Modal content-->
		      		<div class="modal-content">
		      			<div class="modal-header">

		      				<h4 class="modal-title" id='modalDesvincularTitulo'>Cambios de Datos</h4>
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      			</div>
		      			<div class="modal-body">
		      				<!-- Cosa -->
		      				<input id="modalCambiosID_afiliado" type="hidden" />
		      				<table id="modalCambios_table" class="table">
		      					<thead>
		      						<tr>
		      							<th>#</th>
		      							<!--<th>Estado</th>-->
		      							<th>Dato</th>
		      							<th>Antiguo</th>
		      							<th>Actual</th>
		      							<th>Fuente</th>
		      							<th>Fecha</th>
		      							<th>Usuario</th>
		      							<th>Observacion</th>
		      							<th></th>
		      						</tr>
		      					</thead>
		      					<tbody></tbody>
		      				</table>
		      			</div>
		      			<div class="modal-footer">
		      				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		      			</div>
		      		</div>
		      	</div>
		      </div>	
		      <!-- Patologias -->
		      <div class="modal " id="modalPatologias">
		      	<div class="modal-dialog">
		      		<div class="modal-content">

		      			<!-- Modal Header -->
		      			<div class="modal-header">
		      				<h4 class="modal-title">Informar una nueva patologia</h4>
		      				<button type="button" class="close" data-dismiss="modal">&times;</button>
		      			</div>

		      			<!-- Modal body -->
		      			<div class="modal-body">
		      				<form method="post" action="" enctype="multipart/form-data">
		      					<input type="hidden" name="MAX_FILE_SIZE" value="2098000000000">
		      					<input type="hidden" id="pat_id_afiliado" name="pat_id_afiliado" >
		      					<input type="hidden" id="pat_id_titular" name="pat_id_titular">

		      					<table class="table" id="tabPatologias">
		      						<tr>
		      							<th>Patologia</th>
		      							<td>
		      								<select name="pat_patologias" id="pat_patologias">
		      									<option value="">Seleccione</option>
		      								</select>
		      							</td>
		      						</tr>
		      						<tr>
		      							<th>Fecha</th>
		      							<td>
		      								<input type="date" name="pat_fecha">
		      							</td>
		      						</tr>
		      						<tr>
		      							<th>Observacion</th>
		      							<td>
		      								<textarea name="pat_observacion" class="form-control"></textarea>
		      							</td>
		      						</tr>
		      						<tr>
		      							<th>Archivo</th>
		      							<td>

		      								<input type="file" name="archivo" size="3000" class="form-control">
		      							</td>
		      						</tr>
		      					</table>
		      					<br>
		      					<input type="submit" name="btnInformaPatologia" style="display: none;">
		      					<input type="submit" name="Subir" value="Subir" class='btn btn-success'>
		      				</form>

		      			</div>

		      			<!-- Modal footer -->
		      			<div class="modal-footer">
		      				<button type="button" class="btn btn-danger" id="cierraModalPatologia" data-dismiss="modal">Cerrar</button>
		      				<button type="button" class="btn btn-warning" >Finalizar</button>
		      			</div>

		      		</div>
		      	</div>
		      </div>

		      <!-- Modal Agregar familiar -->
		      <div class="modal fade bd-example-modal-lg" id="modal_add_fam" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		      	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		      		<div class="modal-content">
		      			<div class="modal-header">
		      				<h5 class="modal-title" id="exampleModalLongTitle">Agregando nuevo familiar</h5>
		      				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      					<span aria-hidden="true">&times;</span>
		      				</button>
		      			</div>
		      			<div class="modal-body">       
		      				<!-- Tab alta familiar -->
		      				<table id="tabAltaFamiliar" class="table" style="margin: auto; font-size: 12px;">
		      					<tr>
		      						<th>DNI </th>
		      						<td>						
		      							<input type="text" name="fm_dni" id="fm_dni" placeholder="DNI de 8 digitos.">						
		      							<span id="fm_dni_span" style="color: red;">* el dni debe tener 8 caracteres</span>
		      						</td>	
		      						<th>Sexo</th>
		      						<td>						
		      							<select name="fm_sexo" id="fm_sexo">
		      								<option value="M">M - Masculino</option>
		      								<option value="F">F - Femenino</option>
		      							</select>
		      						</td>
		      					</tr>
		      					<tr>
		      						<th>Fecha nacimiento</th>
		      						<td>
		      							<input id="fm_fn" name="fm_fn" type="date" class="form-control input-md">
		      						</td>
		      						<th>Gpar</th>
		      						<td>
		      							<input id="fm_gpar" name="fm_gpar" type="text" placeholder="gpar" class="form-control input-md" required="">
		      						</td>	
		      					</tr>
		      					<tr>
		      						<th>CUIL</th>
		      						<td>
		      							<input id="fm_cuil" name="fm_cuil" type="text" placeholder="cuil" class="form-control input-md">
		      							<span>El cuil es calculado con el dni y sexo</span>
		      						</td>	
		      						<th>Parentesco</th>
		      						<td>
		      							<select id="fm_parentesco" name="fm_parentesco" class="form-control">					      
		      							</select>
		      						</td>
		      					</tr>
		      					<tr>
		      						<th>Apellido</th>
		      						<td>
		      							<input id="fm_apellido" name="fm_apellido" type="text" placeholder="apellido" class="form-control input-md" required="">
		      						</td>
		      						<th>Nombre</th>
		      						<td>
		      							<input id="fm_nombre" name="fm_nombre" type="text" placeholder="nombre" class="form-control input-md" required="">
		      						</td>		
		      					</tr>
		      					<tr>
		      						<th>Fecha alta</th>
		      						<td>
		      							<input id="fm_fecha_alta" name="fm_fecha_alta" type="date" value="<?=date('Y-m-d');?>" class="form-control input-md">
		      						</td>
		      						<th>Incapacidad</th>
		      						<td>
		      							<select id="fm_incapacidad" name="fm_incapacidad" class="form-control">
		      								<option value="00">00 - No incapacitado</option>
		      								<option value="01">01 - Incapacidad</option>
		      							</select>
		      						</td>							
		      					</tr>
		      					<tr>
		      						<th>Nacionalidad</th>
		      						<td>
		      							<select id="fm_nacionalidad" name="fm_nacionalidad" class="form-control">					      
		      							</select>
		      						</td>
		      						<th>Estado civil</th>
		      						<td>
		      							<select id="fm_estado_civil" name="fm_estado_civil" class="form-control">					      
		      							</select>
		      						</td>		
		      					</tr>
		      					<tr>
		      						<th>Telefono</th>
		      						<td>
		      							<input id="fm_telefono" name="fm_telefono" type="text" placeholder="telefono" class="form-control input-md">
		      							<span style="color: red;">(*) No requerido</span>
		      						</td>
		      						<th>Email</th>
		      						<td>
		      							<input id="fm_email" name="fm_email" type="email" placeholder="email" class="form-control input-md">
		      							<span style="color: red;">(*) No requerido</span>
		      						</td>		
		      					</tr>
		      					<!-- Domicilio Familiar -->
		      					<tr>
		      						<th>Domicilio</th>
		      						<td>
		      							<select id="select_s_domicilio">
		      								<option value="0">El del titular</option>
		      								<option value="1">Agregar domicilio</option>
		      							</select>
		      							<span>Seleccione cual utilizar</span>
		      							<input type="hidden" name="inp_fm_id_domicilio" id="inp_fm_id_domicilio">
		      						</td>

		      					</tr>
		      					<tr class="tr_fm_nuevo_domicilio">
		      						<th>Provincia</th>
		      						<td>						
		      							<select name="fm_provincia" id="fm_provincia">							
		      							</select>
		      						</td>					
		      					</tr>
		      					<tr class="tr_fm_nuevo_domicilio">
		      						<th>Localidad</th>
		      						<td colspan="3">	
						<!--					
						<datalist name="fm_localidad" id="fm_localidad">							
						</datalist> -->
						<input id="fm_inp_localidad" list="fm_localidad" class="col-sm-12 custom-select custom-select-sm">
						<datalist id="fm_localidad" name="fm_localidad">						    
						</datalist>
						<input type="hidden" name="fm_id_localidad" id="fm_id_localidad">
						<span>Al seleccionar la localidad luego presione TAB y pase al siguiente campo</span>
					</td>
				</tr>
				<tr class="tr_fm_nuevo_domicilio">
					<th>Calle</th>
					<td>
						<input type="text" name="fm_calle" id="fm_calle">
					</td>
					<th>Numero</th>
					<td>
						<input type="text" name="fm_numero" id="fm_numero">
					</td>
				</tr>
				<tr class="tr_fm_nuevo_domicilio">
					<th>Piso</th>
					<td>
						<input type="text" name="fm_piso" id="fm_piso">
					</td>
					<th>Departamento</th>
					<td>
						<input type="text" name="fm_departamento" id="fm_departamento">
					</td>
				</tr>
			</table>
			
			<!-- Fin tabla alta familiar -->
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			<button type="button" class="btn btn-warning" id="btnAltaFamiliar">Confirmar alta</button>
		</div>
	</div>
</div>
</div>


<!-- Modal Documentacion -->
<div class="modal fade" id="modalDocumentacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Documentacion del afiliado</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<!-- Nav tabs -->
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#d_subir">Subir</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#d_ver">Ver documentacion</a>
					</li>			  
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane container fade" id="d_ver">
						<table id="tabVerDocumentacion" class="table " style="font-size: 11px;">
							<thead>
								<tr>
									<th>#</th>
									<th>Ver</th>
									<th>Documentacion</th>
									<th>Archivo</th>
									<th>Fecha</th>
									<th>Observacion</th>
									<th>Usuario</th>
									<th>Fecha carga</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="tab-pane container active" id="d_subir">
						<table id="tabDocumentacion" class="table" style="margin: auto; font-size: 12px;">
							<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
							<input type="hidden" name="doc_id_afiliado" id="doc_id_afiliado">
							<tr>
								<th>Documentacion</th>
								<td>
									<select name="tipo_documentacion" id="tipo_documentacion" class="form-control input-sm" required>
										<option value="">Seleccione</option>
									</select>
									<p style="color: red;">(*) Requerido</p>
								</td>
							</tr>
							<tr>
								<th>Archivo </th>
								<td>						
									<input id="doc_archivo" name="doc_archivo" type="file" class="form-control input-sm">
									<p style="color: red;">(*) Requerido</p>
								</td>	

							</tr>
							<tr>
								<th>Fecha vencimiento</th>
								<td>						
									<input id="doc_fecha" name="doc_fecha" type="date" class="form-control input-sm" >
								</td>	
							</tr>

							<tr>
								<th>Observacion </th>
								<td>
									<textarea id="doc_observacion" name="doc_observacion" class="form-control input-sm"></textarea>
								</td>	

							</tr>
						</table>
					</div>			  
				</div>	        
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="btnSubirDocumentacion">Subir informacion</button>
			</div>
		</div>
	</div>
</div>
<!-- FIN - Modal Documentacion -->

<!-- Modal ModificaEstado -->
	<!--
	<div id="ModificaEstado" class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-lg" role="document">
	
	    
	    <div class="modal-content">
	      <div class="modal-header">
	        
	        <h4 class="modal-title">Modificando estado afiliado</h4>

	        <button type="button" class="close" data-dismiss="modal">&times;</button>

	      </div>
	      <div class="modal-body">
	      	
	      </div>
	      <div class="modal-footer">
	      	
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	      </div>
	    </div>
	
	  </div>
	</div>
-->
<!-- Confirmar Parentesco Cursando (Certificado Estudios - CE) MODAL -->
<div class="modal fade bd-example-modal-sm" id="modalCE" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Formulario de presentacion de certificado de estudio</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<table id="tabAltaFamiliar" class="table" style="margin: auto; font-size: 12px;">
					<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
					<tr>
						<th>Fecha de Baja: </th>
						<td>						
							<input id="ce_baja" name="ce_baja" type="date" class="form-control input-md" value="<?php echo date('Y-03-01',strtotime('+12 months'));?>">
						</td>	
						
					</tr>
					<tr>
						<th>Archivo: </th>
						<td>						
							<input id="ce_archivo" name="ce_archivo" type="file" class="form-control input-md">
						</td>	
						
					</tr>
					<tr>
						<th>Observacion: </th>
						<td>						
							<input id="ce_observacion" name="ce_observacion" type="textarea" class="form-control input-md">
						</td>	
						
					</tr>
				</table>
				<hr>
				<p style="font-size: 12px; color: red; background-color: black; padding: 5px;">
					Una vez ingresada la informacion, haga click en realizado y luego Guardar cambios. 
				</p>
			</div>



			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" data-dismiss="modal" id="btnCE" >Realizado</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>

		</div>
	</div>
</div>

<!-- ModalEstados-->
<!--Modal de Alta -->
<div id="modalEstados" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id='modalEstadosTitulo'>Historico de afiliados y cambio de estado</h4>

				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<!-- Cosa -->
				<input id="modalEstadosHiddenCuil" type="hidden" />
				<input id="modalEstadosHiddenid_afiliado" type="hidden" />

				<div class="d-flex justify-content-center">
					<center>
						<a class="btn btn-info btn-margin btn-sm" id="lbl_nd" data-info="" title='Copiar DNI' style="display: none;">
							<i class="fas fa-copy"></i> DNI
						</a>
						<a class="btn btn-info btn-margin btn-sm" id="lbl_cuil" data-info="" title='Copiar CUIL' style="display: none;">
							<i class="fas fa-copy"></i> CUIL
						</a>

						<a class="btn btn-info btn-margin btn-sm" target='_NEW1' id="modalEstadosRedAportes" href="">
							Aportes/DDJJ
						</a>
						<a class="btn btn-info btn-margin btn-sm" target='_NEW3' id="modalEstadosRedCodem" onclick="" href='http://servicioswww.anses.gob.ar/ooss2/'>
							CODEM ANSES
						</a>
						<a class="btn btn-info btn-margin btn-sm" target='_NEW3' id="modalEstadosRedCodem" onclick="" href='https://servicioswww.anses.gob.ar/censite/index.aspx'>
							Negativa ANSES
						</a>

						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?b_publica=Acceso+P%C3%BAblico&user=GRAL&page=bus650'>
							SSS RG
						</a>		        			

						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?cat=consultas&page=busopc'>
							SSS opciones RG
						</a>

						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?page=busmon'>
							SSS MT
						</a>

						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?cat=consultas&page=busopcmono'>
							SSS opciones MT
						</a>

						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?cat=consultas&page=mono_pagos'>
							SSS pagos MT
						</a>
						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.sssalud.gob.ar/index.php?cat=consultas&page=mono_pagos_sd'>
							SSS pagos ServDom
						</a>
						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://www.anses.gob.ar/consultas/fecha-de-cobro'>
							Desem | Fecha y cobro
						</a>
						<a class="btn btn-info btn-margin btn-sm modalEstadosRedSSSRG" target='_NEW2' href='https://efectores.mds.gob.ar/webefectores/verestados'>
							Estado monotributo
						</a>
						<a class="btn btn-secondary btn-margin btn-sm text-light" id="modalInfoAFIP">
							[TEST] Info AFIP
						</a>
					</center>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-5" id='modalEstadosForm'>
						<!--modalEstadosFormulario -->
						<form class="form-horizontal" action="/action_page.php">
							<h5>Informar nuevo movimiento</h5>
							<div class="form-group">
								<label class="control-label" for="email">Tipo movimiento</label>
								<div class="col-sm-10">
									<select class="form-control" name="me_tipo_movimiento" id="me_tipo_movimiento">
										<option value="">Seleccione</option>
										<option value="alta">Alta</option>
										<option value="baja">Baja</option>
										<option value="OBSV">Info</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2" for="pwd">Motivo</label>
								<div class="col-sm-10">          
									<select class="form-control " name="me_motivo" id="me_motivo">
										<option value="">Seleccione</option>
									</select>
								</div>
							</div>
							<div class="form-group">        
								<label class="control-label col-sm-2" for="pwd">Fecha</label>
								<div class="col-sm-10">
									<div class="checkbox ">
										<input type="date" class="input-sm" id="me_fecha" value="<?=date("Y-m-d");?>" />
									</div>
								</div>
							</div>
							<div class="form-group">        
								<label class="control-label " for="pwd">Observacion</label>	
								<div class="col-sm-10">
									<textarea id="me_observacion" class="form-control"></textarea>
								</div>
							</div>
							<div class="form-group">
								<button type="button" class="btn btn-success btn-sm" data-dismiss="modal" id="btnGrabarMovimiento">Grabar</button>
							</div>

						</form>  
					</div>
					<div class="col-md-7" id="modalEstadosHistoricoDiv">
						<center>			        			
							<i class='fas fa-spinner fa-spin fa-4x'></i>
						</center>
					</div>
				</div>


			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

			</div>
		</div>
	</div>
</div>
<!-- FIN de Modal Estados-->
<!-- ModalCredencial-->
<div id="modalCredencial" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id='modalCredencialTitulo'>Historico de afiliados y cambio de estado</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<!-- Cosa -->
				<input id="modalCredencialHiddenCuil" type="hidden" />
				<input id="modalCredencialHiddenid_afiliado" type="hidden" />
				<a class="btn btn-info btn-margin btn-sm" id="imprimir_credencial">
					Imprimir nueva credencial
				</a>
				<div class="row">
					<div class="col-md-12" id="modalCredencialHistoricoDiv">
						<center>			        			
							<i class='fas fa-spinner fa-spin fa-4x'></i>
						</center>
					</div>
				</div>


			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

			</div>
		</div>
	</div>
</div>
<!-- FIN de Modal Credencial-->
<!-- Modal ListFctPrestacion -->
<div id="modalCronologia" class="modal fade " role="dialog">
	<div class="modal-dialog " >

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cronologia </h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id="divListCronologia">

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>

	</div>
</div>
<!-- FIN de Modales-->

<!-- BOOTSTRAP, Popper.js, and jQuery -->
<script src="js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>>

<script type="text/javascript" src="js/notify.min.js"></script>
<script src="js/headroom.min.js"></script>

<script>

		//Variables de perfil y redirecciones
		var tipo_perfil = '<?php echo $_SESSION['perfil']; ?>';
		var ajax_url = 'ajax.php';
		var ajax_selects_url = 'ajax_selects.php';	

		var var_vgp,var_tip,var_cct,var_dom,var_dtit;
		var id_af_consultado = $("#id_af_consultado").val();

		const DOMINIO = "<?echo DOMINIO;?>";
		const N_BASE = "<?echo N_BASE;?>";
		const INST_DOCUMENTACION_URL = "<?echo INST_DOCUMENTACION_URL;?>";
		const INST_VGP_NRO_SIND = "<?echo INST_VGP_NRO_SIND;?>";
		const INST_VGP_PATOLOGIA = "<?echo INST_VGP_PATOLOGIA;?>";

		let tit_tbt;
		let tit_empresa;

		$(function(){

			console.log('INST_VGP_NRO_SIND '+INST_VGP_NRO_SIND);
			//console.log('T perfil: '+tipo_perfil);
			$('#me_motivo').addClass("select2");	
			$('.select2').select2();

			if(tipo_perfil != 'admin' && tipo_perfil != 'upd_dto_personales'){
				$('#btnNuevoFamiliar').hide();
			}

			$('#divBody').scrollspy({target: ".navbar", offset: 50});  
			$('#divBody').hide(); //Div de datos personales (a mejorar esto)
			$("#btnGuardarCambios").hide();
			$('select').addClass('form-control');
			$('input[type=text]').addClass('form-control');
			$('input[type=date]').addClass('form-control');
			$('.tr_fm_nuevo_domicilio input').attr('disabled','disabled');
			$('.tr_fm_nuevo_domicilio select').attr('disabled','disabled');
			$("#fm_dni_span").attr("hidden",true);
			$('#btnModalLoading').click();
			$("#tabGrupoFamiliar tbody").html('<div class="spinner-grow text-warning" style="margin: 3px;" ></div>');
			VerificarPermisos();
			LoadGP(true);

			var cod_prestacion = $('#t_id_localidad').select2({
	      //width: 'resolve',
	      ajax: {
	      	url: "ajax.php",
	      	dataType: 'json',
	      	delay: 250,
	      	data: function (params) {
	      		return {
	            keyword: params.term, // search term
	            page: params.page,
	            parametro: 'buscar_localidad'
	          };
	        },
	        processResults: function (data, params) {
	        	params.page = params.page || 1;

	        	return {
	        		results: data.results,
	        		pagination: {
	        			more: (params.page * 30) < data.total_count
	        		}
	        	};
	        },
	        cache: true
	      },
	      placeholder: 'Busque una localidad',
	      minimumInputLength: 3,
	      templateResult: formatRepo,
	      templateSelection: formatRepoSelection
	    });

			$('#t_id_localidad').on('select2:open', function() {//Manejo de #DNI

				$(this).data('select2').dropdown.$search.on('keydown', function(e) {

					if (e.keyCode === 13) { // Check if Enter key is pressed
						e.stopPropagation();
						e.preventDefault(); // Prevent default Select2 behavior
						var searchBoxContent = $(this).val();

						var $results = select2.data('select2').results.$results; 
						var $options = $results.children('.select2-results__option'); 
						var $filteredOptions = $options.filter(function() {
							console.log("Paso por aquii : "+$filteredOptions.length);
							return $(this).text().trim() !== 'No results found';
						});

						if ($filteredOptions.length !== 0) {
							console.log(`Enter Key was pressed during #t_id_localidad use with ${$results.children('.select2-results__option').length} result/s`);  
						}
					}
				});
			});

			$('#parentesco').change(function() {//En caso de parentesco "Hijo cursando..." abre el modal para cargar la documentacion de este
				var opval = $(this).val(); 
				if(opval=="4"){ 
					$('#modalDocumentacion').modal("show");
					$("#tipo_documentacion").val(1);
					var id_afiliado = $('#id_afiliado').val();
					$('#doc_id_afiliado').val(id_afiliado);
				}
			})
			$('#lbl_cuil').on('click',function(){//Copia el CUIL al portapapeles (Actualmente sin uso)
				var valor = $('#lbl_cuil').data('info');
				var str = valor.toString();
				var res = str.substr(0,2)+'-'+str.substr(2,8)+'-'+str.substr(10,1);
				//console.log(res);
				copytext(res);
			})
			$('#lbl_nd').on('click',function(){//Copia el DNI al portapapeles (Actualmente sin uso)
				var valor = $('#lbl_nd').data('info');
				copytext(valor);
			})
			$(document).on('shown.bs.modal','#modalCambios',function(e){
				var id_afiliado = e.relatedTarget.dataset.id_afiliado;
				$('#modalCambios_table tbody').html(" ");
				$('#modalCambios_table tbody').append("<tr><td colspan=7><center><i class='fas fa-spinner fa-spin fa-3x'></i></center></td></tr>");
				$.getJSON(ajax_url, 
					{parametro: 'traer_sinc_logs_no_pend',id_afiliado:id_afiliado}, 
					function(data) {
						$('#modalCambios_table tbody').html(" ");
						//console.table(data);
						var j=1;
						var botonRevertir;
						for(var i=0; i<=data.length-1 ;i++){

							switch(data[i]['estado']){
								case 'Aceptado':
								tr_estado = "class''";
								botonRevertir="<td>"
								+"<button class='btn btn-danger CambioRevertir' data-id_log='"+data[i]['id']+"' data-d_actual='"+data[i]['d_actual']+"' data-d_nuevo='"+data[i]['d_nuevo']+"'>Revertir</button>"
								+"</td>";
								break
								case 'Revertido':
								tr_estado = " class='tr_estado_revertido' ";
								botonRevertir="<td></td>";
								break
							}

							$('#modalCambios_table tbody').append(
								"<tr "+tr_estado+">"
								+"<td>"
								+j
								+"</td>"
								+"<td>"
								+data[i]['tipo_dato']
								+"</td>"
								+"<td>"
								+data[i]['dato_actual']
								+"</td>"
								+"<td>"
								+data[i]['dato_nuevo']
								+"</td>"
								+"<td>"
								+data[i]['tipo_lote']
								+"</td>"
								+"<td>"
								+data[i]['fechador']
								+"</td>"
								+"<td>"
								+data[i]['usuario']
								+"</td>"
								+"<td>"
								+data[i]['observacion']
								+"</td>"
								+botonRevertir
								+"</tr>"
								);
							j++;
						}
					});
			})
			$(document).on('shown.bs.modal', '#modalEstados', function (e){//Rellena M. Estados al abrirse, vacia los datos anteriores, prepara redirecciones correspondientes para el afiliado
				if(tipo_perfil!=='admin'){
					$("#modalEstadosForm").removeClass();
					$("#modalEstadosForm").hide();
					$('#btnGrabarMovimiento').prop('disabled',true);
					$("#btnGrabarMovimiento").attr('title', 'Usted no cuenta con permiso para grabar cambios');
					$("#modalEstadosHistoricoDiv").removeClass();
					$("#modalEstadosHistoricoDiv").addClass('col');
					$('#modalEstadosHistoricoDiv').removeAttr('style');
				}
				$('#me_observacion').val('');
				$('#modalEstadosHistoricoDiv').html("");
				$('#modalEstadosHistoricoDiv').append("<center><i class='fas fa-spinner fa-spin fa-4x'></i></center>");

	    			//Variables iniciales
	    			var cuil = e.relatedTarget.dataset.cuil;
	    			var nd = e.relatedTarget.dataset.nd;
	    			var id_afiliado = e.relatedTarget.dataset.id_afiliado;

	    			$('#lbl_nd').attr('data-info',nd);
	    			$('#lbl_cuil').attr('data-info',cuil);
	    			//console.log(nd+' cuil: '+cuil)

	    			$('#modalEstadosHiddenid_afiliado').val(id_afiliado);

	    			//var redaportes = 'http://'+DOMINIO+'/consultas/index.php?p_cuil='+cuil;

	    			var redaportes = '../consultas/index.php?p_cuil='+cuil;
	    			$('#modalEstadosRedAportes').attr('href',redaportes);

		    		//var str = cuil.toString();
		    		var str = cuil;
		    		console.log(str)
		    		var res = str.substr(0,2)+'-'+str.substr(2,8)+'-'+str.substr(10,1);
		    		var redicCUIL = 'copytext("'+res+'")';
		    		$('.modalEstadosRedSSSRG').attr('onClick','');
		    		$('.modalEstadosRedSSSRG').attr('onClick',redicCUIL);

		    		var redicND = 'copytext('+nd+')';
		    		$('#modalEstadosRedCodem').attr('onClick','');
		    		$('#modalEstadosRedCodem').attr('onClick',redicND);

		    		$('#modalInfoAFIP').attr('onClick','')
		    		$('#modalInfoAFIP').attr('onClick',`BuscarInfoAfip(${cuil})`);

		    		$.getJSON(ajax_url, 
					//{'parametro': 'historico_stored','cuil': cuil}, 
					{'parametro': 'historico_stored','cuil': cuil}, 
					function(data) {
							//alert('Hi');
							console.table(data);

							$('#modalEstadosHistoricoDiv').html("");

							var fuente = '';

							$('#modalEstadosHistoricoDiv').append("<h5>Movimientos historicos</h5>"
								+"<table class='table table-stripped' id='TablaMovimientos'>"
								+"<thead>"
								+"<tr>"
								+"<th>"
								+"</th>"
								+"<th class='w-10'>"
								+"Fuente"
								+"</th>"
								+"<th class='w-60'>"
								+"Descripcion"
								+"</th>"
								+"<th class='w-15'>"
								+"Tipo"
								+"</th>"
								+"<th class='w-25'>"
								+"Fecha movimiento"
								+"</th>"
								+"<th class='w-25'>"
								+"Fecha actualizacion"
								+"</th>"
								+"<th class='w-25'>"
								+"Usuario"
								+"</th>"
								+"<th class='w-20'>"
								+"Observacion"
								+"</th>"
								+"</tr>"
								+"</thead>"
								+"<tbody></tbody>"
								+"</table>");
							let texto_descripcion = texto_observacion = "";

							if(data['estado']){
								$('#TablaMovimientos').append(`<tr><th colspan=7 align='center'>${data['estado']}</th></tr>`);
							}
							for(var i=0; i<=data.length-1 ;i++){

								let {cuil_titular,cuil,movimiento_de,movimiento,tipo,fec_mov,fecha_movimiento,fechador,usuario,observacion} = data[i];

								btn_opc = item_agr_nov = "";

								if(cuil_titular==cuil){

									fuente = 'Propio' ;

								}else{

									fuente = 'Familiar'; 

								}

								if(tipo==="OBSV"){
									texto_descripcion = observacion;
									texto_observacion = "";
								}else{
									texto_descripcion = movimiento;
									texto_observacion = observacion;
								}

								if(tipo.charAt(0) == "A" || tipo.charAt(0) == "B"){
									item_agr_nov = "<a class='dropdown-item nov_agregar' style='cursor:pointer;' "
									+"data-cuil='"+cuil+"' "
									+"data-tipo_mov='"+tipo.charAt(0)+"' "
									+"data-fecha_movimiento='"+fecha_movimiento+"' "
									+">"
									+"Informar en NOV. Carpeta Vigente"
									+"</a>";		

									btn_opc = `
									<div class='btn-group btn-group-default'>                
									<button style='margin-left: 20%; margin-right: auto;'  data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='height: 34px;' type='button'>
									<i class='fa fa-ellipsis-v' aria-hidden='true'></i>
									</button>
									<ul class='dropdown-menu'>
									${item_agr_nov}														                     		 
									</ul>
									</div>
									`;							
								}


								$('#TablaMovimientos').append("<tr>"
									+`<td>
									${btn_opc}
									</td>`
									+"<td>"
									+movimiento_de
									+"</td>"
									+"<td>"
									+texto_descripcion
									+"</td>"
									+"<td>"
									+tipo
									+"</td>"
									+"<td>"
									+fec_mov
									+"</td>"
									+"<td>"
									+fechador
									+"</td>"
									+"<td>"
									+usuario
									+"</td>"
									+"<td>"
									+texto_observacion
									+"</td>"
									+"</tr>");

							}
						});


		    	})

			$(document).on('shown.bs.modal', '#modalSinc', function (e){//Rellena M. Sincronizacion con los datos correspondientes al afiliado y vacia lo anterior 
				var id_afiliado = e.relatedTarget.dataset.id_afiliado;
				$('#modalSinc_table tbody').html(" ");
				$('#modalSinc_table tbody').append("<tr><td colspan=7><center><i class='fas fa-spinner fa-spin fa-3x'></i></center></td></tr>");
				$.getJSON(ajax_url, 
					{parametro: 'traer_sinc_logs',id_afiliado:id_afiliado}, 
					function(data) {
						$('#modalSinc_table tbody').html(" ");
						console.table(data);
						var j=1;
						for(var i=0; i<=data.length-1 ;i++){
							$('#modalSinc_table tbody').append(
								"<tr>"
								+"<td>"
								+j
								+"</td>"
								+"<td>"
								+data[i]['tipo_lote']
								+"</td>"
								+"<td>"
								+data[i]['fechador']
								+"</td>"
								+"<td>"
								+data[i]['tipo_dato']
								+"</td>"
								+"<td>"
								+data[i]['dato_actual']
								+"</td>"
								+"<td>"
								+data[i]['dato_nuevo']
								+"</td>"
								+"<td>"
								+"<button class='btn btn-success SincAceptar' data-id_log='"+data[i]['id']+"' data-d_actual='"+data[i]['d_actual']+"' data-d_nuevo='"+data[i]['d_nuevo']+"'>Aceptar</button>"
								+"<button class='btn btn-danger SincRechazar' data-id_log='"+data[i]['id']+"'>Rechazar</button>"
								+"</td>"
								+"</tr>"
								);
							j++;
						}
					});
			})

			$(document).on('click', '.CambioRevertir', function(e){//Ejecuta el cambio en padron que esta pendiente en M. Sincronizacion y altera su estado a 'aceptado'
				var id = $(this).data('id_log');
				var conf = confirm('¿Seguro?');
				var datos = {
					"parametro": 'revertir_log',
					"id":id,
					"d_actual": $(this).data('d_actual'),
					"d_nuevo": $(this).data('d_nuevo')
				};
				console.table(datos);
				if(id && conf){
					$.ajax({
						url: ajax_url,
						type: 'get',
						dataType: 'text',
						data: datos,
					})
					.done(function(data) {
						console.log(data);
						if(data == 'ok'){

							alert('Cambio Guardado.');
							LoadGP(false);
							$('#modalCambios').modal('hide');
						}
					});
				}
			})

			$(document).on('click', '.SincAceptar', function(e){//Ejecuta el cambio en padron que esta pendiente en M. Sincronizacion y altera su estado a 'aceptado'
				var id = $(this).data('id_log');
				var conf = confirm('¿Seguro?');
				var datos = {
					"parametro": 'sinc_aceptar',
					"id":id,
					"d_actual": $(this).data('d_actual'),
					"d_nuevo": $(this).data('d_nuevo')
				};
				if(id && conf){
					$.ajax({
						url: ajax_url,
						type: 'POST',
						dataType: 'text',
						data: datos,
					})
					.done(function(data) {
						if(data == 'ok'){
							alert('Cambio Guardado');
							LoadGP(false);
							$('#modalSinc').modal('hide');
							$('#BodyCerrar').click();
						}
					});
				}
			})

			$(document).on('click', '.SincRechazar', function(e){//No ejecuta el cambio pendiente, pero le cambia el estado a 'rechazado'
				var id = $(this).data('id_log');
				var conf = confirm('¿Seguro?');
				if(id && conf){
					$.ajax({
						url: ajax_url,
						type: 'POST',
						dataType: 'text',
						data: {parametro: 'sinc_rechazar',id:id},
					})
					.done(function(data) {
						if(data == 'ok'){
							alert('Cambio Guardado');
							LoadGP(false);
							$('#modalSinc').modal('hide');
						}
					});
				}
			})
			//me_tipo_movimiento			
			$('#me_tipo_movimiento').on('change',function(){

				$("#me_motivo").empty();
				var tipo_movimiento = $(this).val();
				//console.log(provincia);

				$.getJSON(ajax_selects_url,
					{ parametro: "tipo_movimiento", me_movimiento: tipo_movimiento },						       				
					function(datos){ 

						$.each(datos, function (key, item) {
							$("#me_motivo").append("<option value='"+item.id+"' >"+item.descripcion+"</option>");
						});

							}//fin function data

				);//fin getjson
			})
			
			$('#btnGrabarMovimiento').on('click',function(){
				
				var id_afiliado = $('#modalEstadosHiddenid_afiliado').val();
				//console.log(id_afiliado); return false;
				var id_evento = $('#me_motivo').val();
				var fecha = $('#me_fecha').val();
				var observacion = $('#me_observacion').val();

				var cuil =$('#cuil').val();
				
				console.log(id_afiliado+" "+id_evento+" "+fecha+" "+observacion);
				
				if(id_evento!="" && fecha!="" &&id_afiliado!=""){
					
					var datos = {
						"parametro": "grabar_movimiento_afiliado",
						"id_afiliado": id_afiliado,
						"id_evento": id_evento,
						"fecha": fecha,						
						"observacion": observacion
					};
					
					$.ajax({

						url: ajax_url,
						type: 'get',
						datatype:'text',
						data: datos,
						success: function(data){
							console.log(data);
							$("#tabHistorico tbody").html('<div class="spinner-border"></div>');
							if(data==='ok'){
								alert('Cambio Guardado.');
								//VerGrupoFamiliar(id_titular);
								var id_titular = $('#id_titular').val() ;
								window.location.href='index.php?id_titular='+id_titular ;
								
							}	
							else{
								alert('Hubo un error, '+data);
							}	

							/*moco*/
							$.getJSON(ajax_url,
								{parametro:"historico_stored", cuil: cuil},
								function(data){

									console.table(data);

									$('#modalEstadosHistoricoDiv').html("");

									var fuente = '';

									$('#modalEstadosHistoricoDiv').append("<table class='table table-stripped' id='TablaMovimientos'>"
										+"<thead>"
										+"<tr>"
										+"<th class='w-10'>"
										+"Fuente"
										+"</th>"
										+"<th class='w-60'>"
										+"Descripcion"
										+"</th>"
										+"<th class='w-15'>"
										+"Tipo"
										+"</th>"
										+"<th class='w-25'>"
										+"Fecha"
										+"</th>"
										+"</tr>"
										+"</thead>"
										+"<tbody></tbody>"
										+"</table>");

									for(var i=0; i<=data.length-1 ;i++){

										if(data[i]['cuil_titular']==data[i]['cuil']){

											fuente = 'Propio' ;

										}else{

											fuente = 'Familiar'; 

										}

										$('#TablaMovimientos').append("<tr>"
											+"<td>"
											+data[i]['movimiento_de']
											+"</td>"
											+"<td>"
											+data[i]['movimiento']
											+"</td>"
											+"<td>"
											+data[i]['tipo']
											+"</td>"
											+"<td>"
											+data[i]['fecha_movimiento']
											+"</td>"
											+"</tr>");

									}

								}

								);

						}
					});
					
				}
				else{
					alert('Complete los campos requeridos');
					return false;
				}
				
				
			})

			//Ver grupo familiar ( con id_titular )			
			setTimeout(function(){ 

				var id_titular = $('#id_titular').val();
				var id_af_consultado = $('#id_af_consultado').val();
				//console.log(id_titular);
				
				//VerGrupoFamiliar(id_titular,id_af_consultado);

				//divDesempleo
				$.getJSON(ajax_url,
					{ parametro: "es_desempleado", id_titular: id_titular },						       				
					function(data){ 

						if(data[0]['estado']=="si"){
							$('#divPresentaDesempleo').css('display','block');
							$('#divPresentaDesempleo').html("<b>DESEMPLEO desde "+data[0]['per_min']+" al "+data[0]['per_max']+"</b>");
						}


							}//fin function data

				);//fin getjson
				$.ajax({
					url: ajax_url,
					type: 'GET',
					dataType: 'json',
					data: { parametro: "es_jubilado_ahora", id_titular: id_titular },
				})
				.done(function(data) {

					if(data[0]['actual']=="si"){
						$('#ult_periodo').css('display','block');
						$('#ult_periodo').html("<b>El titular se encuentra en el ultimo lote de jubilados</b><br><p>El ultimo periodo es: <b>"+data[0]['periodo']+"</b></p>");
					}
					if(data[0]['actual']=="no"){
						switch (data[0]['causa']) {
							case 'no_es':
						   // console.log('No hago nada');
						   break;
						   case 'lo_fue':
						   $('#ult_periodo').css('display','block');
						   $('#ult_periodo').html("<p>El titular <b>NO se</b> encuentra en el ultimo lote de jubilados</p><br><p>El ultimo periodo es: <b>"+data[0]['periodo']+"</b></p>");
						   break;
						   case 'nunca_estuvo':
						   $('#ult_periodo').css('display','block');
						   $('#ult_periodo').html("<p>El titular <b>NO</b> encuentra en ningun lote de jubilados</p>");
						   break;
						 }
						}
					});
				
				

				//Domicilio Titular
				$.getJSON(ajax_url,
					{ parametro: "domicilio_titular", id_titular: id_titular },						       				
					function(datos){ 

						$('#inp_fm_id_domicilio').val(datos[0]['id_domicilio_titular']);

							}//fin function data

				);//fin getjson

				//modalDesvincularTBT
				$.getJSON(ajax_selects_url,
					{ parametro: 'tbt' },						       				
					function(data){ 
						console.log(data);
								//alert('Ok');
								
								$.each(data, function (key, item) {

									$("#modalDesvincularTBT").append("<option value="+item.id+">"+item.beneficiario+"</option>");
								});
							}//Fin function data
				);//Fin getjson
			}, 500);
			/***********************************************************************************************************************************************************************************/
			//Mostrar datos de afiliados 
			$('#tabGrupoFamiliar').on('click','.verAfiliado',function(){

				if(tipo_perfil =='consulta' || tipo_perfil =='consulta_padron' || tipo_perfil =='consulta_externo'){
					$('#tabDatosPersonales tr td').find("input,button,textarea,select").attr("disabled", "disabled");
					$('#tabDomicilio tr td').find("input,button,textarea,select").attr("disabled", "disabled");
					$('#tabAfiliacion tr td').find("input,button,textarea,select").attr("disabled","disabled");
					$('#BodyFooter').hide();
				}

				$('#divBody').show();

				$("#tabHistorico tbody").html('<div class="spinner-border"></div>');
				
				$('#btnModalLoading').click();

				$('input[name=formDatosPersonales]').val(0);
				$('input[name=formDomicilio]').val(0);
				$('input[name=formDatosAfiliacion]').val(0);

				//obtenemos la posición en la que se encuentra el botón
				var posicion_boton = $('#nav-personales').offset().top;

				//hacemos scroll hasta el botón
				$("html, body").animate({scrollTop:posicion_boton+"px"});

				var id_afiliado = $(this).data('id_afiliado'); 
				$('#id_afiliado').val("");
				$('#id_afiliado').val(id_afiliado);

				var s_nom_afil = $(this).data('s_nom_afil');
				$('#s_nom_afil').html('<div class="spinner-border"></div>');
				//console.log(s_nom_afil)

				var tipo_afil = $(this).data('t_paren');
				var cuil = $(this).data('cuil');


				//Trae datos personales
				$.getJSON(ajax_url,
					{ parametro: "datos_personales", id_afiliado: id_afiliado },						       				
					function(data){ 

						$('#s_nom_afil').html('');
						$('#s_nom_afil').html(s_nom_afil);
						$('#btnCierraSmallModalLoading').click();

						if(data[0]['estado']=="ERROR - NO se encontraron resultados"){
							alert('El afiliado no existe');
						}
						else{

							$('input[name=nd]').val(data[0]['nd']);
							$('select[name=sexo]').val(data[0]['sexo']);
							$('input[name=cuil]').val(data[0]['cuil']);
							$('select[name=desreguladora]').val(data[0]['id_desreguladora']);
							$('input[name=nben]').val(data[0]['nben']);
							$('input[name=gpar]').val(data[0]['gpar']);
							$('input[name=apellido]').val(data[0]['apellido']);
							$('input[name=nombre]').val(data[0]['nombre']);
							$('input[name=estado]').val(data[0]['estado_afiliado']);
							$('select[name=parentesco]').val(data[0]['id_parentesco']);
							$('input[name=fn]').val(data[0]['fn']);
							$('select[name=estado_civil]').val(data[0]['id_estado_civil']);
							$('select[name=incapacidad]').val(data[0]['incapacidad']);
							$('input[name=telefono]').val(data[0]['telefono']);
							$('input[name=email]').val(data[0]['email']);

									//Salvo los valores para motivo modificacion de campos  
									salva_da_dtos_personales(data);

								}


							}//fin function data

				);//fin getjson Trae datos personales


				//Trae domicilio
				$.getJSON(ajax_url,
					{ parametro: "domicilio", id_afiliado: id_afiliado },						       				
					function(data){ 

						if(data[0]['estado']=="ERROR - NO se encontraron resultados"){
									//alert('El afiliado no existe');
									console.log('No trajo el domicilio');
								}else{
									$('input[name=id_domicilio_afiliado]').val(data[0]['id_domicilio']);
									//$('input[name=localidad]').val(data[0]['localidad']);
									//$("#t_localidad").val(data[0]['localidad']).attr('selected','selected');
									$("#t_inp_localidad").val(data[0]['id_localidad']);
									$('input[name=calle]').val(data[0]['calle']);
									$('input[name=numero]').val(data[0]['numero']);
									$('input[name=piso]').val(data[0]['piso']);
									$('input[name=depto]').val(data[0]['depto']);

									recarga_localidad();
									salva_da_domicilio(data);
									$("t_inp_localidad").blur();
									
								}


							}//fin function data

				);//fin getjson Trae domicilio

				//Trae Datos afiliacion
				$.getJSON(ajax_url,
					{ parametro: "datos_afiliacion", id_titular: $('#id_titular').val() },						       				
					function(data){ 

								//console.log(data);

								if(data[0]['estado']=="ERROR - NO se encontraron resultados"){
									//alert('El afiliado no existe');
									console.log('No trajo los datos de afiliacion');
								}
								else{
									
									console.log(data);
									$('select[name=tbt]').val(data[0]['id_tbt']);
									$('select[name=revista]').val(data[0]['id_revista']);
									$('select[name=seccional]').val(data[0]['id_filial']).change();
									$('#plan_medico').val(data[0]['id_plan_medico']);

									//Salvo los valores para motivo modificacion de campos  
									salva_da_dtos_afi(data);

									
								}


							}//fin function data afiliacion

				);//fin getjson Trae domicilio

				//Trae fecha de ALTA y BAJA
				$.getJSON(ajax_url,
					{ parametro: "fechas_alt_baj", id_afiliado: id_afiliado },						       				
					function(data){ 

								//console.log(data);
								var fec_baja = "";

								if(data[0]['estado']=="ERROR - NO se encontraron resultados"){
									//alert('El afiliado no existe');
									console.log('No trajo los datos de afiliacion');
								}
								else{
									
									
									$('input[name=fecha_alta]').val(data[0]['ult_fecha_alta']);
									$('input[name=fecha_baja]').val(data[0]['ult_fecha_baja']);
									
									//Boton para modificar estado alta-baja
									if(tipo_afil!="Titular"){
										tipo_afil="Familiar";
									}

									console.log(data[0]['ult_fecha_baja']);
									if(data[0]['ult_fecha_baja']==null){
										fec_baja = "0000-00-00";
									}
									else{
										fec_baja = data[0]['ult_fecha_baja'];
									}


									$('.btn-modificaFechaAB').attr('data-tipo_afil',tipo_afil);

									$('.btn-modificaFechaAB').attr('data-fec_alta',data[0]['ult_fecha_alta']);
									$('.btn-modificaFechaAB').attr('data-fec_baja',fec_baja);

									$('.btn-modificaFechaAB').attr('data-fec_alta',data[0]['ult_fecha_alta']);
									$('.btn-modificaFechaAB').attr('data-fec_baja',fec_baja);

									$('.btn-modificaFechaAB').attr('data-id_afiliado',id_afiliado);
									$('.btn-modificaFechaAB').attr('data-cuil',cuil);


								}

							}//fin function data

				);//fin getjson Trae fecha de ALTA y BAJA

				//Historico
				/*
				$.getJSON(ajax_url,
							{ parametro: "historico", id_afiliado: id_afiliado },						       				
							function(data){ 
								
								//console.log(data)
								$("#tabHistorico tbody").html(""); 

								for(var i=0; i<=data.length-1 ;i++){
									
									$("#tabHistorico tbody").append("<tr>"																
																		+"<td>"+(i+1)+"</td>"
																		+"<td>"+data[i]['descripcion']+"</td>"
																		+"<td>"+data[i]['fechador']+"</td>"
																		+"<td>"+data[i]['motivo_modificacion']+"</td>"
																		+"<td>"+data[i]['nombre_campo']+"</td>"
																		+"<td>"+data[i]['valor_anterior']+"</td>"
																		+"<td>"+data[i]['usuario']+"</td>"				      				
																	+"</tr>") ;		
								}	
							}//fin function data

				);//fin getjson Historico
				*/
				$.getJSON(ajax_url,
					{parametro:"historico_stored", cuil: cuil},
					function(data){
						$("#tabHistorico tbody").html("");

						for(var i=0;i<=data.length-1;i++){
							$("#tabHistorico tbody").append("<tr>"
								+"<td>"+(i+1)+"</td>"
								+"<td>"+data[i]['movimiento_de']+"</td>"
								+"<td>"+data[i]['movimiento']+"</td>"
								+"<td>"+data[i]['tipo']+"</td>"
								+"<td>"+data[i]['fecha_movimiento']+"</td>")
						}

					}

					);

			})

$('#tabGrupoFamiliar').on('click','.vincular-otro-grupo-familiar',function(){

	var id_titular = $("#id_titular").val();
	var id_afiliado = $(this).data('id_afiliado');
	console.log(id_titular);

	$('#modalDesvincularTitulo').html('');
	$('#modalDesvincularTitulo').append('Desvincular Familiar '+cuil);
	$('#modalVincular_familiar_ID_titular').val(id_titular);
	$('#modalVincular_familiar_ID_afiliado').val(id_afiliado);

				// $("#tabVincularOtroGF").dataTable({			    	
				// 							"bPaginate": true,
				// 							"iDisplayLength": 100,
				// 							"bLengthChange": false,
				// 							"bFilter": true,
				// 							"bSort": true,
				// 							"bInfo": false,
				// 							"aaSorting": [[ 1, "desc" ]],
				// 							"bAutoWidth": false,
				// 							"language": {				    
				// 							    "search": "Buscar",
				// 							    "paginate": {
				// 								      "previous": "Anterior",
				// 								      "next": "Proximo"
				// 								}
				// 						    }
				// 					});

			})

$("#inp_busqueda_titular").on('keypress keyup keydown',function(){

	var inp_dato = $(this).val();
	var id_titular = $("#id_titular").val();

	console.log(id_titular);

	if(inp_dato.length>=10){

		console.log(inp_dato);

		$("#tabVincularOtroGF tbody").html("<tr><td><h4>Cargando...</h4></td></tr>");

					//tabVincularOtroGF
					$.getJSON('ajax.php',
						{ parametro: "otros_titulares", id_titular: id_titular, inp_dato: inp_dato },						       				
						function(data){

							console.log('hola') ;

							$("#tabVincularOtroGF tbody").html("");

							for(var i=0; i<=data.length-1 ;i++){

								$("#tabVincularOtroGF tbody").append("<tr class='vincular-ogf' data-id_titular_nuevo="+data[i]['id_afiliado']+">"																
									+"<td>"+data[i]['cuil']+"</td>"
									+"<td>"+data[i]['ayn']+"</td>"
									+"<td>"+data[i]['desreguladora']+"</td>"

									+"</tr>") ;	


							}	



								}//fin function data

					);//fin getjson

					
				}

			})

$("#tabVincularOtroGF tbody").on('click','.vincular-ogf',function(){

	var respuest = confirm('Seguro ?');

	if(respuest){

		var id_titular_nuevo = $(this).data('id_titular_nuevo');
		var id_titular = $('#id_titular').val();
		var id_afiliado = $('#modalVincular_familiar_ID_afiliado').val();

		var datos = {
			"parametro": "vincular_otro_grupo_familiar",
			"id_afiliado": id_afiliado,
			"id_titular": id_titular,
			"id_titular_nuevo": id_titular_nuevo
		};



		$.ajax({

			url: 'ajax.php',
			type: 'get',
			data: datos,
			success: function(data){		

				if(data=="ok"){

					alert("Cambio realizado, ahora debe buscar el nuevo grupo familiar para visualizar el cambio");
					$('.cierra-modal').click();
					$('#id_titular').val(id_titular_nuevo);
					LoadGP(false);
				}	
				else{
					console.log(data);
				}			

			}
		})

	}
	else{
		return false;
	}



})

$('#tabGrupoFamiliar').on('click','.desvincular-familiar',function(){
	var id_titular = $(this).data('id_titular');
	var id_afiliado = $(this).data('id_afiliado');
	var cuil = $(this).data('cuil');

	$('#modalDesvincularTitulo').html('');
	$('#modalDesvincularTitulo').append('Convertir a titular '+cuil);
	$('#modalDesvincularID_titular').val(id_titular);
	$('#modalDesvincularID_afiliado').val(id_afiliado);
	$('#modalDesvincularCUIL').val(cuil);
	$('#modalTransferirTitularGrabar').css('display','none');
	$('#modalDesvincularGrabar').css('display','inline');
				/*
				
				*/

			})

$('#tabGrupoFamiliar').on('click','.transferir-titularidad',function(){
	var id_titular = $(this).data('id_titular');
	var id_afiliado = $(this).data('id_afiliado');
	var cuil = $(this).data('cuil');

	$('#modalDesvincularTitulo').html('');
	$('#modalDesvincularTitulo').append('Transferir titularidad '+cuil);
	$('#modalDesvincularID_titular').val(id_titular);
	$('#modalDesvincularID_afiliado').val(id_afiliado);
	$('#modalDesvincularCUIL').val(cuil);
	$('#modalTransferirTitularGrabar').css('display','inline');
	$('#modalDesvincularGrabar').css('display','none');
				/*
				
				*/

			})

$(document).on('click','.nov_agregar',function(){
	var cuil = $(this).data('cuil');
	var tipo_mov = $(this).data('tipo_mov');
	var fecha_movimiento = $(this).data('fecha_movimiento');

	console.log(nd);
	var datos = {
		"parametro": "novedades_agregar_a_presentacion",
		"cuil": cuil,
		"tipo_mov": tipo_mov,
		"fecha_movimiento": fecha_movimiento
	};

	$.ajax({

		url: 'ajax.php',
		type: 'get',
		data: datos,
		success: function(data){	

			if(data=="ok"){
				alert("Agregado con exito");
			}
			else{
				console.log(data);
				alert("Ocurrio un error");
			}
		}
	})


})

$('#BodyCerrar').on('click',function(){
	$('#divBody').hide();
})

$('#modalDesvincularGrabar').on('click',function(e){

	e.preventDefault();

	var id_titular = $('#modalDesvincularID_titular').val();
	var id_afiliado = $('#modalDesvincularID_afiliado').val();
	var cuil = $('#modalDesvincularCUIL').val();
	var fecha = $('#modalDesvincularFecha').val();
	var id_tbt = $('#modalDesvincularTBT').val();

	if(!fecha){
		alert('Ingrese una fecha de Alta para esta desvinculacion!');
	}
	else{

		var conf = confirm('¿Seguro?');

		if(conf){
			$.ajax({
				url: ajax_url,
				type: 'GET',
				dataType: 'text',
				data: {parametro: 'desvincular_familiar' ,id_titular: id_titular ,id_afiliado: id_afiliado,fecha:fecha ,id_tbt: id_tbt},
			})
			.done(function(data) {
							//console.log(data);
							if(data === 'ok'){
								alert('Cambio grabado exitosamente! Busquelo con su cuil '+cuil+' en caso que quiera revisarlo.');
								$('#modalDesvincular').hide();
								LoadGP(false);
							}
							else{
								alert('data')
							}
							
						});

		}
	}
})

$('#modalTransferirTitularGrabar').on('click',function(e){

	e.preventDefault();

	var id_titular = $('#modalDesvincularID_titular').val();
	var id_afiliado = $('#modalDesvincularID_afiliado').val();
	var cuil = $('#modalDesvincularCUIL').val();
	var fecha = $('#modalDesvincularFecha').val();
	var id_tbt = $('#modalDesvincularTBT').val();

	if(!fecha){
		alert('Ingrese una fecha de Alta para esta desvinculacion!');
	}
	else{

		var conf = confirm('¿Seguro?');

		if(conf){
			$.ajax({
				url: ajax_url,
				type: 'GET',
				dataType: 'text',
				data: {parametro: 'transferir_titularidad' ,id_titular: id_titular ,id_afiliado: id_afiliado,fecha:fecha ,id_tbt: id_tbt},
			})
			.done(function(data) {
							//console.log(data);
							if(data === 'ok'){
								alert('Cambio grabado exitosamente!');
								$('#modalDesvincular').hide();
								LoadGP(false);
							}
							else{
								alert('data')
							}
							
						});

		}
	}
})



			//Si modifica algun campo de los 3 formularios, de actualiza el input hidden para indicarlo.
			$('.inp_personales').on('change',function(){
				//console.log($(this).attr('name'))
				
				var nd_valida = $("#nd").val();

				if(nd_valida.length>=6){

					$('input[name=formDatosPersonales]').val(1);
					var inp_name = $(this).attr('name')+'_ant';
					//console.log(inp_name);
					$("input[name="+inp_name+"]").attr('data-inp_estado',1);
					$("#btnGuardarCambios").show();
					$("#btnGuardarCambios").removeClass("btn-link").addClass("btn-success");
				}else{
					$("#btnGuardarCambios").hide();
				}
				
			})

			$('.inp_domicilio').on('change',function(){
				//console.log($(this).attr('name'))
				var nd_valida = $("#nd").val();

				if(nd_valida.length>=6){
					$('input[name=formDomicilio]').val(1);
					var inp_name = $(this).attr('name')+'_ant';
					//console.log(inp_name);
					$("input[name="+inp_name+"]").attr('data-inp_estado',1);
					$("#btnGuardarCambios").show();
					$("#btnGuardarCambios").removeClass("btn-link").addClass("btn-success");	
				}else{
					$("#btnGuardarCambios").hide();
				}
			})

			$('.inp_dtos_afiliacion').on('change',function(){
				//console.log($(this).attr('name'))
				var nd_valida = $("#nd").val();

				if(nd_valida.length>=6){
					$('input[name=formDatosAfiliacion]').val(1);
					var inp_name = $(this).attr('name')+'_ant';
					//console.log(inp_name);
					$("input[name="+inp_name+"]").attr('data-inp_estado',1);
					$("#btnGuardarCambios").show();
					$("#btnGuardarCambios").removeClass("btn-link").addClass("btn-success");
				}else{
					$("#btnGuardarCambios").hide();
				}
			})

			

			/*******************************************************************************************/

			$('#btnVerAportes').on('click',function(){

				var cuil = $('#cuil_titular').val();
				//var url = "../padron/afil_consulta_ddjj.php?p_cuil="+cuil;
				//var url = "http://"+DOMINIO+"/consultas/index.php?p_cuil="+cuil;
				var url = "../consultas/index.php?p_cuil="+cuil;
				abrirEnPestana(url);

			})

			$('#tabGrupoFamiliar').on('click','.infoPatologia',function(){

				

				$("#pat_id_afiliado").val($(this).data('id_afiliado'));
				$("#pat_id_titular").val($('#id_titular').val());

			})

			//Prueba de small modal - luego eliminar
			$('#btnModalLoading').on('click',function(){

				setTimeout(function(){ 
					$('#btnCierraSmallModalLoading').click();
				}, 3000);

			})

			//Guardar cambios de un afiliado
			$('#btnGuardarCambios').on('click',function(){


				var proceso_dtos_p = proceso_domicilio = proceso_dtos_afi = proceso_certificado = 0;

				var dni_length = $('#fm_dni').val();
				
				//console.log(dni_length.length);

				
				var r = confirm("¿Esta seguro?");
				if (r == true) {

					if($('input[name=formDatosPersonales]').val()==1){

						if ($('input[name=desreguladora_ant]').val() != $('select[name=desreguladora]').val()) {
							let obs = prompt('Observación por cambio de Capita');
							if (obs === null || obs.trim() === "") {
								alert("El cambio fue cancelado o no se ingresó observación.");
								return; // ⚠️ Salís del flujo, no ejecuta grabar_datos_personales()
							}
							$('#observacion_desreguladora').val(obs);
						}

						// Solo se llega acá si no hubo cancelación
						grabar_datos_personales().then(function(data) {
							proceso_dtos_p = estado_dp;
							console.log('ES: ' + proceso_dtos_p);
						});

					}

					if($('input[name=formDomicilio]').val()==1){
				  		if(tipo_perfil == "upd_dto_personales"){//Esta validacion queda obsoleta
				  			//alert('No tiene acceso a modificar datos de DOMICILIO'); return false;
								//$("#btnGuardarCambios").hide();
							}
							else{
							}
							proceso_domicilio = grabar_domicilio();

						//console.log(proceso_domicilio);
					}

					if($('input[name=formDatosAfiliacion]').val()==1){

						
						if(tipo_perfil == "upd_dto_personales"){//Esta validacion queda obsoleta
							alert('No tiene acceso a modificar datos de AFILIACION'); return false;
							//$("#btnGuardarCambios").hide();
						}
						else{
						}
						var id_titular = $('#id_titular').val();
						var id_afiliado = $('input[name=id_afiliado]').val();
						//console.log('id_titular: '+id_titular+' id_afiliado: '+id_afiliado)
						if(id_titular==id_afiliado){
							//console.log('va a grabar dtos afiliacion');
							proceso_dtos_afi = grabar_dtos_afiliacion();	
						}
						
					}
					//var proceso_certificado ;
					if($('input[name=DatosCE]').val()==1){
						
						//proceso_certificado = grabar_baja_automatica();
						//console.log('Proceso certificado: '+proceso_certificado);
						var estado_baja = 0;

						var parametro = "graba_baja_automatica";
						var id_afiliado = $('input[name=id_afiliado]').val();
						var baja_ce = $('input[name=ce_baja]').val();
						var observacion = $('input[name=ce_observacion]').val();
						
						var myFormData = new FormData();
						myFormData.append('parametro', parametro);
						myFormData.append('id_afiliado', id_afiliado);
						myFormData.append('ce_archivo', ce_archivo.files[0]);
						myFormData.append('fecha_aPartir', baja_ce);
						myFormData.append('observacion', observacion);

						$.ajax({
							url: ajax_url,
							type: 'POST',
						  processData: false, // important
						  contentType: false, // important
						  dataType : 'json',
						  data: myFormData,
						  success: function(data){

						  	if(data=="ok"){
						  		estado_baja = 0;
						  	}
						  	else{
						  		estado_baja = 1;
						  	}

							//console.log(data);

						}
					});
					}
					

					if(proceso_dtos_p==0 && proceso_domicilio==0 && proceso_dtos_afi==0 && proceso_certificado==0 ){

						$('#btnGuardarCambios').notify(
							"Las modificaciones se grabaron correctamente", 
							{ position:"bottom",className: 'success' }
							);

						LoadGP(false);
						$('#BodyCerrar').click();
						$("#btnGuardarCambios").hide();

						$("#btnGuardarCambios").removeClass("btn-success").addClass("btn-link");
					}
					else{
						$('#btnGuardarCambios').notify(
							"Ocurrio un error al grabar, comuniqueselo a sistemas.", 
							{ position:"bottom",className: 'error' }
							);

						//$("#btnGuardarCambios").removeClass("btn-success").addClass("btn-link");
					}

				} 
				
			})//Fin Guardar cambios

			/* VALIDACIONES Guardar Cambios - Alan */

			//Consulta de CUIL
			$('#tabDatosPersonales').on('blur change','#sexo',function(){

				var dni = $('#nd').val();
				var sexo = $(this).val();
				//console.log('Che vino esto '+dni+' '+sexo);
				
				//Trae cuil
				$.getJSON(ajax_url,
					{ parametro: "consulta_cuil", dni: dni, sexo: sexo },						       				
					function(data){ 

						if(data[0]['estado']==0){
									//alert('El afiliado no existe');
									//console.log('No trajo el domicilio');
									$.notify(data[0]['cuil'], "error");
								}
								else{
									
									$('#cuil').val(data[0]['cuil']);
									
								}


							}//fin function data

				);//fin getjson Trae datos personales
				
			})//FIN - Consulta de CUIL



			/************************************************************/


			/******* Documentacion *****************************************************/

			$('#tabGrupoFamiliar').on('click','.docItem',function(){

				var id_afiliado = $(this).data('id_afiliado');
				$('#doc_id_afiliado').val(id_afiliado);
				console.log(id_afiliado);

				$.getJSON(ajax_url,
					{ parametro: "ver_documentacion", id_afiliado: id_afiliado },						       				
					function(data){ 
						
						$("#tabVerDocumentacion tbody").html("");

						if(data[0]['estado']=="ERROR - NO se encontraron resultados"){
							$("#tabVerDocumentacion tbody").append("<tr>"																
								+"<td colspan=8>No se subieron archivos</td>"
								+"</tr>") ;	
						}
						else{

							for(var i=0; i<=data.length-1 ;i++){

								$("#tabVerDocumentacion tbody").append("<tr>"																
									+"<td>"+(i+1)+"</td>"
									+"<td>"
									+"<a class='btn btn-xs btn-default verArchivo' title='Ver archivo' data-id_doc="+data[i]['id_documentacion']+"><i class='fas fa-eye'></i></a>"
									+"</td>"
									+"<td>"+data[i]['documentacion']+"</td>"
									+"<td>"+data[i]['archivo']+"</td>"
									+"<td>"+data[i]['fecha']+"</td>"
									+"<td>"+data[i]['observacion']+"</td>"
									+"<td>"+data[i]['usuario']+"</td>"
									+"<td>"+data[i]['fecha_carga']+"</td>"											

									+"</tr>") ;		
							}	

						}

						
					}//fin function data

				);//fin getjson

			})

			/******* FIN - Documentacion *****************************************************/




			/****************ALta familiar**********************/

			//Nuevo GPAR para familiar
			$('#btnNuevoFamiliar').on('click',function(){

				var datos = {
					"parametro": "nuevo_gpar",
					"id_titular": $('#id_titular').val()
				};

				$.ajax({

					url: ajax_url,
					type: 'get',
					data: datos,
					success: function(data){						
						
						console.log(data);
						$('#fm_gpar').val("");
						$('#fm_gpar').val(data);

					}
				})

			})
			
			$('#fm_dni').on('blur',function(){

				var datos = {
					"parametro": "dni_familiar",
					"dni": $(this).val()
				};

				$.ajax({

					url: ajax_url,
					type: 'get',
					data: datos,
					success: function(data){						
						
						console.log(data);
						var mensaje = "";

						if(data.length>0){

							mensaje = data+" ya existe en el padron."

							$('#fm_dni').notify(
								mensaje, 
								{ position:"right" }
								);	

							$('#btnAltaFamiliar').prop('disabled', true);
						}
						else{
							$('#btnAltaFamiliar').prop('disabled', false);
						}

					}
				})

			})

			//SELECTORES
			//provincia
			$.getJSON(ajax_url,
				{ parametro: "provincia" },						       				
				function(datos){ 

					$.each(datos, function (key, item) {
						$("select[name=fm_provincia]").append("<option value="+item.id+">"+item.provincia+"</option>");
					});

					$("#fm_provincia").val(2).attr('selected','selected');

						}//fin function data

			);//fin getjson

			//Localidad
			$.getJSON(ajax_url,
				{ parametro: "localidad", provincia: 2 },						       				
				function(datos){ 

					$.each(datos, function (key, item) {
						$("#fm_localidad").append("<option label='CP: "+item.localidad+"'  value='"+item.id+"' >");
					});

						}//fin function data

			);//fin getjson
			
			$('#tabAltaFamiliar').on('change','select[name=fm_provincia]',function(){//NUEVO FAMILIAR - Hace variar el selector de Localidades dependiendo la provincia
				$("#fm_localidad").empty();
				var provincia = $(this).val();
				console.log(provincia);

				$.getJSON(ajax_selects_url,{ parametro: "localidad", provincia: provincia },function(datos){ 		
					$.each(datos, function (key, item) {
						$("#fm_localidad").append("<option label='CP: "+item.localidad+"'  value='"+item.id+"' >");
					});
				});//fin getjson
			})
			$('#select_s_domicilio').on('change',function(){//NUEVO FAMILIAR -Carga domicilio del titular o uno propio del familiar
				if($(this).val()==0){
					$('.tr_fm_nuevo_domicilio input').attr('disabled','disabled');
					$('.tr_fm_nuevo_domicilio select').attr('disabled','disabled');
				}
				else{
					$('.tr_fm_nuevo_domicilio input').removeAttr('disabled');
					$('.tr_fm_nuevo_domicilio select').removeAttr('disabled');
				}
			})
			$("#tabAltaFamiliar").on('blur','#fm_inp_localidad',function(){//NUEVO FAMILIAR - Seleccion de la localidad  
				var id_localidad = document.querySelector('#fm_inp_localidad').value;
				var t_localidad = AddValue(document.getElementById('fm_inp_localidad'),document.getElementById('fm_localidad')) ;

				$('#fm_id_localidad').val(id_localidad);				
				$('#fm_inp_localidad').val(t_localidad);				
			})
			$('#tabAltaFamiliar').on('blur change','#fm_sexo',function(){//NUEVO FAMILIAR - Consulta de CUIL
				var dni = $('#fm_dni').val();
				var sexo = $(this).val();
				$.getJSON(ajax_url,
					{ parametro: "consulta_cuil", dni: dni, sexo: sexo },						       				
					function(data){ 
						if(data[0]['estado']==0){
							$.notify(data[0]['cuil'], "error");
						}
						else{
							$('#fm_cuil').val(data[0]['cuil']);
						}
					});
			})
			$('#btnAltaFamiliar').on('click',function(){//NUEVO FAMILIAR - GRABAR
				var r = confirm("¿Confirma el alta del nuevo familiar?");
				if (r == true) {
					//Datos principales
					var id_titular = $('#id_titular').val();
					var dni = $('#fm_dni').val();
					var sexo = $('#fm_sexo').val();
					var parentesco = $('#fm_parentesco').val();
					var gpar = $('#fm_gpar').val();
					var cuil = $('#fm_cuil').val();
					var fn = $('#fm_fn').val();
					var apellido = $('#fm_apellido').val();
					var nombre = $('#fm_nombre').val();
					var fecha_alta = $('#fm_fecha_alta').val();
					var incapacidad = $('#fm_incapacidad').val();
					var nacionalidad = $('#fm_nacionalidad').val();
					var estado_civil = $('#fm_estado_civil').val();
					var telefono = $('#fm_telefono').val();
					var email = $('#fm_email').val();
					//Domicilio
					var select_s_domicilio = $('#select_s_domicilio').val();
					var id_domicilio_titular = $('#inp_fm_id_domicilio').val();
					var localidad = $('#fm_id_localidad').val();					
					var calle = $('#fm_calle').val();
					var numero = $('#fm_numero').val();
					var departamento = $('#fm_departamento').val();
					var piso = $('#fm_piso').val();

					var datos = {
						"parametro": "alta_familiar",
						"id_titular": id_titular,	
						"dni": dni,
						"sexo": sexo,
						"parentesco": parentesco,
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

						"select_s_domicilio": select_s_domicilio,
						"id_domicilio_titular": id_domicilio_titular,
						"localidad": localidad,
						"calle": calle,
						"numero": numero,
						"departamento": departamento,
						"piso": piso
					};
					$.ajax({
						url: ajax_url,
						type: 'POST',
						data: datos,
						success: function(data){						
							console.log(data);
							if(data=="ok"){
								alert("Familiar Grabado") ;
								LoadGP(false);
							}
							else{
								alert('Error en la grabacion, comuniqueselo a sistemas');
								LoadGP(false);
							}
							$('#fm_cuil').val("").change();
							$('#fm_dni').val("").change();
							$('#modal_add_fam').modal('hide');
						}});//Fin AJAX()
				}
				else{ return false; }
			})

			$('.btn-modificaFechaAB').on('click',function(){

				var id_afiliado = $(this).data('id_afiliado');
				
				if(id_afiliado.length==0){

					$.notify("Debe seleccionar a un afiliado primero.", "error");

					return false;
				}
				else{

					var operacion = $(this).data('operacion');
					var tipo_afil = $(this).data('tipo_afil');
					var fec_alta = $(this).data('fec_alta');
					var fec_baja = $(this).data('fec_baja');
					
					var cuil = $(this).data('cuil');

					var link_modifica_estado = 'modifica_estado.php?operacion='+operacion
					+'&tipo_afil='+tipo_afil
					+'&fec_alta='+fec_alta
					+'&fec_baja='+fec_baja
					+'&id_afiliado='+id_afiliado
					+'&cuil='+cuil ;

					window.open(link_modifica_estado,'','width=1000,height=500,scrollbars=yes'); 
					window.close();

				}

			})

			$('#imprimir_credencial').on('click',function(){
				let cuil = $(this).data('cuil');
				let nd = $(this).data('nd');

				let datos = {
					'parametro': 'verificar_credencial',
					'nd': nd
				};

				$.ajax({
					url: ajax_url,
					dataType: 'json',
					type: 'GET',
					data: datos
				}).then(function(data){

					let {estado, cod_filial, fecha_vencimiento, propio} = data;

					if(propio == 1){
						if(!cod_filial){
							alert('El Titular no tiene una Filial asignada');
						}else{
							let url
							let respuesta = prompt("Verifique la fecha de vencimiento.", fecha_vencimiento);
							if (respuesta != null) {
								
								if(tit_tbt == "RG" && !tit_empresa){
									empresa = prompt('El afiliado no tiene una empresa declarada a la fecha. Cargue el nombre de la empresa.');
									if(empresa){
										url = "../carnets/imprimir_carnets.php?sl=678&fv="+respuesta+"&cuil="+cuil+"&empresa="+empresa;
									}
									window.open(url);
								}else{
									url = "../carnets/imprimir_carnets.php?sl=678&fv="+respuesta+"&cuil="+cuil;
									window.open(url);
								}
							}	
						}
					}else{
						alert('Este titular no es un afiliado genuino.');
					}
					
				});
			})
			$(document).on('click','.credenciales',function(e){
				$('#imprimir_credencial').data('cuil',$(this).data('cuil'));
				$('#imprimir_credencial').data('nd',$(this).data('nd'));
				CargarInfoCredencial($(this).data('id_afiliado'));
			})
			$("#tabGrupoFamiliar tbody").on('click','.btnCronologia',function(){
				var id_persona = $(this).data('id_persona');
				genera_tabla_cronologia();
				llena_tabla_cronologia(id_persona);
			})
		});//Fin (function(){}) (Funcion Anonima)

		function recarga_localidad(){

			$.ajax({
				url: ajax_url, 
				type: 'get',
				dataType: 'json',
				data : {parametro: "buscar_localidad_especifica",id: $("#t_inp_localidad").val()}
			}).then(function(data){
		        var option = new Option(data[0].text, data[0].id, true, true); // Creamos la nueva opción
		        
		        // Usamos el método de select2 para agregar la nueva opción con formato HTML
		        $('#t_id_localidad').append(option).trigger('change');

		        // Aseguramos que se renderice correctamente el HTML
		        $('#t_id_localidad').trigger({
		            type: 'select2:select',
		            params: {
		                data: data[0]
		            }
		        });
			});
		}
		//Funciones de Guardar datos afiliado
		function salva_da_dtos_personales(data){

			$("input[name=nd_ant]").val(data[0]['nd']);
			$("input[name=sexo_ant]").val(data[0]['sexo']);
			$("input[name=desreguladora_ant]").val(data[0]['id_desreguladora']);
			$("input[name=nben_ant]").val(data[0]['nben']);
			$("input[name=gpar_ant]").val(data[0]['gpar']);
			$("input[name=apellido_ant]").val(data[0]['apellido']);
			$("input[name=nombre_ant]").val(data[0]['nombre']);
			$("input[name=parentesco_ant]").val(data[0]['id_parentesco']);
			$("input[name=estado_civil_ant]").val(data[0]['id_estado_civil']);
			$("input[name=fn_ant]").val(data[0]['fn']);
			$("input[name=incapacidad_ant]").val(data[0]['incapacidad']);
			$("input[name=telefono_ant]").val(data[0]['telefono']);
			$("input[name=email_ant]").val(data[0]['email']);

		}

		function grabar_datos_personales(){


			return new Promise(function(resolve, reject) {
				
				let estado_dp = 0;

				let id_afiliado = $('input[name=id_afiliado]').val();
				let nd = $('#nd').val();

				let datos = {
					"parametro": "graba_datos_personales",
					"id_afiliado": id_afiliado,
					"nd": nd,
					"sexo": $('select[name=sexo]').val(),
					"cuil": $('input[name=cuil]').val(),
					"desreguladora": $('select[name=desreguladora]').val(),
					"nben": $('input[name=nben]').val(),
					"gpar": $('input[name=gpar]').val(),
					"apellido": $('input[name=apellido]').val(),
					"nombre": $('input[name=nombre]').val(),
					"parentesco": $('select[name=parentesco]').val(),
					"fn": $('input[name=fn]').val(),
					"estado_civil": $('select[name=estado_civil]').val(),
					"incapacidad": $('select[name=incapacidad]').val(),
					"telefono": $('input[name=telefono]').val(),					
					"email": $('input[name=email]').val(),

					"nd_ant": $('input[name=nd_ant]').val(),
					"sexo_ant": $('input[name=sexo_ant]').val(),				
					"desreguladora_ant": $('input[name=desreguladora_ant]').val(),
					"nben_ant": $('input[name=nben_ant]').val(),
					"gpar_ant": $('input[name=gpar_ant]').val(),
					"apellido_ant": $('input[name=apellido_ant]').val(),
					"nombre_ant": $('input[name=nombre_ant]').val(),
					"parentesco_ant": $('input[name=parentesco_ant]').val(),
					"fn_ant": $('input[name=fn_ant]').val(),
					"estado_civil_ant": $('input[name=estado_civil_ant]').val(),
					"incapacidad_ant": $('input[name=incapacidad_ant]').val(),
					"telefono_ant": $('input[name=telefono_ant]').val(),					
					"email_ant": $('input[name=email_ant]').val(),		

					'observacion_desreguladora': $('#observacion_desreguladora').val()		
				};


				ValidDNI(id_afiliado,nd).then(function(data){
					console.log(data);
					let {valid} = data;

					if(valid){
						SavePersona(datos).then(function(data){
							//console.log(data);
							
							if(data=="ok"){

								
								if($('input[name=desreguladora_ant]').val()!=$('select[name=desreguladora]').val()){
									$("#gf_desreguladora").html("Desreguladora: <b>"+$('select[name=desreguladora] option:selected').text()+"</b>");
									
								}
								estado_dp = 0;
							}
							else{
								estado_dp = 1;
							}

							resolve(estado_dp);
						}).catch(function(err){
							console.log(err);
						});	
					}else{
						$('#btnGuardarCambios').notify(
							"Funcion deshabilitada temporalmente.", 
							{ position:"bottom",className: 'error' }
							);
					}
				}).catch(function(err){
					console.log(err);
				});

				



			});
		}
		function ValidDNI(id_afiliado,nd) {
			return new Promise(function(resolve, reject) {
				$.ajax({
					url: ajax_url,
					data: {parametro: 'validar_dni', id_afiliado: id_afiliado, nd: nd},
					dataType: 'json',
					success: function(data) {
		        resolve(data) // Resolve promise and go to then()
		      },
		      error: function(err) {
		        reject(err) // Reject the promise and go to catch()
		      }
		    });
			});
		}
		function SavePersona(datos){
			return new Promise(function(resolve, reject) {
				/*

				*/
				$.ajax({
					url: ajax_url,
					type: 'get',
					data: datos,
					success: function(data) {
		        resolve(data) // Resolve promise and go to then()
		      },
		      error: function(err) {
		        reject(err) // Reject the promise and go to catch()
		      }
		    });
			});
		}

		function salva_da_domicilio(data){
			$("input[name=t_id_localidad_ant]").val(data[0]['id_localidad']);
			$("input[name=calle_ant]").val(data[0]['calle']);
			$("input[name=numero_ant]").val(data[0]['numero']);
			$("input[name=piso_ant]").val(data[0]['piso']);
			$("input[name=depto_ant]").val(data[0]['depto']);
		}
		function grabar_domicilio(){
			var estado_dom = 0;
			//Valores actuales
			var id_afiliado = $('input[name=id_afiliado]').val();
			var id_domicilio_afiliado = $('input[name=id_domicilio_afiliado]').val();
			var id_localidad = $('#t_id_localidad').val();
			var calle = $('input[name=calle]').val();
			var numero = $('input[name=numero]').val();
			var piso = $('input[name=piso]').val();
			var departamento = $('input[name=depto]').val();

			//Valores anteriores
			var id_localidad_ant = $("input[name=t_id_localidad_ant]").val();
			var calle_ant = $("input[name=calle_ant]").val();
			var numero_ant = $("input[name=numero_ant]").val();
			var piso_ant = $("input[name=piso_ant]").val();
			var departamento_ant = $("input[name=depto_ant]").val();

			var datos = {
				"parametro": "graba_domicilio",
				"id_afiliado": id_afiliado,
				"id_domicilio_afiliado": id_domicilio_afiliado,
				"id_localidad": id_localidad,
				"calle": calle,
				"numero": numero,
				"piso": piso,
				"departamento": departamento,

				"id_localidad_ant": id_localidad_ant,
				"calle_ant": calle_ant,
				"numero_ant": numero_ant,
				"piso_ant": piso_ant,
				"departamento_ant": departamento_ant
			};

			$.ajax({
				url: ajax_url,
				type: 'get',
				data: datos,
				success: function(data){	
					if(data=="ok"){
						estado_dom = 0;
					}
					else{
						estado_dom = 1;
					}
				}
			})	
			return estado_dom;		
		}
		function salva_da_dtos_afi(data){
			$('input[name=tbt_ant]').val(data[0]['id_tbt']);
			$('input[name=revista_ant]').val(data[0]['id_revista']);
			$('input[name=seccional_ant]').val(data[0]['id_seccional']);
			$('#plan_medico_ant').val(data[0]['id_plan_medico']);
		}

		function grabar_dtos_afiliacion(){

			let id_afiliado = $('input[name=id_afiliado]').val();
			var estado_dt_afil = 0;

			let datos = {
				"parametro": "graba_datos_afiliacion",
				"id_afiliado": id_afiliado,
				"tbt": $('select[name=tbt]').val(),
				"revista": $('select[name=revista]').val(),
				"seccional": $('select[name=seccional]').val(),
				"plan_medico": $('#plan_medico').val(),
				"tbt_ant": $('input[name=tbt_ant]').val(),
				"revista_ant": $('input[name=revista_ant]').val(),
				"seccional_ant": $('input[name=seccional_ant]').val(),
				"plan_medico_ant": $('#plan_medico_ant').val()
			};

			$.ajax({

				url: ajax_url,
				type: 'get',
				data: datos,
			}).then(function(data){
				//console.log(data); return false;
				if(data=="ok"){
					estado_dt_afil = 0;
				}
				else{
					estado_dt_afil = 1;
				}
			});
			return estado_dt_afil;	
		}
		//FIN - Funciones de Guardar datos afiliado

		//Funciones de alta familiar
		function alta_familiar(){//Actualmente sin uso

			//Datos principales
			var id_titular = $('#id_titular').val();
			var dni = $('#fm_dni').val();
			var sexo = $('#fm_sexo').val();
			var parentesco = $('#fm_parentesco').val();
			var gpar = $('#fm_gpar').val();
			var cuil = $('#fm_cuil').val();
			var fn = $('#fm_fn').val();
			var apellido = $('#fm_apellido').val();
			var nombre = $('#fm_nombre').val();
			var fecha_alta = $('#fm_fecha_alta').val();
			var incapacidad = $('#fm_incapacidad').val();
			var nacionalidad = $('#fm_nacionalidad').val();
			var estado_civil = $('#fm_estado_civil').val();
			var telefono = $('#fm_telefono').val();
			var email = $('#fm_email').val();
			//Domicilio
			var select_s_domicilio = $('#select_s_domicilio').val();
			var id_domicilio_titular = $('#inp_fm_id_domicilio').val();
			var localidad = $('#fm_inp_localidad').val();
			var calle = $('#fm_calle').val();
			var numero = $('#fm_numero').val();
			var departamento = $('#fm_departamento').val();
			var piso = $('#fm_piso').val();

			var datos = {
				"parametro": "alta_familiar",
				"id_titular": id_titular,	
				"dni": dni,
				"sexo": sexo,
				"parentesco": parentesco,
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
				"select_s_domicilio": select_s_domicilio,
				"id_domicilio_titular": id_domicilio_titular,
				"localidad": localidad,
				"calle": calle,
				"numero": numero,
				"departamento": departamento,
				"piso": piso
			};

			$.getJSON(ajax_url,datos,function(data){ 			
				if(data[0]['estado']=="ok"){

					alert("Grabado con exito");
					LoadGP(false);
				}
				else{
					console.log(data);
				}			
			});//Fin getJSON
		}
		//FIN - Funciones de alta familiar

		//Funciones de utilidad
		function abrirEnPestana(url){//Abre en una nueva pestaña la url traida como parametro
			var a = document.createElement("a");
			a.target = "_blank";
			a.href = url;
			a.click();
		}
		function AddValue(el, dl){//Toma el elemento seleccionado de un datalist y lo coloca en el input asignado
			if(el.value.trim() != ''){
				var opSelected = dl.querySelector(`[value="${el.value}"]`);
				return opSelected.getAttribute('label');
			}
		}
		function genera_tabla_cronologia(){

			$('#divListCronologia').html("<table id='tabListaCronologia' class='table' style='font-size: 11px;'>"
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

			var li_modal_crono = "";

			$.getJSON('ajax.php',
				{ parametro: "lst_cronologia_afiliado", id_persona: id_persona },						       				
				function(data){ 

					$("#tabListaCronologia tbody").html("");

					console.log("cantidad: "+data.length);

					for(var i=0; i<=data.length-1 ;i++){



						$("#tabListaCronologia tbody").append("<tr>"																
							+"<td>"+(i+1)+"</td>"


							+"<td>"+data[i]['fechador']+"</td>"
							+"<td>"+data[i]['movimiento']+"</td>"


							+"</tr>") ;		
					}	

						}//fin function data

			);//fin getjson

		}
		function copytext(text){//Copia al portapapeles el texto traido como parametro
			var textField = document.createElement('textarea');
			textField.innerText = text;
			document.body.appendChild(textField);
			textField.select();
		    textField.focus(); //SET FOCUS on the TEXTFIELD
		    document.execCommand('copy');
		    textField.remove();
		    console.log('should have copied ' + text); 
		    AltaModal.focus(); //SET FOCUS BACK to MODAL
		  }
		function TraerEstadosHoy(){//Actualmente sin uso
			console.log('Hey');
			$('#tabGrupoFamiliar tbody tr').each(function(){
				var thisTr = $(this);
				var id_afiliado = $(this).find('td.estado_hoy').data('id_afiliado');
				var cuil = $(this).find('td.estado_hoy').data('cuil');
				$.ajax({
					url: ajax_url,
					type: 'get',
					dataType: 'text',
					data: {parametro: 'traer_estado_dia',cuil:cuil},
					success: function(data){
						console.log(cuil+" "+data);
						thisTr.find('td.estado_hoy').html('');
						thisTr.find('td.estado_hoy').html(data);
						if(data=='BAJA'){
							thisTr.addClass('tr_estado_baja');
						}
					}
				});
			});
		}
		function VGP(data,id_af_consultado){
			$("#tabGrupoFamiliar tbody").html(""); 
			var cuil = '';

			for(var i=0; i<=data.length-1 ;i++){

				let {cuil,id,nd,sincronizacion,patologias,nro_sind} = data[i];

				tr_estado = item_sincronizar = badge_sincronizar = estados_item = documentacion_item = documentacion_item_nuevo = documentacion_item_surge =  cambios_item = item_grupo_familiar = item_desvincular_familiar = credenciales = li_modal_crono = td_bgcolor_sind = td_consultado_style = td_patologias = td_nro_sind = item_transferir_titularidad ="";

				if(tipo_perfil !='consulta_externo'){ 	
					documentacion_item = "<a class='dropdown-item' 'style='cursor:pointer;' href='http://"+INST_DOCUMENTACION_URL+"/documentacion/subir_docu.php?id_afiliado="+data[i]['id']+"&cuil="+data[i]['cuil']+"&ayn="+data[i]['ayn_persona']+"' target='_blank' >"
					+" Documentacion</a>";

					if(N_BASE != "ospm"){
						documentacion_item_surge = "<a class='dropdown-item' style='cursor:pointer;' href='http://"+INST_DOCUMENTACION_URL+"/documentacion/subir_docu_surge.php?id_afiliado="+data[i]['id']+"&cuil="+data[i]['cuil']+"&ayn="+data[i]['ayn_persona']+"' target='_blank' >"
						+" Documentacion SURGE</a>";
					}

					if(N_BASE == "oseam" || N_BASE == "ospm"){
						documentacion_item_nuevo = "<a class='dropdown-item' 'style='cursor:pointer;' href='doc_export/browser.php?id_afiliado="+data[i]['id']+"&cuil="+data[i]['cuil']+"&ayn="+data[i]['ayn_persona']+"' target='_blank' >"
						+" Documentacion 2025</a>";
					}
					cambios_item = "<a class='dropdown-item' style='cursor:pointer;' "
					+"data-id_afiliado='"+data[i]['id']+"' "
					+"data-toggle='modal' "
					+"data-target='#modalCambios' "
					+">"
					+"Ver Cambios"
					+"</a>";

					estados_item = "<a class='dropdown-item ver-estados' style='cursor:pointer;' "
					+"data-cuil='"+data[i]['cuil']+"' "
					+"data-nd='"+data[i]['nd']+"' "
					+"data-id_afiliado='"+data[i]['id']+"' "
					+"data-toggle='modal' "
					+"data-target='#modalEstados' "
					+">"
					+"Ver Historicos"
					+"</a>";

					console.log('Data length' + data.length);
					if(data[i]['parentesco']!="Titular" || (data.length==1) ){//Los Familiares pueden vincularse a otro grupo familiar o convertirse en titulares, por ende el titular no puede tener esas opciones a no ser que este solo


						item_grupo_familiar = "<a class='dropdown-item vincular-otro-grupo-familiar' style='cursor:pointer;' "
						+"data-cuil='"+data[i]['cuil']+"' "
						+"data-id_titular='"+id_titular+"' "
						+"data-id_afiliado='"+data[i]['id']+"' "
						+"data-toggle='modal' "
						+"data-target='#modalVincularOtroGrupo' "
						+">"
						+"Vincular a otro grupo familiar"
						+"</a>";							
					}

					if(data[i]['parentesco']!="Titular"){
						item_desvincular_familiar = "<a class='dropdown-item desvincular-familiar' style='cursor:pointer;' "
						+"data-cuil='"+data[i]['cuil']+"' "
						+"data-id_titular='"+id_titular+"' "
						+"data-id_afiliado='"+data[i]['id']+"' "
						+"data-toggle='modal' "
						+"data-target='#ModalDesvincular' "
						+">"
						+"Convertir a Titular"
						+"</a>";
					}

					if(data[i]['parentesco']!="Titular"){
						item_transferir_titularidad = "<a class='dropdown-item transferir-titularidad' style='cursor:pointer;' "
						+"data-cuil='"+data[i]['cuil']+"' "
						+"data-id_titular='"+id_titular+"' "
						+"data-id_afiliado='"+data[i]['id']+"' "
						+"data-toggle='modal' "
						+"data-target='#ModalDesvincular' "
						+">"
						+"Transferir titularidad"
						+"</a>";
					}

					if(data[i]['parentesco']=="Titular"){
						credenciales = `
						<a class='dropdown-item credenciales' style='cursor:pointer;' data-nd='${nd}' data-cuil='${cuil}' data-id_afiliado='${id}' data-toggle='modal' data-target='#modalCredencial'>
						Credenciales
						</a>`;
					}

					if(data[i]['sincronizacion']>0){//Si esta variable es mayor a 0, significa que tiene sincronizaciones pendientes que deben ser resueltas, y requiere que se le muestre este modal (y opcion para abrirlo)
						item_sincronizar="<a class='dropdown-item desvincular-familiar' style='cursor:pointer;' "
						+"data-cuil='"+data[i]['cuil']+"' "
						+"data-id_titular='"+id_titular+"' "
						+"data-id_afiliado='"+data[i]['id']+"' "
						+"data-toggle='modal' "
						+"data-target='#modalSinc' "
						+">"
						+"Sincronizar Datos (test)"
						+"</a>";

						badge_sincronizar="<span class='badge badge-notify'></span>"

					}
				}
				switch(data[i]['estado']){
					case 'ALTA':
					tr_estado = "class''";
					break
					case 'BAJA':
					tr_estado = " class='tr_estado_baja' ";
					break
				}

				if(data[i]['id']==id_af_consultado){
					td_consultado_style = " class='td_af_consultado' ";
				}

				li_modal_crono="<a class='dropdown-item btnCronologia' data-id_persona="+data[i]['id_persona']+" data-toggle='modal' data-target='#modalCronologia' "
				+"data-cuil='"+data[i]['cuil']+"' "
				+"data-id_titular='"+id_titular+"' "
				+"data-id_afiliado='"+data[i]['id']+"' "
				+"data-toggle='modal' "
				+"data-target='#modalSinc' "
				+">"
				+"Cronologia Novedades"
				+"</a>";

				if(data[i]['nro_sind']!=''){

					td_bgcolor_sind = " style='background-color: red; color: white;' ";
				}

				if(INST_VGP_NRO_SIND == 1){
					td_nro_sind = `<td ${td_bgcolor_sind}>${nro_sind}</td>`;
				}

				if(INST_VGP_PATOLOGIA == 1){
					td_patologias = `<td>${patologias}</td>`;
				}
				
				$("#tabGrupoFamiliar tbody").append("<tr "+tr_estado+" >"																
					+"<td>"+data[i]['id']+"</td>"
					+"<td>"
					+"<div class='dropdown'>"
					+"<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>"

					+"</button>"
					+badge_sincronizar
					+"<div class='dropdown-menu'>"
					+item_sincronizar
					+"<a class='dropdown-item verAfiliado' ' style='cursor:pointer;' data-id_afiliado='"+data[i]['id']+"' data-s_nom_afil='"+data[i]['ayn_persona']+"' data-t_paren='"+data[i]['parentesco']+"' data-cuil='"+data[i]['cuil']+"' data-nd='"+data[i]['nd']+"' >Ver Datos</a>"
					+estados_item
					+cambios_item
					+li_modal_crono
					+documentacion_item
					+documentacion_item_surge
					+documentacion_item_nuevo
						    //+"<!-- <a class='dropdown-item' href='#'>Cambiar estado</a> -->"
						    //+"<!-- <a class='dropdown-item infoPatologia' ' style='cursor:pointer;' data-id_afiliado='"+data[i]['id']+"' data-toggle='modal' data-target='#modalPatologias'>"
						    	//+"Informar patologia</a> -->"
						    	+item_grupo_familiar
						    	+item_desvincular_familiar
						    	+item_transferir_titularidad
						    	+credenciales

						    	+"</div>"
						    	+"</div>"
						    	+"</td>"
						    	+"<td class='estado_hoy'>"
						    	+data[i]['estado']
						    	+"</td>"
						    	+"<td style='text-align: right;'>"+data[i]['beneficiario']+"</td>"
						    	+"<td><span "+td_consultado_style+">"+data[i]['ayn_persona']+"</span></td>"
						    	+"<td>"+data[i]['parentesco']+"</td>"
						    	+"<td>"+data[i]['cuil']+"</td>"
						    	+"<td>"+data[i]['fn']+"</td>"
						    	+"<td>"+data[i]['edad']+"</td>"
						    	+td_nro_sind
						    	+"<td class='incapacidad'>"+data[i]['incapacidad']+"</td>"
						    	+td_patologias					      				
						    	+"</tr>") ;		
			}
			if(tipo_perfil =='consulta_externo'){
				$('.incapacidad').hide();
			}
			//console.log('Termino VGP');

		}
		function LoadGP(load_selects){
			
			$("#tabGrupoFamiliar tbody").html('');
			$("#tabGrupoFamiliar tbody").html('<div class="spinner-grow text-warning" style="margin: 3px;" ></div>');
			
			$("#tabGrupoFamiliar tbody").html(""); 
			var id_titular = $('#id_titular').val();
			var id_af_consultado = $("#id_af_consultado").val();
			$("#tabGrupoFamiliar tbody").html('');
			$("#tabGrupoFamiliar tbody").html('<div class="spinner-grow text-warning" style="margin: 3px;" ></div>');
			
			$.when(
				$.getJSON(ajax_url,{ parametro: "ver_grupo_familiar", id_titular: id_titular },function(data){
					var_vgp = data;
				}),
				$.getJSON(ajax_url,{ parametro: "info_principal", id_titular: id_titular },function(data){ 
					var_tip = data;
				}),
				$.getJSON(ajax_url,{ parametro: "consulta_cuil_titular", id_titular: id_titular },function(data){ 
					var_cct= data;
				}),
				$.getJSON(ajax_url,{ parametro: "es_desempleado", id_titular: id_titular },function(data){ 
					var_dom = data; 
				})
				).then(function(){
					$("#gf_desreguladora").html("Desreguladora: " +"<b>"+ var_tip[0]['desreguladora'] +"</b>");
					$("#gf_tbt").html(var_tip[0]['beneficiario']);
					if(var_tip[0]['ultimo_pye']){	
						var gg = var_tip[0]['ultimo_pye'].split('-');

						tit_tbt = var_tip[0]['sigla_tbt'];
						tit_empresa = var_tip[0]['empresa'];

						if(var_tip[0]['sigla_tbt']==="RG"){
							$("#ult_periodo").html('Ultimo periodo presentado: '+gg[1]);
							$("#ult_empresa").html('Empresa: '+gg[0]+' - '+var_tip[0]['empresa']);
						}else{
							$("#ult_periodo").html('Ultimo periodo pagado: '+gg[1]);
							$("#ult_empresa").html("");
						}
					}
					console.log(var_vgp[0]['plan_medico']);
					if(var_vgp[0]['id_plan_medico'] != 1){
						$('#gf_plan_medico').html("");
						$('#gf_plan_medico').html("Plan medico: " +"<b>"+ var_vgp[0]['plan_medico'] +"</b>");
					}else{
						$('#gf_plan_medico_wrap').hide();
					}
				//console.log('Termino GJ1');

				$("#cuil_titular").val(var_cct[0]['cuil_titular']);
				//console.log('Termino GJ2');

				VGP(var_vgp,id_af_consultado);

				if(var_dom[0]['estado']=="si"){
					$('#divPresentaDesempleo').css('display','block');
					$('#divPresentaDesempleo').html("<b>DESEMPLEO desde "+var_dom[0]['per_min']+" al "+var_dom[0]['per_max']+"</b>");
				}
				//console.log('Termino GJ3');

				$('.bd-example-modal-sm').modal('hide');//Termino, cierro modal
				
				$.when(
					$.getJSON(ajax_url,{ parametro: "domicilio_titular", id_titular: id_titular },function(datos){var_dtit = datos;})
					).then(function(){
						$('#inp_fm_id_domicilio').val(var_dtit[0]['id_domicilio_titular']);
					//console.log('Termino GJ4');
				});

					if(load_selects === true){
						CallSelects();
					}
				});	
			}
			function VerificarPermisos(){
				if(tipo_perfil =='consulta_externo'){
					$('#btnVerAportes').hide();
					$('#ult_periodo').hide();
				}
			}
			function CargarInfoCredencial(id_afiliado){
				$.ajax({
					url: ajax_url,
					type: 'GET',
					dataType: 'json',
					data: {parametro: 'info_credenciales',id_afiliado: id_afiliado},
				})
				.done(function(data) {
					$('#modalCredencialHistoricoDiv').html("");

					$('#modalCredencialHistoricoDiv').append("<h5>Movimientos historicos</h5>"
						+"<table class='table table-stripped' id='TablaCredenciales'>"
						+"<thead>"
						+"<tr>"
						+"<th class='w-30'>"
						+"F. Impresion"
						+"</th>"
						+"<th class='w-30'>"
						+"F. Vencimiento"
						+"</th>"
						+"<th class='w-40'>"
						+"Usuario"
						+"</th>"
						+"</tr>"
						+"</thead>"
						+"<tbody></tbody>"
						+"</table>");

					for(var i=0; i<=data.length-1 ;i++){
						let {fecha_vencimiento,fechador,usuario} = data[i];

						$('#TablaCredenciales').append("<tr>"
							+"<td>"
							+fechador
							+"</td>"
							+"<td>"
							+fecha_vencimiento
							+"</td>"
							+"<td>"
							+usuario
							+"</td>"
							+"</tr>");

					}
				});
			}
			function BuscarInfoAfip(cuil){
				$.ajax({
					url: 'http://replica.microteam.us/afip2.0/main.php',
					type:'get',
					dataType:'json',
					data: {foo: cuil}
				}).then(function(data){
					console.log(data);
					let {apellido,nombre,tipoPersona,codPostal,descripcionActividad,direccion,idActividad,idPersona,localidad} = data;
					alert(`${apellido}\n${nombre}\n${codPostal}\n${descripcionActividad}\n${direccion}\n${idActividad}\n${idPersona}\n${localidad}`);
				});
			}
			//Filter Select2
		function formatRepo (repo) {
			if (repo.loading) {
				return repo.text;
			}
			var $container = $(
				"<div class='select2-result-repository clearfix'>" +
				"<div class='select2-result-repository__meta'>" +
				"<div class='select2-result-repository__title'></div>" +
				"</div>" +
				"</div>"
				);
			$container.find(".select2-result-repository__title").html(repo.text);
			return $container;
		}
		function formatRepoSelection(repo) {
			// Esto permite que el valor seleccionado también muestre HTML
			return $('<span>').html(repo.text);
		}
		</script>
		<script type="text/javascript" src="js/db_selects.js?ts=<?echo rand();?>"></script>
	</body>
	</html>
