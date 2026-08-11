<?php
include("../../Config/Conectar.inc");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Filtros (test)</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.0/jspdf.plugin.autotable.min.js" integrity="sha512-DgV2mIRy66quVbkj4yS6FN7cccMH/iPXhDOi/ckWIAANbOL78RuoaA6MAu9BAdYEyAdIuIm63LzsaFmHGd7L8w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../../../style.css">
    <style type="text/css">
        .table thead th {
            background-color: #6c757d; /* Gray background color */
            color: #f8f9fa; /* Light text color */
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
  <div>    
    <div class="row">
      <div class="col-md-2" style="overflow-x: scroll" id="menu_columnas">
        <div id="column-selector" class="list-group">
            <!-- Checkboxes will be added dynamically here -->
        </div>
      </div>
      <div class="col-md-10" id="div_tabla" style="background: #fff;">
        <div class="row">
          <div class="col-md-1">
            <h4>Padron</h4>
          </div>
          <div class="col-md-11" id="filtros_usados" style="font-size: 18px;"></div>
          <div class="col-md-2"></div>
        </div>
        <hr>
        <button id="saveButton">Nuevo Filtro</button>
        <select id="ListTemplates">
          <option value="">Seleccione...</option>
        </select>
        <button id="applyTemplate">Aplicar Filtro</button>
        <!--
        <button id="exportExcelButton">Exportar a Excel</button>
        <button id="exportPDFButton">Exportar a PDF</button>
        -->
        <button id="estadisticas">Estadisticas</button>
        <button id="prueba" style="background-color: aquamarine;">Tildar / Destildar todos</button>
        <button id="toggleMenu">Mostrar/Ocultar Filtros</button>

        <div class="table-responsive">
          <table id="data-table" class="display table table-stripped" style="width:100%">
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
<script type="text/javascript" src="functions.js"></script>
<script>

  window.jsPDF = window.jspdf.jsPDF;
  var tipo = 'detalles';
  var table;
  const INST_NAME = "<?php echo INST_NAME;?>";
  $(document).ready(function() {

    ListTemplates(tipo);
    setTimeout(function(){ 
      //alert("Debe selecionar los campos que requiere dentro del listado."); 
      //$('.column-checkbox').prop('checked', false);
      $("#applyTemplate").click();
      //$('.column-checkbox').click();
      //$('.column-checkbox').data('index').click();
    },2500);
    
    $("#prueba").on('click',function(){
      //$(".form-check-input-lg").removeAttr('checked');
      $('.column-checkbox').click();

    });

    let date = new Date();
    let year = date.getFullYear();
    let month = ('0' + (date.getMonth() + 1)).slice(-2);
    let day = ('0' + date.getDate()).slice(-2);

    let formattedDate = `${year}${month}${day}`;

    $.ajax({
      //url: 'archivos/'+INST_NAME+'_padron_csv.csv',
      url: "Padron_"+INST_NAME.toUpperCase()+"_"+formattedDate+".csv",
      dataType: 'text',
      success: function(data) {
        if (isValidCSV(data)) {
          var parsedData = Papa.parse(data, { header: true });
          var columns = Object.keys(parsedData.data[0]).map(function(columnName, index) {
            return { index: index, data: columnName, title: columnName ,"defaultContent": " "};
          });

          table = $('#data-table').DataTable({
            columns: columns,
            data: parsedData.data,
            pageLength: 100,
            deferRender: true, // Enable deferred rendering
            processing: true, // Display processing indicator
            serverSide: false, // Enable server-side processing
            ajax: function(data, callback, settings) {
              // Simulate server-side processing by slicing the parsed data
              var filteredData = parsedData.data.slice(data.start, data.start + data.length);
              callback({
                draw: data.draw,
                recordsTotal: parsedData.data.length,
                recordsFiltered: parsedData.data.length,
                data: filteredData
              });
            },
            //deferRender: true,
            "defaultContent": "",
            buttons: [
              'pdf', // PDF export button
              'excel', // Excel export button
              'estadisticas'
            ]
          });

          // Generate checkboxes for each column
          var columnSelector = $('#column-selector');
          columns.forEach(function(column) {
            columnSelector.append(
              //'<div class="form-check d-inline-block mr-2">' 
              //+
              '<a class="list-group-item list-group-item-action list-group-item-dark darker a-checkbox">'
              +'<p class="mb-0 text-light mb-custom">'
              +'<input type="checkbox" class="column-checkbox form-check-input form-check-input-lg" data-index="' + column.index + '" data-column="' + column.data + '" checked> ' 
              + column.title 
              +'</p>'
              + '</a>'
                //+'</div>'
            );
          });
          // Handle column checkbox changes
          $('.column-checkbox').on('change', function() {
            //table.draw();
            var column = $(this).data('index');
            var isVisible = $(this).is(':checked');
            console.log(isVisible);
            table.column(column).visible(isVisible);
            console.log('Column index ' + column + ' is ' +
              (table.column(column).visible() === true ? 'visible' : 'not visible')
            );
          });

          // Attach click event handler to the anchor text within .a-checkbox
          $('.a-checkbox').on('click', function(event) {
            event.preventDefault(); // Prevent default behavior of the anchor's click

            // Find the corresponding checkbox
            var checkbox = $(this).find('.column-checkbox');

            // Disable the checkbox click event
            checkbox.off('click');

            // Toggle the checkbox state
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Trigger the change event on the checkbox
            checkbox.trigger('change');

            // Re-enable the checkbox click event after a brief delay
            setTimeout(function() {
              checkbox.on('click', handleCheckboxClick);
            }, 200);
          });
          // Attach initial click event handler to the checkboxes
          $('.column-checkbox').on('click', handleCheckboxClick);
          $("#applyTemplate").on('click',function(){
            var option_selected = $('#ListTemplates option:selected');
            console.log('Testing');
            if(option_selected){
              let rows = option_selected.data('rows');
              let cols = option_selected.data('cols');
              console.log(cols);
              $('.column-checkbox').each(function() {
                var columnName = $(this).data('column');
                if (cols.indexOf(columnName) === -1) {
                  $(this).click();
                }
              });
            }else{
              console.log('Epee');
            }
          });

          // Add click event handlers to the export buttons
          $('#pdfButton').on('click', function() {
            console.log('Hola')
              table.buttons('pdf', null).trigger();
          });

          $('#excelButton').on('click', function() {
              table.buttons('excel', null).trigger();
          });

        } else {
          console.log('CSV data is not valid.');
        }
      }
    });

    $.ajax({url:'ajax_padron.php',
      data: {parametro: 'traer_filtros_usados'},
      success: function(data){
        $('#filtros_usados').html(data);
      }
    });
    // Event handler for the "Save" button
    $('#saveButton').on('click', function(){
      var cols = [];
      var nombre_group = prompt('Indique como se va a llamar este listado');
      if(!nombre_group) return false;
      // Iterate through column checkboxes and store selected columns
      $('.column-checkbox').each(function() {
        if ($(this).is(':checked')) {
          // Get the column name or identifier (e.g., data-column attribute)
          var columnName = $(this).data('column');
          cols.push(columnName);
        }
      });
      SaveTemplate(tipo,nombre_group,JSON.stringify(cols),JSON.stringify('[]'));
    });
    $('#exportExcelButton').click(function() {
      ExportToExcel();
    });
    $('#estadisticas').click(function(){
      ExportToCSV();
    })
    $('#exportPDFButton').click(function() {
        var doc = new jsPDF('l', 'mm'); // Create a new jsPDF instance

        // Get the HTML table element
        var table = $('#data-table')[0]; // Replace 'data-table' with the actual ID of your table

        // Convert the HTML table to PDF
        doc.autoTable({
            html: table,
            tableWidth: doc.internal.pageSize.getWidth(),
            margin: { left: 0 },
        });

        // Save the PDF file
        doc.save('table.pdf');
    });
    $('#toggleMenu').click(function() {
      // Utiliza jQuery para mostrar u ocultar #menu_columnas
      $('#menu_columnas').toggle();
      $('#div_tabla').toggleClass('col-md-12');
    });
  });

  function handleCheckboxClick(event) {
    event.stopPropagation();
  }
  function isValidCSV(data) {
    return true;//data.includes('filial');
  }
  function ExportToExcel(){
    var table = document.getElementById('data-table'); 
    var ws = XLSX.utils.table_to_sheet(table);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, 'table.xlsx');
  }
  function ExportToCSV() {
    var table = document.getElementById('data-table'); // Replace 'data-table' with your table's ID
    var rows = [];
    for (var i = 0; i < table.rows.length; i++) {
      var row = [];
      // Loop through the table cells in each row
      for (var j = 0; j < table.rows[i].cells.length; j++) {
        row.push(table.rows[i].cells[j].textContent.trim()); // Get cell content
      }
      rows.push(row.join(',')); // Join cells with a comma to create a CSV row
    }
    var csvContent = rows.join('\n');
    
    $.ajax({
      type: 'POST',
      url: 'ajax_padron.php', // Replace with the actual path to your PHP script
      data: { csvData: csvContent, parametro: 'guardar_csv_filtrado' },
      success: function(response) {
        window.open('estadisticas_padron.php');
        console.log(response);
      }
    });
  }
</script>

</html>
