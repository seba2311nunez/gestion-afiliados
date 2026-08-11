<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

function database_private_config(){
    $config=servicio_privado_institucion('database',true);
    servicio_privado_validar($config,array('host','usuario','clave'),'database');
    if(!isset($config['port']) || !intval($config['port'])) $config['port']=3306;
    if(!isset($config['charset']) || trim($config['charset'])==='') $config['charset']='utf8';
    return $config;
}

function database_private_host($config){
    $host=$config['host'];
    if(intval($config['port'])!==3306) $host.=':'.intval($config['port']);
    return $host;
}

function database_private_connect($baseSeleccionada){
    $config=database_private_config();
    $conexion=mysql_connect(database_private_host($config),$config['usuario'],$config['clave']);
    if(!$conexion) return false;
    if($baseSeleccionada!==null && $baseSeleccionada!=='') mysql_select_db($baseSeleccionada,$conexion);
    mysql_set_charset($config['charset'],$conexion);
    return $conexion;
}
?>
