-- Stored procedures y vistas utilizados por actualizacion_padron_sss
-- Generado: 2026-08-08T18:09:56+02:00

DELIMITER $$

DROP PROCEDURE IF EXISTS `osemm_padron`.`novedades_crea_nuevo_periodo`$$
CREATE PROCEDURE `novedades_crea_nuevo_periodo`()
BEGIN
	
	--
	DECLARE p_lotes_a_cerrar INTEGER ;
	DECLARE p_id_anterior INTEGER ;
	DECLARE p_id_errores INTEGER ;
	DECLARE p_id_rechazados INTEGER ;
	declare p_id_ult_devErr INTEGER;
	--
	CALL osemm_padron.`novedades_envio_presentaciones`();
	#SELECT * FROM osemm_padron.lst_novedades_presentaciones ;
	--
	SELECT id into p_id_anterior 
	FROM osemm_padron.lst_novedades_presentaciones 
	ORDER BY id DESC 
	LIMIT 1 ;
	--
	SELECT errores into p_id_errores
	FROM osemm_padron.lst_novedades_presentaciones 
	ORDER BY id DESC 
	LIMIT 1 ;
	--
	SELECT rechazados into p_id_rechazados
	FROM osemm_padron.lst_novedades_presentaciones 
	ORDER BY id DESC 
	LIMIT 1 ;
	--
	SELECT COUNT(*) AS q INTO p_lotes_a_cerrar
	FROM osemm_historicos.lotes 
	WHERE proceso='novedades_exportables'
		AND estado='Proceso'
		AND obrasocial<CURDATE()
		
		;
	--
	IF p_lotes_a_cerrar>0 THEN	
		UPDATE osemm_historicos.lotes SET estado='Cerrado' WHERE estado='Proceso' ;
		--
		INSERT INTO osemm_historicos.lotes(descripcion,archivo,id_usuario,estado,proceso)
		SELECT DATE_ADD(archivo,INTERVAL 1 MONTH) AS descrip, 		
			DATE_ADD(archivo,INTERVAL 1 MONTH) AS descripcion,
			1,'Proceso','novedades_exportables'
		#select *
		FROM osemm_historicos.lotes 
		WHERE proceso='novedades_exportables'
		ORDER BY id DESC 
		LIMIT 1 ;
		--
		SET @X = LAST_INSERT_ID();
		--
	END IF;
	--
	-- Actualizo errores 
	UPDATE osemm_historicos.`novedades_sss_errores` t 
	JOIN osemm_padron.persona p ON t.nd=p.nd 
	SET t.id_persona=p.id ;
	--
	-- Actualizo rechazados 
	UPDATE osemm_historicos.`novedades_sss_rechazados` a
	JOIN osemm_padron.persona p ON a.nd=p.nd 
	SET a.id_persona=p.id ;
	--	
	-- Agrego los errores anteriores
	INSERT INTO osemm_historicos.`novedades_exportables`(id_lote,id_persona,tipo_movimiento)
	SELECT @X,n.id_persona,'alta'
	#select *
	FROM osemm_historicos.`novedades_sss_errores` n
	JOIN osemm_historicos.`novedades_exportables` e ON n.id_persona=e.id_persona 
	WHERE n.id_lote=p_id_errores 
		AND e.id_lote=p_id_anterior ;
	--
	-- Agrego los rechazos anteriores 
	INSERT INTO osemm_historicos.`novedades_exportables`(id_lote,id_persona,tipo_movimiento)
	SELECT @X,n.id_persona,'alta'
	#select *
	FROM osemm_historicos.`novedades_sss_rechazados` n
	JOIN osemm_historicos.`novedades_exportables` e ON n.id_persona=e.id_persona 
	WHERE n.id_lote=p_id_rechazados 
		AND e.id_lote=p_id_anterior 
		AND n.id_persona NOT IN ( SELECT id_persona FROM osemm_historicos.`novedades_exportables` WHERE id_lote=@X );
	--
	-- Revisar para tomar el ultimo lote de error y cargarlos al envio nuevo de novedades
	-- es probable que convenga realizar esto en la carga de la ultima version del archivo devErr 
	-- 
	SELECT osemm_ppdev.`integracion_devErr_ult_lote_x_periodo`(DATE_FORMAT(archivo,'%Y%m')) AS p_id_ult_devErr
	FROM osemm_padron.lst_novedades_presentaciones 
	WHERE estado='Proceso'
	ORDER BY id DESC 
	LIMIT 1 ;
	--
	INSERT INTO osemm_historicos.`novedades_exportables`(id_lote,id_persona,tipo_movimiento)
	SELECT DISTINCT @X,p.id,'alta'
	#select *
	FROM osemm_historicos.`sd_sss_devolucion_err` er
	JOIN osemm_ppdev.`subsidio_discapacidad` sd ON er.id_cap=sd.id_cap 
	JOIN osemm_padron.persona p ON sd.nd_cargado=p.nd 
	WHERE id_lote=p_id_ult_devErr 
		AND TRIM(cod_rechazo) LIKE '%400%' 
		AND p.id NOT IN ( SELECT DISTINCT id_persona FROM osemm_historicos.`novedades_sss_aceptados`  ) 
		AND p.id NOT IN ( SELECT DISTINCT id_persona FROM osemm_historicos.`novedades_exportables` WHERE id_lote=@X ) ;
	--
    END$$

