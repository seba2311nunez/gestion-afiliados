<?php
include('../../../Config/Conectar.inc');

if(!$id_afiliado){
    echo "<h3>Acceso restringido.</h3>";exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentacion Respaldatoria</title>

    <!-- Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .no-resize {
            resize: none;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Documentacion de Afiliado ID: <span id="badge_id_afiliado"></span> </h1>
    <h2 class="text-center"><span id="badge_cuil_afiliado"></span> - <span id="badge_ayn_afiliado"></span></h1>
    <!-- File Upload Section -->
    <div class="mt-4">
        <h2>Agregar Documentación</h2>
        <div class="form-group">
            <div class="row">
                <div class="col-md">
                    <select id="tipo_archivo" class="form-control">
                        <option>Legajos</option>
                        <option>Planillas</option>
                        <option>SURGE</option>
                        <option>Documentacion_Afiliatoria</option>
                    </select>
                </div>
                <div class="col-md">
                    <select id="tipo_archivo_2" class="form-control">
                        <option selected>2026</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>   
                </div>    
            </div>
            <hr>
            <input type="file" id="fileInput" class="form-control-file">
            <br>
            <h4>Observacion:</h4>
            <textarea id="observacion" class="form-control no-resize" rows="4" placeholder='Agregue una observacion (opcional)'></textarea>
        </div>
        <button id="uploadButton" class="btn btn-primary">Subir Documento</button>
        <div id="uploadStatus" class="mt-3"></div>
    </div>

    <div id="content-list" style="display:none;" class="mt-4">
        <h2>Contenido:</h2>
        <!-- Table for displaying files -->
        <table id="filesTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tamaño</th>
                    <th>Última Modificación</th>
                    <th>Observacion</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="contents">
                <!-- Content will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap 4 JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
const INST_NAME = "<?echo INST_NAME;?>";
const id_afiliado = "<?echo $id_afiliado;?>";
const cuil = "<?echo $cuil;?>";
const ayn = "<?echo $ayn;?>";
var const_prefix = "";
$('#badge_id_afiliado').html(id_afiliado);
$('#badge_cuil_afiliado').html(cuil);
$('#badge_ayn_afiliado').html(ayn);

$(document).ready(function() {
	let filesTable = $('#filesTable').DataTable({
		destroy: true, // Permite reinicializar DataTable cada vez que se cargan nuevos datos
		searching: true, // Habilita la búsqueda en la tabla
		paging: true, // Habilita la paginación en la tabla
		"language": {
			"decimal": ",",
			"thousands": ".",
			"processing": "Procesando...",
			"lengthMenu": "Mostrar _MENU_ registros por página",
			"zeroRecords": "No se encontraron resultados",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
			"infoEmpty": "Mostrando 0 a 0 de 0 registros",
			"infoFiltered": "(filtrado de _MAX_ registros totales)",
			"search": "Buscar:",
			"paginate": {
				"first": "Primero",
				"last": "Último",
				"next": "Siguiente",
				"previous": "Anterior"
			},
			"loadingRecords": "Cargando...",
			"emptyTable": "No hay datos disponibles en la tabla"
		}    
	});

	const_prefix = `padron/${$('#tipo_archivo').val()}/${$('#tipo_archivo_2').val()}/${id_afiliado}/`;
	loadFolderContents(const_prefix);

	$('#uploadButton').click(function() {
		var fileInput = $('#fileInput')[0].files[0];
		if (!fileInput) {
			alert('Por favor, selecciona un archivo para subir.');
			return;
		}
		uploadFile(const_prefix, fileInput, id_afiliado);
	});
	$('#tipo_archivo, #tipo_archivo_2').on('change',function(e){
		const_prefix = `padron/${$('#tipo_archivo').val()}/${$('#tipo_archivo_2').val()}/${id_afiliado}/`;
		loadFolderContents(const_prefix);
	});
    $(document).on('click', '.btn-delete', function(e) {

        e.preventDefault();
        const filePath = $(this).data('file');
        if (confirm('¿Está seguro de que desea eliminar este archivo?')) {
            $.ajax({
                url: 'delete.php',
                method: 'POST',
                data: { filepath: filePath },
                success: function(response) {
                    if (response.success) {
                        alert('Archivo eliminado correctamente.');
                        loadFolderContents(const_prefix); // Replace with actual input if needed
                    } else {
                        alert('Error al eliminar el archivo: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error al realizar la solicitud: ' + error);
                }
            });
        }
    });
});

// Function to load folder contents
function loadFolderContents(prefix) {
    $.ajax({
        url: 'load.php',
        method: 'GET',
        dataType: 'json',
        data: { prefix: prefix },
        success: function(result) {
            // Limpia los datos existentes en la tabla y en DataTable
            let filesTable = $('#filesTable').DataTable();
            filesTable.clear().draw();

            if (result.folders.length === 0 && result.files.length === 0) {
                //$('#contents').append('<tr><td colspan="4" class="text-center">No hay archivos cargados para este Afiliado en esta carpeta.</td></tr>');
            }

            // Agrega los archivos a la tabla
            if (result.files.length > 0) {
                result.files.forEach(function(file) {
                    let filename = file.name.split('/').pop();
                    let encodedFilePath = encodeURIComponent(file.name);


                    let obs = file.observacion || "";
                    obs = obs.replace(/\\r\\n|\\n|\\r/g, "<br>");

                    filesTable.row.add([
                        filename,
                        file.size,
                        file.last_modified,
                        obs,
                        file.owner,
                        `<a href="${file.url}" download>Descargar</a>
                        <a class="btn-delete" data-file="${encodedFilePath}">Eliminar</a>`
                    ]).draw();
                });
            }

            $('#content-list').show();
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
        }
    });
}

// Function to upload file
function uploadFile(prefix, file, p_id_afiliado) {
    var formData = new FormData();
    formData.append('file', file);
    formData.append('prefix', prefix);
    formData.append('id_afiliado', p_id_afiliado);

    var observacion = $('#observacion').val();
    formData.append('observacion', observacion);

    $.ajax({
        url: 'upload.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#uploadStatus').html(`<div class="alert alert-success">Archivo subido con éxito. 
                <br> Ruta de Guardado: ${prefix} </div>`);
            loadFolderContents(prefix);
        },
        error: function(xhr, status, error) {
            $('#uploadStatus').html('<div class="alert alert-danger">Error al subir el archivo: ' + error + '</div>');
        }
    });
}
</script>

</body>
</html>