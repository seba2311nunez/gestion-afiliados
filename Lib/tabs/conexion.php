<?php
require_once dirname(__FILE__).'/../../Config/database.inc.php';
function conectar()
{
	return database_private_connect('tabs');
}

function desconectar()
{
	mysql_close();
}
?>
