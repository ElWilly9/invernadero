<?php
session_start();
include_once 'conexion.php';
header('Content-Type: application/json');

$range = $_GET['range'] ?? '24h';
$variable = $_GET['variable'] ?? 'all';

$startDate = match($range) {
    '24h' => date('Y-m-d H:i:s', strtotime('-24 hours')),
    '7d' => date('Y-m-d H:i:s', strtotime('-7 days')),
    '30d' => date('Y-m-d H:i:s', strtotime('-30 days')),
    default => date('Y-m-d H:i:s', strtotime('-24 hours'))
};

// Seleccionar columnas según la variable solicitada
$columns = match($variable) {
    'temp' => 'th.temperatura_ambiente',
    'hum' => 'th.humedad_ambiente',
    'suelo' => '(
        SELECT hs.humedad
        FROM hum_suelo hs
        WHERE DATE(hs.fecha_registro) = DATE(th.fecha_registro)
        AND ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro)))) < 60
        ORDER BY ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro))))
        LIMIT 1
    ) as humedad',
    default => 'th.temperatura_ambiente, th.humedad_ambiente, (
        SELECT hs.humedad
        FROM hum_suelo hs
        WHERE DATE(hs.fecha_registro) = DATE(th.fecha_registro)
        AND ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro)))) < 60
        ORDER BY ABS(TIME_TO_SEC(TIMEDIFF(hs.hora_registro, TIME(th.fecha_registro))))
        LIMIT 1
    ) as humedad'
};

$SQL = "SELECT {$columns}, th.fecha_registro 
        FROM temp_hum_amb th
        WHERE th.fecha_registro >= '$startDate'
        ORDER BY th.fecha_registro ASC";

$consulta = mysqli_query($con, $SQL);

$response = [
    'labels' => [],
    'temp' => [],
    'hum' => [],
    'suelo' => []
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    while($fila = mysqli_fetch_assoc($consulta)) {
        $response['labels'][] = $fila['fecha_registro'];
        
        if ($variable == 'temp' || $variable == 'all') {
            $response['temp'][] = $fila['temperatura_ambiente'];
        }
        if ($variable == 'hum' || $variable == 'all') {
            $response['hum'][] = $fila['humedad_ambiente'];
        }
        if ($variable == 'suelo' || $variable == 'all') {
            $response['suelo'][] = $fila['humedad'];
        }
    }
}

echo json_encode($response);
?>