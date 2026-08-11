<?php
/* Resuelve Composer tanto en XAMPP como en Linux/Hostinger. */
function composer_autoload_path(){
    $configurada=getenv('COMPOSER_AUTOLOAD_FILE');
    $candidatas=array();
    if($configurada) $candidatas[]=$configurada;
    $candidatas[]=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    $candidatas[]=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    $candidatas[]='/var/www/html/sistema.obra.social/vendor/autoload.php';
    foreach(array_unique($candidatas) as $ruta){
        if(is_file($ruta) && is_readable($ruta)) return $ruta;
    }
    throw new Exception('No se encontro vendor/autoload.php. Puede definir COMPOSER_AUTOLOAD_FILE.');
}
require_once composer_autoload_path();
?>
