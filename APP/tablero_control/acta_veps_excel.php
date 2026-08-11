<?php
include('../../Config/Conectar.inc');
require_once './../../Lib/PHPExcel/Classes/PHPExcel.php';

function formatColumnName($name) {
    return ucwords(str_replace('_', ' ', strtolower($name)));
}

#if (ob_get_length()) ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="acta_exportada.xlsx"');
header('Cache-Control: max-age=0');

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->setActiveSheetIndex(0);

// Ejecutar consulta
mysql_query("CALL ospedyb_fiscalizacion.trae_acta($id_acta);");
$res = mysql_query("SELECT * FROM ospedyb_fiscalizacion.tmp_acta_con_veps");




// Obtener nombres de columnas
$columns = [];
$colIndex = 0;
while ($field = mysql_fetch_field($res)) {
    $columns[] = $field->name;
}


// Columnas a formatear como numéricas
$columnas_numericas = [
    'rem', 'd931', 'd931_381', 'd931_401',
    'dadic_conv', 'debe_impo', 'int_al_pago', 'debio_pagar', 'pago_por_aporte',
    'faporte_381', 'faporte_401', 'falta_pagar', 'falta_pagar_d931',
    'falta_d931_381', 'falta_d931_401', 'falta_interes_1', 'interes_saldo',
    'dias_saldo', 'falta_interes_2', 'debe_pagar'
];
 
$columna_indices = []; // para identificar posición

// Formato encabezado
$style_encabezado = [
    'font' => ['bold' => true],
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'D9E1F2']
    ],
    'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
];

// Escribir encabezados
foreach ($columns as $colIndex => $colName) {
    $columna_indices[strtolower($colName)] = $colIndex;
    $sheet->setCellValueByColumnAndRow($colIndex, 1, formatColumnName($colName));
    $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($style_encabezado);
}

// Cargar datos
$rowIndex = 2;
while ($row = mysql_fetch_assoc($res)) {
    foreach ($columns as $colIndex => $colName) {
        $value = $row[$colName];
        $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);

        // Estilo para datos
        $sheet->getStyleByColumnAndRow($colIndex, $rowIndex)
              ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        if (in_array(strtolower($colName), $columnas_numericas)) {
            $sheet->getStyleByColumnAndRow($colIndex, $rowIndex)
                  ->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyleByColumnAndRow($colIndex, $rowIndex)
                  ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }
    }
    $rowIndex++;
}

// Autosuma
$sumRow = $rowIndex;
foreach ($columnas_numericas as $nombre_col) {
    $colIndex = $columna_indices[$nombre_col];
    $colLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex);
    $sheet->setCellValue("{$colLetter}{$sumRow}", "=SUM({$colLetter}2:{$colLetter}" . ($sumRow - 1) . ")");
    $sheet->getStyle("{$colLetter}{$sumRow}")->getFont()->setBold(true);
    $sheet->getStyle("{$colLetter}{$sumRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("{$colLetter}{$sumRow}")
          ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
}

// Mapas de columnas para el resumen
$col_empleado = 'U';
$col_empresa = 'V';
$col_interes1 = 'W';
$col_interes2 = 'AA';

// Fila donde está el total de autosuma
$fila_total = $rowIndex;

// Valores individuales
$valor_empleado = $sheet->getCell("{$col_empleado}{$fila_total}")->getCalculatedValue();
$valor_empresa = $sheet->getCell("{$col_empresa}{$fila_total}")->getCalculatedValue();
$valor_intereses = $sheet->getCell("{$col_interes1}{$fila_total}")->getCalculatedValue()
                   + $sheet->getCell("{$col_interes2}{$fila_total}")->getCalculatedValue();
$valor_total = $valor_empleado + $valor_empresa + $valor_intereses;


// Espacio en blanco
$rowIndex = $fila_total + 2;

// Encabezado
$sheet->setCellValue("A{$rowIndex}", "RESUMEN DE VEPS");
$sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true);
$rowIndex++;

// Fila por fila
$sheet->setCellValue("A{$rowIndex}", "Importe aportado por el EMPLEADO");
$sheet->setCellValue("B{$rowIndex}", $valor_empleado);
$sheet->getStyle("B{$rowIndex}")->getNumberFormat()->setFormatCode('#,##0.00');
$rowIndex++;

$sheet->setCellValue("A{$rowIndex}", "Importe aportado por la EMPRESA");
$sheet->setCellValue("B{$rowIndex}", $valor_empresa);
$sheet->getStyle("B{$rowIndex}")->getNumberFormat()->setFormatCode('#,##0.00');
$rowIndex++;

$sheet->setCellValue("A{$rowIndex}", "INTERESES por mora");
$sheet->setCellValue("B{$rowIndex}", $valor_intereses);
$sheet->getStyle("B{$rowIndex}")->getNumberFormat()->setFormatCode('#,##0.00');
$rowIndex++;

$sheet->setCellValue("A{$rowIndex}", "TOTAL A PAGAR");
$sheet->setCellValue("B{$rowIndex}", $valor_total);
$sheet->getStyle("B{$rowIndex}")->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle("A{$rowIndex}:B{$rowIndex}")->getFont()->setBold(true);


// Descargar el archivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
