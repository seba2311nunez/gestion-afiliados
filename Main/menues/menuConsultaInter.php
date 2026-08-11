<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include('../../Config/init.inc');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo INST_NAME_F; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery -->
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    
    <!-- jQuery DataTables -->
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.0/css/jquery.dataTables.min.css">

    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- BOOTSTRAP, Popper.js, and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <!-- ESTILO ESTANDAR --> 
  	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">

  	<!-- FONT AWESOME ICONS -->
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

  	<!-- ESTILO LOCAL  -->
	<link rel="stylesheet" href="../../../style.css">
</head>
<body>
	<div>
		<div class="list-group">
			<div style="background-color: #343a40;">
				<p style="color: white; font-size: 20px; margin: revert;  padding-left: 20px;">
					<?php echo INST_NAME_F; ?>					
				</p>
			</div>
			<span class="list-group-item active bg-dark mt-1">Perfil:<b class='ml-2' id="perfil_activo"></b></span>
			<a href='../../APP/buscar_afiliado/index.php' class='list-group-item list-group-item-action list-group-item-dark darker' target="bottomFrame">
				<p class='mb-0 text-light'>
					<i class="fas fa-lg fa-user mr-2"></i>
					Afiliados
				</p>
			</a>
	        <a href='../../APP/listados/descargar_gerenciadora.php' class='list-group-item sub-item list-group-item-dark darker' target="bottomFrame">
	            <p class='mb-0 text-light'>
	                <i class="fas fa-lg fa-notes-medical mr-2"></i>
	                Padron 
	            </p>
	        </a>
		</div>
	</div>
	<footer>
		<a class="btn btn-dark btn-disconnect text-light" onclick="cerrar_sesion();">
			<i class="fas fa-lg fa-pull-left"></i> Cerrar sesion
		</a>	
	</footer>
	
</body>
<script>
	const perfil= "<?php echo $_SESSION['perfil']; ?>";

	$('#perfil_activo').html(perfil).css('textTransform', 'capitalize');;
	function cerrar_sesion(){
		parent.window.location.href='../salir.php';
	}
</script>
</html>
