<?php
/*
    Prueba aislada de documentacion de afiliados sobre S3 para OSEMM.
    Se accede directo con ?id_afiliado=NNN (sin pasar por el menu de
    ver_grupo_familiar), para validar que los archivos migrados por
    migrar_documentacion_osemm_a_s3.py se puedan ver/abrir bien.
*/
include('../../Config/Conectar.inc');

if(!$id_afiliado){
    echo "<h3>Acceso restringido. Pasar ?id_afiliado=NNN por URL.</h3>";exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentacion Respaldatoria (prueba)</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <style>
        body { font-family: Arial, sans-serif; }
        .drop-zone {
            border: 2px dashed #6c757d;
            border-radius: 0.75rem;
            padding: 2.5rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .drop-zone.hover { border-color: #0d6efd; background-color: #f0f8ff; }
        .file-list { max-height: 220px; overflow-y: auto; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="alert alert-warning">Pantalla de prueba (APP/docu_afiliados) - no enlazada todavia al menu del sistema.</div>
    <h2 class="mb-4">Documentación del Afiliado: <?=$id_afiliado?></h2>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <strong>Agregar Documentación</strong>
      </div>
      <div class="card-body">
        <form id="uploadForm" enctype="multipart/form-data">
          <input type="hidden" id="dni" name="dni" value="<?=$dni?>">
          <input type="hidden" id="id_afiliado" name="id_afiliado" value="<?=$id_afiliado?>">

          <div id="dropZone" class="drop-zone mb-3">
            <div class="fw-semibold mb-1">Arrastrá los archivos acá</div>
            <div class="text-muted small">o hacé clic para seleccionarlos desde tu equipo</div>
          </div>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <button type="button" id="btnSelectFiles" class="btn btn-outline-primary btn-sm">Seleccionar archivos...</button>
            <button type="button" id="btnSelectFolder" class="btn btn-outline-secondary btn-sm">Seleccionar carpeta...</button>
          </div>

          <input type="file" id="fileInput" name="files[]" multiple style="display:none;">
          <input type="file" id="dirInput" name="dirFiles[]" webkitdirectory multiple style="display:none;">

          <div id="fileInfo" class="mb-3" style="display:none;">
            <label class="form-label mb-1 fw-semibold">Archivos seleccionados</label>
            <div class="file-list border rounded p-2 bg-white">
              <ul id="fileList" class="list-unstyled mb-0"></ul>
            </div>
            <div id="fileCount" class="form-text mt-1"></div>
          </div>

          <div id="uploadStatus" class="small text-muted mb-2" style="display:none;"></div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" id="btnLimpiar" class="btn btn-outline-secondary btn-sm" disabled>Limpiar</button>
            <button type="submit" id="btnSubir" class="btn btn-primary btn-sm" disabled>Subir</button>
          </div>
        </form>
      </div>
    </div>

    <div id="content-list" style="display:none;" class="mt-4">
        <h2>Contenido:</h2>
        <table id="filesTable" class="display" style="width:100%; font-size: 12px;">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tamaño</th>
                    <th>Última Modificación</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="contents"></tbody>
        </table>
    </div>
</div>

<!-- Modal cargando -->
<div class="modal fade" id="modalCargando" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div>Cargando documentos...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  const INST_NAME = "<?echo INST_NAME;?>";
  const dni = "<?echo $dni;?>";
  const id_afiliado = "<?echo $id_afiliado;?>";
  const const_prefix = `afiliados/documentacion/${id_afiliado}/`;

  $(document).ready(function() {
      let filesTable = $('#filesTable').DataTable({
          destroy: true,
          searching: true,
          paging: true,
          "language": {
              "decimal": ",", "thousands": ".", "processing": "Procesando...",
              "lengthMenu": "Mostrar _MENU_ registros por página",
              "zeroRecords": "No se encontraron resultados",
              "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
              "infoEmpty": "Mostrando 0 a 0 de 0 registros",
              "infoFiltered": "(filtrado de _MAX_ registros totales)",
              "search": "Buscar:",
              "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" },
              "loadingRecords": "Cargando...",
              "emptyTable": "No hay datos disponibles en la tabla"
          }
      });

      loadFolderContents(const_prefix);
  });

  function loadFolderContents(prefix) {
      $('#modalCargando').modal('show');

      $.ajax({
          url: 'ajax.php',
          method: 'GET',
          dataType: 'json',
          data: { parametro: 'load', prefix: prefix },
          success: function(result) {
              let filesTable = $('#filesTable').DataTable();
              filesTable.clear().draw();

              if (result.folders.length === 0 && result.files.length === 0) {
                  $('#contents').append('<tr><td colspan="5" class="text-center">No hay archivos cargados.</td></tr>');
              }

              if (result.files.length > 0) {
                  result.files.forEach(function(file) {
                      let filename = file.name.split('/').pop();
                      filesTable.row.add([
                          filename,
                          file.size,
                          file.last_modified,
                          file.owner,
                          `<a href="${file.url}" target="_blank" class="btn btn-info btn-xs" title='Ver'><i class="fas fa-eye"></i></a>
                           <a href="#" onclick="eliminarArchivo('${file.delete_url}','${prefix}'); return false;" class="btn btn-danger btn-xs" title='Eliminar'><i class="fas fa-trash-alt"></i></a>`
                      ]).draw();
                  });
              }
              $('#content-list').show();
          },
          error: function(xhr, status, error) { alert('Error: ' + error); },
          complete: function() { $('#modalCargando').modal('hide'); }
      });
  }

  function eliminarArchivo(url, prefix) {
      if (confirm("¿Seguro que deseas eliminar este archivo?")) {
          $.ajax({
              url: url, method: 'GET', dataType: 'json',
              success: function(response) {
                  if (response.status === 'success') { window.location.reload(); }
                  else { alert('Error al eliminar: ' + response.message); }
              },
              error: function(xhr, status, error) { alert('Error: ' + error); }
          });
      }
  }
</script>

<script>
  const dropZone    = document.getElementById('dropZone');
  const fileInput   = document.getElementById('fileInput');
  const dirInput    = document.getElementById('dirInput');
  const form        = document.getElementById('uploadForm');
  const fileInfo    = document.getElementById('fileInfo');
  const fileListUl  = document.getElementById('fileList');
  const fileCount   = document.getElementById('fileCount');
  const btnSubir    = document.getElementById('btnSubir');
  const btnLimpiar  = document.getElementById('btnLimpiar');
  const btnSelect   = document.getElementById('btnSelectFiles');
  const btnFolder   = document.getElementById('btnSelectFolder');
  const uploadStatus= document.getElementById('uploadStatus');
  const idAfiliadoInput = document.getElementById('id_afiliado');

  let selectedFiles = [];

  function bytesToSize(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  function updateFileList() {
    fileListUl.innerHTML = '';
    if (!selectedFiles.length) {
      fileInfo.style.display = 'none';
      fileCount.textContent = '';
      btnSubir.disabled = true;
      btnLimpiar.disabled = true;
      dropZone.innerHTML = '<div class="fw-semibold mb-1">Arrastrá los archivos acá</div><div class="text-muted small">o hacé clic para seleccionarlos desde tu equipo</div>';
      return;
    }
    selectedFiles.forEach(function(f) {
      var li = document.createElement('li');
      li.className = 'd-flex justify-content-between align-items-center border-bottom py-1 small';
      var left = document.createElement('span');
      left.textContent = f.webPath ? f.webPath : f.name;
      var right = document.createElement('span');
      right.className = 'text-muted';
      right.textContent = bytesToSize(f.size);
      li.appendChild(left);
      li.appendChild(right);
      fileListUl.appendChild(li);
    });
    fileInfo.style.display = 'block';
    fileCount.textContent = selectedFiles.length + ' archivo(s) listo(s) para subir';
    btnSubir.disabled = false;
    btnLimpiar.disabled = false;
    dropZone.innerHTML = '<div class="fw-semibold mb-1">' + selectedFiles.length + ' archivo(s) listo(s) para subir</div><div class="text-muted small">Podés agregar más arrastrando o usando los botones</div>';
  }

  function addFiles(fileList) {
    for (var i = 0; i < fileList.length; i++) {
      var f = fileList[i];
      var webPath = f.webkitRelativePath || f.name;
      var nf = new File([f], f.name, {type: f.type, lastModified: f.lastModified});
      nf.webPath = webPath;
      selectedFiles.push(nf);
    }
  }

  function resetAll() {
    selectedFiles = [];
    fileInput.value = '';
    dirInput.value  = '';
    uploadStatus.style.display = 'none';
    uploadStatus.textContent = '';
    updateFileList();
  }

  dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.classList.add('hover'); });
  dropZone.addEventListener('dragleave', function(e) { e.preventDefault(); dropZone.classList.remove('hover'); });
  dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.classList.remove('hover');
    var files = e.dataTransfer.files;
    if (!files || !files.length) return;
    addFiles(files);
    updateFileList();
  });

  dropZone.addEventListener('click', function() { fileInput.click(); });
  btnSelect.addEventListener('click', function() { fileInput.click(); });
  btnFolder.addEventListener('click', function() { dirInput.click(); });

  fileInput.addEventListener('change', function() {
    if (fileInput.files.length) { addFiles(fileInput.files); fileInput.value = ''; updateFileList(); }
  });
  dirInput.addEventListener('change', function() {
    if (dirInput.files.length) { addFiles(dirInput.files); dirInput.value = ''; updateFileList(); }
  });
  btnLimpiar.addEventListener('click', function() { resetAll(); });

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!selectedFiles.length) return;

    var formData = new FormData();
    formData.append('parametro', 'upload');
    formData.append('id_afiliado', idAfiliadoInput.value);
    for (var i = 0; i < selectedFiles.length; i++) {
      var f = selectedFiles[i];
      formData.append('files[]', f, f.webPath || f.name);
    }

    btnSubir.disabled = true;
    btnSubir.textContent = 'Subiendo...';
    uploadStatus.style.display = 'block';
    uploadStatus.textContent = 'Subiendo archivos a S3, por favor esperá...';

    fetch('ajax.php', { method: 'POST', body: formData })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.ok) {
          uploadStatus.textContent = 'Subidos correctamente: ' + data.subidos + ' archivo(s).';
          location.reload();
        } else {
          uploadStatus.textContent = 'Error: ' + (data.msj || 'No se pudo subir.');
          btnSubir.disabled = false;
          btnSubir.textContent = 'Subir';
        }
        resetAll();
      })
      .catch(function(err) {
        console.error(err);
        uploadStatus.textContent = 'Ocurrió un error al subir los archivos.';
        btnSubir.disabled = false;
        btnSubir.textContent = 'Subir';
      });
  });
</script>
</body>
</html>