DROP PROCEDURE IF EXISTS `osemm_historicos`.`NOV_nuevo_periodo_automatico`$$
CREATE PROCEDURE `NOV_nuevo_periodo_automatico`()
BEGIN
	
	SELECT IF(obrasocial<CURDATE() AND obrasocial IS NOT NULL,1,0) INTO @vencimiento
	#select *
	FROM osemm_historicos.lotes 
	WHERE proceso='novedades_exportables'
		AND id=osemm_historicos.`get_id_presentacion_novedades_activa`()
	;
	--
	
	IF @vencimiento=1 THEN 
		INSERT INTO osemm_padron.`log_eventos`(evento,id_usuario)
		VALUES ('NOV_nuevo_periodo_automatico',1);
		
		set @p_lote_actual = osemm_historicos.`get_id_presentacion_novedades_activa`();
		 CALL osemm_padron.novedades_envio_presentaciones();
 
		 SELECT errores INTO @p_id_errores
		 #select *
		 FROM osemm_padron.lst_novedades_presentaciones
		 WHERE 1=1
			AND estado='proceso'
			and id=@p_lote_actual
		;
		--		
		--
		INSERT INTO osemm_historicos.lotes(descripcion,archivo,proceso,id_usuario,estado,obrasocial)
		SELECT MID(DATE_ADD(archivo,INTERVAL 1 MONTH),1,7),DATE_ADD(archivo,INTERVAL 1 MONTH),'novedades_exportables',1,'Proceso',
			CONCAT(MID(DATE_ADD(archivo,INTERVAL 2 MONTH),1,7),'-14')
		FROM osemm_historicos.lotes 
		WHERE proceso='novedades_exportables'
			AND id=osemm_historicos.`get_id_presentacion_novedades_activa`();
		set @p_id_nuevo_lote=last_insert_id();	
		--
		UPDATE osemm_historicos.lotes SET estado='cerrado' WHERE id=@p_lote_actual ;
		
		INSERT INTO osemm_historicos.`novedades_exportables`(id_lote,id_persona,tipo_mov,id_usuario,cod_error_)
		SELECT @p_id_nuevo_lote,id_persona,cod_mov,1,cod_error
		FROM osemm_historicos.`novedades_sss_errores`
		WHERE id_lote=@p_id_errores;
		--
	end if;
	
    END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`NOV_presentar_periodo`$$
CREATE PROCEDURE `NOV_presentar_periodo`(p_id_lote integer )
BEGIN
#Creo un log de presentacion
INSERT INTO osemm_historicos.`novedades_presentaciones` (id_lote) VALUES (p_id_lote);
SET @id_presentacion_novedades= LAST_INSERT_ID();
 
