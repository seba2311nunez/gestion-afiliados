# Integración IA para Gestión de Afiliaciones

## Alcance inicial

La primera etapa agrega la infraestructura, el prompt versionado y un cliente interno para Responses API. No expone todavía un chat público ni permite que el modelo ejecute SQL o modifique afiliados.

El asistente debe recibir contexto preparado por PHP, limitado al lote o afiliado autorizado. La API de OpenAI nunca debe recibir una copia completa del padrón ni credenciales.

## Componentes

- `Config/openai.inc.php`: descubre el JSON privado según Windows/Linux e institución activa.
- `scripts/openai.example.json`: plantilla sin secretos.
- `lib/openai_sss.php`: cliente interno de Responses API con verificación TLS activa.
- `prompts/asistente_actualizacion_padron_sss.md`: instrucciones del dominio.
- `database/002_tablas_y_ejemplos.sql`: estructuras y muestras anonimizadas para comprender el modelo.
- `database/003_storeds_y_vistas.sql`: lógica almacenada y vistas disponibles en OSEMM.

## Configuración privada

Rutas predeterminadas:

- Windows: `C:\xampp\config\openai.json`
- Linux: `/etc/sistema.obra.social/openai.json`
- Alternativa Linux: `/var/www/config/openai.json`
- Sobrescritura: variable de entorno `OPENAI_CONFIG_FILE`

El archivo debe quedar fuera de `htdocs`, ser legible únicamente por el usuario del servidor web y usar la estructura de `scripts/openai.example.json`.

## Seguridad y privacidad

1. Rotar la clave expuesta en el prototipo antes de configurar esta integración.
2. No desactivar `CURLOPT_SSL_VERIFYPEER` ni `CURLOPT_SSL_VERIFYHOST`.
3. Preparar contexto del lado servidor; el navegador no recibe la API key.
4. Enmascarar CUIL, DNI y datos de contacto cuando no sean indispensables.
5. Registrar usuario, finalidad, lote consultado, modelo y consumo, pero no el prompt completo con datos personales.
6. Aplicar autorización por obra social y perfil antes de armar el contexto.
7. El modelo propone y explica; una persona confirma cualquier cambio de datos.

## Próxima etapa sugerida

Crear un endpoint autenticado `asistente_sss.php` con tres herramientas de solo lectura:

- resumen de un lote;
- explicación de errores/rechazos de un afiliado;
- cronología de un afiliado.

Cada herramienta debe usar consultas parametrizadas, limitar filas y devolver únicamente campos necesarios. Después de evaluar exactitud y privacidad se puede agregar una interfaz dentro del modal de afiliados.
