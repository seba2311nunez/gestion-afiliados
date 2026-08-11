<?php 
session_start();
$_SESSION = $_SESSION;
  

//($conexion = new mysqli("microteam.cdbpururdyjh.us-east-1.rds.amazonaws.com", "mluis", "Consulta!123", ");
$conexion = new mysqli("190.18.6.190", "myuser", "mypass", ");
if ($conexion->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error;
}

foreach($_REQUEST as $var=>$value){
 $_GLOABLS[$var] = $value;
 $$var = mysql_real_escape_string($value);
} 

function shutdown()
{
 global $_SESSION;
 $_SESSION = array_merge($_SESSION, $_SESSION);
}

register_shutdown_function('shutdown');

function session_register($arg){}; // dummy
$ip=$_SERVER['REMOTE_ADDR'];


$usuario= $_SESSION["usu"];
$perfil=$_SESSION["perfil"];
include("funciones.inc");
include("funcionesJavaScript.inc");
$script = $_SERVER["SCRIPT_NAME"];
$id_institucion = $_SESSION["id_institucion"];
$_fecha_hora_actual=date("d-m-Y H:i:s");
mysqli_query($conexion, "insert into log_seguridad (script,usuario,ip) values ('$script','$usuario','$ip')");
$rs=mysqli_query($conexion, "SELECT * FROM _seguridad_scriptxusuario WHERE usuario='$perfil' and TRIM(script) LIKE '%$script'");

if (mysqli_num_rows($rs) == 0 || mysqli_num_rows($rs) == null ) {
	echo "<h2>ACCESO RESTRINGIDO AL USUARIO!</h2>";exit();
}
while($data=mysqli_fetch_object($rs)){$acceso=$data->acceso;}


function habilitarSegunPerfilButton(){
	global $acceso;
    if($acceso=="TOTAL"){
		return "";
    }else{
    	return "disabled='dissabled'";
    }
}

function habilitarSegunPerfilHref(){
	global $acceso;
    if($acceso=="TOTAL"){
		return "";
    }else{
    	return "return false";
    }
}

function habilitarLink(){
	global $acceso;
    if($acceso=="TOTAL"){
		return "href";
    }else{
    	return "name";
    }
}
?>