#Set de fecha de cierre de presentacion
SELECT archivo INTO @periodo_presentacion FROM osemm_historicos.lotes WHERE id = p_id_lote;
#Creo tmp_novedades
truncate table osemm_padron.tmp_novedades ;
insert into osemm_padron.tmp_novedades (rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,depto,localidad,codigo_postal,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,fec_cierre_2,movimiento)
SELECT '111605' AS rnos,
	#IF(rg.cuit IS NULL,p.cuil,rg.cuit) AS cuit,
	COALESCE(p2.cuil,p.cuil) as cuit,
	COALESCE(p2.cuil,p.cuil) AS cuil_titular,
	LPAD(pa.codsss,2,'00') AS parentesco,
	p.cuil,
	'DU' AS td,
	p.nd,
	RPAD(MID(TRIM(REPLACE(REPLACE(CONCAT(TRIM(p.apellido),' ',TRIM(p.nombre)),'Á',''),'Ð','D')),1,30),30,' ') AS ayn,
	p.sexo,
	LPAD(e.codsss,2,0) AS est_civil,
	CONCAT(MID(p.fn,9,2),MID(p.fn,6,2),MID(p.fn,1,4)) AS fn,
	LPAD(pais.codsss,3,'000') AS nacionalidad,
	mid(d.calle,1,20) as calle,
	RPAD(MID(TRIM(d.numero),1,4),4,' ') AS numero,
	RPAD(MID(TRIM(IF(d.piso = 0,' ',d.piso)),1,4),4,' ') AS piso,
	IF(d.depto = 0,' ',d.depto) AS depto,
	RPAD(MID(REPLACE(l.nomsss,'Ñ','#'),1,20),20,' ') AS localidad,
	l.cp AS codigo_postal,
	pr.codsss AS provincia,
	'' AS tipo_dom,
	' ' AS telefono,
	'00' AS revista,
	a.incapacidad,
	COALESCE(tbt2.codsss,tbt.codsss) AS tbt,
	DATE_FORMAT(COALESCE(t.fec_mov,@periodo_presentacion),'%d%m%Y') as f_mov,
	DATE_FORMAT(LAST_DAY(@periodo_presentacion),'%d%m%Y') AS fec_cierre_2,#periodo_presentacion
	tipo_mov AS movimiento
FROM (SELECT * FROM osemm_historicos.novedades_exportables WHERE id_lote=p_id_lote ) t
JOIN osemm_padron.persona p ON t.id_persona=p.id 
JOIN osemm_padron.afiliados a ON p.id=a.id_persona 
JOIN osemm_padron.domicilio d ON p.id_domicilio=d.id 
JOIN osemm_padron.localidad l ON d.id_localidad=l.id 
JOIN osemm_padron.provincia pr ON l.provincia=pr.cod 
JOIN osemm_padron.estadocivil e ON p.id_estado_civil=e.id 
JOIN osemm_padron.parentesco pa ON a.id_parentesco=pa.id 
JOIN osemm_padron.pais ON pais.id=p.id_nacionalidad 
JOIN osemm_padron.tipo_beneficiario_titular tbt ON a.id_tipo_aporte=tbt.id 
LEFT JOIN osemm_padron.afiliados a2 ON a.id_titular=a2.id 
LEFT JOIN osemm_padron.persona p2 ON a2.id_persona=p2.id
LEFT JOIN osemm_padron.tipo_beneficiario_titular tbt2 ON a2.id_tipo_aporte=tbt2.id ;
###
call osemm_padron.`NOV_presentar_cuits`();
#Update el cuit informado
UPDATE osemm_padron.tmp_novedades tn
JOIN osemm_padron.tmp_novedades_cuit tnc ON tnc.cuil  = tn.cuil_titular 
SET tn.cuit=tnc.cuit;
--
-- Modificaciones por validaciones 
UPDATE osemm_padron.tmp_novedades SET nacionalidad='012' WHERE nacionalidad='999'; #rectifico codigo 036
--
UPDATE osemm_padron.tmp_novedades SET cuit='33637617449' WHERE tbt='08'; #rectifico codigo 062
--
UPDATE osemm_padron.tmp_novedades SET revista='13' WHERE tbt='08'; #rectifico codigo 159
--
UPDATE osemm_padron.tmp_novedades 
SET cuit=cuil_titular
WHERE #`cuil`=cuit AND parentesco!='00' AND 
	tbt IN ('04','05','07','11');#rectifico codigo 063
