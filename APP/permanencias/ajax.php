<?php 
include("../../Config/Conectar.inc");

mysql_query("SET NAMES 'utf8'");
header('Content-Type: application/json; charset=utf-8');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

switch ($accion) {

    case 'reporte_permanencias':

        $desde = isset($_POST['desde']) ? $_POST['desde'] : '';
        $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : '';
        $desreguladora = isset($_POST['desreguladora']) ? $_POST['desreguladora'] : '';

        if ($desde == '' || $hasta == '') {
            echo json_encode(array(
                "ok" => false,
                "error" => "Debe indicar período desde y hasta"
            ));
            exit;
        }

        $fecha_desde = $desde . "-01";
        $fecha_hasta = date("Y-m-t", strtotime($hasta . "-01"));

        $where_extra = "";

        /*
            Si _historico_afiliados tiene una columna directa de desreguladora,
            se puede activar esto ajustando el nombre del campo.

            Ejemplo:
            if ($desreguladora != '') {
                $where_extra .= " AND a.desreguladora = '".mysql_real_escape_string($desreguladora)."' ";
            }

            Si la desreguladora está en otra tabla, después hacemos el JOIN.
        */

        $sql = "
            SELECT
                periodo_alta,
                COUNT(*) AS altas,
                SUM(CASE WHEN fecha_baja IS NOT NULL THEN 1 ELSE 0 END) AS bajas_detectadas,
                SUM(CASE WHEN fecha_baja IS NULL THEN 1 ELSE 0 END) AS sin_baja,

                ROUND(AVG(
                    CASE
                        WHEN fecha_baja IS NOT NULL
                        THEN TIMESTAMPDIFF(MONTH, fecha_alta, fecha_baja)
                    END
                ), 1) AS promedio_meses,

                SUM(CASE
                    WHEN fecha_baja IS NOT NULL
                     AND TIMESTAMPDIFF(MONTH, fecha_alta, fecha_baja) < 12
                    THEN 1 ELSE 0
                END) AS menor_12,

                SUM(CASE
                    WHEN fecha_baja IS NOT NULL
                     AND TIMESTAMPDIFF(MONTH, fecha_alta, fecha_baja) BETWEEN 12 AND 14
                    THEN 1 ELSE 0
                END) AS entre_12_15,

                SUM(CASE
                    WHEN fecha_baja IS NOT NULL
                     AND TIMESTAMPDIFF(MONTH, fecha_alta, fecha_baja) BETWEEN 15 AND 17
                    THEN 1 ELSE 0
                END) AS entre_15_18,

                SUM(CASE
                    WHEN fecha_baja IS NOT NULL
                     AND TIMESTAMPDIFF(MONTH, fecha_alta, fecha_baja) >= 18
                    THEN 1 ELSE 0
                END) AS mayor_18

            FROM (
                SELECT
                    a.id_afiliado,
                    a.fecha_aPartir AS fecha_alta,
                    DATE_FORMAT(a.fecha_aPartir, '%Y-%m') AS periodo_alta,
                    MIN(b.fecha_aPartir) AS fecha_baja

                FROM $base_historicos._historico_afiliados a

                LEFT JOIN $base_historicos._historico_afiliados b
                    ON b.id_afiliado = a.id_afiliado
                   AND b.id_evento_afiliado = 121
                   AND b.estado = 'BAJA'
                   AND b.fecha_aPartir > a.fecha_aPartir

                WHERE a.id_evento_afiliado = 120
                  AND a.estado = 'ALTA'
                  AND a.fecha_aPartir BETWEEN '$fecha_desde' AND '$fecha_hasta'
                  $where_extra

                GROUP BY
                    a.id_afiliado,
                    a.fecha_aPartir
            ) x

            GROUP BY periodo_alta
            ORDER BY periodo_alta
        ";

        $result = mysql_query($sql) or die(json_encode(array(
            "ok" => false,
            "error" => mysql_error(),
            "sql" => $sql
        )));

        $detalle = array();

        $total_altas = 0;
        $total_bajas = 0;
        $total_sin_baja = 0;
        $suma_promedio_ponderado = 0;

        while ($row = mysql_fetch_assoc($result)) {

            $altas = intval($row['altas']);
            $bajas = intval($row['bajas_detectadas']);
            $sin_baja = intval($row['sin_baja']);
            $promedio = is_null($row['promedio_meses']) ? 0 : floatval($row['promedio_meses']);

            $total_altas += $altas;
            $total_bajas += $bajas;
            $total_sin_baja += $sin_baja;

            if ($bajas > 0) {
                $suma_promedio_ponderado += ($promedio * $bajas);
            }

            $detalle[] = array(
                "periodo_alta" => $row['periodo_alta'],
                "altas" => number_format($altas, 0, ',', '.'),
                "bajas_detectadas" => number_format($bajas, 0, ',', '.'),
                "sin_baja" => number_format($sin_baja, 0, ',', '.'),
                "promedio_meses" => number_format($promedio, 1, ',', '.'),
                "menor_12" => number_format(intval($row['menor_12']), 0, ',', '.'),
                "entre_12_15" => number_format(intval($row['entre_12_15']), 0, ',', '.'),
                "entre_15_18" => number_format(intval($row['entre_15_18']), 0, ',', '.'),
                "mayor_18" => number_format(intval($row['mayor_18']), 0, ',', '.')
            );
        }

        $promedio_total = 0;
        if ($total_bajas > 0) {
            $promedio_total = $suma_promedio_ponderado / $total_bajas;
        }

        $sin_baja_pct = 0;
        if ($total_altas > 0) {
            $sin_baja_pct = ($total_sin_baja * 100) / $total_altas;
        }

        echo json_encode(array(
            "ok" => true,
            "kpis" => array(
                "altas" => number_format($total_altas, 0, ',', '.'),
                "bajas" => number_format($total_bajas, 0, ',', '.'),
                "promedio" => number_format($promedio_total, 1, ',', '.'),
                "sin_baja_pct" => number_format($sin_baja_pct, 1, ',', '.')
            ),
            "detalle" => $detalle
        ));

        break;

    default:
        echo json_encode(array(
            "ok" => false,
            "error" => "Acción no válida"
        ));
        break;
}