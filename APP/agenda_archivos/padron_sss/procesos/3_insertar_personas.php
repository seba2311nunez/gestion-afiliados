<?php 
require(__DIR__."/../../../../Config/Conectar.inc");

//echo "Hola  " . $_SERVER['PHP_SELF'] . "<br>"; 

$sql="CALL $base_historicos.PDS_carga_persona()";
mysql_query($sql) or die(mysql_error().$sql);

echo "<h3>Termino</h3>";
echo "<a href='#' onclick='javascript: window.close()'>Cerrar </a>";

?>