--
DELETE FROM osemm_padron.tmp_novedades WHERE f_alta IS NULL;#rectifico codigo 058
DELETE FROM osemm_padron.tmp_novedades WHERE fn = '00000000';#rectifico codigo 033
--
UPDATE osemm_padron.tmp_novedades SET est_civil='02'
WHERE est_civil='01' AND parentesco='01'; #rectifico codigo 077
--
UPDATE osemm_padron.tmp_novedades SET ayn = REPLACE(ayn,'Ñ','N'); #rectifico codigo 023
--
UPDATE osemm_padron.`tmp_novedades` p 
SET p.cuil=prueba.`consulta_cuil`(p.nd,p.`sexo`)
WHERE p.`cuil`='';#rectifico codigo 012
--
SELECT osemm_historicos.get_id_ultimo_padron_sss() INTO @id_ultimo_padron_sss;
DROP TEMPORARY TABLE IF EXISTS osemm_historicos.tmp_cuil_sss;
CREATE TEMPORARY TABLE osemm_historicos.tmp_cuil_sss
SELECT p.parentesco,p.cuil,p.cuil_titular
FROM osemm_historicos.`padrones_sss` p
WHERE p.`id_lote`=@id_ultimo_padron_sss;
alter table osemm_historicos.tmp_cuil_sss add index cuil (cuil);
ALTER TABLE osemm_historicos.tmp_cuil_sss ADD INDEX cuil_titular (cuil_titular);
-- Esto arregla el error -065 
UPDATE osemm_padron.afiliados a
JOIN osemm_padron.persona p ON a.id_persona=p.id
JOIN osemm_historicos.`tmp_cuil_sss` pa ON p.cuil=pa.cuil AND pa.parentesco!='00'
JOIN osemm_padron.persona pp ON pa.cuil_titular=pp.cuil 
JOIN osemm_padron.afiliados aa ON pp.id=aa.id_persona 
SET a.id_titular=aa.id 
WHERE a.id_parentesco!=0 
AND a.id_titular=a.id ;
INSERT INTO osemm_historicos.novedades_presentaciones_log (id_lote,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,depto,localidad,codigo_postal,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,fec_cierre_2,movimiento)
SELECT @id_presentacion_novedades,rnos,cuit,cuil_titular,parentesco,cuil,td,nd,ayn,sexo,est_civil,fn,nacionalidad,calle,numero,piso,depto,localidad,codigo_postal,provincia,tipo_dom,telefono,revista,incapacidad,tbt,f_alta,fec_cierre_2,movimiento
FROM osemm_padron.tmp_novedades;
-- ############################### Recupero los datos del CUIT para los RG ####################################
/*
DROP TABLE IF EXISTS tit_cuiles ;
CREATE TABLE tit_cuiles
SELECT DISTINCT cuil_titular FROM osemm_padron.tmp_novedades ;
--
ALTER TABLE `osemm_padron`.`tit_cuiles` ADD COLUMN `cuit` VARCHAR(11) NULL AFTER `cuil_titular` ; 
ALTER TABLE `osemm_padron`.`tit_cuiles` CHANGE `cuil_titular` `cuil_titular` VARCHAR(11) CHARSET latin1 COLLATE latin1_swedish_ci NULL; 
-- 
DROP TEMPORARY TABLE IF EXISTS tmp_cuil_bmax ;
CREATE TEMPORARY TABLE tmp_cuil_bmax
SELECT t.`cuil_titular`,MAX(b) AS bmax
FROM tit_cuiles t 
JOIN osemm_historicos.`declaraciones_juradas` d ON t.cuil_titular=d.d 
GROUP BY 1 ;
--
DROP TEMPORARY TABLE IF EXISTS tmp_cul_bmax_cuit ;
CREATE TEMPORARY TABLE tmp_cul_bmax_cuit
SELECT DISTINCT t.cuil_titular,d.c 
FROM tmp_cuil_bmax t 
JOIN osemm_historicos.`declaraciones_juradas` d ON t.cuil_titular=d.d AND d.b=t.bmax ;
--
UPDATE tmp_cul_bmax_cuit t
JOIN tit_cuiles tt ON t.cuil_titular=tt.cuil_titular 
SET tt.cuit=t.c
where  ;
--
--
DROP TEMPORARY TABLE IF EXISTS tmp_cuil_bmax ;
CREATE TEMPORARY TABLE tmp_cuil_bmax
SELECT t.`cuil_titular`,MAX(h) AS bmax
FROM tit_cuiles t 
JOIN osemm.aportes a ON t.cuil_titular=a.k
WHERE t.cuit IS NULL  
GROUP BY 1 ;
--
DROP TEMPORARY TABLE IF EXISTS tmp_cul_bmax_cuit ;
CREATE TEMPORARY TABLE tmp_cul_bmax_cuit
SELECT DISTINCT t.cuil_titular,a.g 
FROM tmp_cuil_bmax t 
JOIN osemm.aportes a ON t.cuil_titular=a.k AND a.h=t.bmax
WHERE b='381' ;
--
UPDATE tmp_cul_bmax_cuit t
JOIN tit_cuiles tt ON t.cuil_titular=tt.cuil_titular 
SET tt.cuit=t.g
WHERE tt.cuit IS NULL ;
--
ALTER TABLE `osemm_padron`.`tit_cuiles` CHANGE `cuil_titular` `cuil_titular` VARCHAR(11) CHARSET latin1 COLLATE latin1_general_ci NULL; 
--
UPDATE osemm_padron.tmp_novedades t 
JOIN osemm_padron.tit_cuiles tt ON t.cuil_titular=tt.cuil_titular 
SET t.cuit=tt.cuit ;
*/
--
-- SELECT * FROM osemm_padron.tmp_novedades;
--
END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`NOV_mostrar_lote`$$
CREATE PROCEDURE `NOV_mostrar_lote`(IN p_id_lote INT, IN p_limit INT, IN p_offset INT, IN p_convenio_real VARCHAR(255))
BEGIN
	TRUNCATE TABLE osemm_padron.tmp_afiliados_novedades_mostrar;
	INSERT INTO osemm_padron.tmp_afiliados_novedades_mostrar (
		id_expo, id_persona, id_afiliado, id_titular, cuil, nd, ayn, fn,
		sexo, edad, parentesco, incapacidad, desreguladora, tipo_mov,fecha_movimiento,errores,rechazos
	)
	SELECT
		t.id AS id_expo, p.id AS id_persona, a.id AS id_afiliado, a.id_titular AS id_titular,
		p.cuil, p.nd, CONCAT(p.apellido, ' ', p.nombre) AS ayn, p.fn, p.sexo,
		TIMESTAMPDIFF(YEAR, p.fn, CURDATE()) AS edad, pa.parentesco, a.incapacidad,
		d.convenio AS desreguladora, t.tipo_mov, t.fec_mov, t.cod_error_, t.cod_rechazados
	FROM osemm_historicos.novedades_exportables t
	JOIN osemm_padron.persona p ON t.id_persona = p.id
	JOIN osemm_padron.afiliados a ON p.id = a.id_persona
	JOIN osemm_padron.desreguladoras d ON a.id_desreguladora = d.id
	JOIN osemm_padron.parentesco pa ON a.id_parentesco = pa.id
	WHERE t.id_lote = p_id_lote AND d.convenio_real = p_convenio_real;
	UPDATE osemm_padron.tmp_afiliados_novedades_mostrar SET cuil_titular = cuil WHERE id_titular = 0;
	UPDATE osemm_padron.tmp_afiliados_novedades_mostrar t
	JOIN osemm_padron.afiliados a2 ON t.id_afiliado = a2.id
	JOIN osemm_padron.tipo_beneficiario_titular tb ON tb.id = a2.id_tipo_aporte
	SET t.tbt = tb.sigla WHERE t.id_titular = 0;
	UPDATE osemm_padron.tmp_afiliados_novedades_mostrar t
	JOIN osemm_padron.afiliados a2 ON t.id_titular = a2.id
	JOIN osemm_padron.persona p2 ON a2.id_persona = p2.id
	JOIN osemm_padron.tipo_beneficiario_titular tb ON tb.id = a2.id_tipo_aporte
	SET t.cuil_titular = p2.cuil, t.tbt = tb.sigla WHERE t.id_titular != 0;
	CALL osemm_padron.NOV_mostrar_lote_incluir_errores(p_id_lote);
	SET @sql_pag = CONCAT('SELECT *, (SELECT COUNT(*) FROM osemm_padron.tmp_afiliados_novedades_mostrar) AS total_registros FROM osemm_padron.tmp_afiliados_novedades_mostrar LIMIT ', p_offset, ', ', p_limit);
	PREPARE stmt_pag FROM @sql_pag;
	EXECUTE stmt_pag;
	DEALLOCATE PREPARE stmt_pag;
