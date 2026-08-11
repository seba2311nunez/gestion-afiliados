<?php
/*
 * Punto unico para auto_prepend_file. No carga secretos hasta que un script
 * solicita un servicio mediante las funciones de Config/*.inc.php.
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'servicios_privados.inc.php';

