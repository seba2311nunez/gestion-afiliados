<?php
if($Entrar=="Entrar"){
include("Conectar.inc");



$sql="SELECT * FROM sys_users where nombre ='$usu' and clave='$cod'";

$resultado_set = mysql_query($sql);


if (mysql_num_rows($resultado_set)==0) 
	{
		mysql_free_result($resultado_set);
			echo "<h2 align='center'>El codigo es incorrecto</h2>";
	}
	else{
	
	
	
	$_SESSION["usu"] = $usu;
	$id_institucion=mysql_result($resultado_set, 0, 'id_institucion');
	$_SESSION["id_institucion"] = $id_institucion;	
	$_SESSION["bienv"] = mysql_result($resultado_set, 0, 'nombre');	
	$_SESSION["iduser"] = mysql_result($resultado_set, 0, 'id');
	$iduser=	$_SESSION["iduser"];
	$sql="
		select * from sys_user_modulo
		where id_usuario=$iduser and sistema='preventa'
		";

	$rs=mysql_query($sql);
	if (mysql_num_rows($rs)==0) 
	{
		mysql_free_result($resultado_set);
			echo "<h2 align='center'>El codigo es incorrecto</h2>";
	} else{
	$_SESSION["consulta"] = mysql_result($rs, 0, 'consulta');
	mysql_free_result($resultado_set);
	$rss=mysql_query("SELECT nombre, razon_social FROM institucion WHERE id=$id_institucion");
	$_SESSION["institucion"] = mysql_result($rss, 0, 'nombre');
	$_SESSION["razon_social"] = mysql_result($rss, 0, 'razon_social');
		
	mysql_close($conexion);
	echo "<script languaje='javascript'> 
window.close();
</script>";
	
	}
	}

}else{ //AL ingresar elimina la sesion


session_destroy();
}
?>

<body bgcolor=lightblue>
<form method="POST" action="relogear.php">
<div align="center">
Su sesion a expirado. Por favor vuelva a logearse en el sistema.
<br>
<table align=center border=0>
  <tr bgcolor=#C0C0C0><td><font face=verdana size=1>Usuario:&nbsp;</td><td><input name=usu value="<?php echo $usuario;?>"></td></tr>
  <tr bgcolor=#C0C0C0><td><font face=verdana size=1>C&oacute;digo:&nbsp;</td><td><INPUT NAME=cod type=password></td></tr>
<tr><td colspan="2" align="center">
<input type=submit name=Entrar id=Entrar value=Entrar >
</td></tr>
</table>
</div>