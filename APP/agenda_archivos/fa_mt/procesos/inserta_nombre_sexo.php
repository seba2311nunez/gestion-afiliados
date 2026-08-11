<?php 
require(__DIR__"/../../../../Config/Conectar.inc");

$sql="INSERT INTO $base_fa.nombre_sexo (nombre, sex) 
	  SELECT fa.nombre, fa.sexo 
	  FROM $base_fa.tmp_fa fa 
	  LEFT JOIN $base_fa.nombre_sexo ns ON ns.nombre = fa.nombre 
	  WHERE ns.nombre IS NULL ";
mysql_query($sql);
?>