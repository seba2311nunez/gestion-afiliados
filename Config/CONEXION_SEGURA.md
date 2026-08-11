# Conexion central segura

Los scripts mantienen sus includes actuales de `Config/Conectar.inc`. Ese
archivo versionado no contiene secretos: carga `database.json`, abre la
conexion central y conserva las variables y constantes esperadas por el codigo
legado.

Ubicaciones predeterminadas:

- Windows/XAMPP: `C:\xampp\config\database.json`
- Linux: `/etc/sistema.obra.social/database.json`
- Hostinger: puede usarse `~/config/database.json`

La variable de entorno `DATABASE_CONFIG_FILE` permite indicar otra ruta.
Cambiar de RDS o servidor MySQL requiere modificar solamente ese JSON externo.

El archivo debe copiar la estructura de
`APP/actualizacion_padron_sss/scripts/configuracion_privada/database.json.example`,
permanecer fuera del directorio publico y tener permisos restringidos al
usuario que ejecuta PHP.
