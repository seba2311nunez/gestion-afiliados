<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Descargar Notificaciones de Legajos subidos</title>
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

	<!-- BOOTSTRAP, Popper.js, and jQuery -->
	<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

	<!-- Databatables -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

	<!-- Select clase -->
	<link href="http://93.188.164.97/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
	<script src="http://93.188.164.97/framework/bootstrap/select/select2.min.js"></script>

	<style>
		body{
			background-color: #454d55;
			font-size:11px;
		}
		/* width */
		::-webkit-scrollbar {
		width: 10px;
		}

		/* Track */
		::-webkit-scrollbar-track {
		background: #f1f1f1; 
		}

		/* Handle */
		::-webkit-scrollbar-thumb {
		background: #888; 
		}

		/* Handle on hover */
		::-webkit-scrollbar-thumb:hover {
		background: #555; 
		}
		.myLi{
			margin-left:15px;
			margin-right:15px;
		}
		.table-container{
			height:480px;
			overflow: scroll;
			overflow-x: hidden;
			width: 100%;
		}
		.mySubmit{
			padding-left: 35px;
			padding-top: 10px;
		}
		.mySubmit a{
			width:150px;
		}
		th{
			position: sticky;
  			top: 0;
  			background: #212529;
  			box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
		}
		.prestadores{
			width: 20%;
		}
		.select2{

			width: 97%;
		}
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container-fluid">
	   	<div class="navbar-header" style="margin-right: 25px;">
	      <a class="navbar-brand" href="#">Descargar Notificaciones de Legajos subidos</a>
	    </div>
	    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
		    	<span class="navbar-toggler-icon"></span>
	  	</button>
  		<div class="collapse navbar-collapse" id="navbarNavDropdown">
		    	<li class="myLi navbar-brand">
		            <select id="year" class="form-control">
		            	<option value="2025">2025</option>
		            	<option value="2026">2026</option>
		            </select>
		    	</li>
		    	<li class="myLi navbar-brand">
		    		<input type="date" name="fecha_desde" id="fecha_desde" value="<?=date('Y-m-d'); ?>" class="form-control input-sm" min="1995-01-01" max="2049-12-31" required />
		    	</li>
		      	<li class="myLi navbar-brand">
		    		<input type="date" name="fecha_hasta" id="fecha_hasta" value="<?=date('Y-m-d'); ?>" class="form-control input-sm" min="1995-01-01" max="2049-12-31" required />
		    	</li>
		    	<li class="myLi navbar-brand">
		            <select id="clausula_usuarios" class="form-control">
		            	<option value="Todos">Todos</option>
		            	<option value="solo_ei">Equipo Interdisciplinario</option>
		            	<option value="no_ei">Redes</option>
		            </select>
		    	</li>
		    	<li class="myLi navbar-brand">
		    		<a class="btn btn-success btn-xs text-light" id='listar' name="listar">Listar</a>
		    	</li>
			</div>
		</div>
	</nav>
</body>
<script>		
	const INST_NAME = "<?php echo INST_NAME;?>";
	$(function(){
		$('#listar').on('click',function(e){
			e.preventDefault();
			let parametro = 'legajos_subidos';
			let year = $('#year').val();

			let fecha_desde = $('#fecha_desde').val();
			let fecha_hasta = $('#fecha_hasta').val();
			let clausula_usuarios = $('#clausula_usuarios').val();
			if(!year || !fecha_desde || !fecha_hasta || !clausula_usuarios) return false;
			window.open(`ajax.php?parametro=${parametro}&year=${year}&fecha_desde=${fecha_desde}&fecha_hasta=${fecha_hasta}&clausula_usuarios=${clausula_usuarios}`);
		});			
	});	

	function currencyFormat(num) {
	  return (
	    num
	      .toFixed(2) // always two decimal digits
	      .replace('.', ',') // replace decimal point character with ,
	      .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
	  ) // use . as a separator
	}
	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}
</script>
</html>
