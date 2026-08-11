<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once(__DIR__ . '/../../Config/init.inc');

$perfilActivo = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : '';
$usuarioActivo = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : '';
$institucionActiva = INST_NAME;
$tipoMenu = isset($tipoMenu) ? $tipoMenu : ($perfilActivo === 'consulta_filial' ? 'consulta_padron' : $perfilActivo);

/*
 * Primera fuente de datos del menú. Más adelante este arreglo podrá ser
 * reemplazado por registros SQL sin modificar el renderizado HTML.
 */
$opcionesMenu = array(
    array('id' => 'home', 'titulo' => 'Home', 'icono' => '⌂', 'url' => '../dashboard.php'),
    array('id' => 'afiliados', 'titulo' => 'Afiliados', 'icono' => '●', 'url' => '../../APP/buscar_afiliado/index.php', 'descripcion' => 'Consulta y grupo familiar actual', 'perfiles' => array('admin', 'consulta_padron')),
    array('id' => 'carnet-filial', 'titulo' => 'Imp. Carnets Filial', 'icono' => '▣', 'url' => '../../APP/carnets/imprimir_filiales.php', 'obras' => array('ospm')),
    array('id' => 'consulta-ddjj', 'titulo' => 'Consulta DDJJ afil.', 'icono' => '▤', 'url' => '../../APP/padron/afil_consulta_ddjj.php', 'descripcion' => 'Buscar un afiliado por CUIL, DNI o nombre', 'obras' => array('osemm', 'ospm')),
    array(
        'id' => 'carnets', 'titulo' => 'Carnets', 'icono' => '▣', 'obras' => array('osemm', 'ospm'),
        'hijos' => array(
            array('id' => 'carnets-provisorio', 'titulo' => 'Imprimir carnets (provisorio)', 'url' => '../../APP/carnets/carnets.php'),
            array('id' => 'carnets-plastico', 'titulo' => 'Imprimir carnets plástico', 'url' => '../../APP/carnets/index.php'),
            array('id' => 'carnets-seccional', 'titulo' => 'Carnets plástico por seccional', 'url' => '../../APP/carnets/carnets_x_seccional.php'),
        ),
    ),
    array(
        'id' => 'administracion', 'titulo' => 'Administración', 'icono' => '⚙', 'obras' => array('osemm', 'ospm'),
        'hijos' => array(
            array('id' => 'abm-empresas', 'titulo' => 'ABM de Empresas', 'url' => '../../APP/administracion/abm_empresas/buscar_empresas.php'),
        ),
    ),
    array(
        'id' => 'listados', 'titulo' => 'Listados', 'icono' => '☷', 'obras' => array('osemm', 'ospm'),
        'hijos' => array(
            array('id' => 'padron-completo', 'titulo' => 'Padrón Completo', 'url' => '../../APP/listados/descargar_padron_hoy.php'),
            array('id' => 'exportaciones-prestadores', 'titulo' => 'Exportaciones Prestadores', 'url' => '../../APP/listados/propios_por_cp.php'),
        ),
    ),
    array(
        'id' => 'consultas', 'titulo' => 'Consultas', 'icono' => '⌕', 'obras' => array('osemm', 'ospm'),
        'hijos' => array(
            array('id' => 'consulta-propios', 'titulo' => 'Consulta Propios', 'url' => '../../APP/padron/consulta_propios.php'),
            array('id' => 'afiliados-baja', 'titulo' => 'Afiliados de Baja', 'url' => '../../APP/padron/afiliados_de_baja.php'),
        ),
    ),
    array('id' => 'agenda', 'titulo' => 'Agenda', 'icono' => '□', 'url' => '../../APP/agenda_archivos/index.php'),
    array('id' => 'novedades', 'titulo' => 'Novedades', 'icono' => 'ϟ', 'url' => '../../APP/actualizacion_padron_sss/'),
    array('id' => 'solicitud-traspasos', 'titulo' => 'Solicitud de Traspasos', 'icono' => '⇩', 'url' => '../../APP/insertadores/opciones_recibidas/'),
    array(
        'id' => 'reportes', 'titulo' => 'Reportes', 'icono' => '▥',
        'hijos' => array(
            array('id' => 'reporte-padron', 'titulo' => 'Padrón', 'url' => '../../APP/listados/descargar_gerenciadora.php'),
            array('id' => 'altas-bajas', 'titulo' => 'Altas / Bajas', 'url' => '../../APP/listados/descargar_movimientos.php'),
            array('id' => 'tablero-traspasos', 'titulo' => 'Tablero de traspasos', 'url' => '../../APP/listados/tablero_traspasos.php'),
            array('id' => 'tablero-traspasos-redes', 'titulo' => 'Tablero de traspasos (Redes)', 'url' => '../../APP/listados/tablero_traspasos_red.php'),
            array('id' => 'descarga-traspasos', 'titulo' => 'Descarga de traspasos', 'url' => '../../APP/listados/formulario_traspasos.php'),
            array('id' => 'consumos', 'titulo' => 'Consumos', 'url' => '../../APP/administracion/l_pr_afi.php'),
            array('id' => 'listar-legajos', 'titulo' => 'Listar Legajos', 'url' => '../../APP/listados/descargar_legajos_subidos.php', 'obras' => array('ospm')),
        ),
    ),
);

