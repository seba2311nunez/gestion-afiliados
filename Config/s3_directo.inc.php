<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function s3_directo_private_config(){
    $config = servicio_privado_institucion('s3_directo', true);
    return servicio_privado_validar($config, array('secret', 'url'), 's3_directo');
}
?>
