<?php 
include('../../Config/Conectar.inc');
$usuario = $_SESSION['perfil'];

$p_cuil = $_GET['p_cuil'];
?>
<html>
	<head>
		<title>Consultas</title>
		<!-- Jquery -->
		  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		
		<!-- Bootstrap -->
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		
		<!-- Databatables -->
		<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		
		<!-- Estilos propios -->
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="col-md-12">
				<div class="x_panel">				
					<h2 class="tituloDiv">
						Consulta cobertura de afiliado				
					</h2>
					
					<!-- Div de consulta -->
					<div class="row" id="divParametros">
						<div class="container">						
							<hr>
							<!-- Parametros de busqueda -->					
							<div class="col-md-6">
								
								<p style="margin-left: 20px;">  						
									<b>La consulta es por cuil</b>, de no tenerlo complete el dni y el sexo, el cuil se calcula <br>
									automatico con ambos datos y luego puede realizar la consulta.
								</p>
								<br>
								<table class="table" style="width: 500px;">							
									<tr>
										<th>Dni</th>
										<td>
											<input type="number" name="dni" id="dni" />
										</td>								
									</tr>
									<tr>
										<th>Sexo</th>
										<td>
											<select name="sexo" id="sexo">
												<option value="M">Masculino</option>
												<option value="F">Femenino</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>Cuil</th>
										<td>
											<input type="text" name="cuil" id="cuil" placeholder="Acepto con o sin guiones" value="<?=$p_cuil;?>" />									
										</td>
									</tr>
								</table>	
								<br>						
								<a class="btn btn-success" id="btnConsultar" >
									<span id="spanEnviar"></span>Consultar
								</a>							
								
							</div>
							<!-- FIN - Parametros de busqueda -->
							
							<!-- Funciones extras -->
							<div class="col-md-5">
								
								<div class="container">
								  <label style="">Consultar por</label>	
								  <br>	  					  
			  					  <a href="perfil_empresa.php" class="btn btn-warning btn-sm" style="color: black; ">Empresa</a>  					  
			  					</div>	
			  					
			  					<div id="divMensaje" class="alert" style="width: 600px; margin: 15px;"></div>
			  					
							</div>						
							<!-- Fin - Funciones extras -->
							
						</div>					
					</div>
					<!-- FIN Div de consulta -->
					
					<div id="divCargando">
						<i class="fas fa-sync-alt fa-spin fa-2x"></i> Procesando
					</div>
					<hr>
					<!-- Div de Resultados -->
					<div class="row">
						<div id="divResultados" class="container" style="margin-top: 20px; padding: 10px; display: none;">
							<div id="divCapitaActual" style="max-width: 90%"></div>	
							<ul class="nav nav-tabs" style="width: 90%;">
							  <li class="active"><a data-toggle="tab" href="#home"><span id="item0"></span> DDJJ contra aportes </a></li>
							  <li><a data-toggle="tab" href="#menu1"><span id="item1"></span> Aportes </a></li>
							  <li><a data-toggle="tab" href="#menu7"><span id="item7"></span> Liquidaciones</a></li>	
							  <li><a data-toggle="tab" href="#menu4"><span id="item4"></span> Desempleo</a></li>
							  <li><a data-toggle="tab" href="#menu5"><span id="item5"></span> Jubilados</a></li>
							  <!--
							  <li><a data-toggle="tab" href="#menu5"><span id="item5"></span> Info fam MT y MS</a></li> -->
							  <!--<li><a data-toggle="tab" href="#menu6"><span id="item6"></span> Traspasos</a></li>-->
							  
							  <!--
							  <li><a data-toggle="tab" href="#menu2"><span id="item2"></span> Altas y bajas</a></li> 
							  <li><a data-toggle="tab" href="#menu3"><span id="item3"></span> Grupo familiar </a></li> -->							  							  
							  <!--
							  <li><a data-toggle="tab" href="#menu4">Consumos</a></li> -->
							</ul>
							
							<div class="tab-content" style="width: 90%;">
								<div id="home" class="tab-pane fade in active">
								  	<hr/>
								    	<table id="tabDDJJ" class="table" style="max-width: 800px;">
								    		<thead>
									    		<tr>
									    			<th>Periodo</th>
									    			<th>CUIT</th>
									    			<th>Ultima Rect.</th>
									    			<th>Remuneracion</th>
									    			<th>Descuento Calc. Total</th>
									    			<th>Aporte</th>
									    		</tr>
								    		</thead>
									    	<tbody>						    		
									    	</tbody>
								    	</table>
								    <hr/>
								</div>
							  <!-- Aportes -->
								<div id="menu1" class="tab-pane fade">						  	
							  	 	<hr />
									    <table id="tabAportes" class="table">
									    	<thead>
									    		<tr>
									    			<th>#</th>
									    			<th>Tipo beneficiario</th>
									    			<th>F. Acred.</th>
									    			<th>Periodo</th>
									    			<?php if($usuario=='admin'){ ?>
									    			<th>Aporte</th>	
									    			<?php }?>					    								    			
									    		</tr>
									    	</thead>
									    	<tbody>						    		
									    	</tbody>
									    </table>
								    <hr />
								</div>

								<!-- Liquidaciones -->
							  	<div id="menu7" class="tab-pane fade">
							  		<hr/>
							  		<table id="tabLiq" class="table">
							  			<thead>
							  				<tr>
							  					<th>Fuente</th>
							  					<th>Proceso</th>
							  					<th>Periodo</th>
							  					<th>Cuit</th>
							  					<th>Convenio</th>
							  					<th>Aporte</th>
							  					<th>Contribucion</th>
							  					<th>Total</th>
							  				</tr>
							  			</thead>
							  			<tbody></tbody>
							  		</table>
							  		<hr/>
							  	</div>


							  <!-- Desempleo -->
							 	<div id="menu4" class="tab-pane fade">						  	
							  		<hr/>
								    <table id="tabDesempleo" class="table">
								    	<thead>
								    		<tr>
								    			<th>Mes presentado</th>						    			
								    			<th>CUIL Titular</th>						    			
								    			<th>Fecha vigencia</th>
								    			<th>Fecha inicio</th>
								    			<th>Fecha fin</th>
								    			<th>Parentesco</th>			
								    			<th>CUIL familiar</th>
								    			<th>DNI</th>						    			
								    			<th>Apellido y nombre</th>
								    			<th>Fecha nacimiento</th>						    			
								    			<th>Sexo</th>
								    		</tr>
								    	</thead>
								    	<tbody>						    		
								    	</tbody>
								    </table>
							  	</div>
							  <!-- Jubilados -->
							  	<div id="menu5" class="tab-pane fade">						  	
							  		<hr />
							    	<table id="tabJub" class="table">
								    	<thead>
								    		<tr>
								    			<th>#</th>
								    			<th>Periodo</th>							    			
								    		</tr>
								    	</thead>
								    	<tbody>						    		
								    	</tbody>
							    	</table>
							  	</div>
							 
							  
							</div>
						</div>					
					</div>
					<!-- FIN Div de Resultados -->
									
				</div>
			</div>
			
		</div>
		<script src="../../Config/functions.js"></script>
		<script>
			$(function(){

				var perfil_usu = "<?php echo $usuario; ?>";

				//alert(perfil_usu);
				$('#divParametros').css('display','none');


				setTimeout(function(){ 
					
					if($('#cuil').val()!=""){
						$('#btnConsultar').click();
						
					}
					else{
						$('#divParametros').css('display','block');
						$('#divCargando').css('display','none');
					}

				}, 1000);

				$('#sexo').on('blur change',function(){
					
					var datos = { "parametro": "consulta_cuil", "dni": $('#dni').val(), "sex": $('#sexo').val() };
		       				
					jQuery.ajax({	
							data: datos,
							url: "ajax.php",
							type:"GET",
							success: function(data){
								
								$('#cuil').val(data);
								
							}
					});
					
				})
				
				
				$('#btnConsultar').on('click',function(){
					
					//console.log("Hola");
					var cuil_valido = $('#cuil').val();
					$('#divCargando').css('display','block');
					
					cuil_valido = cuil_valido.replace('-','');
					cuil_valido = cuil_valido.replace('-','');
					//console.log(cuil_valido)
					
					if(cuil_valido.length!=11){
						
						mostrarMensajeError("El CUIL debe tener 11 digitos", "error");
						return false;
						//console.log(); false;
					}
					
					var datos = {
						"parametro": "log_utilizacion",
						"cuil": cuil_valido
					};
					
					$.ajax({
					
						url: 'ajax.php',
						type: 'get',
						data: datos,
						success: function(data){						
							console.log(data);
						}
					})
					
					
					$('#item0').html(""); $('#item0').html('<i class="fas fa-sync-alt fa-spin"></i>');					
					$('#item1').html(""); $('#item1').html('<i class="fas fa-sync-alt fa-spin"></i>');	
					$('#item2').html(""); $('#item2').html('<i class="fas fa-sync-alt fa-spin"></i>');
					$('#item3').html(""); $('#item3').html('<i class="fas fa-sync-alt fa-spin"></i>');
					$('#item4').html(""); $('#item4').html('<i class="fas fa-sync-alt fa-spin"></i>');
					$('#item5').html(""); $('#item5').html('<i class="fas fa-sync-alt fa-spin"></i>');
					$('#item6').html(""); $('#item6').html('<i class="fas fa-sync-alt fa-spin"></i>');
					$('#item7').html(""); $('#item7').html('<i class="fas fa-sync-alt fa-spin"></i>');
					
					
					$('#tabDDJJ tbody').html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabGrupoFamiliar tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabAltasBajas tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabAportes tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>'); 
					$("#tabDesempleo tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabJub tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabFaHistorico tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$("#tabOpciones tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					
					$("#tabLiq tbody").html('<i class="fas fa-sync-alt fa-spin fa-2x "></i>');
					$('#divResultados').css('display','none');
					
					$(this).attr('disabled','disabled');
					$(this).html('');					
					$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Consultando');

					//$('input[name=submit]').click();
					
					
					//divCapitaActual
					$.getJSON('ajax.php',
								{ parametro: "consulta_capita", cuil: cuil_valido },						       				
								function(data0){ 

									console.table(data0);
									
									$("#divCapitaActual").html("");
									
									if(data0[0]['capita']==""){
										$('#divCapitaActual').html("<h3 style='background-color: red; color: white; padding: 10px;'>Desreguladora - <span>No informada - " +data0[0]['ayn']+"</span></h3><br>");
									}
									else{
										$('#divCapitaActual').html("<h3 style='background-color: red; color: white; padding: 10px;'>Desreguladora - <span>"+data0[0]['capita']+" - "+data0[0]['ayn']+"</span></h3><br>");
									}
																
									
									
								}//fin function data
					
					);//fin getjson
										
					//DDJJ contra aportes
					$.getJSON('ajax.php',
								{ parametro: "consulta_ddjj_contra_aportes", cuil: cuil_valido },						       				
								function(data1){ 
									
									$('#tabDDJJ tbody').html("");
									
									if(data1.length>0){
										
										$('#item0').html("<i class='fas fa-check-circle'></i>");

										for(var i=0; i<=data1.length-1 ;i++){
											let {periodo,cuit,ultima_rectificacion,remuneracion,descuento_total,aporte} = data1[i];
											
											tr_style = "";

											if(!cuit){
												tr_style = "style='background-color:orange'";
											}
											$("#tabDDJJ tbody").append(
												`<tr ${tr_style}>`
													+"<td  style='text-align: right;'>"+data1[i]['periodo']+"</td>"
													+"<td>"+data1[i]['cuit']+"</td>"
													+"<td>"+formatDate(data1[i]['ultima_rectificacion'])+"</td>"				
													+"<td  style='text-align: right;'>"+currencyFormat(data1[i]['remuneracion'])+"</td>"
													+"<td  style='text-align: right;'>"+currencyFormat(data1[i]['descuento_total'])+"</td>"
													+"<td  style='text-align: right;'>"+currencyFormat(data1[i]['aporte'])+"</td>"     				
												+"</tr>") ;		
										}	
									}
									else{
										$('#item0').html("");
										$("#tabDDJJ tbody").append("<tr><td colspan=5 align='center'>No hay registros de DDJJ</td></tr>");
									}
																
									
									
								}//fin function data
					
					);//fin getjson
					
					//Aportes
					$.getJSON('ajax.php',
								{ parametro: "consulta_aportes", cuil: cuil_valido },						       				
								function(data2){ 
									
									$("#tabAportes tbody").html('');
									
									if(data2.length>0){
										
										$('#item1').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data2.length-1 ;i++){
				
											if(perfil_usu =='admin'){

												$("#tabAportes tbody").append("<tr>"
																				+"<td>"+(i+1)+"</td>"
																				+"<td>"+data2[i]['tbt']+"</td>"
																				+"<td>"+data2[i]['f_acred']+"</td>"
																				+"<td>"+data2[i]['periodo']+"</td>"
																				+"<td align='right'>"+currencyFormat(data2[i]['aporte'])+"</td>"
																				+"</tr>");
											}
											else{
												$("#tabAportes tbody").append("<tr>"
																				+"<td>"+(i+1)+"</td>"
																				+"<td>"+data2[i]['tbt']+"</td>"
																				+"<td>"+data2[i]['f_acred']+"</td>"
																				+"<td>"+data2[i]['periodo']+"</td>"
																				
																				+"</tr>");
											}		

											
										}	
										
									}
									else{
										
										$('#item1').html("");
										$("#tabAportes tbody").append("<tr>"
																			+"<td colspan='4'></td>"															      				
																		+"</tr>") ;	
									}
																
									
									
								}//fin function data
					
					);//fin getjson


					//Liquidaciones
					$.getJSON('ajax.php',
								{ parametro: "consulta_liquidaciones", cuil: cuil_valido },						       				
								function(data8){ 
									
									console.table(data8);
									$("#tabLiq tbody").html("");
										
									if(data8.length>0){
																				
										$('#item7').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data8.length-1 ;i++){
											
											$("#tabLiq tbody").append("<tr>"
																			+"<td>"+data8[i]['fuente']+"</td>"																
																			+"<td>"+data8[i]['proceso']+"</td>"
																			+"<td>"+data8[i]['periodo']+"</td>"
																			+"<td>"+data8[i]['cuit']+"</td>"
																			+"<td>"+data8[i]['capita']+"</td>"
																			+"<td style='text-align: right;'>"+currencyFormat(data8[i]['aporte'])+"</td>"
																			+"<td style='text-align: right;'>"+currencyFormat(data8[i]['contri'])+"</td>"
																			+"<td style='text-align: right;'>"+currencyFormat(data8[i]['total'])+"</td>"  				
																		+"</tr>") ;		
										
										}
										
									}
									else{
										
										$('#item7').html("");
										$("#tabLiq tbody").append("<tr>"
																		+"<td colspan='10'>Sin datos</td>"															      				
																	+"</tr>") ;		
									}
										
										
									
									
								}//fin function data
					
					);//fin getjson

					
					//Altas y bajas
					/*
					$.getJSON('ajax.php',
								{ parametro: "consulta_altas_bajas", cuil: cuil_valido },						       				
								function(data3){ 
									
									$("#tabAltasBajas tbody").html('');
									
									if(data3.length>0){
										
										$('#item2').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data3.length-1 ;i++){
				
											$("#tabAltasBajas tbody").append("<tr>"
																				+"<td>"+(i+1)+"</td>"
																				+"<td>"+data3[i]['movimiento']+"</td>"
																				+"<td>"+data3[i]['nro_formulario']+"</td>"	
																				+"<td>"+data3[i]['fecha_aPartir']+"</td>"	
																				+"<td>"+data3[i]['fec_eleccion']+"</td>"
																				+"<td>"+data3[i]['observacion']+"</td>"	
																			+"</tr>") ;		
										}	
										
									}
									else{
										$('#item2').html("");
									}
																
									
									
								}//fin function data
					
					);//fin getjson
					*/

					/*
					//Grupo familiar
					$.getJSON('ajax.php',
								{ parametro: "consulta_grupoFamiliar_padron_sss", cuil: cuil_valido },						       				
								function(data4){ 
									
									$("#tabGrupoFamiliar tbody").html("");
									
									if(data4.length>0){
										
										$('#item3').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data4.length-1 ;i++){
				
											$("#tabGrupoFamiliar tbody").append("<tr>"
																			+"<td>"+data4[i]['ayn']+"</td>"																
																			+"<td>"+data4[i]['parentesco']+"</td>"
																			+"<td>"+data4[i]['nd']+"</td>"
																			+"<td>"+data4[i]['sexo']+"</td>"
																			+"<td>"+data4[i]['fn']+"</td>"
																			+"<td>"+data4[i]['incapacidad']+"</td>"
																			+"<td>"+data4[i]['localidad']+"</td>"
																			+"<td>"+data4[i]['cp']+"</td>"
																			+"<td>"+data4[i]['f_alta']+"</td>"
																			+"<td>"+data4[i]['periodo_desde_opcion']+"</td>"							      				
																		+"</tr>") ;	
										}
									
									}	
									else{
										$('#item3').html("");
									}
									
								}//fin function data
					
					);//fin getjson
					*/
					
					//Desempleo
					$.getJSON('ajax.php',
								{ parametro: "consulta_desempleo", cuil: cuil_valido },						       				
								function(data5){ 
									
									$("#tabDesempleo tbody").html("");
									
									if(data5.length>0){
										
										$('#item4').html("<i class='fas fa-check-circle'></i>");
											
										for(var i=0; i<=data5.length-1 ;i++){
				
											$("#tabDesempleo tbody").append("<tr>"
																				+"<td>"+data5[i]['mes_vigencia']+"</td>"																
																				+"<td>"+data5[i]['cuil_titular']+"</td>"
																				+"<td>"+data5[i]['f_vigencia']+"</td>"
																				+"<td>"+data5[i]['fec_ini']+"</td>"
																				+"<td>"+data5[i]['fec_fin']+"</td>"																			
																				+"<td>"+data5[i]['parentesco']+"</td>"
																				+"<td>"+data5[i]['cuil']+"</td>"
																				+"<td>"+data5[i]['dni']+"</td>"
																				+"<td>"+data5[i]['ayn']+"</td>"
																				+"<td>"+data5[i]['fn']+"</td>"
																				+"<td>"+data5[i]['sexo']+"</td>"							      				
																			+"</tr>") ;		
										}	
										
									}
									else{
										$('#item4').html("");
									}
																
									
									
									/*
									$("#tabDesempleo").dataTable({			    	
											"bPaginate": true,
											"iDisplayLength": 1000,
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
									*/
									
								}//fin function data
					
					);//fin getjson
					
					//Info fa_110404
					
					$.getJSON('ajax.php',
								{ parametro: "consulta_jubilados", cuil: cuil_valido },						       				
								function(data6){ 
									
									$("#tabJub tbody").html("");
										
									if(data6.length>0){
																				
										$('#item5').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data6.length-1 ;i++){
											
											$("#tabJub tbody").append("<tr>"
																			+"<td>"+(i+1)+"</td>"		
																			+"<td>"+data6[i]['periodo']+"</td>"																      				
																		+"</tr>") ;		
										
										}
										
									}
									else{
										
										$('#item5').html("");
										$("#tabJub tbody").append("<tr>"
																		+"<td colspan='2'></td>"															      				
																	+"</tr>") ;		
									}
										
										
									
									
								}//fin function data
					
					);//fin getjson
										
					
					/*
					$.getJSON('ajax.php',
								{ parametro: "fa_x_periodo" },						       				
								function(data8){ 
									
									$("#tabFaHistorico tbody").html("");
										
									if(data8.length>0){
										
										for(var i=0; i<=data8.length-1 ;i++){
											
											$("#tabFaHistorico tbody").append("<tr>"
																				+"<td>"+data8[i]['periodo']+"</td>"																
																				+"<td>"+data8[i]['cantidad']+"</td>"																																											      				
																			+"</tr>") ;		
										
										}
										
									}
									else{
										
										$("#tabFaHistorico tbody").append("<tr>"
																		+"<td colspan='2'></td>"															      				
																	+"</tr>") ;		
									}
										
										
									
									
								}//fin function data
					
					);//fin getjson
					*/
					
					//Opciones
					$.getJSON('ajax.php',
								{ parametro: "consulta_opciones", cuil: cuil_valido },						       				
								function(data7){ 
									
									$("#tabOpciones tbody").html("");
										
									if(data7.length>0){
																				
										$('#item6').html("<i class='fas fa-check-circle'></i>");
										
										for(var i=0; i<=data7.length-1 ;i++){
											
											$("#tabOpciones tbody").append("<tr>"
																			+"<td>"+data7[i]['deleg_nombre']+"</td>"																
																			+"<td>"+data7[i]['nro_formulario']+"</td>"
																			+"<td>"+data7[i]['regimen']+"</td>"
																			+"<td>"+data7[i]['ayn']+"</td>"
																			+"<td>"+data7[i]['sexo']+"</td>"
																			+"<td>"+data7[i]['fn']+"</td>"																		
																			+"<td>"+data7[i]['os_procedencia']+"</td>"
																			+"<td>"+data7[i]['fec_eleccion']+"</td>"		
																			+"<td>"+data7[i]['fec_entrega']+"</td>"		
																			+"<td>"+data7[i]['desreguladora']+"</td>"																										      				
																		+"</tr>") ;		
										
										}
										
									}
									else{
										
										$('#item6').html("");
										$("#tabOpciones tbody").append("<tr>"
																		+"<td colspan='10'>Sin datos</td>"															      				
																	+"</tr>") ;		
									}
										
										
									
									
								}//fin function data
					
					);//fin getjson

					
					
					setTimeout(function(){ 
					
						$('#btnConsultar').removeAttr('disabled');
						$('#btnConsultar').html("<span id='spanEnviar'></span>Consultar"); 
						
						$('#divCargando').css('display','none');
						$('#divResultados').css('display','block');
						
					}, 2000);
					
					
					
				})
				
			})
			
			function mostrarMensajeError(mensaje, estado){
				
				if(estado=="ok"){
					$('#divMensaje').removeClass('alert-danger')
					$('#divMensaje').addClass('alert-success')					
				}else{
					$('#divMensaje').removeClass('alert-success')
					$('#divMensaje').addClass('alert-danger')
				}
				
				$('#divMensaje').html(mensaje);
				$("#divMensaje").fadeIn("slow");				
				
				setTimeout(function(){ 
					$("#divMensaje").fadeOut("slow");		
					$('#divMensaje').html('');								
				}, 4000);
			}
			
			
			
		</script>
	</body>
</html>