<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<BODY>
<div align="center">
<font face=Tahoma size=2 color=DarkBlue>
<b>Buscar en Empresas para trabajar con ellas</b></font>

<br>
<hr>
<br>
<br>
<form action=ver_empresas.php id=form1 name=form1>
<table>
<tr>
	<td><font face=verdana size=1>CUIT</td>
	<td><input type="text" id="c" name="c" value = "<?echo $xxx; ?>"></td>
</tr>

<tr>
	<td><font face=verdana size=1>Nombre</td>
	<td><input type="text" id="n" name="n" value=""></td>
</tr>

</table>
<p><font face=verdana size=1><em>Para listar todo, dejar en blanco todos los campos y clickear "Buscar"</em></p>
<br>
<input type="submit" id="submit1" name="submit1" value="Buscar">
</form>
</div>
</b></font>
</BODY>
</html>