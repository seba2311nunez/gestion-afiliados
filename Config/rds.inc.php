<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function rds_private_config($entorno){
    $json = servicio_privado_archivo('rds');
    if($entorno === 'produccion' && !isset($json[$entorno]) && isset($json['origen'])) $entorno = 'origen';
    if($entorno === 'desarrollo' && !isset($json[$entorno]) && isset($json['destino'])) $entorno = 'destino';
    if(!isset($json[$entorno]) || !is_array($json[$entorno])){
        throw new Exception('No hay configuracion RDS para el entorno '.$entorno.'.');
    }
    return servicio_privado_validar($json[$entorno], array('host','port','usuario','clave'), 'rds/'.$entorno);
}
