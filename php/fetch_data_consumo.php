<?php
include_once 'conexion.php';

$lastUpdate = $_GET['last_update'] ?? '2023-01-01 00:00:00';

// Consulta para obtener datos nuevos desde la última actualización
$SQL = "SELECT 
    litros_min,
    flujo_acumulado,
    fecha_registro
FROM flujo_agua
WHERE fecha_registro > ?
ORDER BY fecha_registro ASC";

$stmt = $con->prepare($SQL);
$stmt->bind_param("s", $lastUpdate);
$stmt->execute();
$result = $stmt->get_result();

$newLitrosMin = [];
$newFlujoAcumulado = [];
$newDates = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = $result->fetch_assoc()) {
        $newLitrosMin[] = $row['litros_min'];
        $newFlujoAcumulado[] = $row['flujo_acumulado'];
        $newDates[] = $row['fecha_registro'];
    }
    
    $lastUpdate = end($newDates); // Actualiza la última fecha
    $newData = true;
} else {
    $newData = false;
}

// Devolver datos en formato JSON
echo json_encode([
    'newData' => $newData,
    'newDates' => $newDates,
    'newLitrosMin' => $newLitrosMin,
    'newFlujoAcumulado' => $newFlujoAcumulado,
    'last_update' => $lastUpdate
]);