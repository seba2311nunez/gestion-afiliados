<?php
/* Cargador seguro de OpenAI. El JSON real debe residir fuera de htdocs. */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function openai_config_path(){
    return servicio_privado_ruta('openai');
}

function openai_private_config(){
    static $config=null;
    if($config!==null) return $config;
    $entrada=servicio_privado_institucion('openai',true);
    servicio_privado_validar($entrada,array('api_key'),'openai');
    if(empty($entrada['model'])) $entrada['model']='gpt-5.6-sol';
    $config=$entrada;
    return $config;
}
?>
