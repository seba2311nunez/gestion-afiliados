$(function() {
	
	/***Titulo con contadores***/
	
    $("#n_inter_actuales").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				     	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_inter_actuales" },						       				
   				function(data){ 
   					
   					$("#n_inter_actuales").html(data[0]['internados']);
   					$("#n_camas_totales").html(data[0]['camas']+"&nbsp; Camas Totales");
   					
					$("#n_inter_actuales").css('padding-left','30px');
   					
   				}
   	
   	); 
   	
   						
	$("#n_guardias").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_guardias" },						       				
   				function(data){ 
   					
   					$("#n_guardias").html(data[0]['total']);
   					$("#n_g_clinico").html(data[0]['clinico']+"&nbsp; Clinico <br>");
   					$("#n_g_pediatrico").html(data[0]['pediatria']+"&nbsp; Pediatrico <br>");
   					$("#n_g_obstetricia").html(data[0]['obstetricia']+"&nbsp; Obstetrico");
   					
					$("#n_guardias").css('padding-left','30px');
   					
   				}
   	
   	); 
	
	
	$("#guardias_internacion").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_guardias_internacion" },						       				
   				function(data){ 
   					
   					$("#guardias_internacion").html(data[0]['total']);
					$("#guardias_internacion").css('padding-left','30px');
   					
   				}
   	
   	); 
   	
   	
   	$("#n_egresos_mes").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_egresos_mes" },						       				
   				function(data){ 
   					
   					$("#n_egresos_mes").html(data[0]['total']);
   					$("#n_egresos_as").html(data[0]['alta_sanatorial']+"&nbsp; Alta Sanatorial<br>");
   					$("#n_egresos_d").html(data[0]['defuncion']+"&nbsp; Defuncion<br>");
   					$("#n_egresos_o").html(data[0]['otros']+"&nbsp; Otros<br>");
					$("#n_egresos_mes").css('padding-left','30px');
   					
   				}
   	
   	); 
   	
   	
   	$("#n_guardias_espera").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_guardias_espera" },						       				
   				function(data){ 
   					
   					$("#n_guardias_espera").html(data[0]['total']);
   					$("#n_ge_clinico").html(data[0]['clinico']+"&nbsp; Clinico <br>");
   					$("#n_ge_pediatrico").html(data[0]['pediatria']+"&nbsp; Pediatrico <br>");
   					$("#n_ge_obstetricia").html(data[0]['obstetricia']+"&nbsp; Obstetrico");
   					
					$("#n_guardias_espera").css('padding-left','30px');
   					
   				}
   	
   	); 
   	
   	
   	$("#n_ingreso_a_internacion").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_ingreso_a_internacion" },						       				
   				function(data){ 
   					
   					$("#n_ingreso_a_internacion").html(data[0]['total']);
   					$("#n_d_guardia").html(data[0]['guardia']+"&nbsp; Por Guardia <br>");
   					$("#n_d_derivacion").html(data[0]['derivacion']+"&nbsp; Por Derivacion <br>");
   					$("#n_d_otros").html(data[0]['otros']+"&nbsp; Otros <br>");   					       					
					$("#n_ingreso_a_internacion").css('padding-left','30px');
   					
   				}
   	
   	); 
   	
   	$("#n_cirugias").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_cirugias" },						       				
   				function(data){ 
   					
   					$("#n_cirugias").html(data[0]['total']);
   					$("#n_c_programada").html(data[0]['cirugias_internacion']+"&nbsp; Cirugias en Internacion <br>");
   					$("#n_c_ambulatoria").html(data[0]['cirugias_ambulatorio']+"&nbsp; Cirugias Ambulatorias <br>");
   					//$("#n_c_cirugias").html(data[0]['cirugias']+"&nbsp; Cirugias <br>");
   					$("#n_c_partos").html(data[0]['partos']+"&nbsp; Partos <br>");
   					$("#n_c_cesareas").html(data[0]['cesareas']+"&nbsp; Cesareas <br>");    					       					
					$("#n_cirugias").css('padding-left','30px');
   					
   				}
   	
   	); 
	
	$("#n_giro_cama").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	/*$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_giro_cama" },						       				
   				function(data){ 
   					
   					$("#n_giro_cama").html(data[0]['giro']);	       					
					$("#n_giro_cama").css('padding-left','30px');
   					
   				}
   	
   	);*/ 
	
	$("#n_dias_promedio").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_dias_promedio" },						       				
   				function(data){ 
   					
   					$("#n_dias_promedio").html(data[0]['dias_pr']);	       					
					$("#n_dias_promedio").css('padding-left','30px');
   					
   				}
   	
   	); 
	
	
	/*****Titulo con contadores*****/
	
	
	/***Graficos***/
	$('#internacion_ult_semana').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
	
	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php', 
				 { parametro: "internados_ult_semana" },
		         function(datos){	
					
					$('#internacion_ult_semana').html("");
															
					/*Morris.Area({
					  element: 'internacion_ult_semana',
					  behaveLikeLine: true,
					  data: datos,
					  xkey: 'fecha',
					  ykeys: ['internaciones','ingresos','obitos'],
					  labels: ['Internaciones','Ingresos','Obitos'],
					  stacked: true
					});*/
					
					Morris.Bar({
					  element: 'internacion_ult_semana',
					  data: datos,
					  xkey: 'fecha',
					  ykeys: ['internaciones','ingresos','obitos'],
					  labels: ['Internaciones','Ingresos','Obitos'],
					  stacked: true,
					  hideHover: 'auto',
	          		  resize: true
					});
					
	});
    
    
    $('#os_ocupacion').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
    
    $.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php', 
				 { parametro: "internados_ocupacion_os", periodo: "ahora"},
		         function(datos){	
					
					$('#os_ocupacion').html("");
					$('#sm_ocupacion_os').html("Ahora");
					
					Morris.Bar({
						  element: 'os_ocupacion',
						  data: datos,
						  xkey: 'OS',
						  ykeys: ['TOTAL'],
						  labels: ['TOTAL'],
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
					});
					
	});
	
	
	$('#lineas').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
  	
	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php', 
				 { parametro: "internados_ult_semana_new" },
		         function(datos){	
					
					$('#lineas').html("");
					
					Morris.Line({
					  element: 'lineas',
					  data: datos,
					  xkey: 'fecha',
					  ykeys: ['final','internados','ingreso','egreso','obito'],
					  labels: ['Final','Internaciones','Ingresos','Egresos','Obitos'],					  
					  stacked: true,
					  hideHover: 'auto',
	          		  resize: true
					});
					
	});
	
});



