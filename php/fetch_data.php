<?php
session_start();
include_once 'conexion.php';

header('Content-Type: application/json');

$lastUpdate = $_GET['last_update'] ?? date('Y-m-d H:i:s');

$SQL = "SELECT 
    th.temperatura_ambiente,
    th.humedad_ambiente,
    th.fecha_registro,
    (
        SELECT hs.humedad
        FROM hum_suelo hs
        WHERE DATE(hs.fecha_registro) = DATE(th.fecha_registro)
        AND ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro)))) < 60
        ORDER BY ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro))))
        LIMIT 1
    ) as humedad
FROM temp_hum_amb th
WHERE th.fecha_registro > '$lastUpdate'
ORDER BY th.fecha_registro ASC";

$consulta = mysqli_query($con, $SQL);

$response = [
    'newData' => false,
    'current' => [
        'temp' => 0,
        'hum' => 0,
        'suelo' => 'N/A'
    ],
    'newTemp' => [],
    'newHum' => [],
    'newSuelo' => [],
    'newDates' => [],
    'last_update' => $lastUpdate
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    while($fila = mysqli_fetch_assoc($consulta)) {
        $response['newTemp'][] = $fila['temperatura_ambiente'];
        $response['newHum'][] = $fila['humedad_ambiente'];
        $response['newSuelo'][] = $fila['humedad'];
        $response['newDates'][] = $fila['fecha_registro'];
    }
    
    $response['newData'] = true;
    $response['current']['temp'] = end($response['newTemp']);
    $response['current']['hum'] = end($response['newHum']);
    $response['current']['suelo'] = end($response['newSuelo']) ?: 'N/A';
    $response['last_update'] = end($response['newDates']);
}

echo json_encode($response);
?>