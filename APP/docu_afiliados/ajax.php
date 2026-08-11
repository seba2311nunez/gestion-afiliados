<?php
/*
    Prueba aislada de documentacion de afiliados sobre S3 para OSEMM
    (mismo esquema de key que ya usa OSETRA en produccion: sin categoria
    ni año, solo afiliados/documentacion/{id_afiliado}/{archivo}).

    Carpeta de prueba, independiente del menu de ver_grupo_familiar: se
    accede pasando ?id_afiliado=... directo por URL a index.php, para
    validar que los archivos migrados por el script de Python se puedan
    ver/abrir bien antes de integrarlo al resto del sistema.
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

include('../../Config/Conectar.inc');
include('../../Config/aws_s3.inc.php');
require '../../Config/composer_autoload.inc.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$bucket = $bucketName = INST_NAME;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => AWS_S3_REGION,
    'credentials' => [
        'key'    => AWS_S3_KEY,
        'secret' => AWS_S3_SECRET,
    ],
]);

function s3_content_type_por_extension($fileName){
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    switch ($ext) {
        case 'pdf': return 'application/pdf';
        case 'jpg':
        case 'jpeg': return 'image/jpeg';
        case 'png': return 'image/png';
        case 'txt': return 'text/plain';
        default: return 'application/octet-stream';
    }
}

switch ($parametro) {
    case 'load':
        $prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
        try {
            $result = $s3->listObjectsV2([
                'Bucket'    => $bucketName,
                'Prefix'    => $prefix,
                'Delimiter' => '/',
            ]);

            $folders = [];
            $files = [];

            if (isset($result['CommonPrefixes'])) {
                foreach ($result['CommonPrefixes'] as $p) {
                    $folders[] = ['name' => $p['Prefix']];
                }
            }

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $content) {
                    $fileName = $content['Key'];
                    $fileSizeInBytes = $content['Size'];

                    if ($fileSizeInBytes <= 0) continue;

                    $date = $content['LastModified'];
                    $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
                    $lastModified = $date->format('d/m/Y H:i:s');

                    $metadataResult = $s3->headObject([
                        'Bucket' => $bucketName,
                        'Key'    => $fileName,
                    ]);

                    $currentType = isset($metadataResult['ContentType']) ? $metadataResult['ContentType'] : 'application/octet-stream';

                    if ($currentType === 'application/octet-stream') {
                        $newType = s3_content_type_por_extension($fileName);
                        if ($newType !== 'application/octet-stream') {
                            $s3->copyObject([
                                'Bucket' => $bucketName,
                                'CopySource' => urlencode("$bucketName/$fileName"),
                                'Key' => $fileName,
                                'MetadataDirective' => 'REPLACE',
                                'ContentType' => $newType,
                                'ContentDisposition' => 'inline',
                                'Metadata' => $metadataResult['Metadata']
                            ]);
                            $metadataResult = $s3->headObject([
                                'Bucket' => $bucketName,
                                'Key'    => $fileName,
                            ]);
                        }
                    }

                    $owner = isset($metadataResult['Metadata']['owner']) ? $metadataResult['Metadata']['owner'] : '';

                    // Si el archivo fue migrado desde el blob de MySQL,
                    // trae la fecha real de carga original como metadata
                    // custom (fecha_real) - se prioriza sobre el
                    // LastModified de S3, que para archivos migrados
                    // siempre va a ser la fecha en que se corrio la
                    // migracion, no la fecha real del documento.
                    if (!empty($metadataResult['Metadata']['fecha_real'])) {
                        $lastModified = $metadataResult['Metadata']['fecha_real'];
                    }

                    $cmd = $s3->getCommand('GetObject', [
                        'Bucket' => $bucketName,
                        'Key'    => $fileName,
                        'ResponseContentDisposition' => 'inline'
                    ]);

                    $fileSizeInKB = round($fileSizeInBytes / 1024, 2);
                    $request = $s3->createPresignedRequest($cmd, '+60 minutes');
                    $presignedUrl = (string) $request->getUri();

                    $files[] = [
                        'name' => $fileName,
                        'size' => $fileSizeInKB . ' KB',
                        'last_modified' => $lastModified,
                        'url' => $presignedUrl,
                        'owner' => $owner,
                        'delete_url' => "ajax.php?parametro=delete&file=" . urlencode($fileName)
                    ];
                }
            }

            echo json_encode(['folders' => $folders, 'files' => $files]);
        } catch (AwsException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'upload':
        $id_afiliado = isset($_POST['id_afiliado']) ? trim($_POST['id_afiliado']) : '';

        if ($id_afiliado === '') {
            echo json_encode(['ok' => false, 'msj' => 'Falta el id Afiliado.']);
            exit;
        }
        if (!isset($_FILES['files']) || !is_array($_FILES['files']['name'])) {
            echo json_encode(['ok' => false, 'msj' => 'No se recibieron archivos.']);
            exit;
        }

        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'sistema';
        $total = count($_FILES['files']['name']);
        $subidos = 0;

        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $name = $_FILES['files']['name'][$i];
            $tmpName = $_FILES['files']['tmp_name'][$i];

            $baseName = preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $name);
            $s3Key = "afiliados/documentacion/$id_afiliado/$baseName";

            try {
                $s3->putObject([
                    'Bucket'     => $bucketName,
                    'Key'        => $s3Key,
                    'SourceFile' => $tmpName,
                    'ACL'        => 'private',
                    'Metadata'   => ['owner' => $usuario],
                ]);
            } catch (Exception $e) {
                error_log("docu_afiliados upload error ($s3Key): " . $e->getMessage());
                continue;
            }

            if (file_exists($tmpName)) @unlink($tmpName);
            $subidos++;
        }

        echo json_encode(['ok' => true, 'subidos' => $subidos]);
        exit;

    case 'delete':
        if (isset($_GET['file'])) {
            $fileKey = urldecode($_GET['file']);
            try {
                $s3->deleteObject(['Bucket' => $bucket, 'Key' => $fileKey]);
                echo json_encode(['status' => 'success', 'message' => 'Archivo eliminado']);
            } catch (AwsException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Archivo no especificado']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Parametro invalido.']);
        break;
}
