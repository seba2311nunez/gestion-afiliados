<?php
include "../Config/init.inc";
//echo "$perfil"; exit();


switch ($perfil) {
    case "ventas":
		header("location: menues/menuVentas.php");
    	break;
	case "coor_ventas":
		header("location: menues/menuCoorVentas.php");
    	break;	
    case "asistente1":
		header("location: menues/menuAsistente1.php");
    	break;
    case "asistente2":
		header("location: menues/menuAsistente2.php");
    	break;
    case "afiliaciones":
		header("location: menues/menuAfiliaciones.php");
    	break;    
    case "consulta_padron":
		header("location: menues/menuConsultaPadron.php");
    	break;
    case "consulta":
        header("location: menues/menuConsultaInter.php");
        break;
    case "upd_dto_personales":
		header("location: menues/menuConsulta.php");
    	break;
	case "consulta_extendida":
		header("location: menues/menuConsultaExtendida.php");
    	break;
    case "consulta_filial":
        header("location: menues/menuConsultaPadron.php");
        break;
	case "consulta_reducida":
		header("location: menues/menuConsultaReducida.php");
    	break;
    case "consulta_prestador":
        header("location: menues/menuConsultaPrestador.php");
        break;
    case "equipo_interdisciplinario":
        header("location: menues/menuEI.php");
        break;
	case "admin":
		header("location: menues/menuAdmin.php");
    	break;			
    default:
		header("location: menues/menuConsultaReducida.php");
   }

?>
