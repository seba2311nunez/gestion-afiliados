$(function() {
	
	/***Titulo con contadores***/
	
    $("#n_inter_actuales").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				     	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_inter_actuales" },						       				
   				function(data){ 
   					
   					$("#n_inter_actuales").html(data[0]['internados']);
   					$("#n_camas_totales").html(data[0]['camas']+"&nbsp; Camas Totales");
   					
					$("#n_inter_actuales").css('padding-left','50px');
   					
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
   	
   	
   	$("#n_mortalidad_mes").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_mortalidad_mes" },						       				
   				function(data){ 
   					
   					$("#n_mortalidad_mes").html(data[0]['cant']);
					$("#n_mortalidad_mes").css('padding-left','30px');
   					
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
   	
   	
   	$("#n_derivaciones").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				       	
   	$.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php',
   				{ parametro: "n_derivaciones" },						       				
   				function(data){ 
   					
   					$("#n_derivaciones").html(data[0]['cant']);	       					
					$("#n_derivaciones").css('padding-left','30px');
   					
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
					  element: 'graph',
					  behaveLikeLine: true,
					  data: datos,
					  xkey: 'fecha',
					  ykeys: ['total','internaciones','ingresos','obitos'],
					  labels: ['Total','Internaciones','Ingresos','Obitos'],
					  stacked: true
					});*/
					
					Morris.Bar({
					  element: 'internacion_ult_semana',
					  data: datos,
					  xkey: 'fecha',
					  ykeys: ['internaciones','ingresos','obitos'],
					  labels: ['Internaciones','Ingresos','Obitos'],
					  stacked: true
					});
					
	});
    
    
    $('#os_ocupacion').html("<div style='padding-top: 80px;padding-left: 400px;padding-bottom: 50px;'><i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i><span class='sr-only'>Cargando...</span></div>");
    
    $.getJSON('http://34.123.90.171/php-bin/ws/ws_admision.php', 
				 { parametro: "internados_ocupacion_os", },
		         function(datos){	
					
					$('#os_ocupacion').html("");
					
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
						  }
					});
					
					
					
					
	});
    
    
     
});



