<?php
// Uso: php runner_regenerar_afil_work.php <schema>
$log = '/tmp/afil_work_runner.log';
function logx($m){ @file_put_contents($GLOBALS['log'], "[".date('Y-m-d H:i:s')."] $m\n", FILE_APPEND); }

if ($argc < 2) { logx("Faltan argumentos"); exit(1); }
$schema_arg = $argv[1];                 // <--- usa un nombre distinto
logx("Inicio runner. schema_arg=".$schema_arg);

// Importante: incluye después de guardar schema_arg
require_once __DIR__ . '/../../Config/Conectar.inc';

// (Opcional) lock externo si tu SP no se auto-excluye
$rs = mysql_query("SELECT GET_LOCK('afil_work_worker', 0) AS l");
if ($rs){
  $row = mysql_fetch_assoc($rs);
  if ((int)$row['l'] !== 1){
    logx("No obtuve lock afil_work_worker. Ya hay otro corriendo.");
    exit(0);
  }
}

@mysql_query("SET SESSION max_execution_time = 0");

// Verificar que exista el schema recibido por argv (NO uses variables del include)
$qSchema = sprintf(
  "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '%s'",
  mysql_real_escape_string($schema_arg)
);
$rs = mysql_query($qSchema);
if (!$rs || mysql_num_rows($rs) === 0) {
  logx("Schema no existe (schema_arg): ".$schema_arg);
  @mysql_query("DO RELEASE_LOCK('afil_work_worker')");
  exit(2);
} else {
  logx("Schema OK (schema_arg): ".$schema_arg);
}

// Llamada calificada usando SIEMPRE schema_arg
$sqlCall = "CALL `".$schema_arg."`.crea_afil_work(CURDATE())";
logx("Ejecutando: ".$sqlCall);
$ok = @mysql_query($sqlCall);

if (!$ok) {
  logx("Error CALL: ".mysql_error());
} else {
  logx("CALL OK");
}

@mysql_query("DO RELEASE_LOCK('afil_work_worker')");
