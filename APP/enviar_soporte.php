<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que venga por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = "error|Acceso inválido al formulario.";
    header("Location: soporte.php");
    exit;
}

// Sanitizar entradas
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sector  = trim($_POST['sector'] ?? '');
$asunto  = trim($_POST['asunto'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// Datos de sesión
$obra_social = $_SESSION['obra_social'] ?? 'O.S.E.A.M';
$usuario     = $_SESSION['usuario'] ?? 'Admin';

// Validaciones
if (!$email || empty($sector) || empty($asunto) || empty($mensaje)) {
    $_SESSION['mensaje'] = "error|Todos los campos son obligatorios y el email debe ser válido.";
    header("Location: soporte.php");
    exit;
}

// Destinatario (CAMBIA esto por tu correo real)
$destinatario = "consulta@smadm.com";

// Asunto del correo
$titulo = "[Soporte - $sector] $asunto";

// Cuerpo del correo
$cuerpo = "
Se recibió una nueva consulta de soporte:

Obra Social: $obra_social
Usuario: $usuario
Email: $email
Sector: $sector
Asunto: $asunto

Mensaje:
$mensaje
";

// Cabeceras
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Enviar correo
echo "El script se ejecutó hasta el final.<br>";
exit;



// Redirigir de vuelta
header("Location: soporte.php");
exit;
