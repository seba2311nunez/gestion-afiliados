<?php
//session_destroy();
session_start();
$_SESSION['x']=$_GET['x'];
$x=$_SESSION['x'];
include("../Config/Conectar.inc");

$ip = $_SERVER['REMOTE_ADDR'];
 #echo $x; exit();

$query = "SELECT l.*,u.usuario,u.clave 
			FROM logs_sistemas.log_os l 
			JOIN $base_usuarios.users u ON l.id_usuario=u.id 
			WHERE l.id=$x"; #echo $query; exit();
$result = mysql_query($query) or die(mysql_error().$query);
$d=mysql_fetch_object($result);

if($d->fecha_log){

	echo "Acceso restringido"; exit();

}

//echo $d->usuario; exit();

$_SESSION["usuario"] = 	$d->usuario;
$_SESSION["clave"] = 	$d->clave;
#$hash = $_SESSION['hash_session'] ;

$usu = 	$_SESSION["usuario"];
$cod = 	$_SESSION["clave"];
#$hash = $_SESSION['hash_session'] ;


$cod=str_replace("=","+",$cod);
$sql = "SELECT * FROM $base_usuarios.users WHERE usuario ='$usu' AND clave='$cod' AND estado='alta'";
//echo "$sql";exit();

$resultado_set = mysql_query($sql);


if (mysql_num_rows($resultado_set)==0) 
	{
		mysql_free_result($resultado_set);
		//echo "llego aca"; exit();
		#header("Location: error.php");
	}

	else {
				
		$_SESSION["usu"] = $usu;		
		$_SESSION["bienv"] = mysql_result($resultado_set, 0, 'nombre');	
		$_SESSION["iduser"] = mysql_result($resultado_set, 0, 'id');
		$_SESSION["id_user"] = mysql_result($resultado_set, 0, 'id');
		$iduser=	$_SESSION["iduser"];
		$sql = "SELECT * FROM $base_usuarios.users_modulos
					WHERE id_user=$iduser 
					AND sistema='afiliaciones'";	
				

		$rs=mysql_query($sql);
		if (mysql_num_rows($rs)==0) 
		{
			mysql_free_result($resultado_set);
			header("Location: error.php");
		} 
		
		$_SESSION["id_institucion"] = 1;
		$id_institucion=$_SESSION["id_institucion"] ;	
		$_SESSION["consulta"] = mysql_result($rs, 0, 'consulta');		
		$_SESSION["perfil"] = mysql_result($rs, 0, 'perfil');		
		$_SESSION['id_especialidad'] = mysql_result($rs, 0, 'id_especialidad');
			
		// echo $_SESSION["perfil"];
		
		mysql_query("UPDATE $base_usuarios.users_modulos SET fecha_ult_acceso=NOW() WHERE sistema='afiliaciones' AND id_user=$iduser ") or die(mysql_error());

		mysql_query("INSERT INTO $base_usuarios.log_accesos (id_user,sistema,ip)
										VALUES ($iduser,'afiliaciones','$ip')");

		mysql_free_result($resultado_set);
		$rss=mysql_query("SELECT nombre, razon_social FROM institucion WHERE id=$id_institucion");
		$_SESSION["institucion"] = mysql_result($rss, 0, 'nombre');
		$_SESSION["razon_social"] = mysql_result($rss, 0, 'razon_social');
			
		mysql_close($conexion);
		header("Location: ../index2.php");

	}


?>
