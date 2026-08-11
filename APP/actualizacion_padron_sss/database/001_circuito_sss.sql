-- Ejecutar reemplazando __BASE_HISTORICOS__ por la base historica de la institucion.
-- El modulo tambien crea estas tablas en forma idempotente al iniciar.

CREATE TABLE IF NOT EXISTS __BASE_HISTORICOS__.sss_catalogo_errores (
  codigo VARCHAR(3) NOT NULL,
  campo VARCHAR(120) NOT NULL DEFAULT '',
  descripcion VARCHAR(500) NOT NULL,
  accion VARCHAR(500) NOT NULL,
  version_instructivo VARCHAR(30) NOT NULL DEFAULT '2026-07 v6',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS __BASE_HISTORICOS__.sss_presentacion_control (
  id_lote INT NOT NULL,
  periodo VARCHAR(7) NOT NULL,
  estado VARCHAR(40) NOT NULL DEFAULT 'PREPARADO',
  archivo_envio VARCHAR(255) NULL,
  hash_archivo CHAR(64) NULL,
  cantidad_movimientos INT NOT NULL DEFAULT 0,
  fecha_cierre DATE NULL,
  resultados_disponibles_desde DATE NULL,
  fecha_generado DATETIME NULL,
  fecha_enviado DATETIME NULL,
  fecha_error_inmediato DATETIME NULL,
  fecha_resultado DATETIME NULL,
  ultimo_error TEXT NULL,
  id_usuario INT NULL,
  actualizado DATETIME NOT NULL,
  PRIMARY KEY (id_lote),
  KEY idx_periodo (periodo),
  KEY idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS __BASE_HISTORICOS__.sss_afiliado_cronologia (
  id BIGINT NOT NULL AUTO_INCREMENT,
  id_persona INT NOT NULL,
  id_lote INT NULL,
  periodo VARCHAR(7) NULL,
  estado VARCHAR(40) NOT NULL,
  codigo_error VARCHAR(50) NULL,
  detalle VARCHAR(500) NOT NULL,
  origen VARCHAR(30) NOT NULL DEFAULT 'SSS',
  id_usuario INT NULL,
  fechador DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_persona_fecha (id_persona, fechador),
  KEY idx_lote (id_lote)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

