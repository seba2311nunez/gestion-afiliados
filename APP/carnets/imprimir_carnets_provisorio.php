<?
include("../../Config/Conectar.inc");
include("../../Config/funciones.inc");
//include("../../Lib/fpdf.php");

for($i=1; $i <= $renglones; $i++){
	
	$var="imprime$i";	$actual=$$var;	
	if($actual!='' or $actual!= null){
		$actual=split("@", $actual);
		mysql_query("update $base_ppdev.carnets_tmp set imprime='SI' where nd=$actual[0]");
	$sql="update afiliados set tiene_credencial='S',vencimiento_credencial='$actual[1]' where id=$actual[2]";
	if(!mysql_query($sql)){
	echo "ERROR EN LA GRABACION";
	exit();	
	}
	
	$sql="INSERT INTO $base_ppdev.carnets_emitidos (id_institucion,usuario,fecha_vencimiento,id_afiliado)
			values('$id_institucion','$usuario','$actual[1]','$actual[2]')";
	if(!mysql_query($sql)){
	echo "ERROR EN LA GRABACION m";
	exit();	
	}
}
}

$hoy=DATE("d/m/Y");$hora=DATE("h:m");

function get_cuil_titular($dni){
	
	$sql="SELECT cuil as cuil_titular
			FROM ".N_BASE_PADRON.".persona 
				WHERE nd=$dni";
	
	$rs=mysql_query($sql);
	$d=mysql_fetch_object($rs);
	
	return $d->cuil_titular ;
}


$cuil_titular = get_cuil_titular($dni) ;

?>

