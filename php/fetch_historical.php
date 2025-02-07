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

// Consulta base
$SQL = "SELECT 
            temperatura_ambiente1, 
            temperatura_ambiente2, 
            humedad_ambiente1, 
            humedad_ambiente2, 
            humedad1, 
            humedad2, 
            fecha_registro 
        FROM temp_hum_amb
        WHERE fecha_registro >= '$startDate'
        ORDER BY fecha_registro ASC";

$consulta = mysqli_query($con, $SQL);

$response = [
    'labels' => [],
    'temp1' => [],
    'temp2' => [],
    'hum1' => [],
    'hum2' => [],
    'suelo1' => [],
    'suelo2' => []
];

if($consulta && mysqli_num_rows($consulta) > 0) {
    while($fila = mysqli_fetch_assoc($consulta)) {
        $response['labels'][] = $fila['fecha_registro'];
        
        switch ($variable) {
            case 'temp':
                $response['temp1'][] = $fila['temperatura_ambiente1'];
                $response['temp2'][] = $fila['temperatura_ambiente2'];
                break;
            
            case 'hum':
                $response['hum1'][] = $fila['humedad_ambiente1'];
                $response['hum2'][] = $fila['humedad_ambiente2'];
                break;
            
            case 'suelo':
                $response['suelo1'][] = $fila['humedad1'];
                $response['suelo2'][] = $fila['humedad2'];
                break;
            
            default:
                $response['temp1'][] = $fila['temperatura_ambiente1'];
                $response['temp2'][] = $fila['temperatura_ambiente2'];
                $response['hum1'][] = $fila['humedad_ambiente1'];
                $response['hum2'][] = $fila['humedad_ambiente2'];
                $response['suelo1'][] = $fila['humedad1'];
                $response['suelo2'][] = $fila['humedad2'];
                break;
        }
    }
}

echo json_encode($response);
?>