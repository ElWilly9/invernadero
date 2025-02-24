<?php
include_once 'conexion.php';

// Obtener últimos 50 registros
$SQL = "SELECT 
    cl.valor_clorofila1,
    cl.fecha_registro,
    cl.hora_registro
FROM clorofila cl
ORDER BY cl.fecha_registro DESC 
LIMIT 50";

$consulta = mysqli_query($con, $SQL);
$datos = [];
$valor_clorofila = [];
$fechas = [];
$fechas_hora = []; // Para fecha y hora combinadas

if($consulta && mysqli_num_rows($consulta) > 0){
    while ($resultado = mysqli_fetch_assoc($consulta)) {
        $fecha_formateada = date('Y-m-d', strtotime($resultado['fecha_registro']));
        $hora_formateada = date('H:i:s', strtotime($resultado['hora_registro']));
        
        $datos[] = [
            'fecha_registro' => $fecha_formateada,
            'hora_registro' => $hora_formateada,
            'valor_clorofila1' => $resultado['valor_clorofila1']
        ];
        
        $valor_clorofila[] = $resultado['valor_clorofila1'];
        $fechas[] = $fecha_formateada;
        $fechas_hora[] = $fecha_formateada . ' ' . $hora_formateada; // Combinar fecha y hora
    }
    
    // Invertir para orden cronológico correcto
    $valor_clorofila = array_reverse($valor_clorofila);
    $fechas = array_reverse($fechas);
    $fechas_hora = array_reverse($fechas_hora);
    
    // Obtener el valor actual (último registro)
    $clorofila_actual = end($valor_clorofila);
    
    // Determinar el estado de salud
    $rango_min = 35;
    $rango_max = 50;
    $estado_salud = ($clorofila_actual >= $rango_min && $clorofila_actual <= $rango_max) ? "Óptimo" : "No Óptimo";
    
    // Devolver todos los datos necesarios
    echo json_encode([
        'fechas' => $fechas,
        'valores' => $valor_clorofila,
        'fechas_hora' => $fechas_hora,
        'datos_tabla' => $datos, // Datos para la tabla
        'clorofila_actual' => $clorofila_actual, // Valor actual
        'estado_salud' => $estado_salud // Estado de salud
    ]);
} else {
    echo json_encode([
        'fechas' => [],
        'valores' => [],
        'fechas_hora' => [],
        'datos_tabla' => [],
        'clorofila_actual' => 'N/A',
        'estado_salud' => 'No Óptimo'
    ]);
}