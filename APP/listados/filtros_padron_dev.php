<html lang="en">
<head>
		<meta charset="UTF-8">
		<title>Server-side Pagination with DataTables</title>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
		<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
</head>
<body>
	<table id="employeeTable" class="display" style="width:100%">
		<!--
		<thead>
			<tr>
				<th>Nben</th>
				<th>CUIL</th>
				<th>AyN</th>
				<th>ND</th>
			</tr>
		</thead>
	-->
	</table>

	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				url: 'ajax_padron.php',
				type: 'POST',
				data: { 'parametro': 'test_columns' }, // A parameter to differentiate the request
				success: function(response) {
					var columnNames = [];
					var data = JSON.parse(response);

					// Assuming 'data.columns' is an array of column names returned from the server
					$.each(data.columns, function(index, value) {
						columnNames.push({ "data": value.data, "title": value.title });
					});
					$('#employeeTable').DataTable({
						"processing": true,
						"serverSide": true,
						"ajax": {
							"url": "ajax_padron.php",
							"data": {'parametro': 'test'},
							"type": "POST"
						},
						"columns": columnNames,
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
				}
			});
		});
	</script>
</body>
</html>