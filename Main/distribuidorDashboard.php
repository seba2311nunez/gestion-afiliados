<?php
include "../Config/init.inc";
//echo "$perfil"; exit();


switch ($perfil) {
    case "consulta_prestador":
        header("location: ../APP/buscar_afiliado/index.php");
        break;
    case "consulta_padron":
        header("location: ../APP/buscar_afiliado/index.php");
        break;	
    case "consulta_filial":
        header("location: ../APP/buscar_afiliado/index.php");
        break;  
    case "consulta":
        header("location: ../APP/buscar_afiliado/index.php");
        break;
    case "equipo_interdisciplinario":
        header("location: ../APP/buscar_afiliado/index.php");
        break;	
    default:
		header("location: dashboard.php");
   }

?>
