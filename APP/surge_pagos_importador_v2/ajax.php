<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
    Importador SURGE Pagos

    Tablas usadas (todas en $base_historicos, igual que el resto del
    sistema - lotes/surge_pagos son transaccionales/historicas, no van
    en $base_padron):
      - $base_historicos.lotes       (ya existe, se reusa proceso='surge_pagos')
      - $base_historicos.surge_pagos (detalle importado por lote)

    El cruce por CUIL contra el padron real (persona -> afiliados ->
    desreguladoras) lo hace el stored procedure
    prueba.surge_pagos_cruzar_red_prestacion (ver
    surge_pagos_cruzar_red_prestacion.sql), deployado una sola vez en el
    esquema compartido "prueba" para servir a cualquier obra social.

    Mapeo de columnas del archivo real de SURGE (14 columnas, A-N):
      A Nro. Solicitud      -> solicitud
      B Patologia           -> patologia
      C Estado              -> estado_pago       (columna nueva, ver
                                                    surge_pagos_alter_tabla.sql)
      D RNOS                -> rnos
      E Obra Social         -> obra_social
      F CUIL                -> cuil
      G Periodo             -> periodo
      H Monto Solicitado    -> monto_solicitado  (columna nueva)
      I Monto Reintegro     -> importe
      J Tipo                -> tipo
      K Fecha Creacion      -> fecha_creacion    (columna nueva)
      L Fecha aceptacion    -> fecha_aceptacion  (columna nueva)
      M Fecha de Pago       -> fecha_pago
      N Nro expte GDE       -> expediente_gde

    Importacion en 3 pasos (evita timeouts y permite mostrar progreso
    real en el navegador, mismo patron que el modal de FTP a SSS):
      1. iniciar_importacion_surge_pagos  -> sube el archivo, crea el
         lote, guarda el archivo en un temporal propio del modulo y
         devuelve id_lote + total de filas.
      2. procesar_chunk_surge_pagos       -> se llama repetidas veces
         desde el navegador (de a N filas), hace 1 sola query de INSERT
         multi-fila por llamada. El navegador va mostrando el % segun
         la fila alcanzada.
      3. finalizar_importacion_surge_pagos -> corre el cruce contra el
         padron, calcula totales, borra el archivo temporal y devuelve
         el resumen final.
*/

// Conectar.inc ya hace su propio session_start() (linea 2). Llamarlo
// tambien aca generaba el Notice "session had already been started",
// que HTML-contaminaba la respuesta JSON y rompia el parseo en el
// navegador (por eso quedaban lotes trabados en IMPORTANDO).
include('../../Config/Conectar.inc');
require_once('../../Lib/PHPExcel/Classes/PHPExcel/IOFactory.php');

$base_historicos = isset($base_historicos) ? $base_historicos : 'ppp1_historicos';
$base_padron = isset($base_padron) ? $base_padron : 'ppp1_padron';

$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';

function json_out($arr){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr);
    exit;
}

function q($sql){
    $r = mysql_query($sql);
    if(!$r){ json_out(array('ok'=>false, 'error'=>mysql_error(), 'sql'=>$sql)); }
    return $r;
}

function esc($v){ return mysql_real_escape_string(trim((string)$v)); }
function sql_str($v){ return ($v === null || trim((string)$v) === '') ? 'NULL' : "'".esc($v)."'"; }
function sql_num($v){
    if($v === null || trim((string)$v) === '') return '0';
    if(is_numeric($v)) return str_replace(',', '.', $v);
    $s = trim((string)$v);
    $s = str_replace(array('$',' '), '', $s);
    if(strpos($s, ',') !== false){ $s = str_replace('.', '', $s); $s = str_replace(',', '.', $s); }
    return is_numeric($s) ? $s : '0';
}
function solo_num($v){
    $s = preg_replace('/[^0-9]/', '', (string)$v);
    return $s === '' ? null : $s;
}

function tabla_existe($tabla_completa){
    $rs = @mysql_query("SHOW TABLES FROM ".extraer_base($tabla_completa)." LIKE '".esc(extraer_tabla($tabla_completa))."'");
    return ($rs && mysql_num_rows($rs) > 0);
}
function extraer_base($tabla_completa){
    $t = str_replace('`','',$tabla_completa);
    $p = explode('.', $t);
    return count($p) == 2 ? '`'.$p[0].'`' : 'DATABASE()';
}
function extraer_tabla($tabla_completa){
    $t = str_replace('`','',$tabla_completa);
    $p = explode('.', $t);
    return count($p) == 2 ? $p[1] : $p[0];
}

