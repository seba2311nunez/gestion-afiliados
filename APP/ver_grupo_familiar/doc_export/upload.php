<?php
require '../../../Config/composer_autoload.inc.php';
include('../../../Config/Conectar.inc');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

include_once('../../../Config/aws_s3.inc.php');
$s3 = new S3Client([
    'version' => 'latest',
    'region' => AWS_S3_REGION,
    'credentials' => [
        'key' => AWS_S3_KEY,
        'secret' => AWS_S3_SECRET
    ],
]);

$prefix = $_POST['prefix'];
$file = $_FILES['file'];

// File path in S3
$bucket = INST_NAME;
$key = $prefix . basename($file['name']);
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'unknown';

try {

	$observacion = mysql_real_escape_string($observacion);
    $sql="INSERT INTO $base.logs_documentacion (tipo,ubicacion,nombre_archivo,accion,observacion,id_afiliado,id_usuario) VALUES ('legajos','$prefix','".$file['name']."','subir','$observacion',$id_afiliado,".$_SESSION['id_user'].")";
    $query = mysql_query($sql);

    if(!$query){
        echo json_encode(['status' => 'error', 'message' => mysql_error(), 'message2' => $sql]);
        exit();
    }
    
	// Upload the file to S3
	$result = $s3->putObject([
		'Bucket' => $bucket,
		'Key'    => $key,
		'SourceFile' => $file['tmp_name'],
		'Metadata' => [
			'owner' => $usuario,
		]
	]);

	echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.']);
} catch (AwsException $e) {
	echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
