<?php
/*
 * Resolucion central de configuraciones privadas.
 * Este archivo es versionable: nunca debe contener credenciales.
 * Compatible con el PHP antiguo utilizado por el proyecto.
 */

function servicio_privado_definiciones(){
    return array(
        'database' => array('archivo' => 'database.json', 'entorno' => 'DATABASE_CONFIG_FILE'),
        'aws_s3' => array('archivo' => 'aws_s3.json', 'entorno' => 'AWS_S3_CONFIG_FILE'),
        'openai' => array('archivo' => 'openai.json', 'entorno' => 'OPENAI_CONFIG_FILE'),
        'rds' => array('archivo' => 'rds.json', 'entorno' => 'RDS_CONFIG_FILE'),
        'ftp_sss' => array('archivo' => 'ftp_sss.json', 'entorno' => 'SSS_FTP_CONFIG_FILE'),
        's3_directo' => array('archivo' => 's3_directo.json', 'entorno' => 'S3_DIRECTO_CONFIG_FILE'),
        'revision_opciones' => array('archivo' => 'revision_opciones.json', 'entorno' => 'SSS_REVISION_OPCIONES_CONFIG_FILE')
    );
}

function servicio_privado_document_root(){
    if(!isset($_SERVER['DOCUMENT_ROOT']) || !$_SERVER['DOCUMENT_ROOT']) return false;
    return realpath($_SERVER['DOCUMENT_ROOT']);
}

function servicio_privado_ruta($servicio){
    $definiciones = servicio_privado_definiciones();
    if(!isset($definiciones[$servicio])) throw new Exception('Servicio privado desconocido: '.$servicio);

    $definicion = $definiciones[$servicio];
    $configurada = getenv($definicion['entorno']);
    if($configurada) return $configurada;

    $archivo = $definicion['archivo'];
    $esWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $candidatas = array();
    if($esWindows){
        $candidatas[] = 'C:'.DIRECTORY_SEPARATOR.'xampp'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'gestion-afiliados'.DIRECTORY_SEPARATOR.$archivo;
        $candidatas[] = 'C:'.DIRECTORY_SEPARATOR.'xampp'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$archivo;
        if($servicio === 'rds') $candidatas[] = 'C:'.DIRECTORY_SEPARATOR.'xampp'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'rds_auditoria.json';
    } else {
        $homeUsuario = getenv('HOME');
        if($homeUsuario){
            $configUsuario = rtrim($homeUsuario, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'config';
            $candidatas[] = $configUsuario.DIRECTORY_SEPARATOR.'gestion-afiliados'.DIRECTORY_SEPARATOR.$archivo;
            $candidatas[] = $configUsuario.DIRECTORY_SEPARATOR.$archivo;
        }
        $documentRootServidor = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : '';
        $posicionDomains = strpos($documentRootServidor, '/domains/');
        if($posicionDomains !== false){
            $homeDesdeDocumentRoot = substr($documentRootServidor, 0, $posicionDomains);
            if($homeDesdeDocumentRoot !== ''){
                $configDesdeDocumentRoot = rtrim($homeDesdeDocumentRoot, '/').'/config';
                $candidatas[] = $configDesdeDocumentRoot.'/gestion-afiliados/'.$archivo;
                $candidatas[] = $configDesdeDocumentRoot.'/'.$archivo;
            }
        }
        $candidatas[] = DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'sistema.obra.social'.DIRECTORY_SEPARATOR.$archivo;
        if($servicio === 'rds') $candidatas[] = DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'sistema.obra.social'.DIRECTORY_SEPARATOR.'rds_auditoria.json';
        $candidatas[] = DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$archivo;
    }

    $documentRoot = servicio_privado_document_root();
    if($documentRoot) $candidatas[] = dirname($documentRoot).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$archivo;
    foreach(array_unique($candidatas) as $ruta){
        if(is_file($ruta) && is_readable($ruta)) return $ruta;
    }
    return count($candidatas) ? $candidatas[0] : '';
}

function servicio_privado_archivo($servicio){
    static $cache = array();
    if(isset($cache[$servicio])) return $cache[$servicio];

    $ruta = servicio_privado_ruta($servicio);
    if(!$ruta || !is_file($ruta) || !is_readable($ruta)){
        throw new Exception('No se encontro la configuracion privada de '.$servicio.'. Ruta esperada: '.$ruta);
    }

    $rutaReal = realpath($ruta);
    $documentRoot = servicio_privado_document_root();
    if($documentRoot && $rutaReal && strpos(strtolower($rutaReal), strtolower($documentRoot.DIRECTORY_SEPARATOR)) === 0){
        throw new Exception('La configuracion de '.$servicio.' debe estar fuera del directorio web.');
    }

    $json = json_decode(file_get_contents($ruta), true);
    if(!is_array($json)) throw new Exception('La configuracion privada de '.$servicio.' no contiene JSON valido.');
    $cache[$servicio] = $json;
    return $json;
}

function servicio_privado_institucion($servicio, $permitirDefault){
    $json = servicio_privado_archivo($servicio);
    $instancia = defined('INST_NAME') ? strtolower(trim(INST_NAME)) : '';
    $rnos = defined('INST_RNOS') ? trim((string) INST_RNOS) : '';
    if($instancia !== '' && isset($json[$instancia]) && is_array($json[$instancia])) return $json[$instancia];
    if($rnos !== '' && isset($json[$rnos]) && is_array($json[$rnos])) return $json[$rnos];
    if($permitirDefault && isset($json['default']) && is_array($json['default'])) return $json['default'];
    throw new Exception('No hay configuracion de '.$servicio.' para la institucion activa.');
}

function servicio_privado_validar($config, $campos, $servicio){
    foreach($campos as $campo){
        if(!isset($config[$campo]) || trim((string) $config[$campo]) === ''){
            throw new Exception('Falta '.$campo.' en la configuracion privada de '.$servicio.'.');
        }
    }
    return $config;
}
