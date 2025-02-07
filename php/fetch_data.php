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

// Consulta simple tratando humedad1 y humedad2 como columnas de temp_hum_amb
$SQL = "SELECT 
    temperatura_ambiente1,
    temperatura_ambiente2,
    humedad_ambiente1,
    humedad_ambiente2,
    humedad1,
    humedad2,
    fecha_registro
FROM temp_hum_amb 
WHERE fecha_registro > ?
ORDER BY fecha_registro ASC";

$stmt = mysqli_prepare($con, $SQL);
mysqli_stmt_bind_param($stmt, 's', $lastUpdate);
mysqli_stmt_execute($stmt);
$consulta = mysqli_stmt_get_result($stmt);

$response = [
    'newData' => false,
    'newDates' => [],
    'newTemp1' => [],
    'newTemp2' => [],
    'newHum1' => [],
    'newHum2' => [],
    'newSuelo1' => [],
    'newSuelo2' => [],
    'current' => [
        'temp1' => 'N/A',
        'temp2' => 'N/A',
        'hum1' => 'N/A',
        'hum2' => 'N/A',
        'suelo1' => 'N/A',
        'suelo2' => 'N/A'
    ],
    'last_update' => $lastUpdate
];

if($consulta && mysqli_num_rows($consulta) > 0){
    $response['newData'] = true;
    
    while ($row = mysqli_fetch_assoc($consulta)) {
        $response['newDates'][] = $row['fecha_registro'];
        $response['newTemp1'][] = $row['temperatura_ambiente1'] !== null ? floatval($row['temperatura_ambiente1']) : null;
        $response['newTemp2'][] = $row['temperatura_ambiente2'] !== null ? floatval($row['temperatura_ambiente2']) : null;
        $response['newHum1'][] = $row['humedad_ambiente1'] !== null ? floatval($row['humedad_ambiente1']) : null;
        $response['newHum2'][] = $row['humedad_ambiente2'] !== null ? floatval($row['humedad_ambiente2']) : null;
        $response['newSuelo1'][] = $row['humedad1'] !== null ? floatval($row['humedad1']) : null;
        $response['newSuelo2'][] = $row['humedad2'] !== null ? floatval($row['humedad2']) : null;
        
        // Actualizar valores actuales
        $response['current'] = [
            'temp1' => $row['temperatura_ambiente1'] ?? 'N/A',
            'temp2' => $row['temperatura_ambiente2'] ?? 'N/A',
            'hum1' => $row['humedad_ambiente1'] ?? 'N/A',
            'hum2' => $row['humedad_ambiente2'] ?? 'N/A',
            'suelo1' => $row['humedad1'] ?? 'N/A',
            'suelo2' => $row['humedad2'] ?? 'N/A'
        ];
        
        $response['last_update'] = $row['fecha_registro'];
    }
}

echo json_encode($response);
?>