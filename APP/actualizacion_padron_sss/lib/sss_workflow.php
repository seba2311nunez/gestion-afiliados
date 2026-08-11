<?php

function sss_escape($valor){
    return mysql_real_escape_string((string)$valor);
}

function sss_json($datos, $httpStatus = 200){
    if(function_exists('http_response_code')) http_response_code($httpStatus);
    header('Content-Type: application/json; charset=utf-8');
    $json = json_encode($datos);
    if($json === false){
        $json = json_encode(array('status' => 'error', 'mensaje' => 'No se pudo codificar la respuesta JSON.'));
    }
    echo $json;
}

function sss_crear_estructura(){
    $base = N_BASE_HISTORICOS;
    mysql_query("CREATE TABLE IF NOT EXISTS {$base}.sss_catalogo_errores (
        codigo VARCHAR(3) NOT NULL, campo VARCHAR(120) NOT NULL DEFAULT '',
        descripcion VARCHAR(500) NOT NULL, accion VARCHAR(500) NOT NULL,
        version_instructivo VARCHAR(30) NOT NULL DEFAULT '2026-07 v6', activo TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (codigo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die(mysql_error());

    mysql_query("CREATE TABLE IF NOT EXISTS {$base}.sss_presentacion_control (
        id_lote INT NOT NULL, periodo VARCHAR(7) NOT NULL, estado VARCHAR(40) NOT NULL DEFAULT 'PREPARADO',
        archivo_envio VARCHAR(255) NULL, hash_archivo CHAR(64) NULL, cantidad_movimientos INT NOT NULL DEFAULT 0,
        fecha_cierre DATE NULL, resultados_disponibles_desde DATE NULL, fecha_generado DATETIME NULL,
        fecha_enviado DATETIME NULL, fecha_error_inmediato DATETIME NULL, fecha_resultado DATETIME NULL,
        ultimo_error TEXT NULL, id_usuario INT NULL, actualizado DATETIME NOT NULL,
        PRIMARY KEY (id_lote), KEY idx_periodo (periodo), KEY idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die(mysql_error());

    mysql_query("CREATE TABLE IF NOT EXISTS {$base}.sss_afiliado_cronologia (
        id BIGINT NOT NULL AUTO_INCREMENT, id_persona INT NOT NULL, id_lote INT NULL, periodo VARCHAR(7) NULL,
        estado VARCHAR(40) NOT NULL, codigo_error VARCHAR(50) NULL, detalle VARCHAR(500) NOT NULL,
        origen VARCHAR(30) NOT NULL DEFAULT 'SSS', id_usuario INT NULL, fechador DATETIME NOT NULL,
        PRIMARY KEY (id), KEY idx_persona_fecha (id_persona, fechador), KEY idx_lote (id_lote)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die(mysql_error());

    mysql_query("CREATE TABLE IF NOT EXISTS {$base}.sss_cronograma_ftp (
        periodo CHAR(6) NOT NULL, fecha_apertura DATE NOT NULL, fecha_cierre DATE NOT NULL,
        fecha_respuesta DATE NOT NULL, fecha_devolucion DATE NULL, fuente VARCHAR(255) NOT NULL,
        actualizado DATETIME NOT NULL, PRIMARY KEY (periodo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die(mysql_error());

    sss_sembrar_catalogo();
	sss_sembrar_cronograma_ftp();
	sss_actualizar_vencimientos_desde_cronograma();
}

function sss_sembrar_cronograma_ftp(){
    $base = N_BASE_HISTORICOS;
    $filas = array(
        array('202511','2025-12-01','2025-12-15','2025-12-18','2025-12-23'), array('202512','2026-01-01','2026-01-14','2026-01-16','2026-01-23'),
        array('202601','2026-02-01','2026-02-16','2026-02-19','2026-02-24'), array('202602','2026-03-01','2026-03-12','2026-03-16','2026-03-25'),
        array('202603','2026-04-01','2026-04-15','2026-04-20','2026-04-24'), array('202604','2026-05-01','2026-05-14','2026-05-18','2026-05-22'),
        array('202605','2026-06-01','2026-06-12','2026-06-16','2026-06-24'), array('202606','2026-07-01','2026-07-14','2026-07-16','2026-07-24'),
        array('202607','2026-08-01','2026-08-13','2026-08-18','2026-08-25'), array('202608','2026-09-01','2026-09-14','2026-09-16','2026-09-24'),
        array('202609','2026-10-01','2026-10-14','2026-10-16','2026-10-23'), array('202610','2026-11-01','2026-11-13','2026-11-17','2026-11-24'),
        array('202611','2026-12-01','2026-12-14','2026-12-16','2026-12-22'), array('202612','2027-01-01','2027-01-13','2027-01-15','2027-01-25')
    );
    foreach($filas as $f){
        mysql_query("INSERT INTO {$base}.sss_cronograma_ftp(periodo,fecha_apertura,fecha_cierre,fecha_respuesta,fecha_devolucion,fuente,actualizado)
            VALUES('{$f[0]}','{$f[1]}','{$f[2]}','{$f[3]}','{$f[4]}','cronograma_ftp.pdf',NOW())
            ON DUPLICATE KEY UPDATE fecha_apertura=VALUES(fecha_apertura),fecha_cierre=VALUES(fecha_cierre),fecha_respuesta=VALUES(fecha_respuesta),fecha_devolucion=VALUES(fecha_devolucion),actualizado=NOW()") or die(mysql_error());
    }
}

function sss_actualizar_vencimientos_desde_cronograma(){
    $base = N_BASE_HISTORICOS;
    mysql_query("UPDATE {$base}.lotes l JOIN {$base}.sss_cronograma_ftp c ON REPLACE(l.descripcion,'-','')=c.periodo
        SET l.obrasocial=c.fecha_cierre WHERE l.proceso='novedades_exportables' AND (l.obrasocial IS NULL OR l.obrasocial<>c.fecha_cierre)") or die(mysql_error());
    mysql_query("UPDATE {$base}.sss_presentacion_control p JOIN {$base}.sss_cronograma_ftp c ON REPLACE(p.periodo,'-','')=c.periodo
        SET p.fecha_cierre=c.fecha_cierre,p.resultados_disponibles_desde=c.fecha_respuesta,p.actualizado=NOW()
        WHERE p.fecha_cierre IS NULL OR p.fecha_cierre<>c.fecha_cierre OR p.resultados_disponibles_desde IS NULL OR p.resultados_disponibles_desde<>c.fecha_respuesta") or die(mysql_error());
}

function sss_fecha_respuesta_cronograma($periodo, $fechaCierre){
    $base = N_BASE_HISTORICOS; $periodo = preg_replace('/[^0-9]/','',(string)$periodo);
    $rs = mysql_query("SELECT fecha_respuesta FROM {$base}.sss_cronograma_ftp WHERE periodo='".sss_escape($periodo)."' LIMIT 1");
    if($rs && ($row=mysql_fetch_assoc($rs))) return $row['fecha_respuesta'];
    return sss_fecha_habil_desde($fechaCierre,2);
}

function sss_catalogo_base(){
    return array(
        '000'=>array('Validacion','Novedad aceptada. Sin errores de validacion.','Ninguna.'),
        '001'=>array('CUIT del empleador','Debe estar informado.','Corregir el CUIT del empleador.'),
        '002'=>array('CUIT del empleador','Debe tener 11 caracteres numericos.','Corregir el CUIT del empleador.'),
        '003'=>array('CUIT del empleador','Prefijo invalido para movimientos A o M.','Verificar el prefijo y corregir.'),
        '004'=>array('CUIT del empleador','Digito verificador incorrecto.','Corregir el CUIT del empleador.'),
        '005'=>array('CUIL titular','Debe estar informado.','Corroborar el CUIL del titular.'),
        '006'=>array('CUIL titular','Debe tener 11 caracteres numericos.','Corroborar el CUIL del titular.'),
        '007'=>array('CUIL titular','Prefijo invalido.','Corregir el CUIL del titular.'),
        '008'=>array('CUIL titular','Digito verificador incorrecto.','Corregir el CUIL del titular.'),
        '009'=>array('Parentesco','Debe estar informado.','Codificarlo segun la tabla del instructivo.'),
        '010'=>array('Parentesco','Supera dos posiciones numericas.','Corregir el parentesco.'),
        '011'=>array('Parentesco','No existe en la tabla correspondiente.','Codificarlo segun el instructivo.'),
        '012'=>array('CUIL','Debe estar informado.','Solicitar o gestionar el CUIL del familiar.'),
        '013'=>array('CUIL','Debe tener 11 caracteres numericos.','Corregir el CUIL.'),
        '014'=>array('CUIL','Prefijo invalido.','Corregir el CUIL.'),
        '015'=>array('CUIL','Digito verificador incorrecto.','Corregir el CUIL.'),
        '016'=>array('Tipo documento','Debe estar informado.','Corroborar y codificar segun el instructivo.'),
        '018'=>array('Tipo documento','No existe en la tabla correspondiente.','Corregir el tipo de documento.'),
        '019'=>array('Documento','Debe estar informado.','Corroborar el documento del afiliado.'),
        '020'=>array('Documento','Supera ocho posiciones numericas.','Corregir el numero de documento.'),
        '021'=>array('Documento','Numero repetitivo no admitido.','Corroborar el documento.'),
        '022'=>array('Apellido y nombres','Debe estar informado.','Completar apellido y nombres.'),
        '023'=>array('Apellido y nombres','Supera 30 posiciones.','Corregir el formato.'),
        '024'=>array('Sexo','Debe estar informado.','Codificar segun el instructivo.'),
        '025'=>array('Sexo','No existe en la tabla correspondiente.','Corregir el sexo informado.'),
        '029'=>array('Fecha nacimiento','Debe estar informada.','Verificar y corregir la fecha.'),
        '030'=>array('Fecha nacimiento','Debe contener ocho digitos.','Usar formato DDMMAAAA.'),
        '031'=>array('Fecha nacimiento','Es posterior a la fecha del proceso.','Corregir la fecha.'),
        '032'=>array('Fecha nacimiento','La edad supera 110 anos.','Verificar la fecha de nacimiento.'),
        '033'=>array('Fecha nacimiento','Fecha invalida.','Usar formato DDMMAAAA.'),
        '042'=>array('Codigo postal','Supera ocho posiciones.','Corregir el formato.'),
        '043'=>array('Provincia','Debe estar informada.','Codificar segun el instructivo.'),
        '052'=>array('Incapacidad','Debe estar informada.','Informar 00 o 01.'),
        '054'=>array('Incapacidad','Solo se aceptan 00 o 01.','Corregir el valor.'),
        '055'=>array('Tipo beneficiario','Debe estar informado.','Codificar segun el instructivo.'),
        '057'=>array('Tipo beneficiario','Tipo inexistente o no admitido.','Verificar el tipo de beneficiario.'),
        '058'=>array('Fecha ingreso','Debe estar informada.','Corregir la fecha.'),
        '060'=>array('Fecha ingreso','Fecha invalida.','Usar formato DDMMAAAA.'),
        '065'=>array('Grupo familiar','El CUIL del familiar coincide con el titular.','Informar el CUIL correcto del familiar.'),
        '066'=>array('Parentesco/edad','Parentesco 3 o 5 con edad no admitida.','Verificar fecha de nacimiento y parentesco.'),
        '067'=>array('Parentesco/edad','Parentesco 4 o 6 con edad no admitida.','Verificar fecha de nacimiento y parentesco.'),
        '069'=>array('Parentesco/edad','Parentesco 9 con edad menor a 25 anos.','Verificar fecha de nacimiento.'),
        '079'=>array('Grupo familiar','Familiar sin titular correspondiente.','Informar primero el alta del titular.'),
        '100'=>array('CUIL','CUIL verificado; pertenece a la persona.','Ninguna.'),
        '101'=>array('CUIL titular','La SSS apropio un CUIL distinto para el titular.','Aplicar el cambio al padron; no reenviar.'),
        '111'=>array('CUIL familiar','La SSS apropio un CUIL distinto para el familiar.','Aplicar el cambio al padron; no reenviar.'),
        '112'=>array('Documento','La SSS apropio el numero de documento.','Aplicar el documento al padron; no reenviar.'),
        '136'=>array('Fecha cierre','La fecha es anterior al proceso menos dos meses.','Corregir el periodo de presentacion.'),
        '137'=>array('Fecha alta','Es posterior al ultimo dia del mes de proceso.','No presentar altas futuras.'),
        '140'=>array('DDJJ','Para tipo 0 o 1 no hay DDJJ en los ultimos seis meses.','Verificar CUIT/CUIL y DDJJ.'),
        '153'=>array('Movimiento','Debe informarse A, B o M.','Corregir el codigo de movimiento.'),
        '168'=>array('Movimiento','No se permiten novedades de cambio de CUIL con movimiento C.','Informar baja y alta cuando corresponda.'),
        '180'=>array('Opcion vigente','Titular RD con opcion activa hacia otra obra social.','Verificar la opcion vigente antes de reenviar.'),
        '181'=>array('Opcion vigente','Titular MT/SD/efector con opcion activa hacia otra obra social.','Verificar la opcion vigente antes de reenviar.'),
        '182'=>array('Codigo postal','Debe estar entre 1001 y 9421.','Corregir el codigo postal.'),
        '183'=>array('Provincia/codigo postal','El codigo postal no corresponde al rango de la provincia.','Corregir provincia o codigo postal.'),
        '190'=>array('Opcion finalizada','Titular RD con opcion finalizada y sin DDJJ para la obra social.','Verificar DDJJ y opcion.'),
        '191'=>array('Baja de regimen','Titular MT o efector con baja informada por AFIP.','Verificar situacion tributaria.'),
        '192'=>array('Pagos','Tipo 4 o 7 sin pagos de monotributo en diez meses.','Verificar pagos antes de reenviar.'),
        '193'=>array('Pagos','Tipo 5 sin pagos de servicio domestico en diez meses.','Verificar pagos antes de reenviar.'),
        '194'=>array('Baja/opcion','Baja de titular tipo 0 o 1 con opcion vigente.','Verificar datos y opcion.'),
        '195'=>array('Baja/opcion','Baja de titular tipo 4, 5 o 7 con opcion vigente.','Verificar datos y opcion.'),
        '196'=>array('Familiar/DDJJ','Alta de familiar con DDJJ propia en el periodo.','Verificar si corresponde afiliacion como titular.'),
        '197'=>array('Baja/DDJJ','Baja de titular tipo 0 o 1 con DDJJ vigente y sin pluriempleo.','Verificar datos y reenviar si corresponde.'),
        '198'=>array('Situacion revista','Situacion 71 incompatible con tipo o parentesco.','Verificar tipo, parentesco y situacion de revista.'),
        '199'=>array('Situacion revista','Situacion 72 incompatible con tipo o parentesco.','Verificar tipo, parentesco y situacion de revista.'),
        '300'=>array('Identidad','Persona inexistente en el universo CUIT-CUIL de AFIP.','Verificar los datos y reenviar.')
    );
}

function sss_sembrar_catalogo(){
    $base = N_BASE_HISTORICOS;
    $catalogoBase = sss_catalogo_base();
    $rsCantidad = mysql_query("SELECT COUNT(*) cantidad FROM {$base}.sss_catalogo_errores WHERE version_instructivo='2026-07 v6' AND activo=1");
    $cantidad = $rsCantidad ? intval(mysql_fetch_object($rsCantidad)->cantidad) : 0;
    if($cantidad >= count($catalogoBase)) return;
    foreach($catalogoBase as $codigo => $dato){
        $campo = sss_escape($dato[0]); $descripcion = sss_escape($dato[1]); $accion = sss_escape($dato[2]);
        mysql_query("INSERT INTO {$base}.sss_catalogo_errores(codigo,campo,descripcion,accion,version_instructivo,activo)
            VALUES('{$codigo}','{$campo}','{$descripcion}','{$accion}','2026-07 v6',1)
            ON DUPLICATE KEY UPDATE campo=VALUES(campo),descripcion=VALUES(descripcion),accion=VALUES(accion),version_instructivo=VALUES(version_instructivo),activo=1") or die(mysql_error());
    }
}

function sss_catalogo_en_memoria(){
    static $catalogo = null;
    if($catalogo !== null) return $catalogo;
    $catalogo = array();
    $rs = mysql_query("SELECT codigo,campo,descripcion,accion FROM ".N_BASE_HISTORICOS.".sss_catalogo_errores WHERE activo=1");
    if($rs) while($row = mysql_fetch_assoc($rs)) $catalogo[$row['codigo']] = $row;
    return $catalogo;
}

function sss_detalle_codigos($valor){
    $resultado = array('codigos'=>array(), 'descripcion'=>'', 'accion'=>'');
    preg_match_all('/\b([0-9]{3})\b/', (string)$valor, $m);
    $catalogo = sss_catalogo_en_memoria(); $descripciones = array(); $acciones = array();
    foreach(array_unique($m[1]) as $codigo){
        $resultado['codigos'][] = $codigo;
        if(isset($catalogo[$codigo])){
            $descripciones[] = $codigo.' - '.$catalogo[$codigo]['descripcion'];
            if($catalogo[$codigo]['accion'] !== '') $acciones[] = $catalogo[$codigo]['accion'];
        } else {
            $descripciones[] = $codigo.' - Codigo sin catalogar';
        }
    }
    $resultado['descripcion'] = implode(' | ', $descripciones);
    $resultado['accion'] = implode(' | ', array_unique($acciones));
    return $resultado;
}

function sss_fecha_habil_desde($fecha, $dias){
    $ts = strtotime($fecha); $sumados = 0;
    while($sumados < $dias){ $ts = strtotime('+1 day', $ts); $n = intval(date('N', $ts)); if($n < 6) $sumados++; }
    return date('Y-m-d', $ts);
}

function sss_registrar_estado($idLote, $periodo, $estado, $usuario, $extra = array()){
    $base = N_BASE_HISTORICOS; $idLote = intval($idLote); $usuario = intval($usuario);
    $periodo = sss_escape($periodo); $estado = sss_escape($estado);
    $columnas = array();
    foreach($extra as $campo => $valor){
        $permitidos = array('archivo_envio','hash_archivo','cantidad_movimientos','fecha_cierre','resultados_disponibles_desde','fecha_generado','fecha_enviado','fecha_error_inmediato','fecha_resultado','ultimo_error');
        if(in_array($campo, $permitidos)) $columnas[] = "{$campo}='".sss_escape($valor)."'";
    }
    $setExtra = count($columnas) ? ','.implode(',', $columnas) : '';
    mysql_query("INSERT INTO {$base}.sss_presentacion_control(id_lote,periodo,estado,id_usuario,actualizado)
        VALUES({$idLote},'{$periodo}','{$estado}',{$usuario},NOW())
        ON DUPLICATE KEY UPDATE estado=VALUES(estado),id_usuario=VALUES(id_usuario),actualizado=NOW()") or die(mysql_error());
    if(count($columnas)) mysql_query("UPDATE {$base}.sss_presentacion_control SET ".implode(',', $columnas).",actualizado=NOW() WHERE id_lote={$idLote}") or die(mysql_error());
}

function sss_cronologia($idPersona, $idLote, $periodo, $estado, $codigo, $detalle, $usuario){
    $base = N_BASE_HISTORICOS;
    mysql_query("INSERT INTO {$base}.sss_afiliado_cronologia(id_persona,id_lote,periodo,estado,codigo_error,detalle,id_usuario,fechador)
        VALUES(".intval($idPersona).",".intval($idLote).",'".sss_escape($periodo)."','".sss_escape($estado)."','".sss_escape($codigo)."','".sss_escape($detalle)."',".intval($usuario).",NOW())") or die(mysql_error());
}

function sss_propagar_errores_al_periodo_siguiente($idLoteOrigen, $periodoOrigen, $usuario){
    $base = N_BASE_HISTORICOS; $idLoteOrigen = intval($idLoteOrigen);
    $rs = mysql_query("SELECT id,descripcion FROM {$base}.lotes WHERE proceso='novedades_exportables' AND descripcion>'".sss_escape($periodoOrigen)."' ORDER BY descripcion ASC LIMIT 1");
    $siguiente = $rs ? mysql_fetch_assoc($rs) : null;
    if(!$siguiente) return 0;
    $idDestino = intval($siguiente['id']);
    mysql_query("UPDATE {$base}.novedades_exportables destino
        JOIN {$base}.novedades_exportables origen ON origen.id_persona=destino.id_persona AND origen.id_lote={$idLoteOrigen}
        SET destino.cod_error_=origen.cod_error_
        WHERE destino.id_lote={$idDestino} AND TRIM(COALESCE(origen.cod_error_,''))<>''") or die(mysql_error());
    $afectados = mysql_affected_rows();
    $rsPersonas = mysql_query("SELECT destino.id_persona,origen.cod_error_ FROM {$base}.novedades_exportables destino
        JOIN {$base}.novedades_exportables origen ON origen.id_persona=destino.id_persona AND origen.id_lote={$idLoteOrigen}
        WHERE destino.id_lote={$idDestino} AND TRIM(COALESCE(origen.cod_error_,''))<>''");
    if($rsPersonas) while($p = mysql_fetch_assoc($rsPersonas)) sss_cronologia($p['id_persona'],$idDestino,$siguiente['descripcion'],'ERROR_PROPAGADO',$p['cod_error_'],'Error heredado de la presentacion '.$periodoOrigen,$usuario);
    return $afectados;
}

?>
