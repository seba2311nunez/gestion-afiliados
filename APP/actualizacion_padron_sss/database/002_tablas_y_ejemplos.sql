-- Catalogo reproducible de tablas del submodulo actualizacion_padron_sss
-- Generado: 2026-08-08T18:09:45+02:00. Las filas son muestras anonimizadas y no deben usarse como respaldo.

-- ============================================================
-- osemm_historicos.lotes
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `lotes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lote` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `archivo` varchar(80) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `aprobado_aplicar_padron` int DEFAULT NULL,
  `identificacion_leida` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'no hay',
  `cant_registros` int DEFAULT NULL COMMENT 'presentados inicialmente',
  `obrasocial` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `proceso` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `usuario` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `estado` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `texto_entero` text CHARACTER SET latin1 COLLATE latin1_general_ci COMMENT 'aca deberia ir el contenido del txt',
  `exp_gecros` int DEFAULT '0' COMMENT 'valida si se exporto',
  `exp_padron` int DEFAULT '0' COMMENT 'valida si exporto al padron',
  `clave_agenda` varchar(100) DEFAULT NULL,
  `importe_calculado` double DEFAULT NULL,
  `imp_renglon_resumen` double DEFAULT NULL,
  `imp_n06` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `archivo` (`archivo`),
  KEY `lote` (`lote`),
  KEY `archivo_2` (`archivo`),
  KEY `id_usuario` (`id_usuario`),
  KEY `usuario` (`usuario`),
  KEY `proceso` (`proceso`),
  KEY `estado` (`estado`),
  KEY `clave_agenda` (`clave_agenda`)
) ENGINE=InnoDB AUTO_INCREMENT=14045 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_historicos`.`lotes` (`id`,`lote`,`descripcion`,`archivo`,`aprobado_aplicar_padron`,`identificacion_leida`,`cant_registros`,`obrasocial`,`fechador`,`proceso`,`usuario`,`id_usuario`,`estado`,`texto_entero`,`exp_gecros`,`exp_padron`,`clave_agenda`,`importe_calculado`,`imp_renglon_resumen`,`imp_n06`) VALUES (1001,NULL,'2021-01-01','a111605_sss_2021-01-01.txt',NULL,'no hay',8,NULL,'2021-01-18 16:20:27','alta_rg_sss',NULL,'PERSONA EJEMPLO 1',NULL,NULL,0,0,'[REDACTED]',NULL,NULL,NULL);
INSERT INTO `osemm_historicos`.`lotes` (`id`,`lote`,`descripcion`,`archivo`,`aprobado_aplicar_padron`,`identificacion_leida`,`cant_registros`,`obrasocial`,`fechador`,`proceso`,`usuario`,`id_usuario`,`estado`,`texto_entero`,`exp_gecros`,`exp_padron`,`clave_agenda`,`importe_calculado`,`imp_renglon_resumen`,`imp_n06`) VALUES (1002,NULL,'2020-12-27','assistencial_oct_nov_dic_2020',NULL,'no hay',190,NULL,'2021-01-19 13:09:08','opciones_nuevas','PERSONA EJEMPLO 2','PERSONA EJEMPLO 2','procesado',NULL,0,0,NULL,NULL,NULL,NULL);
INSERT INTO `osemm_historicos`.`lotes` (`id`,`lote`,`descripcion`,`archivo`,`aprobado_aplicar_padron`,`identificacion_leida`,`cant_registros`,`obrasocial`,`fechador`,`proceso`,`usuario`,`id_usuario`,`estado`,`texto_entero`,`exp_gecros`,`exp_padron`,`clave_agenda`,`importe_calculado`,`imp_renglon_resumen`,`imp_n06`) VALUES (1003,NULL,'2021-02-01','a111605_sss_2021-02-01.txt',NULL,'no hay',129,NULL,'2021-01-19 13:48:05','alta_rg_sss',NULL,'PERSONA EJEMPLO 3',NULL,NULL,0,0,'[REDACTED]',NULL,NULL,NULL);

-- ============================================================
-- osemm_historicos.novedades_exportables
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `novedades_exportables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lote` int DEFAULT NULL,
  `id_persona` int DEFAULT NULL,
  `origen_nov` varchar(255) DEFAULT NULL,
  `id_motivo` int DEFAULT '1' COMMENT 'Default 1  = No especificado',
  `tipo_mov` enum('A','B','M') DEFAULT 'A',
  `fec_mov` date DEFAULT NULL COMMENT 'ej fec alta',
  `padron_sss` int DEFAULT '0',
  `id_usuario` int DEFAULT NULL,
  `fechador_carga` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `eliminar` int DEFAULT '0',
  `cod_error_` varchar(255) DEFAULT NULL,
  `cod_rechazados` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_lote_2` (`id_lote`,`id_persona`,`tipo_mov`,`fec_mov`),
  KEY `id_lote` (`id_lote`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=293809 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_historicos`.`novedades_exportables` (`id`,`id_lote`,`id_persona`,`origen_nov`,`id_motivo`,`tipo_mov`,`fec_mov`,`padron_sss`,`id_usuario`,`fechador_carga`,`eliminar`,`cod_error_`,`cod_rechazados`) VALUES (1001,1001,1001,NULL,1001,'A',NULL,0,NULL,'2023-04-06 13:16:27',0,NULL,NULL);
INSERT INTO `osemm_historicos`.`novedades_exportables` (`id`,`id_lote`,`id_persona`,`origen_nov`,`id_motivo`,`tipo_mov`,`fec_mov`,`padron_sss`,`id_usuario`,`fechador_carga`,`eliminar`,`cod_error_`,`cod_rechazados`) VALUES (1002,1002,1002,NULL,1002,'A',NULL,0,NULL,'2023-04-06 13:16:27',0,NULL,NULL);
INSERT INTO `osemm_historicos`.`novedades_exportables` (`id`,`id_lote`,`id_persona`,`origen_nov`,`id_motivo`,`tipo_mov`,`fec_mov`,`padron_sss`,`id_usuario`,`fechador_carga`,`eliminar`,`cod_error_`,`cod_rechazados`) VALUES (1003,1003,1003,NULL,1003,'A',NULL,0,NULL,'2023-04-06 13:16:27',0,NULL,NULL);

-- ============================================================
-- osemm_historicos.novedades_sss_errores
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `novedades_sss_errores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lote` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `id_persona` int DEFAULT NULL,
  `periodo` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `rnos` varchar(255) DEFAULT NULL,
  `cuit` varchar(255) DEFAULT NULL,
  `cuil_titular` varchar(255) DEFAULT NULL,
  `parentesco` varchar(255) DEFAULT NULL,
  `cuil` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `td` varchar(255) DEFAULT NULL,
  `nd` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ayn` varchar(255) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `est_civil` varchar(255) DEFAULT NULL,
  `fn` varchar(255) DEFAULT NULL,
  `nacionalidad` varchar(255) DEFAULT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `numero` varchar(255) DEFAULT NULL,
  `piso` varchar(255) DEFAULT NULL,
  `dto` varchar(255) DEFAULT NULL,
  `localidad` varchar(255) DEFAULT NULL,
  `cp` varchar(255) DEFAULT NULL,
  `provincia` varchar(255) DEFAULT NULL,
  `tipo_dom` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `revista` varchar(255) DEFAULT NULL,
  `incapacidad` varchar(255) DEFAULT NULL,
  `tbt` varchar(255) DEFAULT NULL,
  `fec_alta` varchar(255) DEFAULT NULL,
  `fec_cierre` varchar(255) DEFAULT NULL,
  `cod_mov` varchar(255) DEFAULT NULL,
  `cod_error` varchar(255) DEFAULT NULL,
  `cod_error2` varchar(255) DEFAULT NULL,
  `cod_error3` varchar(255) DEFAULT NULL,
  `fechador` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nd` (`nd`),
  KEY `cuil_titular` (`cuil_titular`),
  KEY `cuil` (`cuil`)
) ENGINE=InnoDB AUTO_INCREMENT=46231 DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_historicos`.`novedades_sss_errores` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1001,1001,1001,'2023-02',111605,'20000000001','20000000001',11,'20000000001','DU','90000001','PERSONA EJEMPLO 1','M','01',20062017,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1878    ','02','00','0000000000','00','01','00','01022023',28022023,'A','-011                                                                                                \n','','','2023-03-29 21:20:50');
INSERT INTO `osemm_historicos`.`novedades_sss_errores` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1002,1002,1002,'2023-02',111605,'20000000002','20000000002',11,'20000000002','DU','90000002','PERSONA EJEMPLO 2','M','01',20062017,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1878    ','02','00','0000000000','00','01','00','01022023',28022023,'A','-011                                                                                                \n','','','2023-03-29 21:22:20');
INSERT INTO `osemm_historicos`.`novedades_sss_errores` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1003,1003,1003,'2023-02',111605,'20000000003','20000000003',11,'20000000003','DU','90000003','PERSONA EJEMPLO 3','M','01',20062017,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1878    ','02','00','0000000000','00','01','00','01022023',28022023,'A','-011                                                                                                \n','','','2023-03-29 21:22:56');

-- ============================================================
-- osemm_historicos.novedades_sss_aceptados
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `novedades_sss_aceptados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lote` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `id_persona` int DEFAULT NULL,
  `periodo` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `rnos` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuit` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuil_titular` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuil` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `td` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `nd` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `ayn` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `sexo` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `est_civil` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fn` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `nacionalidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `calle` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `numero` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `piso` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `dto` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `localidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cp` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `provincia` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `tipo_dom` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `telefono` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `revista` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `incapacidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `tbt` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fec_alta` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fec_cierre` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_mov` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error2` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error3` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `movimiento_desestimado` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cuit` (`cuit`),
  KEY `cuil_titular` (`cuil_titular`),
  KEY `parentesco` (`parentesco`),
  KEY `nd` (`nd`),
  KEY `fec_cierre` (`fec_cierre`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=17433 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `osemm_historicos`.`novedades_sss_aceptados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`,`movimiento_desestimado`) VALUES (1001,1001,1001,'2023-02',111605,'20000000001','20000000001','03','20000000001','DU','90000001','PERSONA EJEMPLO 1','F','01','06112003','012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1657    ','02','00','0000000000','00','00','00','01022023',28022023,'A',           0,100,'          0\n','2023-05-04 12:16:46',0);
INSERT INTO `osemm_historicos`.`novedades_sss_aceptados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`,`movimiento_desestimado`) VALUES (1002,1002,1002,'2023-02',111605,'20000000002','20000000002','02','20000000002','DU','90000002','PERSONA EJEMPLO 2','F','02',15091994,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1828    ','02','00','0000000000','00','00','00','01022023',28022023,'A',           0,100,'          0\n','2023-05-04 12:16:46',0);
INSERT INTO `osemm_historicos`.`novedades_sss_aceptados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`,`movimiento_desestimado`) VALUES (1003,1003,1003,'2023-02',111605,'20000000003','20000000003','01','20000000003','DU','90000003','PERSONA EJEMPLO 3','F','02','07121960','012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','0       ','02','00','0000000000','00','00','00','01022023',28022023,'A',           0,100,'          0\n','2023-05-04 12:16:46',0);

