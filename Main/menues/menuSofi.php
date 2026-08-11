<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include('../../Config/init.inc');


session_start();
// Suponiendo que ya guardás el usuario en la sesión, como:
$_SESSION['usuario'] = 'admin_test';  


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo Afiliaciones; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    
    #menuPrincipal {
      width: 280px;
      min-height: 100vh;
      background-color: #343a40;
      color: white;
      padding: 1rem;
      display: flex;
      flex-direction: column;
    }
    #menuPrincipal .nav-link {
      color: white;
      font-size: 12px;
    }
    #menuPrincipal .nav-link:hover,
    #menuPrincipal .nav-link.active {
      background-color: #0d6efd;
      color: white;
    }
    .submenu {
      display: none;
      padding-left: 15px;
    }
    #menuPrincipal .nav {
      overflow: auto;
    }
    #menuPrincipal footer {
      margin-top: auto;
    }
    #menuPrincipal .nav-link i {
    margin-right: 12px;
    min-width: 20px;
    text-align: center;
    }

    #menuPrincipal .nav-link {
    padding-left: 16px;
    white-space: nowrap; /* Evita que se corte el texto */
    }

      
    }
  </style>
</head>
<body>

  <div id="menuPrincipal" class="d-flex flex-column">
  <h5 class="text-white mb-4">
    <small class="text-secondary">Afiliaciones</small><br>
  </h5>
  <h6 class="mb-3" style="color: white;">
  <small>Perfil: <span id="perfil_activo"></span></small><br><br>
  <small>Usuario: <strong><?php echo $_SESSION['usuario']; ?></strong></small>
</h6>

  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link" href="../dashboard.php" target="bottomFrame"><i class="fas fa-home me-2"></i>Home</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../../APP/buscar_afiliado/index.php" target="bottomFrame"><i class="fas fa-user me-2"></i>Afiliados</a>
    </li>
    <?php if(INST_NAME == 'ospm'){ ?>
    <li class="nav-item">
      <a class="nav-link" href="../../APP/carnets/imprimir_filiales.php" target="bottomFrame"><i class="fas fa-id-card me-2"></i>Imp. Carnets Filial</a>
    </li>
    <?php } ?>
    <?php if(INST_NAME == 'osemm'){ ?>
    <li class="nav-item">
      <a class="nav-link" href="../../APP/padron/afil_consulta_ddjj.php" target="bottomFrame"><i class="fas fa-file-alt me-2"></i>Consulta DDJJ afil</a>
    </li>
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-id-card me-2"></i>Carnets <i class="fas fa-caret-down float-end"></i></a>
      <ul class="submenu list-unstyled">
        <li><a class="nav-link" href="../../APP/carnets/carnets.php" target="bottomFrame">Imprimir carnets (provisorio)</a></li>
        <li><a class="nav-link" href="../../APP/carnets/index.php" target="bottomFrame">Imprimir carnets plástico</a></li>
        <li><a class="nav-link" href="../../APP/carnets/carnets_x_seccional.php" target="bottomFrame">Carnets por Seccional</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-cogs me-2"></i>Administración <i class="fas fa-caret-down float-end"></i></a>
      <ul class="submenu list-unstyled">
        <li><a class="nav-link" href="../../APP/administracion/abm_empresas/buscar_empresas.php" target="bottomFrame">ABM de Empresas</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-list me-2"></i>Listados <i class="fas fa-caret-down float-end"></i></a>
      <ul class="submenu list-unstyled">
        <li><a class="nav-link" href="../../APP/listados/descargar_padron_hoy.php" target="bottomFrame">Padrón Completo</a></li>
        <li><a class="nav-link" href="../../APP/listados/propios_por_cp.php" target="bottomFrame">Propios por CP | Imagen</a></li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-search me-2"></i>Consultas <i class="fas fa-caret-down float-end"></i></a>
      <ul class="submenu list-unstyled">
        <li><a class="nav-link" href="../../APP/padron/consulta_propios.php" target="bottomFrame">Consulta Propios</a></li>
        <li><a class="nav-link" href="../../APP/padron/afiliados_de_baja.php" target="bottomFrame">Afiliados de Baja</a></li>
      </ul>
    </li>
    <?php } ?>
    <li class="nav-item">
      <a class="nav-link" href="../../APP/agenda_archivos/index.php" target="bottomFrame"><i class="fas fa-calendar me-2"></i>Agenda</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../../APP/genera_novedades/index.php" target="bottomFrame"><i class="fas fa-bolt me-2"></i>Novedades</a>
    <li class="nav-item">
  <a class="nav-link" href='../../APP/insertadores/opciones_recibidas/' target="bottomFrame">
    <i class="fas fa-lg fa-arrows-down-to-people me-2"></i> Solicitud de Traspasos
  </a>
  </li>
    <li class="nav-item">
      <a class="nav-link toggle-submenu" href="#"><i class="fas fa-chart-bar me-2"></i>Reportes <i class="fas fa-caret-down float-end"></i></a>
      <ul class="submenu list-unstyled">
        <li><a class="nav-link" href="../../APP/listados/descargar_gerenciadora.php" target="bottomFrame">Padrón</a></li>
      <li class="nav-item">
  <a class="nav-link" href='../../APP/listados/descargar_movimientos.php' target="bottomFrame">
    Altas / Bajas
  </a>
</li>

<li class="nav-item">
  <a class="nav-link" href='../../APP/listados/tablero_traspasos.php' target="bottomFrame">
   Tablero de traspasos
  </a>
</li>

<li class="nav-item">
  <a class="nav-link" href='../../APP/listados/formulario_traspasos.php' target="bottomFrame">
    Descarga de traspasos
  </a>
</li>
    </a>      

        <?php
          if($usuario=="admin_mteam"){
            ?>
            
            <?
          }
        ?>
        <?php if (INST_NAME == "ospilm" || INST_NAME == "ospedyb") { ?>
        <li><a class="nav-link" href="../../APP/listados/descargar_padron_apaisado.php" target="bottomFrame">Evolución del Padrón</a></li>
        <?php } ?>
        
        <li><a class="nav-link" href="../../APP/administracion/l_pr_afi.php" target="bottomFrame">Consumos</a></li>
      </ul>
      
          <li class="nav-item">
  <a class="nav-link" href="../../APP/soporte.php" target="bottomFrame">
    <i class="fas fa-user me-2"></i>Soporte
  </a>
</li>

  
    

  </ul>

  <footer class="mt-auto">
    <a class="btn btn-dark text-light" onclick="cerrar_sesion();">
      <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
    </a>
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  const perfil = "<?php echo $_SESSION['perfil']; ?>";
  $('#perfil_activo').html(perfil).css('textTransform', 'capitalize');
  function cerrar_sesion() {
    parent.window.location.href = '../salir.php';
  }

  $(document).ready(function () {
    $('.toggle-submenu').on('click', function (e) {
      e.preventDefault();
      var $submenu = $(this).next('.submenu');
      $('.submenu').not($submenu).slideUp();
      $submenu.slideToggle();
    });
  });
</script>
  

</body>
</html>


