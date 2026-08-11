<?php include "../../../Config/init.inc";?>
<HTML>
<head>
	<?php include "../../../Lib/fxValidacionesForms/fxCreadasValidaForms.inc"; include "../../../Lib/fxValidacionesForms/jqueryGeneralFormsControles.inc";?>
	<link type="text/css" href="../../../Config/css/estilo.css" rel="stylesheet"/>
<style type="text/css">
th
{
background-color: #336699;
}
select.new 
{
font-size: 60%;
}
input.new 
{
height: 20px;
font-size: 60%;
}
</style>

</head>
<BODY>
<form action=grmod_empresa.php id=form1 name=form1 METHOD=POST>
<font face=Tahoma size=2 color=DarkBlue>
		<b>Modificacion de Empresa</b></font>
		<br>
		<hr>
<br>
<center>

<table border=1>
<?

	$sql= "SELECT e.*, rubro FROM empresa e LEFT JOIN rubro r ON r.id = e.id_rubro WHERE e.id=$id";
	//echo $sql;//exit();
	$rs = mysql_query ($sql);
	$filas = mysql_num_rows($rs);
	$data = mysql_fetch_object($rs);


	$sql= "select *  from  fiscalizacion_estado_empresas order by estado";
	$rs2 = mysql_query ($sql);
	$filas2 = mysql_num_rows($rs2);	

	$sql= "select distinct localidad  from  empresa order by localidad";
	$rs7 = mysql_query ($sql);
	$filas7 = mysql_num_rows($rs7);

?>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Denominacion</th>
		<td colspan=4><input name="nombre" id="nombre" size="79" value="<? echo $data->nombre; ?>"></td>
</tr>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Rubro</th>
	<td colspan=4>
		<select name="rubro" id="rubro">
			<?php 
				$sql_rubro = "SELECT * FROM rubro";
				$rs_rubro = mysql_query($sql_rubro);

				while ($o = mysql_fetch_object($rs_rubro)) {
					if ($data->rubro == $o->rubro) {
						echo "<option selected value='$o->id'>$o->rubro</option>";
					} else {
						echo "<option value='$o->id'>$o->rubro</option>";
					}	
				}
				
				if ($data->rubro == "") {
					echo "<option selected value='0'>Sin rubro. Seleccione rubro por favor...</option>";
				}
			?>
		</select>
	</td>
</tr>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>CUIT</th>
		<td>
			<input type="hidden" name="idemp" value="<? echo $id; ?>" >
			<input name="cuit" id="cuit" value="<? echo $data->cuit; ?>"></td>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Domicilio</th>
		<td><input name="dir" id="dir" size= 29 value="<? echo $data->direccion;?>"></td>
</tr>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Cod Postal</th>
		<td><input name="codpos" id="codpos" value="<? echo $data->codigopostal;?>"></td>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Localidad</th>
		<td>
			<select name="localidad" id="localidad" style="width: 160px;">
				<?
				$a="";
				
				for ($w = 0; $w < $filas7; $w++)
				{
					$data7= mysql_fetch_object($rs7);
		
					if ($data->localidad == $data7->localidad)
						{
							$a= "selected";
						}
					else
						{
							$a="  ";
						}
				
				
				?>
				
				<option <? echo $a;?> value="<? echo $data7->localidad ; ?>"><? echo $data7->localidad; ?></option>
				
			<?
			}
			
			?>
			</select>
		</td>
</tr>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Email</th>
		<td><input name="emailpersona" id="emailpersona" value="<? echo $data->email;?>"></td>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Telefono</th>
		<td><input name="telefono" id="telefono" size= 29 value="<? echo $data->telefono;?>"></td>
</tr>
<tr>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Contacto</th>
		<td><input name="contacto" id="contacto" value="<? echo $data->contacto;?>"></td>
	<th align=left  bgcolor=#336699><font face=verdana size=2 color=white>Calificacion de Fiscalizacion</th>
		<td>
			<select name="calificacion" id="calificacion" style="width: 160px;">
				<?
				$a="";
				
				for ($w = 0; $w < $filas2; $w++)
				{
					$data2= mysql_fetch_object($rs2);
		
					if ($data->estado_aportes == $data2->estado)
						{
							$a= "selected";
						}
					else
						{
							$a="  ";
						}
				
				
				?>
				
				<option <? echo $a;?> value="<? echo $data2->estado ; ?>"><? echo $data2->estado; ?></option>
				
			<?
			}
			
			?>
			</select>
		</td>
</tr>




<?
mysql_free_result($rs);
mysql_free_result($rs2);
mysql_close($conexion);
?>
</table>
<br>
<input type="hidden" id="clasificacion_anterior" name="clasificacion_anterior" value="<?php echo $data->estado_aportes;?>">
<input type="submit" name="b1" id="b1" value="Procesar">
</form>
</BODY>
</HTML>
