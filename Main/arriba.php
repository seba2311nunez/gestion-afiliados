<?



include ("../Config/funciones.inc");
include ("../Config/Conectar.inc");
if ( $_SESSION["usu"] == "" )
{ echo "<h1>USUARIO INCORRECTO!</h1></br>"; 
exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
<style type="text/css">
<!--
body {

	background-color: #6699CC;
}
-->
</style>
<script language="javascript">
<!--

function ver_principal(){
	var documento=document.form1.dni.value;
	direccion="../APP/"+"pplan_grupo_familiar.php?documento="+documento;
	//alert("La pagina que vas a ir es: "+direccion);
	parent.bottomFrame.location.href=direccion;
}
	
function direccionar(pagina){
switch(pagina)
{
	case "altaPaciente":
		variables="?paciente="+document.form1.numafi.value;
    		direccion="../APP/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		break;
	case "ese_solo":
		variables="?paciente="+document.form1.numafi.value;
    		direccion="../APP/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		break;
	case "datosPaciente":
		variables="?paciente="+document.form1.numafi.value;
    		direccion="../APP/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "internacion":
		variables="?paciente="+document.form1.numafi.value;
    		direccion="../APP/internados/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "quirurgico_amb":
		variables="?paciente="+document.form1.numafi.value;
    	direccion="../APP/internados/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "I_turnos":
		variables="?paciente="+document.form1.numafi.value;
    	direccion="../APP/ambulatorio/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "historiaclinica":
		variables="?paciente="+document.form1.numafi.value;
    	direccion="../APP/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "quirurgico_int":
		variables="?paciente="+document.form1.numafi.value;
    	direccion="../APP/internados/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	case "guardia_ambul":
		variables="?paciente="+document.form1.numafi.value;
    	direccion="../APP/internados/"+pagina+".php" +variables;
		//alert("La pagina que vas a ir es: "+direccion);
		parent.bottomFrame.location.href=direccion;
		//parent.mainFrame.location.href="boca.php?numafi="+document.form1.numafi.value;
		break;
	}
}
//-->
</script>
</head>
<body>
<form name="form1" action="arriba.php">
<table>
<?
if($buscar!="")
{
	$sql="
		select a.id, nombre, nd, numafi,nom,codigoObraSocial
		from paciente a, entidad
		where
		nd like '$ndoc%' and numafi like '$numafi%' and upper(nombre) like upper('%$nombre_afi%')
	    and id_entidad = entidad.id
		order by nombre
        LIMIT 50
		";
//echo $sql;
$rs_afi=mysql_query($sql);
if(mysql_num_rows($rs_afi)>0){
	echo "<tr><th>Beneficio<th>Nombre<th>Doc<tr>";
	echo "<td colspan=\"3\"><select name=\"numafi\">";
	while($data=mysql_fetch_object($rs_afi)){
		echo "<option value=\"$data->id\">$data->nd, $data->numafi, $data->nombre,$data->nom-$data->codigoObraSocial</option>";	
	}
 echo "</select>";
 }else{
 	echo "<tr><td colspan=3><b>Afiliado inexistente padr�n local</b></td></tr><tr>";
 	echo "	<td width=20><input type='text' name='dni' id='dni' value='$ndoc' /></td>
	 		<td><input type='button' name='principal' value='Ver Principal' OnClick=\"ver_principal()\">
			 </td>
	<input type='hidden' name='numafi' id='numafi' value='' />
			 </tr>";
 } 
}
else
{
echo "<tr><th>Beneficio<th>Nombre<th>Doc<tr>";
?>
<td><input name="numafi" type="text" />
<td><input name="nombre_afi" type="text" />
<td><input name="ndoc" type="text" />
<td align="center"><input name="buscar" type="submit" value="Afiliado" />
<?
}
?>

</table>
<br />
<input name="altaPaciente" type="button" value="Alta Paciente" OnClick="direccionar('altaPaciente')" />
<input name="internacion" type="button" value="Internar" OnClick="direccionar('internacion')" />
<input name="turnos" type="button" value="Ambulatorio" OnClick="direccionar('I_turnos')" />
<input name="ese_solo" type="button" value="Historial" OnClick="direccionar('ese_solo')" />
<input name="datosPaciente" type="button" value="Mod Paciente" OnClick="direccionar('datosPaciente')" />
<input name="quirAmb" type="button" value="Quir Amb" OnClick="direccionar('quirurgico_amb')" />
<input name="historiaclinica" type="button" value="H.C." OnClick="direccionar('historiaclinica')" />
<input name="quirurgico_int" type="button" value="Quir Prog" OnClick="direccionar('quirurgico_int')" />
<input name="guardia_ambul" type="button" value="Guardia" OnClick="direccionar('guardia_ambul')" />

</body>
</html>