function opcionDisponible($opcion, $institucion, $tipoMenu)
{
    $disponibleParaObra = !isset($opcion['obras']) || in_array($institucion, $opcion['obras'], true);
    $perfilesPermitidos = isset($opcion['perfiles']) ? $opcion['perfiles'] : array('admin');

    return $disponibleParaObra && in_array($tipoMenu, $perfilesPermitidos, true);
}

function escaparMenu($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function renderizarOpcionesMenu($opciones, $institucion, $tipoMenu)
{
    foreach ($opciones as $opcion) {
        if (!opcionDisponible($opcion, $institucion, $tipoMenu)) {
            continue;
        }

        $id = escaparMenu($opcion['id']);
        $titulo = escaparMenu($opcion['titulo']);
        $icono = isset($opcion['icono']) ? escaparMenu($opcion['icono']) : '';
        $descripcion = isset($opcion['descripcion']) ? escaparMenu($opcion['descripcion']) : $titulo;

        if (isset($opcion['hijos'])) {
            echo '<li class="menu-item menu-grupo" data-menu-id="' . $id . '" data-search="' . $titulo . '">';
            echo '<button type="button" class="menu-link submenu-toggle" data-submenu="submenu-' . $id . '" data-tooltip="' . $titulo . '" title="' . $titulo . '" aria-expanded="false">';
            echo '<span class="menu-icon" aria-hidden="true">' . $icono . '</span><span class="menu-text">' . $titulo . '</span><span class="chevron" aria-hidden="true">⌄</span>';
            echo '</button><ul class="submenu" id="submenu-' . $id . '">';
            renderizarOpcionesMenu($opcion['hijos'], $institucion, $tipoMenu);
            echo '</ul></li>';
            continue;
        }

        echo '<li class="menu-item menu-hoja" data-menu-id="' . $id . '" data-search="' . $titulo . '">';
        echo '<button type="button" class="favorito" data-favorito="' . $id . '" aria-label="Agregar ' . $titulo . ' a favoritos" title="Agregar a favoritos">☆</button>';
        echo '<a class="menu-link" href="' . escaparMenu($opcion['url']) . '" target="bottomFrame" title="' . $descripcion . '" data-tooltip="' . $titulo . '">';
        if ($icono !== '') {
            echo '<span class="menu-icon" aria-hidden="true">' . $icono . '</span>';
        }
        echo '<span class="menu-text">' . $titulo . '</span></a>';
        echo '</li>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escaparMenu(INST_NAME_F); ?></title>
    <link rel="stylesheet" href="menuAdminDesarrollo.css">
</head>
<body>
<aside id="menuPrincipal" data-storage-key="<?php echo escaparMenu($institucionActiva . '-' . $perfilActivo); ?>">
    <header class="menu-header">
        <div class="marca">
            <strong class="menu-text"><?php echo escaparMenu(INST_NAME_F); ?></strong>
            <span class="menu-text">Afiliaciones</span>
        </div>
        <button type="button" id="contraerMenu" class="icon-button" data-tooltip="Contraer menú" aria-label="Contraer menú">☰</button>
    </header>

    <div class="identidad menu-text">
        <span>Usuario: <strong><?php echo escaparMenu($usuarioActivo); ?></strong></span>
        <span>Perfil: <strong id="perfil_activo"><?php echo escaparMenu($perfilActivo); ?></strong></span>
    </div>

    <section class="herramientas" aria-label="Herramientas del menú">
        <label class="buscador" data-tooltip="Buscar opciones">
            <span aria-hidden="true">⌕</span>
            <input id="buscarOpcion" type="search" placeholder="Buscar opción..." autocomplete="off">
        </label>
        <button type="button" id="mostrarFavoritos" class="filtro-favoritos" data-tooltip="Mostrar solo favoritos" aria-pressed="false"><span aria-hidden="true">☆</span><span class="menu-text">Favoritos</span></button>
    </section>

    <p id="sinResultados" class="sin-resultados" hidden>No se encontraron opciones.</p>
    <nav class="menu-scroll" aria-label="Menú principal">
        <ul id="listaMenu" class="menu-lista">
            <?php renderizarOpcionesMenu($opcionesMenu, $institucionActiva, $tipoMenu); ?>
        </ul>
    </nav>

    <footer>
        <button type="button" class="cerrar-sesion" data-tooltip="Cerrar sesión" onclick="cerrarSesion();"><span aria-hidden="true">↪</span><span class="menu-text">Cerrar sesión</span></button>
    </footer>
</aside>

<script src="menuAdminDesarrollo.js"></script>
</body>
</html>
