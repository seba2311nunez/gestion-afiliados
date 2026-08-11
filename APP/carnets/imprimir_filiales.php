<?php 
$date = new DateTime();


?>

<!DOCTYPE html>
<html>
<head>
	<!-- Jquery -->
	<script src="../agenda_archivos/jquery.min.js"></script>
	
	<!-- Bootstrap -->
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	<!-- Estilos propios -->
	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
	<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>

	<title>Imprimir Carnets p/ filiales</title>
</head>
<body>
	<div class="container">
		<div class="x_panel">
			<div class="tituloDiv">
				Imprimir Carnets por Filial
			</div>
			<hr>
			<select id="seccionales">
				<option value="">Seleccione...</option>
			</select>
			<hr>
			<input type="number" id="cuit" / placeholder="CUIT">
			<hr>
			<a id="btnDescargar" class="btn btn-success" target="_blank">
				Descargar
			</a>
		</div>
	</div>	
</body>

<script>
	$(function(){

		$.ajax({
			url: '../ver_grupo_familiar/ajax_selects.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'seccional'},
		})
		.done(function(data){			
			for(var i=0; i<=data.length-1 ;i++){
				let {seccional,cod_filial} = data[i];
				$('#seccionales').append(`<option value='${cod_filial}'>${seccional}</option>`);
			}
		});		

		$("#btnDescargar").on('click',function(e){

			e.preventDefault();
			let filial = $('#seccionales').val();
			let cuit = $('#cuit').val();
			if(filial){
				$(this).attr('disabled','disabled');
				$(this).html('');					
				$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
				let respuesta = prompt("Verifique la fecha de vencimiento.", "YYYY-MM-DD");
				let url = "imprimir_carnets.php?sl=filiales&fv="+respuesta+"&filial="+filial+"&cuit="+cuit;
				window.open(url);
			}			
		})

	})

	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}

</script>
</html>