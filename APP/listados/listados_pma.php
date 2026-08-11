<?
include (__DIR__."/../../Config/Conectar.inc");
include (__DIR__."/../../Config/funciones.inc");

$id_institucion = $_SESSION["id_institucion"];
$id_pplan = $_SESSION["id_pplan"];

if ( $_SESSION["usu"] == "" )
{ echo "<h1>Problemas con el ingreso al sistema </h1></br>"; 
exit();
}
$usuario=$_SESSION["usu"];
$id_user=$_SESSION["iduser"];
?>
<html>
  <head>
  	<title>Listados PMA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>		
		<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
		<link rel="stylesheet" href="principal.css" />
		<script src="principal.js"></script>
		<style type="text/css">
			.nderecha{
				text-align: right;
			}
			#tab_factIngresadasMensual tbody tr td{
				background-color: #b3b3b3;
				color: black;
			}
			#tab_factIngresadasMensual thead tr th{
				background-color: #cccccc;
			}
			#tabEstadisticaTrapasos tbody tr td {
				text-align: right;
			}
			.btn-disconnect{
				position: fixed;
				top:1%; 
				right:1%;
				z-index: 1;
			}
			.btnDescargaXlsCapitas{
				cursor: pointer;
			}
			#bottomDiv {
		    position: fixed;
		    left: 0;
		    bottom: 0;
		    width: 100%;
		    background-color: #333;
		    color: #fff;
		    padding: 10px;
		    text-align: center;
		}
		</style>
    <script>
      $(document).ready(function(){
	      		// $('#actividad_sistema').DataTable( {
				    // fixedHeader: true
				// });
  		});
    	function cerrar_sesion(id){
				window.parent.location=id;
			}
			function cerrar_sesion_volver(id){
				window.parent.location=id;								
			}
    </script>
  </head>
  <body>    	
    <div class="container-fluid main">
	  	<div class="row">    
  			<div class="col-md-12">
          	<div class="x_panel">
							<h4><i class="fas fa-chart-bar"></i> Listados de PMA</h4>
							<div id="listados_pma"></div>			
						</div>	
					</div>
				</div> 
			</div>
		</div>
  </body>
	<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
	<script src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script>
	<script>
		const DOMINIO = "<?echo DOMINIO;?>";
		const INST_NAME = "<?echo INST_NAME;?>";
		$(function(){
		  ListadosPMA();
		});

		function ListadosPMA() {
	    $('#listados_pma').html("");
	    $.ajax({
        url: 'ajax_padron.php',
        type: 'GET',
        dataType: 'json',
        data: { parametro: 'lst_listados_pma' },
	    }).done(function(data) {
        console.table(data);

        $('#listados_pma').append('<ul class="list-group">');
        for (var i = 0; i < data.length; i++) {
          
          var listItem = `<a href="../../${data[i]['url']}" class="list-group-item">${data[i]['nombre']}<span class="badge">Ir</span></a>`;
          $('#listados_pma').append(listItem);
        }
        $('#listados_pma').append("</ul>");
	    });
		}
	</script>
</html>
