<?php
require_once dirname(__FILE__).'/../../Config/database.inc.php';
$conexion=database_private_connect('ppdev');
 if (!$conexion) {
     echo "<CENTER>
          Problemas de conexion con la base de datos.
          </CENTER>";
  exit();
}
$dni=$_GET['dni'];
$sql="SELECT * FROM
			(SELECT * FROM  persona   WHERE nd=$dni AND td='DNI') e
			 JOIN 
			 (SELECT * FROM preventa WHERE id_empresa=1 and id_procedencia=1) a  ON e.id=a.id_persona
		";
$resultado=mysql_query($sql);
if(mysql_fetch_assoc($resultado,$conexion)){
	echo "true";
}else{
	echo "false";
}


?>
