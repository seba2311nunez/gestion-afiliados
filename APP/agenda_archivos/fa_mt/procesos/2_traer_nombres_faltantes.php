<?php 
require(__DIR__."/../../../../Conectar.inc");
$sql="CALL $base_historicos_bk.FA_traer_nombres_faltantes()";
mysql_query($sql) or die(mysql_error().$sql);

echo "<h3>Termino, ahora tenes que procesar a mano en la tabla <b>$base_padron.tmp_nombres_faltantes<b> antes de usar el siguiente proceso</h3>";
echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

?>