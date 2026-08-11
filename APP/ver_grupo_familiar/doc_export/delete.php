<?php
require '../../../Config/composer_autoload.inc.php';
include('../../../Config/Conectar.inc');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

header('Content-Type: application/json');


if (!isset($_POST['filepath'])) {
    echo json_encode(['success' => false, 'message' => 'Falta la ruta del archivo.']);
    exit;
}

$sql  = "INSERT INTO $base_padron.log_eventos (evento,ip,id_usuario,otros_parametros) 
VALUES ('Borrar Documentacion','".$_SERVER['HTTP_CLIENT_IP']."',".$_SESSION['id_user'].",'".$_POST['filepath']."')";
if(!mysql_query($sql)){
    echo json_encode(['success' => false, 'message' => mysql_error()." ".$sql]);
    exit;
}
$bucket = INST_NAME;
$key = urldecode($_POST['filepath']);

include_once('../../../Config/aws_s3.inc.php');
$s3 = new S3Client([
    'version' => 'latest',
    'region' => AWS_S3_REGION,
    'credentials' => [
        'key' => AWS_S3_KEY,
        'secret' => AWS_S3_SECRET
    ],
]);

try {
    $result = $s3->deleteObject([
        'Bucket' => $bucket,
        'Key'    => $key,
    ]);

    echo json_encode(['success' => true]);
} catch (AwsException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getAwsErrorMessage()
    ]);
}
