<?
include (__DIR__."/../../Config/Conectar.inc");

$filename = N_BASE."_HIV".".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);

$table="
	<table border=1>
		<tr>			
			<th>Gerenciador</th>
			<th>Total</th>
		</tr>
";
$tr_estado = "";
$sql = "CALL $base_padron.`INFO_totales_hiv_hoy`()";
mysql_query($sql);

$sql = "SELECT * FROM `$base_padron`.`tmp_totales_hiv` ";
$result = mysql_query($sql);

while($d=mysql_fetch_object($result)){
	$table.= "
		<tr $tr_estado>
			<td>$d->gerenciador</td>
			<td>$d->cantidad</td>				
		</tr>
	";
}
$table.="</table>";

echo $table; exit();
?>