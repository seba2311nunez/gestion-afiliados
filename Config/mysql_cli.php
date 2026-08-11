<?php
/* Ejecutor CLI para exportaciones legadas, sin credenciales en argumentos. */
if(PHP_SAPI!=='cli'){ http_response_code(403); exit('Solo CLI'); }
if($argc!==2){ fwrite(STDERR,"Uso: php mysql_cli.php SQL\n"); exit(2); }
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'database.inc.php';
$config=database_private_config();
$mysqli=@new mysqli($config['host'],$config['usuario'],$config['clave'],null,intval($config['port']));
if($mysqli->connect_error){ fwrite(STDERR,"No fue posible conectar con la base configurada.\n"); exit(3); }
$mysqli->set_charset($config['charset']);
$resultado=$mysqli->query($argv[1]);
if($resultado===false){ fwrite(STDERR,"La consulta no pudo ejecutarse.\n"); exit(4); }
if($resultado===true) exit(0);
$campos=$resultado->fetch_fields();
$encabezado=array();
foreach($campos as $campo) $encabezado[]=$campo->name;
echo implode("\t",$encabezado)."\n";
while($fila=$resultado->fetch_row()){
    foreach($fila as &$valor) $valor=str_replace(array("\t","\r","\n"),' ',(string)$valor);
    unset($valor);
    echo implode("\t",$fila)."\n";
}
?>
