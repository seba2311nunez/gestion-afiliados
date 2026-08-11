<?php
require '../../../Config/composer_autoload.inc.php';
include('../../../Config/Conectar.inc');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Create an S3Client instance
include_once('../../../Config/aws_s3.inc.php');
$s3 = new S3Client([
    'version' => 'latest',
    'region' => AWS_S3_REGION,
    'credentials' => [
        'key' => AWS_S3_KEY,
        'secret' => AWS_S3_SECRET
    ],
]);

$bucketName = INST_NAME;  // Replace with your 
$prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
try {

    $result = $s3->listObjectsV2([
      'Bucket'    => $bucketName,
      'Prefix'    => $prefix,
      'Delimiter' => '/',
	  ]);


    $folders = [];
    $files = [];

    // Check if there are any common prefixes (folders)
    if (isset($result['CommonPrefixes'])) {
        foreach ($result['CommonPrefixes'] as $prefix) {
            $folders[] = ['name' => $prefix['Prefix']];
        }
    }

    // List the files (objects) in the specified path
    if (isset($result['Contents'])) {
        foreach ($result['Contents'] as $content) {
            $fileName = $content['Key'];
            $fileSizeInBytes = $content['Size'];
            $dt = clone $content['LastModified']; 
            $dt->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
            $lastModified = $dt->format('Y-m-d H:i:s');

            if($fileSizeInBytes > 0){
                $metadataResult = $s3->headObject([
                    'Bucket' => $bucketName,
                    'Key'    => $fileName,
                ]);
                $owner = isset($metadataResult['Metadata']['owner']) 
                         ? $metadataResult['Metadata']['owner'] 
                         : '';

                // --- Obtener observación desde logs_documentacion ---
                $observacion = ''; // por defecto vacío
                $ubicacion_db = mysql_real_escape_string($prefix);

                // ---- Obtener solo el nombre del archivo ----
                $nombre_archivo_db = basename($fileName);
                $nombre_archivo_db = mysql_real_escape_string($nombre_archivo_db);

                // Tomamos la última observación registrada para ese archivo (si hay)
                $sql_obs = "
                    SELECT observacion 
                    FROM $base.logs_documentacion 
                    WHERE ubicacion = '$ubicacion_db'
                      AND nombre_archivo = '$nombre_archivo_db'
                    ORDER BY id DESC
                    LIMIT 1
                ";
                $rs_obs = mysql_query($sql_obs);
                if ($rs_obs && mysql_num_rows($rs_obs) > 0) {
                    $row_obs    = mysql_fetch_assoc($rs_obs);
                    $observacion = $row_obs['observacion'];
                }

	            $cmd = $s3->getCommand('GetObject', [
	                'Bucket' => $bucketName,
	                'Key'    => $fileName
	            ]);

	            $fileSizeInKB = $fileSizeInBytes / 1024;
	            $fileSizeInKB = round($fileSizeInKB, 2);

	            $request = $s3->createPresignedRequest($cmd, '+60 minutes');
	            $presignedUrl = (string) $request->getUri();

	            $files[] = [
	                'name' => $fileName,
	                'size' => $fileSizeInKB . ' KB',
	                'last_modified' => $lastModified,
	                'url' => $presignedUrl,
                    'owner' => $owner,
                    'observacion'  => $observacion // <- NUEVO
	            ];
            }
        }
    }

    echo json_encode([
        'folders' => $folders,
        'files' => $files
    ]);

} catch (AwsException $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
