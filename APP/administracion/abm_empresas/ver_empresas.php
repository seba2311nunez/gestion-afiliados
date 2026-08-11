<? include "../../../Config/init.inc";?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script type="text/javascript" src="../../../Lib/tabla/table.js"></script> 
	<link rel="stylesheet" type="text/css" href="../../../Lib/tabla/table.css" media="all">
	<?php include "../../../Lib/fxValidacionesForms/fxCreadasValidaForms.inc"; include "../../../Lib/fxValidacionesForms/jqueryGeneralForms.inc";?>
	<link type="text/css" href="../../../Config/css/estilo.css" rel="stylesheet"/>
</head>
<body>
<form name="form1" id="form1" action="alta_empresa.php" method="post">
<font face=Tahoma size=2 color=DarkBlue>
		<b>Trabajar con Empresas</b></font>
		<br>
		<hr>
<center>
<table name='t1' id='t1' border=1 class='example table-autosort table-autofilter table-autopage:20 table-stripeclass:alternate table-page-number:t1page table-page-count:t1pages table-filtered-rowcount:t1filtercount table-rowcount:t1allcount'>
<thead>	
<tr>	
	<th class='table-sortable:default'>ID</th>
	<th class='table-filterable table-sortable:default'>Nombre</th>
	<th class='table-sortable:default'>CUIT</th>
	<th class='table-filterable table-sortable:default'>Domicilio</th>
	<th class='table-sortable:default'>Localidad</th>
	<th class='table-sortable:default'>Provincia</th>
	<th class='table-sortable:numeric'>C.P.</th>
	<th class='table-sortable:default'>Telefono</th>
	<th class='table-filterable table-sortable:default'>Estado</th>
	<th class='table-filterable table-sortable:default'>Eliminar</th>
</tr>
</tr>
</thead>	
<tbody>
<?php 
	$sql_buscar_empresas= "SELECT * FROM empresa WHERE cuit LIKE '%$c%' AND nombre LIKE '%$n%' ORDER BY nombre";
	$rs_buscar_empresas = mysql_query ($sql_buscar_empresas);
	$control_user=habilitarLink();	
	while($data=mysql_fetch_object($rs_buscar_empresas)){?>
	<tr>
			
		<td><a href="mod_empresa.php?id=<?php echo $data->id; ?>"><? echo $data->id; ?></a></td>
		<td><?php echo $data->nombre;?></td>
		<td><?php echo $data->cuit; ?></td>
		<td><?php echo ucwords(strtolower($data->direccion));?></td>
		<td><?php echo ucwords(strtolower($data->localidad));?></td>
		<td><?php echo ucwords(strtolower($data->provincia));?></td>
		<td><?php echo $data->codigopostal; ?></td>
		<td><?php echo $data->telefono; ?></td>
		<td><?php echo $data->estado_aportes; ?></td>
		<td><a <? echo $control_user;?>="_eliminar_empresa.php?id_empresa=<?php echo $data->id;?>&c=<?php echo $c;?>&n=<?php echo $n;?>">Eliminar</a></td>
	</tr>
	<?php }mysql_free_result($rs);mysql_close($conexion);?>
</tbody>
	<tfoot> 
		<tr>
		<td title='Ir a pagina anterior' class=table-page:previous style=cursor:pointer;>&lt; &lt; Atras</td>
		<td colspan=8 style=text-align:center;>Pagina <span id=t1page></span>&nbsp; de <span id=t1pages></span></td> 

		<td title='Ir a proxima pagina' class=table-page:next style=cursor:pointer;>Siguiente &gt; &gt;</td>
		</tr>
		<tr>
		<td colspan=10 align=right><span id='t1filtercount'></span>&nbsp;de <span id='t1allcount'></span>&nbsp;filas filtradas</td>
	</tr>
	</tfoot>
	</table>
</br></br>
<input type="submit" id="b1" name="b1" value="Agregar nueva empresa">
</form>
</body>
</html>

