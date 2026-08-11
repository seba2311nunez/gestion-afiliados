<?php

// Dashboard 01/03/2022

include ("../Config/Conectar.inc");
include ("../Config/funciones.inc");

$id_institucion = $_SESSION["id_institucion"];
$id_pplan = $_SESSION["id_pplan"];

if ( $_SESSION["usu"] == "" )
{ echo "<h1>Problemas con el ingreso al sistema </h1></br>"; 
exit();
}
$usuario=$_SESSION["usu"];
$id_user=$_SESSION["iduser"];



?>
<HTML>
    <head>
    	
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <!-- Meta, title, CSS, favicons, etc. -->
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	    <title>Sistema de padron </title>
	
	    <!-- Bootstrap -->
	    <!-- <link href="http://93.188.164.97/dashboard_sistema/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	    <!-- Font Awesome -->	    
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	    <!-- NProgress -->
	    <!-- <link href="http://93.188.164.97/dashboard_sistema/vendors/nprogress/nprogress.css" rel="stylesheet"> -->
	   
    	<!-- Custom Theme Style -->
    	<link href="http://45.132.242.129/dashboard_sistema/build/css/custom.min.css" rel="stylesheet">
        <style>
        	body{
        		background-color: #F7F7F7;
        	}
        	.x_panel{
        		
			    width: 100%;			    
			    padding: 10px 17px;
			    display: inline-block;
			    background: #fff;
			    border: 1px solid #E6E9ED;
			    margin-left: 10px;
			    margin-top: 10px;
			    margin-bottom: 10px;
			    
        	}
        	.a_pendientes{
        		color: white;
        	}
			.dto_numero{
				text-align: right;
			}

        </style>
    </head>
    <BODY>    	
        <div class="container-fluid main">   
		    <div class="row col-md-12 col-lg-12">
		        <div class="row col-md-4 col-lg-4">	        	
			        <div class="x_panel">
			        	<h4>Datos de contacto &nbsp;&nbsp;<i class="fas fa-sign-out-alt"></i>&nbsp;<i class="fas fa-phone"></i>&nbsp;<i class="fas fa-at"></i></h4><hr>
			        	<div class="panel-body">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Cerrar Sesion de : </a> <?=$usuario;?>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <a href="#" class="btn btn-warning" onclick="cerrar_sesion('salir.php');"><i class="fas fa-sign-out-alt"></i> Click aqui para cerrar sesion</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Telefono de contacto - Whatsapp</a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            Sebastian Nuñez 11-6189-0816
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Emails</a>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <!-- - <b>Sistemas</b> &nbsp; sistemas@smadm.com<br> -->
                                            - <b>Luis Miedzinski</b> &nbsp; mluis@smadm.com<br>
                                            - <b>Sebastian Nuñez</b> &nbsp; sebastian@smadm.com
                                        </div>
                                    </div>
                                    
                                </div>
                                <br>                                								                                                                
                            </div>
                        </div>		        				        			  			
					</div>
		    	</div>   	
	    		
	    		<div class="row col-md-3 col-lg-3" style="margin-left: 10px;">
	    			<div class="x_panel">
	    				<h4>Ultimos accesos &nbsp;&nbsp;</h4>
	    				<hr>
	    				<b> Su ultimo acceso: <label id="ultimo_acceso"></label>	 </b>
	    				<hr>
	    				Accesos de hoy <br><br>
	    				<?echo ultimos_accesos($base_usuarios);?>	    				
	    			</div>
	    		</div>
	    		
	    		<div class="row col-md-5 col-lg-5" style="margin-left: 10px;">
	    			<div class="x_panel">
	    				<!--
	    				<h4><i class="fas fa-chart-bar"></i> Listado de pendientes</h4>
	    				<br>
		    			<ul class="list-group">							
						 	<li class="list-group-item"> Afiliados 
						 		<span class="badge"><a id="cant_aai" href="../APP/padron/ListadoMaestro.php" style="color: white;">1</a></span>
						 	</li>
						</ul>	
						-->		
						<h4><i class="fas fa-chart-bar"></i> Listados de Padron</h4>
						<div id="listados_padron"></div>			
					</div>
	    		</div>
	    		
	    	</div>

	    	<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    		<div class="col-md-4">
	    			<div class="x_panel">
		    			<h4><i class="fas fa-chart-bar"></i> Procesos masivos</h4>
		    			<hr>
		    			<ul class="list-group">							
						 	<li class="list-group-item"> Bajas por declaraciones juradas 
						 		<span class="badge"><a href="../APP/procesos_masivos/bajas_ddjj/index2.php" style="color: white;">Ir</a></span>
						 	</li>
						</ul>
		    		</div>	
	    		</div>
	    		
	    		<div class="col-md-4">
	    			<div class="x_panel">
		    			<h4><i class="fas fa-chart-bar"></i> Listados</h4>
		    			<hr>
		    			<ul class="list-group">							
						 	<li class="list-group-item btnListados" data-toggle="modal" data-target="#modalListados" data-tipo="bajas_rg_sss"> 
						 		Bajas regimen general ultimos periodos | Por desreguladora
						 	</li> 
						 	<li class="list-group-item btnListados" data-toggle="modal" data-target="#modalListados" data-tipo="opciones_rg_altas"> 
						 		Traspasos RG de alta (opciones no altas)
						 	</li>	
						 	
						</ul>
		    		</div>	
	    		</div>

	    		<!--
	    		<div class="col-md-8">
	    			<div class="x_panel">
		    			<h4><i class="fas fa-chart-bar"></i> Nuevo modulo de gestion de afiliados</h4>
		    			<hr>
		    			<div id="alerta_ingresar_ver_grupo_familiar" style="max-width: 700px; padding-left: 10px;">
							<div class="alert alert-warning">
							  <strong style="margin-right: 6px;">Importe !</strong> Probar nuevo trabajar con afiliados 
							  <br>
							  <a href="../APP/buscar_afiliado/">
							  	Haga click para acceder
							  </a>
							</div>
							
						</div>
		    		</div>	
	    		</div>
	    		-->
	    			
          	</div>   

	    	<!--

	    	<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">	    		
		    		<div class="col-md-4">
		    			<div class="x_panel ">	    					
	    					<div class="x_title">
			                  <h2>Por parentesco</h2>
			                  <ul class="nav navbar-right panel_toolbox">
			                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			                    </li>
			                    <li class="dropdown">
			                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
			                      <ul class="dropdown-menu" role="menu">
			                        <li><a href="#">Settings 1</a>
			                        </li>
			                        <li><a href="#">Settings 2</a>
			                        </li>
			                      </ul>
			                    </li>
			                    <li><a class="close-link"><i class="fa fa-times"></i></a>
			                    </li>
			                  </ul>
			                  <div class="clearfix"></div>
			                </div>
	    					<div class="x_content">
				    			<table class="table" id="tabPorParentesco" style="font-size: 13px;">
				    				<thead>
				    					<tr>
				    						<th>Parentesco</th>
				    						<th style='text-align: right;'>Cantidad</th>
				    					</tr>
				    				</thead>
				    				<tbody>
				    					
				    				</tbody>
				    				<tfoot>
				    					<tr>
				    						<th>Total</th>
				    						<td id="total_parentesco" style='text-align: right;'></td>
				    					</tr>
				    					<tr style="display: none;">
				    						<th>Promedio familiares</th>
				    						<td id="promedio_parentesco" style='text-align: right;'></td>
				    					</tr>
				    					
				    				</tfoot>
				    			</table>
				    		</div>	
			    		</div>
		    		</div>
		    		
		    		<div class="col-md-4">
		    			<div class="x_panel  ">
	    					<div class="x_title">
			                  <h2><i class="fas fa-chart-bar"></i> Altas vs Bajas</h2>
			                  <ul class="nav navbar-right panel_toolbox">
			                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			                    </li>
			                    <li class="dropdown">
			                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
			                      <ul class="dropdown-menu" role="menu">
			                        <li><a href="#">Settings 1</a>
			                        </li>
			                        <li><a href="#">Settings 2</a>
			                        </li>
			                      </ul>
			                    </li>
			                    <li><a class="close-link"><i class="fa fa-times"></i></a>
			                    </li>
			                  </ul>
			                  <div class="clearfix"></div>
			                </div>
			                <div class="x_content">			                	
			                	<table class="table" id="tabPeriodoAltasBajas" style="font-size: 11px;">
				    				<thead>
				    					<tr>
				    						<th>Periodo</th>				    						
				    						<th style='text-align: right;'>Altas</th>
				    						<th style='text-align: right;'>Bajas</th>
				    					</tr>
				    				</thead>
				    				<tbody>
				    					
				    				</tbody>
				    				<tfoot>
				    					<tr>
				    						<th>Total</th>			    						
				    						<td id="total_altas" style='text-align: right;' ></td>
				    						<td id="total_bajas" style='text-align: right;' ></td>
				    					</tr>			    					
				    				</tfoot>	
				    			</table>
			                </div>			    			
			    		</div>	  
		    		</div>
		    		
		    		<div class="col-md-4">
		    			<div class="x_panel  ">
	    					<div class="x_title">
			                  <h2><i class="fas fa-chart-bar"></i> Por capita</h2>
			                  <ul class="nav navbar-right panel_toolbox">
			                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			                    </li>
			                    <li class="dropdown">
			                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
			                      <ul class="dropdown-menu" role="menu">
			                        <li><a href="#">Settings 1</a>
			                        </li>
			                        <li><a href="#">Settings 2</a>
			                        </li>
			                      </ul>
			                    </li>
			                    <li><a class="close-link"><i class="fa fa-times"></i></a>
			                    </li>
			                  </ul>
			                  <div class="clearfix"></div>
			                </div>
			                <div class="x_content">			                	
			                	<table class="table" id="tabCapita" style="font-size: 11px;">
				    				<thead>
				    					<tr>
				    						<th>Capita</th>
				    						<th>Titulares</th>
				    						<th>Familiares</th>
				    						<th style='text-align: right;'>Cantidad</th>
				    					</tr>
				    				</thead>
				    				<tbody>
				    					
				    				</tbody>
				    				<tfoot>
				    					<tr>
				    						<th>Total</th>
				    						<td id="total_tit" style='text-align: right;' ></td>
				    						<td id="total_fam" style='text-align: right;' ></td>			    						
				    						<td id="total_capitas" style='text-align: right;' ></td>
				    					</tr>			    					
				    				</tfoot>	
				    			</table>
			                </div>			    			
			    		</div>	  
		    		</div>
	    		   		
          	</div> 
	    	
	    	<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    		<div class="col-md-4">
	    			<div class="x_panel">    					
    					<div class="x_title">
		                  <h2><i class="fas fa-chart-bar"></i> Por grupo etareo y sexo</h2>
		                  <ul class="nav navbar-right panel_toolbox">
		                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
		                    </li>
		                    <li class="dropdown">
		                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
		                      <ul class="dropdown-menu" role="menu">
		                        <li><a href="#">Settings 1</a>
		                        </li>
		                        <li><a href="#">Settings 2</a>
		                        </li>
		                      </ul>
		                    </li>
		                    <li><a class="close-link"><i class="fa fa-times"></i></a>
		                    </li>
		                  </ul>
		                  <div class="clearfix"></div>
		                </div>
    					<div class="x_content">
    						<table class="table" id="tabPorGeSexo" style="font-size: 11px;">
			    				<thead>
			    					<tr>
			    						<th>Grupo Etareo</th>
			    						<th style='text-align: right;'>Hombres</th>
			    						<th style='text-align: right;'>Mujeres</th>
			    						<th style='text-align: right;'>Total</th>
			    					</tr>
			    				</thead>
			    				<tbody>
			    					
			    				</tbody>		    				
			    			</table>
    					</div>		    			
		    		</div>	
	    		</div>	
          		
          		<div class="col-md-4">
		    			<div class="x_panel tile ">
	    					<div class="x_title">
			                  <h2><i class="fas fa-chart-bar"></i> Por tipo de aporte (Titulares)</h2>
			                  <ul class="nav navbar-right panel_toolbox">
			                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			                    </li>
			                    <li class="dropdown">
			                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
			                      <ul class="dropdown-menu" role="menu">
			                        <li><a href="#">Settings 1</a>
			                        </li>
			                        <li><a href="#">Settings 2</a>
			                        </li>
			                      </ul>
			                    </li>
			                    <li><a class="close-link"><i class="fa fa-times"></i></a>
			                    </li>
			                  </ul>
			                  <div class="clearfix"></div>
			                </div>
			                <div class="x_content">
			                	<table class="table" id="tabTipoAporte" style="font-size: 12px;">
				    				<thead>
				    					<tr>
				    						<th>Tipo beneficiario</th>
				    						<th style='text-align: right;'>Cantidad</th>
				    					</tr>
				    				</thead>
				    				<tbody>
				    					
				    				</tbody>
				    				<tfoot>
				    					<tr>
				    						<th>Total</th>			    						
				    						<td id="total_tipobeneficio" style='text-align: right;'></td>
				    					</tr>			    					
				    				</tfoot>	
				    			</table>
			                </div>			    			
			    		</div>
		    		</div>
          	
          	</div> 
	    	
          	-->

	    	<div class="row col-md-12 col-lg-12" style="display: none;">
	    		
	    		
	    	</div>	    
	        	    	
	    	<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
	    		    		
          	</div>   
          	       	    	
    	</div>
    	
    	
		
		<!-- Modal Listados -->
		<div id="modalListados" class="modal fade" role="dialog">
		  <div class="modal-dialog " style="width: 1100px;">
		
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title" id="divListadosTitulo">Modal Header</h4>
		      </div>
		      <div class="modal-body">
		        <div id="divListados">
		        	
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		      </div>
		    </div>
		
		  </div>
		</div>
    	
    	<!-- jQuery -->
	    <!-- <script src="http://45.132.242.129/dashboard_sistema/vendors/jquery/dist/jquery.min.js"></script> -->
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	    <!-- Bootstrap -->	    
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	    	   
	    <!-- Custom Theme Scripts -->
	    <script src="http://45.132.242.129/dashboard_sistema/build/js/custom.min.js"></script>
		
    	<script>
    		const DOMINIO = "<?echo DOMINIO;?>";
    		$(document).ready(function(){
    			
    			var url = `http://${DOMINIO}/ws/ws_padron.php`;

    			ListadosPadron();
    			
    			setTimeout(function(){ 
					$('[data-toggle="tooltip"]').tooltip(); 

					$('#alerta_ingresar_ver_grupo_familiar').fadeIn(600).delay(500).fadeOut(600, parpadear);
				}, 2000);
    			
    			var datos = {
					"parametro": "ultimo_acceso",
					"id_user": "<?=$id_user;?>",
					"sistema": "afiliaciones"
				};
				
				$.ajax({
				
					url: url,
					type: 'get',
					data: datos,
					success: function(data){						
						
						$('#ultimo_acceso').html(data)
					}
				})

				$('.btnListados').on('click',function(){
					
					var tipo = $(this).data("tipo");
					var titulo = $(this).html().trim();
					
					//console.log(titulo);
					switch(tipo){
						//code
						case 'bajas_rg_sss':
							
							genera_tabla_bajas_rg(titulo);
							llena_tabla_bajas_rg();

							break;
							
						case 'opciones_rg_altas':
							
							genera_tabla_traspasos_rg_alta(titulo);
							llena_tabla_traspasos_rg_alta();

							break;

					}
					
				})
    			
    		})

    		function ListadosPadron(){

    			$('#listados_padron').html("");

    			$.ajax({
    				url: 'ajax.php',
    				type: 'GET',
    				dataType: 'json',
    				data: {parametro: 'listados_padron'},
    			})
    			.done(function(data){

    				console.table(data);

    				$('#listados_padron').append('<ul class="list-group">');
    				for(var i=0; i<=data.length-1 ;i++){

    					$('#listados_padron').append(
    						'<li class="list-group-item"> '+data[i]['nombre']+''
						 		+'<span class="badge"><a data-id="'+data[i]['id']+'" href="../'+data[i]['url']+'" style="color: white;">Ir</a></span>'
						 	+'</li>'
    					);
    				}
    				$('#listados_padron').append("</ul>");
				});
    			
    		}
    		
			function parpadear(){ $('#alerta_ingresar_ver_grupo_familiar').fadeIn(600).delay(500).fadeOut(600, parpadear)  }


			//Bajas RG
			function genera_tabla_bajas_rg(titulo){

				$("#divListadosTitulo").html(titulo);

				$('#divListados').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Procesando');
				
				$('#divListados').html("<table id='tab_Bajas_rg' class='table'>"
												+"<thead>"
													+"<tr>"
														+"<th>#</th>"
														+"<th></th>"
														+"<th>Periodo</th>"														
														+"<th>Total bajas</th>"
														+"<th>Empadronadas</th>"
														+"<th>Assistencial</th>"
														+"<th>Scientis</th>"
														+"<th>RSM</th>"
														+"<th>PPLAN</th>"
														+"<th>PROPIOS</th>"
														+"<th>Otros</th>"
														
													+"</tr>"
												+"</thead>"
												+"<tbody>"					    		
												+"</tbody>"
											    +"</table>");

			


			}

			function llena_tabla_bajas_rg(){


				$.getJSON('ajax.php',
							{ parametro: "lst_bajas_rg_x_periodo_desreguladora" },						       				
							function(data){ 
								
								$("#tab_Bajas_rg tbody").html("");
								
								for(var i=0; i<=data.length-1 ;i++){
								
									$("#tab_Bajas_rg tbody").append("<tr>"																
																		+"<td>"+(i+1)+"</td>"
																		+"<td></td>"
																		// +"<td>"+data[i]['nombre_periodo']+"</td>"
																		+"<td>"+data[i]['periodo1']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['cant_registros']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['personas']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['assistencial']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['scientis']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['rsm']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['pplan']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['propios']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['otros']+"</td>"					      				
																	+"</tr>") ;		
								}	
							}//fin function data

				);//fin getjson

			}
    		
    		//Traaspasos de alta
    		function genera_tabla_traspasos_rg_alta(titulo){

				$("#divListadosTitulo").html(titulo);

				$('#divListados').html('<i class="fas fa-sync-alt fa-spin fa-2x"></i> Procesando');
				
				$('#divListados').html("<table id='tab_traspasos_rg_alta' class='table'>"
												+"<thead>"
													+"<tr>"
														+"<th>#</th>"
														+"<th></th>"
														+"<th>Periodo</th>"														
														+"<th>Propios</th>"
														+"<th>Assistencial</th>"
														+"<th>Scientis</th>"														
														+"<th>Sin asignar</th>"
														+"<th>RSM</th>"
														+"<th>RSM Sin asignar</th>"
														+"<th>Todos</th>"
													+"</tr>"
												+"</thead>"
												+"<tbody>"					    		
												+"</tbody>"
											    +"</table>");

			
				$("#tab_traspasos_rg_alta tbody").html("Cargando...");

			}
    		
    		function llena_tabla_traspasos_rg_alta(){


				$.getJSON('ajax.php',
							{ parametro: "traspasos_rg_alta" },						       				
							function(data){ 
								
								$("#tab_traspasos_rg_alta tbody").html("");
								
								for(var i=0; i<=data.length-1 ;i++){
									
									$("#tab_traspasos_rg_alta tbody").append("<tr>"																
																		+"<td>"+(i+1)+"</td>"
																		+"<td></td>"
																		// +"<td>"+data[i]['nombre_periodo']+"</td>"
																		+"<td>"+data[i]['fec_eleccion']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['propios']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['assistencial']+"</td>"	
																		+"<td class='dto_numero'>"+data[i]['scientis']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['sin_asignar']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['rsm']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['rsm_sin_asignar']+"</td>"
																		+"<td class='dto_numero'>"+data[i]['total']+"</td>"
																							      				
																	+"</tr>") ;		
								}	
							}//fin function data

				);//fin getjson

			}
    		
    		
			function cerrar_sesion(id){
				window.parent.location=id;
			}

			function cerrar_sesion_volver(id){
				window.parent.location=id;								
			}	

    	</script>
    	<!--
    	<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
		<script src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script> 
		-->
    </BODY>
