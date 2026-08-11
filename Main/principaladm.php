<?
include ("../Config/Conectar.inc");



include ("../Config/funciones.inc");
$id_institucion = $_SESSION["id_institucion"];
if ( $_SESSION["usu"] == "" )
{ echo "<h1>Problemas con el ingreso al sistema </h1></br>"; 
exit();
}
$usuario=$_SESSION["usu"];
$perf=$_SESSION["perfil"];

$squs = "SELECT descripcion FROM _seguridad_scriptxusuario s, _scripts p WHERE s.script=p.id AND s.usuario='$usuario'";
$uspuede = mysql_query($squs);
$autoriz="";
while($ds=mysql_fetch_object($uspuede)){
	  		$autoriz = $autoriz.$ds->descripcion."\n";
	  }
$autoriz=trim($autoriz);
?>
<HTML>
    <head>
    	
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <!-- Meta, title, CSS, favicons, etc. -->
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	    <title>OSPM | Padron </title>
	
	    <!-- Bootstrap -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	    <!-- Font Awesome -->
	    <!-- <link href="http://34.123.90.171/dashboard_sistema/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet"> -->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	    <!-- NProgress -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/nprogress/nprogress.css" rel="stylesheet">
	    <!-- iCheck -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
	    <!-- bootstrap-progressbar -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
	    <!-- JQVMap -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
	    <!-- bootstrap-daterangepicker -->
	    <link href="http://34.123.90.171/dashboard_sistema/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
	
	    <!-- Custom Theme Style -->
	    <!-- <link href="http://34.123.90.171/dashboard_sistema/build/css/custom.css" rel="stylesheet"> -->
	    
	    <!--Tabla responsive -->
	    <link href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css" rel="stylesheet" />
	    
	    
	    <!-- Grafico nuevo GOOGLE -->
	    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.2/raphael-min.js"></script>
		
		<script src="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.js"></script>
		
	    <link rel="stylesheet" href="http://34.123.90.171/dashboard_sistema/vendors/morris.js/morris.css">
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/morris.js/morris.min.js"></script>      
	    <!-- <script src="../vendor/example.js"></script> -->
	  	<!-- <link rel="stylesheet" href="../vendor/example.css"> -->
	  	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.min.css">
	  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    	
    	<!--<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>		
		<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
		<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">		
		<link rel="stylesheet" href="principal.css" />
		<script src="principal.js"></script> -->
        <script>
        
        	$(document).ready(function(){
        		
        		$('#liquidacion_por_periodo').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
  				
				$.getJSON('http://34.123.90.171/php-bin/ws/ws_oseiv.php', 
							 { parametro: "liquidacion_por_periodo" },
					         function(datos){	
								
								$('#liquidacion_por_periodo').html("");
								
								Morris.Line({
								  element: 'liquidacion_por_periodo',
								  data: datos,								  
								  xkey: 'periodo',
								  ykeys: ['rsm','misiones','gcg','oseiv','medvisur','ensalud','no_hay','salud_total','su_salud','valmed','anticipo','s'],
							  	  labels: ['RSM','Misiones','GCG','OSEIV','Medvisur','Ensalud','no_hay','Salud_total','Su_salud','Valmed','Anticipo','S'],			  
								  stacked: true,
								  hideHover: 'auto',
				          		  resize: true
								});
								
				});
								
				$('#cuiles_por_periodo').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
    				
			    $.getJSON('http://34.123.90.171/php-bin/ws/ws_oseiv.php', 
							 { parametro: "cuiles_por_periodo"},
					         function(datos){	
								
								$('#cuiles_por_periodo').html("");
								
								Morris.Line({
								  element: 'cuiles_por_periodo',
								  data: datos,								  
								  xkey: 'periodo',
								  ykeys: ['rsm','misiones','gcg','oseiv','medvisur','ensalud','no_hay','salud_total','su_salud','valmed','anticipo','s'],
							  	  labels: ['RSM','Misiones','GCG','OSEIV','Medvisur','Ensalud','no_hay','Salud_total','Su_salud','Valmed','Anticipo','S'],				  
								  stacked: true,
								  hideHover: 'auto',
				          		  resize: true
								});
																
								/*Morris.Bar({
									  element: 'cuiles_por_periodo',
									  data: datos,
									  xkey: 'periodo',
									  ykeys: ['rsm','misiones','gcg','oseiv','medvisur','ensalud','no_hay','salud_total','su_salud','valmed','anticipo','s'],
							  		  labels: ['RSM','Misiones','GCG','OSEIV','Medvisur','Ensalud','no_hay','Salud_total','Su_salud','Valmed','Anticipo','S'],
									  barColors: function (row, series, type) {
									    if (type === 'bar') {
									      var red = Math.ceil(255 * row.y / this.ymax);
									      return 'rgb(' + red + ',0,0)';
									    }
									    else {
									      return '#000';
									    }
									  },
									  hideHover: 'auto',
			          				  resize: true
								}); */
								
				});
				
				
        	})
        
        	function cerrar_sesion(id){
        		
        		window.parent.location=id;        		
        		
        	}
        </script>
        <style>
        	body{
        		background-color: #F7F7F7;
        	}
        	.x_panel{
        		
			    width: 100%;			    
			    padding: 10px 17px;
			    display: inline-block;
			    background: #fff;
			    border: 1px solid #E6E9ED;
			    margin-left: 10px;
			    margin-top: 10px;
			    margin-bottom: 10px;
			    
        	}
        </style>
    </head>
    <BODY>    	
        <div class="container-fluid main">
        	<div class="header">
        		<br>
		        Este sistema fue desarrollado SMADM S.A.	       
		        <br />
		        Contacto: sistemas@smadm.com	        
		        <br /><br />
		        Estas conectado como el usuario: <?php echo $usuario; ?>
		        <br><br>	        	
		        	<a href="#" class="btn btn-warning" onclick="cerrar_sesion('salir.php');"><i class="fa fa-sign-out" aria-hidden="true"></i> Click aqui para cerrar sesion</a>
		        <br><br>
	        </div>
	        <div class="row col-md-12 col-lg-12">
	        	<div class="col-md-6 col-lg-6">
	        		<div class="x_panel">
			  			<div class="panel-heading">
	                        <h3><i class="fa fa-bar-chart-o fa-fw"></i> Liquidaciones por periodo <small>Año actual</small></h3>                        
	                    </div>  
						<div id="liquidacion_por_periodo"></div>
					</div>
	        	</div>
	        	<div class="col-md-6 col-lg-6">
	        		<div class="x_panel">
			  			<div class="panel-heading">
	                        <h3><i class="fa fa-bar-chart-o fa-fw"></i> Cuiles por periodo <small>Por meses</small></h3>  
	                        <!-- <div class="pull-left">
		                        <div class="btn-group">
		                            <div style="background-color: white; border-bottom: white;" class="panel-heading pull-right">
			                            <select id="lpd_periodo" class="form-control" style="background-color: #D73814; color: white;">
			                            	<option value="201709">Septiembre 2017</option>
			                            	<option value="201708">Agosto 2017</option>
			                            	<option value="201707" selected>Julio 2017</option>
			                            	<option value="201706">Junio 2017</option>
			                            	<option value="201705">Mayo 2017</option>
			                            	<option value="201704">Abril 2017</option>
			                            </select>
			                        </div>
		                        </div>
		                    </div> -->                         
	                    </div>  
						<div id="cuiles_por_periodo"></div>
					</div>
	        	</div>
		        
	    	</div>	    	
    	</div>
    	
    	<!-- jQuery -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/jquery/dist/jquery.min.js"></script>
	    <!-- Bootstrap -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
	    <!-- FastClick -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/fastclick/lib/fastclick.js"></script>
	    <!-- NProgress -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/nprogress/nprogress.js"></script>
	    <!-- Chart.js -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Chart.js/dist/Chart.min.js"></script>
	    <!-- gauge.js -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/gauge.js/dist/gauge.min.js"></script>
	    <!-- bootstrap-progressbar -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
	    <!-- iCheck -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/iCheck/icheck.min.js"></script>
	    <!-- Skycons -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/skycons/skycons.js"></script>
	    <!-- Flot -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Flot/jquery.flot.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Flot/jquery.flot.pie.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Flot/jquery.flot.time.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Flot/jquery.flot.stack.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/Flot/jquery.flot.resize.js"></script>
	    <!-- Flot plugins -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/flot.curvedlines/curvedLines.js"></script>
	    <!-- DateJS -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/DateJS/build/date.js"></script>
	    <!-- JQVMap -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/jqvmap/dist/jquery.vmap.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
	    <!-- bootstrap-daterangepicker -->
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/moment/min/moment.min.js"></script>
	    <script src="http://34.123.90.171/dashboard_sistema/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
	
	    <!-- Custom Theme Scripts -->
	    <script src="http://34.123.90.171/dashboard_sistema/build/js/custom.min.js"></script>
		
		<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
		<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    	
    	<!--<script src='https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js'></script>
		<script src='https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js'></script> -->
    </BODY>
</HTML>

