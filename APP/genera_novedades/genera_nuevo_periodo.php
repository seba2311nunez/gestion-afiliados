<?php
include '../../Conectar.inc';

// Verificación rápida
function verificarPeriodo() {
    global $Conexion;
    
    $fecha_hoy = date('Y-m-d');
    echo "=== Verificación de Períodos - $fecha_hoy ===" . PHP_EOL;
    
    // 1. Obtener período activo
    $sql = "SELECT id, obrasocial as vencimiento 
            FROM lotes 
            WHERE proceso = 'novedades_exportables' 
            AND estado = 'Proceso' 
            ORDER BY id DESC 
            LIMIT 1";
    
    $result = $Conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $periodo = $result->fetch_assoc();
        $vencimiento = $periodo['vencimiento'];
        
        echo "Período activo ID: " . $periodo['id'] . PHP_EOL;
        echo "Fecha vencimiento: $vencimiento" . PHP_EOL;
        echo "Fecha hoy: $fecha_hoy" . PHP_EOL;
        
        // 2. Verificar si hoy es día de cierre
        if ($fecha_hoy >= $vencimiento) {
            echo "HOY ES DÍA DE CIERRE - Creando nuevo período..." . PHP_EOL;
            
            // 3. Ejecutar stored procedure
            if ($Conexion->query("CALL NOV_nuevo_periodo_automatico()")) {
                echo "Nuevo período creado exitosamente" . PHP_EOL;
                return true;
            } else {
                echo "Error al crear nuevo período: " . $Conexion->error . PHP_EOL;
                return false;
            }
        } else {
            echo "Aún no es día de cierre" . PHP_EOL;
            return false;
        }
    } else {
        echo "No hay períodos activos" . PHP_EOL;
        return false;
    }
}

// Ejecutar verificación
verificarPeriodo();
?>