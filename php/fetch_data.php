<?php
session_start();
include_once 'conexion.php';

header('Content-Type: application/json');

// Obtener última fecha de actualización
$lastUpdate = $_GET['last_update'] ?? date('Y-m-d H:i:s');

// Consultar nuevos registros
$SQL = "SELECT * 
        FROM temp_hum_amb 
        WHERE fecha_registro > '$lastUpdate'
        ORDER BY fecha_registro ASC";
$consulta = mysqli_query($con, $SQL);

$response = [
    'newData' => false,
    'current' => [
        'temp' => 0,
        'hum' => 0
    ],
    'newTemp' => [],
    'newHum' => [],
    'newDates' => [],
    'last_update' => $lastUpdate
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    while($fila = mysqli_fetch_assoc($consulta)) {
        $response['newTemp'][] = $fila['temperatura_ambiente'];
        $response['newHum'][] = $fila['humedad_ambiente'];
        $response['newDates'][] = $fila['fecha_registro'];
    }
    
    $response['newData'] = true;
    $response['current']['temp'] = end($response['newTemp']);
    $response['current']['hum'] = end($response['newHum']);
    $response['last_update'] = end($response['newDates']);
}

echo json_encode($response);
?>