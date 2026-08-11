(function () {
    'use strict';

    var menu = document.getElementById('menuPrincipal');
    var lista = document.getElementById('listaMenu');
    var buscador = document.getElementById('buscarOpcion');
    var botonFavoritos = document.getElementById('mostrarFavoritos');
    var botonContraer = document.getElementById('contraerMenu');
    var sinResultados = document.getElementById('sinResultados');
    var prefijo = 'menuAdminDesarrollo:' + (menu.dataset.storageKey || 'general') + ':';
    var favoritos = leerJson('favoritos', []);
    var soloFavoritos = false;

    function leerJson(clave, valorInicial) {
        try {
            var valor = localStorage.getItem(prefijo + clave);
            return valor === null ? valorInicial : JSON.parse(valor);
        } catch (error) {
            return valorInicial;
        }
    }

    function guardarJson(clave, valor) {
        try { localStorage.setItem(prefijo + clave, JSON.stringify(valor)); } catch (error) { /* Navegación privada o almacenamiento deshabilitado. */ }
    }

    function normalizar(texto) {
        return (texto || '').toLocaleLowerCase('es').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function abrirSubmenu(id, guardar) {
        document.querySelectorAll('.submenu').forEach(function (submenu) {
            var abrir = submenu.id === id;
            submenu.classList.toggle('abierto', abrir);
            var boton = document.querySelector('[data-submenu="' + submenu.id + '"]');
            if (boton) boton.setAttribute('aria-expanded', abrir ? 'true' : 'false');
        });
        if (guardar !== false) guardarJson('submenuAbierto', id || '');
    }

    function actualizarFavoritos() {
        document.querySelectorAll('[data-favorito]').forEach(function (boton) {
            var activo = favoritos.indexOf(boton.dataset.favorito) !== -1;
            boton.classList.toggle('activo', activo);
            boton.textContent = activo ? '★' : '☆';
            boton.title = activo ? 'Quitar de favoritos' : 'Agregar a favoritos';
            boton.setAttribute('aria-label', boton.title);
        });
    }

    function filtrarMenu() {
        var termino = normalizar(buscador.value.trim());
        var visibles = 0;

        lista.querySelectorAll(':scope > .menu-item').forEach(function (item) {
            if (item.classList.contains('menu-grupo')) {
                var hijosVisibles = 0;
                var coincideGrupo = normalizar(item.dataset.search).indexOf(termino) !== -1;
                item.querySelectorAll('.menu-hoja').forEach(function (hijo) {
                    var coincide = coincideGrupo || normalizar(hijo.dataset.search).indexOf(termino) !== -1;
                    var esFavorito = favoritos.indexOf(hijo.dataset.menuId) !== -1;
                    var visible = coincide && (!soloFavoritos || esFavorito);
                    hijo.classList.toggle('oculto', !visible);
                    if (visible) hijosVisibles++;
                });
                var mostrarGrupo = hijosVisibles > 0;
                item.classList.toggle('oculto', !mostrarGrupo);
                if (mostrarGrupo) visibles++;
                if (termino && mostrarGrupo) abrirSubmenu(item.querySelector('.submenu').id, false);
            } else {
                var coincide = normalizar(item.dataset.search).indexOf(termino) !== -1;
                var esFavorito = favoritos.indexOf(item.dataset.menuId) !== -1;
                var visible = coincide && (!soloFavoritos || esFavorito);
                item.classList.toggle('oculto', !visible);
                if (visible) visibles++;
            }
        });

        sinResultados.hidden = visibles !== 0;
        if (!termino) abrirSubmenu(leerJson('submenuAbierto', ''), false);
    }

    function ajustarFrameset(contraido) {
        try {
            var frameset = parent.document.getElementById('estructuraPrincipal');
            if (frameset) {
                frameset.cols = contraido ? '64,*' : '300,*';
                frameset.setAttribute('cols', frameset.cols);
            }
        } catch (error) { /* Puede ejecutarse fuera del frameset. */ }
    }

    function establecerContraido(contraido) {
        menu.classList.toggle('contraido', contraido);
        botonContraer.setAttribute('aria-label', contraido ? 'Expandir menú' : 'Contraer menú');
        botonContraer.dataset.tooltip = contraido ? 'Expandir menú' : 'Contraer menú';
        ajustarFrameset(contraido);
        guardarJson('contraido', contraido);
    }

    document.querySelectorAll('.submenu-toggle').forEach(function (boton) {
        boton.addEventListener('click', function () {
            var id = boton.dataset.submenu;
            abrirSubmenu(boton.getAttribute('aria-expanded') === 'true' ? '' : id, true);
        });
    });

    document.querySelectorAll('[data-favorito]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            var id = boton.dataset.favorito;
            var posicion = favoritos.indexOf(id);
            if (posicion === -1) favoritos.push(id); else favoritos.splice(posicion, 1);
            guardarJson('favoritos', favoritos);
            actualizarFavoritos();
            filtrarMenu();
        });
    });

    lista.querySelectorAll('a[target="bottomFrame"]').forEach(function (enlace) {
        enlace.addEventListener('click', function () {
            lista.querySelectorAll('.menu-link.activo').forEach(function (item) { item.classList.remove('activo'); });
            enlace.classList.add('activo');
            guardarJson('opcionActiva', enlace.closest('.menu-item').dataset.menuId);
        });
    });

    buscador.addEventListener('input', filtrarMenu);
    botonFavoritos.addEventListener('click', function () {
        soloFavoritos = !soloFavoritos;
        botonFavoritos.classList.toggle('activo', soloFavoritos);
        botonFavoritos.setAttribute('aria-pressed', soloFavoritos ? 'true' : 'false');
        botonFavoritos.querySelector('[aria-hidden]').textContent = soloFavoritos ? '★' : '☆';
        filtrarMenu();
    });
    botonContraer.addEventListener('click', function () { establecerContraido(!menu.classList.contains('contraido')); });

    actualizarFavoritos();
    abrirSubmenu(leerJson('submenuAbierto', ''), false);
    var opcionActiva = leerJson('opcionActiva', '');
    var activa = lista.querySelector('[data-menu-id="' + opcionActiva + '"] > .menu-link');
    if (activa) activa.classList.add('activo');
    establecerContraido(leerJson('contraido', false));

    window.cerrarSesion = function () { parent.window.location.href = '../salir.php'; };
}());
