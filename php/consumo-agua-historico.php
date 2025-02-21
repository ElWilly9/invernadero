<?php
session_start();
include_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener el rango de tiempo solicitado
$range = $_GET['range'] ?? '24h';

// Preparar la consulta según el rango
switch ($range) {
    case '7d':
        $timeLimit = 'DATE_SUB(NOW(), INTERVAL 7 DAY)';
        break;
    case '30d':
        $timeLimit = 'DATE_SUB(NOW(), INTERVAL 30 DAY)';
        break;
    default: // 24h
        $timeLimit = 'DATE_SUB(NOW(), INTERVAL 24 HOUR)';
}

// Consulta para obtener datos históricos
$SQL = "SELECT 
    litros_min,
    consumo,
    consumo_total,
    fecha_registro
FROM flujo_agua
WHERE fecha_registro >= {$timeLimit}
ORDER BY fecha_registro ASC";

$consulta = mysqli_query($con, $SQL);

$data = [
    'labels' => [],
    'litros_min' => [],
    'consumo' => [],
    'consumo_total' => []
];

if ($consulta && mysqli_num_rows($consulta) > 0) {
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $data['labels'][] = $resultado['fecha_registro'];
        $data['litros_min'][] = floatval($resultado['litros_min']);
        $data['consumo'][] = floatval($resultado['consumo']);
        $data['consumo_total'][] = floatval($resultado['consumo_total']);
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>