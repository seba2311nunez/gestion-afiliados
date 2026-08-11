<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function revision_opciones_private_config(){
    $config = servicio_privado_institucion('revision_opciones', false);
    return servicio_privado_validar($config, array('usuario','clave'), 'revision_opciones');
}

