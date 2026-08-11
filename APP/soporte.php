<?php
// Configuración para mostrar TODOS los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Iniciar sesión de forma segura
if (!isset($_SESSION)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Verificar si hay errores antes de continuar
$errores = error_get_last();
if ($errores) {
    echo "Error inicial: " . $errores['message'];
    exit;
}

// Manejar mensajes de sesión
$mostrar_mensaje = false;
$clase_alerta = '';
$icono = '';
$titulo = '';
$texto_mensaje = '';

if (isset($_SESSION['mensaje']) && !empty($_SESSION['mensaje'])) {
    $mensaje_data = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
    
    $partes = explode("|", $mensaje_data, 2);
    if (count($partes) === 2) {
        $tipo = $partes[0];
        $texto_mensaje = $partes[1];
        $mostrar_mensaje = true;
        
        if ($tipo == "success") {
            $clase_alerta = "alert-success";
            $icono = "fa-check-circle";
            $titulo = "¡Éxito!";
        } else {
            $clase_alerta = "alert-danger";
            $icono = "fa-exclamation-circle";
            $titulo = "Error";
        }
    }
}

// Valores por defecto con verificación
$obra_social = 'O.S.E.A.M';
$usuario = 'Admin';

if (isset($_SESSION['obra_social']) && !empty($_SESSION['obra_social'])) {
    $obra_social = htmlspecialchars($_SESSION['obra_social']);
}

if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
    $usuario = htmlspecialchars($_SESSION['usuario']);
}

// Verificar nuevamente si hay errores antes del HTML
$errores = error_get_last();
if ($errores) {
    echo "Error después de procesar variables: " . $errores['message'];
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soporte - Sistema de Afiliaciones</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f8f9fa;
      padding: 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .support-container {
      max-width: 800px;
      margin: 20px auto;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .support-header {
      background: #2c3e50;
      color: white;
      padding: 20px;
      border-radius: 10px 10px 0 0;
      text-align: center;
    }
    .support-body {
      padding: 25px;
    }
    .form-section {
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e9ecef;
    }
    .btn-primary {
      background: #3498db;
      border: none;
      padding: 10px 20px;
      font-weight: 500;
    }
    .btn-primary:hover {
      background: #2980b9;
    }
    .form-label {
      font-weight: 500;
      margin-bottom: 6px;
      color: #2c3e50;
    }
    .alert-fixed {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1050;
      min-width: 300px;
      max-width: 400px;
    }
  </style>
</head>
<body>
  <!-- Mensajes de alerta -->
  <?php if ($mostrar_mensaje): ?>
  <div class="alert <?php echo $clase_alerta; ?> alert-dismissible fade show alert-fixed" role="alert">
    <div class="d-flex align-items-center">
      <i class="fas <?php echo $icono; ?> fa-2x me-3"></i>
      <div>
        <h6 class="alert-heading mb-1"><?php echo $titulo; ?></h6>
        <p class="mb-0 small"><?php echo $texto_mensaje; ?></p>
      </div>
      <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  <?php endif; ?>

  <div class="support-container">
    <div class="support-header">
      <h4 class="mb-1"><i class="fas fa-life-ring me-2"></i>Soporte</h4>
      
    </div>
    
    <div class="support-body">
      <form method="POST" action="enviar_soporte.php">
        <!-- Información del usuario -->
        <div class="form-section">
          <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Información del usuario</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Obra Social</label>
              <input type="text" class="form-control bg-light" value="<?php echo $obra_social; ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Usuario</label>
              <input type="text" class="form-control bg-light" value="<?php echo $usuario; ?>" readonly>
            </div>
          </div>
        </div>

        <!-- Información de contacto -->
        <div class="form-section">
          <h5 class="text-primary mb-3"><i class="fas fa-envelope me-2"></i>Información de contacto</h5>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="tu.email@ejemplo.com">
          </div>
        </div>

        <!-- Detalles de la consulta -->
        <div class="form-section">
          <h5 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i>Detalles de la consulta</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Sector</label>
              <select name="sector" class="form-select" required>
                <option value="">Seleccione un sector</option>
                <option value="Afiliaciones">Afiliaciones</option>
                <option value="Auditoría">Auditoría</option>
                <option value="Facturación">Facturación</option>
                
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Asunto</label>
              <input type="text" name="asunto" class="form-control" required placeholder="Describa brevemente el problema">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Mensaje</label>
            <textarea name="mensaje" rows="4" class="form-control" required placeholder="Describa en detalle su consulta o problema"></textarea>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-2">
          <button type="reset" class="btn btn-secondary">Limpiar</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i>Generar consulta
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cerrar automáticamente las alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      });
    });
  </script>
</body>
</html>