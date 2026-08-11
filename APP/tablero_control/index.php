<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tablero Control</title>
	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	  
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	
	<!-- Iconos -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	
	<!-- Databatables -->
	<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	
	<!-- Estilos propios -->
	<link href="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.css" rel="stylesheet">
	<script src="http://34.123.90.171/framework/bootstrap/css/estilo_estandar.js"></script>

	<!-- Select clase -->
	<link href="http://45.132.242.129/framework/bootstrap/select/select2.min.css" rel="stylesheet" type="text/css">
	<script src="http://45.132.242.129/framework/bootstrap/select/select2.min.js"></script>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		.highlight {
		  background-color: #EEE;
		}
		#tabla_control tbody tr td{
			cursor:pointer;
			text-align: right;
		} 
		#tabla_control tbody tr td.no_clickeable{
			cursor:default;
		} 
		#tabla_control tbody tr td.periodo{
			font-weight: bold;
			text-align: left;
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="x_panel">
			<div class="tituloDiv">Tablero de Control</div>
			<div id="table-container">
				<table class="table" id="tabla_control">
					<thead>
						<tr>
							<th>Periodo</th>
							<th>Coef.</th>
							<th data-tipo="altas_rg">Altas RG</th>
							<th>Bajas RG</th>
							<th>Altas MT</th>
							<th>Bajas MT</th>
							<th>Bajas B15</th>
							<th>Desempleo</th>
							<th>Fallecidos</th>
							<th>Jubilados</th>
							<th>DDJJ</th>
							<th>Aportes</th>
						</tr>	
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" id="aux_grupo" />
	<div class="modal fade" id="modal-filtros-modal-1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Modal title</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<!--Modal Body-->
				<div class="modal-body">
					<div class="accordion" id="accordion-grupo-1">
						<!--Hidden Inputs-->
						<input type="hidden" id="modal-1-tipo-archivo" />
						<input type="hidden" id="modal-1-periodo">
						<!--Fin hidden inputs -->
						<!--Resumenes-->
					  <div class="card">
					    <div class="card-header" id="headingOne">
					      <h2 class="mb-0">
					        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					          Resumenes
					        </button>
					      </h2>
					    </div>
					    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion-grupo-1">
					      <div class="card-body">
					        <div class="form-check">
									  <input class="form-check-input radio-filtros" type="radio" name="TipoDescarga" id="resumen_cantidad_x_capita" value="cantidad_por_desreguladora">
									  <label class="form-check-label" for="flexRadioDefault1">
									    Cantidad por desreguladora
									  </label>
									</div>
									<!--
									<div class="form-check">
									  <input class="form-check-input radio-filtros" type="radio" name="TipoDescarga" id="resumen_test" value="test">
									  <label class="form-check-label" for="flexRadioDefault2">
									    Test
									  </label>
									</div>
									-->
					      </div>
					    </div>
					  </div>
					  <!--Fin Resumenes-->
					  <!--Detalles-->
					  <div class="card">
					    <div class="card-header" id="headingTwo">
					      <h2 class="mb-0">
					        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					         Detalles
					        </button>
					      </h2>
					    </div>
					    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-grupo-1">
					      <div class="card-body">
					        <div class="form-check">
									  <input class="form-check-input radio-filtros" type="radio" name="TipoDescarga" id="detalle_normal" value="detalle">
									  <label class="form-check-label" for="flexRadioDefault3">
									    Detalle normal
									  </label>
									</div>
					      </div>
					    </div>
					  </div>
					  <!--Fin Detalles-->
					</div>
				</div>
				<!--Fin Modal Body-->
				<!--Modal Footer-->
				<div class="modal-footer">
        <button type="button" class="btn btn-success" id="descargar-modal-1">Descargar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
				<!--Fin Modal Footer-->
			</div>
		</div>
	</div>