END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`NOV_mostrar_lote_incluir_errores`$$
CREATE PROCEDURE `NOV_mostrar_lote_incluir_errores`(p_id_lote integer)
BEGIN
			SELECT descripcion into @periodo_presentacion FROM osemm_historicos.`lotes` WHERE id=p_id_lote;
			SELECT MAX(l.id) into @id_errores FROM osemm_historicos.lotes l WHERE proceso='novedades_errores' AND descripcion=@periodo_presentacion ORDER BY id DESC;
			
			DROP TEMPORARY TABLE IF EXISTS osemm_padron.tmp_novedades_ultimos_errores;
			CREATE TEMPORARY TABLE osemm_padron.tmp_novedades_ultimos_errores
			SELECT p.id AS id_persona,p.`cuil`,TRIM(CONCAT(cod_error,cod_error2,cod_error3)) AS errores FROM osemm_historicos.`novedades_sss_errores` nse 
			JOIN osemm_padron.`persona` p ON p.nd=nse.`nd`
			WHERE nse.`id_lote`=@id_errores;
			ALTER TABLE osemm_padron.tmp_novedades_ultimos_errores ADD INDEX id_persona (id_persona);
			UPDATE osemm_padron.tmp_afiliados_novedades_mostrar t
			JOIN osemm_padron.tmp_novedades_ultimos_errores t2 ON t.id_persona=t2.id_persona
			SET t.errores=t2.errores;
    END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`novedades_envio_presentaciones`$$