-- ============================================================
-- osemm_historicos.novedades_sss_rechazados
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `novedades_sss_rechazados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lote` varchar(255) DEFAULT NULL,
  `id_persona` int DEFAULT NULL,
  `periodo` varchar(255) DEFAULT NULL,
  `rnos` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuit` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuil_titular` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `parentesco` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuil` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `td` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `nd` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `ayn` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `sexo` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `est_civil` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fn` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `nacionalidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `calle` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `numero` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `piso` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `dto` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `localidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cp` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `provincia` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `tipo_dom` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `telefono` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `revista` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `incapacidad` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `tbt` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fec_alta` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fec_cierre` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_mov` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error2` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cod_error3` varchar(255) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fechador` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cuit` (`cuit`),
  KEY `cuil_titular` (`cuil_titular`),
  KEY `parentesco` (`parentesco`),
  KEY `nd` (`nd`),
  KEY `fec_cierre` (`fec_cierre`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=45655 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_historicos`.`novedades_sss_rechazados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1001,1001,1001,'2023-02',111605,'20000000001','20000000001','01','20000000001','DU','90000001','PERSONA EJEMPLO 1','M','02',13021980,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1754    ','02','00','0000000000','00','00','00','01022023',28022023,'A',         138,300,'          0\n','2023-05-04 12:52:55');
INSERT INTO `osemm_historicos`.`novedades_sss_rechazados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1002,1002,1002,'2023-02',111605,'20000000002','20000000002','00','20000000002','DU','90000002','PERSONA EJEMPLO 2','F','01','09091996','000','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','1623    ','02','00','0000000000','00','00','00','01022023',28022023,'A',          92,100,'          0\n','2023-05-04 12:52:55');
INSERT INTO `osemm_historicos`.`novedades_sss_rechazados` (`id`,`id_lote`,`id_persona`,`periodo`,`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`dto`,`localidad`,`cp`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`fec_alta`,`fec_cierre`,`cod_mov`,`cod_error`,`cod_error2`,`cod_error3`,`fechador`) VALUES (1003,1003,1003,'2023-02',111605,'20000000003','20000000003','03','20000000003','DU','90000003','PERSONA EJEMPLO 3','M','01','05012015','012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO','5260    ','03','00','0000000000','00','00','00','01022023',28022023,'A',         138,107,'          0\n','2023-05-04 12:52:55');