<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		
		<!-- Jquery -->
		<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
		
		<!-- Bootstrap -->
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		
		<!-- Iconos -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
		
		<!-- Databatables -->
		<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		
		<!-- Estilos propios 
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
		<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>
		-->
		
		<style>
			.cuadro_bordes{
				border: solid black 2px;
				margin-top: 10px;
				margin-bottom: 10px;
				padding: 15px;
			}
		</style>
		
	</head>
	<body>
		<div >
			
			<div class="row">
					<div style="text-align: center; /*float: left; width: 300px;*/">
						<img src="<?echo INST_NAME;?>_logo_ofi.png" style="height: 150px;" />
					</div>
					<!-- <div  style="/*float: left;*/  width: 900px;    text-align: center;">
						
						<label style="font-size: 18px; margin-top: 30px; text-align: center;">Formulario de actualizacion de datos</label>
						
						
					</div> -->
				</div>
			
			<div class="col-md-12">
				
				
				<div class="pull-right">
					<label>
						<?=date("d/m/Y H:i");?>
					</label>							
				</div>
				<div style="text-align: center;">
					<h4>Formulario de actualizacion de datos | Constancia para atencion</h4>					
				</div>

				<div class="cuadro_bordes" style="text-align: center;">
					<label style="font-size: 18px;">AFILIADO TITULAR</label> 
				</div>
				
				<div class="cuadro_bordes">
					<?
						$sql_titular="SELECT a.id,a.id_titular,
												CONCAT(nben,'/',gpar) AS nro_afiliado,
												CONCAT(apellido,' ',nombre) AS ayn,
												DATE_FORMAT(fn,'%d/%m/%Y') AS fn,
												DATE_FORMAT(estado_afiliado_ultima_alta(a.id,CURDATE()),'%d/%m/%Y')  AS fecha_alta,
												CONCAT(d.calle,'',d.numero) AS domicilio,
												l.nombreLoca AS localidad,
												l.cp,
												p.`telef_celular`,
												p.td,p.nd,tbt.sigla AS tbt,tbt.beneficiario AS tbt_denominacion	
												
											FROM $base_padron.persona p
											JOIN $base_padron.afiliados a ON p.id=a.id_persona 
											JOIN $base_padron.tipo_beneficiario_titular tbt ON tbt.id=a.id_tipo_aporte
											JOIN $base_padron.domicilio d ON p.`id_domicilio`=d.id 
											JOIN $base_padron.localidad l ON d.id_localidad=l.id 
											WHERE p.cuil=$cuil_titular 
													AND a.id_titular=0  ";
											
						$rs_titular = mysql_query($sql_titular) or die(mysql_error()." ".$sql_titular);
						$d_titular = mysql_fetch_object($rs_titular);					
																
					?>
					<table style="width: 990px;">
						<tr>
							<th>Nro de afiliado:</th>
							<td> <?=$d_titular->nro_afiliado;?></td>
							<th>Apellido y nombre</th>
							<td> <?=$d_titular->ayn;?></td>
						</tr>
						<tr>
							<th>Fecha de nacimiento:</th>
							<td> <?=$d_titular->fn;?></td>
							<th>Fecha de alta</th>
							<td> <?=$d_titular->fecha_alta;?></td>
						</tr>
						<tr>
							<th>Domicilio:</th>
							<td> <?=$d_titular->domicilio;?></td>
							<th>Localidad:</th>
							<td> <?=$d_titular->localidad;?></td>
							<th>C.P.:</th>
							<td> <?=$d_titular->cp;?></td>
						</tr>
						<tr>
							<th>Telefono</th>
							<td> <?=$d_titular->telef_celular;?></td>
							<th>Delegacion</th>
							<td></td>
						</tr>
						<tr>
							<th>Oficina</th>
							<td></td>
						</tr>
						<tr>
							<th>Telefono laboral</th>
							<td></td>
						</tr>
						<tr>
							<th>Documento</th>
							<td> <?=$d_titular->nd;?></td>
						</tr>

							<?		

							if($d_titular->tbt == "RG"){
								$sql_empresa="SELECT dj.c,COALESCE(e2.nombre,'No existe la empresa en el padron de empresas') AS empresa
														FROM $base_historicos.`declaraciones_juradas` dj														 
														LEFT JOIN $base.empresas e2 ON dj.c=e2.cuit COLLATE latin1_general_ci
														WHERE d = $cuil_titular
														ORDER BY b DESC 
														LIMIT 1";
								
								$rs_empresa=mysql_query($sql_empresa);
								$d_empresa=mysql_fetch_object($rs_empresa);	
								
								echo "
									<tr>
										<th>Empresa</th>
										<td>".$d_empresa->empresa."</td>
										<th>CUIT Empresa</th>
										<td>".$d_empresa->c."</td>
									</tr>
								";
							}else{
								echo "
									<tr>
										<th>Tipo Beneficiario</th>
										<td>".$d_titular->tbt_denominacion."</td>
									</tr>
								";
							}
							?>

						<tr>
							<th>Email</th>
							<td></td>
						</tr>
					</table>
				</div>
				
				<div class="cuadro_bordes">
					<label>Familiares</label> 
					<hr>
					
					<table style="width: 1000px;">
						<tr>
							<th>Nro Afiliado</th>
							<th>Documento</th>
							<th>Apellido y Nombre</th>
							<th>Fec. Nac</th>
							<th>Historia Clinica</th>
						</tr>
						<?
							$sql_familiar = "SELECT CONCAT(nben,'/',gpar) AS nro_afiliado,
											p.nd,CONCAT(apellido,' ',nombre) AS ayn,
											DATE_FORMAT(fn,'%d/%m/%Y') AS fn
											
										FROM $base_padron.padron p
										JOIN $base_padron.afiliados a ON p.id_afiliado=a.id 
										WHERE cuil_titular=$cuil_titular
											AND parentesco='familiar' 
											AND MID($base_padron.`estado_afiliado_nuevo_test`(a.id,CURDATE()),1,4)='ALTA' ";
											

							$sql="SELECT a.id
							FROM $base_padron.`persona` p
							JOIN $base_padron.`afiliados` a ON a.`id_persona`=p.`id`
							WHERE cuil='$cuil_titular'";

							$rs = mysql_query($sql);
							$d = mysql_fetch_object($rs);
							$id_titular = $d->id;

							$sql="SELECT CONCAT(nben,'/',gpar) AS nro_afiliado,
								p.nd,CONCAT(apellido,' ',nombre) AS ayn,
								DATE_FORMAT(fn,'%d/%m/%Y') AS fn
							FROM $base_padron.`afiliados` a
							JOIN $base_padron.`persona` p ON a.`id_persona`=p.`id`
							WHERE id_titular=$id_titular AND $base_padron.estado_afiliado_nuevo_test(a.id,CURDATE()) LIKE 'ALTA%'";

							$rs = mysql_query($sql);
							
							while($d_familiar=mysql_fetch_object($rs)){
								
								echo "<tr>
										<td>$d_familiar->nro_afiliado</td>
										<td>$d_familiar->nd</td>
										<td>$d_familiar->ayn</td>
										<td>$d_familiar->fn</td>
										<td></td>										
									  </tr>";
							}
						
						?>
					</table>
				</div>	
				
				<div>
					
					<div style="text-align: center;">
						<img src="../../../img/firmas_provisorias.jpg" />
					</div>
				</div>
				
			</div>
		</div>
		<script>
			$(function(){
				window.print();
			})
		</script>
	</body>
</html>
