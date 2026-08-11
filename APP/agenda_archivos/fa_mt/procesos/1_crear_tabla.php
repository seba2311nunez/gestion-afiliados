<?php 
require(__DIR__."/../../../../Config/Conectar.inc");
$sql="CALL $base_historicos.FA_crear_tabla($id_lote);";
mysql_query($sql) or die(mysql_error().$sql);

echo "<h3>Termino</h3>";
echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

?>