<?php
include('../../Config/Conectar.inc');
mysql_select_db('$base_padron', $conexion);
$id_usuario = $_SESSION["id_user"];
$root = $_SERVER['DOCUMENT_ROOT'];


mysql_query("SET NAMES 'utf8'");
header('Content-Type:text/html; charset=UTF-8');

$query = "SELECT obrasocial,IF(obrasocial<CURDATE(),1,0) AS genera 
			FROM ospedyb_historicos.lotes 
			WHERE proceso='novedades_exportables'
				AND id=ospedyb_historicos.`get_id_presentacion_novedades_activa`()";

$result = mysql_query($query) or die(mysql_error().$query);

$d = mysql_fetch_object($result);

if($d->genera==1){

	
}

?>