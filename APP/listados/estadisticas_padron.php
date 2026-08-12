<?php
include('../../Config/Conectar.inc');
$archivoCsv = 'Padron_'.strtoupper(INST_NAME).'_'.date('Ymd').'.csv';
$archivoCsvDisponible = is_file(__DIR__.DIRECTORY_SEPARATOR.$archivoCsv);
?>
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
  <?php if(!$archivoCsvDisponible){ ?>
  <div style="margin:30px;padding:15px;border:1px solid #d39e00;background:#fff3cd;color:#664d03;">
    No se encontró el archivo de estadísticas del día. Volvé al listado y usá el botón <strong>Estadísticas</strong> para generarlo.
  </div>
  <?php } ?>
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

    var archivoCsv = <?php echo json_encode($archivoCsv); ?>;
    var archivoCsvDisponible = <?php echo $archivoCsvDisponible ? 'true' : 'false'; ?>;
    if(!archivoCsvDisponible) return;
    Papa.parse(archivoCsv, {
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
