

	//Selectores
	var ajax_url = 'ajax.php';
	var ajax_selects_url = 'ajax_selects.php';

	var var_desr,var_par,var_parf,var_nac,var_estc,var_del,var_sec,var_tbt,var_rev,var_pat,var_pro,var_loc,var_tdoc,var_pmed;
	function CallSelects(){
		$.when(
			$.getJSON(ajax_url,{ parametro: "desreguladoras" },function(data){var_desr=data}),
			$.getJSON(ajax_url,{ parametro: "parentescos" },function(data){var_par=data}),
			$.getJSON(ajax_url,{ parametro: "parentescos_familiar" },function(data){var_parf=data}),
			$.getJSON(ajax_url,{ parametro: "nacionalidad" },function(data){var_nac=data}),
			$.getJSON(ajax_url,{ parametro: "estado_civil" },function(data){var_estc=data}),
			//$.getJSON(ajax_selects_url,{ parametro: "delegacion" },function(data){var_del=data}),
			$.getJSON(ajax_selects_url,{ parametro: "seccional" },function(data){var_sec=data}),
			$.getJSON(ajax_selects_url,{ parametro: "tbt" },function(data){var_tbt=data}),
			$.getJSON(ajax_selects_url,{ parametro: "revista" },function(data){var_rev=data}),
			$.getJSON(ajax_selects_url,{ parametro: "patologias" },function(data){var_pat=data}),
			$.getJSON(ajax_selects_url,{ parametro: "provincia" },function(data){var_pro=data}),
			$.getJSON(ajax_selects_url,{ parametro: "localidad", provincia: 2 },function(data){var_loc=data}),
			$.getJSON(ajax_selects_url,{ parametro: "tipo_documentacion" },function(data){var_tdoc=data}),
			$.getJSON(ajax_selects_url,{ parametro: "plan_medico" },function(data){var_pmed=data}),
		).then(function(){
			$.each(var_desr, function (key, item) {
		        $("select[name=desreguladora]").append("<option value="+item.id+">"+item.convenio+"</option>");
		    });
			$.each(var_par, function (key, item) {
				console.log('Lo hizo');
		    	$("select[name=parentesco]").append("<option value="+item.id+">"+item.parentesco+"</option>");
		    });
			$.each(var_parf, function (key, item) {
		        $("select[name=fm_parentesco]").append("<option value="+item.id+">"+item.parentesco+"</option>");
		    });
			$("#fm_parentesco").val(3).attr('selected','selected');
			$.each(var_nac, function (key, item) {				                				                
		        $("select[name=fm_nacionalidad]").append("<option value="+item.id+">"+item.nacionalidad+"</option>");
		    });
			$("select[name=fm_nacionalidad]").val(1).attr('selected','selected');
			$.each(var_estc, function (key, item) {       
		        $("select[name=estado_civil]").append("<option value="+item.id+">"+item.estado_civil+"</option>");
		        $("select[name=fm_estado_civil]").append("<option value="+item.id+">"+item.estado_civil+"</option>");
		    });
			$("select[name=fm_estado_civil]").val(2).attr('selected','selected');
			//$.each(var_del, function (key, item) {
		        //$("select[name=delegacion]").append("<option value="+item.id+">"+item.delegacion+"</option>");
		    //});
			$.each(var_sec, function (key, item) {
		        $("select[name=seccional]").append("<option value="+item.id+">"+item.seccional+"</option>");
		    });
			$.each(var_tbt, function (key, item) {
		        $("select[name=tbt]").append("<option value="+item.id+">"+item.beneficiario+"</option>");
		    });
			$.each(var_rev, function (key, item) {
			    $("select[name=revista]").append("<option value="+item.id+">"+item.revista+"</option>");
		    });
			$.each(var_pat, function (key, item) {
		        $("select[name=pat_patologias]").append("<option value="+item.id+">"+item.nombre+"</option>");
		    });
			$.each(var_pro, function (key, item) {
                $("select[name=t_provincia]").append("<option value="+item.id+">"+item.provincia+"</option>");
            });
			$("#t_provincia").val(2).attr('selected','selected');
			$.each(var_loc, function (key, item) {
	            $("#t_localidad").append("<option label='CP: "+item.localidad+"'  value='"+item.id+"' >");
	        });
			$.each(var_tdoc, function (key, item) {
                $("#tipo_documentacion").append("<option value='"+item.id+"' >"+item.documentacion+"</option>");
            });
			$.each(var_pmed, function (key, item) {
                $("#plan_medico").append("<option value='"+item.id+"' >"+item.nombre+"</option>");
            });
			console.log('Done');
		});
	}
