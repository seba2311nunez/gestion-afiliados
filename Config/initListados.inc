<?php 
//include("ConectarListados.inc");
include("Conectar.inc");
$ip=$_SERVER['REMOTE_ADDR'];
include("funciones.inc");
$script = $_SERVER["SCRIPT_NAME"];
$id_institucion = $_SESSION["id_institucion"];
$perfil = $_SESSION["perfil"];
$usuario= $_SESSION["usu"];
$id_usuario=$_SESSION["iduser"] ;
$institucion = $_SESSION["institucion"];
$rs=mysql_query("select * from _seguridad_scriptxusuario where usuario='$perfil' and script='$script'");

/*if (mysql_num_rows($rs) == 0 || mysql_num_rows($rs) == null ) {
	echo "<h2>ACCESO RESTRINGIDO AL USUARIO!</h2>";exit();
}*/