-- ============================================================
-- osemm_historicos.sss_catalogo_errores
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `sss_catalogo_errores` (
  `codigo` varchar(3) NOT NULL,
  `campo` varchar(120) NOT NULL DEFAULT '',
  `descripcion` varchar(500) NOT NULL,
  `accion` varchar(500) NOT NULL,
  `version_instructivo` varchar(30) NOT NULL DEFAULT '2026-07 v6',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_historicos`.`sss_catalogo_errores` (`codigo`,`campo`,`descripcion`,`accion`,`version_instructivo`,`activo`) VALUES ('000','Validacion','Novedad aceptada. Sin errores de validacion.','Ninguna.','2026-07 v6',1);
INSERT INTO `osemm_historicos`.`sss_catalogo_errores` (`codigo`,`campo`,`descripcion`,`accion`,`version_instructivo`,`activo`) VALUES ('001','CUIT del empleador','Debe estar informado.','Corregir el CUIT del empleador.','2026-07 v6',1);
INSERT INTO `osemm_historicos`.`sss_catalogo_errores` (`codigo`,`campo`,`descripcion`,`accion`,`version_instructivo`,`activo`) VALUES ('002','CUIT del empleador','Debe tener 11 caracteres numericos.','Corregir el CUIT del empleador.','2026-07 v6',1);

-- ============================================================
-- osemm_historicos.sss_presentacion_control
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `sss_presentacion_control` (
  `id_lote` int NOT NULL,
  `periodo` varchar(7) NOT NULL,
  `estado` varchar(40) NOT NULL DEFAULT 'PREPARADO',
  `archivo_envio` varchar(255) DEFAULT NULL,
  `hash_archivo` char(64) DEFAULT NULL,
  `cantidad_movimientos` int NOT NULL DEFAULT '0',
  `fecha_cierre` date DEFAULT NULL,
  `resultados_disponibles_desde` date DEFAULT NULL,
  `fecha_generado` datetime DEFAULT NULL,
  `fecha_enviado` datetime DEFAULT NULL,
  `fecha_error_inmediato` datetime DEFAULT NULL,
  `fecha_resultado` datetime DEFAULT NULL,
  `ultimo_error` text,
  `id_usuario` int DEFAULT NULL,
  `actualizado` datetime NOT NULL,
  PRIMARY KEY (`id_lote`),
  KEY `idx_periodo` (`periodo`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_historicos`.`sss_presentacion_control` (`id_lote`,`periodo`,`estado`,`archivo_envio`,`hash_archivo`,`cantidad_movimientos`,`fecha_cierre`,`resultados_disponibles_desde`,`fecha_generado`,`fecha_enviado`,`fecha_error_inmediato`,`fecha_resultado`,`ultimo_error`,`id_usuario`,`actualizado`) VALUES (1001,'2026-07','ERRORES_INMEDIATOS_IMPORTADOS','OSEMM_novedades_2026-07.txt','[REDACTED]',2430,'2026-08-13','2026-08-18','2026-08-08 00:27:14','2026-08-08 00:27:31','2026-08-08 00:31:52',NULL,'DATO ANONIMIZADO','PERSONA EJEMPLO 1','2026-08-07 21:25:21');

-- ============================================================
-- osemm_historicos.sss_afiliado_cronologia
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `sss_afiliado_cronologia` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `id_persona` int NOT NULL,
  `id_lote` int DEFAULT NULL,
  `periodo` varchar(7) DEFAULT NULL,
  `estado` varchar(40) NOT NULL,
  `codigo_error` varchar(50) DEFAULT NULL,
  `detalle` varchar(500) NOT NULL,
  `origen` varchar(30) NOT NULL DEFAULT 'SSS',
  `id_usuario` int DEFAULT NULL,
  `fechador` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_persona_fecha` (`id_persona`,`fechador`),
  KEY `idx_lote` (`id_lote`)
) ENGINE=InnoDB AUTO_INCREMENT=658 DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_historicos`.`sss_afiliado_cronologia` (`id`,`id_persona`,`id_lote`,`periodo`,`estado`,`codigo_error`,`detalle`,`origen`,`id_usuario`,`fechador`) VALUES (1001,1001,1001,'2026-07','ERROR_VALIDACION','066                                               ','066 - Parentesco 3 o 5 con edad no admitida.','SSS','PERSONA EJEMPLO 1','2026-08-07 19:29:54');
INSERT INTO `osemm_historicos`.`sss_afiliado_cronologia` (`id`,`id_persona`,`id_lote`,`periodo`,`estado`,`codigo_error`,`detalle`,`origen`,`id_usuario`,`fechador`) VALUES (1002,1002,1002,'2026-07','ERROR_VALIDACION','066-078                                           ','066 - Parentesco 3 o 5 con edad no admitida. | 078 - Codigo sin catalogar','SSS','PERSONA EJEMPLO 2','2026-08-07 19:29:54');
INSERT INTO `osemm_historicos`.`sss_afiliado_cronologia` (`id`,`id_persona`,`id_lote`,`periodo`,`estado`,`codigo_error`,`detalle`,`origen`,`id_usuario`,`fechador`) VALUES (1003,1003,1003,'2026-07','ERROR_VALIDACION','078                                               ','078 - Codigo sin catalogar','SSS','PERSONA EJEMPLO 3','2026-08-07 19:29:54');

-- ============================================================
-- osemm_historicos.sss_cronograma_ftp
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `sss_cronograma_ftp` (
  `periodo` char(6) NOT NULL,
  `fecha_apertura` date NOT NULL,
  `fecha_cierre` date NOT NULL,
  `fecha_respuesta` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `fuente` varchar(255) NOT NULL,
  `actualizado` datetime NOT NULL,
  PRIMARY KEY (`periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_historicos`.`sss_cronograma_ftp` (`periodo`,`fecha_apertura`,`fecha_cierre`,`fecha_respuesta`,`fecha_devolucion`,`fuente`,`actualizado`) VALUES (202511,'2025-12-01','2025-12-15','2025-12-18','2025-12-23','cronograma_ftp.pdf','2026-08-07 21:37:30');
INSERT INTO `osemm_historicos`.`sss_cronograma_ftp` (`periodo`,`fecha_apertura`,`fecha_cierre`,`fecha_respuesta`,`fecha_devolucion`,`fuente`,`actualizado`) VALUES (202512,'2026-01-01','2026-01-14','2026-01-16','2026-01-23','cronograma_ftp.pdf','2026-08-07 21:37:30');
INSERT INTO `osemm_historicos`.`sss_cronograma_ftp` (`periodo`,`fecha_apertura`,`fecha_cierre`,`fecha_respuesta`,`fecha_devolucion`,`fuente`,`actualizado`) VALUES (202601,'2026-02-01','2026-02-16','2026-02-19','2026-02-24','cronograma_ftp.pdf','2026-08-07 21:37:30');

-- ============================================================
-- osemm_padron.persona
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `persona` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuil` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `apellido` varchar(60) CHARACTER SET latin1 NOT NULL,
  `nombre` varchar(70) CHARACTER SET latin1 NOT NULL,
  `td` enum('DNI','CI','PAS','LE','LC','DNM','PRO','DU') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'tipo documento',
  `nd` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'numero de documento',
  `telef_celular` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `fn` date DEFAULT NULL COMMENT 'fecha nacimiento',
  `sexo` enum('F','M','I') CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `id_estado_civil` int NOT NULL DEFAULT '2',
  `id_nacionalidad` int NOT NULL DEFAULT '1',
  `id_domicilio` int NOT NULL,
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL COMMENT 'usuario q lo creo',
  `email` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nd` (`nd`),
  KEY `apellido` (`apellido`,`nombre`),
  KEY `sexo` (`sexo`),
  KEY `id_nacionalidad` (`id_nacionalidad`),
  KEY `id_estado_civil` (`id_estado_civil`),
  KEY `id_domicilio` (`id_domicilio`),
  KEY `id_usuario` (`id_usuario`),
  KEY `cuil` (`cuil`)
) ENGINE=InnoDB AUTO_INCREMENT=179832 DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_padron`.`persona` (`id`,`cuil`,`apellido`,`nombre`,`td`,`nd`,`telef_celular`,`fn`,`sexo`,`id_estado_civil`,`id_nacionalidad`,`id_domicilio`,`fechador`,`id_usuario`,`email`) VALUES (1001,'20000000001','PERSONA EJEMPLO 1','PERSONA EJEMPLO 1','LE','90000001','0000000000','1931-02-16','M',1001,1001,'DATO ANONIMIZADO','2019-03-19 17:24:39','PERSONA EJEMPLO 1','persona1@example.invalid');
INSERT INTO `osemm_padron`.`persona` (`id`,`cuil`,`apellido`,`nombre`,`td`,`nd`,`telef_celular`,`fn`,`sexo`,`id_estado_civil`,`id_nacionalidad`,`id_domicilio`,`fechador`,`id_usuario`,`email`) VALUES (1002,'20000000002','PERSONA EJEMPLO 2','PERSONA EJEMPLO 2','LC','90000002','0000000000','1934-12-19','F',1002,1002,'DATO ANONIMIZADO','2019-03-20 10:36:26','PERSONA EJEMPLO 2','persona2@example.invalid');
INSERT INTO `osemm_padron`.`persona` (`id`,`cuil`,`apellido`,`nombre`,`td`,`nd`,`telef_celular`,`fn`,`sexo`,`id_estado_civil`,`id_nacionalidad`,`id_domicilio`,`fechador`,`id_usuario`,`email`) VALUES (1003,'20000000003','PERSONA EJEMPLO 3','PERSONA EJEMPLO 3','DNI','90000003','0000000000','1952-12-10','M',1003,1003,'DATO ANONIMIZADO','2019-03-20 10:46:02','PERSONA EJEMPLO 3','persona3@example.invalid');

-- ============================================================
-- osemm_padron.afiliados
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `afiliados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_persona` int DEFAULT NULL,
  `id_titular` int DEFAULT NULL,
  `id_parentesco` int DEFAULT NULL,
  `id_desreguladora` int DEFAULT '1',
  `nben` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `gpar` varchar(4) CHARACTER SET latin1 DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `incapacidad` enum('00','01') CHARACTER SET latin1 DEFAULT NULL COMMENT 'al reemplazar la tabla familiar por esta, no tengo donde poner la incapacidad del familiar. Para el titular esta en opcion, me parece que ambas incapacidades irian aca',
  `id_plan_medico` int DEFAULT '1',
  `cbu` varchar(35) CHARACTER SET latin1 DEFAULT NULL,
  `id_patologia` int DEFAULT '1',
  `id_tipo_aporte` int DEFAULT '0',
  `cuit__` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'viene de ddjjFinal',
  `id_revista` int DEFAULT '0',
  `filial` int DEFAULT NULL,
  `habilitado_emision_carnet` enum('S','N','T') CHARACTER SET latin1 DEFAULT 'S',
  `observaciones` text CHARACTER SET latin1,
  `id_plan_anterior` int DEFAULT '1',
  `tiene_credencial` enum('S','N') CHARACTER SET latin1 DEFAULT 'N',
  `vencimiento_credencial` date DEFAULT NULL,
  `id_sanatorio` int DEFAULT '1',
  `delaactividad` enum('SI','NO') CHARACTER SET latin1 DEFAULT 'NO',
  `origen` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `categoria` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `estado_dia` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `estado_app` varchar(100) DEFAULT 'No informado',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_persona` (`id_persona`),
  KEY `id_titular` (`id_titular`),
  KEY `nben` (`nben`,`gpar`),
  KEY `id_desreguladora` (`id_desreguladora`),
  KEY `id_parentesco` (`id_parentesco`),
  KEY `id_plan_medico` (`id_plan_medico`),
  KEY `id_patologia` (`id_patologia`),
  KEY `id_tipo_aporte` (`id_tipo_aporte`),
  KEY `incapacidad` (`incapacidad`),
  KEY `habilitado_emision_carnet` (`habilitado_emision_carnet`),
  KEY `id_sanatorio` (`id_sanatorio`),
  KEY `estado_dia` (`estado_dia`),
  KEY `delaactividad` (`delaactividad`),
  KEY `id_revista` (`id_revista`),
  CONSTRAINT `afiliados_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `afiliados_ibfk_2` FOREIGN KEY (`id_desreguladora`) REFERENCES `desreguladoras` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=178054 DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_padron`.`afiliados` (`id`,`id_persona`,`id_titular`,`id_parentesco`,`id_desreguladora`,`nben`,`gpar`,`id_usuario`,`fechador`,`incapacidad`,`id_plan_medico`,`cbu`,`id_patologia`,`id_tipo_aporte`,`cuit__`,`id_revista`,`filial`,`habilitado_emision_carnet`,`observaciones`,`id_plan_anterior`,`tiene_credencial`,`vencimiento_credencial`,`id_sanatorio`,`delaactividad`,`origen`,`categoria`,`estado_dia`,`estado_app`) VALUES (1001,1001,1001,1001,1001,'EJEMPLO1','EJEMPLO1','PERSONA EJEMPLO 1','2019-03-19 17:24:39','00',1001,NULL,1001,1001,NULL,1001,2,'S','DATO ANONIMIZADO',1001,'[REDACTED]',NULL,1001,'NO',NULL,'01','BAJA@2022-07-08','No informado');
INSERT INTO `osemm_padron`.`afiliados` (`id`,`id_persona`,`id_titular`,`id_parentesco`,`id_desreguladora`,`nben`,`gpar`,`id_usuario`,`fechador`,`incapacidad`,`id_plan_medico`,`cbu`,`id_patologia`,`id_tipo_aporte`,`cuit__`,`id_revista`,`filial`,`habilitado_emision_carnet`,`observaciones`,`id_plan_anterior`,`tiene_credencial`,`vencimiento_credencial`,`id_sanatorio`,`delaactividad`,`origen`,`categoria`,`estado_dia`,`estado_app`) VALUES (1002,1002,1002,1002,1002,'EJEMPLO2','EJEMPLO2','PERSONA EJEMPLO 2','2019-03-20 10:36:26','00',1002,NULL,1002,1002,NULL,1002,2,'S','DATO ANONIMIZADO',NULL,'[REDACTED]',NULL,1002,'NO',NULL,'01','BAJA@2022-07-08','No informado');
INSERT INTO `osemm_padron`.`afiliados` (`id`,`id_persona`,`id_titular`,`id_parentesco`,`id_desreguladora`,`nben`,`gpar`,`id_usuario`,`fechador`,`incapacidad`,`id_plan_medico`,`cbu`,`id_patologia`,`id_tipo_aporte`,`cuit__`,`id_revista`,`filial`,`habilitado_emision_carnet`,`observaciones`,`id_plan_anterior`,`tiene_credencial`,`vencimiento_credencial`,`id_sanatorio`,`delaactividad`,`origen`,`categoria`,`estado_dia`,`estado_app`) VALUES (1003,1003,1003,1003,1003,'EJEMPLO3','EJEMPLO3','PERSONA EJEMPLO 3','2019-03-20 10:46:02','00',1003,NULL,1003,1003,NULL,1003,NULL,'S','DATO ANONIMIZADO',NULL,'[REDACTED]',NULL,1003,'NO',NULL,NULL,'BAJA@2019-10-30','No informado');

-- ============================================================
-- osemm_padron.desreguladoras
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `desreguladoras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `convenio` varchar(30) CHARACTER SET utf8mb3 DEFAULT NULL,
  `id_convenio_real` int DEFAULT NULL,
  `real` int DEFAULT '0',
  `convenio_real` varchar(55) CHARACTER SET utf8mb3 DEFAULT NULL,
  `capita` varchar(55) CHARACTER SET utf8mb3 DEFAULT NULL,
  `grupo_fisca` varchar(55) CHARACTER SET utf8mb3 DEFAULT NULL,
  `user_ftp` varchar(55) CHARACTER SET utf8mb3 DEFAULT NULL,
  `cuenta` varchar(55) CHARACTER SET utf8mb3 DEFAULT NULL,
  `id_grupo_convenio` int DEFAULT '0' COMMENT 'osemm_usuarios.grupos_covenios',
  PRIMARY KEY (`id`),
  KEY `convenio` (`convenio`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_padron`.`desreguladoras` (`id`,`convenio`,`id_convenio_real`,`real`,`convenio_real`,`capita`,`grupo_fisca`,`user_ftp`,`cuenta`,`id_grupo_convenio`) VALUES (1001,'PROPIOS',1001,1,'PROPIOS',NULL,'PROPIOS',NULL,NULL,1001);
INSERT INTO `osemm_padron`.`desreguladoras` (`id`,`convenio`,`id_convenio_real`,`real`,`convenio_real`,`capita`,`grupo_fisca`,`user_ftp`,`cuenta`,`id_grupo_convenio`) VALUES (1002,'NOVEDADES_SSS',1002,0,'SIN DATO',NULL,'SIN DATO',NULL,NULL,1002);
INSERT INTO `osemm_padron`.`desreguladoras` (`id`,`convenio`,`id_convenio_real`,`real`,`convenio_real`,`capita`,`grupo_fisca`,`user_ftp`,`cuenta`,`id_grupo_convenio`) VALUES (1003,'altas_pendientes',1003,0,'SIN DATO',NULL,'SIN DATO',NULL,NULL,1003);

-- ============================================================
-- osemm_padron.parentesco
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `parentesco` (
  `id` int NOT NULL,
  `parentesco` varchar(80) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `codsss` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `adicional_aporte` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `osemm_padron`.`parentesco` (`id`,`parentesco`,`codsss`,`adicional_aporte`) VALUES (1001,'Titular',0,0);
INSERT INTO `osemm_padron`.`parentesco` (`id`,`parentesco`,`codsss`,`adicional_aporte`) VALUES (1002,'Cónyuge',1,0);
INSERT INTO `osemm_padron`.`parentesco` (`id`,`parentesco`,`codsss`,`adicional_aporte`) VALUES (1003,'Concubino/a',2,0);

-- ============================================================
-- osemm_padron.tipo_beneficiario_titular
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `tipo_beneficiario_titular` (
  `id` int NOT NULL AUTO_INCREMENT,
  `beneficiario` varchar(80) NOT NULL,
  `codsss` varchar(15) NOT NULL,
  `tipo` varchar(15) DEFAULT NULL,
  `sigla` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_padron`.`tipo_beneficiario_titular` (`id`,`beneficiario`,`codsss`,`tipo`,`sigla`) VALUES (1001,'Relación de Dependencia','00',1,'RG');
INSERT INTO `osemm_padron`.`tipo_beneficiario_titular` (`id`,`beneficiario`,`codsss`,`tipo`,`sigla`) VALUES (1002,'Pasantes','01',1,'PAS');
INSERT INTO `osemm_padron`.`tipo_beneficiario_titular` (`id`,`beneficiario`,`codsss`,`tipo`,`sigla`) VALUES (1003,'Jubilados del sistema nacional de seguros de salud','02',3,'JUB');

-- ============================================================
-- osemm_padron.lst_novedades_presentaciones
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `lst_novedades_presentaciones` (
  `id` int NOT NULL DEFAULT '0',
  `descripcion` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `descripcion2` varchar(6) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `estado` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `archivo` varchar(80) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `q` bigint NOT NULL DEFAULT '0',
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `errores` int NOT NULL DEFAULT '0',
  `errores_q` int NOT NULL DEFAULT '0',
  `aceptados` int NOT NULL DEFAULT '0',
  `aceptados_q` int NOT NULL DEFAULT '0',
  `rechazados` int NOT NULL DEFAULT '0',
  `rechazados_q` int NOT NULL DEFAULT '0',
  `fecha_vencimiento` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_mostrar` varchar(21) CHARACTER SET utf8mb3 DEFAULT NULL,
  `usuario` char(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `osemm_padron`.`lst_novedades_presentaciones` (`id`,`descripcion`,`descripcion2`,`estado`,`archivo`,`q`,`fechador`,`errores`,`errores_q`,`aceptados`,`aceptados_q`,`rechazados`,`rechazados_q`,`fecha_vencimiento`,`fecha_mostrar`,`usuario`) VALUES (1001,'2026-07',202607,'Proceso','2026-07-01',111,'2026-07-15 22:00:24',14044,661,0,0,0,0,'2026-08-13','15/07/2026 22:00','PERSONA EJEMPLO 1');
INSERT INTO `osemm_padron`.`lst_novedades_presentaciones` (`id`,`descripcion`,`descripcion2`,`estado`,`archivo`,`q`,`fechador`,`errores`,`errores_q`,`aceptados`,`aceptados_q`,`rechazados`,`rechazados_q`,`fecha_vencimiento`,`fecha_mostrar`,`usuario`) VALUES (1002,'2026-06',202606,'cerrado','2026-06-01',134,'2026-06-15 22:00:12',13925,14,14018,87,14019,18,'2026-07-14','15/06/2026 22:00','PERSONA EJEMPLO 2');
INSERT INTO `osemm_padron`.`lst_novedades_presentaciones` (`id`,`descripcion`,`descripcion2`,`estado`,`archivo`,`q`,`fechador`,`errores`,`errores_q`,`aceptados`,`aceptados_q`,`rechazados`,`rechazados_q`,`fecha_vencimiento`,`fecha_mostrar`,`usuario`) VALUES (1003,'2026-05',202605,'cerrado','2026-05-01',120,'2026-05-15 22:00:23',13810,65,13917,47,13918,7,'2026-06-12','15/05/2026 22:00','PERSONA EJEMPLO 3');

-- ============================================================
-- osemm_padron.tmp_novedades
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `tmp_novedades` (
  `rnos` varchar(6) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  `cuit` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cuil_titular` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parentesco` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cuil` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `td` varchar(2) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  `nd` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'numero de documento',
  `ayn` varchar(30) NOT NULL DEFAULT '',
  `sexo` enum('F','M','I') CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `est_civil` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `fn` varchar(8) CHARACTER SET utf8mb3 DEFAULT NULL,
  `nacionalidad` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `calle` varchar(80) NOT NULL,
  `numero` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `piso` varchar(4) DEFAULT NULL,
  `depto` varchar(10) DEFAULT NULL,
  `localidad` varchar(20) CHARACTER SET utf8mb3 DEFAULT NULL,
  `codigo_postal` int DEFAULT NULL,
  `provincia` varchar(11) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tipo_dom` char(0) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  `telefono` varchar(1) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  `revista` varchar(2) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  `incapacidad` enum('00','01') DEFAULT NULL COMMENT 'al reemplazar la tabla familiar por esta, no tengo donde poner la incapacidad del familiar. Para el titular esta en opcion, me parece que ambas incapacidades irian aca',
  `tbt` varchar(15) NOT NULL,
  `f_alta` varchar(8) CHARACTER SET utf8mb3 DEFAULT NULL,
  `fec_cierre_2` varchar(8) CHARACTER SET utf8mb3 DEFAULT NULL,
  `movimiento` varchar(1) CHARACTER SET utf8mb3 NOT NULL DEFAULT '',
  KEY `cuil_titular` (`cuil_titular`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `osemm_padron`.`tmp_novedades` (`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`depto`,`localidad`,`codigo_postal`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`f_alta`,`fec_cierre_2`,`movimiento`) VALUES (111605,'20000000001','20000000001','08','20000000001','DU','90000001','PERSONA EJEMPLO 1','F','01',12101989,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO',1425,'01','','0000000000','00','00','00','01072026',31072026,'A');
INSERT INTO `osemm_padron`.`tmp_novedades` (`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`depto`,`localidad`,`codigo_postal`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`f_alta`,`fec_cierre_2`,`movimiento`) VALUES (111605,'20000000002','20000000002','08','20000000002','DU','90000002','PERSONA EJEMPLO 2','F','01',25102006,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO',1166,'01','','0000000000','00','00','00','01072026',31072026,'A');
INSERT INTO `osemm_padron`.`tmp_novedades` (`rnos`,`cuit`,`cuil_titular`,`parentesco`,`cuil`,`td`,`nd`,`ayn`,`sexo`,`est_civil`,`fn`,`nacionalidad`,`calle`,`numero`,`piso`,`depto`,`localidad`,`codigo_postal`,`provincia`,`tipo_dom`,`telefono`,`revista`,`incapacidad`,`tbt`,`f_alta`,`fec_cierre_2`,`movimiento`) VALUES (111605,'20000000003','20000000003','05','20000000003','DU','90000003','PERSONA EJEMPLO 3','M','01',14052002,'012','DATO ANONIMIZADO','0','0','0','DATO ANONIMIZADO',1005,'01','','0000000000','00','00','00','01072026',31072026,'A');

-- ============================================================
-- osemm_padron.tmp_afiliados_novedades_mostrar
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `tmp_afiliados_novedades_mostrar` (
  `id_expo` int DEFAULT NULL,
  `id_persona` int NOT NULL DEFAULT '0',
  `id_afiliado` int NOT NULL DEFAULT '0',
  `id_titular` int NOT NULL DEFAULT '0',
  `tipo_mov` varchar(10) COLLATE latin1_general_ci DEFAULT 'A',
  `cuil_titular` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `cuil` varchar(11) COLLATE latin1_general_ci DEFAULT NULL,
  `nd` varchar(9) COLLATE latin1_general_ci NOT NULL,
  `ayn` varchar(131) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fn` date DEFAULT '1880-01-01',
  `sexo` enum('F','M','I') COLLATE latin1_general_ci DEFAULT NULL,
  `edad` bigint DEFAULT NULL,
  `parentesco` varchar(80) COLLATE latin1_general_ci DEFAULT NULL,
  `incapacidad` enum('00','01') COLLATE latin1_general_ci DEFAULT NULL,
  `desreguladora` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `tbt` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `fecha_movimiento` date DEFAULT NULL,
  `errores` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `rechazos` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  KEY `id_titular` (`id_titular`),
  KEY `id_afiliado` (`id_afiliado`),
  KEY `id_persona` (`id_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

INSERT INTO `osemm_padron`.`tmp_afiliados_novedades_mostrar` (`id_expo`,`id_persona`,`id_afiliado`,`id_titular`,`tipo_mov`,`cuil_titular`,`cuil`,`nd`,`ayn`,`fn`,`sexo`,`edad`,`parentesco`,`incapacidad`,`desreguladora`,`tbt`,`fecha_movimiento`,`errores`,`rechazos`) VALUES (1001,1001,1001,1001,'A','20000000001','20000000001','90000001','PERSONA EJEMPLO 1','1962-05-01','M',64,'Titular','00','Red de seguro medico','RG','2026-07-01',NULL,NULL);
INSERT INTO `osemm_padron`.`tmp_afiliados_novedades_mostrar` (`id_expo`,`id_persona`,`id_afiliado`,`id_titular`,`tipo_mov`,`cuil_titular`,`cuil`,`nd`,`ayn`,`fn`,`sexo`,`edad`,`parentesco`,`incapacidad`,`desreguladora`,`tbt`,`fecha_movimiento`,`errores`,`rechazos`) VALUES (1002,1002,1002,1002,'A','20000000002','20000000002','90000002','PERSONA EJEMPLO 2','1966-07-15','M',60,'Titular','00','Red de seguro medico','DESEM','2026-07-01',NULL,NULL);
INSERT INTO `osemm_padron`.`tmp_afiliados_novedades_mostrar` (`id_expo`,`id_persona`,`id_afiliado`,`id_titular`,`tipo_mov`,`cuil_titular`,`cuil`,`nd`,`ayn`,`fn`,`sexo`,`edad`,`parentesco`,`incapacidad`,`desreguladora`,`tbt`,`fecha_movimiento`,`errores`,`rechazos`) VALUES (1003,1003,1003,1003,'A','20000000003','20000000003','90000003','PERSONA EJEMPLO 3','1975-11-04','F',50,'Titular','00','Red de seguro medico','RG','2026-07-01',NULL,NULL);

-- ============================================================
-- osemm_padron.log_eventos
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `log_eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evento` varchar(100) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `fechador` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fechador_fin` timestamp NULL DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `fecha_parametro` varchar(10) DEFAULT NULL,
  `query_where` text,
  `filtros_cabecera` longtext,
  `otros_parametros` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14929 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_padron`.`log_eventos` (`id`,`evento`,`ip`,`fechador`,`fechador_fin`,`id_usuario`,`fecha_parametro`,`query_where`,`filtros_cabecera`,`otros_parametros`) VALUES (1001,'Consulta 20256872863','127.0.0.1','2020-07-28 12:33:35',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `osemm_padron`.`log_eventos` (`id`,`evento`,`ip`,`fechador`,`fechador_fin`,`id_usuario`,`fecha_parametro`,`query_where`,`filtros_cabecera`,`otros_parametros`) VALUES (1002,'Consulta 20259073147','127.0.0.1','2020-07-28 12:35:12',NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `osemm_padron`.`log_eventos` (`id`,`evento`,`ip`,`fechador`,`fechador_fin`,`id_usuario`,`fecha_parametro`,`query_where`,`filtros_cabecera`,`otros_parametros`) VALUES (1003,'Consulta 20284642946','127.0.0.1','2020-07-28 12:35:40',NULL,NULL,NULL,NULL,NULL,NULL);

-- ============================================================
-- osemm_usuarios.users
-- ============================================================
-- Definicion observada (no ejecutar sobre una base existente sin revision).
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` char(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `clave` char(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `estado` enum('alta','baja') DEFAULT NULL,
  `estado_real` varchar(20) DEFAULT NULL,
  `nombrecompleto` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `tel_contacto` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `comentario` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `hash_session` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

INSERT INTO `osemm_usuarios`.`users` (`id`,`usuario`,`clave`,`estado`,`estado_real`,`nombrecompleto`,`email`,`tel_contacto`,`comentario`,`hash_session`) VALUES (1001,'PERSONA EJEMPLO 1','[REDACTED]','alta','alta','PERSONA EJEMPLO 1',NULL,NULL,NULL,'[REDACTED]');
INSERT INTO `osemm_usuarios`.`users` (`id`,`usuario`,`clave`,`estado`,`estado_real`,`nombrecompleto`,`email`,`tel_contacto`,`comentario`,`hash_session`) VALUES (1002,'PERSONA EJEMPLO 2','[REDACTED]','alta','alta','PERSONA EJEMPLO 2',NULL,NULL,NULL,'[REDACTED]');
INSERT INTO `osemm_usuarios`.`users` (`id`,`usuario`,`clave`,`estado`,`estado_real`,`nombrecompleto`,`email`,`tel_contacto`,`comentario`,`hash_session`) VALUES (1003,'PERSONA EJEMPLO 3','[REDACTED]','alta','alta','PERSONA EJEMPLO 3',NULL,NULL,NULL,'[REDACTED]');

