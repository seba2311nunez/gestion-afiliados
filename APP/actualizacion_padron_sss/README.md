# Actualizacion permanente del padron SSS

Modulo de trabajo independiente para generar, enviar e importar las novedades mensuales de la SSS.

Entrada principal: `index_dev.php`.

## Circuito

1. `PREPARADO`: el lote se puede revisar y corregir.
2. `GENERADO`: se genero un archivo de exactamente 27 campos por registro. Se guarda cantidad y SHA-256.
3. `ENVIADO`: el archivo fue transferido por FTPS.
4. `VALIDADO_SIN_ERRORES` o `ERROR_INMEDIATO_DISPONIBLE`: respuesta `.ok/.err` de la validacion inicial.
5. `ERRORES_INMEDIATOS_IMPORTADOS`: los codigos se marcaron en `novedades_exportables` y se registraron en la cronologia.
6. `ESPERANDO_RESULTADOS`: el periodo esta cerrado y se espera `Devolucion.zip`.
7. `RESULTADOS_IMPORTADOS`: se importaron ACEPTADOS y RECHAZOS sobre el lote cerrado y los rechazos se propagaron al lote siguiente.

Las fechas de cierre y respuesta se obtienen de `sss_cronograma_ftp`, cargada desde el cronograma oficial. Solo cuando un periodo no esta catalogado se usa como contingencia el calculo de dos dias habiles posteriores al cierre. La disponibilidad real siempre se confirma en el FTP.

## Tablas propias

- `sss_catalogo_errores`: descripcion y accion por codigo, versionadas contra el instructivo.
- `sss_presentacion_control`: estado, fechas, archivo, cantidad y hash por lote.
- `sss_afiliado_cronologia`: eventos de validacion, aceptacion, rechazo y propagacion por persona.
- `sss_cronograma_ftp`: apertura, cierre, respuesta y devolucion oficial por periodo.

Las tablas se crean en la base historica de cada institucion de forma idempotente. El SQL equivalente esta en `database/001_circuito_sss.sql`.

## Reglas de importacion

- La asociacion con personas usa CUIL y luego `id_persona`; no usa DNI como clave primaria.
- La carga manual de aceptados/rechazados queda disponible solo como contingencia y usa la misma funcion que el proceso automatico.
- Un periodo con estado `RESULTADOS_IMPORTADOS` no se vuelve a importar.
- Los errores del mes cerrado permanecen en ese lote y se copian al primer lote posterior de la misma persona.

## Dependencias operativas

- PHP con acceso a las bases configuradas por `Config/Conectar.inc`.
- Python 3 para `scripts/ftp_sss.py`.
- Acceso FTPS saliente al servidor de la SSS.

## Credenciales FTPS

Las claves no se guardan dentro de `APP` ni se exponen al navegador. El cargador
`Config/ftp_sss.inc.php` selecciona la entrada por `INST_NAME` y valida que el
RNOS coincida con la sesion activa.

Las rutas predeterminadas, siempre fuera del directorio público, son:

- Windows/XAMPP: `C:\xampp\config\ftp_sss.json`
- Linux: `/etc/sistema.obra.social/ftp_sss.json`
- Alternativa Linux: `/var/www/config/ftp_sss.json`
- Alternativa portable: carpeta `config` hermana del `DocumentRoot`.

La ruta puede cambiarse mediante la variable de entorno
`SSS_FTP_CONFIG_FILE`. El formato está documentado en
`scripts/ftp_sss.example.json`. El usuario del servicio Apache debe tener
permiso de lectura, pero el archivo no debe ser accesible por HTTP.

En producción Linux se recomienda `/etc/sistema.obra.social/ftp_sss.json`,
propietario `root`, grupo del servicio web y permisos `640`. El archivo real
no forma parte de Git y debe instalarse como secreto durante el despliegue.

## AWS S3 y OpenAI

Los cargadores versionables `Config/aws_s3.inc.php` y `Config/openai.inc.php`
no contienen credenciales. Detectan Windows/Linux, seleccionan la institucion
activa y rechazan configuraciones ubicadas dentro del directorio web.

- AWS: `C:\xampp\config\aws_s3.json` o `/etc/sistema.obra.social/aws_s3.json`.
- OpenAI: `C:\xampp\config\openai.json` o `/etc/sistema.obra.social/openai.json`.
- Variables opcionales: `AWS_S3_CONFIG_FILE` y `OPENAI_CONFIG_FILE`.
- Plantillas: `scripts/aws_s3.example.json` y `scripts/openai.example.json`.

La arquitectura de la integracion esta documentada en
`PROMPT_Y_ARQUITECTURA_IA.md`. El prompt versionado vive en `prompts/`.

## Catalogos SQL

- `database/002_tablas_y_ejemplos.sql`: `CREATE TABLE` y hasta tres muestras
  anonimizadas por tabla.
- `database/003_storeds_y_vistas.sql`: stored procedures y vistas disponibles
  en la instalacion OSEMM, sin `DEFINER` dependiente del servidor.
- `scripts/generar_catalogos_sql.php`: regenerador autenticado/CLI. Ejemplo:
  `php generar_catalogos_sql.php --inst=osemm`.

Las dependencias ausentes se registran en `CATALOGO_TABLAS.md`; no se crean
definiciones ficticias.
