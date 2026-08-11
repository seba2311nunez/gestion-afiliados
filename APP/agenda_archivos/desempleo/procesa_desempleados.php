<?php
include(__DIR__.'/../../../Config/Conectar.inc');
$usuario = $_SESSION['usuario'];
$id_user = $_SESSION['id_user'];
if($btnEnviar=="Enviar"){
	
	//echo "hola";
	
	echo "<!-- Bootstrap -->
				<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css' rel='stylesheet'>
				<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>";
		$ext = $_FILES['archivo']['name']; 
		$ext=explode('.',$ext);
		$descripcion = $ext[0]."_".$periodo;
		$tipo= $ext[1]; //EXTENSION DEL ARCHIVO
		$descripcion = $ext[0]."_".$periodo;
		if($ext[0]!="desempleo" && $ext[0]!="Desempleo"){
			
			echo "<div class='container' style='margin: 20px;'>
					<div class='alert alert-danger'>
					No es un archivo de desempleo  $ext[0]
					<br><br> Formato es Desempleo.txt
				  </div>";
				  exit();
		}
		$directorio_temporal = sys_get_temp_dir(); // Obtiene el directorio temporal del sistema

		if ($directorio_temporal) {
	    $nombre_archivo_temporal = tempnam($directorio_temporal, 'archivo_temporal_');
	    if ($nombre_archivo_temporal) {
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $nombre_archivo_temporal)) {
	        $gestor = fopen($nombre_archivo_temporal, "r");
					$konta=0 ;
			
					while ($input = fgets($gestor, 250)) {
						
						$input= ereg_replace( "'", " ", $input );
						
						if($input==""){
							
						}
						else{
							$konta++;
							insertar($konta,$input,$descripcion,$periodo);
							
						}
						
					}
					
					$id_lote = graba_lote_y_cierra($konta,$periodo,$descripcion,$gestor,$usuario);
					
					$q_titulares = informa_historico_titulares($id_lote, $periodo, $id_user); 
					$q_familiares = informa_historico_familiares($id_lote, $periodo, $id_user);
					
					echo "<div class='container' style='margin-top: 50px'>
							<div class='alert alert-success'>
								Archivo subido correctamente
								<br>
								<br><b>Lote # $id_lote ($konta registros)</b>
								<br>
								<br> - Titulares: $q_titulares
								<br> - Familiares: $q_familiares 
							</div>
							<br>
							<a class='btn btn-info' href='procesa_desempleados.php'>
								Volver 
							</a>
						  </div>"; 
					fclose($gestor); // No olvides cerrar el archivo después de su uso

	        // Finalmente, puedes eliminar el archivo temporal si ya no lo necesitas
	        unlink($nombre_archivo_temporal);
		    } else {
	        echo "Error moviendo el archivo al directorio temporal.";
		    }
			} else {
		    echo "No se pudo crear un archivo temporal.";
			}
		} else {
			echo "El servidor no tiene configurado un directorio temporal.";
		}
		exit();
}




