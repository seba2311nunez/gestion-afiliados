<?php
/*
 * Cargador de credenciales privadas AWS S3.
 *
 * Este archivo puede versionarse porque no contiene secretos. El JSON real
 * debe residir fuera de htdocs. La ruta puede sobrescribirse mediante
 * AWS_S3_CONFIG_FILE.
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function aws_s3_config_path(){
    return servicio_privado_ruta('aws_s3');
    /*
    $configurada = getenv('AWS_S3_CONFIG_FILE');
    if($configurada) return $configurada;

    $esWindows = strtoupper(substr(PHP_OS,0,3)) === 'WIN';
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $candidatas = array();

    if($esWindows){
        $candidatas[] = 'C:'.DIRECTORY_SEPARATOR.'xampp'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'aws_s3.json';
    } else {
        $candidatas[] = DIRECTORY_SEPARATOR.'etc'.DIRECTORY_SEPARATOR.'sistema.obra.social'.DIRECTORY_SEPARATOR.'aws_s3.json';
        $candidatas[] = DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'aws_s3.json';
    }
    if($documentRoot) $candidatas[] = dirname($documentRoot).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'aws_s3.json';

    foreach(array_unique($candidatas) as $ruta){
        if(is_file($ruta) && is_readable($ruta)) return $ruta;
    }
    return count($candidatas) ? $candidatas[0] : ''; */
}

function aws_s3_private_config(){
    static $config = null;
    if($config !== null) return $config;

    $entrada = servicio_privado_institucion('aws_s3', true);
    servicio_privado_validar($entrada, array('key','secret','region'), 'aws_s3');
    $config = $entrada;
    return $config;
    /*
    $ruta = aws_s3_config_path();
    if(!$ruta || !is_file($ruta) || !is_readable($ruta)){
        throw new Exception('No se encontro la configuracion privada AWS S3. Ruta esperada: '.$ruta);
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $rutaReal = realpath($ruta);
    if($documentRoot && $rutaReal && strpos(strtolower($rutaReal),strtolower($documentRoot.DIRECTORY_SEPARATOR))===0){
        throw new Exception('La configuracion AWS S3 debe estar fuera del directorio web.');
    }

    $json = json_decode(file_get_contents($ruta),true);
    if(!is_array($json)) throw new Exception('La configuracion privada AWS S3 no contiene JSON valido.');

    $instancia = defined('INST_NAME') ? strtolower(trim(INST_NAME)) : '';
    $rnos = defined('INST_RNOS') ? trim((string)INST_RNOS) : '';
    $entrada = isset($json[$instancia]) ? $json[$instancia] : (isset($json[$rnos]) ? $json[$rnos] : (isset($json['default']) ? $json['default'] : null));
    if(!is_array($entrada)) throw new Exception("No hay configuracion AWS S3 para la institucion activa.");

    foreach(array('key','secret','region') as $campo){
        if(!isset($entrada[$campo]) || trim((string)$entrada[$campo])==='') throw new Exception("Falta {$campo} en la configuracion AWS S3.");
    }
    $config = $entrada;
    return $config; */
}

$awsS3Config = aws_s3_private_config();
if(!defined('AWS_S3_KEY')) define('AWS_S3_KEY',$awsS3Config['key']);
if(!defined('AWS_S3_SECRET')) define('AWS_S3_SECRET',$awsS3Config['secret']);
if(!defined('AWS_S3_REGION')) define('AWS_S3_REGION',$awsS3Config['region']);
if(isset($awsS3Config['bucket']) && !defined('AWS_S3_BUCKET')) define('AWS_S3_BUCKET',$awsS3Config['bucket']);

?>