CREATE PROCEDURE `novedades_envio_presentaciones`()
BEGIN
	--
	DROP TABLE IF EXISTS lst_novedades_presentaciones ;
	--
	CREATE TABLE lst_novedades_presentaciones
	SELECT l.id,descripcion,
		CONCAT(MID(descripcion,1,4),MID(descripcion,6,2)) AS descripcion2,
		l.estado,l.archivo,
		COUNT(*) AS q,
		l.fechador,
		0 AS errores,
		0 AS errores_q,
		0 AS aceptados,
		0 AS aceptados_q,
		0 AS rechazados,
		0 AS rechazados_q,
		obrasocial as fecha_vencimiento,		
		DATE_FORMAT(l.fechador,'%d/%m/%Y %H:%i') AS fecha_mostrar,
		u.usuario 
	FROM osemm_historicos.lotes l
	JOIN osemm_usuarios.users u ON l.id_usuario=u.id 
	LEFT JOIN osemm_historicos.novedades_exportables ds ON l.id=ds.id_lote 
	WHERE proceso='novedades_exportables'
		AND l.estado IS NOT NULL 
	GROUP BY l.id 
	ORDER BY descripcion DESC ;
	--
	-- Errores 
	DROP TEMPORARY TABLE IF EXISTS tmp_lotes  ;
	CREATE TEMPORARY TABLE tmp_lotes 
	SELECT l.descripcion,MAX(l.id) AS mxid_lote
	FROM osemm_historicos.lotes l 
	JOIN osemm_historicos.`novedades_sss_errores` na ON l.id=na.id_lote
	WHERE proceso='novedades_errores'
	GROUP BY 1  ;
	--
	DROP TEMPORARY TABLE IF EXISTS tmp_lotes_err ;
	CREATE TEMPORARY TABLE tmp_lotes_err
	SELECT l.id,l.descripcion,COUNT(*) AS q
	FROM tmp_lotes t
	JOIN osemm_historicos.lotes l ON t.mxid_lote=l.id 
	JOIN osemm_historicos.novedades_sss_errores e ON l.id=e.id_lote
	GROUP BY 1  ;
	--
	UPDATE osemm_padron.lst_novedades_presentaciones n
	JOIN tmp_lotes_err t ON n.descripcion=t.descripcion 
	SET n.errores=t.id,n.errores_q=t.q ;
	--
	-- Aceptados
	DROP TEMPORARY TABLE IF EXISTS tmp_lotes  ;
	CREATE TEMPORARY TABLE tmp_lotes 
	SELECT l.id,l.descripcion,COUNT(*) AS q
	FROM osemm_historicos.lotes l 
	JOIN osemm_historicos.`novedades_sss_aceptados` na ON l.id=na.id_lote
	WHERE proceso='novedades_aceptados'
	GROUP BY 1  ;
	--
	UPDATE lst_novedades_presentaciones n
	JOIN tmp_lotes t ON n.descripcion=t.descripcion 
	SET n.aceptados=t.id,n.aceptados_q=t.q ;
	-- 
	-- Rechazados	
	DROP TEMPORARY TABLE IF EXISTS tmp_lotes  ;
	CREATE TEMPORARY TABLE tmp_lotes 
	SELECT l.id,l.descripcion,COUNT(*) AS q
	FROM osemm_historicos.lotes l 
	JOIN osemm_historicos.`novedades_sss_rechazados` na ON l.id=na.id_lote
	WHERE proceso='novedades_rechazados'
	GROUP BY 1  ;
	--
	UPDATE lst_novedades_presentaciones n
	JOIN tmp_lotes t ON n.descripcion=t.descripcion 
	SET n.rechazados=t.id,n.rechazados_q=t.q ;
	--
    END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`novedades_cronologia`$$