function graba_lote_y_cierra($konta,$archivo,$periodo,$descripcion,$usuario){

	$inserta="INSERT INTO ".N_BASE_HISTORICOS.".lotes (lote,descripcion,archivo,cant_registros,fechador,proceso,clave_agenda,usuario,id_usuario) 
					VALUES ('$descripcion','$descripcion','$archivo','$konta',now(),'desempleo','$periodo','$usuario',1)"; 
	
	mysql_query($inserta) or die(mysql_error()."<br>".$inserta);
	$id_lote = mysql_insert_id();
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".desempleo 
					SET id_lote = '$id_lote' 
					WHERE id_lote=0") or die(mysql_error()."UPDATE desempleo SET id_lote=$id_lote WHERE id_lote=0");
	
	mysql_query("UPDATE ".N_BASE_HISTORICOS.".agenda 
					SET procesado=1
					WHERE clave='$periodo' ") or die(mysql_error()."Error marcando agenda/procesado");

	mysql_query("CALL ".N_BASE_HISTORICOS.".DES_proceso_completo($id_lote)") or die(mysql_error());
	return $id_lote;
		
	
}

function insertar($konta,$input,$archivo,$lote){
	//https://www.sssalud.gob.ar/descargas/rnos/publico/ftp/disenoDesempleo.pdf 
	
	list($a, $b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p)=explode("|",$input);
		
	$sql="INSERT INTO ".N_BASE_HISTORICOS.".desempleo (clave,marca_fin_pago,gp,td,nd,provincia,cuil,fn,ayn,fec_vigencia,sexo,fec_inicio_rel,fec_cese,os,fecha_proceso,cuil_titular)
			VALUES ('$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k','$l','$m','$n','$o','$p')";
	
	mysql_query($sql) or die(mysql_error().$sql);
}

function informa_historico_titulares($id_lote, $periodo, $id_user){
	
	/////////////////////////////////////////////////////////////////////////////
	/*Registro el movimiento en el historico afiliados*/
	
	$tabla = "desempleo_".str_replace("-", "", $periodo);
	
	$create="DROP TEMPORARY TABLE IF EXISTS ".N_BASE_PADRON.".$tabla";
	mysql_query($create) or die (mysql_error()." ".$create);
	
	$sq="CREATE TEMPORARY TABLE ".N_BASE_PADRON.".$tabla
					SELECT DISTINCT cuil_titular
					FROM ".N_BASE_HISTORICOS.".`desempleo`
					WHERE id_lote=$id_lote  ";
	mysql_query($sq) or die ( mysql_error()." ".$sq);
					
	$alt="ALTER TABLE ".N_BASE_PADRON.".$tabla ADD INDEX (cuil_titular)";

	mysql_query($alt) or die ( mysql_error()." ".$alt );

	$alt2="ALTER TABLE ".N_BASE_PADRON.".$tabla CONVERT TO CHARACTER SET latin1";

	mysql_query($alt2) or die (mysql_error()." ".$alt2);
	
	$sql_afiliados = "SELECT a.id AS id_afiliado,d.cuil_titular
							FROM ".N_BASE_PADRON.".$tabla d
							JOIN ".N_BASE_PADRON.".persona p ON d.cuil_titular=p.cuil COLLATE latin1_swedish_ci 
							JOIN ".N_BASE_PADRON.".afiliados a ON p.id=a.id_persona 
							
							WHERE a.id_titular=0 ";
	
	$rs_afiliados = mysql_query($sql_afiliados) or die(mysql_error()." ".$sql_afiliados);
	
	$n=1;
	
	while($d=mysql_fetch_object($rs_afiliados)){
		
		if($d->id_afiliado!=null){
			$update_afiliado = "UPDATE ".N_BASE_PADRON.".afiliados a									
									SET a.id_tipo_aporte=8
									WHERE a.id='$d->id_afiliado'
										AND a.id_titular=0";
										
			mysql_query($update_afiliado) or die(mysql_error().$update_afiliado);
			$n++;	
		}
	}
	
	return $n;
}// FIN informa_historico_titulares
	
function informa_historico_familiares($id_lote, $periodo, $id_user){
	/*Registro el movimiento en el historico afiliados*/
	$sql_afiliados = "SELECT a.id AS id_afiliado,d.nd,d.fec_vigencia
							FROM ".N_BASE_HISTORICOS.".desempleo d
							LEFT JOIN ".N_BASE_PADRON.".persona p ON d.nd=p.nd 
							LEFT JOIN ".N_BASE_PADRON.".afiliados a ON p.id=a.id_persona AND a.id_titular!=0
							WHERE d.id_lote=$id_lote ";
	
	$rs_afiliados = mysql_query($sql_afiliados) or die(mysql_error()." ".$sql_afiliados);
	
	$n=1;
	
	while($d=mysql_fetch_object($rs_afiliados)){
		
		if($d->id_afiliado!=null){
			$update_afiliado = "UPDATE ".N_BASE_PADRON.".afiliados a
									JOIN ".N_BASE_PADRON.".persona p ON a.id_persona=p.id 
									
									SET a.id_tipo_aporte=8 
									
									WHERE p.nd='$d->nd'
										AND a.id_titular!=0";
										
			mysql_query($update_afiliado) or die(mysql_error().$update_afiliado);
			
			$n++;
			
		}
		else{
					
			//Dar de alta al beneficiario 	
			
		}
		
		
		
	}


		
	return $n;
	
}// FIN informa_historico_familiares

?>

<html>
	<head>
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
		<script src='http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js'></script>
	</head>

	<body bgcolor="#FFFFFF" text="#000000">
		<div class="container">
			<!-- ------------------------------------------------ -->
			<div class="x_panel">
				<div class="tituloDiv">
					Cargando archivos de <b>DESEMPLEADOS</b> 	
				</div>
				<div class="row" style="margin: 10px;">
					<hr>
					<div style="width: 600px;">						
						<form method="post" name="form1" id="form1" action="procesa_desempleados.php" enctype="multipart/form-data">	
							
							<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
							<table class="table">
								<tr>
									<td>
										<input type="file" name="archivo" id="archivo" required>
									</td>											
								</tr>
								<tr>
									<td>
										<select id="periodo" name="periodo" required>
											<option value="">Seleccione Periodo</option>
											<?
														
											$sql="SELECT primer_dia,periodo1 
														FROM prueba.periodos 
														WHERE periodo2<=prueba.periodo_actual() 
															OR DATE_ADD(CURDATE(), INTERVAL 1 MONTH) BETWEEN primer_dia AND ultimo_dia
														ORDER BY id DESC  
														LIMIT 12";
														
											$rs=mysql_query($sql) or die(mysql_error().$sql);
											while($d=mysql_fetch_object($rs)){

												if($periodo==$d->primer_dia){

													echo "<option value='$d->primer_dia' selected>$d->periodo1</option>";	
												}
												else{
													echo "<option value='$d->primer_dia'>$d->periodo1</option>";
												}
												
												
											}
											?>
										</select>
									</td>
									
								</tr>
								<tr>
									<td>
										<p style="color: red; font-size: 12px;">El archivo a importar debe ser el txt no el zip</p>
									</td>
								</tr>
							</table> 							
							<hr>
							<input type="submit" name="btnEnviar" value = "Enviar" style="display: none;">
							<a id="btnEnviar" class="btn btn-success"  onclick="javascript:return confirm('Seguro ?')">
								<span id="spanEnviar"></span>  Procesar 
							</a>
						</form>
					</div>					
				</div>
			</div>
		</div>		
	
		<script>
			$(function(){
				
				$('#btnEnviar').on('click',function(){
					
					$(this).attr('disabled','disabled');
					$('#btnEnviar').html('');					
					$('#btnEnviar').html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
					$('input[name=btnEnviar]').click();
				})
				
				/*
				$("#tabListado").dataTable({			    	
											"bPaginate": true,
											"iDisplayLength": 100,
											"bLengthChange": false,
											"bFilter": true,
											"bSort": true,
											"bInfo": false,											
											"bAutoWidth": false,
											"oLanguage": {
											    "sSearch": "Buscar",
											}
				});
				*/
				
			})
		</script>
		
	</body>
</html>
