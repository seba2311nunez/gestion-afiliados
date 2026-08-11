<?php
require(__DIR__."/../../../../Config/Conectar.inc");

$traer_inexistentes="SELECT nombre, sexo from $base_fa.tmp_fa WHERE sexo='I'";
$result = mysql_query($traer_inexistentes);
echo "<table border=1>";
echo "<tr> <th>Nombre</th> <th>Sexo</th> </tr>";
while ($rs = mysql_fetch_object($result)){

	if($rs->sexo == 'I'){
		$selected = "<select>
						 <option value='I' selected>I</option>
						 <option value='M'>M</option>
						 <option value='F'>F</option>
					</select>";
	}

	echo "<tr><td>" .$rs->nombre . "</td><td style='width: 30px; text-align: center;'>". $selected . "</td></tr>";
}
echo "</table>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Proceso Inexistentes</title>
</head>
<body>
	<form>	
	</form>
</body>
</html>