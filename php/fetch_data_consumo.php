<?php
session_start();
include_once 'conexion.php';
$lastUpdate = $_GET['last_update'] ?? date('Y-m-d H:i:s');

header('Content-Type: application/json');

if(!isset($_SESSION['usuario'])){
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$SQL = "SELECT 
    litros_min,
    flujo_acumulado,
    fecha_registro
FROM flujo_agua 
WHERE fecha_registro > ?
ORDER BY fecha_registro ASC";

$stmt = mysqli_prepare($con, $SQL);
mysqli_stmt_bind_param($stmt, 's', $lastUpdate);
mysqli_stmt_execute($stmt);
$consulta = mysqli_stmt_get_result($stmt);

$response = [
    'newData' => false,
    'newDates' => [],
    'newLitrosMin' => [],
    'newFlujoAcumulado' => [],
    'current' => [
        'litrosMin' => 'N/A',
        'flujoAcumulado' => 'N/A'
    ],
    'last_update' => $lastUpdate
];

if($consulta && mysqli_num_rows($consulta) > 0){
    $response['newData'] = true;
    
    while ($row = mysqli_fetch_assoc($consulta)) {
        $response['newDates'][] = $row['fecha_registro'];
        $response['newLitrosMin'][] = $row['litros_min'] !== null ? floatval($row['litros_min']) : null;
        $response['newFlujoAcumulado'][] = $row['flujo_acumulado'] !== null ? floatval($row['flujo_acumulado']) : null;
        
        // Actualizar valores actuales
        $response['current'] = [
            'litrosMin' => $row['litros_min'] ?? 'N/A',
            'flujoAcumulado' => $row['flujo_acumulado'] ?? 'N/A'
        ];
        
        $response['last_update'] = $row['fecha_registro'];
    }
}

echo json_encode($response);
?>