CREATE PROCEDURE `novedades_cronologia`(p_id_persona int)
BEGIN
			select cuil into @p_cuil from osemm_padron.`persona` where id=p_id_persona;
			
			drop TEMPORARY TABLE if exists tmp_cronologia_novedades ;
			create temporary table tmp_cronologia_novedades 
			
			SELECT fechador,id_usuario,'Carga en Personas: ' AS evento,'INICIAL' AS tipo_mov
			FROM osemm_padron.`persona` p
			WHERE p.`cuil`=@p_cuil
			UNION  
			SELECT fechador_carga,'',CONCAT('Carga en presentacion: ',l.descripcion ),tipo_mov
			#select *
			FROM osemm_historicos.`novedades_exportables` ne 
			JOIN osemm_historicos.`lotes` l ON l.id=ne.id_lote
			WHERE ne.`id_persona`=p_id_persona
			UNION
			SELECT np.fechador,'',CONCAT('Exportado: ',l.descripcion ),ne.tipo_mov
			FROM osemm_historicos.`novedades_exportables` ne 
			JOIN osemm_historicos.`lotes` l ON l.id=ne.id_lote
			JOIN osemm_historicos.`novedades_presentaciones` np ON np.id_lote=l.id
			WHERE ne.`id_persona`=p_id_persona
			UNION
			SELECT l.`fechador`,'',CONCAT('Novedades aceptadas: ',l.`descripcion`),nsa.cod_mov
			#select *
			FROM osemm_historicos.`novedades_sss_aceptados` nsa 
			JOIN osemm_historicos.`lotes` l ON l.id=nsa.`id_lote`
			WHERE nsa.`cuil`=@p_cuil
			UNION
			SELECT l.`fechador`,'',CONCAT('Novedades Rechazadas: ',l.`descripcion`,' | ',TRIM(cod_error),'-',TRIM(cod_error2)),nsa.cod_mov
			FROM osemm_historicos.`novedades_sss_rechazados` nsa 
			JOIN osemm_historicos.`lotes` l ON l.id=nsa.`id_lote`
			WHERE nsa.`cuil`=@p_cuil
			UNION
			SELECT l.fechador,'',CONCAT('Novedades Error: ',l.`descripcion`,' | ',TRIM(cod_error),'-',TRIM(cod_error2)),coalesce(nse.cod_mov,'')
			FROM osemm_historicos.`novedades_sss_errores` nse
			JOIN osemm_historicos.`lotes` l ON l.id=nse.`id_lote`
			WHERE nse.`cuil`=@p_cuil
			ORDER BY 1;
    END$$

