<?php
//session_destroy();
include("../Config/Conectar.inc");

mysql_select_db($base_usuarios);

$cod=str_replace("=","+",$cod);
$sql = "SELECT * FROM users WHERE usuario ='$usu' AND clave='$cod' AND estado='alta'";
//echo "$sql";exit();

$resultado_set = mysql_query($sql);


if (mysql_num_rows($resultado_set)==0) 
	{
		mysql_free_result($resultado_set);
		header("Location: error.php");
	}

	else {
				
		$_SESSION["usu"] = $usu;		
		$_SESSION["bienv"] = mysql_result($resultado_set, 0, 'nombre');	
		$_SESSION["iduser"] = mysql_result($resultado_set, 0, 'id');
		$iduser=	$_SESSION["iduser"];
		$sql = "SELECT * FROM users_modulos
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
			
		// echo $_SESSION["perfil"];
		
		mysql_query("UPDATE users_modulos SET fecha_ult_acceso=NOW() WHERE sistema='afiliaciones' AND id_user=$iduser ");
		mysql_free_result($resultado_set);
		$rss=mysql_query("SELECT nombre, razon_social FROM institucion WHERE id=$id_institucion");
		$_SESSION["institucion"] = mysql_result($rss, 0, 'nombre');
		$_SESSION["razon_social"] = mysql_result($rss, 0, 'razon_social');
			
		mysql_close($conexion);
		header("Location: ../index2.php");

	}


?>