</HTML>

<?

function ultimos_accesos($base_usuarios){
	
	/*<ul class="list-group">
						  <li class="list-group-item">Preventas Pendientes <span class="badge"><?echo ver_preventas_pendientes($usuario);?></span></li>
						  <li class="list-group-item">Modificaciones de callcenter para aprobar <span class="badge"><?echo mod_callcenter_aprobar();?></span></li> 
						  <li class="list-group-item">Afiliados en tramite para aprobar <span class="badge"><?echo afiliados_en_tramite_aprobar();?></span></li>					  
						  <li class="list-group-item">Referidos para revisar <span class="badge"><?echo referidos_para_revisar($usuario);?></span></li> 
						  <li class="list-group-item">Carnets solicitados pendientes <span class="badge"><?echo carnets_solicitados_pendientes($usuario);?></span></li> 
						  <li class="list-group-item">Preventas Cargadas por BAC <span class="badge"><?echo preventas_cargadas_por_BAC($usuario);?></span></li> 
						  <li class="list-group-item">Preventas Aprobadas por PPLAN (auditoria) <span class="badge"><?echo preventas_aprobadas_por_PPLAN($usuario);?></span></li>
						</ul>
	 */
	 
	 $sql="SELECT usuario,nombrecompleto,perfil,DATE_FORMAT(fecha_ult_acceso,'%d-%m-%Y %H:%i') AS fecha_ult_acceso
					FROM $base_usuarios._users 
					WHERE sistema='afiliaciones' 
						AND fecha_ult_acceso LIKE CONCAT(CURDATE(),' %') ";
						
	 $rs=mysql_query($sql) or die(mysql_error().$sql);
	 
	 $tabla="<table class='table' style='font-size: 12px;'>
	 			<thead>
	 				<tr class='warning'>
	 					<th>Usuario</th>
	 					<th>Fecha</th>
	 				</tr>
	 			</thead>
	 			<tbody>";
				
	while($d= mysql_fetch_object($rs) ){
		$tabla.="<tr>
					<td>$d->usuario</td>
					<td>$d->fecha_ult_acceso</td>
				 </tr>";
	}
	
	$tabla.="</tbody></table>";
	
	return $tabla;
}


?>
