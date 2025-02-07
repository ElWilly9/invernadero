<?php
session_start();
include_once 'conexion.php';
header('Content-Type: application/json');

$range = $_GET['range'] ?? '24h';

$startDate = match($range) {
    '24h' => date('Y-m-d H:i:s', strtotime('-24 hours')),
    '7d' => date('Y-m-d H:i:s', strtotime('-7 days')),
    '30d' => date('Y-m-d H:i:s', strtotime('-30 days')),
};

$SQL = "SELECT 
    litros_min,
    flujo_acumulado,
    fecha_registro 
FROM flujo_agua 
WHERE fecha_registro >= '$startDate' 
ORDER BY fecha_registro ASC";

$consulta = mysqli_query($con, $SQL);

$response = [
    'labels' => [],
    'litrosMin' => [],
    'flujoAcumulado' => []
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    while($fila = mysqli_fetch_assoc($consulta)) {
        $response['labels'][] = $fila['fecha_registro'];
        $response['litrosMin'][] = floatval($fila['litros_min']);
        $response['flujoAcumulado'][] = floatval($fila['flujo_acumulado']);
    }
}

echo json_encode($response);
?>