<?php 
$date = new DateTime();


?>

<!DOCTYPE html>
<html>
<head>
	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	<!-- Estilos propios -->
    <link href="../../Lib/estilos_propios/estilo_estandar.css" rel="stylesheet">
    <script src="../../Lib/estilos_propios/estilo_estandar.js"></script>

    <!-- Select clase -->    
    <link href="../../Lib/select2/select2.min.css" rel="stylesheet">
    <script src="../../Lib/select2/select2.min.js"></script>

	<title>Reporte de altas y bajas.</title>
</head>
<body>
	<button id="btnToggleMenu" class="btn btn-primary" onclick="parent.toggleMenu()" title="Mostrar/ocultar menú">
	  ☰
	</button>
	<div class="container-fluid">
		<div class="x_panel">
			<div class="tituloDiv">
				Reporte general de altas y bajas
			</div>
			<hr>
			<div class="row">
			  <div class="col-md-3">
			    <b>Tipo de fecha</b>
			    <select id="tipo_fecha" class="form-control input-sm">
			      <option value="fecha_aPartir">Fecha alta/baja</option>
			      <option value="fechador">Fecha incorporacion</option>			          
			      <option value="fechador_aprobacion">Fecha aprobacion</option>
			    </select>
			  </div>	
			  <div class="col-md-3">
			    <b>Fecha Desde</b>
			    <input type="date" id="fecha_desde" class="form-control" value="<?=date('Y-m-d');?>" style="width: 200px;" />
			  </div>	
			  <div class="col-md-3">
			    <b>Fecha Hasta</b>
			    <input type="date" id="fecha_hasta" class="form-control" value="<?=date('Y-m-d');?>" style="width: 200px;" />
			  </div>	
			  <div class="col-md-3">
					<b>Aprobacion</b>
					<select class="form-control" id="aprobados">
						<option value="">Todos</option>
						<option value="aprobados">Aprobados</option>
						<option value="no_aprobados">No aprobados</option>
					</select>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-3">
				    <b>Estado alta/baja</b>
				    <select id="tipo_movimiento">			        	
				    	<option value="">Todos</option>
				    	<option value="alta">Alta</option>
				    	<option value="baja">Baja</option>
				    </select>
				</div>
				<div class="col-md-7">
					<b>Descripcion cambio estado alta/baja</b>
			        <!-- <select id="motivo_descripcion">			        					        	
			        </select> -->
			        <br>
			        <select id="motivo_descripcion" name="motivo_descripcion[]" class="form-control select2" style="width:100%"></select>

				</div>
				
			</div>
			
			<div class="float-start" role="group" aria-label="Acciones">
				<hr>
		        <a id="btnVisualizarPantalla" class="btn btn-info btn-sm" >
		         <i class="fas fa-list"></i> Listar
		        </a>
		        <a id="btnDescargar" class="btn btn-success btn-sm" target="_blank">
		          <i class="fas fa-file-excel"></i> Excel
		        </a>	
		        <a id="btnEstadisticas" class="btn btn-warning btn-sm"><i class="fas fa-chart-line"></i>Estadisticas</a>
		        <button class="btn btn-primary btn-sm" id="btnAprobar">
		        	<i class="far fa-check-square"></i> Aprobar seleccionados
		        </button>	
			</div>
			<hr>
			<hr>
			<!-- Navs -->
			<ul class="nav nav-tabs" id="tabMovimientos" role="tablist">
			  <li class="nav-item">
			    <a class="nav-link active" id="filtros-tab" data-toggle="tab" href="#filtros" role="tab" aria-controls="filtros" aria-selected="true">Listado</a>
			  </li>
			  <li class="nav-item">
			    <a class="nav-link" id="graficos-tab" data-toggle="tab" href="#graficos" role="tab" aria-controls="graficos" aria-selected="false">Estadisticas</a>
			  </li>
			</ul>

			<div class="tab-content" id="tabMovimientosContent">
			  <!-- Tab Filtros -->
			  <hr>
			  <div class="tab-pane fade show active" id="filtros" role="tabpanel" aria-labelledby="filtros-tab">
			    <!-- Aquí colocás TODO el contenido que ya tenías en x_panel -->
			    <!-- Desde <div class="row">...</div> hasta el último botón -->			    
			    
			    <hr>
			    
			    <div class="row">
			    	<br><hr>
			    	<table class="table" id="tabListadoGeneral">
			    		<thead>
			    			<tr>
			    				<th></th>
			    				<th>
						          <input type="checkbox" id="checkAll">
						        </th>
						        <th>Aprobado</th>
			    				<th>Estado</th>
			    				<th>Descripcion</th>
			    				<th>Fecha alta/baja</th>
			    				<th>Cuil</th>
			    				<th>Afiliado</th>
			    				<th>Procedencia</th>
			    				<th>Parentesco</th>
			    				<th>Usuario</th>
			    				<th>Fecha incorporacion</th>
			    				<th>Fecha aprobacion</th>
			    			</tr>
			    		</thead>
			    		<tbody></tbody>
			    	</table>
			    	<br>
			    	
			    	<br>
  					
			    </div>
			  </div>

			  <!-- Tab Gráficos -->
			  <div class="tab-pane fade" id="graficos" role="tabpanel" aria-labelledby="graficos-tab" style="margin-top: 20px;">
			  	<hr>
			  	<div class="row">
			  		<hr>
			  		<div class="col-md-6">
			  			<div  style=" margin-bottom: 50px;">
					      <p>Altas y bajas por periodo.</p>
					      <hr>
					      <!-- Ejemplo básico -->
					      <canvas id="grafico1" width="400" height="200"></canvas>
					    </div>
			  		</div>
			  		<div class="col-md-6">
			  			<div  style=" margin-bottom: 50px;">
					      <p>Altas y bajas por dia.</p>
					      <hr>
					      <!-- Ejemplo básico -->
					      <canvas id="grafico2" width="400" height="200"></canvas>
					    </div>
			  		</div>
			  		<hr>
			  	</div>
			    
			  </div>
			  <hr>
			</div>

		</div>		
	</div>	
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	let graficoAltasBajasPorPeriodo;
	let graficoAltasBajas;

	$(function(){	

		setTimeout(function(){ 
			// $('#motivo_descripcion').select2({
		    // 	width: '320px'
			// });
			llena_select_eventos_afiliados();
			
		}, 3000);

		
		$("#btnVisualizarPantalla").on('click',function(){

			genera_listado_con_filtros();			
					
		})

		$("#btnEstadisticas").on('click',function(){
			cargarGraficoAltasBajas();
		})

		$("#btnDescargar").on('click',function(e){

			e.preventDefault();
			
			$(this).attr('disabled','disabled');
			$(this).html('');					
			$(this).html('<i class="fas fa-sync-alt fa-spin"></i> Procesando');
			
			DescargarExcel();			
					
		})

		$('#tipo_movimiento').on('change', function() {

		    var tipo_movimiento = $(this).val();

		    // Destruir select2 si ya fue inicializado
		    if ($("#motivo_descripcion").hasClass("select2-hidden-accessible")) {
		        $("#motivo_descripcion").select2('destroy');
		    }

		    // Limpiar el select completamente
		    $("#motivo_descripcion").empty();

		    // Agregar el atributo "multiple" (si no lo tenía)
		    $("#motivo_descripcion").attr("multiple", "multiple");

		    // Cargar nuevas opciones según el tipo de movimiento
		    $.getJSON('ajax.php', { 
		        parametro: "motivos_descripcion", 		        
		        me_movimiento: tipo_movimiento 
		    }, function(datos) {



		        // Recorrer y cargar options
		        $.each(datos, function (key, item) {
		            $("#motivo_descripcion").append("<option value='"+item.id+"'>"+item.motivo_descripcion+"</option>");
		        });

		        // Inicializar nuevamente como select2 múltiple
		        $("#motivo_descripcion").select2({
		            placeholder: "Seleccione uno o varios motivos",
		            width: '600px',
		            allowClear: true
		        });

		        //$("#motivo_descripcion").prepend("<option value=''>Seleccione uno o varios elementos</option>");

		    });

		});


		$('.selectores-multiples').on('change', function() {
	        var firstOption = $('option[value=""]', this);
	        var selectedOptions = $('option:selected', this);

	        if (selectedOptions.length > 1) {
	            // Deselect the first option if it is selected along with others
	            firstOption.prop('selected', false);
	        }
	    });

	    // Check general
		$(document).on('change', '#checkAll', function() {
		  $('.chk-aprobar').prop('checked', this.checked);
		});

		// Botón aprobar
		$(document).on('click', '#btnAprobar', function() {
		  let aprobados = [];

		  $('.chk-aprobar:checked').each(function() {
		    aprobados.push({
		      id_tabla: $(this).data('id_tabla'),
		      tabla_nombre: $(this).data('tabla_nombre')
		    });
		  });

		  if (aprobados.length === 0) {
		    alert("No seleccionaste ningún registro para aprobar.");
		    return;
		  }

		  //console.log(aprobados);return false;

		  // Enviar por GET JSON a ajax.php
		  $.getJSON('ajax.php', {
		    parametro: "aprobar_seleccionados",
		    registros: JSON.stringify(aprobados)
		  }, function(response) {
		    alert("Registros aprobados correctamente.");
		    genera_listado_con_filtros(); // Recargar tabla si querés
		  });
		});


	})

	function genera_listado_con_filtros() {
		$("#tabListadoGeneral tbody").html("<tr><td colspan='9'><h3>Cargando <i class='fas fa-spinner fa-spin'></i></h3></td></tr>");

		$.getJSON('ajax.php', {
		  parametro: "listado_principal",
		  tipo_fecha: $("#tipo_fecha").val(),
		  fdesde: $("#fecha_desde").val(),
		  fhasta: $("#fecha_hasta").val(),
		  tipo_movimiento: $("#tipo_movimiento").val(),
		  motivo_descripcion: $("#motivo_descripcion").val(),
		  aprobados: $("#aprobados").val()
		},
		function(data) {
		  $("#tabListadoGeneral tbody").html("");

		  let aprobacion_mostrar = aprobacion_mostrar_td = "";



		  for (let i = 0; i < data.length; i++) {

		  	if(data[i]['aprobacion_historico']==0){
			  	aprobacion_mostrar = "<input type='checkbox' class='chk-aprobar form-check-input' data-id_tabla='" + data[i]['id_tabla'] + "' data-tabla_nombre='" + data[i]['tabla_nombre'] + "' style='margin-left: 1px;'>";
				aprobacion_mostrar_td = "NO";
			}
			else{
			  	aprobacion_mostrar = " <p title='"+data[i]['fechador_aprobacion']+"''>"+data[i]['usu_aprobacion']+"</p> ";
			  	aprobacion_mostrar_td = "SI";
			}

		    $("#tabListadoGeneral tbody").append("<tr>" +
		      "<td>" + (i+1) + "</td>" +
		      "<td>"
		      +aprobacion_mostrar
		      +"</td>" +
		      "<td>" + aprobacion_mostrar_td + "</td>" +
		      "<td>" + data[i]['estado_mov'] + "</td>" +
		      "<td>" + data[i]['evento_descripcion'] + "</td>" +
		      "<td>" + data[i]['fecha_aPartir_f'] + "</td>" +
		      "<td>" + data[i]['cuil'] + "</td>" +
		      "<td>" + data[i]['apellido'] + " " + data[i]['nombre'] + "</td>" +
		      "<td>" + data[i]['tabla_nombre'] + "</td>" +
		      "<td>" + data[i]['parentesco'] + "</td>" +
		      "<td>" + data[i]['usuario'] + "</td>" +
		      "<td>" + data[i]['fechador_f'] + "</td>" +
		      "<td>" + data[i]['fechador_aprobacion_f'] + "</td>" +
		      "</tr>");
		  }
		});
	}

	function llena_select_eventos_afiliados(){

		// Destruir select2 si ya fue inicializado
	    if ($("#motivo_descripcion").hasClass("select2-hidden-accessible")) {
	        $("#motivo_descripcion").select2('destroy');
	    }

	    // Limpiar el select completamente
	    $("#motivo_descripcion").empty();

	    // Agregar el atributo "multiple" (si no lo tenía)
	    $("#motivo_descripcion").attr("multiple", "multiple");

		// Cargar nuevas opciones según el tipo de movimiento
	    $.getJSON('ajax.php', { 
	        parametro: "motivos_descripcion", 		        
	        me_movimiento: $("#tipo_movimiento").val() 
	    }, function(datos) {



	        // Recorrer y cargar options
	        $.each(datos, function (key, item) {
	            $("#motivo_descripcion").append("<option value='"+item.id+"'>"+item.motivo_descripcion+"</option>");
	        });

	        // Inicializar nuevamente como select2 múltiple
	        $("#motivo_descripcion").select2({
	            placeholder: "Seleccione uno o varios motivos",
	            width: '600px',
	            allowClear: true
	        });

	        //$("#motivo_descripcion").prepend("<option value=''>Seleccione uno o varios elementos</option>");

	    });
	}

	function genera_listado_con_filtros2(){

		$("#tabListadoGeneral tbody").html("<h3>Cargando <i class='fas fa-spinner fa-spin'></i></h3>");

		$.getJSON('ajax.php',
					{ parametro: "listado_principal", 
						tipo_fecha: $("#tipo_fecha").val(),
						fdesde: $("#fecha_desde").val(),
						fhasta: $("#fecha_hasta").val(),
						tipo_movimiento: $("#tipo_movimiento").val(),
						motivo_descripcion: $("#motivo_descripcion").val() 
					},						       				
					function(data){ 

						$("#tabListadoGeneral tbody").html("");
						
						for(var i=0; i<=data.length ;i++){
						
							$("#tabListadoGeneral tbody").append("<tr>"																
											+"<td>"+data[i]['estado_mov']+"</td>"
											+"<td>"+data[i]['evento_descripcion']+"</td>"
											+"<td>"+data[i]['fecha_aPartir']+"</td>"
											+"<td>"+data[i]['apellido']+" "+data[i]['nombre']+"</td>"
											+"<td>"+data[i]['procedencia']+"</td>"
											+"<td>"+data[i]['parentesco']+"</td>"
											+"<td>"+data[i]['usuario']+"</td>"
											+"<td>"+data[i]['fechador']+"</td>"
																										      				
										+"</tr>") ;		
						}	
					}//fin function data

		);//fin getjson

	}

	function cargarGraficoAltasBajas() {
		let tipo_fecha = $('#tipo_fecha').val(); // 'estado' o 'carga'
		//let rango = $('#rango_fecha').val();
		let desde = $('#fecha_desde').val();
		let hasta = $('#fecha_hasta').val();

		//desde = '2025-03-18';
		//hasta = '2025-03-25';

		// if (rango !== 'personalizado') {
		// 	desde = '';
		// 	hasta = '';
		// }

		$.ajax({
			url: 'ajax.php',
			data: {
			parametro: 'altas_bajas_por_periodo',
			tipo_fecha: tipo_fecha,
			//rango: rango,
			desde: desde,
			hasta: hasta
			},
			dataType: 'json',
			success: function(data) {
				let labels = data.map(e => e.periodo);
				let altas = data.map(e => e.altas);
				let bajas = data.map(e => e.bajas);

				if (graficoAltasBajasPorPeriodo) graficoAltasBajasPorPeriodo.destroy();
				

				const ctx = document.getElementById('grafico1').getContext('2d');
				graficoAltasBajasPorPeriodo = new Chart(ctx, {
					type: 'line',
					data: {
					  labels: labels,
					  datasets: [
					    {
					      label: 'Altas',
					      data: altas,
					      borderColor: '#28a745',
					      backgroundColor: 'rgba(40, 167, 69, 0.1)',
					      fill: false,
					      tension: 0.3
					    },
					    {
					      label: 'Bajas',
					      data: bajas,
					      borderColor: '#dc3545',
					      backgroundColor: 'rgba(220, 53, 69, 0.1)',
					      fill: false,
					      tension: 0.3
					    }
					  ]
					},
					options: {
					  responsive: true,
					  scales: {
					    y: {
					      beginAtZero: true
					    }
					  }
					}
				});

			}
		});//Final ajax

		$.ajax({
			url: 'ajax.php',
			data: {
			parametro: 'altas_bajas_diarias',
			tipo_fecha: tipo_fecha,
			//rango: rango,
			desde: desde,
			hasta: hasta
			},
			dataType: 'json',
			success: function(data) {
				let labels = data.map(e => e.fecha_format);
				let altas = data.map(e => e.altas);
				let bajas = data.map(e => e.bajas);

				
				if (graficoAltasBajas) graficoAltasBajas.destroy();

				const ctx = document.getElementById('grafico2').getContext('2d');
				graficoAltasBajas = new Chart(ctx, {
					type: 'line',
					data: {
					  labels: labels,
					  datasets: [
					    {
					      label: 'Altas',
					      data: altas,
					      borderColor: '#28a745',
					      backgroundColor: 'rgba(40, 167, 69, 0.1)',
					      fill: false,
					      tension: 0.3
					    },
					    {
					      label: 'Bajas',
					      data: bajas,
					      borderColor: '#dc3545',
					      backgroundColor: 'rgba(220, 53, 69, 0.1)',
					      fill: false,
					      tension: 0.3
					    }
					  ]
					},
					options: {
					  responsive: true,
					  scales: {
					    y: {
					      beginAtZero: true
					    }
					  }
					}
				});

				
			}
		});//Final ajax
	}


	// $('#btnEstadisticas').on('click',function(){

    //     cargarGraficoAltasBajas();
    //     //$("#modalFiltros").hide();
    // })

	// $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	// 	if (e.target.id === 'graficos-tab') {
	// 		cargarGraficoAltasBajas();
	// 	}
	// });

	$('#rango_fecha').on('change', function () {
	$('.rango-personalizado').toggle($(this).val() === 'personalizado');
	});

	function DescargarExcel(){
		let parametro = "reporte_excel";
		let tipo_fecha = $("#tipo_fecha").val();
		let fdesde = $("#fecha_desde").val();
		let fhasta = $("#fecha_hasta").val();
		let tipo_movimiento = $("#tipo_movimiento").val();
		let motivo_descripcion = $("#motivo_descripcion").val();

		

		let url = `ajax.php?parametro=${parametro}&tipo_fecha=${tipo_fecha}&tipo_movimiento=${tipo_movimiento}&motivo_descripcion=${motivo_descripcion}&fdesde=${fdesde}&fhasta=${fhasta}`;

		// if($('#gerenciadoras').val()){
		// 	url = url.concat(`&gerenciadora=${$('#gerenciadoras').val()}`);
	    
	    // var opcionesSeleccionadas = $('#gerenciadoras').find('option:selected');
	    // var textoOpciones = opcionesSeleccionadas.map(function() {
	    //   return $(this).text();
	    // }).get().join(', ');
	    // url = url.concat(`&gerenciadora_nombre=${textoOpciones}`);
		// }

		console.log(url); //return false;

		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();

		$("#btnDescargar").removeAttr('disabled'); 
		$('#btnDescargar').html('Excel');	
	}
	function convertirCadenaANumeros(cadena) {
	  // Dividir la cadena en un array de subcadenas
	  var subcadenas = cadena.split(',');
	  
	  // Convertir las subcadenas en números
	  var numeros = subcadenas.map(function(item) {
	    return parseInt(item, 10);
	  });

	  return numeros;
	}
	function abrirEnPestana(url) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url;
		a.click();
	}

	// Se ejecuta al mostrar la pestaña de gráficos
	$('a[data-toggle="tabs"]').on('shown.bs.tab', function (e) {
		if (e.target.id === 'graficos-tab') {
		  var ctx = document.getElementById('grafico1').getContext('2d');
		  new Chart(ctx, {
		    type: 'bar',
		    data: {
		      labels: ['Altas', 'Bajas'],
		      datasets: [{
		        label: 'Cantidad',
		        data: [12, 19],
		        backgroundColor: ['#17a2b8', '#dc3545']
		      }]
		    }
		  });
		}
	});

</script>
</html>