</body>
</html>
<script>
	$(function(){
		tipos_filtros = new Array("Altas RG","Bajas RG","Altas MT","Bajas MT","Bajas B15","Fallecidos","Jubilados");//Estos son clickeables (tendrian que poderse descargar)
		grupo1 = new Array("Altas RG","Bajas RG","Altas MT","Bajas MT");//Grupo 1 (Misma estructura de tablas)
		grupo2 = new Array("Bajas B15");
		grupo3 = new Array("Fallecidos","Jubilados");

		var grupo = 0;

		ListarTablero();

		$('#tabla_control').on('click','td:not(.no_clickeable)',function(event){
			var td = $(event.target).closest('td'), th = thFromTd(td), tr = td.parent();
			var periodo = $(tr).find("td:eq(0)").html();
			var tipo = th.html(), cantidad = td.html();

			console.log(periodo);
			console.log(tipo);
			console.log(cantidad);

			//Se denomina en "grupos" a los tipos de archivos que comparten misma estructura.

			if($.inArray(tipo,tipos_filtros) !== -1){//IF Abrir Modal

				if($.inArray(tipo,grupo1) !== -1){grupo = 1; var modal =1;}
				if($.inArray(tipo,grupo2) !== -1){grupo = 2; var modal =1;}
				if($.inArray(tipo,grupo3) !== -1){grupo = 3; var modal =1;}
				console.log(grupo+', '+modal);
				switch (modal){
					case 1://altas_mt / altas_rg / bajas_mt / bajas_rg

						$('#modal-1-tipo-archivo').val(tipo);
						$('#modal-1-periodo').val(periodo);
						$('#aux_grupo').val(grupo);
						$('#modal-filtros-modal-1 .modal-dialog .modal-content .modal-header .modal-title').html(tipo+" "+periodo);
						$('#modal-filtros-modal-1').modal('toggle');
					break;
				}
				
			}else{//ELSE ABRIR MODAL
				//window.open("control_xls.php?tipo="+tipo+"&periodo="+periodo);
			}//Fin Abrir modal
		})

		$(document).on('shown.bs.modal',function(event){
			console.log("Hola. Abriste un modal");
			$(".modal-dialog .modal-content .modal-body .accordion .card .collapse").removeClass('show');
			$('.radio-filtros').prop("checked",false);
			$('#descargar-modal-1').attr("disabled","disabled");
		})
		$('.radio-filtros').on('click',function(e){
			$('#descargar-modal-1').removeAttr('disabled');
		})
		$('#descargar-modal-1').on('click',function(e){
    	   	var tipo_descarga = $('input[name=TipoDescarga]:checked', '#modal-filtros-modal-1').val();
    	   	var periodo = $('#modal-1-periodo').val();
    	   	var tipo= $('#modal-1-tipo-archivo').val();
    	   	
    	   	console.log(tipo_descarga);
    	   	console.log(periodo);
    	   	console.log(tipo);

    	   	if(grupo === 0){
    	   		return false;
    	   	}else{
    	   		//console.log("control_xls.php?tipo="+tipo+"&periodo="+periodo+"&tipo_descarga="+tipo_descarga+"&grupo="+grupo);
				window.open("control_xls.php?tipo="+tipo+"&periodo="+periodo+"&tipo_descarga="+tipo_descarga+"&grupo="+grupo);
    	   	}

    })

	});

	function ListarTablero(){

		$.ajax({
			url: 'ajax.php',
			type: 'GET',
			dataType: 'json',
			data: {parametro: 'listar'},
		})
		.done(function(data) {
			console.table(data);
            for(var i=0; i<=data.length-1 ;i++){

            	$('#tabla_control tbody').append("<tr>"																
													+"<td class='no_clickeable periodo'>"
															+data[i]['periodo']
													+"</td>"
													+"<td>"
														+data[i]['coef']
													+"</td>"
													+"<td class='filtros'>"
														+data[i]['altas_rg']
													+"</td>"
													+"<td class='filtros'>"
														+data[i]['bajas_rg']
													+"</td>"
													+"<td class='filtros'>"
														+data[i]['altas_mt']
													+"</td>"
													+"<td class='filtros'>"
														+data[i]['bajas_mt']
													+"</td>"
													+"<td class='filtros'>"
														+data[i]['bajas_b15']
													+"</td>"
													+"<td>"
														+data[i]['desempleo']
													+"</td>"
													+"<td>"
														+data[i]['fallecidos']
													+"</td>"	
													+"<td>"
														+data[i]['jubilados']
													+"</td>"	
													+"<td>"
														+data[i]['ddjj']
													+"</td>"
													+"<td>"
														+data[i]['aportes']
													+"</td>"
												+"</tr>");
            }

            $('#tabla_control tbody tr td').each(function() {
			    var valor = $(this).html();
			    if(valor == "0") $(this).addClass('no_clickeable'); 
			 });
		});
	}
	function thFromTd(td) {
		var ofs = td.offset().left,
		    table = td.closest('table'),
		    thead = table.children('thead').eq(0),
		    positions = cacheThPositions(thead),
		    matches = positions.filter(function(eldata) {
		      return eldata.left <= ofs;
		    }),
		    match = matches[matches.length-1],
		    matchEl = $(match.el);
		return matchEl;
	}
	function cacheThPositions(thead) {
	    var data = thead.data('cached-pos'),
	        allth;
	    if (data)
	      return data;
	    allth = thead.children('tr').children('th');
	    data = allth.map(function() {
	      var th = $(this);
	      return {
	        el: this,
	        left: th.offset().left
	      };
	    }).toArray();
	    thead.data('cached-pos', data);
	    return data;
  	}
</script>