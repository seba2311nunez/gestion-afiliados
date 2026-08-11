<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'rds.inc.php';

try {
    $source = rds_private_config('produccion');
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage()."\n");
    exit(2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = mysqli_init();
mysqli_options($db, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

$connected = @mysqli_real_connect(
    $db,
    (string) (isset($source['host']) ? $source['host'] : ''),
    (string) (isset($source['usuario']) ? $source['usuario'] : ''),
    (string) (isset($source['clave']) ? $source['clave'] : ''),
    null,
    (int) (isset($source['port']) ? $source['port'] : 3306)
);

if (!$connected) {
    fwrite(STDERR, sprintf(
        "Conexion rechazada (%d): %s\n",
        mysqli_connect_errno(),
        mysqli_connect_error()
    ));
    exit(1);
}

$result = mysqli_query($db, "
    SELECT VERSION() AS version,
           @@hostname AS servidor,
           COALESCE((SELECT VARIABLE_VALUE
                     FROM performance_schema.session_status
                     WHERE VARIABLE_NAME = 'Ssl_cipher'), '') AS ssl_cipher
");
$status = $result ? mysqli_fetch_assoc($result) : [];

$schemas = [];
$result = mysqli_query($db, "
    SELECT SCHEMA_NAME
    FROM information_schema.SCHEMATA
    WHERE SCHEMA_NAME IN ('ospedyb_padron', 'ospedyb_historicos', 'ospedyb_usuarios')
    ORDER BY SCHEMA_NAME
");
while ($result && ($row = mysqli_fetch_assoc($result))) {
    $schemas[] = $row['SCHEMA_NAME'];
}

echo 'Conexion: OK' . PHP_EOL;
echo 'Version: ' . (isset($status['version']) ? $status['version'] : 'desconocida') . PHP_EOL;
echo 'Servidor: ' . (isset($status['servidor']) ? $status['servidor'] : 'desconocido') . PHP_EOL;
$sslCipher = isset($status['ssl_cipher']) ? $status['ssl_cipher'] : '';
echo 'SSL: ' . ($sslCipher !== '' ? $sslCipher : 'NO DETECTADO') . PHP_EOL;
echo 'Esquemas: ' . implode(', ', $schemas) . PHP_EOL;

mysqli_close($db);
