<?
include (__DIR__."/../../Config/Conectar.inc");

$filename = N_BASE."_GRUPOS_ETAREOS".".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);

$table="
	<table border=1>
		<tr>			
			<th>Grupo Etareo</th>
			<th>M</th>
			<th>F</th>
			<th>No asignado</th>
			<th>Total</th>
		</tr>
";
$tr_estado = "";
$sql = "CALL $base_padron.`INFO_grupos_etareos_x_sexo`()";
mysql_query($sql);

$sql = "SELECT * FROM `$base_padron`.`tmp_grupos_etareos_hoy` ";
$result = mysql_query($sql);

while($d=mysql_fetch_object($result)){
	$table.= "
		<tr $tr_estado>
			<td>$d->grupo_etareo</td>
			<td>$d->m</td>
			<td>$d->f</td>
			<td>$d->no_asignado</td>
			<td>$d->total</td>				
		</tr>
	";
}
$table.="</table>";

echo $table; exit();
?>