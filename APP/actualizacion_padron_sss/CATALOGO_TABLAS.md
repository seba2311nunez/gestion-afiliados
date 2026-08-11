# Catalogo de datos - Actualizacion del padron SSS

Relevamiento estatico de `APP/actualizacion_padron_sss`. Los nombres de base reales provienen de `Config/Conectar.inc`:

- `N_BASE_PADRON` / `$base_padron`: base operativa del padron.
- `N_BASE_HISTORICOS` / `$base_historicos`: lotes, presentaciones y resultados historicos.
- `$base_usuarios`: usuarios del sistema.

## Tablas centrales del circuito actual

| Base | Tabla | Rol | Operaciones del modulo | Scripts principales |
|---|---|---|---|---|
| Historicos | `lotes` | Cabecera de procesos y presentaciones mensuales. `proceso='novedades_exportables'` identifica los periodos SSS. | SELECT, INSERT, UPDATE | `ajax_dev.php`, `lib/sss_workflow.php`, procesos de nuevo periodo |
| Historicos | `novedades_exportables` | Detalle de afiliados y movimientos A/B/M que integran cada lote. Conserva errores inmediatos y rechazos. | SELECT, INSERT, UPDATE, DELETE | `ajax_dev.php`, `lib/sss_workflow.php` |
| Historicos | `sss_presentacion_control` | Estado tecnico del envio: generado, enviado, errores importados, espera e importacion de resultados. | CREATE, SELECT, INSERT/UPSERT, UPDATE | `lib/sss_workflow.php`, `ajax_dev.php` |
| Historicos | `sss_cronograma_ftp` | Fechas oficiales de apertura, cierre, respuesta y devolucion por periodo. | CREATE, SELECT, INSERT/UPSERT | `lib/sss_workflow.php` |
| Historicos | `sss_catalogo_errores` | Codigo SSS, descripcion y accion sugerida tomada del instructivo. | CREATE, SELECT, INSERT/UPSERT | `lib/sss_workflow.php` |
| Historicos | `sss_afiliado_cronologia` | Historial por persona de validaciones, aceptaciones, rechazos y propagaciones. | CREATE, SELECT, INSERT | `lib/sss_workflow.php`, `ajax_dev.php` |
| Historicos | `novedades_sss_errores` | Registros del archivo `.err` o errores inmediatos devueltos por FTP. | SELECT, INSERT, UPDATE | `ajax_dev.php` |
| Historicos | `novedades_sss_aceptados` | Resultado definitivo ACEPTADOS.TXT por periodo. | SELECT, INSERT, UPDATE | `ajax_dev.php` |
| Historicos | `novedades_sss_rechazados` | Resultado definitivo RECHAZOS.TXT y fuente de novedades a corregir/reintentar. | SELECT, INSERT, UPDATE | `ajax_dev.php`, `supervisar_rnov.php` |

## Maestro del afiliado y tablas de referencia

| Base | Tabla | Rol | Uso |
|---|---|---|---|
| Padron | `persona` | Identidad: CUIL, documento, nombre, sexo y nacimiento. | Armar archivos, listados, busquedas y asociaciones con resultados SSS. |
| Padron | `afiliados` | Relacion de afiliacion, titular, parentesco, gerenciadora y tipo de aporte. | Determinar grupo familiar y clasificar titular/familiar. |
| Padron | `desreguladoras` | Convenio/gerenciadora y convenio real. | Agrupacion y separacion institucional de los envios. |
| Padron | `parentesco` | Descripcion del parentesco. | Visualizacion y validacion del grupo familiar. |
| Padron | `tipo_beneficiario_titular` | Codigo/sigla del tipo de beneficiario. | Archivo SSS, resumen TBT y filtros. |
| Usuarios | `users` | Identidad del operador. | Mostrar usuario asociado a lotes y procesos de supervision. |

## Vistas y tablas temporales heredadas

