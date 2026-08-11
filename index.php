<?php 
session_start();
session_destroy();
if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) 
{ 
echo "<script>alert('El sistema no es compatible con Internet Explorer. Utilizar Mozilla Firefox o Google Chrome')</script>"; 
exit();
}
?>
<html>
<head>
 <title>SMADM</title>
</head>
<body bgcolor=lightblue>
<!-- -->
<br><br><h1><center><font color=#336699 size=6 face='Courier New'><b>SMADM</center></h1>
<br><br><h1><center><font color=#336699 size=6 face='Courier New'><b>Sistema de afiliaciones y administracion del padron</b></center></h1>
<br>

<br>
<u><font color=black size=2 face=Tahoma>Ingrese sus datos:</u></h1>
<br><br>
<form autocomplete="off" method="POST" action="Main/dologin4.php">
<input type="hidden" name="aci" value="logout">
<table align=left border=0>
  <tr bgcolor=#C0C0C0><td align="right"><font face=verdana size=1>Usuario:&nbsp;</td><td align=center><input name=usu></td></tr>
  <tr bgcolor=#C0C0C0><td align="right"><font face=verdana size=1>C&oacute;digo:&nbsp;</td><td align=center><INPUT NAME=cod type=password></td></tr>
</table>
<br>
<input type=submit value=Entrar>
</form>
</body>
</html>
