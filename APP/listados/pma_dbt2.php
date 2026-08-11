<?
include (__DIR__."/../../Config/Conectar.inc");

$filename = N_BASE."_diabetes_x_tipo".".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);

$table="
	<table border=1>
		<tr>			
			<th>Gerenciador</th>
			<th>Tipo</th>
			<th>Total</th>
		</tr>
";
$tr_estado = "";
$sql = "CALL $base_padron.`INFO_totales_diabetes_x_tipo_hoy`()";
mysql_query($sql);

$sql = "SELECT * FROM `$base_padron`.`tmp_totales_diabetes_x_tipo` ";
$result = mysql_query($sql);

while($d=mysql_fetch_object($result)){
	$table.= "
		<tr $tr_estado>
			<td>$d->gerenciador</td>
			<td>$d->tipo</td>
			<td>$d->cantidad</td>				
		</tr>
	";
}
$table.="</table>";

echo $table; exit();
?>