| Base | Objeto | Rol actual | Observacion |
|---|---|---|---|
| Padron | `lst_novedades_presentaciones` | Vista que resume lotes, movimientos, errores, aceptados y rechazados. | Alimenta la lista principal. Conviene documentar su `SHOW CREATE VIEW` y luego reemplazarla por una consulta versionada. |
| Padron | `tmp_novedades` | Resultado temporal usado al generar novedades. | Es cargada por procedimientos almacenados. Si es una tabla fisica compartida, existe riesgo de concurrencia. |
| Padron | `tmp_afiliados_novedades_mostrar` | Listado temporal heredado de `NOV_mostrar_lote`. | El listado nuevo ya no depende de ella; permanecen exportaciones antiguas que deben migrarse. |
| Padron | `tmp_cronologia_novedades` | Cronologia historica temporal generada por `novedades_cronologia`. | Se combina con `sss_afiliado_cronologia`. Riesgo de mezcla entre sesiones si es tabla fisica. |
| Padron | `tmp_afiliados_nov_padronsss_insertar` | Preparacion/supervision de comparaciones contra el padron SSS. | Pertenece al circuito heredado de supervision. |
| Padron | `log_eventos` | Registro de ejecuciones como `ctrlPadronCompleto`. | Solo lo usa el tablero heredado de supervision. |

## Circuitos auxiliares o heredados

| Base | Tabla | Rol | Scripts |
|---|---|---|---|
| Historicos | `novedades_exportables_comparacion` | Diferencias entre padron local y padron SSS para revision previa. | `ajax_dev.php`, `supervisar_compsss.php` |
| Auxiliar | `prueba.periodos` | Opciones del formulario manual de nuevo periodo. | `index_dev.php`; queda sin uso visible al retirar el alta manual. Debe eliminarse junto con el modal heredado. |

## Procedimientos almacenados involucrados

Los procedimientos no son tablas, pero pueden leer o modificar objetos adicionales que no aparecen en los PHP:

- `novedades_crea_nuevo_periodo`
- `NOV_nuevo_periodo_automatico`
- `NOV_mostrar_lote`
- `NOV_mostrar_lote_incluir_errores`
- `novedades_cronologia`
- `NOV_agrega_rechazos_periodo_actual`
- Procedimientos de generacion/exportacion que cargan `tmp_novedades`

Para completar el inventario de dependencias indirectas se debe relevar en cada instalacion `SHOW CREATE PROCEDURE` y `SHOW CREATE VIEW`, porque su definicion vive en MySQL y puede diferir entre obras sociales.

## Diagnostico de "Supervisar lote"

La opcion no supervisaba el lote seleccionado: el enlace abria `supervisar_novedades.php` sin enviar `id_lote`. Esa pantalla es un tablero general heredado que lista:

- comparaciones entre padron local y SSS;
- bajas calculadas por falta de DDJJ/aportes;
- altas calculadas por ingreso de DDJJ/aportes;
- rechazos pendientes de reingreso;
- ejecuciones del controlador de padron completo.

Por no estar vinculada al lote de la fila, se retiro del menu contextual. Si ese tablero sigue siendo necesario, deberia publicarse como una herramienta administrativa independiente y renombrarse `Supervision general de novedades`.

## Recomendaciones de saneamiento

1. Mantener como nucleo `lotes`, `novedades_exportables` y las cuatro tablas `sss_*` de control.
2. Migrar las exportaciones que todavia llaman `NOV_mostrar_lote` para eliminar `tmp_afiliados_novedades_mostrar`.
3. Sustituir tablas temporales fisicas por consultas directas o temporales de sesion.
4. Versionar la definicion de `lst_novedades_presentaciones` y de los procedimientos almacenados.
5. Retirar modal, JavaScript y endpoints de alta manual de periodo, edicion manual de vencimiento y gestion manual de errores cuando termine la etapa de contingencia.
