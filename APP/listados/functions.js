function SaveTemplate(tipo,nombre_group,cols,rows){
	$.ajax({
		url: 'ajax_padron.php',
		type: 'post',
		dataType: 'text',
		data: {parametro: 'guardar_agrupamiento' ,tipo: tipo ,nombre_group: nombre_group ,cols: cols ,rows: rows}
	}).then(function(data){
		if(data=="ok"){
    	alert('Guardado');
		}
	});
}
function ListTemplates(tipo){
	$.ajax({
		url: 'ajax_padron.php',
		type: 'get',
		dataType: 'json',
		data: {parametro: 'listar_templates_listados_padron', tipo: tipo}
	}).then(function(data){
		$("#ListTemplates").html("");
		console.log(data.length-1);
		for(var i=0;i<=data.length-1;i++){
			let {id,nombre,rows,cols} = data[i];
			$("#ListTemplates").append(`<option value="${id}" data-rows=${rows} data-cols=${cols}>${nombre}</option>`);
		}
	});
}