<?php
require '../../Lib/PHPExcel/Classes/PHPExcel.php';

function leer_padron($archivo) {
    $objPHPExcel = PHPExcel_IOFactory::load($archivo);
    $sheet = $objPHPExcel->getActiveSheet();
    $data = [];

    foreach ($sheet->getRowIterator(2) as $row) { // desde fila 2
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $fila = [];

        foreach ($cellIterator as $cell) {
            $fila[] = trim($cell->getFormattedValue());
        }

        $documento = $fila[5]; // F: nd
        if ($documento) {
            $data[$documento] = [
                'documento' => $documento,
                'ayn'       => $fila[3],  // D: ayn
                'estado'    => $fila[16], // Q: estado
            ];
        }
    }

    return $data;
}

$bajas = $altas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['padron_anterior'], $_FILES['padron_actual'])) {
    $archivo_anterior = $_FILES['padron_anterior']['tmp_name'];
    $archivo_actual   = $_FILES['padron_actual']['tmp_name'];

    $anterior = leer_padron($archivo_anterior);
    $actual   = leer_padron($archivo_actual);

    $bajas = array_diff_key($anterior, $actual);
    $altas = array_diff_key($actual, $anterior);

    // Si se presiona el botón de descarga
    if (isset($_POST['descargar'])) {
        $excel = new PHPExcel();

        // Hoja 1: Bajas
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle('Bajas');
        $excel->getActiveSheet()->fromArray(['Documento', 'Apellido y Nombre', 'Estado'], NULL, 'A1');
        $fila = 2;
        foreach ($bajas as $d) {
            $excel->getActiveSheet()
                ->setCellValue("A$fila", $d['documento'])
                ->setCellValue("B$fila", $d['ayn'])
                ->setCellValue("C$fila", $d['estado']);
            $fila++;
        }

        // Hoja 2: Altas
        $excel->createSheet();
        $excel->setActiveSheetIndex(1);
        $excel->getActiveSheet()->setTitle('Altas nuevas');
        $excel->getActiveSheet()->fromArray(['Documento', 'Apellido y Nombre', 'Estado'], NULL, 'A1');
        $fila = 2;
        foreach ($altas as $d) {
            $excel->getActiveSheet()
                ->setCellValue("A$fila", $d['documento'])
                ->setCellValue("B$fila", $d['ayn'])
                ->setCellValue("C$fila", $d['estado']);
            $fila++;
        }

        $filename = 'Diferencias_Padron_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comparar Padrones de Afiliados</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 6px; font-size: 14px; }
        th { background-color: #eee; }
        .baja { background-color: #ffe6e6; }
        .alta { background-color: #e6ffe6; }
    </style>
</head>
<body>

<h2>Comparar Padrones de Afiliados (.xlsx)</h2>

<form method="POST" enctype="multipart/form-data">
    <label><b>Padron anterior (.xlsx):</b></label><br>
    <input type="file" name="padron_anterior" accept=".xlsx" required><br><br>

    <label><b>Padron actual (.xlsx):</b></label><br>
    <input type="file" name="padron_actual" accept=".xlsx" required><br><br>

    <button type="submit">Comparar</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$bajas && !$altas): ?>
    <p style="color:red;">No se encontraron diferencias entre los padrones.</p>
<?php elseif ($bajas || $altas): ?>

    <h3>✅ Resultado de la comparación:</h3>
    <p><b>Cantidad de Bajas:</b> <?= count($bajas) ?></p>
    <p><b>Cantidad de Altas nuevas:</b> <?= count($altas) ?></p>

    <?php if ($bajas): ?>
        <h4>🔻 Bajas detectadas (ya no están en el padrón actual):</h4>
        <table>
            <tr><th>Documento</th><th>Apellido y Nombre</th><th>Estado</th></tr>
            <?php foreach ($bajas as $d): ?>
                <tr class="baja">
                    <td><?= $d['documento'] ?></td>
                    <td><?= $d['ayn'] ?></td>
                    <td><?= $d['estado'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if ($altas): ?>
        <h4>🆕 Altas nuevas (no estaban en el padrón anterior):</h4>
        <table>
            <tr><th>Documento</th><th>Apellido y Nombre</th><th>Estado</th></tr>
            <?php foreach ($altas as $d): ?>
                <tr class="alta">
                    <td><?= $d['documento'] ?></td>
                    <td><?= $d['ayn'] ?></td>
                    <td><?= $d['estado'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="padron_anterior" value="<?= $_FILES['padron_anterior']['tmp_name'] ?>">
        <input type="hidden" name="padron_actual" value="<?= $_FILES['padron_actual']['tmp_name'] ?>">
        <input type="hidden" name="descargar" value="1">
        <button type="submit">⬇️ Descargar archivo Excel con las diferencias</button>
    </form>

<?php endif; ?>

</body>
</html>
