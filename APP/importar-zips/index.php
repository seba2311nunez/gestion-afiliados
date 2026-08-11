<?php
include('../../Config/Conectar.inc');
include_once('../../Config/s3_directo.inc.php');

$inst = INST_NAME; // o lo que hoy te define conectar.php
$s3DirectoConfig = s3_directo_private_config();
$secret = $s3DirectoConfig['secret'];

$ts = time();
$data = $inst . '|' . $ts;
$sig  = hash_hmac('sha256', $data, $secret);

// URL encode por seguridad
$url = rtrim($s3DirectoConfig['url'], '/') . '/'
     . '?inst=' . urlencode($inst)
     . '&ts='   . urlencode($ts)
     . '&sig='  . urlencode($sig);

header('Location: ' . $url, true, 302);
exit;
