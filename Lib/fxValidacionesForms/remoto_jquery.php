<?php
require_once dirname(__FILE__).'/../../Config/database.inc.php';
$formUsuario=$_GET['usuario'];
$conexion=database_private_connect('ppdev');
$sql="SELECT * FROM persona WHERE nd='$formUsuario'";
$resultado=mysql_query($sql,$conexion);
if(mysql_fetch_assoc($resultado)){
	echo "true";
}else{
	echo "false";
}
?>
