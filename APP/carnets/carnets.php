<?php

include("../../Config/init.inc");


?>
<link href="../../Config/xampp.css" rel="stylesheet" type="text/css" />
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

<style type="text/css">
	.imprimir_carnets_provisorio {
		border-spacing: 0;
		border-collapse: collapse;
		box-sizing: border-box;
		margin: 0;
		font: inherit;
		overflow: visible;
		text-transform: none;
		font-family: inherit;
		display: inline-block;
		margin-bottom: 0;
		font-weight: 400;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		cursor: pointer;
		user-select: none;
		background-image: none;
		border: 1px solid transparent;
		color: #fff;
		background-color: #5cb85c;
		border-color: #4cae4c;
		padding: 5px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
	}
</style>
<script type="text/javascript">
	$(function(){
		$(document).on('click','.imprimir_carnets_provisorio',function(e){

			if($(this).data('tbt') == "RG"){
				busqueda_auto = confirm('¿Desea que el sistema busque la ultima empresa declarada en DDJJ?');
				if(busqueda_auto){
					if($(this).data('cuit')){
						window.open(`imprimir_carnets_provisorio.php?dni=${$(this).data('dni')}`);
					}else{
						credencial_cuit_manual($(this).data('dni'));
					}
				}else{
					console.log('Busqueda_auto is false');
					credencial_cuit_manual($(this).data('dni'));
				}				
			}else{
				window.open(`imprimir_carnets_provisorio.php?dni=${$(this).data('dni')}`);	
			}

		})
	});
	function credencial_cuit_manual(dni){
		let cuit = prompt('CUIT de empleador no encontrado. Ingreselo manualmente');

		if(cuit){
			let nombre_empresa = prompt('Ingrese la razon social del empleador.');
			
			if(nombre_empresa){
				let url = `imprimir_carnets_provisorio.php?dni=${encodeURIComponent(dni)}&cuit=${encodeURIComponent(cuit)}&nombre_empresa=${encodeURIComponent(nombre_empresa)}`;
				window.open(url);
			}else{
				console.log('No hay nombre_empresa');
			}
		}else{
			console.log('No hay cuit');
		}
	}
</script>

<?