DROP PROCEDURE IF EXISTS `osemm_padron`.`NOV_agrega_rechazos_periodo_actual`$$
CREATE PROCEDURE `NOV_agrega_rechazos_periodo_actual`()
BEGIN
	--
	SELECT MAX(l.descripcion) INTO @periodo_actual
	FROM osemm_historicos.`novedades_sss_rechazados` r
	JOIN osemm_historicos.lotes l ON r.id_lote=l.id 
	;
	SELECT id INTO @id_lote_rechazos
	FROM osemm_historicos.lotes
	WHERE descripcion=@periodo_actual
		AND proceso='novedades_rechazados'
	;
	SELECT osemm_historicos.get_ult_lote_rechazados() INTO @id_lote_rechazo ;
	SELECT osemm_historicos.`get_id_presentacion_novedades_activa`() INTO @id_presentacion ;
	SELECT id INTO @id_presentacion_anterior FROM osemm_historicos.lotes 
	WHERE proceso='novedades_exportables' AND estado!='Proceso' ORDER BY id DESC LIMIT 1;
	--
	UPDATE osemm_historicos.`novedades_sss_rechazados` r
	JOIN osemm_historicos.`novedades_exportables` n ON r.`id_persona`=n.`id_persona` AND n.`id_lote`=@id_presentacion
	SET n.`cod_rechazados`=TRIM(CONCAT(cod_error,'-',cod_error2))
	WHERE r.`id_lote`=@id_lote_rechazo;
	--
	INSERT INTO osemm_historicos.`novedades_exportables`(id_lote,id_persona,tipo_mov,fec_mov,id_usuario)
	SELECT @id_presentacion,id_persona,cod_mov,osemm_historicos.`get_fecha_presentacion_novedades_activa`(),1
	FROM osemm_historicos.novedades_sss_rechazados n
	WHERE n.id_lote=@id_lote_rechazo
		AND id_persona NOT IN ( SELECT id_persona FROM osemm_historicos.`novedades_exportables` WHERE id_lote=@id_presentacion)
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='92-100'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='90-300'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='180-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='181-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='190-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='191-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='192-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='193-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='194-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='195-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='196-'
		AND TRIM(CONCAT(cod_error,'-',cod_error2))!='197-'	
		
	;
	SELECT id INTO @id_lote_actual
	FROM osemm_historicos.`lotes`
	WHERE proceso='novedades_exportables'
		AND estado='Cerrado'
	ORDER BY id DESC LIMIT 1	
	;
	
	UPDATE osemm_historicos.novedades_sss_rechazados n
	JOIN osemm_historicos.`novedades_exportables` ne ON n.`id_persona`=ne.`id_persona`
	SET ne.`cod_rechazados`=TRIM(CONCAT(cod_error,'-',cod_error2))
	WHERE n.id_lote=@id_lote_rechazo
		AND ne.`id_lote`=@id_lote_actual;
	--
	--
	UPDATE osemm_historicos.novedades_sss_rechazados n
	JOIN osemm_historicos.`novedades_exportables` ne ON n.`id_persona`=ne.`id_persona`
	SET ne.`cod_rechazados`=TRIM(CONCAT(cod_error,'-',cod_error2))
	WHERE n.id_lote=@id_lote_rechazo
		AND ne.`id_lote`=@id_presentacion_anterior 
		;
	--
    END$$

DELIMITER ;

