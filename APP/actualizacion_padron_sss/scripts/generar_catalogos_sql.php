<?php
/* Genera documentación SQL del submodulo desde la base activa. */
ini_set('display_errors',0);
error_reporting(E_ALL);
include(__DIR__.'/../../../Config/Conectar.inc');

$esCli = php_sapi_name()==='cli';
if(!$esCli) header('Content-Type: application/json; charset=UTF-8');
if(!$esCli && (!isset($_SESSION['id_user']) || !$_SESSION['id_user'])){
    http_response_code(401);
    echo json_encode(array('status'=>'error','mensaje'=>'Sesion requerida.'));
    exit;
}

$instancia = '';
if($esCli){
    foreach($argv as $arg) if(strpos($arg,'--inst=')===0) $instancia=substr($arg,7);
    if($instancia===''){ echo json_encode(array('status'=>'error','mensaje'=>'Usar --inst=nombre.')); exit(1); }
    $rsInst=mysql_query("SELECT inst_name FROM logs_sistemas.obra_social WHERE inst_name='".mysql_real_escape_string($instancia)."' LIMIT 1");
    if(!$rsInst || !($rowInst=mysql_fetch_assoc($rsInst))){ echo json_encode(array('status'=>'error','mensaje'=>'Institucion inexistente.')); exit(1); }
    $instancia=$rowInst['inst_name'];
} else {
    $instancia=N_BASE;
}
$basePadron = $instancia.'_padron';
$baseHistoricos = $instancia.'_historicos';
$baseUsuarios = $instancia.'_usuarios';
$salidaDir = dirname(__DIR__).DIRECTORY_SEPARATOR.'database';

$objetos = array(
    array($baseHistoricos,'lotes'), array($baseHistoricos,'novedades_exportables'),
    array($baseHistoricos,'novedades_sss_errores'), array($baseHistoricos,'novedades_sss_aceptados'),
    array($baseHistoricos,'novedades_sss_rechazados'), array($baseHistoricos,'novedades_exportables_comparacion'),
    array($baseHistoricos,'sss_catalogo_errores'), array($baseHistoricos,'sss_presentacion_control'),
    array($baseHistoricos,'sss_afiliado_cronologia'), array($baseHistoricos,'sss_cronograma_ftp'),
    array($basePadron,'persona'), array($basePadron,'afiliados'), array($basePadron,'desreguladoras'),
    array($basePadron,'parentesco'), array($basePadron,'tipo_beneficiario_titular'),
    array($basePadron,'lst_novedades_presentaciones'), array($basePadron,'tmp_novedades'),
    array($basePadron,'tmp_afiliados_novedades_mostrar'), array($basePadron,'tmp_cronologia_novedades'),
    array($basePadron,'tmp_afiliados_nov_padronsss_insertar'), array($basePadron,'log_eventos')
);
if($baseUsuarios!=='') $objetos[] = array($baseUsuarios,'users');

function sqlIdent($valor){ return '`'.str_replace('`','``',$valor).'`'; }
function sqlTexto($valor){ return "'".mysql_real_escape_string($valor)."'"; }
function valorEjemplo($columna,$valor,$fila){
    if($valor===null) return 'NULL';
    $c = strtolower($columna);
    if(preg_match('/pass|clave|secret|token|api.?key|credencial|hash|cbu|cuenta|user_ftp/',$c)) return sqlTexto('[REDACTED]');
    if(preg_match('/mail|correo/',$c)) return sqlTexto('persona'.$fila.'@example.invalid');
    if(preg_match('/telefono|celular/',$c)) return sqlTexto('0000000000');
    if(preg_match('/apellido|nombre|ayn|razon_social|usuario/',$c)) return sqlTexto('PERSONA EJEMPLO '.$fila);
    if(preg_match('/calle|domicilio|direccion|localidad|observacion|comentario|texto_entero|query_where|filtros_cabecera|otros_parametros|ultimo_error/',$c)) return sqlTexto('DATO ANONIMIZADO');
    if($c==='ip') return sqlTexto('127.0.0.1');
    if(in_array($c,array('numero','piso','dto','depto'))) return sqlTexto('0');
    if(preg_match('/cuil|cuit/',$c)) return sqlTexto('2000000000'.$fila);
    if(preg_match('/(^|_)(dni|nd|documento|nro_doc)/',$c)) return sqlTexto('9000000'.$fila);
    if($c==='id' || strpos($c,'id_')===0) return (string)(1000+$fila);
    if(in_array($c,array('nben','gpar'))) return sqlTexto('EJEMPLO'.$fila);
    if(is_bool($valor)) return $valor ? '1' : '0';
    if(is_numeric($valor) && !preg_match('/^0[0-9]+$/',(string)$valor)) return (string)$valor;
    return sqlTexto((string)$valor);
}

$tablasSql = "-- Catalogo reproducible de tablas del submodulo actualizacion_padron_sss\n";
$tablasSql .= "-- Generado: ".date('c').". Las filas son muestras anonimizadas y no deben usarse como respaldo.\n\n";
$vistas = array(); $incluidos = array(); $omitidos = array();

