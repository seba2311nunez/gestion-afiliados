<?php 
session_start();

$dominio = $_SESSION['DOMINIO']; 
#include('../../Config/init.inc');
#include("../Config/Conectar.inc")
#echo "hola 1";exit();
header("Location: http://".$dominio."/inicio.php"); exit();
?>


<script>
	const DOMINIO = "<?echo DOMINIO; ?>";
	window.Location.href='http://'+DOMINIO+'/';
	//window.close();
</script>