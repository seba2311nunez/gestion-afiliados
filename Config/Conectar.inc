<?php
/* Bootstrap central sin secretos. Conserva el contrato del codigo legado. */
if(session_id() === '') session_start();
error_reporting(0);
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'database.inc.php';

$databaseConfig=database_private_config();
$conexion=database_private_connect(null);
if(!$conexion){
    error_log('No fue posible conectar con la base configurada en '.servicio_privado_ruta('database'));
    echo '<CENTER>Problemas de conexion con la base de datos.</CENTER>'; exit();
}

$idLogSistema=isset($_SESSION['x']) ? intval($_SESSION['x']) : 0;
if($idLogSistema<=0){
    error_log('Conectar.inc: no existe una institucion activa en la sesion.');
    echo '<CENTER>Sesion o institucion no valida.</CENTER>'; exit();
}
$query="SELECT o.* FROM logs_sistemas.log_os l JOIN logs_sistemas.obra_social o ON l.id_os=o.id WHERE l.id=".$idLogSistema;
$result=mysql_query($query,$conexion);
$d=$result ? mysql_fetch_object($result) : false;
if(!$d){
    error_log('Conectar.inc: no se encontro la configuracion institucional para log_os id '.$idLogSistema);
    echo '<CENTER>No se encontro la configuracion de la institucion activa.</CENTER>'; exit();
}

$base=$d->inst_name;
$base_padron=$base.'_padron';
$base_ppdev=$base.'_ppdev';
$base_dev=$base.'_dev';
$base_smadm=$base.'_smadm';
$base_imagenes=$base.'_imagenes';
$base_usuarios=$base.'_usuarios';
$base_logs=$base.'_logs';
$base_presupuesto=$base.'_presupuesto';
$base_historicos=$base.'_historicos';
$base_liquidacion=$base.'_liquidacion';
$base_fiscalizacion=$base.'_fiscalizacion';
$base_tmp=$base.'_tmp';

if(!defined('N_BASE')) define('N_BASE',$base);
if(!defined('N_BASE_PADRON')) define('N_BASE_PADRON',$base.'_padron');
if(!defined('N_BASE_PPDEV')) define('N_BASE_PPDEV',$base.'_ppdev');
if(!defined('N_BASE_SMADM')) define('N_BASE_SMADM',$base.'_smadm');
if(!defined('N_BASE_IMAGENES')) define('N_BASE_IMAGENES',$base.'_imagenes');
if(!defined('N_BASE_USUARIOS')) define('N_BASE_USUARIOS',$base.'_usuarios');
if(!defined('N_BASE_LOGS')) define('N_BASE_LOGS',$base.'_logs');
if(!defined('N_BASE_PRESUPUESTO')) define('N_BASE_PRESUPUESTO',$base.'_presupuesto');
if(!defined('N_BASE_HISTORICOS')) define('N_BASE_HISTORICOS',$base.'_historicos');
if(!defined('N_BASE_LIQUIDACION')) define('N_BASE_LIQUIDACION',$base.'_liquidacion');
if(!defined('N_BASE_FISCALIZACION')) define('N_BASE_FISCALIZACION',$base.'_fiscalizacion');
if(!defined('N_BASE_DEV')) define('N_BASE_DEV',$base.'_dev');
if(!defined('N_BASE_TMP')) define('N_BASE_TMP',$base.'_tmp');

if(!defined('DOMINIO')) define('DOMINIO',$d->dominio);
$_SESSION['DOMINIO']=DOMINIO;
if(!defined('INST_ID')) define('INST_ID',$d->id);
if(!defined('INST_NAME')) define('INST_NAME',$d->inst_name);
if(!defined('INST_NAME_F')) define('INST_NAME_F',$d->inst_name_f);
if(!defined('INST_DIR')) define('INST_DIR',$d->inst_dir);
if(!defined('INST_PROV')) define('INST_PROV',$d->inst_prov);
if(!defined('INST_LOC')) define('INST_LOC',$d->inst_loc);
if(!defined('INST_CP')) define('INST_CP',$d->inst_cp);
if(!defined('INST_CUIT')) define('INST_CUIT',$d->inst_cuit);
if(!defined('INST_CUIT_F')) define('INST_CUIT_F',$d->inst_cuit_f);
if(!defined('INST_RNOS')) define('INST_RNOS',$d->inst_rnos);
if(!defined('INST_RNOS_MIN')) define('INST_RNOS_MIN',$d->inst_rnos_min);
if(!defined('INST_TEL')) define('INST_TEL',$d->inst_tel);
if(!defined('INST_DOCUMENTACION_URL')) define('INST_DOCUMENTACION_URL',$d->inst_documentacion_url);
if(!defined('INST_SUPERVISA_NOVEDADES')) define('INST_SUPERVISA_NOVEDADES',$d->inst_supervisa_novedades);
if(!defined('INST_VGP_NRO_SIND')) define('INST_VGP_NRO_SIND',$d->inst_vgp_nro_sind);
if(!defined('INST_VGP_PATOLOGIA')) define('INST_VGP_PATOLOGIA',$d->inst_vgp_patologia);
if(!defined('INST_USA_VER_ULTIMOS_PROPIOS')) define('INST_USA_VER_ULTIMOS_PROPIOS',$d->inst_usa_ver_ultimos_propios);
if(!defined('INST_DEFAULT_PERSONA')) define('INST_DEFAULT_PERSONA',$d->d_id_persona);
if(!defined('INST_DEFAULT_NOMENCLADOR')) define('INST_DEFAULT_NOMENCLADOR',$d->d_id_nomenclador);

mysql_select_db($base_padron,$conexion);
mysql_set_charset($databaseConfig['charset'],$conexion);
header('Content-Type: text/html; charset=utf-8');
foreach($_REQUEST as $var=>$value){
    $_GLOABLS[$var]=$value;
    $$var=mysql_real_escape_string($value,$conexion);
}
if(!function_exists('shutdown')){
    function shutdown(){ global $_SESSION; $_SESSION=array_merge($_SESSION,$_SESSION); }
    register_shutdown_function('shutdown');
}
if(!function_exists('session_register')){ function session_register($arg){} }
?>
