<?include('../../Config/Conectar.inc');?>
<!DOCTYPE html>
<html>
 <head>
	  <title>Estadisticas padron</title>
	  <!-- external libs from cdnjs -->
	  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js"></script>
    <!-- optional: mobile support with jqueryui-touch-punch -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

    <!-- PivotTable.js libs from ../dist -->
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pivottable@2.23.0/dist/pivot.min.css">
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pivottable@2.23.0/dist/pivot.min.js"></script>

    <style>
        body {font-family: Verdana;}
    </style>


</head>
<body>
  <p><a href="">Estadisticas Padron</a></p>
  <button id="saveButton">Guardar Agrupamiento</button>
  <select id="ListTemplates">
  	<option value="">Seleccione...</option>
  </select>
  <button id="swapTemplate">Cambiar Agrupamiento</button>

  <p style="width: 800px"></p>
  <div id="output" style="margin: 30px;"></div>
</body>
<script type="text/javascript" src="functions.js"></script>
<script type="text/javascript">
  var tipo = 'totales';
  const INST_NAME  = "<?php echo INST_NAME;?>";
  $(function(){
  	ListTemplates(tipo);
    var pivotTable;
    var groupingConfig = {};

    let date = new Date();
    let year = date.getFullYear();
    let month = ('0' + (date.getMonth() + 1)).slice(-2);
    let day = ('0' + date.getDate()).slice(-2);

    let formattedDate = `${year}${month}${day}`;

    //Papa.parse("archivos/"+INST_NAME+"_padron_csv.csv", {
    Papa.parse("Padron_"+INST_NAME.toUpperCase()+"_"+formattedDate+".csv", {
      download: true,
      skipEmptyLines: true,
      complete: function(parsed){
        pivotTable  = $("#output").pivotUI(parsed.data, {
          onRefresh: function(config) {
            groupingConfig = config;
          }
        });
      }
    });
    // Add a click event handler for the save button
    $("#saveButton").on("click", function () {
      var { cols, rows } = groupingConfig;
      var nombre_group = prompt('Indique como se va a llamar este listado');
      if(!nombre_group) return false;
      SaveTemplate(tipo,nombre_group,JSON.stringify(cols),JSON.stringify(rows));
    });
    $("#swapTemplate").on("click",function() {
    	var option_selected = $('#ListTemplates option:selected');
    	let rows = option_selected.data('rows');
    	let cols = option_selected.data('cols');
    	console.log(rows);
    	console.log(cols);
    	groupingConfig['cols'] = cols;
    	groupingConfig['rows'] = rows;
    });
  });
</script>
</html>