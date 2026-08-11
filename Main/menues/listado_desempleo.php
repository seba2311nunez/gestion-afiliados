<?php
require_once '../../Config/database.inc.php';
// listado_desempleados.php

// 1. Conexión a la base de datos
$databaseConfig=database_private_config();
$mysqli = new mysqli($databaseConfig['host'], $databaseConfig['usuario'], $databaseConfig['clave'], 'osemm_padron', intval($databaseConfig['port']));
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// 2. Consulta SQL (ajustar si hace falta)
$sql = "
SELECT d.id, d.nd, d.ayn, d.gp, pa.parentesco,
       osemm_padron.estado_afiliado_nuevo_test(a.id, CURDATE()) AS estado,
       de.convenio 
FROM osemm_historicos.desempleo d
JOIN osemm_padron.persona p ON d.nd = p.nd 
JOIN osemm_padron.afiliados a ON p.id = a.id_persona 
JOIN osemm_padron.desreguladoras de ON a.id_desreguladora = de.id 
JOIN osemm_padron.parentesco pa ON a.id_parentesco = pa.id
WHERE d.id_lote = 12397
";

// 3. Ejecutar la consulta
$resultado = $mysqli->query($sql);

// 4. Mostrar los resultados
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Desempleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">Listado de Desempleados (Lote 12397)</h2>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>ND</th>
                <th>Apellido y Nombre</th>
                <th>GP</th>
                <th>Parentesco</th>
                <th>Estado</th>
                <th>Convenio</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nd'] ?></td>
                    <td><?= $row['ayn'] ?></td>
                    <td><?= $row['gp'] ?></td>
                    <td><?= $row['parentesco'] ?></td>
                    <td><?= $row['estado'] ?></td>
                    <td><?= $row['convenio'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No se encontraron registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$mysqli->close();
?>
