<?php
require("Conectar.inc");
$ip=$_SERVER['REMOTE_ADDR'];


$usuario= $_SESSION["usu"];
$perfil=$_SESSION["perfil"];
$par= $_SERVER['REQUEST_URI'];
include("funciones.inc");
include("funcionesJavaScript.inc");
$script = $_SERVER["SCRIPT_NAME"];
$script = str_replace("/".DOMINIO, "", $script);
//echo $script;
$id_institucion = $_SESSION["id_institucion"];
$_fecha_hora_actual=date("d-m-Y H:i:s");
$rrr="insert into oseiv_logs.logger (consulta,usuario,programa) values ('padron: $par',' $usuario-$perfil-$ip ','$script_v1-$script')";
//echo "$rrr"; exit();
mysql_query($rrr);
mysql_query("insert into log_seguridad (script,usuario,ip) values ('$script','$usuario','$ip')");

$script2 = str_replace(DOMINIO."/", "",  $script); 

$sql="SELECT * FROM $base_padron._seguridad_scriptxusuario 
		WHERE usuario='$perfil' 
			AND ( TRIM(script) LIKE '%$script' OR TRIM(script) LIKE '%$script2' )"; 
//echo $sql; exit();
$rs=mysql_query($sql);



if (mysql_num_rows($rs) == 0 || mysql_num_rows($rs) == null ) {
	//echo "<h2>ACCESO RESTRINGIDO AL USUARIO!</h2><br>$sql";exit();
}
while($data=mysql_fetch_object($rs)){$acceso=$data->acceso;}


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