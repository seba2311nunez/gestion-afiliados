<?php 
include("../../Config/Conectar.inc");

$sql = "SELECT * FROM $base_imagenes.documentacion WHERE id = $id_documentacion";

$result=mysql_query($sql) or die(mysql_error().$sql);
   
//echo "$sql"; exit();
//echo mysql_num_rows($result); exit();

$c=mysql_fetch_object($result);

$nombre_archivo = $c->nombre;

exec("rm ./archivos/* ");

$fp = fopen("archivos/".$nombre_archivo,"w");	
fwrite($fp, $c->imagen);
fclose($fp);		

//echo "<a href='http://54.225.110.0/php-bin/procesos_una_vez/".$nombre_archivo."'>Click para descargar</a>";

$location="Location: ./archivos/".$nombre_archivo;
header($location);


 ?>