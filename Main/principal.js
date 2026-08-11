$(function(){
	
	
	
	$("#actividad_sistema tbody").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				     	
   	$.getJSON('prcpl.php',
   				{ parametro: "actividad_sistema" },						       				
   				function(data){ 
   					
   					$("#actividad_sistema tbody").html("");
   					
   					for(var i=0; i<=data.length ;i++){
   						
   						$("#actividad_sistema tbody").append("<tr  = style='background-color: #FFFFFF;'>"
												      				+"<td>"+data[i]['usuario']+"</td>"
												      				+"<td>"+data[i]['fecha']+"</td>"
													      			+"<td>"+data[i]['evento']+"</td>"      	
													      			+"<td>"+data[i]['id']+"</td>"															      				
													    		+"</tr>") ;
						
   					}
   					
   					$("#ac_sist").css('overflow-y','scroll');
					$("#ac_sist").css('height','400px');
					$("#actividad_sistema").css('font-size','13px');
   					
   				}
   	
   	); 
	
	$("#op_cargadas tbody").html("&nbsp;&nbsp;&nbsp;<i class='fa fa-spinner fa-pulse fa-x fa-fw'></i>");
				     	
   	$.getJSON('prcpl.php',
   				{ parametro: "op_cargadas" },						       				
   				function(data){ 
   					
   					$("#op_cargadas tbody").html("");
   					
   					for(var i=0; i<=data.length ;i++){
   						
   						//var prestador = data[i]['prestadores'] ;
   						
   						//prestador = prestador.replace("ñ","n");
   						//prestador = prestador.replace("Ñ","N");  
   						
   						$("#op_cargadas tbody").append("<tr = style='background-color: #FFFFFF;'>"
												      				+"<td>"+data[i]['fecha']+"</td>"
												      				+"<td>"+data[i]['empresa']+"</td>"
												      				+"<td>"+data[i]['prestadores']+"</td>"
													      			+"<td>"+data[i]['numero']+"</td>"
													      			+"<td>"+data[i]['estado']+"</td>"
													      			+"<td>"+data[i]['facturas']+"</td>"													      			      	
													      			+"<td>"+data[i]['cheques']+"</td>"
													      			+"<td>"+data[i]['usu_op']+"</td>"																      				
													    		+"</tr>") ;		    		
   						
   					}
   					
					$("#div_op_cargadas").css('height','400px');
					$("#div_op_cargadas").css('overflow-y','scroll');					
					$("#op_cargadas").css('font-size','13px');
					//$("#op_cargadas").css('text-align','left');
					//$("#div_op_cargadas").css('max-width','100%');	
   					
   				}
   	
   	); 
	
	$('#cerrar_sesion').on('click',function(){
		alert('hola');
	})
	
})