// Cacheado en memoria del proceso: antes se pedia SHOW COLUMNS por cada
// fila insertada (757 filas = 757 queries extra solo para esto). Ahora
// se pide 1 sola vez por tabla y por request.
function columnas_tabla($tabla_completa){
    static $cache = array();
    if(isset($cache[$tabla_completa])) return $cache[$tabla_completa];
    $cols = array();
    $rs = @mysql_query("SHOW COLUMNS FROM $tabla_completa");
    if($rs){ while($row = mysql_fetch_assoc($rs)){ $cols[$row['Field']] = true; } }
    $cache[$tabla_completa] = $cols;
    return $cols;
}

function insert_dinamico($tabla_completa, $datos_sql){
    $cols = columnas_tabla($tabla_completa);
    $campos = array(); $valores = array();
    foreach($datos_sql as $campo=>$valor_sql){
        if(isset($cols[$campo])){ $campos[] = '`'.$campo.'`'; $valores[] = $valor_sql; }
    }
    if(!count($campos)){ json_out(array('ok'=>false, 'error'=>'No hay columnas compatibles para insertar en '.$tabla_completa)); }
    q("INSERT INTO $tabla_completa (".implode(',', $campos).") VALUES (".implode(',', $valores).")");
    return mysql_insert_id();
}

// Insert multi-fila (1 sola query para N filas) para el detalle de
// surge_pagos. $filas es un array de arrays asociativos campo=>valor_sql
// (todas las filas con el mismo conjunto de claves).
function insertar_filas_surge_pagos_bulk($base_historicos, $filas){
    if(!count($filas)) return 0;
    $tabla = '`'.$base_historicos.'`.`surge_pagos`';
    $cols = columnas_tabla($tabla);

    $campos = array();
    foreach(array_keys($filas[0]) as $campo){
        if(isset($cols[$campo])) $campos[] = $campo;
    }
    if(!count($campos)){ json_out(array('ok'=>false, 'error'=>'No hay columnas compatibles para insertar en '.$tabla)); }

    $tuplas = array();
    foreach($filas as $fila){
        $valores = array();
        foreach($campos as $campo){ $valores[] = isset($fila[$campo]) ? $fila[$campo] : 'NULL'; }
        $tuplas[] = '('.implode(',', $valores).')';
    }

    $sql = "INSERT INTO $tabla (`".implode('`,`', $campos)."`) VALUES ".implode(',', $tuplas);
    q($sql);
    return count($filas);
}

function update_lote_dinamico($base_historicos, $id_lote, $datos_sql){
    $cols = columnas_tabla('`'.$base_historicos.'`.`lotes`');
    $sets = array();
    foreach($datos_sql as $campo=>$valor_sql){ if(isset($cols[$campo])) $sets[] = '`'.$campo.'` = '.$valor_sql; }
    if(count($sets)) q("UPDATE `$base_historicos`.`lotes` SET ".implode(',', $sets)." WHERE id = ".intval($id_lote));
}

function normalizar_fecha_excel($value){
    if($value === null || $value === '') return null;
    if(is_numeric($value) && class_exists('PHPExcel_Shared_Date')){
        $ts = PHPExcel_Shared_Date::ExcelToPHP($value);
        return date('Y-m-d', $ts);
    }
    if($value instanceof DateTime) return $value->format('Y-m-d');
    $value = trim((string)$value);
    if($value === '') return null;
    foreach(array('d/m/Y','Y-m-d','d-m-Y','m/d/Y') as $fmt){
        $dt = DateTime::createFromFormat($fmt, $value);
        if($dt) return $dt->format('Y-m-d');
    }
    $ts = strtotime($value);
    return $ts ? date('Y-m-d', $ts) : null;
}

