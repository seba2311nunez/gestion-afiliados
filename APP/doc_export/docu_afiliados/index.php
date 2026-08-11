<?php
include('../../../Config/Conectar.inc');
$idAfiliado=isset($_GET['id_afiliado']) ? intval($_GET['id_afiliado']) : 0;
$dni=isset($_GET['dni']) ? preg_replace('/[^0-9]/','',$_GET['dni']) : '';
if($idAfiliado<=0){ echo '<h3>Acceso restringido.</h3>'; exit(); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Documentación del afiliado</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body{background:#fff;color:#111}.contenedor{max-width:1110px;margin:42px auto}.panel{border:1px solid #ddd;border-radius:3px}.panel-titulo{background:#087ef5;color:#fff;font-weight:600;padding:13px 20px}.panel-cuerpo{padding:20px}.drop-zone{height:134px;border:2px dashed #6c7a86;border-radius:14px;display:flex;align-items:center;justify-content:center;text-align:center;cursor:pointer}.drop-zone.activa{border-color:#087ef5;background:#eef6ff}.acciones{display:flex;justify-content:space-between;margin-top:15px}.tabla-wrap{margin-top:25px}.nombre-archivo{max-width:430px;word-break:break-word}.btn-ver{background:#00a9c6;color:#fff}.btn-borrar{background:#fa2947;color:#fff}.estado{min-height:25px;margin-top:12px}
  </style>
</head>
<body>
<main class="contenedor">
  <h2 class="mb-4">Documentación del Afiliado: <?=htmlspecialchars((string)$idAfiliado,ENT_QUOTES,'UTF-8')?></h2>
  <section class="panel">
    <div class="panel-titulo">Agregar Documentación</div>
    <div class="panel-cuerpo">
      <div id="dropZone" class="drop-zone">
        <div><strong>Arrastrá los archivos acá</strong><br><small class="text-muted">o hacé clic para seleccionarlos desde tu equipo</small></div>
      </div>
      <input id="archivos" type="file" multiple hidden>
      <input id="carpeta" type="file" webkitdirectory directory multiple hidden>
      <div class="acciones">
        <div>
          <button id="seleccionarArchivos" class="btn btn-outline-primary btn-sm">Seleccionar archivos...</button>
          <button id="seleccionarCarpeta" class="btn btn-outline-secondary btn-sm">Seleccionar carpeta...</button>
        </div>
        <div>
          <button id="limpiar" class="btn btn-outline-secondary btn-sm" disabled>Limpiar</button>
          <button id="subir" class="btn btn-primary btn-sm" disabled>Subir</button>
        </div>
      </div>
      <div id="seleccion" class="estado text-muted"></div>
      <div id="estado" class="estado"></div>
    </div>
  </section>
  <section class="tabla-wrap">
    <h2>Contenido:</h2>
    <table id="tablaDocumentacion" class="display" style="width:100%">
      <thead><tr><th>Nombre</th><th>Tamaño</th><th>Última Modificación</th><th>Usuario</th><th>Acción</th></tr></thead>
      <tbody></tbody>
    </table>
  </section>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
(function(){
  var idAfiliado=<?=$idAfiliado?>;
  var anioActual=(new Date()).getFullYear();
  var archivos=[];
  var tabla=$('#tablaDocumentacion').DataTable({language:{search:'Buscar:',lengthMenu:'Mostrar _MENU_ registros por página',zeroRecords:'No se encontraron documentos',info:'Mostrando _START_ a _END_ de _TOTAL_',infoEmpty:'Sin documentos',paginate:{next:'Siguiente',previous:'Anterior'}},order:[[2,'desc']]});
  function prefijo(anio){return 'padron/Legajos/'+anio+'/'+idAfiliado+'/';}
  function escapar(valor){return $('<div>').text(valor||'').html();}
  function nombre(ruta){return (ruta||'').split('/').pop();}
  function mostrarSeleccion(){
    $('#seleccion').text(archivos.length ? archivos.length+' archivo(s) seleccionado(s)' : '');
    $('#limpiar,#subir').prop('disabled',archivos.length===0);
  }
  function seleccionar(lista){archivos=Array.prototype.slice.call(lista||[]);mostrarSeleccion();}
  function cargarContenido(){
    tabla.clear().draw();
    var consultas=[];
    for(var anio=anioActual;anio>=anioActual-3;anio--){consultas.push($.getJSON('../../ver_grupo_familiar/doc_export/load.php',{prefix:prefijo(anio)}));}
    $.when.apply($,consultas).done(function(){
      var respuestas=consultas.length===1?[arguments[0]]:Array.prototype.slice.call(arguments).map(function(r){return r[0];});
      respuestas.forEach(function(respuesta){
        (respuesta.files||[]).forEach(function(file){
          tabla.row.add([
            '<span class="nombre-archivo">'+escapar(nombre(file.name))+'</span>',
            escapar(file.size),escapar(file.last_modified),escapar(file.owner),
            '<a class="btn btn-sm btn-ver mr-1" target="_blank" title="Ver" href="'+escapar(file.url)+'"><i class="fas fa-eye"></i></a>'+
            '<button class="btn btn-sm btn-borrar" title="Eliminar" data-ruta="'+escapar(file.name)+'"><i class="fas fa-trash-alt"></i></button>'
          ]).draw(false);
        });
      });
    }).fail(function(xhr){$('#estado').html('<div class="alert alert-danger">No se pudo consultar la documentación.</div>');});
  }
  function subirSiguiente(indice){
    if(indice>=archivos.length){$('#estado').html('<div class="alert alert-success">Documentación subida correctamente.</div>');archivos=[];mostrarSeleccion();cargarContenido();return;}
    var datos=new FormData();
    datos.append('file',archivos[indice]);datos.append('prefix',prefijo(anioActual));datos.append('id_afiliado',idAfiliado);datos.append('observacion','');
    $.ajax({url:'../../ver_grupo_familiar/doc_export/upload.php',method:'POST',data:datos,processData:false,contentType:false,dataType:'json'})
      .done(function(r){if(r.status!=='success'){mostrarError(r.message||'No se pudo subir el archivo.');return;}subirSiguiente(indice+1);})
      .fail(function(){mostrarError('El servidor no devolvió una respuesta válida.');});
  }
  function mostrarError(mensaje){$('#estado').html('<div class="alert alert-danger">'+escapar(mensaje)+'</div>');}
  $('#dropZone').on('click',function(){$('#archivos').click();}).on('dragover',function(e){e.preventDefault();$(this).addClass('activa');}).on('dragleave drop',function(e){e.preventDefault();$(this).removeClass('activa');if(e.type==='drop')seleccionar(e.originalEvent.dataTransfer.files);});
  $('#seleccionarArchivos').on('click',function(e){e.preventDefault();$('#archivos').click();});
  $('#seleccionarCarpeta').on('click',function(e){e.preventDefault();$('#carpeta').click();});
  $('#archivos,#carpeta').on('change',function(){seleccionar(this.files);});
  $('#limpiar').on('click',function(){archivos=[];mostrarSeleccion();$('#archivos,#carpeta').val('');});
  $('#subir').on('click',function(){if(archivos.length){$(this).prop('disabled',true);$('#estado').text('Subiendo documentación...');subirSiguiente(0);}});
  $('#tablaDocumentacion').on('click','.btn-borrar',function(){var ruta=$(this).data('ruta');if(!confirm('¿Eliminar este documento?'))return;$.post('../../ver_grupo_familiar/doc_export/delete.php',{filepath:ruta},function(r){if(r.success)cargarContenido();else mostrarError(r.message||'No se pudo eliminar.');},'json');});
  cargarContenido();
})();
</script>
</body>
</html>
