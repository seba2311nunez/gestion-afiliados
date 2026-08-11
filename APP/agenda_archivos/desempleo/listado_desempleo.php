<?php
include('../../../Config/Conectar.inc');
$query = "SELECT
            d.cuil_titular,
            d.gp,
            d.cuil,
            d.nd,
            d.ayn,
            d.fn,
            d.sexo,
            d.fecha_proceso,
            $base_padron.estado_afiliado_nuevo_test(a.id,CURDATE()) AS estado_afiliado,
            de.convenio,
            a.nben,
            a.gpar
          FROM (SELECT * FROM $base_historicos.desempleo d WHERE id_lote=$id_lote) d
          JOIN $base_padron.persona p ON d.nd=p.nd
          JOIN $base_padron.afiliados a ON p.id=a.id_persona
          JOIN $base_padron.desreguladoras de ON a.id_desreguladora=de.id
          JOIN $base_padron.parentesco pa ON a.id_parentesco=pa.id";

$rs = mysql_query($query) or die("ERROR SQL: ".mysql_error()."<br>QUERY: ".$query);
$num_rows = mysql_num_rows($rs);
#echo "hola ".INST_NAME;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Desempleo</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS con Bootstrap -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Botones DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Extensiones para botones -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    
    <style>
        .dataTables_wrapper .row {
            margin: 10px 0;
        }
        .table-container {
            margin: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .dt-buttons {
            display: inline-block;
            margin-left: 10px;
        }
        .dataTables_filter {
            display: inline-block;
            float: right;
        }
        .dt-button {
            padding: 0.375rem 0.75rem;
        }
        .table th {
            background-color: #343a40;
            color: white;
            font-weight: 600;
        }
        .badge-estado {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }
        /* Estilos para el botón Volver arriba */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            display: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .back-to-top:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Botón Volver arriba -->
    <button class="back-to-top" id="backToTop" title="Volver arriba">
        <i class="bi bi-arrow-up"></i>
    </button>

    <div class="container-fluid mt-4">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Listado de Desempleo</h2>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-person-lines-fill"></i> 
                    Cantidad de registros: <strong><?php echo $num_rows; ?></strong>
                </div>
            </div>
            
            <table id="miTabla" class="table table-striped table-hover table-bordered" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>CUIL Titular</th>
                        <th>GP</th>
                        <th>CUIL</th>
                        <th>DNI</th>
                        <th>Apellido y Nombre</th>
                        <th>F. Nacimiento</th>
                        <th>Sexo</th>
                        <th>F. Proceso</th>
                        <th>Estado Afiliado</th>
                        <th>Convenio</th>
                        <?php if (INST_NAME == "osemm") { ?>
                        <th>NBEN/GPAR</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysql_fetch_assoc($rs)) {
                        echo "<tr>";
                        echo "<td>".htmlspecialchars($row['cuil_titular'])."</td>";
                        echo "<td>".htmlspecialchars($row['gp'])."</td>";
                        echo "<td>".htmlspecialchars($row['cuil'])."</td>";
                        echo "<td>".htmlspecialchars($row['nd'])."</td>";
                        echo "<td>".htmlspecialchars($row['ayn'])."</td>";
                        echo "<td>".htmlspecialchars($row['fn'])."</td>";
                        echo "<td>".htmlspecialchars($row['sexo'])."</td>";
                        echo "<td>".htmlspecialchars($row['fecha_proceso'])."</td>";
                        
                        // Cortar el estado desde el arroba y aplicar estilo
                        $estado_completo = htmlspecialchars($row['estado_afiliado']);
                        $estado_cortado = $estado_completo;
                        
                        // Cortar desde el arroba si existe
                        if (strpos($estado_completo, '@') !== false) {
                            $estado_cortado = substr($estado_completo, 0, strpos($estado_completo, '@'));
                        }
                        
                        // Aplicar estilo al estado del afiliado
                        $badge_class = 'bg-secondary'; // Por defecto
                        if (stripos($estado_cortado, 'activo') !== false || stripos($estado_cortado, 'alta') !== false) {
                            $badge_class = 'bg-success';
                            $estado_cortado = 'ALTA';
                        } elseif (stripos($estado_cortado, 'baja') !== false) {
                            $badge_class = 'bg-danger';
                            $estado_cortado = 'BAJA';
                        } elseif (stripos($estado_cortado, 'suspend') !== false) {
                            $badge_class = 'bg-warning';
                        } elseif (stripos($estado_cortado, 'pendiente') !== false) {
                            $badge_class = 'bg-info';
                        }
                        
                        echo "<td><span class='badge $badge_class badge-estado'>$estado_cortado</span></td>";
                        echo "<td>".htmlspecialchars($row['convenio'])."</td>";

                        if (INST_NAME == "osemm") {
                            $nben_gpar = htmlspecialchars($row['nben']).'/'.htmlspecialchars($row['gpar']);
                            echo "<td>".$nben_gpar."</td>";
                        }

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>

        var os = "<?=INST_NAME;?>";
        console.log("OS: "+os);

        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#miTabla').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                "order": [[4, 'asc']], // Ordenar por Apellido y Nombre por defecto
                "responsive": true,
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'fB>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-excel me-1"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Listado_Desempleo_OSPILM',
                        exportOptions: {
                            columns: ':visible',
                            modifier: {
                                page: 'all'
                            }
                        },
                        filename: function() {
                            return 'Listado_Desempleo_'+os+'_' + new Date().toISOString().slice(0, 10);
                        }
                    }
                ],
                "columnDefs": [
                    {
                        "targets": [0, 2], // CUIL Titular y CUIL
                        "render": function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return data.replace(/(\d{2})(\d{8})(\d{1})/, '$1-$2-$3');
                            }
                            return data;
                        }
                    }
                ],
                "initComplete": function() {
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Función para el botón Volver arriba
            var backToTopButton = $('#backToTop');

            // Mostrar/ocultar el botón al hacer scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    backToTopButton.fadeIn();
                } else {
                    backToTopButton.fadeOut();
                }
            });

            // Scroll suave al hacer clic en el botón
            backToTopButton.click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

            // También hacer scroll al top cuando se cambia de página en DataTables
            table.on('page.dt', function() {
                $('html, body').animate({
                    scrollTop: $('.table-container').offset().top - 20
                }, 500);
            });
        });
    </script>
</body>
</html>