function cargar_phpexcel(){
    if(class_exists('PHPExcel_IOFactory')) return;
    $posibles = array(
        __DIR__.'/../PHPExcel/Classes/PHPExcel/IOFactory.php',
        __DIR__.'/PHPExcel/Classes/PHPExcel/IOFactory.php',
        __DIR__.'/../../PHPExcel/Classes/PHPExcel/IOFactory.php',
        __DIR__.'/../../Lib/PHPExcel/Classes/PHPExcel/IOFactory.php'
    );
    foreach($posibles as $p){ if(file_exists($p)){ require_once($p); break; } }
    if(!class_exists('PHPExcel_IOFactory')) json_out(array('ok'=>false, 'error'=>'No se encontró PHPExcel_IOFactory. Ajustar el require_once en ajax.php.'));
}

function validar_extension($nombre){
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    return in_array($ext, array('xlsx','xls','xltx'));
}

// Carpeta temporal propia del modulo donde se guarda el archivo subido
// mientras dura la importacion por chunks (se borra al finalizar).
function ruta_tmp_dir(){
    $dir = __DIR__.'/tmp';
    if(!is_dir($dir)) @mkdir($dir, 0775, true);
    if(!file_exists($dir.'/.htaccess')){
        @file_put_contents($dir.'/.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    }
    return $dir;
}
function ruta_temp_lote($id_lote){
    return ruta_tmp_dir().'/surge_pagos_lote_'.intval($id_lote).'.xlsx';
}

function crear_lote($base_historicos, $nombre_archivo, $observacion, $id_usuario, $usuario){
    $id = insert_dinamico('`'.$base_historicos.'`.`lotes`', array(
        'lote' => sql_str('SURGE_PAGOS_'.date('Ymd_His')),
        'descripcion' => sql_str('Importación SURGE pagos'),
        'archivo' => sql_str($nombre_archivo),
        'cant_registros' => '0',
        'proceso' => sql_str('surge_pagos'),
        'usuario' => sql_str($usuario),
        'id_usuario' => intval($id_usuario),
        'estado' => sql_str('IMPORTANDO'),
        'texto_entero' => sql_str($observacion),
        'importe_calculado' => '0',
        'imp_renglon_resumen' => '0'
    ));
    return $id;
}

function cruzar_con_padron($base_padron, $base_historicos, $id_lote){
    // El cruce real (persona -> afiliados -> desreguladoras) lo hace el
    // stored procedure prueba.surge_pagos_cruzar_red_prestacion, para
    // que sea auditable y reutilizable fuera de este script (ver
    // surge_pagos_cruzar_red_prestacion.sql). Ese stored debe existir
    // ya creado en la base antes de usar este modulo.
    q("CALL `prueba`.`surge_pagos_cruzar_red_prestacion`(".sql_str($base_padron).", ".sql_str($base_historicos).", ".intval($id_lote).")");
}

function obtener_resumen($base_historicos, $id_lote){
    $sql = "SELECT
                COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') AS desreguladora,
                COUNT(DISTINCT cuil) AS cuiles_distintos,
                COUNT(*) AS cantidad_registros,
                SUM(importe) AS importe_total
            FROM `$base_historicos`.`surge_pagos`
            WHERE id_lote = ".intval($id_lote)."
            GROUP BY COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR')
            ORDER BY importe_total DESC";
    $rs = q($sql);
    $data = array();
    while($row = mysql_fetch_assoc($rs)) $data[] = $row;
    return $data;
}

// ----------------------------------------------------------------------
// Paso 1: sube el archivo, crea el lote, lo deja listo para procesar.
// ----------------------------------------------------------------------
function iniciar_importacion_surge_pagos($base_historicos, $id_usuario, $usuario){
    if(!tabla_existe('`'.$base_historicos.'`.`surge_pagos`')) json_out(array('ok'=>false, 'error'=>'No existe la tabla surge_pagos. Crear la tabla antes de importar.'));
    if(!tabla_existe('`'.$base_historicos.'`.`lotes`')) json_out(array('ok'=>false, 'error'=>'No existe la tabla lotes.'));

    if(!isset($_FILES['archivo']) || $_FILES['archivo']['error'] != UPLOAD_ERR_OK) json_out(array('ok'=>false, 'error'=>'No se recibió el archivo o hubo un error en la carga.'));
    $nombre_original = $_FILES['archivo']['name'];
    if(!validar_extension($nombre_original)) json_out(array('ok'=>false, 'error'=>'Formato no permitido. Debe ser XLSX, XLS o XLTX.'));

    cargar_phpexcel();
    $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : '';

    $id_lote = crear_lote($base_historicos, $nombre_original, $observacion, $id_usuario, $usuario);

    $ruta_tmp = ruta_temp_lote($id_lote);
    if(!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_tmp)){
        json_out(array('ok'=>false, 'error'=>'No se pudo guardar el archivo temporal para procesarlo.'));
    }

    try{ $objPHPExcel = PHPExcel_IOFactory::load($ruta_tmp); }
    catch(Exception $e){ json_out(array('ok'=>false, 'error'=>'No se pudo leer el archivo Excel: '.$e->getMessage())); }

    $sheet = $objPHPExcel->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $total_filas = max(0, $highestRow - 1); // fila 1 = encabezado

    json_out(array('ok'=>true, 'id_lote'=>intval($id_lote), 'total_filas'=>$total_filas));
}

// ----------------------------------------------------------------------
// Paso 2: procesa un tramo de filas (se llama varias veces desde el
// navegador). 1 sola query de INSERT multi-fila por llamada.
// ----------------------------------------------------------------------
function procesar_chunk_surge_pagos($base_historicos){
    $id_lote = isset($_REQUEST['id_lote']) ? intval($_REQUEST['id_lote']) : 0;
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 2;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 150;
    if($start < 2) $start = 2;
    if($length <= 0) $length = 150;
    if($length > 500) $length = 500; // tope de seguridad

    if($id_lote <= 0) json_out(array('ok'=>false, 'error'=>'ID de lote inválido.'));

    $ruta_tmp = ruta_temp_lote($id_lote);
    if(!file_exists($ruta_tmp)) json_out(array('ok'=>false, 'error'=>'No se encontró el archivo temporal del lote (puede haber expirado). Volvé a intentar la carga desde el principio.'));

    cargar_phpexcel();
    try{ $objPHPExcel = PHPExcel_IOFactory::load($ruta_tmp); }
    catch(Exception $e){ json_out(array('ok'=>false, 'error'=>'No se pudo leer el archivo Excel: '.$e->getMessage())); }

    $sheet = $objPHPExcel->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $fin = min($highestRow, $start + $length - 1);

    $filas = array();
    for($row=$start; $row <= $fin; $row++){
        $solicitud = $sheet->getCell('A'.$row)->getValue();
        $patologia = $sheet->getCell('B'.$row)->getValue();
        $estado_pago = $sheet->getCell('C'.$row)->getValue();
        $rnos = $sheet->getCell('D'.$row)->getValue();
        $obra_social = $sheet->getCell('E'.$row)->getValue();
        $cuil = solo_num($sheet->getCell('F'.$row)->getValue());
        $periodo = $sheet->getCell('G'.$row)->getValue();
        $monto_solicitado = $sheet->getCell('H'.$row)->getCalculatedValue();
        $monto_reintegro = $sheet->getCell('I'.$row)->getCalculatedValue();
        $tipo = $sheet->getCell('J'.$row)->getValue();
        $fecha_creacion = normalizar_fecha_excel($sheet->getCell('K'.$row)->getValue());
        $fecha_aceptacion = normalizar_fecha_excel($sheet->getCell('L'.$row)->getValue());
        $fecha_pago = normalizar_fecha_excel($sheet->getCell('M'.$row)->getValue());
        $expediente_gde = $sheet->getCell('N'.$row)->getValue();

        if(trim((string)$solicitud)==='' && trim((string)$cuil)==='' && trim((string)$periodo)==='') continue;

        $importe = sql_num($monto_reintegro);
        $filas[] = array(
            'id_lote' => intval($id_lote),
            'solicitud' => sql_str($solicitud),
            'patologia' => sql_str($patologia),
            'estado_pago' => sql_str($estado_pago),
            'rnos' => sql_str($rnos),
            'obra_social' => sql_str($obra_social),
            'cuil' => sql_str($cuil),
            'periodo' => sql_str($periodo),
            'tipo' => sql_str($tipo),
            'fecha_pago' => sql_str($fecha_pago),
            'fecha_creacion' => sql_str($fecha_creacion),
            'fecha_aceptacion' => sql_str($fecha_aceptacion),
            'expediente_gde' => sql_str($expediente_gde),
            'monto_solicitado' => sql_num($monto_solicitado),
            'importe' => $importe,
            'estado_cruce' => sql_str('PENDIENTE')
        );
    }

    $insertadas = count($filas) ? insertar_filas_surge_pagos_bulk($base_historicos, $filas) : 0;
    $terminado = ($fin >= $highestRow);

    json_out(array(
        'ok' => true,
        'procesadas' => $insertadas,
        'siguiente_start' => $fin + 1,
        'terminado' => $terminado
    ));
}

// ----------------------------------------------------------------------
// Paso 3: cruce contra el padron, totales del lote, limpieza del
// temporal y resumen final.
// ----------------------------------------------------------------------
function finalizar_importacion_surge_pagos($base_padron, $base_historicos){
    $id_lote = isset($_REQUEST['id_lote']) ? intval($_REQUEST['id_lote']) : 0;
    if($id_lote <= 0) json_out(array('ok'=>false, 'error'=>'ID de lote inválido.'));

    cruzar_con_padron($base_padron, $base_historicos, $id_lote);

    $rs = q("SELECT COUNT(*) AS cant, COUNT(DISTINCT cuil) AS cuiles, SUM(importe) AS total
             FROM `$base_historicos`.`surge_pagos` WHERE id_lote=".intval($id_lote));
    $row = mysql_fetch_assoc($rs);
    $cant = intval($row['cant']);
    $cuiles = intval($row['cuiles']);
    $total = floatval($row['total']);

    update_lote_dinamico($base_historicos, $id_lote, array(
        'cant_registros' => intval($cant),
        'estado' => sql_str('IMPORTADO'),
        'importe_calculado' => floatval($total),
        'imp_renglon_resumen' => floatval($total)
    ));

    $ruta_tmp = ruta_temp_lote($id_lote);
    if(file_exists($ruta_tmp)) @unlink($ruta_tmp);

    $resumen = obtener_resumen($base_historicos, $id_lote);
    json_out(array('ok'=>true, 'id_lote'=>intval($id_lote), 'cantidad_registros'=>$cant, 'cuiles_distintos'=>$cuiles, 'total_importe'=>$total, 'resumen'=>$resumen));
}

function listar_lotes_surge_pagos($base_historicos){
    $sql = "SELECT
                l.id AS id_lote,
                DATE_FORMAT(l.fechador, '%d/%m/%Y %H:%i') AS fecha_importacion,
                COALESCE(l.archivo, '') AS archivo,
                COALESCE(l.cant_registros, 0) AS cantidad_registros,
                COALESCE(l.importe_calculado, l.imp_renglon_resumen, 0) AS total_importe,
                COALESCE(l.usuario, '') AS usuario,
                COALESCE(l.estado, '') AS estado,
                COALESCE(l.texto_entero, '') AS observacion
            FROM `$base_historicos`.`lotes` l
            WHERE l.proceso = 'surge_pagos'
            ORDER BY l.id DESC
            LIMIT 300";
    $rs = q($sql); $data = array();
    while($row = mysql_fetch_assoc($rs)) $data[] = $row;
    json_out(array('ok'=>true, 'data'=>$data));
}

function resumen_lote_surge_pagos($base_historicos){
    $id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
    if($id_lote <= 0) json_out(array('ok'=>false, 'error'=>'ID de lote inválido.'));
    json_out(array('ok'=>true, 'data'=>obtener_resumen($base_historicos, $id_lote)));
}

function filtro_desreguladora_sql($des){
    if($des === null || $des === '') return '';
    if($des === 'SIN IDENTIFICAR') return " AND COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') = 'SIN IDENTIFICAR'";
    return " AND COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') = '".esc($des)."'";
}

// Columnas visibles en la grilla de detalle, en el mismo orden que las
// pinta el front-end (index.html #tablaDetalle). El indice es el que
// manda DataTables en order[0][column] cuando serverSide=true.
function columnas_detalle_surge_pagos(){
    return array(
        0 => 'solicitud',
        1 => 'patologia',
        2 => 'estado_pago',
        3 => 'rnos',
        4 => 'cuil',
        5 => 'periodo',
        6 => 'importe',
        7 => 'desreguladora_orden',
        8 => 'estado_cruce',
        9 => 'fecha_pago',
        10 => 'expediente_gde'
    );
}

// server-side real para DataTables: recibe draw/start/length/search/order
// y devuelve solo la porcion de datos pedida, permitiendo buscar por
// cualquier campo (cuil, solicitud, obra social, etc.) sin traer todo
// el lote al navegador.
function detalle_lote_surge_pagos_serverside($base_historicos){
    $id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
    $des = isset($_GET['desreguladora']) ? $_GET['desreguladora'] : '';
    if($id_lote <= 0) json_out(array('draw'=>0, 'recordsTotal'=>0, 'recordsFiltered'=>0, 'data'=>array(), 'error'=>'ID de lote inválido.'));

    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 25;
    if($length <= 0) $length = 25;
    if($length > 500) $length = 500; // tope de seguridad

    $busqueda = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';

    $where_base = "id_lote = ".intval($id_lote).filtro_desreguladora_sql($des);

    $where_busqueda = '';
    if($busqueda !== ''){
        $b = esc($busqueda);
        $where_busqueda = " AND (
            cuil LIKE '%$b%' OR
            solicitud LIKE '%$b%' OR
            obra_social LIKE '%$b%' OR
            patologia LIKE '%$b%' OR
            periodo LIKE '%$b%' OR
            expediente_gde LIKE '%$b%' OR
            COALESCE(desreguladora,'') LIKE '%$b%' OR
            COALESCE(estado_cruce,'') LIKE '%$b%' OR
            COALESCE(estado_pago,'') LIKE '%$b%'
        )";
    }

    $columnas = columnas_detalle_surge_pagos();
    $orden_col = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 5;
    $orden_dir = (isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
    $orden_campo = isset($columnas[$orden_col]) ? $columnas[$orden_col] : 'id';

    $rs_total = q("SELECT COUNT(*) AS c FROM `$base_historicos`.`surge_pagos` WHERE $where_base");
    $recordsTotal = intval(mysql_fetch_assoc($rs_total)['c']);

    $rs_filtrado = q("SELECT COUNT(*) AS c FROM `$base_historicos`.`surge_pagos` WHERE $where_base $where_busqueda");
    $recordsFiltered = intval(mysql_fetch_assoc($rs_filtrado)['c']);

    $sql = "SELECT solicitud, patologia, estado_pago, rnos, obra_social, cuil, periodo, tipo,
                   DATE_FORMAT(fecha_pago, '%d/%m/%Y') AS fecha_pago,
                   DATE_FORMAT(fecha_creacion, '%d/%m/%Y') AS fecha_creacion,
                   DATE_FORMAT(fecha_aceptacion, '%d/%m/%Y') AS fecha_aceptacion,
                   expediente_gde, importe, monto_solicitado, id_capita, id_afiliado,
                   COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') AS desreguladora,
                   COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') AS desreguladora_orden,
                   estado_cruce, detalle_cruce
            FROM `$base_historicos`.`surge_pagos`
            WHERE $where_base $where_busqueda
            ORDER BY `$orden_campo` $orden_dir
            LIMIT ".intval($start).", ".intval($length);

    $rs = q($sql); $data = array();
    while($row = mysql_fetch_assoc($rs)) $data[] = $row;

    json_out(array(
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data
    ));
}

function descargar_detalle_surge_pagos_excel($base_historicos){
    $id_lote = isset($_GET['id_lote']) ? intval($_GET['id_lote']) : 0;
    $des = isset($_GET['desreguladora']) ? $_GET['desreguladora'] : '';
    if($id_lote <= 0){ echo 'ID de lote inválido'; exit; }

    cargar_phpexcel();

    $sql = "SELECT solicitud, patologia, estado_pago, rnos, obra_social, cuil, periodo, tipo,
                   DATE_FORMAT(fecha_creacion, '%d/%m/%Y') AS fecha_creacion,
                   DATE_FORMAT(fecha_aceptacion, '%d/%m/%Y') AS fecha_aceptacion,
                   DATE_FORMAT(fecha_pago, '%d/%m/%Y') AS fecha_pago,
                   expediente_gde, monto_solicitado, importe, id_afiliado,
                   COALESCE(NULLIF(desreguladora,''), 'SIN IDENTIFICAR') AS desreguladora,
                   estado_cruce, detalle_cruce
            FROM `$base_historicos`.`surge_pagos`
            WHERE id_lote = ".intval($id_lote).filtro_desreguladora_sql($des)."
            ORDER BY cuil, periodo, solicitud";
    $rs = q($sql);

    $encabezados = array('Solicitud','Patologia','Estado','RNOS','Obra Social','CUIL','Periodo','Tipo','Fecha Creacion','Fecha Aceptacion','Fecha Pago','Expediente GDE','Monto Solicitado','Importe','ID Afiliado','Desreguladora','Estado Cruce','Detalle Cruce');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator('Sistema')->setTitle('SURGE Pagos - Lote '.$id_lote);
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->setTitle('Detalle');

    $col = 0;
    foreach($encabezados as $enc){
        $sheet->setCellValueByColumnAndRow($col, 1, $enc);
        $col++;
    }
    $sheet->getStyle('A1:'.chr(65+count($encabezados)-1).'1')->getFont()->setBold(true);

    $fila = 2;
    while($row = mysql_fetch_assoc($rs)){
        $sheet->setCellValueByColumnAndRow(0, $fila, $row['solicitud']);
        $sheet->setCellValueByColumnAndRow(1, $fila, $row['patologia']);
        $sheet->setCellValueByColumnAndRow(2, $fila, $row['estado_pago']);
        $sheet->setCellValueByColumnAndRow(3, $fila, $row['rnos']);
        $sheet->setCellValueByColumnAndRow(4, $fila, $row['obra_social']);
        $sheet->setCellValueByColumnAndRow(5, $fila, $row['cuil']);
        $sheet->setCellValueByColumnAndRow(6, $fila, $row['periodo']);
        $sheet->setCellValueByColumnAndRow(7, $fila, $row['tipo']);
        $sheet->setCellValueByColumnAndRow(8, $fila, $row['fecha_creacion']);
        $sheet->setCellValueByColumnAndRow(9, $fila, $row['fecha_aceptacion']);
        $sheet->setCellValueByColumnAndRow(10, $fila, $row['fecha_pago']);
        $sheet->setCellValueByColumnAndRow(11, $fila, $row['expediente_gde']);
        $sheet->setCellValueByColumnAndRow(12, $fila, floatval($row['monto_solicitado']));
        $sheet->setCellValueByColumnAndRow(13, $fila, floatval($row['importe']));
        $sheet->setCellValueByColumnAndRow(14, $fila, $row['id_afiliado']);
        $sheet->setCellValueByColumnAndRow(15, $fila, $row['desreguladora']);
        $sheet->setCellValueByColumnAndRow(16, $fila, $row['estado_cruce']);
        $sheet->setCellValueByColumnAndRow(17, $fila, $row['detalle_cruce']);
        $fila++;
    }

    foreach(range('A', chr(65+count($encabezados)-1)) as $letra){
        $sheet->getColumnDimension($letra)->setAutoSize(true);
    }

    $nombre = 'surge_pagos_lote_'.$id_lote.($des!='' ? '_'.preg_replace('/[^a-zA-Z0-9_-]/','_', $des) : '').'.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$nombre.'"');
    header('Cache-Control: max-age=0');

    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->save('php://output');
    exit;
}

$accion = isset($_REQUEST['accion']) ? $_REQUEST['accion'] : '';

switch($accion){
    case 'iniciar_importacion_surge_pagos':
        iniciar_importacion_surge_pagos($base_historicos, $id_usuario, $usuario);
        break;
    case 'procesar_chunk_surge_pagos':
        procesar_chunk_surge_pagos($base_historicos);
        break;
    case 'finalizar_importacion_surge_pagos':
        finalizar_importacion_surge_pagos($base_padron, $base_historicos);
        break;
    case 'listar_lotes_surge_pagos':
        listar_lotes_surge_pagos($base_historicos);
        break;
    case 'resumen_lote_surge_pagos':
        resumen_lote_surge_pagos($base_historicos);
        break;
    case 'detalle_lote_surge_pagos':
        detalle_lote_surge_pagos_serverside($base_historicos);
        break;
    case 'descargar_detalle_surge_pagos':
        descargar_detalle_surge_pagos_excel($base_historicos);
        break;
    default:
        json_out(array('ok'=>false, 'error'=>'Acción no válida.'));
}
