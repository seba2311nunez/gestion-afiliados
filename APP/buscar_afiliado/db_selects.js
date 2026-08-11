$(function(){


	//Desreguladoras
	$.getJSON('ajax_selects.php',
				{ parametro: "desreguladoras" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {
		                
		                $("select[name=t_desreguladora]").append("<option value="+item.id+">"+item.convenio+"</option>");
		            });

					$("select[name=t_desreguladora]").val(1).attr('selected','selected');

				}//fin function data

	);//fin getjson

	//Delegaciones
	$.getJSON('ajax_selects.php',
				{ parametro: "delegacion" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {
		                
		                $("select[name=t_delegacion]").append("<option value="+item.id+">"+item.delegacion+"</option>");
		            });

					$("select[name=t_delegacion]").val(5023).attr('selected','selected');

				}//fin function data

	);//fin getjson

	//Tipo de beneficiario 
	$.getJSON('ajax_selects.php',
				{ parametro: "tbt" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {
		                
		                $("select[name=t_tbt]").append("<option value="+item.id+">"+item.beneficiario+"</option>");
		            });

					$("select[name=t_tbt]").val(0).attr('selected','selected');

				}//fin function data

	);//fin getjson

	
	
	//Situacion de revista
	$.getJSON('ajax_selects.php',
				{ parametro: "revista" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {
		                
		                $("select[name=t_revista]").append("<option value="+item.id+">"+item.revista+"</option>");
		            });

					$("select[name=t_revista]").val(0).attr('selected','selected');

				}//fin function data

	);//fin getjson

	//nacionalidad
	$.getJSON('ajax_selects.php',
				{ parametro: "nacionalidad" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {				                				                
		                $("select[name=t_nacionalidad]").append("<option value="+item.id+">"+item.nacionalidad+"</option>");
		            });

					$("select[name=t_nacionalidad]").val(1).attr('selected','selected');

				}//fin function data

	);//fin getjson

	//estado_civil
	$.getJSON('ajax_selects.php',
				{ parametro: "estado_civil" },						       				
				function(datos){ 
					
					$.each(datos, function (key, item) {
		                
		                $("select[name=t_estado_civil]").append("<option value="+item.id+">"+item.estado_civil+"</option>");
		                
		            });

					$("select[name=t_estado_civil]").val(2).attr('selected','selected');

				}//fin function data

	);//fin getjson

})