if($boton=="Buscar"){
	
	mysql_query("delete from $base_ppdev.carnets_tmp") or die(mysql_error());
	
	mysql_select_db($base_padron, $conexion);
	$boton="";

	if($nben){//A pedido de Pablo se agrego una busqueda por nben, para no modificar el programa que funciona en base a $dni voy a setear esta misma variable en base del nben recibido

		$sql="SELECT nd FROM $base_padron.`afiliados` a
			JOIN $base_padron.`persona` p ON p.id=a.`id_persona`
			WHERE nben=$nben AND id_titular=0";
		$rs = mysql_query($sql) or die(mysql_error());
		$dni = mysql_fetch_object($rs)->nd;

	}
	if($dni!=""){
		//buscar titular del grupo familiar
		$sql_buscar_titular="SELECT IF(a.id_titular=0,a.id,a.id_titular) AS id_titular FROM(
								SELECT * FROM persona WHERE nd='$dni') p
								LEFT JOIN afiliados a ON a.id_persona=p.id";
		$rs_buscar_titular=mysql_query($sql_buscar_titular)or die(mysql_error());
		if(mysql_num_rows($rs_buscar_titular)==0 or mysql_num_rows($rs_buscar_titular)==null){
			echo "<h2>No existe afiliado</h2>";exit();
		}else{
			
		$d_buscar_titular=mysql_fetch_object($rs_buscar_titular);
		$id_titular=$d_buscar_titular->id_titular;
			$sql_grupo=" SELECT a.id AS id_afiliado,a.id_persona,mid(CONCAT(pe.apellido,' ',pe.nombre),1,29) AS nombre_afiliado,pe.nd,pe.cuil,
					 pe.telef_celular,CONCAT(MID(pe.fn,9,2),'/',MID(pe.fn,6,2),'/',MID(pe.fn,1,4)) AS fn,p.nombre AS plan,
					 IF(paren.id IN(3,4,5,6,11,12),'Hijo soltero',IF(paren.id IN (7,8,9),'Familiar',paren.parentesco)) as parentesco,
					 em.nombre AS empresa,em.cuit AS cuit_empresa,em.estado_aportes AS estado_empresa,
					 i.nombre AS obra,s.sanatorio,estado_afiliado_nuevo_test(a.id,CURDATE()) AS estado_afil,pat.nombre as patologia,a.cbu,pe.email
					 ,habilitado_emision_carnet,a.id_parentesco, nben,gpar,filial,tbt.sigla as tbt
					 FROM
					(
					SELECT * FROM afiliados WHERE id=$id_titular OR id_titular=$id_titular) a
					JOIN persona pe ON pe.id=a.id_persona
					JOIN planes p ON p.id=a.id_plan_medico
					JOIN parentesco paren ON paren.id=a.id_parentesco
					JOIN tipo_beneficiario_titular tbt on tbt.id=a.id_tipo_aporte
					LEFT JOIN preventa pr ON pr.id_persona=pe.id
					LEFT JOIN opcion op ON op.id_preventa=pr.id
					LEFT JOIN campos_afiliados_sin_preventa_ni_opcion ca ON ca.id_afiliado=a.id
					LEFT JOIN institucion i ON i.id=COALESCE(op.id_institucion,ca.`id_obra_social`)
					LEFT JOIN sanatorio s ON s.id=a.id_sanatorio
					LEFT JOIN empresa em ON em.id=COALESCE(pr.`id_empresa`,ca.`id_empresa`)
					JOIN patologias pat ON pat.id=a.id_patologia
					ORDER BY parentesco DESC";			
		
		//echo $sql_grupo;exit();	
		$rs_grupo=mysql_query($sql_grupo) or die(mysql_error());
		$cant=mysql_num_rows($rs_grupo);
		$n=1;


		echo "	<center><fieldset><legend>Grupo Familiar</legend><br>
				<form name='formu2' action='imprimir_carnets_bk.php'>";
		while ($d=mysql_fetch_object($rs_grupo)){



			if($dni==$d->nd){
				$bgcolor="bgcolor='lime'";
			}else{
				$bgcolor="";
			}

			if($n==1){
				if($d->estado_afil!='ELIMINADO'){
				$titular_nom=$d->nombre_afiliado;
				$filial=$d->filial;

				if($d->tbt == "RG"){
					$sql_empresa="SELECT dj.c,COALESCE(e2.nombre,'No existe la empresa en el padron de empresas') AS empresa
											FROM $base_historicos.`declaraciones_juradas` dj														 
											LEFT JOIN $base.empresas e2 ON dj.c=e2.cuit COLLATE latin1_general_ci
											WHERE d = $d->cuil
											ORDER BY b DESC 
											LIMIT 1";
					
					$rs_empresa=mysql_query($sql_empresa);
					$d_empresa=mysql_fetch_object($rs_empresa);	


				}
				echo "<table border=1>
				<tr bgcolor='#FFCC33'>
					<td align='center' colspan='8'>DATOS DEL TITULAR</td></tr>
					<td align='center'>Nombre</td>
					<td align='center'>Cuil</td>
					<td align='center'>Telefono</td>
					<td align='center'>Plan</td>
					<td align='center'>Obra Social</td>
					<td align='center'>Residencia</td>
					<td align='center'>Empresa</td>
					<td align='center'>Estado Empresa</td>
				</tr>
					<td>$titular_nom</td>
					<td>$d->cuil</td>
					<td>$d->telef_celular</td>
					<td>$d->plan</td>
					<td>$d->obra</td>
					<td>$d->sanatorio</td>
					<td>$d->empresa</td>
					<td>$d->estado_empresa</td>
				</tr>		
			</table>
					<br>
					
					<a class='imprimir_carnets_provisorio' data-dni='$d->nd' data-tbt='$d->tbt' data-cuit='$d_empresa->c' data-empresa='$d_empresa->empresa'   title='Imprimir carnet provisorio ultima version'>
						Imprimir 
					</a>
										
					<br><br><br><br>
					<table border=1><tr><td align='center'>N</td>
					<td align='center'>Nombre</td>
					<td align='center'>Documento</td>
					<td align='center'>Plan</td>
					<td align='center'>Familia</td>
					<td align='center'>Nacimiento</td>
					<td align='center'>Estado</td>
					<td align='center'>Patologia</td>
					<td align='center'>IMPRIME?</td>
					<td align='center' colspan='2'>Coordenadas Desde</td>					
					</tr>";
					$nd_titular=$d->cuil;
					$os_grupo=$d->obra;
					$sanatorio_grupo=$d->sanatorio;
					$cbu_grupo=$d->cbu;
					if($d->estado_afil=='PENDIENTE'){
						$os_grupo="Credencial Provisoria-".$os_grupo;	
					}
					
						}else{
							echo "<h2>No existe afiliado</h2>";exit();
						}
					}
			
	
				echo "	<tr><td $bgcolor>$n</td>
						<td $bgcolor>$d->nombre_afiliado</td>						
						<td $bgcolor>$d->nd</td>					
						<td $bgcolor>$d->plan</td>
						<td $bgcolor>$d->parentesco</td>
						<td $bgcolor>$d->fn</td>
						<td $bgcolor align='center'>$d->estado_afil</td>
						<td $bgcolor>$d->patologia</td>
						";
					
				//----VERIFICAR SI TIENE UNA BAJA ANTERIOR AL PROXIMO ANIO PARA SACAR FECHA VENCIMIENTO
				$sql_fecha_baja="SELECT CONCAT(MID(fecha_aPartir,9,2),'/',MID(fecha_aPartir,6,2),'/',MID(fecha_aPartir,1,4)) AS fecha_baja
									FROM bajas_manuales 
									WHERE id_afiliado=$d->id_afiliado 
									AND fecha_aPartir BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 1 YEAR)
									ORDER BY fecha_aPartir LIMIT 1";
				$rs_fecha_baja=mysql_query($sql_fecha_baja);
				//echo "$sql_fecha_baja <br>";	
				if(mysql_num_rows($rs_fecha_baja)==0 || mysql_num_rows($rs_fecha_baja)==null)//fecha_venc 1 a�o
				{ 
				$date_finish=strftime('%d/%m/%Y');/*fecha actual*/
				$datearray= explode("/",$date_finish);
				$vence = date( "d/m/Y", mktime(0,0,0,$datearray[1],$datearray[0],$datearray[2]+1));//a la fecha actual se le suma un a�o
				$vence=t_fec2($vence);	
				$vence_parametro=str_replace("-","/",$vence);
				$tipo_carnet='N';			
				}else{ //SI ENCUENTRA UNA BAJA ANTERIOR AL AÑO
				$d_vencimiento=mysql_fetch_object($rs_fecha_baja);	
				$vence_parametro=$d_vencimiento->fecha_baja;
				$vence=t_fec2($vence_parametro);
				$tipo_carnet='N';	
				}
				
	
				if ($d->id_parentesco==12)//EMITE CREDENCIAL POR 7 dias si es recien nacido
				{
					$date_finish=strftime('%d/%m/%Y'); /*fecha actual*/
					$datearray= explode("/",$date_finish);
					$vence = date( "d/m/Y", mktime(0,0,0,$datearray[1],$datearray[0]+7,$datearray[2]));//a la fecha actual se le suma 7 dias
					$vence=t_fec2($vence);	
					$vence_parametro=str_replace("-","/",$vence);
					$tipo_carnet='T';
				}
					
					
				if ($d->estado_afil=='ALTA' || $d->estado_afil=='EN TRANSITO')//si estado = alta o afiliado en transito
				{
					echo "<td align='center'><INPUT type=checkbox name='imprime$n' id='imprime$n' value='$d->nd@$vence@$d->id_afiliado'></td>";	
				}
				if ($d->estado_afil=='BAJA')
				{
					echo "<td align='center'><INPUT type=checkbox name='imprime$n' id='imprime$n' value='$d->nd@$vence@$d->id_afiliado' disabled></td>";	
				}	
				if ($d->estado_afil=='PENDIENTE')//EMITE CREDENCIAL POR 3 MESES
				{
					if($d->habilitado_emision_carnet=="S"){
					$date_finish=strftime('%d/%m/%Y');/*fecha actual*/
					$datearray= explode("/",$date_finish);
					$vence = date( "d/m/Y", mktime(0,0,0,$datearray[1]+3,$datearray[0],$datearray[2]));//a la fecha actual se le suma un a�o
					$vence=t_fec2($vence);	
					$vence_parametro=str_replace("-","/",$vence);
					$tipo_carnet='T';
					echo "<td align='center'><INPUT type=checkbox name='imprime$n' id='imprime$n' value='$d->nd@$vence@$d->id_afiliado'></td>";	
				}else{
					echo "<td align='center'><INPUT type=checkbox name='imprime$n' id='imprime$n' value='$d->nd@$vence@$d->id_afiliado' disabled></td>";	
				}
				}
				
							
				if($n==1){					
					echo "<td align='center' rowspan='$cant'>
							<select name='inicioen'>
							<option value='0'>Columna 1</option>
							<option value='1'>Columna 2</option>
							<option value='2'>Columna 3</option>
							</select></td>";
					echo "<td align='center' rowspan='$cant'>
							
					<select name='iniciofil'>";
							for($i=0; $i < 7; $i++){
								$actual=$i+1;
								echo "<option value='$i'>Fila $actual</option>";
							}							
							echo "</select></td>";
					echo "</tr>";
				}				
				
				$n++;
				
				mysql_select_db($base_ppdev, $conexion);
				
				$fn=t_fec2($d->fn);
				$plan=substr($d->plan, 0, 3);
				$sql="insert into $base_ppdev.carnets_tmp (nben,gpar,titular,seccional,numafi, nombre, nd, fn, plan, vencimiento, capita, familia,cuit, os_origen, usuario,id_afil_padron)
						values (
						'$d->nben','$d->gpar','$titular_nom','$filial',
						'$nd_titular','$d->nombre_afiliado','$d->nd','$fn','$plan','$vence','','$d->parentesco','$d->cuit','$os_grupo','$usuario',$d->id_afiliado)";
				//echo "$sql <br>";	
				mysql_query($sql);

	}
	
		echo "</table><br>";
		echo "	<input type='hidden' name='renglones' value='$n'>
				<!--
				<input type='submit' name='boton2' value='Imprimir' title='ATENCION: previsualiza la impresi�n y QUEDA ASENTADA LA MISMA'/>	
				-->
				
							
				</form></fieldset></center>";
				exit();
		
	}
	}
	}
	

?>
<script language="javascript">

</script>
<?
$hoy=date("d/m/Y");
$hoy2=date("Y-m-d");
$ahora=date("Hi");

?>
<html>
<body>

<fieldset>
<legend>Busqueda de grupo familiar</legend>
<form name="formu" action="">
<center>
<br>
<table border=1>
<tr>
<td align="center">Documento</td>
<td align="center"><input name="dni" id="dni" type="text" value=""/></td>
</tr>
<tr>
<td align="center">N Benef.</td>
<td align="center"><input name="nben" id="nben" type="text" value=""/></td>
</tr>
</table>
<br>
<input name="boton" id="boton" type="submit" value="Buscar" />
</center>
</form>
</fieldset>
</body>


</html>
