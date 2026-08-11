<?php
header('Content-Type: application/json; charset=utf-8');

include("../../Config/Conectar.inc");

$parametro = isset($_GET["parametro"]) ? $_GET["parametro"] : "";
$desde = isset($_GET["desde"]) ? $_GET["desde"] : "";
$hasta = isset($_GET["hasta"]) ? $_GET["hasta"] : "";

$where = " WHERE 1=1 ";

if ($desde != "") {
    $where .= " AND DATE_FORMAT(fecha, '%Y-%m') >= '$desde' ";
}

if ($hasta != "") {
    $where .= " AND DATE_FORMAT(fecha, '%Y-%m') <= '$hasta' ";
}

switch ($parametro) {

    case "resumen_tablero":

        $sql = "
            SELECT 
                COUNT(*) AS total_movimientos,
                SUM(CASE WHEN movimiento LIKE '%ALTA%' THEN 1 ELSE 0 END) AS total_altas,
                SUM(CASE WHEN movimiento LIKE '%BAJA%' THEN 1 ELSE 0 END) AS total_bajas,
                COUNT(DISTINCT DATE_FORMAT(fecha, '%Y-%m')) AS total_meses
            FROM ospedyb_padron.eventos_afiliados
            $where
        ";

        $rs = mysql_query($sql) or die(json_encode(array("error" => mysql_error(), "sql" => $sql)));
        $row = mysql_fetch_assoc($rs);

        echo json_encode($row);
        break;


    case "resumen_mensual":

        $sql = "
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') AS periodo,
                COUNT(*) AS total,
                SUM(CASE WHEN movimiento LIKE '%ALTA%' THEN 1 ELSE 0 END) AS altas,
                SUM(CASE WHEN movimiento LIKE '%BAJA%' THEN 1 ELSE 0 END) AS bajas
            FROM ospedyb_padron.eventos_afiliados
            $where
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY periodo DESC
        ";

        $rs = mysql_query($sql) or die(json_encode(array("error" => mysql_error(), "sql" => $sql)));

        $data = array();

        while ($row = mysql_fetch_assoc($rs)) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;


    case "detalle_movimientos":

        $sql = "
            SELECT 
                id,
                DATE_FORMAT(fecha, '%Y-%m') AS periodo,
                afiliado,
                cuil,
                movimiento,
                origen,
                DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha,
                observacion
            FROM ospedyb_padron.eventos_afiliados
            $where
            ORDER BY fecha DESC, id DESC
            LIMIT 1000
        ";

        $rs = mysql_query($sql) or die(json_encode(array("error" => mysql_error(), "sql" => $sql)));

        $data = array();

        while ($row = mysql_fetch_assoc($rs)) {
            $data[] = $row;
        }

        echo json_encode($data);
        break;


    default:
        echo json_encode(array("error" => "Parametro no valido"));
        break;
}
?>