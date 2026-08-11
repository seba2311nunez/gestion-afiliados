# Configuraciones privadas

Estas plantillas no contienen secretos. Los JSON reales nunca deben copiarse al repositorio ni dentro de `htdocs`.

| Servicio | Windows | Linux | Variable opcional |
|---|---|---|---|
| Base principal | `C:\xampp\config\database.json` | `/etc/sistema.obra.social/database.json` | `DATABASE_CONFIG_FILE` |
| S3 | `C:\xampp\config\aws_s3.json` | `/etc/sistema.obra.social/aws_s3.json` | `AWS_S3_CONFIG_FILE` |
| OpenAI | `C:\xampp\config\openai.json` | `/etc/sistema.obra.social/openai.json` | `OPENAI_CONFIG_FILE` |
| RDS | `C:\xampp\config\rds.json` | `/etc/sistema.obra.social/rds.json` | `RDS_CONFIG_FILE` |
| FTP SSS | `C:\xampp\config\ftp_sss.json` | `/etc/sistema.obra.social/ftp_sss.json` | `SSS_FTP_CONFIG_FILE` |
| Revisión de opciones | `C:\xampp\config\revision_opciones.json` | `/etc/sistema.obra.social/revision_opciones.json` | `SSS_REVISION_OPCIONES_CONFIG_FILE` |

En Linux deben pertenecer al usuario del servidor web y tener permisos `600` o equivalentes. Para RDS se recomienda verificar el certificado con el bundle CA oficial de AWS.

## Carga automática

Apache con PHP como módulo:

```apache
php_value auto_prepend_file "C:/xampp/htdocs/sistema.obra.social/padron/Config/bootstrap_privado.inc.php"
```

Linux con PHP-FPM, en el pool o en `.user.ini`:

```ini
auto_prepend_file=/var/www/sistema.obra.social/padron/Config/bootstrap_privado.inc.php
```

El bootstrap no contiene ni abre credenciales. Sólo incorpora el resolvedor común; cada servicio se lee cuando el código lo solicita.

La ruta de un archivo de configuración no es un secreto. La protección real consiste en que el JSON esté fuera del directorio público, tenga permisos restringidos y jamás se versione.
