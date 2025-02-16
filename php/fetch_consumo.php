<?php
session_start();
include_once 'conexion.php';

// Verificar si se proporcionó la última fecha de actualización
$last_update = isset($_GET['last_update']) ? $_GET['last_update'] : '';

// Consultar nuevos registros desde la última actualización
$SQL = "SELECT 
    litros_min,
    flujo_acumulado,
    fecha_registro
FROM flujo_agua
WHERE fecha_registro > ?
ORDER BY fecha_registro ASC";

$stmt = mysqli_prepare($con, $SQL);
mysqli_stmt_bind_param($stmt, "s", $last_update);
mysqli_stmt_execute($stmt);
$consulta = mysqli_stmt_get_result($stmt);

$response = [
    'newData' => false,
    'newDates' => [],
    'newLitrosMin' => [],
    'newFlujoAcumulado' => [],
    'last_update' => $last_update
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    $response['newData'] = true;
    
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $response['newDates'][] = $resultado['fecha_registro'];
        $response['newLitrosMin'][] = floatval($resultado['litros_min']);
        $response['newFlujoAcumulado'][] = floatval($resultado['flujo_acumulado']);
        $response['last_update'] = $resultado['fecha_registro'];
    }
}

// Enviar la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);