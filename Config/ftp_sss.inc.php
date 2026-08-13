<?php
/*
 * Ubicacion de las credenciales FTPS de la SSS.
 *
 * Este archivo NO contiene secretos. El archivo JSON real debe vivir fuera
 * de htdocs y puede cambiarse con la variable SSS_FTP_CONFIG_FILE.
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function ftp_sss_config_path(){
    return servicio_privado_ruta('ftp_sss');
    /*
    $configurada = getenv('SSS_FTP_CONFIG_FILE');
    if($configurada) return $configurada;

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $esWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $candidatas = array();

    if($esWindows){
        $candidatas[] = 'C:'.DIRECTORY_SEPARATOR.'xampp'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'ftp_sss.json';
    } else {
        $candidatas[] = DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'sistema.obra.social'.DIRECTORY_SEPARATOR.'ftp_sss.json';
        $candidatas[] = DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'ftp_sss.json';
    }

    // Alternativa portable: una carpeta config hermana del DocumentRoot.
    if($documentRoot){
        $candidatas[] = dirname($documentRoot).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'ftp_sss.json';
    }

    foreach(array_unique($candidatas) as $ruta){
        if(is_file($ruta) && is_readable($ruta)) return $ruta;
    }

    // Devuelve la ruta principal para que el mensaje de error indique dónde crearla.
    return count($candidatas) ? $candidatas[0] : ''; */
}

function ftp_sss_estado_configuracion($instName, $instRnos){
    $ruta = ftp_sss_config_path();
    $estado = array('estado'=>'no_configurada','automatico'=>false,'manual'=>true,'mensaje'=>'');
    if(!$ruta || !is_file($ruta) || !is_readable($ruta)){
        $estado['mensaje'] = 'No se encontró configuración FTPS para esta obra social. Utilice la presentación manual.';
        return $estado;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $rutaReal = realpath($ruta);
    if($documentRoot && $rutaReal && strpos(strtolower($rutaReal), strtolower($documentRoot.DIRECTORY_SEPARATOR)) === 0){
        $estado['estado'] = 'invalida';
        $estado['mensaje'] = 'La configuración FTPS está dentro del directorio web y no puede utilizarse.';
        return $estado;
    }

    $config = json_decode(file_get_contents($ruta), true);
    if(!is_array($config)){
        $estado['estado'] = 'invalida';
        $estado['mensaje'] = 'El archivo privado FTPS no contiene JSON válido.';
        return $estado;
    }

    $claveInst = strtolower(trim($instName));
    $claveRnos = trim((string)$instRnos);
    $entrada = isset($config[$claveInst]) ? $config[$claveInst] : (isset($config[$claveRnos]) ? $config[$claveRnos] : null);
    if(!is_array($entrada)){
        $estado['mensaje'] = "No hay credenciales FTPS para {$claveInst} (RNOS {$claveRnos}). Utilice la presentación manual.";
        return $estado;
    }

    $clave = isset($entrada['clave']) ? strtolower(trim((string)$entrada['clave'])) : '';
    if(in_array($clave, array('no-compartida','no_compartida','no compartida'))){
        $estado['estado'] = 'no_compartida';
        $estado['mensaje'] = 'Esta obra social no comparte sus credenciales FTPS. El envío y las devoluciones deben gestionarse manualmente.';
        return $estado;
    }

    foreach(array('host','usuario','clave') as $campo){
        if(!isset($entrada[$campo]) || trim((string)$entrada[$campo])===''){
            $estado['estado'] = 'invalida';
            $estado['mensaje'] = "La configuración FTPS no tiene el campo {$campo}. Utilice la contingencia manual.";
            return $estado;
        }
    }
    if(isset($entrada['rnos']) && trim((string)$entrada['rnos']) !== $claveRnos){
        $estado['estado'] = 'invalida';
        $estado['mensaje'] = 'La configuración FTPS corresponde al RNOS '.trim((string)$entrada['rnos']).', pero la sesión usa '.$claveRnos.'.';
        return $estado;
    }

    return array('estado'=>'disponible','automatico'=>true,'manual'=>false,'mensaje'=>'Envío automático FTPS disponible.');
}

function ftp_sss_configuracion_disponible($instName, $instRnos, &$mensaje){
    $estado = ftp_sss_estado_configuracion($instName, $instRnos);
    $mensaje = $estado['mensaje'];
    return $estado['automatico'];
    /*
    $ruta = ftp_sss_config_path();
    if(!$ruta || !is_file($ruta) || !is_readable($ruta)){
        $mensaje = 'No se encontro la configuracion privada FTPS. Ruta esperada: '.$ruta;
        return false;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $rutaReal = realpath($ruta);
    if($documentRoot && $rutaReal && strpos(strtolower($rutaReal), strtolower($documentRoot.DIRECTORY_SEPARATOR)) === 0){
        $mensaje = 'La configuracion FTPS debe estar fuera del directorio web.';
        return false;
    }

    $config = json_decode(file_get_contents($ruta), true);
    if(!is_array($config)){
        $mensaje = 'El archivo privado FTPS no contiene JSON valido.';
        return false;
    }

    $claveInst = strtolower(trim($instName));
    $claveRnos = trim((string)$instRnos);
    $entrada = isset($config[$claveInst]) ? $config[$claveInst] : (isset($config[$claveRnos]) ? $config[$claveRnos] : null);
    if(!is_array($entrada)){
        $mensaje = "No hay credenciales FTPS para la obra social '{$claveInst}' (RNOS {$claveRnos}).";
        return false;
    }

    $requeridos = array('host','usuario','clave');
    foreach($requeridos as $campo){
        if(!isset($entrada[$campo]) || trim((string)$entrada[$campo])===''){
            $mensaje = "La configuracion FTPS de '{$claveInst}' no tiene el campo {$campo}.";
            return false;
        }
    }
    if(isset($entrada['rnos']) && trim((string)$entrada['rnos']) !== $claveRnos){
        $mensaje = 'La configuracion FTPS de '.$claveInst.' corresponde al RNOS '
            .trim((string)$entrada['rnos']).', pero la sesion activa usa el RNOS '.$claveRnos.'.';
        return false;
    }

    $mensaje = '';
    return true; */
}

function ftp_sss_credenciales($instName, $instRnos){
    $json = servicio_privado_archivo('ftp_sss');
    $claveInst = strtolower(trim($instName));
    $claveRnos = trim((string)$instRnos);
    $entrada = isset($json[$claveInst]) ? $json[$claveInst] : (isset($json[$claveRnos]) ? $json[$claveRnos] : null);
    if(!is_array($entrada)) throw new Exception('No hay credenciales FTPS para la obra social activa.');
    foreach(array('host','usuario','clave') as $campo){
        if(!isset($entrada[$campo]) || trim((string)$entrada[$campo]) === ''){
            throw new Exception('La configuracion FTPS no tiene el campo '.$campo.'.');
        }
    }
    if(isset($entrada['rnos']) && trim((string)$entrada['rnos']) !== $claveRnos){
        throw new Exception('La configuracion FTPS no corresponde al RNOS de la sesion.');
    }
    return $entrada;
}

?>