foreach($objetos as $objeto){
    list($base,$tabla) = $objeto;
    $nombre = sqlIdent($base).'.'.sqlIdent($tabla);
    $rsTipo = mysql_query("SELECT TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA=".sqlTexto($base)." AND TABLE_NAME=".sqlTexto($tabla)." LIMIT 1");
    if(!$rsTipo || !($tipo=mysql_fetch_assoc($rsTipo))){ $omitidos[]=$base.'.'.$tabla; continue; }
    $comandoCreate = $tipo['TABLE_TYPE']==='VIEW' ? 'SHOW CREATE VIEW' : 'SHOW CREATE TABLE';
    $rsCreate = mysql_query($comandoCreate." {$nombre}");
    if(!$rsCreate || !($create=mysql_fetch_assoc($rsCreate))){ $omitidos[]=$base.'.'.$tabla; continue; }
    $definicion = isset($create['Create Table']) ? $create['Create Table'] : $create['Create View'];
    $definicion = preg_replace('/DEFINER=`[^`]+`@`[^`]+`\s*/i','',$definicion);
    if($tipo['TABLE_TYPE']==='VIEW'){ $vistas[] = array($base,$tabla,$definicion); $incluidos[]=$base.'.'.$tabla.' (VIEW)'; continue; }

    $tablasSql .= "-- ============================================================\n-- {$base}.{$tabla}\n-- ============================================================\n";
    $tablasSql .= "-- Definicion observada (no ejecutar sobre una base existente sin revision).\n{$definicion};\n\n";
    $rsMuestra = mysql_query("SELECT * FROM {$nombre} LIMIT 3");
    $numero=0;
    if($rsMuestra){
        while($fila=mysql_fetch_assoc($rsMuestra)){
            $numero++;
            $columnas=array(); $valores=array();
            foreach($fila as $columna=>$valor){ $columnas[]=sqlIdent($columna); $valores[]=valorEjemplo($columna,$valor,$numero); }
            $tablasSql .= "INSERT INTO {$nombre} (".implode(',',$columnas).") VALUES (".implode(',',$valores).");\n";
        }
    }
    if($numero===0) $tablasSql .= "-- Sin filas de ejemplo disponibles.\n";
    $tablasSql .= "\n"; $incluidos[]=$base.'.'.$tabla;
}

$rutinas = array(
    'novedades_crea_nuevo_periodo','NOV_nuevo_periodo_automatico','NOV_presentar_periodo',
    'NOV_mostrar_lote','NOV_mostrar_lote_incluir_errores','novedades_envio_presentaciones',
    'novedades_cronologia','Padron_sss_comparativo_lst_control','NOV_agrega_rechazos_periodo_actual'
);
$rutinasSql = "-- Stored procedures y vistas utilizados por actualizacion_padron_sss\n-- Generado: ".date('c')."\n\nDELIMITER $$\n\n";
$rutinasIncluidas=array(); $rutinasOmitidas=array();
foreach($rutinas as $rutina){
    $encontrada=false;
    foreach(array_unique(array($basePadron,$baseHistoricos)) as $base){
        $rs = mysql_query("SHOW CREATE PROCEDURE ".sqlIdent($base).'.'.sqlIdent($rutina));
        if($rs && ($row=mysql_fetch_assoc($rs))){
            $create = isset($row['Create Procedure']) ? $row['Create Procedure'] : '';
            $create = preg_replace('/DEFINER=`[^`]+`@`[^`]+`\s*/i','',$create);
            if($create!==''){
                $rutinasSql .= "DROP PROCEDURE IF EXISTS ".sqlIdent($base).'.'.sqlIdent($rutina)."$$\n{$create}$$\n\n";
                $rutinasIncluidas[]=$base.'.'.$rutina; $encontrada=true; break;
            }
        }
    }
    if(!$encontrada) $rutinasOmitidas[]=$rutina;
}
$rutinasSql .= "DELIMITER ;\n\n";
foreach($vistas as $vista){
    $rutinasSql .= "DROP VIEW IF EXISTS ".sqlIdent($vista[0]).'.'.sqlIdent($vista[1]).";\n".$vista[2].";\n\n";
}

$archivoTablas=$salidaDir.DIRECTORY_SEPARATOR.'002_tablas_y_ejemplos.sql';
$archivoRutinas=$salidaDir.DIRECTORY_SEPARATOR.'003_storeds_y_vistas.sql';
if(file_put_contents($archivoTablas,$tablasSql)===false || file_put_contents($archivoRutinas,$rutinasSql)===false){
    http_response_code(500); echo json_encode(array('status'=>'error','mensaje'=>'No se pudieron escribir los catalogos SQL.')); exit;
}
echo json_encode(array('status'=>'ok','tablas'=>$incluidos,'tablas_omitidas'=>$omitidos,'rutinas'=>$rutinasIncluidas,'rutinas_omitidas'=>$rutinasOmitidas,'archivos'=>array(basename($archivoTablas),basename($archivoRutinas))));
?>
