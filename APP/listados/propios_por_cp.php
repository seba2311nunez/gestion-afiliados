<?

include("../../Config/Conectar.inc");
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
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
		<div class="container-fluid">
			
			<div class="col-md-10">
				<div class="x_panel">					
					<div class="row">
						<?if(INST_ID==4){?>
						<h2 style="margin-left: 15px;">Padron <?php echo strtoupper(INST_NAME);?>-PROPIOS por codigo postal</h2>
						<hr>
						<div style="padding: 30px; width: 700px;">
							<ul class="list-group">
							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=imagen_zona_norte_xls" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Imagen Zona Norte
							  	</a>		
							  	<span class="badge badge-primary badge-pill sp_img_zn" >0</span>	
							  						  						  		
							  </li>
							  <li class="list-group-item" >
							  	<a href="#"  style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Imagen Zona Norte + DDJJ 
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_img_zn_ddjj" >0</span>
							  	<span class="badge badge-primary badge-pill sp_img_zn" >0</span>
							  							  	
							  </li>
							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=imagen_resto_pais_xls" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Imagen Resto Pais	
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_img_rp" >0</span>						  								  	
							  </li>
							  <li class="list-group-item d-flex justify-content-between align-items-center">
							  	<a href="#"  style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Imagen Resto Pais + DDJJ
							  	</a>	
							  	<span class="badge badge-primary badge-pill sp_img_rp_ddjj" >0</span>						  	
							  	<span class="badge badge-primary badge-pill sp_img_rp" >0</span>	
							  	
							  </li>

							  <!-- Ambulancias  -->
							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=amb_semzar" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; SEMZAR ( Zarate - Campana ) | cp 2800 al 2814	
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_semzar" >0</span>						  								  	
							  </li>

							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=amb_emergencias_regionales" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Emergencias regionales | cp 2126 hasta 2128 	
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_emer_reg" >0</span>						  								  	
							  </li>

							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=amb_serva" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; SERVA y Ayuda Medica (Capital y Gba) | cp 0000 a 1980
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_serva" >0</span>						  								  	
							  </li>

							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=amb_cem" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; CEM ( san pedro - san nicolas - villa constitucion) | cp 2900 a 2930
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_cem" >0</span>						  								  	
							  </li>

							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=amb_emerger" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; EMERGER Ambulancias | cp 2000 a 3016
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_emerger" >0</span>						  								  	
							  </li>
							  <li class="list-group-item">
							  	<a href="ajax.php?parametro=ambulancias_xls&tipo=siaco" target="_blank" style="color: black;">
							  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; SIACO 
							  	</a>
							  	<span class="badge badge-primary badge-pill sp_siaco" >0</span>						  								  	
							  </li>


							  <li class="list-group-item">
							  	<b>Otros listados de padron</b>  								  	
							  </li>

								<li class="list-group-item">
									<a href="../../APP/listados/ajax.php?parametro=cursantes_xls" target="bottomFrame" style="color: black;">
										<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp;Listar Cursantes
									</a>
								</li>
								<li class="list-group-item">
									<a href="../../APP/listados/ajax.php?parametro=credenciales_xls" target="bottomFrame" style="color: black;">
										<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp;Listar Credenciales
									</a>
								</li>
								<li class="list-group-item">
									<a href="../../APP/listados/ajax.php?parametro=empresas_sin_dato_xls" target="bottomFrame" style="color: black;">
										<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp;Listar Empresas S/D
									</a>
								</li>
								<li class="list-group-item">
									<a href="../../APP/listados/ajax.php?parametro=monot_serdom_propios" target="bottomFrame" style="color: black;">
										<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp;Listar Monot/Ser. Dom. Propios
									</a>
								</li>
								<li class="list-group-item">
									<a href="../../APP/listados/ajax.php?parametro=jubilados_propios" target="bottomFrame" style="color: black;">
										<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp;Listar Jubilados. Propios
									</a>
								</li>
							</ul>
						</div>
						<?}?>
						<?if(INST_ID==2){?>
							<h2 style="margin-left: 15px;">Padron <?php echo strtoupper(INST_NAME);?>-Exportaciones</h2>
						  <li class="list-group-item">
						  	<a href="ajax.php?parametro=visitar_basico" target="_blank" style="color: black;">
						  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Visitar Basico 
						  	</a>					  								  	
						  </li>
						  <li class="list-group-item">
						  	<a href="ajax.php?parametro=visitar_plata" target="_blank" style="color: black;">
						  		<span class="glyphicon glyphicon-save" style="color: green;"></span>&nbsp; Visitar Plata 
						  	</a>					  								  	
						  </li>
						<?}?>
						<div>
							<button  class="btn btn-danger" id="btnRegenerar">Regenerar padron</button>
							<div id="estadoProceso" style="margin-top:8px; font-size:0.95rem;"></div>
						</div>
					</div>
				</div>				
			</div>
		</div>
		<script>
			$(function(){
				const $btn = $("#btnRegenerar");
				const $estado = $("#estadoProceso");
				let pollTimer = null;

				//Funciones
				function setRunningUI(startedAtText){
					$btn.prop("disabled", true).text("Procesando...");
					$estado.text(startedAtText ? ("Proceso iniciado: " + startedAtText) : "Proceso en ejecución...");
				}

				function setIdleUI(){
					$btn.prop("disabled", false).text("Regenerar padrón");
					$estado.text("");
					if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
				}

				function pollStatus(){
					$.ajax({
					  url: 'ajax.php',
					  type: 'get',
					  dataType: 'json',
					  data: { parametro: 'status_regenerar' },
					  success: function(resp){
					    if (!resp) return;
					    if (resp.running){
					      setRunningUI(resp.started_at ? `Inicio: ${resp.started_at}` : null);
					    } else {
					      setIdleUI();
					    }
					  }
					});
				}

				// Al cargar
				pollStatus();
				pollTimer = setInterval(pollStatus, 5000);

				$btn.on('click', function(){
				setRunningUI("iniciando...");
					$.ajax({
					  url: 'ajax.php',
					  type: 'get',
					  dataType: 'json',
					  data: { parametro: 'regenerar' },
					  success: function(resp){
					    if (resp && resp.status === "started"){
					      $estado.text(`Proceso iniciado: ${resp.started_at || ''}`);
					    } else if (resp && resp.status === "running"){
					      setRunningUI(resp.started_at ? `Inicio: ${resp.started_at}` : null);
					    } else if (resp && resp.status === "error"){
					      alert(resp.message || "Error al iniciar el proceso.");
					      setIdleUI();
					    }
					  },
					  error: function(){
					    alert("Error al iniciar el proceso.");
					    setIdleUI();
					  }
					});
				});

				$("#sp_img_zn").html("Cargando...");
				$("#sp_img_rp").html("Cargando...");
				
				$.getJSON('ajax.php',
							{ parametro: "imagen_cantidades" },						       				
							function(data){ 
								console.log(data);
								$(".sp_img_zn").html(data[0]['zn']);
								$(".sp_img_rp").html(data[0]['rp']);

								$(".sp_img_zn_ddjj").html(data[0]['zn_ddjj']);
								$(".sp_img_rp_ddjj").html(data[0]['rp_ddjj']);

								$(".sp_emerger").html(data[0]['emerger_amb']);
								
								
							}//fin function data

				);//fin getjson




			})
		</script>
	</body>
</html>